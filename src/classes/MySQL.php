<?
/**
 * файл с классами для работы с MySQL
 *
 * Классы:
 * MySQL            - класс работы с базой данных
 * MySQLColsData    - класс заглушка
 * DBResult         - класс работы с результатом запроса
 */

/**
 * Класс для работы с MySQL
 *
 * Свойства:
 * _ConnectError        - Свойство принимает значение, если не удалось подключиться к базе данных
 * _DbConn              - Ссылка на ресурс соединения с базой данных
 * _DbSelectError       - Свойство принимает значение, если не удалось выбрать базу данных
 * _DbName              - Имя базы данных для выбора
 * _Debug               - флаг выводить или нет дебаговую информацию и логировать ли запросы
 * _Log                 - флаг писать или нет в лог запросы
 * _QueryError          - Свойство принимает значение, если не удалось выполнить запрос
 * _QueryLast           - Свойство принимает значение последнего запроса
 * _QueryLog            - Массив всех запросов, выполненых объектом
 * _oResult             - объект DBResult
 * _iLimiteCountInsert  - свойство с лимитом максимально вставляемых строк в методе insert_group_arr
 *
 * Методы:
 * MySQL                    - играет роль конструктора
 * connect                  - создает соединение с базойданных, выбирает тадлицу и устанавливает свойства соединения
 * debuger                  - выводит дебаговую информацию
 * disconnect               - закрывает соединение с базой данных
 * get_affected_rows        - возращает количество обработаных строк
 * get_last_insert_id       - возращает последний вставленый ключ поля с атрибутом auto_increment
 * get_last_query           - возращает последний запрос
 * insert_arr               - формирует запрос на вставку строки в таблицу относительно масива
 * insert_group_arr         - формирует запрос на вставку множества строк в таблицу относительно масива
 * insert_prep_arr          - формирует часть запроса на вставку строки в таблицу относительно масива
 * insert_prep_group_arr    - формирует часть запроса на вставку множества строк в таблицу относительно масива
 * lock_tables              - выполняет запрос блокирования таблицы
 * prepare_set              - формирует часть запроса
 * prepare_where            - формирует часть запроса
 * query                    - выполняет запрос
 * setDebug                 - устанавливает значение свойства _Debug и _Log
 * unlock_tables            - выполняет запрос разблокирования таблиц
 * update_arr               - выполняет запрос обновления данных таблицы относительно переданого массива
 *
 * Методы транзакции:
 * begin                - Выполняет запрос на начало транзакции
 * commit               - Выполняет запрос завершения транзакции
 * rollback             - Выполняет запрос отката транзакции
 * transaction          - Выполняет запросы транзакции
 *
 * split_query          - Метод взят из phpMyAdmin для разбивания группы запросов на одиночные
 */

class MySQL
{
    var $_ConnectError  = FALSE;

	/**
	 * @var resource
	 */
    var $_DbConn;
    var $_DbSelectError = FALSE;
    var $_DbName        = '';
    var $_Debug         = FALSE;
    var $_Log           = FALSE;

    var $_QueryError    = FALSE;
    var $_QueryLast     = '';
    var $_QueryLog      = array();
    var $_QueryLogCall  = array();

	/**
	 * @var array of DBResult
	 */
    var $_aResult;

	/**
	 * @var DBResult
	 */
	var $_oResult;
    var $_aInfoTablColumns = array();

    var $_iLimiteCountInsert = 500;

    /**
     * Если вызван не определенный метод, то попытка найти этот метод в объекте _oResult
     *
     * @param string $name
     * @param array $arguments
     *
     * @return unknown
     */
    public function __call( $name, $arguments )
    {
        if( $this -> _oResult instanceof DBResult && method_exists( $this -> _oResult, $name ) )
            return call_user_func_array( array( $this->_oResult, $name ), $arguments );
        else
            trigger_error( "Class DBResult isn't set or hasn't method ". $name );
    }

	/**
	 * @return array
	 */
	public function get_fetch_ass()
	{
		return $this->_oResult->get_fetch_ass();
	}

	/**
	 * @return int
	 */
	public function get_num_rows()
	{
		return $this->_oResult->get_num_rows();
	}

	/**
	 * @return string
	 */
	public function get_result()
	{
		return $this->_oResult->get_result();
	}

    /**
     * играет роль конструктора
     *
     * @param array $aConfig - массив вида array( 'host' => адрес хоста с базой данных,
     *                                            'db' => имя базы данных для выбора,
     *                                            'user' => имя пользователя для подключения к базе данных,
     *                                            'pass' => пароль для идентефикации пользователя )
     *
     * @return MySQL
     */
    function __construct( $aConfig )
    {
        if( count ( $aConfig ) )
            $this -> connect( $aConfig['db_host'], $aConfig['db_name'], $aConfig['db_user_name'], $aConfig['db_password'], $aConfig['db_port'], isset ( $aConfig['db_charset'] ) ? $aConfig['db_charset'] : 'utf8', isset ( $aConfig['db_collation'] ) ? $aConfig['db_collation'] : 'utf8_general_ci' );
    }

    /**
     * создает соединение с базой данных,
     * выбирает тадлицу и
     * устанавливает свойства соединения
     *
     * @param string $dbHost        - адрес хоста с базой данных
     * @param string $dbName        - имя базы данных для выбора
     * @param string $dbLogin       - имя пользователя для подключения к базе данных
     * @param string $dbPassword    - пароль для идентефикации пользователя
     * @param bool $dbPort          - порт подключения к хосту
     * @param string $character     - сравнение для запросов и ответов
     * @param string $collation     - стравнение для подключения
     * @return bool                 - Удачно / Не удачно
     */

    function connect( $dbHost, $dbName, $dbLogin, $dbPassword, $dbPort = FALSE, $character = 'utf8', $collation = 'utf8_general_ci' )
    {
        $this -> _DbName = $dbName;
        //$this -> _DbConn = mysql_pconnect( ( ( $dbPort ) ? "$dbHost:$dbPort" : "$dbHost" ), $dbLogin, $dbPassword );
        $this -> _DbConn = mysql_connect( ( ( $dbPort ) ? "$dbHost:$dbPort" : "$dbHost" ), $dbLogin, $dbPassword );
        if( !( $this -> _DbConn ) )
        {
            $this -> _ConnectError = mysql_error();
            $this -> debuger();
            return FALSE;
        }

        if( !mysql_select_db( $this -> _DbName, $this -> _DbConn ) )
        {
            $this -> _DbSelectError = mysql_error();
            $this -> debuger();
            return FALSE;
        }
        $this -> query( "set character_set_client='". $character ."'", __FILE__, __LINE__ );
        $this -> query( "set character_set_results='". $character ."'", __FILE__, __LINE__ );
        $this -> query( "set collation_connection='". $collation ."'", __FILE__, __LINE__ );
        return true;
    }

    function copy( $sFromTable, $sToTable, $aColsToCopy, $aWhere, $sFile = FALSE, $iLine = FALSE )
    {
        if( $sFile || $iLine )
            $this -> _QueryLogCall[] = array( 'copy' => $sFile .' ['. $iLine .']' );

        $sCols = ( count( $aColsToCopy ) ? "(`". implode( '`, `', $aColsToCopy ) ."`)" : "" ) ;
        $sWhere = ( count( $aWhere ) ? " WHERE ". implode( ' AND ', $aWhere ) ." " : "" ) ;
        $sQuery = "
                    INSERT INTO
                `". $sToTable ."`
                ". $sCols ."
                    VALUES
                SELECT
            *
                FROM
            `". $sFromTable ."`
                ". $sWhere ."
            ";
        $this -> query( $sQuery, $sFile, $iLine );
    }

    public function copy_to_change_table( $sWhere, $aParam = array(), $sTableName, $sPrefixRowName, $sAdditionally = FALSE )
    {
        if( $sAdditionally )
            $this -> _QueryLogCall[] = array( 'copy_to_change_table' => $sAdditionally );

        if( !$sWhere )
            return FALSE;

        $UA         = Request::getVar( "UA", "global" );
        $sDateTime  = date( 'Y-m-d H:i:s' );    // дата изменения

        $aColumns = $this -> get_columns_info( $sTableName );

        if( count( $aParam ) )
            foreach( $aColumns as $k => $value )
                $aColumns[ $k ] = ( array_key_exists( $k, $aParam ) ) ? "'". $aParam ."' as ". $k : $k;

        $aColumns[ $sPrefixRowName .'_change_time' ] = "'". $sDateTime ."' as csrd_change_time";  // записываем время изменения
        $aColumns[ $sPrefixRowName .'_changer' ]     = "'". $UA['user_id'] ."' as csrd_changer";  // id пользователя

        $sQuery = "
                    INSERT INTO
                ". $sTableName ."_ch
                (". implode( ", ", array_keys( $aColumns ) ) .")

                    SELECT
                ". implode( ", ", $aColumns ) ."
                    FROM
                ". $sTableName ."
                    WHERE
                ". $sWhere .";";
        $this -> query( $sQuery, __FILE__, __LINE__, $sAdditionally );

        return true;
    }


    static public function create_additionally( $sAdditionally, $sFile, $iLine )
    {
        return ( ( $sFile || $iLine ) ? $sAdditionally ."\n". $sFile ." [". $iLine ."]" : $sAdditionally );
    }

    /**
     * выводит дебаговую информацию.
     *
     */
    function debuger()
    {
        if( $this->_Debug && ( GlobalSetup::getInstance()->local_server || GlobalSetup::getInstance()->super_admin_flag ) )
        {
            $str = "<br><font color=#ff0000>"
                 ."_ConnectError='".$this -> _ConnectError."'<br>"
                 ."_DbSelectError='".$this -> _DbSelectError."'<br>"
                 ."_QueryError='".$this -> _QueryError."'<br>"
                 ."_QueryLast='".$this -> _QueryLast."'<br>
                 " . print_r( $this -> _QueryLog, true ) . "
                 </font>";

            if ( GlobalSetup::getInstance()->ajax_flag && GlobalSetup::getInstance()->local_server )
		        mail ( '', '', str_replace ( "<br>", "\n", $str ) );
	        else
		        echo $str;
        }
    }

    /**
     * закрывает соединение с базой данных
     *
     */
    function disconnect()
    {
        mysql_close( $this -> _DbConn );
    }

	/**
	 * Возращает количество обработаных строк запросами
	 * INSERT, UPDATE, REPLACE or DELETE
	 *
	 * @return int
	 */
    function get_affected_rows()
    {
        return mysql_affected_rows( $this -> _DbConn );
    }

	/**
	 * Returns name of the columns for specified table name
	 * @param $table_name - name of the table
	 * @param bool $incl_fields - flag
	 * @return mixed
	 */

	function get_columns_info( $table_name, $incl_fields = true )
    {
        if( !array_key_exists( $table_name, $this -> _aInfoTablColumns ) )
        {
            $this -> _aInfoTablColumns[ $table_name ] = array();

            $query = "SHOW COLUMNS FROM ". $table_name;
            /**
             * var DBResult $oResult
             */
            $result = $this -> query( $query );

            while( $row = $result -> get_fetch_ass() ) // - если несколько записей
                $this -> _aInfoTablColumns[ $table_name ][ $row['Field'] ] = $incl_fields ? $row['Field'] : '';
        }
        return $this -> _aInfoTablColumns[ $table_name ];
    }

    function get_founds_rows()
    {
        $sQuery   = "SELECT FOUND_ROWS()";
        $this -> query( $sQuery, __FILE__, __LINE__);

        return $this -> get_result( 0 );
    }

    /**
     * возращает последний вставленый ключ поля с атрибутом auto_increment
     *
     * @return integer or FALSE
     */
    function get_last_insert_id()
    {
        return mysql_insert_id( $this -> _DbConn );
    }

    /**
     * возращает следующее значение auto_increment таблицы
     *
     * @param string $sTableName    - имя таблицы
     * @param string $sFile         - путь к файлу из которого вызван метод
     * @param int $iLine         - строка файла из которой вызван метод
     * @param string $sAdditionally - дополнительная информация
     *
     * @return integer
     */
    function get_last_increment_id( $sTableName, $sFile = '', $iLine = 0, $sAdditionally = '' )
    {
        if( $sAdditionally )
            $this -> _QueryLogCall[] = array( 'get_last_increment_id' => $sAdditionally );

        if( $sFile || $iLine )
            $this -> _QueryLogCall[] = array( 'get_last_increment_id' => $sFile .' ['. $iLine .']' );

        $sQuery = "SELECT Auto_increment FROM information_schema.tables WHERE table_name='". $sTableName ."';";
        $this -> query( $sQuery, $sFile, $iLine, $sAdditionally );
        return $this -> get_result( 0 );
    }


    function get_debug_last_key_query_log()
    {
        return count( $this -> _QueryLog ) - 1;
    }

    function get_debug_query_log( $iFrom = 0, $iTo = FALSE )
    {
        $iTo = $iTo !== FALSE ? $iTo: count( $this -> _QueryLog );
        return array_slice( $this -> _QueryLog, $iFrom, $iTo );
    }

    /**
     * возращает последний запрос
     *
     * @return string
     */
    function get_last_query()
    {
        return $this -> _QueryLast;
    }

	/**
	 * формирует запрос на вставку строки в таблицу относительно массива
	 *
	 * @param string $tbl                       - имя таблицы
	 * @param array $arr                        - массив данных вида array( имя поля => значение поля ... )
	 * @param bool $add_creator              - флаг
	 * @param string $sFile                     - путь к файлу из которого вызван метод
	 * @param int $iLine                        - строка файла из которой вызван метод
	 * @param string $sAdditionally             - дополнительная информация
	 *
	 * @return bool|DBResult  - В случае удачи возращает объект DBResult, в противном случаи FALSE
	 */
    function insert_arr( $tbl, $arr, $add_creator = FALSE, $sFile = '', $iLine = 0, $sAdditionally = '' )
    {
	    if( $sAdditionally )
            $this -> _QueryLogCall[] = array( 'insert_arr' => $sAdditionally );

        if( $sFile || $iLine )
            $this -> _QueryLogCall[] = array( 'insert_arr' => $sFile .' ['. $iLine .']' );

	    if ( $add_creator )
	    {
			$arr[$tbl.'_creator']     = GlobalSetup::getInstance()->user;
		    $arr[$tbl.'_create_date'] = new MySQLColsData( 'NOW()' );
	    }

        $sQuery = "
            INSERT INTO
        $tbl
        " . $this -> prepare_set( $arr );

        return $this -> query( $sQuery, $sFile, $iLine, $sAdditionally );
    }

	/**
	 * формирует запрос на вставку множества строк в таблицу относительно маcсива
	 *
	 * @param string $tbl           - имя таблицы
	 * @param array $arr            - массив данных вида array( array( имя поля => значение поля ... ), ... )
	 * @param bool $ON_DUPL
	 * @param bool|string $sFile - путь к файлу из которого вызван метод
	 * @param bool|string $iLine - строка файла из которой вызван метод
	 * @param bool|string $sAdditionally - дополнительная информация
	 * @param bool $mCashCreate
	 * @return bool
	 *
	 */
    function insert_group_arr( $tbl, $arr, $ON_DUPL = FALSE, $sFile = FALSE, $iLine = FALSE, $sAdditionally = FALSE, $mCashCreate = FALSE )
    {
        if( count( $arr ) )
        {
			if( $sAdditionally )
				$this -> _QueryLogCall[] = array( 'insert_group_arr' => $sAdditionally );

			if( $sFile || $iLine )
				$this -> _QueryLogCall[] = array( 'insert_group_arr' => $sFile .' ['. $iLine .']' );

			$aChunk = array_chunk( $arr, $this -> _iLimiteCountInsert );
			foreach( $aChunk as $aInsertData )
			{
				$sQuery = "INSERT INTO $tbl " . $this -> insert_prep_group_arr( $aInsertData, $ON_DUPL, $mCashCreate );
				$this -> query( $sQuery, $sFile, $iLine, $sAdditionally );
			}

			return true;
        }
	    else
		    return false;
    }

	/**
	 * формирует часть запрос на вставку строки в таблицу относительно массива
	 *
	 * @param array $arr        - массив данных вида array( имя поля => значение поля ... )
	 *                            Примечание:
	 *                              Если значение поля являеться объектом класса MySQLColsData, то данное значение вставляется как есть,
	 *                              т.е. не обрамляется одинарными ковычками
	 * @param bool|string $ON_DUPL - флаг или имя поля, указывает что при совпадении ключа сделать UPDATE записи
	 * @param bool $mCashCreate
	 * @return string           - часть запроса на вставку
	 */
    function insert_prep_arr( $arr, $ON_DUPL = FALSE, $mCashCreate = FALSE )
    {
        $values         = array();
        $onduplicate    = array();

        if( $mCashCreate )
        {
	        unset( $arr[ $mCashCreate . "_creator" ] );
	        unset( $arr[ $mCashCreate . "_create_date" ] );
        }

	    /**
	     * @var string|MySQLColsData $value
	     */
        foreach( $arr AS $key => $value )
        {
			$values[]       = ( !is_object( $value ) || get_class( $value ) != "MySQLColsData" ) ?  "'". mysql_real_escape_string( $value ) ."'" : $value -> get_value();
			if( $ON_DUPL && $key !== $ON_DUPL )
				$onduplicate[]  = "`$key`=VALUES(`". $key ."`)";
        }

        $keys           = "`". implode( '`, `', array_keys( $arr ) ) ."`";
        $values         = implode( ", ", $values );

        if( $mCashCreate )
        {
            $keys   .=  ", `". $mCashCreate ."_create_date`, `". $mCashCreate ."_creator`";
            $values .=  ", NOW(), '". GlobalSetup::getInstance()->user ."'";
        }
        $onduplicate    = implode( ",\n", $onduplicate );
        $sQuery         = "\n(". $keys .")\n VALUES \n(". $values .")\n". ( ( $ON_DUPL ) ? "\n ON DUPLICATE KEY UPDATE \n". $onduplicate : "" );
        return $sQuery;
    }

	/**
	 * формирует часть запрос на вставку множества строк в таблицу относительно массива
	 *
	 * @param array $arr        - массив данных вида array( array( имя поля => значение поля ... ), ... )
	 *
	 * @param bool $ON_DUPL     - флаг или имя поля, указывает что при совпадении ключа сделать UPDATE записи
	 * @param bool $mCashCreate
	 * @return string           - часть запроса на вставку
	 */
    function insert_prep_group_arr( $arr, $ON_DUPL = FALSE, $mCashCreate = FALSE )
    {
        $mKeys       = array();
        $mValues     = array();
        $onduplicate = array();

        foreach( $arr AS &$values ) // Получаем названия колонок
        {
            if( $mCashCreate )
            {
                $values[ $mCashCreate ."_create_date" ]  = new MySQLColsData( 'NOW()' );
                $values[ $mCashCreate ."_creator" ] = GlobalSetup::getInstance()->user;
            }

            ksort( $values ); // Что бы все ключи шли попрорядку

            foreach ( $values as $key => $value )
                $mKeys[$key] = $key;
        }

        foreach( $arr AS $values2 ) // Получаем значения для полей
        {
            $aData = array();
            foreach ( $mKeys as $kkNameRow => $vvNameRow )
                $aData[]       = ( array_key_exists( $kkNameRow, $values2 ) && is_object( $value ) && get_class( $values2[$kkNameRow] ) == "MySQLColsData" ) ?  $values2[$kkNameRow] -> get_value() : ( array_key_exists( $kkNameRow, $values2 ) ? "'". mysql_real_escape_string( $values2[$kkNameRow] ) ."'" : "''" );

            $mValues[] = "(" . implode( ", ", $aData ) .")";
        }

        if( $ON_DUPL )
            foreach ( $mKeys as $key )
                if( $key !== $ON_DUPL || ( is_array( $ON_DUPL ) && !in_array( $key, $ON_DUPL ) ) )
                    $onduplicate[]  = "`$key`=VALUES(`$key`)";

        $mKeys       = "\n`" . implode( '`,`', $mKeys ) . "`\n";

        $mValues     = implode( ",\n", $mValues );
        $onduplicate = ( $onduplicate ) ? implode( ",\n", $onduplicate ) : "";

        $sQuery      = "(". $mKeys .")\n VALUES \n". $mValues ."". ( ( $onduplicate ) ? "\n ON DUPLICATE KEY UPDATE \n". $onduplicate : "" );

        return $sQuery;
    }

	/**
	 * выполняет запрос блокирования таблицы
	 *
	 * @param string $tbl           - имя таблицы
	 * @param bool|string $sFile - путь к файлу из которого вызван метод
	 * @param bool|string $iLine - строка файла из которой вызван метод
	 * @param bool|string $sAdditionally - дополнительная информация
	 *
	 */
    function lock_tables( $tbl, $sFile = FALSE, $iLine = FALSE, $sAdditionally = FALSE )
    {
        if( $sAdditionally )
            $this -> _QueryLogCall[] = array( 'lock_tables' => $sAdditionally );

        if( $sFile || $iLine )
            $this -> _QueryLogCall[] = array( 'lock_tables' => $sFile .' ['. $iLine .']' );

        $this -> query( "LOCK TABLE " . $tbl, $sFile, $iLine, $sAdditionally );
    }

	/**
	 * функция для создания записи "истории" в [table_name]_ch таблице и удаления соответствующих записей из [table_name]
	 *
	 * @param   string  $sWhere         - условие для выбора данных
	 * @param   array   $aParam         - массив полей с параметрами, которые надо вставить(заменить) в [table_name]_ch
	 * @param   string  $sTableName     - имя таблицы ([table_name])
	 * @param   string  $sPrefixRowName - префикс полей таблицы
	 * @param bool|string $sAdditionally - дополнительная информация

	 *
	 * @return mixed FALSE или object DBResult  - В случаи удачи возращает объект DBResult, в противном случаи FALSE
	 *
	 */

    public function move_to_change_table( $sWhere, $aParam = array(), $sTableName, $sPrefixRowName, $sAdditionally = FALSE )
    {
        if( $sAdditionally )
            $this -> _QueryLogCall[] = array( 'move_to_change_table' => $sAdditionally );

        if( !$sWhere )
            return FALSE;

        $this -> copy_to_change_table( $sWhere, $aParam, $sTableName, $sPrefixRowName, $sAdditionally );

        $sQuery = "
            DELETE FROM
        ". $sTableName ."
            WHERE
        ". $sWhere;

        return $this -> query( $sQuery, __FILE__, __LINE__, $sAdditionally );
    }

    /**
     * Формирует часть запроса
     *
     * @param array  $aSet      - массив данных вида array( 'имя столбца' => 'значение', )
     *
     * @return string  - Часть строки запроса
     */
    function prepare_set( $aSet )
    {
        /**
         * @var string|MySQLColsData $value
         */
	    $tmpSet = array();

	    foreach( $aSet AS $key => $value )
                $tmpSet[] = "`$key` = ". ( ( is_object( $value ) && get_class( $value ) == "MySQLColsData" ) ? $value -> get_value() : "'". mysql_real_escape_string( $value ) ."'" );

	    return count( $tmpSet ) ? "     SET
	    " . implode( ", \n", $tmpSet ) : "";
    }

	/**
	 * Формирует часть запроса
	 *
	 * @param array|string $mWhere - массив условий вида array( 'условие 1', ..., 'условие N' ) || или строковое условие
	 *
	 * @return string  - Часть строки запроса
	 */
    function prepare_where( $mWhere )
    {
        return is_array( $mWhere ) && count ( $mWhere ) ? " WHERE " . implode( ' AND ', $mWhere ) : $mWhere;
    }

	/**
	 * выполняет запрос
	 *
	 * возращае:
	 * объект DBResult
	 * или массив объектов DBResult на каждый запрос, если было переданы несколько запросов в строке
	 *
	 * @param string $sQuery        - строка запроса
	 * @param bool|string $sFile - путь к файлу из которого вызван метод
	 * @param bool|string $iLine - строка файла из которой вызван метод
	 * @param bool|string $sAdditionally - дополнительная информация
	 *
	 * @return array|DBResult
	 */
    function query( $sQuery, $sFile = FALSE, $iLine = FALSE, $sAdditionally = FALSE )
    {
        $trace_str = '';
	    if ( !$sFile || !$iLine )
        {
	        $trace_arr = debug_backtrace(  );
	        $trace_str = parse_debug_trace( $trace_arr, false );
        }

	    if( $sAdditionally )
            $this -> _QueryLogCall[] = array( 'query' => $sAdditionally );

        if( $sFile || $iLine )
            $this -> _QueryLogCall[] = array( 'query' => $sFile .' ['. $iLine .']' );

        unset( $this -> _oResult );
        unset( $this -> _aResult );

        $return = array();
        $this -> _QueryLast   = $sQuery;
        $aQueres = $this -> split_query( $sQuery );
        $iKeyQuery = 0;

        foreach( $aQueres as $value )
        {
            if( strpos( $value['query'], "/*key:" ) !== FALSE )
            {
                $aTmp       = explode( "/*key:", $value['query'] );
                $aTmp       = explode( "*/", $aTmp[ count( $aTmp ) - 1 ] );
                $sKeyQuery  = $aTmp[0];
            }
            else
                $sKeyQuery = $iKeyQuery++;

            $iTmpLastKeyQueryLog = $this->_Debug ? $this -> get_debug_last_key_query_log() + 1 : 0;

	        $iStartQuery  = microtime( TRUE );
	        $iStartMemory = memory_get_usage();
            if ( $this->_Debug )
	        {
                /*+++ Debug Data */
				$this -> _QueryLog[$iTmpLastKeyQueryLog]  = array(
												'FILE'          => $sFile,
												'LINE'          => $iLine,
												'ADDITIONALLY'  => $sAdditionally,
												'QUERY'         => $value['query'],
											  );
	        }

            /*--- Debug Data */
            $sQueryToMySQL = $value['query'] ."\n/* [". date('Y-m-d h:i:s') ."]\n".
                   ( $sFile && $iLine ? $sFile ."[". $iLine ."]". "\n". str_replace( '*/', '*\/', $sAdditionally ) : $trace_str ) ."*/";
            // Добавлены в коментарий запроса Файл, строка и история вызова запроса, что бы можно было видеть в логе MySQL

            $result = mysql_query( $sQueryToMySQL, $this -> _DbConn )
                      or eu( $sFile, $iLine, $sQueryToMySQL . ( $sAdditionally ? "\n#" . str_replace( "\n", "\n#", $sAdditionally ) : ''  ) );

	        if ( $this->_Debug )
	        {
				/*+++ Debug Data */
				$this -> _QueryLog[$iTmpLastKeyQueryLog]['time_query']      = microtime( TRUE ) - $iStartQuery;
				$this -> _QueryLog[$iTmpLastKeyQueryLog]['memory_use']      = '~'. number_format( ( memory_get_usage() - $iStartMemory ), 2, ',', ' ' ) .'bytes';
				$this -> _QueryLog[$iTmpLastKeyQueryLog]['affected_rows']   = $this -> get_affected_rows();
				$this -> _QueryLog[$iTmpLastKeyQueryLog]['num_rows']        = @mysql_num_rows( $result );
				$this -> _QueryLog[$iTmpLastKeyQueryLog]['insert_id']       = $this -> get_last_insert_id();
				$this -> _QueryLog[$iTmpLastKeyQueryLog]['result']          = $result;
				$this -> _QueryLog[$iTmpLastKeyQueryLog]['info']            = mysql_info( $this -> _DbConn );
				/*--- Debug Data */

                $this -> _QueryError = mysql_error();
                $this -> debuger();
            }
            if( $this -> _Log || !$result )
                Debug::in_file( ( !$result ? "ERROR QUERY: " : '' ) . $sQueryToMySQL ."\r\n----------------\r\n" );

            $return[ $sKeyQuery ] = new DBResult( $result, $this -> get_affected_rows(), $this -> get_last_insert_id() );

        }

        if( count( $return ) == 1 )
            return $this -> _oResult = $return[ $sKeyQuery ];
        else
            return $this -> _aResult = $return;
    }

    public static function reverse_escape( $str )
    {
        $search  = array( "\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"' );
        $replace = array( "\\", "\0", "\n", "\r", "\x1a", "'", '"' );
        return str_replace( $search, $replace, $str );
    }
    /**
     * Устанавливает флаг логирования
     *
     * @param boolean $debug
     * @param boolean $log
     */
    public function setDebug( $debug = FALSE, $log = false )
    {
        $this->_Debug = $debug;
	    $this->_Log   = $log;
    }

	/**
	 * выполняет запрос разблокирования таблиц
	 *
	 * @param bool|string $sFile - путь к файлу из которого вызван метод
	 * @param bool|string $iLine - строка файла из которой вызван метод
	 * @param bool|string $sAdditionally - дополнительная информация
	 *
	 */
    function unlock_tables( $sFile = FALSE, $iLine = FALSE, $sAdditionally = FALSE )
    {
        if( $sAdditionally )
            $this -> _QueryLogCall[] = array( 'unlock_tables' => $sAdditionally );

        if( $sFile || $iLine )
            $this -> _QueryLogCall[] = array( 'unlock_tables' => $sFile .' ['. $iLine .']' );

        $this -> query( "UNLOCK TABLES", $sFile, $iLine, $sAdditionally );
    }

	/**
	 * Выполняет запрос обновления данных таблицы относительно переданого массива
	 *
	 * @param string $sTbl      - имя таблицы
	 * @param array  $aSet      - массив данных вида array( 'имя столбца' => 'значение', )
	 * @param bool $add_creator - флаг, показывает, надо ли обновлять поля changer и change_date
	 * @param array|bool $aWhere - массив условий вида array( 'условие 1', ..., 'условие N' )
	 * @param bool|string $sFile - путь к файлу из которого вызван метод
	 * @param bool|string $iLine - строка файла из которой вызван метод
	 * @param bool $sAdditionally
	 * @return mixed FALSE или object DBResult  - В случаи удачи возращает объект DBResult, в противном случаи FALSE
	 */
    function update_arr( $sTbl, $aSet, $add_creator = FALSE, $aWhere = FALSE, $sFile = FALSE, $iLine = FALSE, $sAdditionally = FALSE )
    {
        if( $sAdditionally )
            $this -> _QueryLogCall[] = array( 'update_arr' => $sAdditionally );

        if( $sFile || $iLine )
            $this -> _QueryLogCall[] = array( 'update_arr' => $sFile .' ['. $iLine .']' );

	    if ( $add_creator )
	    {
			$aSet[$sTbl.'_changer']     = GlobalSetup::getInstance()->user;
		    $aSet[$sTbl.'_change_date'] = new MySQLColsData( 'NOW()' );
	    }

        $sQuery = "
            UPDATE
        $sTbl
        " . $this -> prepare_set( $aSet ) . $this -> prepare_where( $aWhere );

        return $this -> query( $sQuery, $sFile, $iLine, $sAdditionally );
    }

    /************************
     *  Методы транзакций  *
     ************************/
    /**
     * Выполняет запрос на начало транзакции
     *
     * @return mixed FALSE или object DBResult  - В случаи удачи возращает объект DBResult, в противном случаи FALSE
     */
    function begin()
    {
      $this -> query( "START TRANSACTION" );
      return $this -> query( "BEGIN" );
    }

    /**
     * Выполняет запрос завершения транзакции
     *
     * @return mixed FALSE или object DBResult  - В случаи удачи возращает объект DBResult, в противном случаи FALSE
     */
    function commit()
    {
      return $this -> query( "COMMIT" );
    }

    /**
     * Выполняет запрос отката транзакции
     *
     * @return mixed FALSE или object DBResult  - В случаи удачи возращает объект DBResult, в противном случаи FALSE
     */
    function rollback()
    {
      return $this -> query( "ROLLBACK" );
    }

    /**
     * Выполняет запросы транзакции
     *
     * @param array $aQuery -массив запросов
     * @return boolean
     */
    function transaction( $aQuery )
    {
       $retval = TRUE;

       $this -> begin();

       foreach( $aQuery as $sQuery )
       {
            $this -> query( $sQuery );
            if( $this -> get_affected_rows() == 0 )
                $retval = FALSE;
       }
       if( !$retval )
       {
            $this -> rollback();
            return false;
       }
       else
       {
            $this -> commit();
            return true;
       }
    }

	/**
	 * Взято из phpMyAdmin
	 * Removes comment lines and splits up large sql files into individual queries
	 *
	 * Last revision: September 23, 2001 - gandon
	 *
	 * @param $sql
	 *
	 * @internal param \the $array splitted sql commands
	 *
	 * @internal param \the $string sql commands
	 *
	 * @internal param \the $integer MySQL release number (because certains php3 versions
	 *                   can't get the value of a constant from within a function)
	 *
	 * @return  array
	 *
	 * @access  public
	 */
    function split_query( $sql )
    {
        // do not trim, see bug #1030644
        //$sql          = trim($sql);
        $ret          = array();
        $sql          = rtrim( $sql, "\n\r" );
        $sql_len      = strlen( $sql );
        $string_start = '';
        $in_string    = FALSE;
        $nothing      = TRUE;
        $time0        = time();

        for( $i = 0; $i < $sql_len; ++$i )
        {
            $char = $sql[$i];

            // We are in a string, check for not escaped end of strings except for
            // backquotes that can't be escaped
            if( $in_string )
            {
                for( ; ; )
                {
                    $i         = strpos($sql, $string_start, $i);
                    // No end of string found -> add the current substring to the
                    // returned array
                    if( !$i )
                    {
                        $ret[] = array( 'query' => $sql, 'empty' => $nothing );
                        return $ret;
                    }
                    // Backquotes or no backslashes before quotes: it's indeed the
                    // end of the string -> exit the loop
                    elseif( $string_start == '`' || $sql[$i-1] != '\\' )
                    {
                        $string_start      = '';
                        $in_string         = FALSE;
                        break;
                    }
                    // one or more Backslashes before the presumed end of string...
                    else
                    {
                        // ... first checks for escaped backslashes
                        $j                     = 2;
                        $escaped_backslash     = FALSE;
                        while( $i - $j > 0 && $sql[ $i - $j ] == '\\' )
                        {
                            $escaped_backslash = !$escaped_backslash;
                            $j++;
                        }
                        // ... if escaped backslashes: it's really the end of the
                        // string -> exit the loop
                        if( $escaped_backslash )
                        {
                            $string_start  = '';
                            $in_string     = FALSE;
                            break;
                        }
                        // ... else loop
                        else
                            $i++;
                    } // end if...elseif...else
                } // end for
            } // end if(in string)

            // lets skip comments (/*, -- and #)
            elseif( ( $char == '-' && $sql_len > $i + 2 && $sql[ $i + 1 ] == '-' && $sql[ $i + 2 ] <= ' ') || $char == '#' || ($char == '/' && $sql_len > $i + 1 && $sql[ $i + 1 ] == '*' ) )
            {
                $i = strpos( $sql, $char == '/' ? '*/' : "\n", $i );
                // didn't we hit end of string?
                if( $i === FALSE )
                    break;
                if( $char == '/' )
                    $i++;
            }

            // We are not in a string, first check for delimiter...
            elseif( $char == ';' )
            {
                // if delimiter found, add the parsed part to the returned array
                $ret[]      = array( 'query' => substr($sql, 0, $i), 'empty' => $nothing );
                $nothing    = TRUE;
                $sql        = ltrim( substr( $sql, min( $i + 1, $sql_len ) ) );
                $sql_len    = strlen( $sql );
                if( $sql_len )
                    $i      = -1;
                else
                    // The submited statement(s) end(s) here
                    return $ret;
            } // end elseif(is delimiter)

            // ... then check for start of a string,...
            elseif( ( $char == '"' ) || ( $char == '\'' ) || ( $char == '`' ) )
            {
                $in_string    = TRUE;
                $nothing      = FALSE;
                $string_start = $char;
            } // end elseif(is start of string)
            elseif( $nothing )
                $nothing = FALSE;

            // loic1: send a fake header each 30 sec. to bypass browser timeout
            $time1     = time();
            if( $time1 >= $time0 + 30 )
            {
                $time0 = $time1;
                header( 'X-Ping: Pong' );
            } // end if
        } // end for

        // add any rest to the returned array
        if( !empty( $sql ) && preg_match( '@[^[:space:]]+@', $sql ) )
            $ret[] = array( 'query' => $sql, 'empty' => $nothing );

        return $ret;
    } // end of the 'split_query()' function
}
/**
 * Класс заглушка для вставки "формул" в запросы.
 * Используеться в объекте MySQL, в методе insert_prep_arr
 *  т.е. если значение на вставку имеет этот класс, что вставляемое значение не обрамляеться одинарными скобками
 *
 * Свойства:
 * _value           - Свойство принимает значение ( на пример: `имя столбца 1` + `имя столбца 2` )
 *
 * Методы:
 * MySQLColsData    - играет роль конструктора
 * get_value        - возращает значение свойства _value
 */
class MySQLColsData
{
    var $_value;

    /**
     * играет роль конструктора
     *
     * @param string $value     - значение
     * @return MySQLColsData
     */
    function MySQLColsData( $value )
    {
        $this -> _value = $value;
    }

    /**
     * возращает значение свойства _value
     *
     * @return mixed
     */
    function get_value()
    {
        return $this -> _value;
    }
}

/**
 * Класс для работы с результатами запросов
 *
 * Свойства:
 * _oResult             - ссылка на ресурс ответа базы данных на запрос
 * _MysqlAffectedRows   - Свойство принимает значение количества измененых строк
 * _MysqlInsertId       - Свойство принимает значение последнего вставленого ключа поля с атрибутом auto_increment
 * _fetchArr            - Массив с данными из запроса (используеться в методе get_fetch_array)
 *
 * Методы:
 * DBResult             - играет роль конструктора
 * get_data_seek        - возращает результат выполнения функции mysql_data_seek
 * get_fetch_array      - возращает массив данных результат выполнения функции mysql_fetch_array
 * get_fetch_arr        - возращает результат выполнения функции mysql_fetch_array
 * get_fetch_ass        - возращает результат выполнения функции mysql_fetch_assoc
 * get_fetch_field      - возращает результат выполнения функции mysql_fetch_field
 * get_fetch_row        - возращает результат выполнения функции mysql_fetch_row
 * get_affected_rows    - возращает значение свойства _MysqlAffectedRows
 * get_insert_id        - возращает значение свойства _MysqlInsertId
 * get_var              - возращает первое значение найденое в результате
 * get_num_rows          - возращает результат выполнения функции mysql_num_rows
 * get_num_fields       - возращает результат выполнения функции mysql_num_fields
 */
class DBResult
{
    var $_oResult;
    var $_MysqlAffectedRows;
    var $_MysqlInsertId;
    var $_fetchArr;

	/**
	 * @param resource $result  - ссылка на ресурс ответа базы данных на запрос
	 * @param int $affRows      - количество измененых строк
	 * @param int $insertId     - последний вставленый ключ поля с атрибутом auto_increment
	 */

    function __construct( $result, $affRows = 0, $insertId = 0 )//После запроса делает выборку значений полей в массив
    {
	    $this -> _oResult           = $result;
        $this -> _MysqlAffectedRows = $affRows;
        $this -> _MysqlInsertId     = $insertId;
        $this -> _fetchArr          = FALSE;
    }

    /**
     * возращает результат выполнения функции mysql_data_seek
     *
     * @param int $index
     * @return bool
     */
    function get_data_seek( $index = 0 )
    {
        return mysql_data_seek( $this -> _oResult, $index );
    }

    /**
     * возращает массив данных результат выполнения функции mysql_fetch_array
     *
     * @param string $key - ключь, для групировки
     * @return unknown
     */
    function get_fetch_array( $key = FALSE )
    {
        if( $this -> _fetchArr )
            return $this -> _fetchArr;
        while( $row =  $this -> get_fetch_ass() )
            if( $key && array_key_exists( $key, $row ) )
                $this -> _fetchArr[$row[$key]]    = $row;
            else
                $this -> _fetchArr[]    = $row;
        return $this -> _fetchArr;
    }

    /**
     * возращает результат выполнения функции mysql_fetch_array
     *
     * @param int $result_type
     * @return array
     */
    function get_fetch_arr( $result_type = MYSQL_BOTH )
    {
        return mysql_fetch_array( $this -> _oResult, $result_type );
    }

    /**
     * возращает результат выполнения функции mysql_fetch_assoc
     *
     * @return array
     */
    function get_fetch_ass()
    {
	    return mysql_fetch_assoc( $this -> _oResult );
    }

    /**
     * возращает результат выполнения функции mysql_fetch_row
     *
     * @param int $index
     * @return string
     */
    function get_fetch_field( $index )
    {
        return mysql_fetch_field( $this -> _oResult, $index );
    }

    function get_result( $index = 0 )
    {
        //if( $this -> get_num_rows() ) /*Закоментировано условие, т.к. такие ошибки нужно отлавливать*/
            return mysql_result( $this -> _oResult, $index );
        //else
        //    return $default;
    }

    /**
     * возращает результат выполнения функции mysql_fetch_row
     *
     * @return unknown
     */
    function get_fetch_row()//После запроса делает выборку значений полей в массив
    {
        return mysql_fetch_row( $this -> _oResult );
    }

    /**
     * возращает значение свойства _MysqlAffectedRows (количество измененых строк)
     *
     * @return unknown
     */
    function get_affected_rows()
    {
        return $this -> _MysqlAffectedRows;
    }

    /**
     * возращает значение свойства _MysqlInsertId (последнего вставленого ключа поля с атрибутом auto_increment)
     *
     * @return unknown
     */
    function get_insert_id()
    {
        return $this -> _MysqlInsertId;
    }

    /**
     * возращает первое значение найденое в результате
     *
     * @param string $key   - имя ключа поиска
     * @param mixed $def    - значение возращаемое по умолчанию
     * @return mixed        - если найден ключ, то возращает значение ключа, в противном случаи значение по умолчанию
     */
    function get_var( $key, $def = FALSE )
    {
        while( $row =  $this -> get_fetch_ass() )
            if( array_key_exists( $key, $row ) )
                return $row[$key];
        return $def;
    }

    /**
     * возращает результат выполнения функции mysql_num_rows (количество строк)
     *
     * @return int
     */
    function get_num_rows()
    {
        if( $this -> _oResult )
            return mysql_num_rows( $this -> _oResult );
	    else
		    return 0;
    }

    /**
     * возращает результат выполнения функции mysql_num_fields (количество столбцов)
     *
     * @return int
     */
    function get_num_fields()
    {
        return mysql_num_fields( $this -> _oResult );
    }
}

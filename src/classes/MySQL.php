<?
/**
 * Class MySQL
 */
class MySQL
{
	/**
	 * @var bool
	 */
	protected $_connect_error = FALSE;
	/**
	 * @var resource
	 */
	protected $_db_conn;
	/**
	 * @var bool
	 */
	protected $_db_select_error = FALSE;
	/**
	 * @var string
	 */
	protected $_db_name = '';
	/**
	 * @var bool
	 */
	protected $_debug = FALSE;
	/**
	 * @var bool
	 */
	protected $_log = FALSE;
	/**
	 * @var bool
	 */
	protected $_query_error = FALSE;
	/**
	 * @var string
	 */
	protected $_query_last = '';
	/**
	 * @var array
	 */
	/**
	 * @var array
	 */
	protected $_QueryLog = array();
	/**
	 * @var array
	 */
	protected $_QueryLogCall = array();

	/**
	 * @var resource
	 */
	protected $_db_result;
	/**
	 * @var int
	 */
	protected $_aff_rows;

	/**
	 * @var int
	 */
	protected $_last_insert_id;
	/**
	 * @var array
	 */
	protected $_TableColumns = array();
	/**
	 * @var int
	 */
	protected $_limit_inserts_numb = 500;

	/**
	 * Constructor
	 *
	 * @param array $Config - массив вида array( 'db_host' => host name,
	 *                                            'db_name' => name of the database,
	 *                                            'db_user_name' => user name,
	 *                                            'db_password' => password
	 *
	 * @return MySQL
	 */
	function __construct( $Config )
	{
		if ( count( $Config ) )
			$this->connect( $Config['db_host'], $Config['db_name'], $Config['db_user_name'], $Config['db_password'], $Config['db_port'], isset ( $Config['db_charset'] ) ? $Config['db_charset'] : 'utf8', isset ( $Config['db_collation'] ) ? $Config['db_collation'] : 'utf8_general_ci' );
	}

	/**
	 * connects to DB, selects table
	 *
	 * @param string $dbHost        - Host
	 * @param string $dbName        - database Name
	 * @param string $dbLogin       - Login
	 * @param string $dbPassword    - Password
	 * @param bool $dbPort          - Port number
	 * @param string $character     - Encoding
	 * @param string $collation     - Collation
	 * @return bool                 - Success flag
	 */

	function connect( $dbHost, $dbName, $dbLogin, $dbPassword, $dbPort = FALSE, $character = 'utf8', $collation = 'utf8_general_ci' )
	{
		$this->_db_name = $dbName;
		$this->_db_conn = mysql_connect( ( ( $dbPort ) ? "$dbHost:$dbPort" : "$dbHost" ), $dbLogin, $dbPassword );
		if ( !( $this->_db_conn ) )
		{
			$this->_connect_error = mysql_error();
			$this->debugger();
			return FALSE;
		}

		if ( !mysql_select_db( $this->_db_name, $this->_db_conn ) )
		{
			$this->_db_select_error = mysql_error();
			$this->debugger();
			return FALSE;
		}
		$this->query( "set character_set_client='" . $character . "'", __FILE__, __LINE__ );
		$this->query( "set character_set_results='" . $character . "'", __FILE__, __LINE__ );
		$this->query( "set collation_connection='" . $collation . "'", __FILE__, __LINE__ );
		return true;
	}

	/**
	 * Close connection
	 * @return bool
	 */
	function disconnect()
	{
		return mysql_close( $this->_db_conn );
	}

	/**
	 * Prepares a query to insert into a table.
	 *
	 * @param string $tbl               - table name
	 * @param array $arr                - array of values
	 * @param bool $add_creator         - add creator flag
	 * @param string $file_name         - file name
	 * @param int $line                 - line number
	 * @param string $add_str           - additional information
	 *
	 * @return bool|resource
	 */
	function insert_arr( $tbl, $arr, $add_creator = FALSE, $file_name = '', $line = 0, $add_str = '' )
	{
		if ( $add_str )
			$this->_QueryLogCall[] = array( 'insert_arr' => $add_str );

		if ( $file_name || $line )
			$this->_QueryLogCall[] = array( 'insert_arr' => $file_name . ' [' . $line . ']' );

		if ( $add_creator )
		{
			$arr[$tbl . '_creator'] = AppConf::getIns()->user;
			$arr[$tbl . '_create_date'] = new NoEscapeClass( 'NOW()' );
		}

		$query = "
            INSERT INTO
        $tbl
        " . $this->prepare_query( $arr );

		return $this->query( $query, $file_name, $line, $add_str );
	}

	/**
	 * Prepares part of a query
	 * @param array $arr      - array with data
	 * @return string  - part of query
	 */
	function prepare_query( $arr )
	{
		/**
		 * @var string|NoEscapeClass $value
		 */
		$tmpSet = array();

		foreach ( $arr AS $key => $value )
			$tmpSet[] = "`$key` = " . ( ( is_object( $value ) && get_class( $value ) == "NoEscapeClass" ) ? $value->get_value() : "'" . mysql_real_escape_string( $value ) . "'" );

		return count( $tmpSet ) ? "     SET
	    " . implode( ", \n", $tmpSet ) : "";
	}

	/**
	 * Prepare WHERE part
	 *
	 * @param array|string $WhereCond - array of conditions
	 *
	 * @return string  - WHERE part of query
	 */
	function prepare_where( $WhereCond )
	{
		return is_array( $WhereCond ) && count( $WhereCond ) ? " WHERE " . implode( ' AND ', $WhereCond ) : $WhereCond;
	}

	/**
	 * Runs a query
	 *
	 * @param string $query        - query string
	 * @param bool|string $file_name - path to file
	 * @param bool|string $line - line number
	 * @param bool|string $add_str - add info
	 *
	 * @return bool|resource
	 */
	function query( $query, $file_name = FALSE, $line = FALSE, $add_str = FALSE )
	{
		$trace_str = '';
		if ( !$file_name || !$line )
		{
			$trace_arr = debug_backtrace();
			$trace_str = Func::parseDebugTrace( $trace_arr, false );
		}

		if ( $add_str )
			$this->_QueryLogCall[] = array( 'query' => $add_str );

		if ( $file_name || $line )
			$this->_QueryLogCall[] = array( 'query' => $file_name . ' [' . $line . ']' );

		unset( $this->_db_result );

		$this->_query_last = $query;

		$last_log_key = $this->_debug ? $this->get_last_log_key() + 1 : 0;

		$start_time = microtime( TRUE );
		$start_memory = memory_get_usage();
		if ( $this->_debug )
		{
			$this->_QueryLog[$last_log_key] = array(
				'FILE' => $file_name,
				'LINE' => $line,
				'ADD_STR' => $add_str,
				'QUERY' => $query,
			);
		}

		/*--- Debug Data */
		$mysql_query = $query . "\n/* [" . date( 'Y-m-d h:i:s' ) . "]\n" .
				( $file_name && $line ? $file_name . "[" . $line . "]" . "\n" . str_replace( '*/', '*\/', $add_str ) : $trace_str ) . "*/";

		$this->_db_result = mysql_query( $mysql_query, $this->_db_conn )
		or query_error( $file_name, $line, $mysql_query . ( $add_str ? "\n#" . str_replace( "\n", "\n#", $add_str ) : '' ) );

		$this->_aff_rows = mysql_affected_rows();
		$this->_last_insert_id = mysql_insert_id();

		if ( $this->_debug )
		{
			/*+++ Debug Data */
			$this->_QueryLog[$last_log_key]['time_query'] = microtime( TRUE ) - $start_time;
			$this->_QueryLog[$last_log_key]['memory_use'] = '~' . number_format( ( memory_get_usage() - $start_memory ), 2, ',', ' ' ) . 'bytes';
			$this->_QueryLog[$last_log_key]['affected_rows'] = $this->_aff_rows;
			$this->_QueryLog[$last_log_key]['num_rows'] = @mysql_num_rows( $this->_db_result );
			$this->_QueryLog[$last_log_key]['insert_id'] = $this->_last_insert_id;
			$this->_QueryLog[$last_log_key]['result'] = $this->_db_result;
			$this->_QueryLog[$last_log_key]['info'] = mysql_info( $this->_db_conn );
			/*--- Debug Data */

			$this->_query_error = mysql_error();
			$this->debugger();
		}
		if ( $this->_log || !$this->_db_result )
			Debug::in_file( ( !$this->_db_result ? "ERROR QUERY: " : '' ) . $mysql_query . "\r\n----------------\r\n" );


		return $this->_db_result;
	}

	/**
	 * Set debug and log flags
	 *
	 * @param boolean $debug
	 * @param boolean $log
	 */
	public function setDebug( $debug = FALSE, $log = false )
	{
		$this->_debug = $debug;
		$this->_log = $log;
	}

	/**
	 * Updates table with data in array
	 *
	 * @param string $tbl      - table name
	 * @param array $arr       - data array
	 * @param bool $add_creator - flag, show if changer fields should be updated
	 * @param array|bool $WhereCond - array of conditions
	 * @param bool|string $file_name - path to file
	 * @param bool|string $line - line number
	 * @param bool $add_str - additional information
	 * @return bool|resource
	 */
	function update_arr( $tbl, $arr, $add_creator = FALSE, $WhereCond = FALSE, $file_name = FALSE, $line = FALSE, $add_str = FALSE )
	{
		if ( $add_str )
			$this->_QueryLogCall[] = array( 'update_arr' => $add_str );

		if ( $file_name || $line )
			$this->_QueryLogCall[] = array( 'update_arr' => $file_name . ' [' . $line . ']' );

		if ( $add_creator )
		{
			$arr[$tbl . '_changer'] = AppConf::getIns()->user;
			$arr[$tbl . '_change_date'] = new NoEscapeClass( 'NOW()' );
		}

		$query = "
            UPDATE
        $tbl
        " . $this->prepare_query( $arr ) . $this->prepare_where( $WhereCond );

		return $this->query( $query, $file_name, $line, $add_str );
	}

	/**
	 * Start transaction
	 *
	 * @return bool|resource
	 */
	function begin()
	{
		$this->query( "START TRANSACTION" );
		return $this->query( "BEGIN" );
	}

	/**
	 * Commit transaction
	 *
	 * @return bool|resource
	 */
	function commit()
	{
		return $this->query( "COMMIT" );
	}

	/**
	 * Rollback transaction
	 *
	 * @return bool|resource
	 */
	function rollback()
	{
		return $this->query( "ROLLBACK" );
	}

	/**
	 * Runs query transaction
	 *
	 * @param array $QueryArr - array of queries
	 * @return bool
	 */
	function transaction( $QueryArr )
	{
		$retval = TRUE;

		$this->begin();

		foreach ( $QueryArr as $query )
		{
			$this->query( $query );
			if ( $this->_aff_rows == 0 )
				$retval = FALSE;
		}

		if ( !$retval )
		{
			$this->rollback();
			return false;
		}
		else
		{
			$this->commit();
			return true;
		}
	}

	/**
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
		$ret = array();
		$sql = rtrim( $sql, "\n\r" );
		$sql_len = strlen( $sql );
		$string_start = '';
		$in_string = FALSE;
		$nothing = TRUE;
		$time0 = time();

		for ( $i = 0; $i < $sql_len; ++$i )
		{
			$char = $sql[$i];

			// We are in a string, check for not escaped end of strings except for
			// backquotes that can't be escaped
			if ( $in_string )
			{
				for ( ; ; )
				{
					$i = strpos( $sql, $string_start, $i );
					// No end of string found -> add the current substring to the
					// returned array
					if ( !$i )
					{
						$ret[] = array( 'query' => $sql, 'empty' => $nothing );
						return $ret;
					}
					// Backquotes or no backslashes before quotes: it's indeed the
					// end of the string -> exit the loop
					elseif ( $string_start == '`' || $sql[$i - 1] != '\\' )
					{
						$string_start = '';
						$in_string = FALSE;
						break;
					}
					// one or more Backslashes before the presumed end of string...
					else
					{
						// ... first checks for escaped backslashes
						$j = 2;
						$escaped_backslash = FALSE;
						while ( $i - $j > 0 && $sql[$i - $j] == '\\' )
						{
							$escaped_backslash = !$escaped_backslash;
							$j++;
						}
						// ... if escaped backslashes: it's really the end of the
						// string -> exit the loop
						if ( $escaped_backslash )
						{
							$string_start = '';
							$in_string = FALSE;
							break;
						}
						// ... else loop
						else
							$i++;
					} // end if...elseif...else
				} // end for
			} // end if(in string)

			// lets skip comments (/*, -- and #)
			elseif ( ( $char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ' ) || $char == '#' || ( $char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*' ) )
			{
				$i = strpos( $sql, $char == '/' ? '*/' : "\n", $i );
				// didn't we hit end of string?
				if ( $i === FALSE )
					break;
				if ( $char == '/' )
					$i++;
			}

			// We are not in a string, first check for delimiter...
			elseif ( $char == ';' )
			{
				// if delimiter found, add the parsed part to the returned array
				$ret[] = array( 'query' => substr( $sql, 0, $i ), 'empty' => $nothing );
				$nothing = TRUE;
				$sql = ltrim( substr( $sql, min( $i + 1, $sql_len ) ) );
				$sql_len = strlen( $sql );
				if ( $sql_len )
					$i = -1;
				else
					// The submited statement(s) end(s) here
				return $ret;
			} // end elseif(is delimiter)

			// ... then check for start of a string,...
			elseif ( ( $char == '"' ) || ( $char == '\'' ) || ( $char == '`' ) )
			{
				$in_string = TRUE;
				$nothing = FALSE;
				$string_start = $char;
			} // end elseif(is start of string)
			elseif ( $nothing )
				$nothing = FALSE;

			// loic1: send a fake header each 30 sec. to bypass browser timeout
			$time1 = time();
			if ( $time1 >= $time0 + 30 )
			{
				$time0 = $time1;
				header( 'X-Ping: Pong' );
			} // end if
		} // end for

		// add any rest to the returned array
		if ( !empty( $sql ) && preg_match( '@[^[:space:]]+@', $sql ) )
			$ret[] = array( 'query' => $sql, 'empty' => $nothing );

		return $ret;
	}

	/**
	 * @return array
	 */
	public function get_fetch_ass()
	{
		var_dump ( $this->_db_result );
		return mysql_fetch_assoc( $this->_db_result );
	}

	/**
	 * @return int
	 */
	public function get_num_rows()
	{
		return mysql_num_rows( $this->_db_result );
	}

	/**
	 * @param int $index
	 * @return string
	 */
	public function get_result( $index = 0 )
	{
		return mysql_result( $this->_db_result, $index );
	}

	/**
	 * @return int
	 */
	public function getAffRows()
	{
		return $this->_aff_rows;
	}

	/**
	 * @return int
	 */
	public function getLastInsertId()
	{
		return $this->_last_insert_id;
	}

	/**
	 * For debugging
	 */
	function debugger()
	{
		if ( $this->_debug && ( AppConf::getIns()->dev_server || AppConf::getIns()->admin_flag ) )
		{
			$str = "<br><font color=#ff0000>"
					. "_connect_error='" . $this->_connect_error . "'<br>"
					. "_db_select_error='" . $this->_db_select_error . "'<br>"
					. "_query_error='" . $this->_query_error . "'<br>"
					. "_query_last='" . $this->_query_last . "'<br>
                 <pre>" . print_r( $this->_QueryLog, true ) . "</pre>
                 </font>";

			if ( AppConf::getIns()->ajax_flag && AppConf::getIns()->dev_server )
				mail( '', '', str_replace( "<br>", "\n", $str ) );
			else
				echo $str;
		}
	}

	/**
	 * Returns last autoincrement ID (synonym)
	 *
	 * @return integer or FALSE
	 */
	function get_last_insert_id()
	{
		return $this->getLastInsertId();
	}

	/**
	 * @return int
	 */
	function get_last_log_key()
	{
		return count( $this->_QueryLog ) - 1;
	}

	/**
	 * get last query
	 *
	 * @return string
	 */
	function get_last_query()
	{
		return $this->_query_last;
	}

	/**
	 * @return string
	 */
	function get_founds_rows()
	{
		$query = "SELECT FOUND_ROWS()";
		$this->query( $query, __FILE__, __LINE__ );

		return $this->get_result( 0 );
	}

	/**
	 * Return number of affected rows (synonym)
	 * @return int
	 */
	function get_affected_rows()
	{
		return $this->getAffRows();
	}

	/**
	 * Returns name of the columns for specified table name
	 * @param $table_name - name of the table
	 * @param bool $incl_fields - flag
	 * @return mixed
	 */

	function get_columns_info( $table_name, $incl_fields = true )
	{
		if ( !array_key_exists( $table_name, $this->_TableColumns ) )
		{
			$this->_TableColumns[$table_name] = array();

			$query = "SHOW COLUMNS FROM " . $table_name;
			$this->query( $query );

			while ( $row = $this->get_fetch_ass() )
				$this->_TableColumns[$table_name][$row['Field']] = $incl_fields ? $row['Field'] : '';
		}
		return $this->_TableColumns[$table_name];
	}
}

/**
 * Class NoEscapeClass
 */
class NoEscapeClass
{
	/**
	 * @var string
	 */
	protected $_value;

	/**
	 * Constructor
	 *
	 * @param string $value     - значение
	 * @return NoEscapeClass
	 */
	function NoEscapeClass( $value )
	{
		$this->_value = $value;
	}

	/**
	 * Get _value
	 * @return mixed
	 */
	function get_value()
	{
		return $this->_value;
	}
}
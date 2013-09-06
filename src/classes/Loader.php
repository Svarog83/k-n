<?php
class Loader
{
    function __construct()
    {
    }
    
	/*
	 * @param config - array of DB settings
	 * @return MySQL
	 */
    public static function DB( $config = array(), $action = "connect" )
    {
	    /**@var MySQL */
	    static $DB;

	    if ( $action == 'connect')
	    {
		    if( !$DB  )
				$DB       = new MySQL( $config );
	    }
	    else if ( $action == 'reconnect' )
	    {
		    $DB->disconnect();
		    $DB = new MySQL( $config );
	    }
	    else if ( $action == 'disconnect' )
	    {
		    $DB->disconnect();
		    unset ( $DB );
	    }

	    return $DB;
    }

    /**
     *
     * @param string $className - имя класса
     * 
     */
    public static function autoLoader( $className )
    {
        static $MainIncludePath;
        if( !$MainIncludePath ) // Если не установлены пути
        {
            $DocRoot = $_SERVER['DOCUMENT_ROOT'];

            $directories = array(
              $DocRoot .'/classes/'
            );
            $MainIncludePath = get_include_path() . implode( PATH_SEPARATOR, $directories );
            
            set_include_path( $MainIncludePath );
        }

        // Преобразуем имя класса в путь и имя файла
        $className = str_ireplace( '_', '/', $className );
        
        if( @include_once( $className.'.php' ) )
            return;

	    //Маски возможных имен файла
        $fileNameFormats = array(
          '%s.class.php',
          '%s.inc.php',
          '%s.php'
        );

        // Если не удалось сразу подключить файл, то пытаемся подключить по имени из шаблона
        foreach( $fileNameFormats as $fileNameFormat )
        {
            $path = sprintf( $fileNameFormat, $className );

            if( @include_once( $path ) )
                return;
        }
    }
}

function __autoload( $class_name )
{
    Loader::autoLoader( $class_name );
}

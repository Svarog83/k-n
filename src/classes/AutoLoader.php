<?php
class AutoLoader
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
        static $incl_path;
        if( !$incl_path )
        {
            $dir_arr = array(
	            $_SERVER['DOCUMENT_ROOT'] .'../src/classes/'
            );
            $incl_path = get_include_path() . implode( PATH_SEPARATOR, $dir_arr );

            set_include_path( $incl_path );
        }

	    if ( strpos ( $className, 'Bundle' ) === false )
	    {
	        //if we are loading basic classes
		    $className = str_ireplace( '_', '/', $className );

	        if( @include_once( $className.'.php' ) )
	            return;

		    //Маски возможных имен файла
	        $file_formats = array(
	          '%s.class.php',
	          '%s.inc.php',
	          '%s.php'
	        );

	        // Если не удалось сразу подключить файл, то пытаемся подключить по имени из шаблона
	        foreach( $file_formats as $file_format )
	        {
	            $path = sprintf( $file_format, $className );

	            if( @include_once( $path ) )
	                return;
	        }
	    }
	    else
	    {
		    //if we need to load all classes of the specified Bundle
		    include_once ( AppConf::getIns()->root_path . '/src/' . $className . '/' . $className . '.php' );
		    $conrollers = glob ( AppConf::getIns()->root_path . '/src/' . $className . '/Controller/*Controller.php' );
		    foreach ( $conrollers AS $conroller )
			    include_once ( $conroller );

	    }
    }
}

function __autoload( $class_name )
{
    AutoLoader::autoLoader( $class_name );
}

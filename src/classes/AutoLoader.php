<?php
use SDClasses\MySQL;
use SDClasses\AppConf;

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
	    /**@var \SDClasses\SafeMySQL */
	    static $DB;

	    if ( $action == 'connect')
	    {
		    if( !$DB  )
				$DB       = new \SDClasses\SafeMySQL( $config );
	    }
	    else if ( $action == 'reconnect' )
	    {
		    $DB = new \SDClasses\SafeMySQL( $config );
	    }
	    else if ( $action == 'disconnect' )
	    {
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

	    if ( strpos( $className, '\\' ) !== false )
	    {
		    $className = explode ( '\\', $className )[1];
	    }

//	    echo '<br>$ = ' . $className . '<br><br>';

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

	        if( include_once( $className.'.php' ) )
	            return;

	        $file_formats = array(
	          '%s.php'
	        );

	        foreach( $file_formats as $file_format )
	        {
	            $path = sprintf( $file_format, $className );

	            if( include_once( $path ) )
	                return;
	        }
	    }
	    else
	    {
		    //if we need to load all classes of the specified Bundle
		    if ( file_exists( AppConf::getIns()->root_path . '/src/' . $className . '/' . $className . '.php' ) )
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

if( function_exists('date_default_timezone_set') )
    date_default_timezone_set( 'Europe/Moscow' );

function our_error_handler( $errno, $errstr, $errfile, $errline, $vars )
{
	AppConf::our_error_handler( $errno, $errstr, $errfile, $errline, $vars );
}

set_error_handler( "our_error_handler" );


<?php

namespace SDClasses;


class Debugger
{
	/**
	 * @var string
	 */
	protected $_display_errors        = true;
	/**
	 * @var int
	 */
	protected $_error_reporting       = E_ALL;
	/**
	 * @var int
	 */
	protected $_max_execution_time    = 3600;
	/**
	 * @var string
	 */
	protected $_memory_limit          = '1024M';

	public static function in_file( $text, $path = '', $file = FALSE, $mode = "a+" )
    {
        if  ( !$path )
	        $path = AppConf::getIns()->root_path . "/app/logs";

        $file = $file ? $file : date( "y.m.d" ).".php";
        $filename = $path."/".$file;

        $fp = fopen( $filename, $mode);
        if( $fp )
            fwrite( $fp, $text );
        fclose( $fp );
    }

    function set_limits()
    {
        ini_set( 'memory_limit', $this -> _memory_limit );
        ini_set( 'max_execution_time', $this -> _max_execution_time );
    }

    function set_error_report()
    {
        ini_set( 'display_errors', $this -> _display_errors );
        ini_set( 'error_reporting', $this -> _error_reporting);
    }
}
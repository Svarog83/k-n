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
	/**
	 * @var int
	 */
	static protected $_time       = 0;
	/**
	 * @var array
	 */
	static protected $Lines      = array();


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

     public static function reset_time()
     {
         self::$_time = FALSE;
     }

	/**
	 * @param bool $print_data
	 * @param bool $__LINE__
	 * @param bool $__FILE__
	 * @return mixed|string
	 */
	public static function time( $print_data = FALSE, $__LINE__ = FALSE, $__FILE__ = FALSE )
    {
        if( self::$_time === 0 )
            self::$_time = microtime( TRUE );

        $time_diff =  microtime( TRUE ) - self::$_time;

        if( $print_data )
             $time_diff = self::prnt_r( "\t" . $time_diff, $__LINE__, $__FILE__, $print_data );

	    return $time_diff;
    }

	/**
	 * @param $data
	 * @param bool $__LINE__
	 * @param bool $__FILE__
	 * @param bool $print_data
	 * @return string
	 */
	public static function prnt_r( $data, $__LINE__ = FALSE, $__FILE__ = FALSE, $print_data = FALSE )
    {
        $out_str = "\n";
        if( $__FILE__ !== FALSE )
            $out_str .= "$__FILE__";
        if( $__LINE__ !== FALSE )
            self::$Lines[ $__LINE__ ] += 1;
            $out_str .= "[$__LINE__][" . self::$Lines[ $__LINE__ ] . "]";

        $out_str .= print_r( $data, 1 ) . "";
        if( $print_data === TRUE )
            echo $out_str;
        return $out_str;
    }

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
}
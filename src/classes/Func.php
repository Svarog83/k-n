<?php

namespace SDClasses;
use SDClasses\AppConf;
/**
 * Class Func
 */
class Func
{
	static private $_debug;
	static private $_log;
	static public  $CurAbr = array();

	public static function getBrands()
	{
		$DB = AutoLoader::DB();
		$DB->setDebug( self::$_debug,  self::$_log );

		$arr = array();
		$query = "SELECT * FROM brand ORDER BY brand_name";
		$DB->query( $query, __FILE__, __LINE__ );
		while ( $row = $DB->get_fetch_ass() )
			$arr[$row['brand_id']] = $row['brand_name'];

		$DB->setDebug();

		return $arr;
	}

	public static function setDebug( $debug = false, $log = false )
	{
		self::$_debug   = $debug;
		self::$_log     = $log;
	}

	public static function getCurrencies( $only_used = true )
	{
		$DB = AutoLoader::DB();
		$DB->setDebug( self::$_debug,  self::$_log );

		$arr = array();
		$query = "SELECT cur_id, cur_abr FROM currency". ( $only_used ? " WHERE cur_use = 1 " : '' ) ." ORDER BY cur_abr";
		$DB->query( $query, __FILE__, __LINE__ );

		while ( $row = $DB->get_fetch_ass() )
			$arr[ $row['cur_id'] ] = $row['cur_abr'];

		$DB->setDebug();

		return $arr;
	}

	public static function getCurAbr( $cur_id )
	{

		$cur_id = (int)$cur_id;
		$cur_abr = '';

		if ( !count ( self::$CurAbr ) )
		{
			$DB = AutoLoader::DB();
			$DB->setDebug( self::$_debug,  self::$_log );

			$query = "SELECT cur_id, cur_abr FROM currency WHERE 1";
			$DB->query( $query, __FILE__, __LINE__ );
			while ( $row = $DB->get_fetch_ass() )
				self::$CurAbr[$row['cur_id']] = $row['cur_abr'];

			$DB->setDebug();
		}

		if ( isset ( self::$CurAbr[$cur_id] ) )
			$cur_abr = self::$CurAbr[$cur_id];

		return $cur_abr;
	}

	public static function number2( $a )
	{
		if ( $a != '' )
		{
			$a = number_format( (double)str_replace( ",", ".", $a ), 2, ".", "" ) ;
			$a = str_replace( '-0.00', '0.00', $a );
		}
		return( $a );
	}


	/**
	 * Shows date for users in convenient way
	 *
	 * @static
	 * @param  $date - Date in format YYYY-MM-DD
	 * @param string $separator - Separator
	 * @return string Date in format DD.MM.YYYY
	 */
	public static function showDate( $date, $separator = '.' )
	{

	    if( $date == '0000-00-00' || empty( $date ) )
	       return( '' );

	    $arr_tmp = explode( '-', $date );

	    if( count( $arr_tmp ) == 3 )
	       return( $arr_tmp[2] . $separator . $arr_tmp[1] . $separator . $arr_tmp[0] );
	}

	/**
	 * @param $str
	 * @return mixed
	 */
	public static function getPrice( $str )
	{
		return str_replace ( array (' ', ',' ), array ( '', '.' ), $str );
	}

	public static function CheckUser()
	{
		$DB = \AutoLoader::DB();
		$user = (int)AppConf::getIns()->user;
		$uid = AppConf::getIns()->uid;
		$query = "SELECT * FROM user WHERE user_id = '$user' AND user_uid = '" . mysql_real_escape_string( $uid ) . "'";
		$DB->query( $query, __FILE__, __LINE__ );

		$row = $DB->get_fetch_ass();
		if ( $DB->getAffRows() == 1 && is_array( $row ) && $row['user_id'] )
			return true;
		else
			return false;
	}

	function sendMail( $to, $subject, $mess, $headers = '' )
	{
		$send_email_option = AppConf::getIns()->send_email_option;

		$send_email = true;
		if ( isset( $send_email_option ) && $send_email_option )
		{
			if ( $send_email_option === 'no' ) // no need to send any e-mails at all
				$send_email = false;
			else if ( $send_email_option === 'test' ) // add "TEST" warning into the subject
				$subject = '!!FROM TEST SYSTEM!!' . $subject;
			else if ( strpos ( $send_email_option, '@' ) !== false ) // if it's an e-mail address all messages will be sent to the specified e-mail.
				$to = $send_email_option;
		}

		if ( $send_email )
		{
			$ret = mail( $to, $subject, $mess, $headers );
		}
		else
			$ret = 1;

		return $ret;
	}

	function assetLink( $type, $name, $path = '' )
    {
        $out_str        = '';
        $web_root   = AppConf::getIns()->root_path . '/web';
        $path_file  = $web_root . "/$type/" . ( $path ? $path . '/' : '' ) . "$name.$type";

        if( file_exists( $path_file ) )
        {
            $timestamp = filemtime( $path_file );
            $url = "/$type/" . ( $path ? $path . '/' : '' ) . "$name.v$timestamp.$type";

            switch( $type )
            {
                case 'css':
                    $out_str = '<link rel="stylesheet" type="text/css" href="'. $url. '">';
                    break;
                case 'js':
                    $out_str = '<script type="text/javascript" language="Javascript" src="'. $url . '"></script>';
                    break;
            }
        }
        echo $out_str . "\n";
    }

	/**
	 * @param  array $trace_arr
	 * @param bool $show_args
	 * @return string
	 */
	public static function parseDebugTrace ( $trace_arr, $show_args = false )
	{
		$str = '';
		if ( is_array ( $trace_arr ) )
			foreach ( $trace_arr AS $k => $v )
			{
				if ( isset ( $v['line'] ) )
				{
					$file = str_replace ( AppConf::getIns()->root_path, "", str_replace ( "\\", "/", $v['file'] ) );
					$str .= "[$file [{$v['line']}] {$v['function']} of " . ( isset ( $v['class'] ) ? $v['class'] : 'Null' ) .
							( $show_args && isset ( $v['args'] ) ? ", Args: \n" . print_r( $v['args'], true ) . "\n ----------- " : "" ) . "] \n";
				}

				if ( $k == 4 )
					break;
			}
		return $str;
	}

}

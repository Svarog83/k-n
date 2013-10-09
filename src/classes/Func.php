<?php

namespace SDClasses;
use SDClasses\AppConf;

/**
 * Class Func
 */
class Func
{
	static private $_log;

	public static function setLog( $log = false )
	{
		self::$_log = $log;
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

		if ( $date == '0000-00-00' || empty( $date ) )
			return ( '' );

		$arr_tmp = explode( '-', $date );

		if ( count( $arr_tmp ) == 3 )
			return ( $arr_tmp[2] . $separator . $arr_tmp[1] . $separator . $arr_tmp[0] );
	}

	public static function CheckUser()
	{
		$DB = \AutoLoader::DB();
		$user = (int)AppConf::getIns()->user;
		$uid = AppConf::getIns()->uid;

		$row = $DB->getRow( "SELECT * FROM user WHERE user_id = ?s AND user_uid = ?s AND user_activ='a'", $user, $uid );
		if ( $DB->affectedRows() == 1 && is_array( $row ) && $row['user_id'] )
			return true;
		else
			return false;
	}

	public static function formatDate( $date, $format )
	{
		$new_date = '';

		if ( $date )
		{
			$timestamp = strtotime( $date );

			if ( $format == 'dd.mm.yy' )
				$new_date = date( "d.m.Y", $timestamp );
			else
				$new_date = $date;
		}

		return $new_date;
	}

	function assetLink( $type, $name, $path = '' )
	{
		$out_str = '';
		$web_root = AppConf::getIns()->root_path . '/web';
		$path_file = $web_root . "/$type/" . ( $path ? $path . '/' : '' ) . "$name.$type";

		if ( file_exists( $path_file ) )
		{
			$timestamp = filemtime( $path_file );
			$url = "/$type/" . ( $path ? $path . '/' : '' ) . "$name.v$timestamp.$type";

			switch ( $type )
			{
				case 'css':
					$out_str = '<link rel="stylesheet" type="text/css" href="' . $url . '">';
					break;
				case 'js':
					$out_str = '<script type="text/javascript" language="Javascript" src="' . $url . '"></script>';
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
	public static function parseDebugTrace( $trace_arr, $show_args = false )
	{
		$str = '';
		if ( is_array( $trace_arr ) )
			foreach ( $trace_arr AS $k => $v )
			{
				if ( isset ( $v['line'] ) )
				{
					$file = str_replace( AppConf::getIns()->root_path, "", str_replace( "\\", "/", $v['file'] ) );
					$str .= "[$file [{$v['line']}] {$v['function']} of " . ( isset ( $v['class'] ) ? $v['class'] : 'Null' ) .
							( $show_args && isset ( $v['args'] ) ? ", Args: \n" . print_r( $v['args'], true ) . "\n ----------- " : "" ) . "] \n";
				}

				if ( $k == 4 )
					break;
			}
		return $str;
	}

	/** Converts an array to a json format(it's require for cyrillic characters)
	 * @param bool|array $a
	 * @return string
	 */
	public static function php2js( $a = false )
	{
		if ( is_null( $a ) ) return 'null';
		if ( $a === false ) return 'false';
		if ( $a === true ) return 'true';
		if ( is_scalar( $a ) )
		{
			if ( is_float( $a ) )
			{
				// Always use "." for floats.
				$a = str_replace( ",", ".", strval( $a ) );
			}

			// All scalars are converted to strings to avoid indeterminism.
			// PHP's "1" and 1 are equal for all PHP operators, but
			// JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
			// we should get the same result in the JS frontend (string).
			// Character replacements for JSON.
			static $jsonReplaces = array( array( "\\", "/", "\n", "\t", "\r", "\b", "\f", '"' ),
				array( '\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"' ) );
			return '"' . str_replace( $jsonReplaces[0], $jsonReplaces[1], $a ) . '"';
		}
		$isList = true;
		for ( $i = 0, reset( $a ); $i < count( $a ); $i++, next( $a ) )
		{
			if ( key( $a ) !== $i )
			{
				$isList = false;
				break;
			}
		}
		$result = array();
		if ( $isList )
		{
			foreach ( $a as $v ) $result[] = php2js( $v );
			return '[ ' . join( ', ', $result ) . ' ]';
		}
		else
		{
			foreach ( $a as $k => $v ) $result[] = php2js( $k ) . ': ' . php2js( $v );
			return '{ ' . join( ', ', $result ) . ' }';
		}
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
			else if ( strpos( $send_email_option, '@' ) !== false ) // if it's an e-mail address all messages will be sent to the specified e-mail.
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
}

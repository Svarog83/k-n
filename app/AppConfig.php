<?php
session_start();

error_reporting ( E_ALL );
require( $_SERVER['DOCUMENT_ROOT'] . '/classes/Loader.php' );

ini_set( 'MAX_EXECUTION_TIME', 1900 );

$DOCUMENT_ROOT  = $_SERVER['DOCUMENT_ROOT'];
$HTTP_REFERER   = isset ( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

if( function_exists('date_default_timezone_set') )
    date_default_timezone_set( 'Europe/Moscow' );


class AppConfig
{
	/** @var AppConfig */
	private static $instance;

	/** @var bool */
	public $local_server;

	/** @var int Разница времени в часах между default_timezone_set и Гринвичем*/
	public $STD;

	/** @var int*/
	public $TimeForAction;

	/** @var bool Показывает, какое сейчас время на сервере - летнее(TRUE) или зимнее(FALSE)*/
	public $IS_DST;

	/** @var int Лимит работы в системе в часах*/
	public $time_limit;

	/** @var int*/
	public $time_limit_update;

	/** @var string*/
	public $Charset;

	/** @var string*/
	public $DBCharset;

	/** @var string*/
	public $admin_mail;

	/** @var array*/
	public $admin_email;

	/** @var array*/
	public $login_admin_arr;

	/** @var string*/
	public $www_main;

	/** @var string*/
	public $root;

	/** @var string*/
	public $directory;

	/** @var array*/
	public $db_main_settings = array();

	/** @var array*/
	public $db_settings = array();

	/** @var string*/
	public $send_email_option;

	/** @var bool*/
	public $test_base = false;

	/** @var bool*/
	public $debug_mode = false;

	/** @var int*/
	public $user = 0;

	/** @var string*/
	public $uid = false;

	/** @var bool*/
	public $ajax_flag = false;

	/** @var bool*/
	public $flag_return_buffer = false;

	/** @var array*/
	public $ajax_return = array();

	/** @var bool*/
	public $admin_flag = false;

	/** @var bool - показывает, используется ли в текущей базе Летнее время*/
	public $BMDST = true;

	public $modal_window = false;

	// Private constructor to limit object instantiation to within the class
	private function __construct()
	{
		$this->ajax_return['success']         = false;
		$this->ajax_return['return_params']   = array();
		$this->ajax_return['return_text']     = '';
	}

	final protected function __clone()
	{
        // no cloning allowed
    }

	/*
	 * @return AppConfig
	 */
	public static function getIns()
	{
		if ( self::$instance === null )
		{
			self::$instance = new AppConfig();
		}

		return self::$instance;
	}
}

$AC = AppConfig::getIns();


if ( isset ( $_SERVER['SERVER_ADDR'] ) && strpos ( $_SERVER['SERVER_ADDR'], '127.0' ) !== false )
    $AC->local_server		= TRUE;
else
	$AC->local_server 		= FALSE;

$AC->STD				= '0';
$AC->TimeForAction		= 1800;

/**
 * Determines if server's time is DST time or not;
 *
 * @return bool
 */
if ( !function_exists( 'is_dst' ) )
{
	function is_dst()
	{
		$lt = localtime ( time(), 1 );
		$dst = ( $lt["tm_isdst"] == 1 ? true : false ); // zero = standard time
		return( $dst );
	}
}

$AC->IS_DST = is_dst();

$AC->Charset 	= 'utf-8';
$AC->DBCharset 	= 'utf8';

if ( $AC->local_server )
{
	$AC->time_limit			= 96;
	$AC->time_limit_update	= 96;
    $AC->STD				=  0;
	$AC->www_main			= 'rems';
}
else
{
	$AC->time_limit			= 56;
	$AC->time_limit_update	= 1;

	$AC->www_main			= 'rems.ru';
}

$AC->admin_email		=  array(
'support_rems@itgr.ru'
);
$AC->admin_mail = $AC->admin_email[0];

$AC->login_admin_arr = array(
'igortom' ,
'itom' ,
'dlap' ,
'Sobaka4' ,
'Svarog',
'Anton',
'Dee'
);

$AC->root				= $_SERVER['DOCUMENT_ROOT'] .'/SysMain';
$AC->directory          = 'SysMain';

if( $AC->local_server )
{
	$AC->db_main_settings = array (
	'db_host'	=> 'localhost',
	'db_name'		=> 'db_main_rems',
	'db_user_name'	=> 'root',
	'db_password'	=> '',
	'db_port' => '3306' );


}
else
{
	$AC->db_main_settings = array (
	'db_host'	=> 'localhost',
	'db_name'	=> 'db_main_rems',
	'db_user_name'	=> 'rems',
	'db_password'	=> 'dyny91lJbEUN',
	'db_port' => '3306' );

}

if ( !function_exists( 'check_admin_login' ) )
{
	function check_admin_login( $login )
	{
		$login_admin_arr = AppConfig::getIns()->login_admin_arr;

		if ( is_array( $login_admin_arr ) )
			foreach ( $login_admin_arr AS $login_admin )
			{
				if ( strpos ( $login, $login_admin ) !== false )
					return true;
			}

		return false;
	}
}

if ( !function_exists( 'eu' ) )
{
	function eu( $sFile, $iLine, $sQuery ) 		{

		global $UA;
		$local_server = AppConfig::getIns()->local_server;
		$admin_email = AppConfig::getIns()->admin_email;
		$sFile = str_replace ( $_SERVER['DOCUMENT_ROOT'], "", str_replace ( "\\", "/", $sFile ) );


		$t =  '<pre><span style="color:red;"><b>SQL_Error</b>:</span><br>file: <b>' .
		$sFile .
		'</b><br> line: <b>'.

		$iLine .
		'</b><br><b>'.

		$sQuery .
		'</b><br>'.
		mysql_errno().
		'<br>'.
		mysql_error().
		'<br></pre>' ;

		$tt = '<span style="color:red;"><hr style="color:red">Sorry, the script was stoped for the MySQL error!!<br>Please, be patient - Mail was sent to Administrators of REMS and the Error will be fixed ASAP<br>You will be informed about this<b></b><hr style="color:red"></span>';

		if( $UA['user_admin'] == 'a' || $local_server )
		{
			echo $t;
	?><pre>$_REQUEST:
	<? print_r( $_REQUEST  ) ?></pre><br><?
		}
		else
		{

			$t_admin = '

Dear, dear Admin!!!!!!!

This is REMS.....

Sorry for disturbance, but damned MySQL doesnt want to work good - give it a kick!

MySQL_Error----------------------------------------------

file: '. $sFile . '
line: '. $iLine .'
database: $$$DATABASE$$$
'. $sQuery . '

'. mysql_errno(). '
'. mysql_error().'

User: '. AppConfig::getIns()->user.'
'. $UA['user_fam_eng'] .'  '. $UA['user_name_eng'] .'
'. $UA['user_email'] .'

Vars:
REQUEST --------------
'.

			print_r( $_REQUEST, true )

			.'
Time---------------
'.


			date( "Y-m-d, H:i:s", time() )

			.'

Referer------------
'.


			$_SERVER["HTTP_REFERER"]

			.'

Browser-----------
'.

			$_SERVER["HTTP_USER_AGENT"]

			.'

With big respects,
REMS

';
			$r = mysql_query( "SELECT DATABASE()" );
   			$ttt = @mysql_result($r, 0 );

   			$t_admin = str_replace( '$$$DATABASE$$$', $ttt, $t_admin );

			echo $tt;
			@mail( implode( ',', $admin_email ), 'MySQL Error REMS', $t_admin );


		}

		die;

	}
}

if ( !function_exists( 'rems_error_handler' ) )
{
	function rems_error_handler( $errno, $errstr, $errfile, $errline, $vars ) 		{

		if( error_reporting() )
		{
			$local_server       = AppConfig::getIns()->local_server;
			$admin_email        = AppConfig::getIns()->admin_email;
			$super_admin_flag   = AppConfig::getIns()->admin_flag;
			$ajax_flag          = AppConfig::getIns()->ajax_flag;

			global $UA;
			global $BMAbr;

			$trace_arr = debug_backtrace(  );
	        $trace_str = parse_debug_trace( $trace_arr, false );


			$t =  '<br><span style="color:red;"><b>PHP Error</b>:</span><br>
Description: '.$errstr.'
<br>
file: <b>' . $errfile .	'</b> line: <b>'. $errline .'</b><br>';

			if( !$ajax_flag && ( $super_admin_flag || $local_server ) )
			{
				echo $t;
			}

			$t_admin = '

Dear, dear Admin!!!!!!!

This is REMS.....

Sorry for disturbance, but damned REMS script doesnt want to work good - give it a kick!

PHP_Error----------------------------------------------

file: '. $errfile . '
line: '. $errline .'
error number: '. $errno .'
error description: '. $errstr .'
BMAbr: '. $BMAbr .'

User: '. AppConfig::getIns()->user.'
'. $UA['user_fam_eng'] .'  '. $UA['user_name_eng'] .'
'. $UA['user_email'] .'

Call Stack:
----------
' .  $trace_str . '
----------

Vars:
REQUEST --------------
'.
            print_r( $_REQUEST, true )

			.'
Time---------------
'.


			date( "Y-m-d, H:i:s", time() )

			.'

Referer------------
'.


			$_SERVER["HTTP_REFERER"]

			.'

Browser-----------
'.

			$_SERVER["HTTP_USER_AGENT"]

			.'

With big respects,
REMS

';
			@mail( implode( ',', $admin_email ), 'PHP Error REMS', $t_admin );


		}
	}
}

if ( !function_exists( 'List_Export' ) )
{
	function List_Export( $buffer )	{

		$buffer =
		strip_tags($buffer, '<table><tr><td><b><i><u><br><style><span><html><head><body><title><meta>');
		$buffer = preg_replace("/((\r)?\n)+/", "\r\n", $buffer);
		$buffer = preg_replace("/<!-- REMOVE_FROM\/\/-->.+<!-- REMOVE_TO\/\/-->/Ui", "", $buffer);
		$buffer = preg_replace("/title ?= ?\"[^\?]*\"/Ui", "", $buffer);
		return $buffer;
	}
}

if ( !function_exists( 'mail_rems' ) )
{
	function mail_rems( $to, $subject, $mess, $headers = '' )
	{
		$send_email_option = AppConfig::getIns()->send_email_option;


		$send_email = true;
		if ( isset( $send_email_option ) && $send_email_option )
		{
			if ( $send_email_option === 'no' ) // no need to send any e-mails at all
				$send_email = false;
			else if ( $send_email_option === 'test' ) // add "TEST" warning into the subject
				$subject = '!!FROM TEST REMS!!' . $subject;
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
}

if ( !function_exists( 'set_script_link' ) )
{
    function set_script_link( $type, $name, $path = '' )
    {
        $uot        = '';
        $doc_root   = $_SERVER['DOCUMENT_ROOT'];
        $path_file  = $doc_root . "/$type/" . ( $path ? $path . '/' : '' ) . "$name.$type";

        if( file_exists( $path_file ) )
        {
            $timestamp = filemtime( $path_file );
            //$url = "/$type/" . ( $path ? $path . '/' : '' ) . "$name.v$timestamp.$type";
			$url = "/$type/" . ( $path ? $path . '/' : '' ) . "$name.$type";

            switch( $type )
            {
                case 'css':
                    $uot = '<link rel="stylesheet" type="text/css" href="'. $url. '">';
                    break;
                case 'js':
                    $uot = '<script type="text/javascript" language="Javascript" src="'. $url . '"></script>';
                    break;
            }
        }
        else
        {
           //TODO SEND notification to admin
        }
        echo $uot . "\n";

    }
}

if ( !function_exists( 'parse_debug_trace' ) )
{
	/**
	 * @param  array $trace_arr
	 * @param bool $show_args
	 * @return string
	 */
	function parse_debug_trace ( $trace_arr, $show_args = false )
	{
		$str = '';
		if ( is_array ( $trace_arr ) )
			foreach ( $trace_arr AS $k => $v )
			{
				if ( isset ( $v['line'] ) )
				{
					$file = str_replace ( $_SERVER['DOCUMENT_ROOT'], "", str_replace ( "\\", "/", $v['file'] ) );
					$str .= "[$file [{$v['line']}] {$v['function']} of " . ( isset ( $v['class'] ) ? $v['class'] : 'Null' ) .
							( $show_args && isset ( $v['args'] ) ? ", Args: \n" . print_r( $v['args'], true ) . "\n ----------- " : "" ) . "] \n";
				}

				if ( $k == 4 )
					break;
			}

		return $str;
	}
}

set_error_handler("rems_error_handler");
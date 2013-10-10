<?php

namespace SDClasses;
/**
 * Class AppConf
 */
class AppConf
{
	/** @var AppConf */
	private static $instance;

	/** @var bool */
	public $dev_server;

	/** @var int Diff hours between default_timezone_set and Greenwich time*/
	public $STD;

	/** @var int*/
	public $logout_time;

	/** @var bool Показывает, какое сейчас время на сервере - летнее(TRUE) или зимнее(FALSE)*/
	public $IS_DST;

	/** @var string*/
	public $Charset;

	/** @var string*/
	public $DBCharset;

	/** @var array*/
	public $admin_emails;

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

	public $modal_window = false;

	public $root_path = '';

	/**
	 * @var Router
	 */
	public $route;
	/**
	 * @var int
	 */
	public $module_time_start;
	/**
	 * @var Controller
	 */
	public $_controller;
	/**
	 * @var View
	 */
	public $_view;
	/**
	 * @var string
	 */
	public $secret_salt;

	/**
	 * @var array
	 */
	public $menu;
	/**
	 * @var array
	 */
	public $modules;

	/**
	 * @var array
	 */
	public $breadcrumb;
	/**
	 * @var
	 */
	public $auth_ok;

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

	/**
	 * @return AppConf
	 */
	public static function getIns()
	{
		if ( self::$instance === null )
		{
			self::$instance = new AppConf();
		}

		return self::$instance;
	}

	/**
	 * Determines if server's time is DST time or not;
	 * @return bool
	 */
	public static function is_dst()
	{
		$lt = localtime ( time(), 1 );
		$dst = ( $lt["tm_isdst"] == 1 ? true : false ); // zero = standard time
		return( $dst );
	}

	public static function query_error( $file_name, $line, $query )
	{
		$local_server = AppConf::getIns()->dev_server;
		$admin_email = AppConf::getIns()->admin_emails;
		$file_name = str_replace ( AppConf::getIns()->root_path, "", str_replace ( "\\", "/", $file_name ) );


		$error_text =  '<pre><span style="color:red;"><b>SQL_Error</b>:</span><br>file: <b>' .
		$file_name .
		'</b><br> line: <b>'.

		$line .
		'</b><br><b>'.

		$query .
		'</b><br>'.
		mysql_errno().
		'<br>'.
		mysql_error().
		'<br></pre>' ;

		$info_text = '<span style="color:red;"><hr style="color:red">Sorry, the script was stoped for the MySQL error!!<br>Please, be patient - Mail was sent to Administrators of REMS and the Error will be fixed ASAP<br>You will be informed about this<b></b><hr style="color:red"></span>';

		if( $local_server )
		{
			echo $error_text;
	?><pre>$_REQUEST:
	<? print_r( $_REQUEST  ) ?></pre><br><?
		}
		else
		{

			$mess_to_admin = '

Hello, master.

Sorry for disturbance, but MySQL does not want to work.

MySQL_Error----------------------------------------------

$$$DATABASE$$$

file: '. $file_name . '
line: '. $line .'
'. $query . '

'. mysql_errno(). '
'. mysql_error().'

User: '. AppConf::getIns()->user.'

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
            $db_name = @mysql_result( $r, 0 );

            $mess_to_admin = str_replace( '$$$DATABASE$$$', $db_name, $mess_to_admin );

			echo $info_text;
			@mail( implode( ',', $admin_email ), 'MySQL Error', $mess_to_admin );
		}
	}

	/** Error handler
	 * @param $errno
	 * @param $errstr
	 * @param $errfile
	 * @param $errline
	 * @param $vars
	 */
	public static function our_error_handler( $errno, $errstr, $errfile, $errline, $vars )
	{
		if( error_reporting() )
		{
			$local_server       = AppConf::getIns()->dev_server;
			$admin_email        = AppConf::getIns()->admin_emails;
			$admin_flag         = AppConf::getIns()->admin_flag;
			$ajax_flag          = AppConf::getIns()->ajax_flag;


			if ( $local_server )
				$trace_str = '';
			else
			{
				$trace_arr = debug_backtrace(  );
	            $trace_str = Func::parseDebugTrace( $trace_arr, false );
			}

			$error_text =  '<br><span style="color:red;"><b>PHP Error</b>:</span><br>
Description: '.$errstr.'
<br>
file: <b>' . $errfile .	'</b> line: <b>'. $errline .'</b><br>
trace stack: <b>' . nl2br ( $trace_str ) .	'</b><br>
';

			if( !$ajax_flag && ( $admin_flag || $local_server ) )
			{
				echo $error_text;
				\Kint::trace();
			}

			else if ( $ajax_flag )
			{

			$mess_to_admin = '

Greetings, commander.


PHP_Error----------------------------------------------

file: '. $errfile . '
line: '. $errline .'
error number: '. $errno .'
error description: '. $errstr .'

User: '. AppConf::getIns()->user.'

Call Stack:
----------
' .  str_replace ( "\n", "\r\n
", $trace_str ) . '
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

Browser-----------
'.

			$_SERVER["HTTP_USER_AGENT"]

			.'

Best regards!

';
				mail( implode( ',', $admin_email ), 'PHP Error', $mess_to_admin );
			}
		}
	}
}
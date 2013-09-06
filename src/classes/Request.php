<?php
/**
 * файл с классами для работы с Request
 */
/********************************************************************
*    Класс для работы с Request
********************************************************************/
class Request
{
    private function __construct()
    {
    }

	/**
	 * @static
	 * @param string $name
	 * @param bool|string|int|array $default
	 * @param string $where
	 * @return bool|string|int|array
	 */
    public static function getVar( $name, $default = false, $where = "request" )
    {
        switch ( $where )
        {
            case 'get':
                if( array_key_exists( $name, $_GET ) )
                   return $_GET[$name];
                break;
            case 'post':
                if( array_key_exists( $name, $_POST ) )
                   return $_POST[$name];
                break;
            case 'cookie':
                if( array_key_exists( $name, $_COOKIE ) )
                   return $_COOKIE[$name];
                break;
            case 'session':
                if( isset( $_SESSION ) && array_key_exists( $name, $_SESSION ) )
                   return $_SESSION[$name];
                break;
            case 'request':
                if( array_key_exists( $name, $_REQUEST ) )
                   return $_REQUEST[$name];
                break;
            case 'global':
                global $$name;
                if( isset( $$name ) )
                    return $$name;
                break;
            case 'mixed':
                if( array_key_exists( $name, $_GET ) )
                   return $_GET[$name];
                if( array_key_exists( $name, $_POST ) )
                   return $_POST[$name];
                if( array_key_exists( $name, $_COOKIE ) )
                   return $_COOKIE[$name];
                if( isset( $_SESSION ) && array_key_exists( $name, $_SESSION ) )
                   return $_SESSION[$name];
                global $$name;
                if( isset( $$name ) )
                    return $$name;
                break;
            default:
                break;
        }
        return $default;
    }

    /*public static function addVar( $name, $value, $where = "mixed" )
    {
        global $_REQUEST, $GLOBALS, $_GET, $_POST, $_COOKIE, $_SESSION;

        foreach ( (array) $where as $key => $_value )
            switch ( $_value )
            {
                case 'get':
                    $HTTP_GET_VARS[$name][] = $value;
                    break;
                case 'post':
                    $HTTP_POST_VARS[$name][] = $value;
                    break;
                case 'cookie':
                    $HTTP_COOKIE_VARS[$name][] = $value;
                    break;
                case 'session':
                    $_SESSION[$name][] = $value;
                    break;
                case 'request':
                    $_REQUEST[$name][] = $value;
                    break;
                case 'global':
                    global $$name;
                    $$name = $value;
                    $GLOBALS[$name][] = $value;
                    break;
                default:
                    break;
            }
    }

    public static function setVar( $name, $value, $where = "mixed" )
    {
        global $_REQUEST, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $_SESSION;

        foreach ( (array) $where as $key => $_value )
        {
            switch ( $_value )
            {
                case 'get':
                    $HTTP_GET_VARS[$name] = $value;
                    break;
                case 'post':
                    $HTTP_POST_VARS[$name] = $value;
                    break;
                case 'cookie':
                    $HTTP_COOKIE_VARS[$name] = $value;
                    break;
                case 'session':
                    $_SESSION[$name] = $value;
                    break;
                case 'request':
                    $_REQUEST[$name] = $value;
                    break;
                case 'global':
                    global $$name;
                    $$name = $value;
                    break;
                default:
                    break;
            }
        }
    }

    public static function unsetVar( $name, $where = "mixed" )
    {
        global $_REQUEST, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $_SESSION;

        foreach ( ( array ) $where as $key => $_value )
        {
            switch ( $_value )
            {
                case 'get':
                    unset( $HTTP_GET_VARS[$name] );
                    break;
                case 'post':
                    unset( $HTTP_POST_VARS[$name] );
                    break;
                case 'cookie':
                    unset( $HTTP_COOKIE_VARS[$name] );
                    break;
                case 'session':
                    unset( $_SESSION[$name] );
                    break;
                case 'request':
                    unset( $_REQUEST[$name] );
                    break;
                case 'global':
                    global $$name;
                    unset( $$name );
                    break;
                default:
                    break;
            }
        }
    }*/
}

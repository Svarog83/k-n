<?php

namespace SDClasses;
/**
 * Class Request
 */
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
}

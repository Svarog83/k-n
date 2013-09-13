<?php
namespace SDClasses;
use AutoLoader;

class Controller
{
	protected $_module = '';
	protected $_action = '';

	public function __construct( $module, $action )
	{

		$class = get_class( $this );

		if ( empty( $action ) )
			$action = 'default';

		if ( $class == 'SDClasses\Controller' )
		{
			$this->_module = $module;
			$this->_action = $action;

			$AC = AppConf::getIns();
			if ( is_dir ( $AC->root_path . '/src/' . $module . 'Bundle' ) )
			{
				AutoLoader::autoLoader( $module . 'Bundle' );
				$classname = '\\SDClasses\\' . $module. 'Bundle' . '\\Controller\\' . $module . 'Controller';
				$obj = new $classname ( $module, $action );

				if ( !method_exists( $obj, $action . 'Action' ) )
					$this->Error404( 'Method ' . $action . 'Action' . ' is not found in class' . $classname );
				else
					call_user_func( array( $obj, $action . 'Action' ) );

			}
			else
				$this->Error404( 'Module `' . $module . '` not found' );
		}
	}

	/**
	 * Triggers specified actions of the specified module
	 * @param $module
	 * @param $action
	 * @param array $params
	 */

	public function forward( $module, $action, $params = array() )
	{
	}

	/**
	 * redirects user's browser to specified URL
	 * @param $url
	 * @param int $status
	 */
	public function redirect( $url, $status = 302 )
	{
		header( 'Location:' . $url, TRUE, $status );
	}

	/**
	 * Renders specified view and returns the resulting string
	 * @param $view
	 * @param array $params
	 * @return string
	 */
	public function renderView( $view, $params = array() )
	{
		$str = '';

		return $str;
	}


	/**
	 * Renders the specified view and outputs it into browser
	 * @param $view_options
	 * @param array $params
	 */
	public function render( $view_options, $params = array() )
	{
		$view = new View( '', $view_options );
		$view->render( $params );
	}

	public function Error404( $text )
	{
		$this->render( array ( 'view' => 'content' ), array ( 'text' => $text . '<br><a href="#" onclick="history.back();">Вернуться назад</a>', 'class' => 'alert-error' ) );
	}
}
<?php

namespace SDClasses;

class View {

	/**
	 * @var string
	 */
	protected $_path = '';

	/**
	 * @param $path
	 * @param array $params
	 */
	public function __construct( $path, $params = array() )
	{
		if ( !is_string( $path ) )
		{
			trigger_error( 'Wrong path ' . $path . ' in View constructor' );
		}

		if ( is_array ( $params ) && count ( $params ) )
		{
			if ( !isset ( $params['module'] ) || $params['module'] == 'Resources' )
				$this->_path = '../app/Resources/views/' . $params['view'] . '.php';
			else if ( !empty ( $params['module'] ) && !empty ( $params['view'] ) )
			{
				$this->_path = AppConf::getIns()->root_path . '/src/' . $params['module'] . 'Bundle' . '/views/' . $params['view'] . '.php';
			}
		}
		else
		{
			$this->_path = $path;
		}
		$AC = AppConf::getIns();
		$AC->_view = $this;
	}

	/**
	 * @param array $params
	 */
	public function render ( $params = array () )
	{
		if ( file_exists( $this->_path ) )
			require ( $this->_path );
		else
			trigger_error( 'View path ' . $this->_path . ' does not exist!' );
	}

	/**
	 * @param array $params
	 * @return string
	 */
	public function renderView ( $params = array () )
	{
		if ( file_exists( $this->_path ) )
		{
			ob_start();

			require ( $this->_path );

			$str = ob_get_clean();
			ob_end_clean();

			return $str;
		}
		else
			trigger_error( 'View path ' . $this->_path . ' does not exist!' );
	}
}
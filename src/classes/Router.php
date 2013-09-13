<?php

namespace SDClasses;

class Router
{
	protected $_module = '';
	protected $_action = '';
	protected $_params   = array();
	public function  __construct( $full_path )
	{
		$arr = explode ( '/', $full_path );

		if ( isset ( $arr[1]) && $arr[1] )
			$this->_module = $arr[1];

		if ( isset ( $arr[2]) && $arr[2] )
			$this->_action = $arr[2];

		if ( strpos ( $this->_action, '?') !== false )
			$this->_action = explode ( '?', $this->_action )[0];

		if ( count ( $arr ) > 3  )
			$this->_params = array_slice( $arr, 3 );
	}

	/**
	 * @return string
	 */
	public function getModule()
	{
		return $this->_module;
	}

	/**
	 * @param string $module
	 */
	public function setModule( $module )
	{
		$this->_module = $module;
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**
	 * @param string $action
	 */
	public function setAction( $action )
	{
		$this->_action = $action;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->_params;
	}

	/**
	 * @param array $params
	 */
	public function setParams( $params )
	{
		$this->_params = $params;
	}

}
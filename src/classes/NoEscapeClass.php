<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Svarog
 * Date: 27.09.13
 * Time: 19:27
 * To change this template use File | Settings | File Templates.
 */

namespace SDClasses;

/**
 * Class NoEscapeClass
 */
class NoEscapeClass
{
	/**
	 * @var string
	 */
	protected $_value;

	/**
	 * Constructor
	 *
	 * @param string $value     - значение
	 * @return NoEscapeClass
	 */
	public function __construct( $value )
	{
		$this->_value = $value;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->_value;
	}

	/**
	 * Get _value
	 * @return mixed
	 */
	public function get_value()
	{
		return $this->_value;
	}


}
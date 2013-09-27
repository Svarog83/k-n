<?php
/**
 * Model class for inheritance
 */

class ModelTable
{
	/** @var array $row */
	protected $row;

	/** @var bool $_debug */
	protected $_debug = false;

	/** @var bool $_log */
	protected $_log = false;

	/** @var bool $_exist */
	protected $_exist = true;

	/** @var string $_table_name */
	private $_table_name;

	/** @var MySQL */
	protected $DB;

	public function __construct( $table_name )
	{
		$this->DB = AutoLoader::DB();
		$this->_table_name = $table_name;
	}

	public function setDebug( $debug )
	{
		$this->_debug = $debug;
	}

	public function getDebug( )
	{
		return $this->_debug;
	}

	/**
	 * @param boolean $log
	 */
	public function setLog( $log )
	{
		$this->_log = $log;
	}

	/**
	 * @return boolean
	 */
	public function getLog( )
	{
		return $this->_log;
	}

	/**
	 * @param array $row
	 */
	public function setRow( $row )
	{
		if ( !is_array ( $row ) || !count ( $row ) )
			$this->_exist = false;

		$this->row = $row;
	}

	/**
	 * @return array
	 */
	public function getRow( )
	{
		return $this->row;
	}

	/**
	 * @param boolean $exist
	 */
	public function setExist( $exist )
	{
		$this->_exist = $exist;
	}

	/**
	 * @return boolean
	 */
	public function getExist( )
	{
		return $this->_exist;
	}

	public function getEmpty()
	{
		return $this->DB->get_columns_info( $this->_table_name, false );
	}

	/**
	 * Saves a record into database
	 * @param bool $add_creator - flasg, shows if creator and create_date fields needs to be added
	 * @param bool $edit_flag - Flag, shows that record will be edited
	 * @param array $WhereArr - An array with conditions for editing a record
	 * @return int - return ID of inserted record
	 */
	public function save( $add_creator = true, $edit_flag = false, $WhereArr = array() )
	{
		$this->DB->setLog( $this->_debug, $this->_log );

		if ( !$edit_flag )
			$this->DB->insert_arr( $this->_table_name, $this->row, $add_creator );
		else
			$this->DB->update_arr( $this->_table_name, $this->row, $add_creator, $WhereArr );

		$this->DB->setLog();

		return $this->DB->get_last_insert_id();
	}
}

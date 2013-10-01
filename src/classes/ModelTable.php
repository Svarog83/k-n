<?php
namespace SDClasses;
/**
 * Model class for inheritance
 */

class ModelTable
{
	/**
	 * @var array
	 */
	protected $_TableColumns = array();

	/** @var array $row */
	protected $row;

	/** @var bool $_log */
	protected $_log = false;

	/** @var bool $_exist */
	protected $_exist = true;

	/** @var string $_table_name */
	private $_table_name;

	/** @var SafeMySQL */
	protected $DB;

	public function __construct( $table_name )
	{
		$this->DB = \AutoLoader::DB();
		$this->_table_name = $table_name;
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
		$table_name = $this->_table_name;
		$DB = \AutoLoader::DB();
		if ( !array_key_exists( $table_name, $this->_TableColumns ) )
		{
			$this->_TableColumns[$table_name] = array();

			$query = "SHOW COLUMNS FROM ?n";
			$result = $DB->query( $query, $table_name );

			while ( $row = $DB->fetch( $result ) )
				$this->_TableColumns[$table_name][$row['Field']] = '';
		}

		return $this->_TableColumns[$table_name];
	}

	/**
	 * Saves a record into database
	 * @param bool $add_creator - flasg, shows if creator and create_date fields needs to be added
	 * @param bool $edit_flag - Flag, shows that record will be edited
	 * @param array $where - An array of prepared (via $db->parse) conditions.
	 * @param bool $hist_record - flag, shows if we need to insert a new record and set as 'ch' the old one
	 * @return int - return ID of inserted record
	 */
	public function save( $add_creator = true, $edit_flag = false, $where = array(), $hist_record = true )
	{
		$this->DB->setLog( $this->_log );

//		$this->DB->setLog( 'display' );

		$arr_creator = $arr_changer = array();

		if ( $add_creator )
		{
			$arr_creator[$this->_table_name . '_creator'] = AppConf::getIns()->user;
			$arr_creator[$this->_table_name . '_create_date'] = new NoEscapeClass( 'NOW()' );
		}

		if ( !$edit_flag )
		{
			$this->DB->query( "INSERT INTO ?n SET ?u", $this->_table_name, array_merge ( $this->row, $arr_creator ) );
		}
		else
		{
			if ( $add_creator )
			{
				$arr_changer[$this->_table_name . '_changer'] = AppConf::getIns()->user;
				$arr_changer[$this->_table_name . '_change_date'] = new NoEscapeClass( 'NOW()' );
			}

			if ( !count ( $where ) )
			{
				$where[] = $this->DB->parse($this->_table_name . "_id = ?s AND " . $this->_table_name . "_activ='a'", $this->row[$this->_table_name . '_id'] );
			}

			$query_arr = array();
			if ( !$hist_record )
			{
				$this->DB->query( "UPDATE ?n SET ?u WHERE " . implode(' AND ', $where), $this->_table_name, array_merge ( $this->row, $arr_changer )  );
			}
			else
			{
				$arr_changer[$this->_table_name . '_activ'] = 'ch';
				$query_arr[] = $this->DB->parse( "UPDATE ?n SET ?u WHERE " . implode(' AND ', $where), $this->_table_name, $arr_changer );

				$query_arr[] = $this->DB->parse( "INSERT INTO ?n SET ?u", $this->_table_name, array_merge ( $this->row, $arr_creator ) );

				$this->DB->queryTransaction( $query_arr );

			}

			/*$this->DB->query( "UPDATE ?n SET ?u WHERE " . implode(' AND ', $where), $this->_table_name, $this->row );*/
		}

//		$this->DB->update_arr( $this->_table_name, $this->row, $add_creator, $where );

		$this->DB->setLog();

		return $this->DB->insertId();
	}

	/**
	 * @param array $TableColumns
	 */
	public function setTableColumns( $TableColumns )
	{
		$this->_TableColumns = $TableColumns;
	}

	/**
	 * @return array
	 */
	public function getTableColumns()
	{
		return $this->_TableColumns;
	}

}

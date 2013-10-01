<?php

namespace SDClasses;
use SDClasses\ModelTable;

/**
 * Class for `user` table
 */
class User extends ModelTable
{
	/**
	 * @var string $table_name
	 */
	private $table_name = 'user';

	/**
	 * @param  int|array $user - id of user OR array with data
	 * @param string $user_uid - hash
	 * @param string $user_activ - flag of activity for DB query
	 * @param string $user_field
	 */
	public function __construct( $user, $user_uid = '', $user_activ = 'a', $user_field = 'user_id' )
	{
		parent::__construct( $this->table_name );
		
		if ( is_array ( $user ) && count ( $user ) )
			$this->setRow( $user );
		else if ( is_numeric( $user ) )
			$this->getUser( $user, $user_uid, $user_activ, $user_field );
		else
		{
			$this->setRow( $this->getEmpty() );
			$this->_exist = false;
		}
	}


	/** Return name of a user
	 * @param $user_id
	 * @param string $field_name
	 * @return string
	 */
	public static function getUserName( $user_id, $field_name = 'user_id' )
	{
		global $CacheUser;
		$DB = \AutoLoader::DB();

		if ( !isset ( $CacheUser[$user_id] ) )
		{
			$query  = "SELECT * FROM user WHERE ?n = ?i && user_activ NOT IN ('ch', 'del')";
			$row = $DB->getRow( $query, $field_name, $user_id );

			$CacheUser[$user_id] = $row;
		}
		else
		{
			$row = $CacheUser[$user_id];
		}

		return self::showUserName( $row );
	}

	/**
	 * @param $arr - User array
	 * @return string - returns name and surname of the user
	 */
	public static function showUserName( $arr )
	{
		return $arr['user_fam_rus'] . ' ' . $arr['user_name_rus'];
	}

	/**
	 * Tries to find a user record in DB
	 * @param $user_id - ID of user
	 * @param $user_uid = uid of user
	 * @param string $user_activ - activity flasg
	 * @param string $user_field - another field to search user
	 */
	public function getUser ( $user_id, $user_uid, $user_activ = 'a', $user_field = 'user_id' )
	{

		$DB = \AutoLoader::DB();
		$user_field = ( $user_field == 'user_ida' ? $user_field : 'user_id' );

		$hash_string = '';
		if ( $user_uid )
			$hash_string =  $DB->parse("AND user_hash = ?s", $user_uid);

		$query = "SELECT * FROM user WHERE ?n = ?i ?p AND user_activ = ?s";
//		$DB->setLog( ('display' ) );
		$row = $DB->getRow( $query, $user_field, $user_id, $hash_string, $user_activ );

		$this->setRow( $row );
	}
}


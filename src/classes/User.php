<?php
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
	 * @param string $user_hash - hash
	 * @param string $user_activ - flag of activity for DB query
	 * @param string $user_field
	 */
	public function __construct( $user, $user_hash = '', $user_activ = 'a', $user_field = 'user_id' )
	{
		parent::__construct( $this->table_name );
		
		if ( is_array ( $user ) && count ( $user ) )
			$this->setRow( $user );
		else if ( is_numeric( $user ) )
			$this->getUser( $user, $user_hash, $user_activ, $user_field );
		else
		{
			$this->setRow( $this->getEmpty() );
			$this->_exist = false;
		}
	}


	public static function getUserName( $user_id, $field_name = 'user_id' )
	{
		global $CacheUser;
		$DB = Loader::DB();

		if ( !isset ( $CacheUser[$user_id] ) )
		{
			$query  = "SELECT * FROM user WHERE " . $field_name ." = '" . (int)$user_id ."' && user_activ NOT IN ('ch', 'del')";
			$DB->query( $query, __FILE__, __LINE__ );
			$row = $DB->get_fetch_ass();

			$CacheUser[$user_id] = $row;
		}
		else
		{
			$row = $CacheUser[$user_id];
		}

		return self::showUserName( $row );
	}

	public static function showUserName( $arr )
	{
		return $arr['user_fam_rus'] . ' ' . $arr['user_name_rus'];
	}

	public function getUser ( $user_id, $user_hash, $user_activ = 'a', $user_field = 'user_id' )
	{
		$user_id = (int)$user_id;
		$user_field = ( $user_field == 'user_ida' ? $user_field : 'user_id' );
		$user_hash = mysql_real_escape_string( $user_hash );

		$hash_string = '';
		if ( $user_hash )
			$hash_string = "AND user_hash = '$user_hash'";
			
		$query = "SELECT * FROM user WHERE $user_field = '$user_id' $hash_string AND user_activ = '$user_activ'";
		$this->DB->query( $query, __FILE__, __LINE__ );
		$row = $this->DB->get_fetch_ass();
		$this->setRow( $row );
	}
}


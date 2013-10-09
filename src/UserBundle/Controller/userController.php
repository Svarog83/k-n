<?php

namespace SDClasses\userBundle\Controller;
use SDClasses;
use SDClasses\AppConf;
use SDClasses\NoEscapeClass;
use SDClasses\User;

class userController extends SDClasses\Controller
{
	public function authAction()
	{
		$login = isset ( $_REQUEST['form_login'] ) ? mysql_real_escape_string( $_REQUEST['form_login'] ) : '';
		$password = isset ( $_REQUEST['form_password'] ) ? sha1( $_REQUEST['form_password'] ) : '';

		$DB = \AutoLoader::DB();
		$row = $DB->getRow( "SELECT * FROM user WHERE user_login = ?s AND user_pass = ?s AND user_activ='a'", $login, $password );

		if ( $DB->affectedRows() == 1 && is_array( $row ) && $row['user_id'] )
		{
			$_SESSION['user_id'] = $row['user_id'];
			$_SESSION['user_uid'] = $uid = sha1( $row['user_id'] . microtime() . AppConf::getIns()->secret_salt );

			$DB->query( "UPDATE user SET user_uid = '$uid' WHERE user_id = ?s AND user_activ = 'a'", $row['user_id'] );

			$this->redirect( '/first' );
		}
		else
		{
			AppConf::getIns()->auth_ok = false;

			$this->render( array( 'view' => 'login_page' ), array( 'flash_message' => 'Вы ввели неправильные данные' ) );
		}
	}

	public function profileAction()
	{
		$this->showAction();
	}

	public function showAction()
	{
		$user_id = isset ( AppConf::getIns()->route->getParams()[0] ) ? AppConf::getIns()->route->getParams()[0] : AppConf::getIns()->user;

		$user = new User( $user_id );

		if ( $user->getExist() )
			$this->render( array( 'module' => 'user', 'view' => 'profile' ), array( 'user' => $user ) );
		else
			$this->Error404( 'Пользователь не найден' );
	}

	public function settingsAction()
	{
		//		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );

		$user = new User( AppConf::getIns()->user );

		if ( $user->getExist() )
			$this->render( array( 'module' => 'user', 'view' => 'settings' ), array( 'user' => $user ) );
		else
			$this->Error404( 'Пользователь не найден' );
	}

	public function edit_profileAction()
	{
		//		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );

		$user = new User( AppConf::getIns()->user );

		if ( $user->getExist() )
			$this->render( array( 'module' => 'user', 'view' => 'edit_profile' ), array( 'user' => $user ) );
		else
			$this->Error404( 'Пользователь не найден' );
	}

	public function save_profileAction()
	{
		//		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );

		$user = new User( AppConf::getIns()->user );

		if ( $user->getExist() )
		{
			$row = $user->getRow();

			unset ( $row['user_ida'], $row['user_changer'], $row['user_change_date'] );

			/*Enable only when we want to add a new record*/
			//$row['user_id'] = new NoEscapeClass( "( SELECT if ( max(user_id)  IS NULL, 1, max(user_id) + 1 ) AS c FROM ( SELECT user_id FROM user ) AS t1 )" );

			$row['user_name_rus'] = $_REQUEST['form_name'];
			$row['user_fam_rus'] = $_REQUEST['form_surname'];
			$row['user_login'] = $_REQUEST['form_login'];
			$row['user_email'] = $_REQUEST['form_email'];
			$user->setRow( $row );
			$user->save( true, true, array(), false );

			$this->render( array( 'module' => 'user', 'view' => 'profile' ), array( 'flash_message' => 'Ваши данные сохранены успешно', 'user' => $user ) );

		}
		else
			$this->Error404( 'Пользователь не найден' );
	}

	public function exitAction()
	{
		$_SESSION['user_id'] = '';
		$_SESSION['user_uid'] = '';

		$this->redirect( '/' );
	}

	public function defaultAction()
	{
		$this->profileAction();
	}

	public function newAction()
	{
		$user = new User( '' );
		$this->render( array( 'module' => 'user', 'view' => 'new' ), array( 'user' => $user ) );
	}

	public function editAction()
	{
		$user_id = isset ( AppConf::getIns()->route->getParams()[0] ) ? AppConf::getIns()->route->getParams()[0] : '';

		$user = new User( $user_id );

		if ( $user->getExist() )
		{
			$this->render( array( 'module' => 'user', 'view' => 'new' ), array( 'user' => $user ) );
		}
		else
			$this->Error404( 'Пользователь не найден' );
	}

	public function saveAction()
	{
		$user_id = isset ( $_REQUEST['form_user_id'] ) ? $_REQUEST['form_user_id'] : '';
		$user = new User( $user_id );
		$edit_flag = $user->getExist() ? true : false;

		$row = $user->getRow();

		if ( $edit_flag )
			unset ( $row['user_ida'], $row['user_changer'], $row['user_change_date'] );
		else
		{
			$row['user_activ'] = 'a';
			$row['user_id'] = new NoEscapeClass( "( SELECT if ( max(user_id)  IS NULL, 1, max(user_id) + 1 ) AS c FROM ( SELECT user_id FROM user ) AS t1 )" );
		}

		$row['user_name_rus'] = $_REQUEST['form_name'];
		$row['user_fam_rus'] = $_REQUEST['form_surname'];
		$row['user_login'] = $_REQUEST['form_login'];
		$row['user_email'] = $_REQUEST['form_email'];
		$row['user_pass'] = sha1 ( $_REQUEST['form_pass'] );
		$row['user_sex'] = $_REQUEST['form_sex'];
		$row['user_blocked'] = $_REQUEST['form_blocked'];

		$user->setRow( $row );

		$insert_id = $user->save( true, $edit_flag, array(), false );

		if ( !$edit_flag && $insert_id )
		{
			$user = new User ( $insert_id, '', 'a', 'user_ida' );
		}

		$this->render( array( 'module' => 'user', 'view' => 'profile' ), array( 'flash_message' => 'Ваши данные сохранены успешно', 'user' => $user ) );
	}

	public function listAction( $flash_mesage = '' )
	{
		$DB = \AutoLoader::DB();
		$UsersArr = $DB->getAll( "SELECT * FROM user WHERE user_activ!='ch' ORDER BY user_fam_rus" );
		$this->render( array( 'module' => 'user', 'view' => 'list' ), array( "users" => $UsersArr, 'flash_message' => $flash_mesage) );
	}

	public function deleteAction()
	{
		$user_id = isset ( AppConf::getIns()->route->getParams()[0] ) ? AppConf::getIns()->route->getParams()[0] : 0;
		$DB = \AutoLoader::DB();
		$DB->query( "UPDATE user SET user_activ= 'd' WHERE user_id = ?i AND user_activ = 'a'", $user_id );

		$this->listAction( 'Пользователь удален' );

	}

	public function activateAction()
	{
		$user_id = isset ( AppConf::getIns()->route->getParams()[0] ) ? AppConf::getIns()->route->getParams()[0] : 0;
		$DB = \AutoLoader::DB();
		$DB->query( "UPDATE user SET user_activ= 'a' WHERE user_id = ?i AND user_activ='d'", $user_id );

		$this->listAction( 'Пользователь восстановлен' );

	}

}

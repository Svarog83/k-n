<?php

namespace SDClasses\userBundle\Controller;
use SDClasses;
use SDClasses\AppConf;

class userController extends SDClasses\Controller
{
	public function authAction()
	{
		$login = isset ( $_REQUEST['form_login'] ) ? mysql_real_escape_string( $_REQUEST['form_login'] ) : '';
		$password = isset ( $_REQUEST['form_password'] ) ? sha1( $_REQUEST['form_password'] ) : '';

		$DB = \AutoLoader::DB();
		$row = $DB->getRow( "SELECT * FROM user WHERE user_login = ?s AND user_pass = ?s", $login, $password );

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
//		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );

		$user = new \User( AppConf::getIns()->user );

		if ( $user->getExist() )
			$this->render( array( 'module' => 'user', 'view' => 'profile' ), array( 'user' => $user ) );
		else
			$this->Error404( 'Пользователь не найден' );
	}

	public function settingsAction()
	{
		//		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );

		$user = new \User( AppConf::getIns()->user );

		if ( $user->getExist() )
			$this->render( array( 'module' => 'user', 'view' => 'settings' ), array( 'user' => $user ) );
		else
			$this->Error404( 'Пользователь не найден' );
	}

	public function edit_profileAction()
	{
		//		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );

		$user = new \User( AppConf::getIns()->user );

		if ( $user->getExist() )
			$this->render( array( 'module' => 'user', 'view' => 'edit_profile' ), array( 'user' => $user ) );
		else
			$this->Error404( 'Пользователь не найден' );
	}

	public function save_profileAction()
	{
		//		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );

		?><pre><?= print_r( $_REQUEST ) ?></pre><?
	}

	public function exitAction()
	{
		$_SESSION['user_id'] = '';
		$_SESSION['user_uid'] = '';

		$this->redirect( '/' );
	}
}

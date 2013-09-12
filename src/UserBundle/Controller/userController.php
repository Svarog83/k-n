<?php

namespace SDClasses\userBundle\Controller;
use SDClasses;
use SDClasses\AppConf;

class userController extends SDClasses\Controller
{
	public function authAction()
	{
		$login = isset ( $_REQUEST['form_login'] ) ? mysql_real_escape_string( $_REQUEST['form_login'] ) : '';
		$password = isset ( $_REQUEST['form_password'] ) ? sha1 ( $_REQUEST['form_password'] ) : '';

		$DB = \AutoLoader::DB();
		$DB->setDebug( false, true );
		$query = "SELECT * FROM user WHERE user_login = '$login' AND user_pass = '$password'";
		$DB->query( $query, __FILE__, __LINE__ );

		$DB->setDebug( );
		$row = $DB->get_fetch_ass();

		if ( $DB->getAffRows() == 1 && is_array( $row ) && $row['user_id'] )
		{
			$_SESSION['user_id']  = $row['user_id'];
			$_SESSION['user_uid'] = $uid = sha1( $row['user_id'] . microtime() . AppConf::getIns()->secret_salt );
			$query = "UPDATE user SET user_uid = '$uid' WHERE user_id = '{$row['user_id']}' AND user_activ = 'a'";
			$DB->query( $query, __FILE__, __LINE__ );

			$this->redirect( '/first' );
		}
		else
		{
			AppConf::getIns()->uid = 'wrong';
			AppConf::getIns()->user = 'bad';

			$this->render( array ( 'view' => 'login_page' ), array ('flash_message' => 'Вы ввели неправильные данные') );
		}
	}

	public function exitAction ()
	{
		$_SESSION['user_id']  = '';
		$_SESSION['user_uid'] = '';

		$this->redirect( '/' );
	}
}

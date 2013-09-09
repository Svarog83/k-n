<?php

namespace SDClasses\userBundle\Controller;
use SDClasses;
use SDClasses\AppConf;

class userController extends SDClasses\Controller
{
	public function authAction()
	{
		$login = isset ( $_REQUEST['form_login'] ) ? $_REQUEST['form_login'] : '';
		$password = isset ( $_REQUEST['form_password'] ) ? $_REQUEST['form_password'] : '';

		$DB = \AutoLoader::DB();
		$DB->setDebug( false, true );
		$query = "SELECT * FROM user WHERE user_login = '$login' AND user_pass = SHA1('$password')";
		$DB->query( $query, __FILE__, __LINE__ );

		$DB->setDebug( );
		$row = $DB->get_fetch_ass();

		if ( $DB->getAffRows() == 1 && is_array( $row ) && $row['user_id'] )
		{


			$this->redirect( '/first' );
		}
		else
		{
			AppConf::getIns()->uid = 'wrong';
			AppConf::getIns()->user = 'bad';

			$this->render( 'login_page', array ('flash_message' => 'Вы ввели неправильные данные') );
		}
	}
}

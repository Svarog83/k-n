<?php

namespace UserBundle\Controller
{

	class userController
	{
		public function authAction()
		{
			?><pre><?= print_r( $_REQUEST ) ?></pre><?

			$login = isset ( $_REQUEST['form_login'] ) ? $_REQUEST['form_login'] : '';
			$password = isset ( $_REQUEST['form_password'] ) ? $_REQUEST['form_password'] : '';

			$DB = \AutoLoader::DB();
			$DB->setDebug( false, true );
			$query = "SELECT * FROM user WHERE user_login = '$login' AND user_pass = SHA1('$password')";
			$DB->query( $query, __FILE__, __LINE__ );
			$DB->setDebug( );
			$row = $DB->get_fetch_ass();
			?><pre><?= print_r( $DB ) ?></pre><?
			?><pre>ser<?= print_r( $row ) ?></pre><?


		}
	}
}
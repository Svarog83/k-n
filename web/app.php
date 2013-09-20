<?php

use SDClasses\AppConf;
use SDClasses\Controller;
use SDClasses\Func;
use SDClasses\Router;
use SDClasses\Request;
use SDClasses\View;

error_reporting ( E_ALL );
session_start();

require( '../src/classes/AutoLoader.php' );

$AC = AppConf::getIns();
$AC->root_path = $_SERVER['DOCUMENT_ROOT'] . '/..';

require ( $AC->root_path . '/app/config/config_main.php' );
if ( ( isset ( $_SERVER['SERVER_ADDR'] ) && strpos ( $_SERVER['SERVER_ADDR'], '127.0' ) !== false ) || strpos ( $_SERVER['HTTP_HOST'], 'mint' ) !== false )
{
    $AC->dev_server		= TRUE;
	require ( $AC->root_path . '/app/config/config_dev.php' );
}
else
{
	$AC->dev_server 	= FALSE;
	require ( $AC->root_path . '/app/config/config_prod.php' );
}

$AC->route = new Router( $_SERVER['REQUEST_URI'] );
$module = $AC->route->getModule();
$action = $AC->route->getAction();

$ajax_flag              = (int)Request::getVar( 'ajax_flag', 0 );
$AC->ajax_flag          = $ajax_flag ? true : false;

$AC->user       = intval( Request::getVar( 'user_id', 0, 'session' ) );
$AC->uid        = trim( Request::getVar( 'user_uid', '', 'session' ) );
$AC->auth_ok    = true;

$DB = AutoLoader::DB( $AC->db_settings );

//check user
if ( $AC->user && $AC->uid && ( $module != 'user' || $action != 'auth' ) )
{
	if ( !Func::CheckUser() )
	{
		$AC->user = $AC->uid = '';
		$AC->auth_ok = false;
	}
	else if ( !$module )
		$module = 'first';
}
else if ( !$AC->user && ( $module != 'user' || $action != 'auth' ) )
	$AC->auth_ok = false;

$AC->module_time_start = time();
if ( $module && $AC->auth_ok )
{
	if ( isset ( $AC->modules[$module] ) )
		$AC->breadcrumb[] = $AC->modules[$module]['name'];

	if ( $action )
	{
		if ( strpos ( $action, 'edit' ) !== false )
			$AC->breadcrumb[] = 'Редактирование';
		else if ( strpos ( $action, 'show' ) !== false )
			$AC->breadcrumb[] = 'Просмотр';
		else if ( strpos ( $action, 'list' ) !== false )
			$AC->breadcrumb[] = 'Листинг';
		else if ( strpos ( $action, 'save' ) !== false )
			$AC->breadcrumb[] = 'Сохранение';
		else
			$AC->breadcrumb[] = 'Просмотр';
	}

	if ( !$AC->ajax_flag && $AC->user)
	{
		new View( '', array ( 'view' => 'header' ) );
		$AC->_view->render();
		new View( '', array ( 'view' => 'sidebar' ) );
		$AC->_view->render( array ( 'menu' => AppConf::getIns()->menu ) );
	}

	$AC->_controller = new Controller( $module, $action );

	if ( !$AC->ajax_flag && $AC->user )
	{
		new View( '', array ( 'view' => 'footer' ) );
		$AC->_view->render();
	}

}
else if ( !$AC->auth_ok )
{
	/*if a user is not authenticated*/
	new View( '../app/Resources/views/login_page.php' );
//	$AC->_view = new View( '', array ( 'module' => 'user', 'view' => 'login_page' ) );
	$AC->_view->render();
}
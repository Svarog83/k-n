<?php

use SDClasses\AppConf;
use SDClasses\Controller;
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

ignore_user_abort( TRUE );
$auth_verified  = false;

$AC->user       = intval( Request::getVar( 'user_id', 0, 'session' ) );
$AC->uid        = trim( Request::getVar( 'user_uid', '', 'session' ) );

$AC->module_time_start = time();

$DB = AutoLoader::DB( $AC->db_settings );

if ( $module )
{
	$AC->_controller = new Controller( $module, $action );
}

/*if a user is not authenticated*/
if ( !$AC->user || !$AC->uid )
{

	new View( '../app/Resources/views/login_page.php' );
//	$AC->_view = new View( '', array ( 'module' => 'user', 'view' => 'login_page' ) );
	$AC->_view->render();
}
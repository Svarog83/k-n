<?
function d($a) 		// debuging
{
	global ${$a};
	echo $a .'<br>'. nl2br(${$a}) .'<br><br>';
}
if ( !isset ( $US ) || !$US )
    $US = '_main';

$module = Request::getVar( 'module', '' );
$action = Request::getVar( 'action', '' );

require_once( $_SERVER['DOCUMENT_ROOT'] .'/incl_main/check_oms.php' );
require_once( '../skins/'. $US .'.php' );

require_once( '../function_main/function_all.php' );
$form_save_ajax = Request::getVar( 'form_save_ajax', '' );
if ( $form_save_ajax !== '' )
{
	$arr = array();
	parse_str( $form_save_ajax, $arr );
	$arr = post_var_edit_final( $arr, 1 );

	$_REQUEST = array_merge( $_REQUEST, $arr );
	unset ( $arr, $_REQUEST['form_save_ajax'] );
}

$new_user_skin          = Request::getVar( 'new_user_skin', '' );
$ajax_flag              = (int)Request::getVar( 'ajax_flag', 0 );
$AC->ajax_flag          = $ajax_flag ? true : false;
$flag_return_buffer     = (int)Request::getVar( 'flag_return_buffer', 0 );
$AC->flag_return_buffer = $flag_return_buffer ? true : false;
$no_frame               = (int)Request::getVar( 'no_frame', 0 );

// сделать проверку наличия акшена и если такого нет в бд, то unset action и грузим первую страницу
if( $module )
{
	$query = "
		SELECT
	setting_modules_title_". $UL ."
		FROM
	setting_modules
		WHERE
	setting_modules_name		= '". $module ."' &&
	setting_modules_active		= '1' &&
	setting_modules_visible		= '1'	&&
	setting_modules_use			= '1'
	";
	$DB->query( $query, __FILE__, __LINE__ );
	$count_mod = $DB->get_num_rows();
	$module_title = $DB->get_result();

	$query = "
		SELECT
	*
		FROM
	setting_value
		WHERE
	setting_value_module		LIKE '%:". $module .":%' 	OR
	setting_value_module		= ''
		ORDER BY
	setting_value_eng
	";
	$DB->query( $query, __FILE__, __LINE__ );

	while ( $row = $DB->get_fetch_ass() )
	{
		if ( !isset ( ${$row['setting_value_type']} ) )
			${$row['setting_value_type']} = array();

		${$row['setting_value_type']}['name'][$row['setting_value_id']] =
		( $row['setting_value_'. $UL] ? $row['setting_value_'. $UL] : $row['setting_value_eng'] );

		${$row['setting_value_type']}['abr'][$row['setting_value_id']] =
		( $row['setting_value_abr_'. $UL] || $row['setting_value_abr_eng'] ?
		( $row['setting_value_abr_'. $UL] ? $row['setting_value_abr_'. $UL] : $row['setting_value_abr_eng'] )
		:
		( $row['setting_value_'. $UL] ? $row['setting_value_'. $UL] : $row['setting_value_eng'] ) );
	}
}

if( file_exists( './incl/_main/module_setup.php' ) )
	require_once ( './incl/_main/module_setup.php' );

if( file_exists( '../incl/settings/glob_prj_config.php' ) )
	require_once ( '../incl/settings/glob_prj_config.php' );

if( $new_user_skin && $new_user_skin != $US && file_exists( '../skins/'. $new_user_skin .'.php' ) )
{
    $US = $new_user_skin;
    require_once( '../skins/'. $US .'.php' );
}

if ( !$action || ( isset ( $count_mod ) && $count_mod != 1 ) || !$module )
{

    $url = '';
	foreach ( $_REQUEST as $k => $v )
		if ( $k != 'module' && $k != 'action' )
			$url .= '&'. $k .'='. $v;


?>
<script language="JavaScript">
<!--
self.location = '../first/index.php?module=first&action=first>';
//-->
</script>
<?
exit;

}

$REQ_FILL = '<span style="color:red;font-weight:bold;cursor: pointer;" title="__**Обязательное поле**__">*</span>';

if ( !$AC->ajax_flag && !$AC->flag_return_buffer )
{
	require_once( '../incl/header.php' );
	require_once( '../incl/css.php' );
	require_once( '../incl/header_2.php' );
}
else
{
	/*Если это аяксовский запрос, то надо послать заголовок с кодировкой*/
	header("Expires: Wed, 01 July 2009 00:00:00");
	header("Cache-Control: no-store, no-cache, must-revalidate, private");
	header("Pragma: no-cache");
	header("Content-type: text/html; charset={$AC->Charset}");
}

require_once( $action .'.php' );
if ( !$AC->ajax_flag && !$AC->flag_return_buffer )
{
	require_once( '../incl/footer.php' );
}

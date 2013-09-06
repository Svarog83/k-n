<?
setcookie("check_num", "0");

?><pre><?= print_r( $_REQUEST ) ?></pre><?
?><pre><?= print_r( $_SERVER ) ?></pre><?

?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>Стартовая страница РЕМС</TITLE>
<?
error_reporting (E_ALL ^ E_NOTICE);
?>

<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<meta http-equiv="expires" content="wed,12 oct 1999 00:00:00 gmt">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-store">
<meta http-equiv="Last-Modified" content="<? echo gmdate("D, M d Y H:i:s") ?> GMT">

</HEAD>
<BODY style="background-color: #FFFFFF;">

<br><br><br><br>

<div id="oiltracking" style="display:block;text-align:center;">

<script>
<!--
document.write(unescape("%3CFORM%20method%3D%22get%22%20name%3D%22enter%22%20action%3D%22<?=$add_check_url?>check.php%22%20id%3D%22oiltarcking%22%20style%3D%22display%3Ablock%3B%22%20title%3D%22%20%20Operation%20Managment%20System%20%26%20Internet%20Reporting%20%20%20%22%3E%0D%0A%09%09%20%20%0D%0A%20%20%20%20%20%20%20%20%20%20%20%20%3CTABLE%20%20align%3Dcenter%20style%3D%22border%3Asolid%20%23669933%201px%20%3B%22%20cellpadding%3D%228%22%3E%0D%0A%20%20%09%09%20%20%20%20%20%20%3CTR%20height%3D100%25%3E%0D%0A%09%09%09%20%20%20%20%3CTD%20colspan%3D%222%22%20align%3D%22center%22%20%3E%3Cspan%20title%3D%22%20%20Operation%20Managment%20System%20%26%20Internet%20Reporting%20%20%22%20style%3D%22font-size%3A14pt%3B%20font-weight%3Abold%3B%20color%3A%23669933%22%3E%20REMS%3C%21--Operation%3Cbr%3EManagment%3Cbr%3ESystem%20--%3E%3C/span%3E%3Cbr%3E%0D%0A%09%09%09%20%20%3CINPUT%20type%3D%22button%22%20style%3D%22color%3Awhite%3B%20background-color%20%3A%23669933%3B%20border%3Asolid%20%23669933%201px%20%3B%20margin-top%3A4px%3B%22%20value%3D%22%20%20%26nbsp%3BENTER%26nbsp%3B%20%20%22%20onClick%3D%22check_window%28%27checking%27%29%3B%20document.enter.target%3Dwindow_title%3B%20document.enter.submit%28%29%3B%20document.enter.reset%28%29%22%3E%3C/TD%3E%0D%0A%09%09%09%20%20%3C/TR%3E%0D%0A%20%20%20%20%20%20%20%20%20%20%20%20%3C/TABLE%3E%0D%0A%20%20%20%20%20%20%20%20%3C/FORM%3E"));
// -->
</script>
</div>
<br>
<br>
<div style="text-align: center;">
<small>Вы находитесь на странице РЕМС</small>
<br><br><br>(c) <?= date ( 'Y' ); ?>
<br><br><a href="http://www.itgr.ru" target="_blank">IT Group</a>
</div>

<script type="text/javascript" src="/jquery/jquery-1.4.2.min.js"></script>

<script language="JavaScript">
<!--
var $j = jQuery;
$j(document).ready( function()
{
    $j("head").append($j('<script src="/jquery/ui/js/jquery-ui-1.8.16.custom.min.js"></scr'+ '' +'ipt>').attr("type","text/javascript"));
    $j("head").append($j('<script src="/jquery/jquery-validate/jquery.validate.min.js">').attr("type","text/javascript"));
    $j("head").append($j('<script src="/jquery/jquery.example.js">').attr("type","text/javascript"));
    $j("head").append($j('<script src="/jquery/jquery.fixedtableheader.min.js">').attr("type","text/javascript"));
    $j("head").append($j('<script src="/jquery/jquery.json-2.2.min.js">').attr("type","text/javascript"));
	//$j("head").append($j('<script src="/jquery/jquery.sha256.min.jss">').attr("type","text/javascript"));
<?
    $arr_js_files = array(
	    "main"=>"functions/",
	    "jquery_scrollTo"=>"",
	    "jquery_tooltip"=>"",
	    "jquery_autocomplete"=>"",
	    "positionBy"=>"menu/",
	    "bgiframe"=>"menu/",
	    "DP_Debug"=>"functions/",
	    "dump"=>"functions/" );
    $doc_root   = $_SERVER['DOCUMENT_ROOT'];
    foreach($arr_js_files as $js_file=>$js_path)
    {
        $_js_file = "/js/". $js_path.$js_file. ".js";
        $timestamp = filemtime( $doc_root . $_js_file );
        //$_js_file = "/js/". $js_path.$js_file.".v".$timestamp. ".js";
?>
    $j("head").append($j('<script src="<?=$_js_file?>">').attr("type","text/javascript"));
<?
    }
?>

});

//-->
</script>

</BODY>
</HTML>

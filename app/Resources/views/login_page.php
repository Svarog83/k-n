<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Unicorn Admin</title>
		<meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="/css/bootstrap.min.css" />
		<link rel="stylesheet" href="/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="/css/unicorn.login.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
    <body>
        <div id="logo">

        </div>
        <div id="loginbox">
            <form id="loginform" class="form-vertical" action="/user/auth" method="POST" />
				<p>Введите логин и пароль</p>
                <div class="control-group">
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-user"></i></span><input type="text" name="form_login" placeholder="Логин" />
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-lock"></i></span><input type="password" name="form_password" placeholder="Пароль" />
                        </div>
                    </div>
	                <?php if ( !empty( $params['flash_message'] ) ): ?>
					<span style="color: red; font-weight: bold;"><?php echo $params['flash_message'] ?></span>
	                <? endif; ?>
                </div>
                <div class="form-actions">
                    <span class="pull-left"><a href="mailto: support@ilaptev.com" class="flip-link" id="to-recover">Забыли пароль?</a></span>
                    <span class="pull-right"><input type="submit" class="btn btn-inverse" value="Войти" /></span>
                </div>
            </form>
        </div>

        <script src="/js/jquery.min.js"></script>
        <script src="/js/unicorn.login.js"></script>

        <script language="JavaScript">
        <!--
        var $j = jQuery;
        $j(document).ready( function()
        {

            $j("head").append($j('<script src="/js/plugins/jquery-validate/jquery.validate.min.js">').attr("type","text/javascript"));
            $j("head").append($j('<script src="/js/plugins/jquery.example.js">').attr("type","text/javascript"));
            $j("head").append($j('<script src="/js/plugins/jquery.fixedtableheader.min.js">').attr("type","text/javascript"));
            $j("head").append($j('<script src="/js/plugins/jquery.json-2.2.min.js">').attr("type","text/javascript"));
        <?
            $arr_js_files = array(
        	    "main"=>"",
        	    "jquery_scrollTo"=>"",
        	    "jquery_tooltip"=>"",
        	    "jquery_autocomplete"=>"",
        	    "DP_Debug"=>"",
        	    "dump"=>"" );
            $web_root   = SDClasses\AppConf::getIns()->root_path . '/web';
            foreach ($arr_js_files as $js_file => $js_path )
            {
                $_js_file = "/js/functions/". $js_path.$js_file. ".js";
                $timestamp = filemtime( $web_root . $_js_file );
                $_js_file = "/js/functions/". $js_path.$js_file.".v".$timestamp. ".js";
        ?>
            $j("head").append($j('<script src="<?=$_js_file?>">').attr("type","text/javascript"));
        <?
            }
        ?>

        });

        //-->
        </script>

	<div id="footer" style="text-align: center; color: #0088cc">
        <?php echo '2013' . ( date( "Y" ) != '2013' ? '-' . date( "Y" ) : '' ) ?> &copy; SD development</a>
    </div>

    </body>
</html>
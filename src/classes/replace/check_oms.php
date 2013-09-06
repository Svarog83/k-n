<?php
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
// проверяет юзера при его постоянной работе в REMS
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////

ignore_user_abort( TRUE );

$not_auth       = false;
$auth_verified  = false;

$AC->uid        = trim( Request::getVar( 'user_uid', '', 'session' ) );
$AC->user       = intval( Request::getVar( 'user_id', 0, 'session' ) );

$pager_update   = (int)Request::getVar( 'pager_update', 0 );
$change_lang    = (string)Request::getVar( 'change_lang', '' );

$time = time();

$done_insert_main   = false;
$limit_time         = false; //flag,, show if user's session has expired.
$check_ip           = TRUE;
$timeout_flag       = FALSE;
$count_user         = 0;

$DB = Loader::DB( $AC->db_main_settings );

if ( $AC->uid && $AC->user )
{
    $query = "
        SELECT
    db, time, checksum, checksum2
        FROM
    userh
        WHERE
    uid	= '" . mysql_real_escape_string( $AC->uid ) . "' &&
    id = '" . mysql_real_escape_string( $AC->user ) . "'
    ";
    $DB->query( $query, __FILE__, __LINE__ );

    $count_main = $DB->get_num_rows();

    $row = $DB->get_fetch_ass();

    if ( $count_main && ( $time - $row['time'] < $AC->time_limit * 60 * 60 ) ) // если есть во временной таблице
    {
        ///////////////////////////////////////////////////////////////////
        ///  выбираем доступ к соответствующей базе данных	++++++++++++++

        if (
            isset ( $_COOKIE['rems_ssid' . $AC->uid] ) &&
            (
                md5( $_COOKIE['rems_ssid' . $AC->uid] ) == $row['checksum'] ||
                    (
                        !isset( $_COOKIE['rems_ssid' . $AC->uid] ) &&
                        !$row['checksum']
                    )
             )
           )
        {
            $query = "SELECT * FROM db WHERE id = '" . $row['db'] . "'";
            $DB->query( $query, __FILE__, __LINE__ );
            $row = $DB->get_fetch_ass();

	        $AC->db_settings = array (
		    'db_id'         => $row['id'],
			'db_host'	    => $row['db_host_name'],
			'db_name'		=> $row['db_name'],
			'db_user_name'	=> $row['db_login'],
			'db_password'	=> $row['db_pass'],
			'db_port'       => $row['db_port'],
			'db_charset'    => $row['db_charset'] );

            /* Encoding */
            $AC->DBCharset = $row['db_charset'];
	        $AC->Charset = ( $AC->DBCharset == 'utf8' ? 'utf-8' : 'windows-1251' );

            $DB = Loader::DB ( $AC->db_settings, 'reconnect' );

            /*Select the user details from the database*/
            $query = "
                SELECT
            *
                FROM
            user
                WHERE
            user_uid	= '" . mysql_real_escape_string( $AC->uid ) . "' &&
            user_id		= '" . mysql_real_escape_string( $AC->user ) . "' &&
            user_activ	IN ( 'a' )
            ";
            $DB->query( $query, __FILE__, __LINE__ );

            $count_user = $DB->get_num_rows();

            if ( $count_user == 1 )
                $UA = $row = $DB->get_fetch_ass();

        }
        else
        {
            //if the cookie is wrong
            $query = "
    			SELECT
    	     *
		         FROM
		     z_cookie_err
    			WHERE
			 zce_user        = '". $AC->user ."' &&
			 zce_cookie      = '". ( isset ( $_COOKIE['rems_ssid'. $AC->uid] ) ? $_COOKIE['rems_ssid'. $AC->uid] : '' ) ."' &&
			 zce_cookie_s    = '". $row['checksum2'] ."' &&
			 zce_ip          = '". $_SERVER['REMOTE_ADDR'] ."' &&
			 zce_agent       = '". $_SERVER['HTTP_USER_AGENT'] ."' &&
			 zce_uri         = '". $_SERVER['REQUEST_URI'] ."'
			";
            $DB->query( $query, __FILE__, __LINE__ );

            if ( $DB->get_num_rows() )
            {
                $query = "
    				UPDATE
				z_cookie_err
    				SET
				zce_count = zce_count + 1,
				zce_time = '" . time( ) . "'
    				WHERE
				zce_user        ='" . mysql_real_escape_string( $AC->user ) . "' &&
				zce_cookie      ='" . mysql_real_escape_string( isset ( $_COOKIE['rems_ssid' . $AC->uid] ) ? $_COOKIE['rems_ssid' . $AC->uid] : '' ) . "' &&
				zce_cookie_s    ='" . mysql_real_escape_string( $row['checksum2'] ) . "' &&
				zce_ip          ='" . mysql_real_escape_string( $_SERVER['REMOTE_ADDR'] ) . "' &&
				zce_agent       ='" . mysql_real_escape_string( $_SERVER['HTTP_USER_AGENT'] ) . "'&&
				zce_uri         ='" . mysql_real_escape_string( $_SERVER['REQUEST_URI'] ) . "'
				";
            }
            else
            {
                $query = "
    				INSERT INTO
				z_cookie_err
    				SET
				zce_user        ='" . mysql_real_escape_string(  $AC->user ) . "',
				zce_count       = 0,
				zce_cookie      ='" . mysql_real_escape_string( isset ( $_COOKIE['rems_ssid' . $AC->uid] ) ? $_COOKIE['rems_ssid' . $AC->uid] : '' ) . "',
				zce_cookie_s    ='" . mysql_real_escape_string( $row['checksum2'] ) . "',
				zce_ip          ='" . mysql_real_escape_string( $_SERVER['REMOTE_ADDR'] ) . "',
				zce_agent       ='" . mysql_real_escape_string( $_SERVER['HTTP_USER_AGENT'] ) . "',
				zce_uri         ='" . mysql_real_escape_string( $_SERVER['REQUEST_URI'] ) . "',
				zce_time        = '" . time( ) . "'
				";
            }
            $DB->query( $query, __FILE__, __LINE__ );

            $count_user = 0; //the user is not found
        }

    }
    else // если нет во временной таблице
    {
        ////// создаем массив баз данных, доступных на сервере
        $arr_db = array( );
        $count_user = 0;

        $query = "SELECT * FROM db";
        $DB->query( $query, __FILE__, __LINE__ );

        while ( $row = $DB->get_fetch_ass() )
            $arr_db[] = array(
				'id'    => $row['id'],
				'h'     => $row['db_host_name'],
				'prt'   => $row['db_port'],
				'n'     => $row['db_name'],
				'u'     => $row['db_login'],
				'p'     => $row['db_pass'],
				'c'     => $row['db_charset']
			);

        /////// ищем юзера по всем базам

        foreach ( $arr_db as $v )
        {
            $AC->db_settings = array (
			'db_host'	=> $v['h'],
			'db_name'		=> $v['n'],
			'db_user_name'	=> $v['u'],
			'db_password'	=> $v['p'],
			'db_port' => $v['prt'],
			'db_charset' => $v['c'] );

			$DB = Loader::DB( $AC->db_settings, 'reconnect' );

            $query = "SELECT * FROM user WHERE user_uid	= '" . $AC->uid . "' && user_id = '" . $AC->user . "' && user_activ	IN ( 'a' )";
            $DB->query( $query, __FILE__, __LINE__ );

            $count_user = $DB->get_num_rows();

            if ( $count_user )
            {
                $AC->db_settings['db_id'] = $v['id'];
	            break;
            }

        }

		$DB = Loader::DB ( $AC->db_main_settings, 'reconnect' );

        //// если нашли юзера на какой-либо базе

        if ( $count_user == 1 )
        {
            $UA = $DB->get_fetch_ass();

            if ( $time - $UA['user_date'] < 60 * 60 * $AC->time_limit )
            {
                $query = "DELETE FROM userh WHERE id = '" . $AC->user . "' && db = '" . $AC->db_settings['db_id'] . "'";
                $DB->query( $query, __FILE__, __LINE__ );

                $query = "
					INSERT INTO
			    userh
					SET
				id		= '" . $AC->user . "',
				uid		= '" . $AC->uid . "',
				db		= '" . $AC->db_settings['db_id'] . "',
				time	= '" . time( ) . "'";
                $DB->query( $query, __FILE__, __LINE__ );

                $done_insert_main = TRUE;
            }
            else
                $timeout_flag = TRUE;

	        if ( !$done_insert_main && !$pager_update )
			{
				$query = "UPDATE userh SET time = '" . time() . "' WHERE id = '" . $AC->user . "'";
				$DB->query( $query, __FILE__, __LINE__ );
			}
        }
    }

	$DB = Loader::DB ( $AC->db_settings, 'reconnect' );

    if ( $count_user == 1 && !$timeout_flag )
    {
         ///////////////////////////////////////////////////////////////////
        //////// собираем массив юзера	+++++++++++++++

        $UL = $UA['user_lang'];
        $auth_verified = TRUE;
        $UT = $UA['user_type'];
        $US = ( $UA['user_skin'] ? $UA['user_skin'] : '_main' );

        if ( $change_lang )
        {
            $query = "UPDATE user SET user_lang='" . $change_lang . "' WHERE user_id = '" . $AC->user . "'";
            $DB->query( $query, __FILE__, __LINE__ );
            $UL = $change_lang;
        }

        $ARR_ADMIN_TUNE =
                $ARR_CUR_TUNE =
                        $ARR_PROHIB_TUNE =
                                $ARR_ADMIN_TUNE_TYPE =
                                        $ARR_ADMIN_EDIT_TYPE = array( );
        $ADMIN_ACTION = FALSE;

		if( $UA['user_admin'] != 'sa' ) /// для пользователей выбираем разрешения
        {
			$query  = "
				SELECT
					*
				FROM
					user_level
					LEFT JOIN user_admin_items ON ( ul_perm LIKE CONCAT(  '%:', uai_name,  ':%' ) )
				WHERE
					ul_abr = '". $UT ."' &&
					ul_activ	= 'a' &&

					uai_activ	= '1'
			";
			$DB->query( $query, __FILE__, __LINE__ );

			while ( $row = $DB->get_fetch_ass() )
                if ( $row['uai_type'] == 'a' )
                    $ARR_ADMIN_TUNE[] = $row['uai_name'];
                elseif ( $row['uai_type'] == 'c' )
                    $ARR_CUR_TUNE[] = $row['uai_name'];
		}

        if ( $UA['user_cur_tune'] && $UA['user_admin'] != 'sa' )
            $ARR_CUR_TUNE = str_replace( ':', '', explode( '::', $UA['user_cur_tune'] ) );
        if ( $UA['user_prohib_tune'] && $UA['user_admin'] != 'sa' )
            $ARR_PROHIB_TUNE = str_replace( ':', '', explode( '::', $UA['user_prohib_tune'] ) );

        if ( $UA['user_admin'] == 'sa' )
        {
            $ADMIN_ACTION = TRUE;

	        AppConfig::getIns()->admin_flag = true;

            $query = "
				SELECT
			*
				FROM
		    user_admin_items
				WHERE
			uai_activ	= '1' &&
			uai_type IN ( 'a', 'c' )
			";
            $DB->query( $query, __FILE__, __LINE__ );

            while ( $row = $DB->get_fetch_ass() )
                if ( $row['uai_type'] == 'a' )
                    $ARR_ADMIN_TUNE[] = $row['uai_name'];
                elseif ( $row['uai_type'] == 'c' )
                    $ARR_CUR_TUNE[] = $row['uai_name'];

        }
        elseif ( $UA['user_admin'] == 'a' && $UA['user_admin_tune'] )
            $ARR_ADMIN_TUNE = str_replace( ':', '', explode( '::', $UA['user_admin_tune'] ) );

        // проверка ip адреса в случае привязки юзера к опредeленному адресу
        if ( $UA['user_ip_request'] && $UA['user_ip'] != $_SERVER['REMOTE_ADDR'] )
            $check_ip = FALSE;
        else
            $check_ip = TRUE;

        //// проверка времени работы в РЕМСе
        $user_date = time( );

        if ( $user_date - $UA['user_date'] < ( 60 * 60 * $AC->time_limit ) && $user_date - $UA['user_date'] > ( 60 * 60 * $AC->time_limit_update ) )
        {
            $limit_time = TRUE;

            if ( !$pager_update )
            {
                $query = "UPDATE user SET user_date = '" . $user_date . "' WHERE user_id = '" . $UA['user_id'] . "'";
                $DB->query( $query, __FILE__, __LINE__ );
            }

        }
        elseif ( $user_date - $UA['user_date'] > ( 60 * 60 * $AC->time_limit ) )
            $limit_time = FALSE;
        else
            $limit_time = TRUE;
    }

}

/*
/////////////////////////////////////////
echo "count_user<br>".$count_user."<br><br>";
echo "uid<br>".$AC->uid."<br><br>";
echo "check_ip<br>".$check_ip."<br><br>";
echo "limit_time<br>".$limit_time."<br><br>";
flush();
sleep(10000);
*/

/* if it's called from pager and timed_out we need to set flag */
if ( ( !$limit_time || $count_user != 1 ) && isset ( $pager_update ) && $pager_update )
{
    $return_arr['time_out'] = 1;
}
else if ( ( $count_user != 1 || !$AC->uid || !$check_ip || !$limit_time ) )
{
	$f = '';

    if ( $count_user != 1 )     $f = 'user_not_found';
    elseif ( !$AC->uid )        $f = 'uid_not_found';
    elseif ( !$check_ip )       $f = 'wrong_ip';
    elseif ( !$limit_time )     $f = 'time_out';
    elseif ( $timeout_flag )    $f = 'timeout_flag';
?>

    <h3>
	    Возникли проблемы с авторизацией в системе.<br>
	    <a href="/">Пожалуйста, попробуйте войти еще раз</a></h3>
    <script language="JavaScript">
        <!--
        try
        {
            window.close();
        }
        catch(e)
        {
        }

        function check_window( pg, pgtit )
        {
            var checking = window.open( pg, pgtit, ' height=600, width=800, status=yes, toolbar=yes, menubar=yes, location=yes, resizable=yes, scrollbars=auto' );
            checking.resizeTo( screen.availWidth, screen.availHeight );
            checking.moveTo( 0, 0 );

            checking.location = pg;
            checking.focus;
        }

        check_window( '/<?= ( $f == 'time_out' || $f == 'timeout_flag' ? 'expired' : 'index' ) ?>.php?f=<?= $f ?>', '' );

        //-->
    </script>

    <?

    exit;

}

//require_once( '../netcop/user_lookup.php' );
require_once ( $_SERVER['DOCUMENT_ROOT'] . '/incl_main/translate.php' );
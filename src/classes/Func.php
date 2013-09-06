<?php

class Func
{
	static private $_debug;
	static private $_log;
	static public  $CurAbr = array();
/**
 * Отдает массив торговых профилей в селект в виде дерева
 *
 * @param bool $input
 * @return unknown
 */
	public static function getTradeProfilesAll( $input = FALSE )
	{
		$DB = Loader::DB();
		$DB->setDebug( self::$_debug,  self::$_log );

		$arr          =
		$arr_except   =       // элементы массива, которые будут выводиться без значения, как исключения
		$arr_return   = array();

		$DB->query( "SELECT * FROM s_commodity_group ORDER BY cgroup_name", __FILE__, __LINE__ );

		while ( $row = $DB->get_fetch_ass() )
			$arr[ $row['cgroup_parent_id'] ][ $row['cgroup_id'] ] = $row['cgroup_name'];

        foreach( $arr as $k => $v )
        {

            if( $k == 0 )
                foreach( $v as $kk => $vv )
                {
                    $arr_return[ $kk ] = $vv;

                    if( !empty( $arr[ $kk ] ) )
                    {
                        if( $input )
                            $arr_except[] = $kk;

                        foreach( $arr[ $kk ] as $kkk => $vvv )
                            $arr_return[ $kkk ] = '&nbsp;&nbsp;&nbsp;&nbsp;'.$vvv;
                    }
                }



        }

		$DB->setDebug();


		if( $input && count( $arr_except ) )
		{

		    $arr_tmp      = $arr_return;
		    $arr_return   = array();

		    $arr_return['select_values']  = $arr_tmp;
		    $arr_return['block_values']   = $arr_except;


		}

		return $arr_return;
	}
/**
 * Отдает массив торговых профилей первого уровня
 *
 * @return unknown
 */


    public static function getTradeProfilesMain()
	{
		$DB = Loader::DB();
		//$DB->setDebug( self::$_debug,  self::$_log );

		$arr_return   = array();

		$DB->query( "SELECT * FROM s_commodity_group WHERE cgroup_parent_id = 0 ORDER BY cgroup_name", __FILE__, __LINE__ );

		while ( $row = $DB->get_fetch_ass() )
			$arr_return[ $row['cgroup_id'] ] = $row['cgroup_name'];

		$DB->setDebug();

		return $arr_return;
	}


/**
 * Отдает название торгового профилей первого уровня
 *
 * @return unknown
 */
	public static function showTradeProfile( $contr_id )
	{
		$DB = Loader::DB();

		$DB->query( "SELECT cgroup_name FROM s_commodity_group, contr WHERE cgroup_id = contr_comm_group && contr_id = '". $contr_id ."'", __FILE__, __LINE__ );

		while ( $row = $DB->get_fetch_ass() )
			return $row['cgroup_name'];

	}

	public static function getBrands()
	{
		$DB = Loader::DB();
		$DB->setDebug( self::$_debug,  self::$_log );

		$arr = array();
		$query = "SELECT * FROM brand ORDER BY brand_name";
		$DB->query( $query, __FILE__, __LINE__ );
		while ( $row = $DB->get_fetch_ass() )
			$arr[$row['brand_id']] = $row['brand_name'];

		$DB->setDebug();

		return $arr;
	}

	public static function setDebug( $debug = false, $log = false )
	{
		self::$_debug   = $debug;
		self::$_log     = $log;
	}

	public static function getCurrencies( $only_used = true )
	{
		$DB = Loader::DB();
		$DB->setDebug( self::$_debug,  self::$_log );

		$arr = array();
		$query = "SELECT cur_id, cur_abr FROM currency". ( $only_used ? " WHERE cur_use = 1 " : '' ) ." ORDER BY cur_abr";
		$DB->query( $query, __FILE__, __LINE__ );

		while ( $row = $DB->get_fetch_ass() )
			$arr[ $row['cur_id'] ] = $row['cur_abr'];

		$DB->setDebug();

		return $arr;
	}

	public static function getCurAbr( $cur_id )
	{

		$cur_id = (int)$cur_id;
		$cur_abr = '';

		if ( !count ( self::$CurAbr ) )
		{
			$DB = Loader::DB();
			$DB->setDebug( self::$_debug,  self::$_log );

			$query = "SELECT cur_id, cur_abr FROM currency WHERE 1";
			$DB->query( $query, __FILE__, __LINE__ );
			while ( $row = $DB->get_fetch_ass() )
				self::$CurAbr[$row['cur_id']] = $row['cur_abr'];

			$DB->setDebug();
		}

		if ( isset ( self::$CurAbr[$cur_id] ) )
			$cur_abr = self::$CurAbr[$cur_id];

		return $cur_abr;
	}

	public static function number2( $a )
	{
		if ( $a != '' )
		{
			$a = number_format( (double)str_replace( ",", ".", $a ), 2, ".", "" ) ;
			$a = str_replace( '-0.00', '0.00', $a );
		}
		return( $a );
	}

	public static function number2_space( $a )
	{
		$a = number_format( (double)str_replace( ",", ".", $a ), 2, ",", " " ) ;
		$a = str_replace( '-0.00', '0.00', $a );
		return( $a );
	}


	/**
	 * Функция для перевод даты формата DD.MM.YYYY в YYYY-MM-DD для сохранения в БД
	 * Если формат правильный, то ничего не происходит
	 * Если проверка формата не прошла возвращает пустую строку
	 *
	 * @static
	 * @param  $date - Дата в каком-то формате
	 * @return string Дату в формате YYYY-MM-DD
	 */

	public static function getDate( $date )
	{

	    if ( preg_match( "/([0-9]{2})\.([0-9]{2})\.([0-9]{4})/", $date, $matches ) )
			return( $matches[3] . '-' . $matches[2] . '-' . $matches[1] );
		else
			return $date;

	}


	/**
	 * Функция для вывода даты в формате DD-MM-YYYY - для просмотра пользователями
	 *
	 * @static
	 * @param  $date - Дата в каком-то формате YYYY-MM-DD
	 * @param string $separator - Разделитель
	 * @return string Дату в формате DD.MM.YYYY
	 */


	public static function showDate( $date, $separator = '.' )
	{

	    if( $date == '0000-00-00' || empty( $date ) )
	       return( '' );

	    $arr_tmp = explode( '-', $date );

	    if( count( $arr_tmp ) == 3 )
	       return( $arr_tmp[2] . $separator . $arr_tmp[1] . $separator . $arr_tmp[0] );


	}


	public static function formatDate( $date, $format )
	{
		$new_date = '';

		if ( $date )
		{

			$timestamp = strtotime( $date );

			if ( $format == 'dd.mm.yy' )
				$new_date = date ( "d.m.Y", $timestamp );
			else
				$new_date = $date;
		}

		return $new_date;
	}

	public static function getCurRates ( $date, $type = false )
	{
		$type = ( $type ? $type : 'm' );

		$date_unix = strtotime( $date );

		$query = "
			SELECT
		cr_rate,  cr_currency
			FROM
		cur_rates
			WHERE
		cr_date     <= '". $date ."'	&&
		cr_date     >= '". date ( 'Y-m-d', $date_unix - ( 86400 * 360 ) ) ."'	&&
		cr_type     =  '". $type ."'
			ORDER BY
		cr_currency,
		cr_date DESC
		";

		$CurRates = array();

		$result = mysql_query($query) or eu( __FILE__, __LINE__, $query );

		while ( $row = mysql_fetch_array ( $result, MYSQL_ASSOC ) )
			if ( !isset( $CurRates[ $row['cr_currency'] ] ) )
				$CurRates[ $row['cr_currency'] ] = $row['cr_rate'];

		return $CurRates;
	}

	/**
	 * Converts from one currency to another...
	 *
	 * @param array $CurRates - array with currency rates
	 * @param float $val   - value that needs to be converted
	 * @param int $from_cur - source currency
	 * @param int $to_cur   - destination currency
	 * @return double       - new value
	 */

	public static function exchangeToCur ( $CurRates, $val, $from_cur, $to_cur )
	{

		if ( empty( $CurRates[ $from_cur ] ) )
			$CurRates[ $from_cur ] = 1;

		if ( $from_cur == $to_cur )
			$rate   = 1;

		else if ( $CurRates[ $to_cur ] && $CurRates[ $from_cur ] )
			$rate 	= round ( $CurRates[ $to_cur ] / $CurRates[ $from_cur ], 5 );

		if( !$rate )
		{
			$mes = '
			Dear dear Admins....

			System can not calculate cross rate in function "ExchangeToCur" in "Func.php" for

			From : ID: '.$from_cur .' ----
			To   : ID: '.$to_cur .' ----

			user: '. GlobalSetup::getInstance()->user .'

			CurRates
			'. print_r( $CurRates, true ) .'

			POST
			'. print_r( $_POST, true ) .'

			GET
			'. print_r( $_GET, true ) .'


			With the deepest respect,

			REMS Error Spy
			'. date( 'Y-m-d H:i:s' ) .'

			';

			   error_log( $mes );
		}
		else
			$new_val	= $val / $rate;

		return $new_val;
	}


	public static function showTopTabs( $id, $htabs, $first_line )
	{
		?>
		<div id="<?php echo $id ; ?>" style="width: 100%; height:20px;background:#4051C0; padding-top:3px;padding-left:10px;overflow:hidden;color:#C7D2F1; border-bottom:4px solid #96ACE7;">
			<div style="height:20px; widht: 100%; overflow:hidden;">
			<?
				echo $first_line;
				if( is_array( $htabs['title'] ) )
					foreach( $htabs['title'] as $k=> $v )
					{
					?>
					   <a id="top_tab_<?=$k?>" class="top_tab" onClick='GoToTab("<?=$k?>")' >&nbsp;&nbsp;<?=$v?>&nbsp;&nbsp;</a>
					<?
					}
			?>
				</div>
		</div>

		<script type="text/javascript">
			<!--
			$j(document).ready( function()
			{
				/* we set it to show properly the scroll bar from the right side */
					var iheight = $j(window).height() - 44;
					$j('body').height( $j(window).height()-8 );
					$j('#form_with_select').css('height', iheight+'px');
					$j('#form_with_select').before($j("#<?php echo $id ; ?>"));

			});
			$j(window).resize(function()
			{
				var iheight = $j(window).height() - 44;
				$j('body').height( $j(window).height()-8 );
				$j('#form_with_select').css('height', iheight+'px');

			});
			//-->
		</script>

<?
	}

	public static function dates_gap ( $str1, $str2 )
	{
		return floor( ( strtotime( $str2 ) - strtotime( $str1 ) ) / 86400 );
	}

	public static function dates_intersect( $date1_start, $date1_end, $date2_start, $date2_end )
	{
		return ( $date1_start == $date2_start ) || ( $date1_start > $date2_start ? $date1_start <= $date2_end : $date2_start <= $date1_end );
	}

	public static function CheckDates( $DatesPairs )
	{
		$intersect = false;
		$gap       = false;

		$len = count ( $DatesPairs );
		if ( $len > 1 )
		{
			for ( $i = 0; $i < $len; $i++ )
			{
				for ( $j=$i+1; $j < $len; $j++ )
				{
					$dates1 = $DatesPairs[$i];
					$dates2 = $DatesPairs[$j];

					$intersect = Func::dates_intersect( $dates1[0], $dates1[1], $dates2[0], $dates2[1] );

					if ( $intersect )
					{
						break;
					}
				}

				if ( $intersect )
				{
					break;
				}
			}
		}

		if ( $intersect )
		{
			echo '<div id="div_warn_date_id"><br><div class="warn">__**Ошибка в датах Финансовых условий!**__<br><br>__**Следующие даты пересекаются**__: ' . Func::showDate( $dates1[0] ) . ' : ' . Func::showDate( $dates1[1] ) . ' __**и**__ ' .Func::showDate( $dates2[0] ) . ' : ' . Func::showDate( $dates2[1] ) . '<br>__**Будет заблокирован выпуск счетов!**__</div></div>';
		}
		else
		{
			$AllDates  = Array();
			for ( $i = 0; $i < $len; $i++ )
			{
				$dates1 = $DatesPairs[$i];
				$AllDates[] = $dates1[0];
				$AllDates[] = $dates1[1];
			}

			sort( $AllDates );

			for ( $i = 2; $i < count ( $AllDates ); $i+=2 )
			{
				$j = $i - 1;
				$date1 = $AllDates[$j];
				$date2 = $AllDates[$i];
				$x = Func::dates_gap( $date1, $date2 );

				if ( $x > 1 )
				{
					$gap = true;
					break;
				}
			}

			if ( $gap )
			{
				echo '<div id="div_warn_date_id"><br><div class="warn">__**Ошибка в датах Финансовых условий!**__<br><br>__**Есть разрыв в следующих датах**__: ' . Func::showDate( $date1 ) . ' __**и**__ ' . Func::showDate( $date2 ) . '<br>__**Будет заблокирован выпуск счетов!**__</div></div>';
			}
		}

		return $intersect || $gap;
	}

	public static function CheckRights()
	{
		/*Заглушка. TODO: надо будет написать настоящую функцию проверки прав*/

		return true;
	}

	/*Преобразует число вида 123 444,34 в 123444.34*/
	public static function getPrice( $str )
	{
		return str_replace ( array (' ', ',' ), array ( '', '.' ), $str );
	}

	/*Возвращает "полноту" периода: 0 - меньше месяца, 1 - месяц, 3 - квартал*/
	public  static  function checkPeriod( $start_date, $end_date )
	{
		$period = 0;

		/*[0] - year, [1] - month, [2] - day*/
		$tmp_arr = explode ( '-', $start_date );
		$start_year     = $tmp_arr[0];
		$start_month    = $tmp_arr[1];
		$start_day      = $tmp_arr[2];
		$start_time     = strtotime( $start_date );

		$tmp_arr = explode ( '-', $end_date );
		$end_year     = $tmp_arr[0];
		$end_month    = $tmp_arr[1];
		$end_day      = $tmp_arr[2];
		$end_time     = strtotime( $end_date );

		/*Если месяц и год не изменились*/
		if ( $start_year == $end_year && $start_month == $end_month )
		{
			if ( $start_day == '01' && $end_day == date( "t", $end_time ) )
			{
				/*У нас "покрыт" полный месяц*/
				$period = 1;
			}
		}
		else
		{
			/*Если у нас указаны разные месяца или года*/

			if ( $start_day == '01' && $end_day == date( "t", $end_time ) ) /*Убеждаемся, что указаны самое начало и самый конец месяца*/
			{
				/*Считаем кол-во _полных_ месяцев разницы между двумя датами*/
				$cnt = 0;
				$min_date = $start_time;

				while ( ( $min_date = strtotime("+1 MONTH", $min_date) ) <= $end_time )
					$cnt++;

				$period = $cnt + 1; /*Нужно прибавить 1, чтобы получит точное кол-во месяцев, т.к. даты у нас с первого по последнее число*/
			}
		}

		return $period;
	}
}

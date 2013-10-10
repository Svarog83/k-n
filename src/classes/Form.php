<?php


namespace SDClasses;
/**
 * A helper class with functions for form generating
 */
class Form extends FormBasic
{
	/**
	 * @var string
	 */
	private $_req_fill = '';
	/**
	 * @var array
	 */

	/**
    * @var View
	*/
	private $view;

	private $options = array();

	/** Constructor
	 * @param array $view
	 * @param array $options
	 */
	public function __construct( $view, $options = array() )
	{
		$this->view = $view;
		$this->options = $options;
		$this->_req_fill = '&nbsp;<span style="color:red;" title="Обязательное поле">*</span>';
	}


	/**
	 * @param array $options
	 * @param bool $start
	 * @param string $title
	 * @param bool $required
	 * @return string
	 */
	function showDataBlock( $options = array(), $start = false, $title = '', $required = false )
	{
		ob_start();
		if ( $start )
		{
			?>
			<div class="control-group" <?php echo isset ( $options['div_id'] ) ? 'id="' . $options['div_id'] . '"' : '' ?>>
			<label class="control-label sd_entry_label"><?= $title ?><?= $required ? $this->_req_fill : '' ?></label>

			<div class="controls sd_content">

		<?
		}
		else
		{
			?>
			</div>
			</div>
		<?
		}
		$txt = ob_get_contents();
		ob_end_clean();
		return $txt;
	}

	/** Show some text with a hidden field
	 * @param string $title
	 * @param string $text
	 * @param string $field_name
	 * @param string $value
	 * @internal param array $options
	 * @return string
	 *
	 */
	function showHiddenBlock( $title, $text, $field_name = '', $value = '' )
	{
		$this->showDataBlock( array(), true, $title );

		echo '<b>' . $text . '</b>' . ( $field_name ? '<input type="hidden" name="' . $field_name . '" value="' . $value . '"/>' : '' );

		$this->showDataBlock();

	}

	/** Shows block with a text field
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showTextBlock( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true : false;

		if ( empty ( $options['size'] ) )
			$options['size'] = 50;

		$txt = $this->showDataBlock( $options, true, $title, $required );

		$txt .= $this->showTextInput( $field_name, $value, $options );

		$txt .= $this->showDataBlock();

		return $txt;
	}

	/** Show a block with a date field
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showDateBlock( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true
				: false;

		$txt .=	$this->showDataBlock( $options, true, $title, $required );

		$txt .= $this->showDateInput( $field_name, $value, $options );

		$txt .= $this->showDataBlock();

		return $txt;
	}

	/** Show a block with select
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showSelectBlock( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== FALSE ? TRUE : FALSE;

		$arr = array( 'type' => isset ( $options['select_type'] ) ? $options['select_type'] : 'select',
			'field_name' => $field_name,
			'id' => isset ( $options['id'] ) ? $options['id'] : '',
			'onchange' => '',
			'show_select_title' => isset ( $options['show_select_title'] ) ? $options['show_select_title'] : 0,
			'multiple' => isset ( $options['multiple'] ) ? $options['multiple'] : '',
			'size' => isset ( $options['size'] ) ? $options['size'] :  ( !empty ( $options['mupltiple'] ) ? '1' : '' ),
			'select_values' => isset ( $options['select_values'] ) ? $options['select_values'] : array(),
			'block_values' => isset ( $options['block_values'] ) ? $options['block_values'] : array(),
			'selected_value' => $value,
			'add_str' => ( isset ( $options['add_str'] ) ? $options['add_str'] : '' )
		);

		if ( !isset ( $arr['add_str'] ) )
			$arr['add_str'] = '';

		if ( !empty( $options['validation'] ) )
			$arr['add_str'] .= ' class="' . $options['validation'] . '"';

		$txt = $this->showDataBlock( $options, true, $title, $required );

		$txt .= $this->showSelectInput( $arr );

		$txt .= $this->showDataBlock();

		return $txt;
	}

	/** Show a block with checkboxes
	 * @param string $title
	 * @param string $field_name
	 * @param array $value
	 * @param array $options
	 * @return string
	 */
	public function showCheckBoxesBlock( $title, $field_name, $value = array(), $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true
				: false;
		$txt = '';

		if ( empty ( $options['no_title'] ) )
			$txt .= $this->showDataBlock( $options, true, $title, $required );

		$i = 0;
		foreach ( $options['select_values'] AS $key => $val )
		{
			$id = $field_name . '_' . $i;
			$options['checked'] = in_array( $key, $value );
			$options['id'] = $id;

			$txt .= '<label>' . $this->showCheckBoxInput( $field_name, $key, $options ) . $val . '</label>
									';
			$i++;
		}

		if ( empty ( $options['no_title'] ) )
			$txt .= $this->showDataBlock();

		return $txt;
	}

	/** Show a block with radio buttons
	 * @param string $title
	 * @param string $field_name
	 * @param int $value
	 * @param array $options
	 * @return string
	 */
	public function showRadioBlock( $title, $field_name, $value = 0, $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true : false;

		$txt = $this->showDataBlock( $options, true, $title, $required );


		$i = 0;

		foreach ( $options['select_values'] AS $key => $val )
		{
			$id = $field_name . '_' . $i;
			$options['checked'] = $key == $value;
			$options['id'] = $id;

			$txt .= '<label style="font-weight: '. ( $options['checked'] ? 'bold ' : 'normal' ) . '">' . $this->showRadioInput( $field_name, $key, $options ) . $val . '</label>
';
			$i++;
		}

		$txt .= $this->showDataBlock();

		return $txt;
	}

	/** Show a block with textarea
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showTextAreaBlock( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true
				: false;

		if ( !isset ( $options['rows'] ) ) $options['rows'] = 12;
		if ( !isset ( $options['cols'] ) ) $options['cols'] = 120;

		$txt = $this->showDataBlock( $options, true, $title, $required );

		$txt .= $this->showTextAreaInput( $field_name, $value, $options );

		$txt .= $this->showDataBlock();
		return $txt;
	}

	/** Show a block with autocomplete text field
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showAutoCompleteBlock( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== FALSE ? TRUE : FALSE;

		$txt = $this->showDataBlock( $options, true, $title, $required );
		$txt .= $this->showAutoCompleteInput( $field_name, $value, $options );
		$txt .= $this->showDataBlock();

		return $txt;
	}

	/**
	 * Show "Save form" buttons and sends request
	 *
	 * @param array $options_arr - array of settings from SaveFormOptions() function
	 * @return boolean true
	 */

	public function SaveForm( $options_arr )
	{

	if ( !empty ( $options_arr['validate'] ) ): ?>
	<script type="text/javascript">
	<!--
	$j( document ).ready( function()
		{
			$( "#" + g_form_id ).validate();
		} );
	//-->
	</script>

	<? endif; ?>

	<div id="div_submit_form" style="display:none;" class="info">
		__**Данные отправляются.**__<br>
		__**Пожалуйста, подождите**__<br>
		<img src="/icon/ajax_load.gif" width="16" height="16" border="0" title="Loading">Загрузка...
	</div>
	<br>

	<div id="div_submit_success" style="display:none;" class="info">
		__**Данные успешно сохранились**__<br><br>
		<span id="span_success"></span>
		<br>
		<br>
	</div>

	<div id="div_submit_error" style="display:none;" class="warn">
		__**Форма не была сохранена**__!!!<br>
		__**Ошибки указаны ниже**__!<br>
		<br>

		<div id="span_error" class="head"></div>
		<br>
		__**Вы можете попробовать сохранить форму еще раз.**__<br>
		__**Если проблема остается, то обратитесь к Администратору.**__<br>
		<br>
	</div>

	<a name="bottom"></a>
	<div class="head" style="margin-right: 20px;" id="div_buttons_save">
		<br>

		<?

		echo $this->view->showButton( 'Сохранить', 'btn-success', 'icon-ok', array( 'submit' => true ) );

	/*DrawButton(  $options_arr['submit_text'], 'SaveForm();', '/icon/save.gif', FALSE, '', FALSE, '', ( !empty ( $options_arr['button_id'] )
					? $options_arr['button_id'] : 'form_buttons_submit' ), ( !empty ( $options_arr['is_disabled'] ) ? TRUE
					: FALSE ) );*/

		echo '&nbsp;&nbsp;&nbsp;';

		/*if ( !empty ( $options_arr['show_reset'] ) )
			DrawButton(
				'__**Очистить форму**__',
				"if( confirm ( '__**Вы уверены, что хотите очистить форму**__?' ) ) { self.location.href='" . $_SERVER['REQUEST_URI'] . "'; this.disabled = true; } else { };",
				'/icon/comment.gif' );*/

		?>
		<br>
		<br>
	</div>
	<br>
	<div class="head" id="div_back_editing" style="display:none;">
		<br>
		<?
		/*DrawButton(
			'__**Вернуться к редактированию**__',
			"BackForEditing();",
			'/icon/back.gif'
		);*/

	?>
		<br>
		<br>
	</div>

	&nbsp;&nbsp;
	<div align="center">
	<?
		/*if ( !empty ( $options_arr['show_up'] ) )
		{
			DrawButton( '__**Вверх**__', '$j(\'#form_with_select\').stop().scrollTo(0, {duration:800});', '/icon/up.gif' );
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}

		if ( !empty ( $options_arr['show_back'] ) )
		{
			DrawButton( '__**Назад**__', 'history.back();', '/icon/left.gif' );
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}

		if ( !empty ( $options_arr['show_close'] ) )
			DrawButton( '__**Закрыть окно**__', "try{ window.close();} catch(e){}; try{parent.closeAll();} catch(e){}", '/icon/exit1.gif', false, '', false, '', 'button_close_id' );*/

	?>
	</div>
	<br>
	<br>
	<div id="div_form_debug" style="display:none;" class="sow_com"></div>

	<script language="JavaScript">
		<!--

		var g_form_id = '<?= $options_arr['form_id']?>';
		var g_need_confirm = '<?= $options_arr['need_confirm']?>';
		var g_script_before = "<?= ( !empty ( $options_arr['javascript_before'] ) ? $options_arr['javascript_before']
				: '' ) ?>";
		var g_script_success = "<?= ( !empty ( $options_arr['javascript_success'] ) ? $options_arr['javascript_success']
				: '' ) ?>";
		var g_script_error = "<?= ( !empty ( $options_arr['javascript_error'] ) ? $options_arr['javascript_error']
				: '' ) ?>";
		var g_module = '<?= $options_arr['module']?>';
		var g_action = '<?= $options_arr['action']?>';
		var g_upload_exist = '<?= $options_arr['upload_exist']?>';

		//-->
	</script>
		<? Func::assetLink( "js", "function_save_form", 'functions' ); ?>

	<?
		return true;
	}

	public /**
	 * Sets default settings for a form
	 * @param string $form_id - form_id
	 * @param string $module - module name
	 * @param string $action - name of the action
	 * @param array $options_arr  - options (non-mandatory)
	 * @return array - new array of options
	 */
	function SaveFormOptions( $form_id, $module, $action, $options_arr = array( ) )
	{
		$handle = array
		(
			'form_id' => $form_id,
			'module' => $module,
			'action' => $action,
			'button_id'     => ( isset( $options_arr['button_id'] ) ? $options_arr['button_id'] : 'form_buttons_submit' ),
			'submit_text'   => ( isset( $options_arr['submit_text'] ) ? $options_arr['submit_text'] : '__**Сохранить форму**__' ),
			'is_disabled'   => ( isset( $options_arr['is_disabled'] ) ? $options_arr['is_disabled'] : false ),
			'show_close'    => ( isset( $options_arr['show_close'] ) ? $options_arr['show_close'] : true ),
			'show_up'       => ( isset( $options_arr['show_up'] ) ? $options_arr['show_up'] : true ),
			'show_reset'    => ( isset( $options_arr['show_reset'] ) ? $options_arr['show_reset'] : true ),
			'show_back'     => ( isset( $options_arr['show_back'] ) ? $options_arr['show_back'] : false ),
			'need_confirm'  => ( isset( $options_arr['need_confirm'] ) ? $options_arr['need_confirm'] : true ),
			'upload_exist'          => ( isset( $options_arr['upload_exist'] ) ? $options_arr['upload_exist'] : false ),
			'javascript_before'     => ( isset( $options_arr['javascript_before'] ) ? $options_arr['javascript_before'] : '' ),
			'javascript_success'    => ( isset( $options_arr['javascript_success'] ) ? $options_arr['javascript_success'] : '' ),
			'javascript_error'      => ( isset( $options_arr['javascript_error'] ) ? $options_arr['javascript_error'] : '' ),
			'validate' => ( isset( $options_arr['validate'] ) ? $options_arr['validate'] : true ),
		);
		return $handle;
	}

}
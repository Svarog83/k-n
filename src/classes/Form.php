<?php


namespace SDClasses;
/**
 * A helper class with functions for form generating
 */
class Form extends FormBasic
{
	private $method = 'POST';
	/**
	 * @var string
	 */
	private $_req_fill = '';

	/**
	 * @var string
	 */
	private $form_id = '';
	/**
	 * @var string
	 */
	private $module = '';
	/**
	 * @var string
	 */
	private $action = '';
	/**
	 * @var string
	 */
	private $button_id = 'form_btn_submit_id';
	/**
	 * @var string
	 */
	private $submit_text = 'Save';
	/**
	 * @var bool
	 */
	private $is_disabled = false;
	/**
	 * @var bool
	 */
	private $show_close = true;
	/**
	 * @var bool
	 */
	private $show_up = true;
	/**
	 * @var bool
	 */
	private $show_reset = true;
	/**
	 * @var bool
	 */
	private $show_back = false;
	/**
	 * @var bool
	 */
	private $need_confirm = true;
	/**
	 * @var bool
	 */
	private $upload_exist = false;
	/**
	 * @var string
	 */
	private $js_before = '';
	/**
	 * @var string
	 */
	private $js_success = '';
	/**
	 * @var string
	 */
	private $js_error = '';
	/**
	 * @var string
	 */
	private $validate = true;

	/** Constructor
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
		$this->_set( $options );
		$this->_req_fill = '&nbsp;<span style="color:red;" title="Обязательное поле">*</span>';
	}

	protected function _set( $properties = array() )
	{
		if ( is_array( $properties ) && !empty( $properties ) )
		{
			$vars = get_object_vars( $this );
			foreach ( $properties as $key => $val )
			{
				if ( array_key_exists( $key, $vars ) )
				{
					$this->{$key} = $val;
				}
			}
		}
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

		$txt .= $this->showDataBlock( $options, true, $title, $required );

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
			'size' => isset ( $options['size'] ) ? $options['size'] : ( !empty ( $options['mupltiple'] ) ? '1' : '' ),
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

			$txt .= '<label style="font-weight: ' . ( $options['checked'] ? 'bold ' : 'normal' ) . '">' . $this->showRadioInput( $field_name, $key, $options ) . $val . '</label>
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
	 * @param \SDClasses\View $view
	 * @param array $arr
	 * @return boolean true
	 */

	public function showForm( $view, $arr )
	{
		?>
		<div class="widget-content form-horizontal">
			<form action="/<?= $this->module ?>/<?= $this->action ?>" method="<?= $this->method ?>"
			      class="form-horizontal">

				<?
				/**
				 * @param \SDClasses\FormElement $element
				 */
				foreach ( $arr AS $element )
				{
					$element->process( $this );
				}
				?>

			</form>
		</div>
		<?

		if ( $this->validate ): ?>
			<script type="text/javascript">
				<!--
				$( document ).ready( function ()
				{
					$( "#" + g_form_id ).validate();
				} );
				//-->
			</script>

		<? endif; ?>

		<div id="div_submit_form" style="display:none;" class="alert alert-info">
			__**Данные отправляются.**__<br>
			__**Пожалуйста, подождите**__<br>
			<img src="/img/select2-spinner.gif" width="16" height="16" border="0" title="Loading">Загрузка...
		</div>
		<br>

		<div id="div_submit_success" style="display:none;" class="alert alert-success">
			__**Данные успешно сохранились**__<br><br>
			<span id="span_success"></span>
			<br>
			<br>
		</div>

		<div id="div_submit_error" style="display:none;" class="alert alert-error">
			__**Форма не была сохранена**__!!!<br>
			__**Ошибки указаны ниже**__!<br>
			<br>

			<div id="span_error" class="alert alert-info"></div>
			<br>
			__**Вы можете попробовать сохранить форму еще раз.**__<br>
			__**Если проблема остается, то обратитесь к Администратору.**__<br>
			<br>
		</div>

		<a name="bottom"></a>
		<div class="form-actions" style="margin-right: 20px;" id="div_buttons_save">
			<?
			echo $view->showButton( $this->submit_text, 'btn-success', 'icon-ok',
				array(
					'id' => $this->button_id,
					'onclick' => 'alert(111);SaveForm();',
					'disabled' => $this->is_disabled
				)
			);

			echo '&nbsp;&nbsp;&nbsp;';

			if ( $this->show_reset )
			{
				echo $view->showButton( 'Reset form', 'btn-primary', 'icon-refresh',
					array(
						'onclick' => "if( confirm ( 'Are you sure?' ) ) { self.location.href='" . $_SERVER['REQUEST_URI'] . "'; this.disabled = true; } else { };",
						'disabled' => false,
						'title' => 'Start from scratch'
					)
				);
			}
			?>
			<br>
			<br>
		</div>
		<br>
		<div class="head" id="div_back_editing" style="display:none;">
			<br>
			<?
			echo $view->showButton( 'Back to form', 'btn-primary', 'icon-edit',
				array(
					'onclick' => "BackForEditing();",
					'disabled' => false
				)
			);
			?>
			<br>
			<br>
		</div>

		&nbsp;&nbsp;
		<div style="text-align: center;">
			<?

			if ( $this->show_up )
			{
				echo $view->showButton( 'Up', 'btn-info', 'icon-arrow-up',
					array(
						'onclick' => '$(window).stop().scrollTo(0, {duration:800});',
						'disabled' => false
					)
				);
			}

			if ( $this->show_back )
			{
				echo $view->showButton( 'Back', 'btn-primary', 'icon-arrow-left',
					array(
						'onclick' => 'history.back();',
						'disabled' => false
					)
				);
			}

			if ( $this->show_close )
			{
				echo $view->showButton( 'Close window', 'btn-danger', 'icon-remove',
					array(
						'onclick' => "try{ window.close();} catch(e){}; try{parent.closeAll();} catch(e){}",
						'disabled' => false,
						'id' => 'button_close_id'
					)
				);
			}

			?>
		</div>
		<br>
		<br>
		<div id="div_form_debug" style="display:none;" class="sow_com"></div>

		<script language="JavaScript">
			<!--

			var g_form_id = '<?= $this->form_id?>';
			var g_need_confirm = '<?= $this->need_confirm?>';
			var g_script_before = "<?= $this->js_before ?>";
			var g_script_success = "<?= $this->js_success ?>";
			var g_script_error = "<?= $this->js_error ?>";
			var g_module = '<?= $this->module?>';
			var g_action = '<?= $this->action?>';
			var g_upload_exist = '<?= $this->upload_exist  ?>';

			//-->
		</script>
		<? Func::assetLink( "js", "function_save_form", 'functions' ); ?>

		<?
		return true;
	}

	/** Show an empty block
	 * @param string $title
	 * @param array $options
	 * @return string
	 */
	public function showEmptyBlock( $title, $options = array() )
	{
		$txt = $this->showDataBlock( $options, true, $title );

		$txt .= '&nbsp;';

		$txt .= $this->showDataBlock();
		return $txt;
	}

}
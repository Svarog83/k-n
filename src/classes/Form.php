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
	private $options = array();

	/** Constructor
	 * @param array $options
	 */
	public function __construct( $options = array() )
	{
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
		$split_lines = true;
		if ( count( $options['select_values'] ) < 3 )
			$split_lines = false;

		foreach ( $options['select_values'] AS $key => $val )
		{
			$id = $field_name . '_' . $i;
			$options['checked'] = $key == $value;
			$options['id'] = $id;

			$txt .= '<label style="font-weight: '. ( $options['checked'] ? 'bold ' : 'normal' ) . '">' . $this->showRadioInput( $field_name, $key, $options ) . $val . '</label>
';

			//$txt .= $split_lines ? '<br>' : '&nbsp;&nbsp;&nbsp;';
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
}
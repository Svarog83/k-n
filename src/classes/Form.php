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
	function showBlock( $options = array(), $start = false, $title = '', $required = false )
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

	/** Shows tr with empty breaks
	 * @return string
	 */
	function insertEmptyRow()
	{
		echo '<tr><td><br><br></td><td></td></tr>';

	}


	/** Shows tr with a text without fields
	 * @param string $title
	 * @param string $text
	 * @param string $field_name
	 * @param string $value
	 * @internal param array $options
	 * @return string
	 *
	 */
	function insertTextRow( $title, $text, $field_name = '', $value = '' )
	{
		$this->showBlock( array(), true, $title );

		echo '<b>' . $text . '</b>' . ( $field_name ? '<input type="hidden" name="' . $field_name . '" value="' . $value . '"/>' : '' );

		$this->showBlock();

	}

	/** Shows tr with a text field
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showTextInput( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true : false;

		if ( !isset ( $options['size'] ) )
			$options['size'] = 50;

		$txt = $this->showBlock( $options, true, $title, $required );

		$txt .= $this->showText( $field_name, $value, $options );

		$txt .= $this->showBlock();

		return $txt;
	}

	/** Shows tr with a date field
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showDateInput( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true
				: false;

		ob_start();

		$this->showBlock( $options, true, $title, $required );

		echo $this->showDate( $field_name, $value, $options );

		$this->showBlock();

		$txt = ob_get_contents();
		ob_end_clean();
		return $txt;
	}

	/** Shows tr with select
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showSelectInput( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== FALSE ? TRUE : FALSE;

		$arr_tmp = array();

		ob_start();

		// проверяем на наличие дополнительных условий в массиве данных

		if ( is_array( $options['select_values'] ) )
		{
			foreach ( $options['select_values'] as $k => $v )
				if ( is_array( $v ) )
				{
					$arr_tmp['select_values'] = $options['select_values']['select_values'];
					$arr_tmp['block_values'] = $options['select_values']['block_values'];


					break;

				}
				else
				{
					$arr_tmp['select_values'] = $options['select_values'];
					break;
				}


		}

		unset( $options['select_values'] );

		$arr = array( 'type' => isset ( $options['select_type'] ) ? $options['select_type'] : 'select',
			'field_name' => $field_name,
			'id' => isset ( $options['id'] ) ? $options['id'] : '',
			'onchange' => 'choice_simple(\'\', \'\', \'\', this);',
			'show_select_title' => isset ( $options['show_select_title'] ) ? $options['show_select_title'] : 0,
			'select_values' => isset ( $arr_tmp['select_values'] ) ? $arr_tmp['select_values'] : array(),
			'block_values' => isset ( $arr_tmp['block_values'] ) ? $arr_tmp['block_values'] : array(),
			'selected_value' => $value,
			'add_str' => ( isset ( $options['add_str'] ) ? $options['add_str'] : '' )
		);

		if ( !isset ( $arr['add_str'] ) )
			$arr['add_str'] = '';

		if ( $options['validation'] )
			$arr['add_str'] .= ' class="' . $options['validation'] . '"';

		$this->showBlock( $options, true, $title, $required );

		echo $this->showSelect( $arr );

		$this->showBlock();

		$txt = ob_get_contents();
		ob_end_clean();
		return $txt;
	}

	/** Shows tr with checkboxes
	 * @param string $title
	 * @param string $field_name
	 * @param array $value
	 * @param array $options
	 * @return string
	 */
	public function showCheckBoxesInput( $title, $field_name, $value = array(), $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true
				: false;

		ob_start();

		if ( !isset ( $options['no_title'] ) )
			$this->showBlock( $options, true, $title, $required );

		$i = 0;
		foreach ( $options['select_values'] AS $key => $val )
		{
			$id = $field_name . '_' . $i;
			$options['checked'] = in_array( $key, $value );
			$options['id'] = $id;

			echo $this->showCheckBox( $field_name, $key, $options );
			?>
			<label for="<?php echo $id ?>"><?= $val ?></label>
			<br>
			<?
			$i++;
		}

		if ( !isset ( $options['no_title'] ) )
			$this->showBlock();

		$txt = ob_get_contents();
		ob_end_clean();
		return $txt;
	}

	/** Shows tr with radio buttons
	 * @param string $title
	 * @param string $field_name
	 * @param int $value
	 * @param array $options
	 * @return string
	 */
	public function showRadioButtonInput( $title, $field_name, $value = 0, $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true : false;
		ob_start();
		$this->showBlock( $options, true, $title, $required );


		$i = 0;
		$split_lines = true;
		if ( count( $options['select_values'] ) < 3 )
			$split_lines = false;

		foreach ( $options['select_values'] AS $key => $val )
		{
			$id = $field_name . '_' . $i;
			$options['checked'] = $key == $value;
			$options['id'] = $id;

			echo $this->showRadioButton( $field_name, $key, $options );
			?>
			<label style="vertical-align: middle; font-weight: <?= $options['checked'] ? 'bold ' : 'normal' ?>"
			       for="<?php echo $id ?>"><?= $val ?></label>
			<?
			echo $split_lines ? '<br>' : '&nbsp;&nbsp;&nbsp;';
			$i++;
		}

		$this->showBlock();

		$txt = ob_get_contents();
		ob_end_clean();
		return $txt;
	}

	/** Shows tr with textarea
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showTextArea( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== false ? true
				: false;

		if ( !isset ( $options['rows'] ) ) $options['rows'] = 12;
		if ( !isset ( $options['cols'] ) ) $options['cols'] = 120;

		ob_start();

		$this->showBlock( $options, true, $title, $required );

		echo $this->showTextArea( $field_name, $value, $options );

		$this->showBlock();
		$txt = ob_get_contents();
		ob_end_clean();
		return $txt;
	}

	/** Shows tr with autocomplete text field
	 * @param string $title
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showBlockAutoComplete( $title, $field_name, $value = '', $options = array() )
	{
		$required = isset ( $options['validation'] ) && strpos( $options['validation'], 'required' ) !== FALSE ? TRUE : FALSE;

		ob_start();

		$this->showBlock( $options, true, $title, $required );

		echo $this->showTextAutoComplete( $field_name, $value, $options );

		$this->showBlock();

		$txt = ob_get_contents();
		ob_end_clean();
		return $txt;
	}
}
<?php

namespace SDClasses;
/**
 * Basic form elements
 */
class FormBasic
{
	/**
	 * @var array
	 */
	private $_options = array();
	/**
	 * @var array
	 */
	private $_auto_complete_names = array();

	/** Constructor
	 * @param array $options
	 */

	public function __construct( $options = array() )
	{
		$this->_options = $options;
		return true;
	}

	/**
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr options array
	 * @return string
	 */
	public function showTextInput( $field_name, $value, $arr = array() )
	{
		/*if ( !empty ( $arr['size'] ) && empty ( $arr['add_str'] ) )
			$arr['add_str'] = 'style="width: ' . $arr['size'] . 'px;"';*/

		return '<input
            type="' . ( !empty( $arr['password'] ) ? 'password' : 'text' ) . '"
            name="' . $field_name . '"
            size="' . ( !empty( $arr['size'] ) ? $arr['size'] : '16' ) . '"
            value="' . $value . '"
            ' . ( !empty( $arr['onchange'] ) ? ' onChange="' . $arr['onchange'] . '"' : '' ) .
		( !empty( $arr['id'] ) ? ' id="' . $arr['id'] . '"' : '' ) .
		( !empty( $arr['validation'] ) ? ' class="' . $arr['validation'] . '"' : '' ) .
		( !empty ( $arr['equalTo'] ) ? ' equalTo="' . $arr['equalTo'] . '"' : '' ) .
		( !empty ( $arr['minlength'] ) ? ' minlength="' . $arr['minlength'] . '"' : '' ) .
		( !empty ( $arr['maxlength'] ) ? ' maxlength="' . $arr['maxlength'] . '"' : '' ) .
		( !empty ( $arr['min'] ) ? ' min="' . $arr['min'] . '"' : '' ) .
		( !empty ( $arr['max'] ) ? ' max="' . $arr['max'] . '"' : '' ) .
		( !empty ( $arr['onfocus'] ) ? ' onFocus="' . $arr['onfocus'] . '"' : '' ) .
		( !empty ( $arr['placeholder'] ) ? ' placeholder="' . $arr['placeholder'] . '"' : '' ) .
		( !empty ( $arr['onblur'] ) ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
		( !empty ( $arr['add_str'] ) ? $arr['add_str'] : '' ) . '>' .
		( !empty ( $arr['help_block'] ) ? '<span class="help-block">' . $arr['help_block'] . '</span>' : '' ) .
		( !empty ( $arr['add_html'] ) ? $arr['add_html'] : '' ) . '';
	}

	/**
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr options array
	 * @return string
	 */
	public function showTextAreaInput( $field_name, $value, $arr = array() )
	{
		return '<textarea
            name="' . $field_name . '"
            rows="' . ( $arr['rows'] ? $arr['rows'] : '5' ) . '"
            cols="' . ( $arr['cols'] ? $arr['cols'] : '30' ) . '"
            ' .
		( !empty ( $arr['id'] ) ? ' id="' . $arr['id'] . '"' : '' ) .
		( !empty ( $arr['validation'] ) ? ' class="' . $arr['validation'] . '"' : '' ) .
		( !empty ( $arr['minlength'] ) ? ' minlength="' . $arr['minlength'] . '"' : '' ) .
		( !empty ( $arr['onchange'] ) ? ' onChange="' . $arr['onchange'] . '"' : '' ) .
		( !empty ( $arr['onfocus'] ) ? ' onFocus="' . $arr['onfocus'] . '"' : '' ) .
		( !empty ( $arr['placeholder'] ) ? ' placeholder="' . $arr['placeholder'] . '"' : '' ) .
		( !empty ( $arr['onblur'] ) ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
		( !empty ( $arr['add_str'] ) ? $arr['add_str'] : '' ) . '>' . $value . '</textarea>' .
		( !empty ( $arr['help_block'] ) ? '<span class="help-block">' . $arr['help_block'] . '</span>' : '' ) .
		( !empty ( $arr['add_html'] ) ? $arr['add_html'] : '' ) . '';
	}

	/**
	 * @param array $arr options array
	 * @return string
	 */
	public function showSelectInput( $arr )
	{
		$txt = '<select name="' . ( $arr['field_name'] ) . '" ' .
				( !empty ( $arr['show_select_title'] ) ? ' data-placeholder="' . $arr['show_select_title'] . '"' : '' ) .
				( !empty ( $arr['id'] ) ? ' id="' . $arr['id'] . '"' : '' ) .
				( !empty ( $arr['multiple'] ) ? ' multiple size="' . $arr['size'] . '"' : '' ) .
				( !empty ( $arr['onchange'] ) ? ' onChange="' . $arr['onchange'] . '"' : '' ) .
				( !empty ( $arr['onfocus'] ) ? ' onFocus="' . $arr['onfocus'] . '"' : '' ) .
				( !empty ( $arr['onblur'] ) ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
				( !empty ( $arr['validation'] ) ? ' class="' . $arr['validation'] . '"' : '' ) .
				( !empty ( $arr['add_str'] ) ? $arr['add_str'] : '' ) . '>';

		if ( !empty ( $arr['show_select_title'] ) )
			$txt .= '<option></option>';

		foreach ( $arr['select_values'] as $key => $val )
			$txt .= '<option value="' .
					( ( isset( $arr['block_values'] ) && !in_array( $key, $arr['block_values'] ) || !isset( $arr['block_values'] ) ) ?
							$key . '"' :
							'empty"'
					) .
					(
					( ( isset( $arr['selected_value'] ) && $arr['selected_value'] !== '' && $key == $arr['selected_value'] ) ||
							( count( $arr['select_values'] ) == 1 && !empty( $arr['set_only_one'] ) )
					) ?
							' selected' :
							''
					) .
					'>' . ( $val ) . '';

		$txt .= '</select>';

		return $txt;
	}

	/**
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr options array
	 * @return string
	 */
	public function showCheckBoxInput( $field_name, $value, $arr )
	{
		//$field_name = str_replace( '[]', '[0]', $field_name );
		return '
            <input type="checkbox" name="' . $field_name . '" value="' . ( !empty ( $value ) ? $value : 1 ) . '" ' .
		( !empty ( $arr['id'] ) ? ' id="' . $arr['id'] . '"' : '' ) .
		( !empty ( $arr['validation'] ) ? ' class="' . $arr['validation'] . '"' : '' ) .
		( !empty ( $arr['checked'] ) ? ' checked="checked"' : '' ) .
		( !empty ( $arr['onchange'] ) ? ' onChange="' . $arr['onchange'] . '"' : '' ) .
		( !empty ( $arr['onfocus'] ) ? ' onFocus="' . $arr['onfocus'] . '"' : '' ) .
		( !empty ( $arr['onblur'] ) ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
		( !empty ( $arr['add_str'] ) ? $arr['add_str'] : '' ) . '>' .
		( !empty ( $arr['add_html'] ) ? $arr['add_html'] : '' ) . '';
	}

	/**
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr options array
	 * @return string
	 */
	public function showRadioInput( $field_name, $value, $arr )
	{
		return '
            <input type="radio" name="' . $field_name . '" value="' . $value . '" ' .
		( !empty ( $arr['id'] ) ? ' id="' . $arr['id'] . '"' : '' ) .
		( !empty ( $arr['validation'] ) ? ' class="' . $arr['validation'] . '"' : '' ) .
		( !empty ( $arr['checked'] ) ? ' checked="checked"' : '' ) .
		( !empty ( $arr['onchange'] ) ? ' onChange="' . $arr['onchange'] . '"' : '' ) .
		( !empty ( $arr['onfocus'] ) ? ' onFocus="' . $arr['onfocus'] . '"' : '' ) .
		( !empty ( $arr['onblur'] ) ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
		( !empty ( $arr['add_str'] ) ? $arr['add_str'] : '' ) . '>' .
		( !empty ( $arr['add_html'] ) ? $arr['add_html'] : '' ) . '';
	}

	/**
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr options array
	 * @return string
	 */

	public function showHiddenInput( $field_name, $value, $arr )
	{
		return '<input type="hidden" name="' . $field_name . '" value="' .
		$value . '" ' .
		( !empty ( $arr['id'] ) ? ' id="' . $arr['id'] . '"' : '' ) .
		( !empty ( $arr['onchange'] ) ? ' onChange="' . $arr['onchange'] . '"' : '' ) .
		( !empty ( $arr['onfocus'] ) ? ' onFocus="' . $arr['onfocus'] . '"' : '' ) .
		( !empty ( $arr['onblur'] ) ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
		( !empty ( $arr['add_str'] ) ? $arr['add_str'] : '' ) . '>' .
		( !empty ( $arr['add_html'] ) ? $arr['add_html'] : '' ) . '';
	}

	/**
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr options array
	 * @return string
	 */
	public function showDateInput( $field_name, $value, $arr = array() )
	{
		return '<span><input
            type="text"
            name="' . $field_name . '"
            readonly
            class="datepicker' . ( isset( $arr['currtime'] ) ? ' currtime' : '' ) . ( !empty ( $arr['validation'] ) ? ' ' . $arr['validation'] : '' ) . '"
            size="' . ( !empty( $arr['size'] ) ? $arr['size'] : '10' ) . '"
            value="' . ( $value != '0000-00-00' ? Func::formatDate( $value, ( !empty ( $arr['format'] ) ? $arr['format'] : 'yy-mm-dd' ) ) : '' ) . '"
            ' . ( !empty( $arr['onchange'] ) ? ' onChange="' . $arr['onchange'] . '"' : '' ) .
		( !empty ( $arr['id'] ) ? ' id="' . $arr['id'] . '"' : '' ) .
		( !empty ( $arr['equalTo'] ) ? ' equalTo="' . $arr['equalTo'] . '"' : '' ) .
		( !empty ( $arr['minlength'] ) ? ' minlength="' . $arr['minlength'] . '"' : '' ) .
		( !empty ( $arr['onfocus'] ) ? ' onFocus="' . $arr['onfocus'] . '"' : '' ) .
		( !empty ( $arr['onblur'] ) ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
		( !empty ( $arr['add_str'] ) ? $arr['add_str'] : '' ) . '>' .
		( !empty ( $arr['add_html'] ) ? $arr['add_html'] : '' ) . ( empty( $arr['noclean'] ) ?
				'&nbsp;<img style="cursor:pointer;" border="0" src="/icon/calendar_clean.gif"  width="24" height="20" alt="__**Очистить**__" onClick="clearDate(this);">'
				: '' ) .
		'</span>
		<script>$j( ".datepicker").datepicker({ dateFormat: "' .
		( !empty ( $arr['format'] ) ? $arr['format'] : 'yy-mm-dd' ) .
		'", changeMonth: true, changeYear: true, showOn: "button", buttonImage: "/icon/calendar.gif", buttonImageOnly: true });</script>';
	}

	/** Shows a text field with autocomplete
	 * @param string $field_name
	 * @param string $value
	 * @param array $options
	 * @return string
	 */
	public function showAutoCompleteInput( $field_name, $value = '', $options = array() )
	{
		static $included = 0;

		$included++;

		ob_start();

		$ac_name = ( isset ( $options['ac_name'] ) && $options['ac_name'] ? $options['ac_name'] : 'rems_auto' ) . '_';

		if ( !isset ( $options['id'] ) || !$options['id'] )
			$options['id'] = $ac_name . $included;

		if ( !isset ( $options['ac_name'] ) )
			$options['ac_name'] = $options['id'];

		if ( !isset ( $options['title'] ) || !$options['title'] )
			$options['title'] = '';

		if ( empty ( $options['settings']['onselect'] ) )
			$options['settings']['onselect'] = '';

		?>

		<script language="JavaScript">
			<!--
			$j( function ()
			{
				var obj_settings = {
					title_width: '<?= isset ( $options['settings']['total_width'] ) ? $options['settings']['total_width']
						: '390' ?>',
					minLength: '<?= isset ( $options['settings']['minlength'] ) ? $options['settings']['minlength']
						: '1' ?>',
					maxLength: '<?= isset ( $options['settings']['maxLength'] ) ? $options['settings']['maxLength']
						: '45' ?>',
					ac_options: <?= isset ( $options['settings'] ) ? Func::php2js( $options['settings'] ) : 'Object()' ?>,
					form_id: '<?= isset ( $options['settings']['form_id'] ) ? $options['settings']['form_id']
						: 'form_id' ?>'
				};
				gCloneObjSettings['<?= $options['id'] ?>'] = obj_settings;

				$j( "#<?= $options['id']?>" ).autoComplete( obj_settings );
				gAC_ID = <?= $included ?>;

				<? if ( isset ( $options['settings']['clone_obj'] ) && $options['settings']['clone_obj'] && isset ( $options['settings']['table_name_html'] ) ): ?>
				if ( gCloneObj['<?= $options['settings']['table_name_html']?>'] == undefined )
					gCloneObj['<?= $options['settings']['table_name_html']?>'] = new Array();
				<? if ( !in_array( $ac_name, $this->_auto_complete_names ) ): ?>
				gCloneObj['<?= $options['settings']['table_name_html']?>'].push( '<?= $options['id']?>' );
				<? endif; ?>
				<? endif; ?>
			} );
			//-->
		</script>
		<input type="text" <?php echo isset ( $options['id'] ) ? 'id="' . $options['id'] . '"' : '' ?>
		       name="<?php echo $field_name ?>"
		       style="background-color:#BFFEB4;color:#000053"
		       class="ac_input <?php echo !empty( $options['settings']['insert_value'] ) ? 'ui-autocomplete-selected' : '' ?>"
		       title="<?php echo $options['title'] ?>"
		       size="<?php echo isset ( $options['size'] ) && $options['size'] ? $options['size'] : 50 ?>"
		       value="<?php echo $value ? $value : '' ?>">

		<? /* it's important to keep the current structure. Otherwise see jquery_clone.js REF1*/ ?>
		<? if ( !empty( $options['title'] ) )
		{
		?>
		<script language="JavaScript">
			<!--
			$j( "#<?= $options['id']?>" ).example( '<?=$options['title']?>', { className: 'hint' } );
			//-->
		</script>
	<? } ?>

		<input type="hidden" class="ac_hidden_input" name="<?php echo $options['settings']['insert_field'] ?>"
		       id="hid_for_<?php echo $options['id'] ?>"
		       value="<?php echo !empty( $options['settings']['insert_value'] ) ? $options['settings']['insert_value'] : '' ?>"
		       onchange='<?= !empty( $options['onchange'] ) ? $options['onchange'] : '' ?>'>

		<? if ( isset ( $options['settings']['show_new'] ) && $options['settings']['show_new'] ): ?>
		<img src="/icon/new.gif" width="16" height="16" border="0" title="__**Add new**__" style="cursor:pointer;"
		     OnClick="showAjaxModWin(this, '<?= $options['settings']['src'] ?>','', '', '500' )">
	<? endif; ?>

		<? if ( isset ( $options['settings']['show_clean'] ) && $options['settings']['show_clean'] ): ?>
		<img src="/icon/edit_empty.gif" width="16" height="16" border="0" title="__**Clean field**__"
		     alt="__**Clean**__"
		     style="cursor:pointer;" OnClick="ACFieldClear(this);">
	<? endif; ?>

		<?
		$this->_auto_complete_names[] = $ac_name;

		$txt = ob_get_contents();
		ob_end_clean();
		return $txt;
	}

	/**
	 * @param string $title - title of the field
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr options array
	 * @return string
	 */
	public function showInline( $title, $field_name, $value, $arr = array() )
	{

		$ac_name = 'ac_' . str_replace( array( '[', ']' ), '', $field_name );
		return $this->showAutoCompleteInput( $ac_name, $arr['add_name'],
			array(
				'size' => ( !empty ( $arr['size'] ) ? $arr['size'] : 20 ),
				'maxLength' => ( !empty ( $arr['maxlength'] ) ? $arr['maxLength']
						: 30 ),
				'ac_name' => $ac_name,
				'title' => $title,
				'onchange' => !empty( $arr['onchange'] ) ? $arr['onchange'] : '',
				'settings' => array(
					'table_name' => $arr['table_name'],
					'table_name_html' => $arr['table_name_html'],
					'minlength' => $arr['minlength'] ? $arr['minlength'] : '2',
					'search_field' => $arr['search_field'],
					'addition_search_fields' => !empty( $arr['addition_search_fields'] )
							? $arr['addition_search_fields'] : '',
					'field_activ' => !empty( $arr['field_activ'] ) ? $arr['field_activ']
							: '',
					'total_width' => !empty( $arr['total_width'] ) ? $arr['total_width']
							: '590',
					'fields' => $arr['fields'],
					'add_fields' => $arr['add_fields'],
					'titles' => $arr['titles'],
					'widths' => $arr['widths'] ? $arr['widths']
							: array( '170', '150', '250' ),
					'add_str' => !empty( $arr['add_str'] ) ? $arr['add_str'] : "",
					'record_id' => $arr['record_id'],
					'form_id' => $arr['form_id'] ? $arr['form_id'] : '',
					'insert_field' => $arr['insert_field'],
					'insert_value' => $value,
					'clone_obj' => $arr['clone_obj'],
					'show_clean' => true,
					'show_new' => $arr['show_new'],
					'src' => $arr['src']
				)
			)
		);
	}
}

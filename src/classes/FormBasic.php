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
	 * @param array $arr - _options
	 * @return string
	 */
	public function showText( $field_name, $value, $arr = array() )
	{
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
	 * @param array $arr - _options
	 * @return string
	 */
	public function showTextArea( $field_name, $value, $arr = array() )
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
		( !empty ( $arr['onblur'] ) ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
		( !empty ( $arr['add_str'] ) ? $arr['add_str'] : '' ) . '>' . $value . '</textarea>' .
		( !empty ( $arr['add_html'] ) ? $arr['add_html'] : '' ) . '';
	}

	/**
	 * @param array $arr - _options
	 * @return string
	 */
	public function showSelect( $arr )
	{
		/* we need to replace number of paramtered for choice_simple function. If there is only one then we need to transform it into 4. */
		if (
				!empty( $arr['onchange'] ) &&
				strpos( $arr['onchange'], 'choice_simple' ) !== false &&
				preg_match( "/choice_simple *\( *'([^']+)' *\)/i", $arr['onchange'] )
		)
			$arr['onchange'] = preg_replace( "/choice_simple *\( *'([^']+)' *\)/i", "choice_simple('$1', '', '', this)", $arr['onchange'] );

		return PrepareSelect( $arr );
	}

	/**
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr - _options
	 * @return string
	 */
	public function showCheckBox( $field_name, $value, $arr )
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
	 * @param array $arr - _options
	 * @return string
	 */
	public function showRadioButton( $field_name, $value, $arr )
	{
		return '
            <input style="vertical-align: middle;" type="radio" name="' . $field_name . '" value="' . $value . '" ' .
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
	 * @param array $arr - _options
	 * @return string
	 */
	public function showModal( $arr )
	{
		return '<div><span id=' . $arr['div_id'] . ' style="font-weight:bold;">' . $arr['add_name'] . '&nbsp;&nbsp;</span>
					&nbsp;
					<img src="/icon/edit.gif" width="16" height="16" border=0 alt="List" style="cursor: pointer;" OnClick="modal_select(this, \'' . $arr['field'] . '\', \'' . $arr['div_id'] . '\', \'' . $arr['src'] . '\', \'' . $arr['parent_query'] . '\' )">
                    <img src="/icon/edit_empty.gif" width="16" height="16" border="0" alt="Clean" style="cursor:pointer;"  OnClick="modalFieldClear(this, \'' . $arr['field'] . '\')">
					</div>
					<input id="' . $arr['field'] . '" type="hidden" name="' . $arr['field_name'] . '" value="' . $arr['selected_value'] . '" onchange="' . $arr['onchange'] . '">';
	}

	/**
	 * @param array $arr - _options
	 * @return string
	 */
	public function showDiv( $arr )
	{
		return '<div><span id=' . $arr['div_id'] . ' style="font-weight:bold;">' . $arr['add_name'] . '&nbsp;&nbsp;</span>
					&nbsp;
					<img src="/icon/edit.gif" width="16" height="16" border=0 alt="List" onclick="showAjaxModWin(this, \'' . $arr['src'] . '?' . $arr['parent_query'] . '\', \'' . $title . '\', ' . ( $arr['win_width']
				? $arr['win_width'] : '550' ) . ', ' . ( $arr['win_height'] ? $arr['win_height'] : '450' ) . ');" style="cursor:pointer;">
					<img src="/icon/edit_empty.gif" width="16" height="16" border="0" alt="Clean" style="cursor:pointer;"  OnClick="modalFieldClear(this, \'' . $arr['field'] . '\');">
					</div>
					<input id="' . $arr['field'] . '" type="hidden" name="' . $arr['field_name'] . '" value="' . $arr['selected_value'] . '">';
	}

	/**
	 * @param array $arr - _options
	 * @return string
	 */
	public function showNewWindow( $arr )
	{
		return '<div><span id="' . $arr['div_id'] . '" style="font-weight:bold;">' . $arr['add_name'] . '&nbsp;&nbsp;</span>
					&nbsp;
					<img src="/icon/edit.gif" width="16" height="16" border=0 alt="List" style="cursor: pointer;" OnClick="self.focus(); save_window(\'\',\'' . $arr['action'] . '\');">
                    <img src="/icon/edit_empty.gif" width="16" height="16" border="0" alt="Clean" style="cursor: pointer;" OnClick="document.getElementById(\'' . $arr['div_id'] . '\').innerHTML = \'' . $arr['add_name'] . '&nbsp;&nbsp;\'; document.getElementById(\'' . $arr['field'] . '\').value = \'\';self.focus();">
					</div>
					<input id="' . $arr['field'] . '" type="hidden" name="' . $arr['field_name'] . '" value="' . $arr['selected_value'] . '">';
	}

	/**
	 * @param string $field_name - name of the field
	 * @param string $value - default value for the field
	 * @param array $arr - _options
	 * @return string
	 */
	public function showHidden( $field_name, $value, $arr )
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
	 * @param array $arr - _options
	 * @return string
	 */
	public function showDate( $field_name, $value, $arr = array() )
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
	public function showTextAutoComplete( $field_name, $value = '', $options = array() )
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
					ac_options: <?= isset ( $options['settings'] ) ? php2js( $options['settings'] ) : 'Object()' ?>,
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
	 * @param array $arr - _options
	 * @return string
	 */
	public function showInline( $title, $field_name, $value, $arr = array() )
	{

		$ac_name = 'ac_' . str_replace( array( '[', ']' ), '', $field_name );
		return $this->showTextAutoComplete( $ac_name, $arr['add_name'],
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

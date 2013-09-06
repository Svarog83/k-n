function ChangeSize(obj, size, focused)
{
	if ( focused != 'out' )
		obj.focused = focused;
	if ( obj.focused != 'focus' )
		obj.style.width= ( size != '' ? size + "px" : '' );

}

function SetSelectSize ( id, size )
{
	if ( size == null )
		size = 80;

	var obj = document.getElementById( id );
	if ( obj )
	{
		obj.style.width = size + 'px';
		obj.onclick 		= new Function ("ChangeSize(this,  '', 'focus')");
		obj.onfocus 		= new Function ("ChangeSize(this,  '', 'focus')");
		obj.onmouseover 	= new Function ("ChangeSize(this,  '', '')");
		obj.onblur 			= new Function ("ChangeSize(this,  '"+size+"', '')");
		obj.onmouseout 		= new Function ("ChangeSize(this,  '"+size+"', 'out')");
	}
}

function getrandom()
{
	var min_random = 0;
	var max_random = 2000;

	max_random++;

	var range = max_random - min_random;
	return Math.floor( Math.random() * range ) + min_random;
}

function choice_simple_this( a )
{

	if ( a.value == 'empty' )
		a.value = '';

	self.focus();
}

function choice_simple( field_name, form_name, value_init, link, exec_func )
{

	if ( !value_init )
		value_init = '';
	if ( !form_name )
		form_name = 'form';

	if ( !link )
	{
		if ( eval( 'document.' + form_name + '.' + field_name + '.value == "empty"' ) )
			eval( 'document.' + form_name + '.' + field_name + '.value = "' + value_init + '"' );
	}
	else
	if ( link.value == 'empty' )
		link.value = value_init;

	if ( exec_func != '' && exec_func && exec_func != undefined && exec_func != 'undefined' )
	{
		str = exec_func + '(document.' + form_name + '.' + field_name + '.value);';
		eval( str );
	}

	self.focus();
}

function countLines( obj )
{
	var rowHeight = obj.clientHeight / obj.rows;
	var curHeight = obj.createTextRange().boundingHeight;

	return parseInt( curHeight / rowHeight ) + ( obj.value != '' ? 1 : 0 );
}

function textareaResizer( a )
{
	if ( !a )
		a = 6;

	b = 25;

	var the_form = document.forms[0];

	for ( var x in the_form )
	{
		if ( ! the_form[x] )
			continue;
		if ( typeof the_form[x].rows != "number" )
			continue;

		var i = countLines( the_form[x] );
		the_form[x].rows = ( i > a ? ( i > b ? b : i + 1 ) : a );
	}

	setTimeout( "textareaResizer(" + a + ");", 300 );
}

function refreshContacts(select_comp, cnt_id)
{
	var obj_span_contacts = $j('#span_contacts');

	if ( obj_span_contacts )
	{
		$j.ajax(
		{
			type: "GET",
			url: "/SysMain/contacts/index.php?module=contacts&action=contacts_refresh&hash=" + Math.random(),
			cache: false,

			dataType: "text",
			data: { select_comp : select_comp,
				ajax_flag : '1'
			},
			success: function( text )
			{
				var data;
				try
				{
					data = $j.parseJSON(text);
				}
				catch(e)
				{
					  showJsonError( obj_span_contacts, e );
				}

				if ( data )
				{
					if ( data['success'] )
					{

						if ( data['return_text'] )
							obj_span_contacts.parent().html ( data['return_text'] );
						else
							obj_span_contacts.parent().html("");

						if ( cnt_id !== null )
							$j('#cnt_' + cnt_id ).toggle();
					}
				}

			},

			error: function( x, e )
			{
				ajaxError( x, e );
			}

		} );
	}
}

function ajaxError ( x, e )
{
	if ( x.status == 0 )
	{
		alert( 'You are offline!!\n Please Check Your Network.' );
	} else if ( x.status == 404 )
	{
		alert( 'Requested URL not found.' );
	} else if ( x.status == 500 )
	{
		alert( 'Internel Server Error.' );
	} else if ( e == 'parsererror' )
	{
		alert( 'Error.\nParsing JSON Request failed.' );
	} else if ( e == 'timeout' )
	{
		alert( 'Request Time out.' );
	}
	else
	{
		alert( 'Unknow Error.\n' + x.responseText );
	}
}

function showJsonError( obj, e )
{
	$j('<div class="warn"></div>').html(e).insertAfter( obj ).click(function(){ $(this).stop(); }).dblclick(function(){ $(this).hide(); }).fadeOut(8000);
}

function GoToTab( tab_id )
{
	if( $j("#a_" + tab_id ).is(":hidden") ) hide_expand( tab_id );

	$j('#form_with_select').scrollTo( "#tab_" + tab_id , 300);
}

function deleteRow( obj )
{
	$j(obj).closest('tr').remove();
}
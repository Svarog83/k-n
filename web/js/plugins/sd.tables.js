$( document ).ready( function ()
{
	//see http://datatables.net/usage/options#sDom for details
	$( '.data-table' ).dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sDom": '<""l>t<"F"p>',
		"aLengthMenu": [[2, 10, 25, 50, -1], [2, 10, 25, 50, "All"]],
		"iDisplayLength" : 2
	} );

	$( 'input[type=checkbox],input[type=radio],input[type=file]' ).uniform();

	$( 'select' ).select2();

	$( "span.icon input:checkbox, th input:checkbox" ).click( function ()
	{
		var checkedStatus = this.checked;
		var checkbox = $( this ).parents( '.widget-box' ).find( 'tr td:first-child input:checkbox' );
		checkbox.each( function ()
		{
			this.checked = checkedStatus;
			if ( checkedStatus == this.checked )
			{
				$( this ).closest( '.checker > span' ).removeClass( 'checked' );
			}
			if ( this.checked )
			{
				$( this ).closest( '.checker > span' ).addClass( 'checked' );
			}
		} );
	} );
} );

 var GlobalSettings = Object();

( function($)
{
    // default settings
    var defaults = { item_class : 'ui-autocomplete-item-grey',
	                 title_width : '390',
	                 ac_options : Object(),
	                 minLength : '1',
	                 'maxLength' : 45,
	                 maxHeight : '450',
	                 form_id : 'form_id' };

    var options;

    $.fn.OMSAutocomplete = function(params)
    {
        options = $.extend({}, defaults, options, params);
        GlobalSettings[$(this).attr('id')] = options;

        var title_width = options.title_width/* - ( $.browser.msie ? 0 : 15 )*/;
        var ac_options = options.ac_options;
        
        $j( this ).css("overflow-x", 'hidden').css("max-height", options.maxHeight );

        $j( this ).autocomplete( {
            minLength: options.minLength,

            source: function( request, response )
            {
                $j.ajax(
                {
                    url: "/SysMain/choice/?module=choice&action=autocomplete&ajax_flag=1&hash=" + Math.random(),
                    dataType: "text",
	                cache: false,
                    data: {
                        options: ac_options,
                        search_str: request.term
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
	                        data = { "rows" : [{"label": "not_found"}]};
                            $j('<div class="warn"></div>').html(text).appendTo( '#' + options.form_id ).click(function(){ $(this).stop(); }).dblclick(function(){ $(this).hide(); }).fadeOut(8000);
                        }
                        $j(".ui-autocomplete").css("overflow-y", data['rows'].length == 1 ? 'hidden' : 'auto' );
						
                        response( data['rows'] );
                    },

                    error: function( x, e )
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

                } );
            },

            select: function( event, ui )
            {
                $j( this ).removeClass("ui-autocomplete-changed ui-autocomplete-loading").addClass("ui-autocomplete-selected");
                
                var record_id       = ac_options['record_id'];
	            var fn_name         = ac_options['onselect'];

                if( record_id !== undefined && ui.item[record_id] != '' )
                {
                    var sel_text = ui.item[ac_options['search_field']];
	                sel_text = sel_text.replace( /&#039;/g, "'" );
                    var val_text = ( sel_text.length > options.maxLength ? sel_text.substr( 0,options.maxLength ) + '...' :sel_text );
                    $j( this ).val( val_text );

	                var return_items = ( typeof record_id !== 'string' ? record_id : [record_id] );

	                var return_arr = new Array();
	                var field_name = '';
	                for ( var i in return_items )
	                {
	                    if ( return_items.hasOwnProperty ( i ) )
	                    {
							field_name = return_items[i];
							if ( ui.item[field_name] != null )
								return_arr.push( ui.item[field_name] );
	                    }
			        }

                    $j("#hid_for_" + $(this).attr('id') ).val( return_arr.join( '::' )  /*ui.item[record_id] */);

	                var fn = window[fn_name];
	                if ( typeof fn === 'function')
	                    fn( ui.item );
                }

            },
            change: function( event, ui )
            {
                    if ( ui.item == null || ui.item[ac_options['search_field']] != $j( this ).val() )
                    {
                        $j( this ).removeClass("ui-autocomplete-selected ui-autocomplete-loading");

                        if( $j( this ).val() != $j( this ).attr( "title" ) ) $j( this ).addClass("ui-autocomplete-changed");
                        else $j( this ).removeClass("ui-autocomplete-changed");

                        $j("#hid_for_" + $(this).attr('id') ).val( '' );
                    }
            },
            search: function( event, ui )
            {
                    $j( this ).removeClass("ui-autocomplete-selected ui-autocomplete-changed").addClass("ui-autocomplete-loading");
                    $j("#hid_for_" + $(this).attr('id') ).val( '' );
            }

        } )

        .data( "autocomplete" )._renderItem = function( ul, item )
        {
            var stext = '';

            if ( item.cnt == 1 )
            {
                var position = "fixed";
                if($j.browser.msie && $j.browser.version.substr(0,3)<8.0)
                    position = "absolute";
                ntitle_width = $j.browser.msie ? title_width : title_width - 5;
                var title =  '<div style="width:'+ ( ntitle_width ) +'px;text-align:center;font-weight:bold;border-bottom:1px solid black;height:20px;position:'+position+';left:1;top:1;background-color:#fff; color:#333;">';
                var len = ac_options.titles.length;

                for ( var i = 0; i < len; i++ )
                {
                    var width = ac_options.widths[i];
                    var text = ac_options.titles[i];
        //                            var float_type = ( i + 1 < len ? 'left' : 'right ');

                    title += '<div style="float:left; width:' + width+ 'px; ">' + text + '</div>';
                }
                title += '<div style="clear: left;"></div></div><br/><br/>';

                    $j( title ).prependTo( ul );
            }

            if ( item.label == 'not_found')
            {
                stext = $j('<div style="color: red;">Ничего не найдено</div>');
                stext.click( function ()
                {
                    $( ".ui-autocomplete-input" ).autocomplete( "close" );
                } );
                $j(".ui-autocomplete").css("overflow-y","hidden");
                $j( "<span></span>" ).append( stext ).prependTo( ul );

                return false;
            }
            else
            {
                stext = '<div style="width:'+ title_width +'px;">';
                var len = ac_options.fields.length;

                for ( var i = 0; i < len; i++ )
                {
                    var width = ac_options.widths[i];
                    var field_name = ac_options.fields[i];
                    var float_type = ( i + 1 < len ? 'left' : 'left ');

                    stext += '<div style="float:'+float_type+'; width:' + width+ 'px; padding-right: 3px;">' + item[field_name] + '</div>';
                }
                stext += '&nbsp;<div style="clear: left;"></div></div>';

                options.item_class = options.item_class == 'ui-autocomplete-item-grey' ? 'ui-autocomplete-item-white' : 'ui-autocomplete-item-grey';

                return $( "<li class='"+ options.item_class +"'></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + stext + "</a>" )
                        .appendTo( ul );
            }
        };

        return this;
        };
})(jQuery);

$j(document).ready(
    function()
    {
        $j( "#form_with_select" ).scroll( function( event )
        {
            $( ".ui-autocomplete-input" ).autocomplete( "close" );
            return false;
        } )
    }
);

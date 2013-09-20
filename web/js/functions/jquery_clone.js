
( function($)
{
    var defaults = { ac_hidden_field: false, results: Object() };

// public methods
    var methods = {
        // plugin initialization
        init:function(params) {
            var options = $.extend({}, defaults, params);
            var _OMSCloneField = $(this);
	        var _id = _OMSCloneField.attr('id');
	        
            _OMSCloneField.data('cnt', 1 );
	        _OMSCloneField.data('ac_hidden_field', options.ac_hidden_field );

	        _OMSCloneField.css('float', 'left');
	        

	        var img_obj = $("<img class='" + _id + "_plus'/>");
            img_obj.attr('src', '/icon/plus-icon_16.png').css('cursor', 'pointer')
		                                                 .css('float', 'left')
		                                                 .css('vertical-align', 'bottom')
		                                                 .css('margin-left', '3px').insertAfter ( _OMSCloneField );
            img_obj.click( function(){ _OMSCloneField.OMSCloneField( 'clone', img_obj ) } );
            img_obj.data ('key', '' );
            img_obj.data ('value', '' );

             var i = 0;
             $.each( options.results, function( key, value)
             {
              if ( i == 0 )
              {

	              if ( _OMSCloneField.attr('tagName') != "SELECT" )
	                _OMSCloneField.val(value);
	              else
	                _OMSCloneField.val( key );

	              if ( options.ac_hidden_field )
                        $j("#hid_for_" + _OMSCloneField.attr('id') ).val( key );
                    i++;
              }
              else
              {
                  img_obj.data ('key', key );
                  img_obj.data ('value', value );
                  img_obj.click( );
              }
            });

            return this;
        },
        // cloning the field
        clone: function( img_obj ) {

            var ac_hidden_field = $j(this).data ( 'ac_hidden_field' );

	        var key = img_obj.data('key');
            var value = img_obj.data('value');

            /*reset values to null*/
            img_obj.data('value', '');
            img_obj.data('key', '');

            /* this is pointing to the original text field which was cloned */
            /*img_obj - this is the plus icon which was clicked*/
            var cnt = $j(this).data('cnt');
            cnt++;
            $j(this).data('cnt', cnt );

            var td_obj = $j ( this ).parent();

	        if ( ac_hidden_field )
	        {
				/*REF1*/
				var hidden_field_obj = $j(this).next().next();
	        }
            var id = $j( this ).attr('id');

//            td_obj.css('border', '1px solid black');
            
            var new_span_id = 'span_'+id+'_'+cnt;
            var new_span = $j('<span id="'+new_span_id+'"><br></span>');

            var new_field = $j( this ).clone();
            var new_field_id = 'field_'+id+'_'+cnt;
            new_field.attr('id', new_field_id).val(value).css('margin-top', '3px').css('float', 'left').css('clear', 'left').removeClass().appendTo( new_span );

	        if ( ac_hidden_field )
	        {
                hidden_field_obj.clone().val(key).attr('id', 'hid_for_' + new_field_id ).appendTo( new_span );
	        }

	        if ( new_field.attr("tagName") == 'SELECT' && key != '' ) /* it it's a select tag we need to set key, not value*/
	            new_field.val ( key );


            var img_obj_minus = $("<img  class='" + id + "_minus'/>");
            img_obj_minus.attr('src', '/icon/minus-icon_16.png').css('cursor', 'pointer').css('margin-left', '3px').css('float', 'left').css('clear', 'right').insertAfter ( new_field );
            img_obj_minus.click( function(){ $(this).OMSCloneField( 'clean', new_span ) } );

            new_span.appendTo(td_obj);

	        if ( new_field.attr("tagName") != 'SELECT' )
	            new_field.focus();

//            $j ( "<br />").insertBefore($j('input:last', td_obj ) );

	        if ( ac_hidden_field )
	        {
				var params = GlobalSettings[id];
				new_field.OMSAutocomplete( params );
	        }
        },
        // removing field
        clean: function(span_obj) {
            span_obj.remove();
        },
	    clear: function( )
	    {
		    var _id = $j(this).attr('id');
		    $j(this).val('');
		    $j('.' + _id + '_minus' ).trigger('click');
		    $j('.' + _id + '_plus' ).remove();
	    }
    };


    $.fn.OMSCloneField = function( method )
    {
         if ( methods[method] ) {
            // if method exists we call it and pass al the parameters
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
        } else if ( typeof method === 'object' || ! method ) {
            // run the init method of the object
            return methods.init.apply( this, arguments );
        } else {
            // if an error
            alert ( 'Proglems with clone plugin. Please contact administrators' );
        }
   };
})(jQuery);
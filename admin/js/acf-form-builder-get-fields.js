(function( $, window, document ) {
	'use strict';
    
    // execute when the DOM is ready
    $(document).ready(function () {
    	
        $('#acf_fb_form_settings').insertAfter('#acf_location');
        $('#acf_fb_form_settings').insertAfter('#acf-field-group-locations');
        var is_pro = jQuery('#acf_fb_is_pro').val();

        // js 'change' event triggered on the acf_fb_show_form field
        $(document).on( 'change', '.show_form input[type=radio]', function () {

        	var show_form = $(this).val();
        	var show_custom_field = '';          
        	
            if (show_form) {
            	$.post(
            		acf_fb_meta_box_obj.url,
            		{
                       action: 'acf_fb_ajax_change',             // POST data, action
                       acf_fb_field_value: show_form, 			// POST data, acf_fb_show_form
                       acf_fb_is_pro: is_pro
                   	},
            		function(data) {            			
            			if (data != 'hide_element') {
                            if ('acf' == is_pro) {// acf free
                				$("table#acf_form_settings tbody tr").not('tr.show_form').remove();
                				$("table#acf_form_settings tbody").append(data);
                            }
                            if ('acf-field-group' == is_pro ) {// acf pro
                                $("div#acf_fb_form_settings div.inside").append(data);
                            }
            			} else {
                            $("table#acf_form_settings tbody tr").not('tr.show_form').remove();// acf free
            				$("div#acf_fb_form_settings div.inside div#hide-form").remove();// acf pro
            			}
            		},
            		'html'
            	);
            }
        });

        // js 'change' event triggered on the show field 
        $(document).on('change','td.show_custom select', function () {

        	var show_custom_field = $(this).val();
        	
	        if (show_custom_field) {
	        	$.post(
	        		acf_fb_meta_box_obj.url,
	        		{
	                   action: 'acf_fb_ajax_change',             // POST data, action
	                   acf_fb_field_value: show_custom_field, 	// POST data, acf_fb_show_form
                       acf_fb_is_pro: is_pro
	               	},
	        		function(data) {            			
	        			if (data != 'hide_element') {
                            // acf free and pro
	        				$("table#acf_form_show_custom tbody tr td.custom_actions select").remove();
	        				$("table#acf_form_show_custom tbody tr td.custom_actions").append(data);
                            
	        			}
	        		},
	        		'html'
	        	);
            }
        });
    });

})( jQuery, window, document );

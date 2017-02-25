jQuery(document).ready(function () {	
    $ = jQuery;

    if($("input[type=checkbox]").is(':checked')){ 
        $('#ov_settings_button').prop( "disabled", false );
    }else{
        $('#ov_settings_button').prop( "disabled", true );
    }

    $("input[name='check_btn']").click(function(){ 
        $("#mo_ln_form").submit();
    });

    //images
    $(".form_preview").click(function () {
       window.open($(this).data("formlink"), '_blank');
    });

	//FAQ
	$(".registration_question").click(function () {
    	$(this).next(".mo_registration_help_desc").slideToggle(400);
    });
    
    $(".form_query").click(function(){
        var str = "#form_query_desc_";
        $(str.concat($(this).data("desc"))).slideToggle(400);
    });
     
    $(".app_enable").click(function () {
        var str = "#";
        $(str.concat($(this).data("toggle"))).slideToggle(400);
        if($("input[type=checkbox]").is(':checked')){
            $('#ov_settings_button').prop( "disabled", false );
        }else{
            $('#ov_settings_button').prop( "disabled", true );
        }
    });

    $(".form_options").click(function(){
        var str="_field";
        var str2 = "form";
        var form_field= $(this).data("form");
        var form = form_field.substr(0, form_field.indexOf('_')+1);
        $(".".concat(form.concat(str2))).slideUp(400);
        $("#".concat(form_field.concat(str))).slideToggle(400);
    });
	
    //form validation
    $("#ov_settings_button").click(function() {
        //woocommerce default form
         if(!$("#wc_default").is(':checked')){
            if($('input[name=mo_customer_validation_wc_enable_type]').is(':checked'))
                $('input[name=mo_customer_validation_wc_enable_type]').prop("checked", false);
        }else{
            if( !$('input[name=mo_customer_validation_wc_enable_type]').is(':checked')){
                $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for Woocommerce Default Registration Form.<br/>');
                $("#wc_default").prop("checked",false);
            }
        }

        //woocommerce checkout form
         if(!$("#wc_checkout").is(':checked')){
            if($('input[name=mo_customer_validation_wc_checkout_type]').is(':checked'))
                $('input[name=mo_customer_validation_wc_checkout_type]').prop("checked", false);
        }else{
            if( !$('input[name=mo_customer_validation_wc_checkout_type]').is(':checked')){
                $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for Woocommerce Checkout Registration Form.<br/>');
                $("#wc_default").prop("checked",false);
            }
        }

        //buddypress form
         if(!$("#bbp_default").is(':checked')){
            if($('input[name=mo_customer_validation_bbp_enable_type]').is(':checked'))
                $('input[name=mo_customer_validation_bbp_enable_type]').prop("checked", false);
        }else{
            if($('input[name=mo_customer_validation_bbp_enable_type]').is(':checked')){
                if($('#bbp_phone').is(':checked')){
                    if ($('#bbp_phone_field_key').val() === ''){
                        $("#bbp_default").prop("checked", false);
                        $('#bbp_phone').prop("checked", false);
                        $('input[name=bbp_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the Name of the phone number field you created in BuddyPress.<br/>');
                    }else{
                        $('input[name=bbp_phone_field_key]').val($('#bbp_phone_field_key').val());
                    }
                }else if($('#bbp_both').is(':checked')){
                    if ($('#bbp_phone_field_key1').val() === ''){
                        $("#bbp_default").prop("checked", false);
                        $('#bbp_both').prop("checked", false);
                        $('input[name=bbp_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the Name of the phone number field you created in BuddyPress.<br/>');
                    }else{
                        $('input[name=bbp_phone_field_key]').val($('#bbp_phone_field_key1').val());
                    }
                }else{
                     $('input[name=bbp_phone_field_key]').val('');
                }
            }else{
                    $("#bbp_default").prop("checked", false);
                    $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for BuddyPress Registration Form.<br/>');
            }
        }

        //simplr form
        if(!$("#simplr_default").is(':checked')){
            if($('input[name=mo_customer_validation_simplr_enable_type]').is(':checked'))
                $('input[name=mo_customer_validation_simplr_enable_type]').prop("checked", false);
            $('input[name=simplr_phone_field_key]').val('');
        }else{
            if($('input[name=mo_customer_validation_simplr_enable_type]').is(':checked')){
                if($('#simplr_phone').is(':checked')){
                    if ($('#simplr_phone_field_key1').val() === ''){
                        $("#simplr_default").prop("checked", false);
                        $('#simplr_phone').prop("checked", false);
                        $('input[name=simplr_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the Field Key of the phone number field you created in Simplr User Registration form.<br/>');
                    }else{
                        $('input[name=simplr_phone_field_key]').val($('#simplr_phone_field_key1').val());
                    }
                }else if($('#simplr_both').is(':checked')){
                    if ($('#simplr_phone_field_key2').val() === ''){
                        $("#simplr_default").prop("checked", false);
                        $('#simplr_both').prop("checked", false);
                        $('input[name=simplr_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the Field Key of the phone number field you created in Simplr User Registration form.<br/>');
                    }else{
                        $('input[name=simplr_phone_field_key]').val($('#simplr_phone_field_key2').val());
                    }
                }else{
                     $('input[name=simplr_phone_field_key]').val('');
                }
            }else{
                    $("#simplr_default").prop("checked", false);
                    $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for Simplr User Registration Form.<br/>');
            }
        }

        //ultimate memeber form
        if(!$("#um_default").is(':checked')){
            if($('input[name=mo_customer_validation_um_enable_type]').is(':checked'))
                $('input[name=mo_customer_validation_um_enable_type]').prop("checked", false);
        }else{
            if( !$('input[name=mo_customer_validation_um_enable_type]').is(':checked')){
                $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for Ultimate Member Registration Form.<br/>');
                $("#um_default").prop("checked",false);
            }
        }

        //event registration form
        if(!$("#event_default").is(':checked')){
            if($('input[name=mo_customer_validation_event_enable_type]').is(':checked'))
                $('input[name=mo_customer_validation_event_enable_type]').prop("checked", false);
        }else{
             if( !$('input[name=mo_customer_validation_event_enable_type]').is(':checked')){
                $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for Event Registration Form.<br/>');
                $("#event_default").prop("checked",false);
            }
        }

        //user ultra form
       if(!$("#uultra_default").is(':checked')){
            if($('input[name=mo_customer_validation_uultra_enable_type]').is(':checked'))
                $('input[name=mo_customer_validation_uultra_enable_type]').prop("checked", false);
            $('input[name=uultra_phone_field_key]').val('');
        }else{
            if($('input[name=mo_customer_validation_uultra_enable_type]').is(':checked')){
               
                if($('#uultra_phone').is(':checked')){
                    if ($('#uultra_phone_field_key').val() === ''){
                        $("#uultra_default").prop("checked", false);
                        $('#uultra_phone').prop("checked", false);
                        $('input[name=uultra_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the Field Key of the phone number field you created in Users Ultra Registration form.<br/>');
                    }else{
                        $('input[name=uultra_phone_field_key]').val($('#uultra_phone_field_key').val());
                    }
                }else if($('#uultra_both').is(':checked')){
                    if ($('#uultra_phone_field_key1').val() === ''){
                        $("#uultra_default").prop("checked", false);
                        $('#uultra_both').prop("checked", false);
                        $('input[name=uultra_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the Field Key of the phone number field you created in Users Ultra Registration form.<br/>');
                    }else{
                        $('input[name=uultra_phone_field_key]').val($('#uultra_phone_field_key1').val());
                    }
                }else{
                     $('input[name=uultra_phone_field_key]').val('');
                }
            }else{ 
                   $("#uultra_default").prop("checked", false);
                   $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for Users Ultra Registration Form.<br/>');
                   
            }
        }

        //crf form
        if(!$("#crf_default").is(':checked')){
            if($('input[name=mo_customer_validation_crf_enable_type]').is(':checked'))
                $('input[name=mo_customer_validation_crf_enable_type]').prop("checked", false);
            $('input[name=crf_phone_field_key]').val('');
            $('input[name=crf_email_field_key]').val('');
        }else{
            if($('input[name=mo_customer_validation_crf_enable_type]').is(':checked')){
                if($('#crf_phone').is(':checked')){
                    if ($('#crf_phone_field_key').val() === ''){
                        $("#crf_default").prop("checked", false);
                        $('#crf_phone').prop("checked", false);
                        $('input[name=crf_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the label name of the phone number field you created in Custom User Registration form.<br/>');
                    }else{
                        $('input[name=crf_phone_field_key]').val($('#crf_phone_field_key').val());
                    }
                }else if($('#crf_email').is(':checked')){
                    if ($('#crf_email_field_key').val() === ''){
                        $("#crf_default").prop("checked", false);
                        $('#crf_email').prop("checked", false);
                        $('input[name=crf_email_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the label name of the email number field you created in Custom User Registration form.<br/>');
                    }else{
                        $('input[name=crf_email_field_key]').val($('#crf_email_field_key').val());
                    }
                }else if($('#crf_both').is(':checked')){
                    if ($('#crf_phone_field_key1').val() === '' || $('#crf_email_field_key1').val() === ''){
                        $("#crf_default").prop("checked", false);
                        $('#crf_both').prop("checked", false);
                        $('input[name=crf_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the label name of the phone and email number field you created in Custom User Registration Form.<br/>');
                    }else{
                        $('input[name=crf_phone_field_key]').val($('#crf_phone_field_key1').val());
                        $('input[name=crf_email_field_key]').val($('#crf_email_field_key1').val());
                    }
                }else{
                     $('input[name=crf_phone_field_key]').val('');
                     $('input[name=crf_email_field_key]').val('');
                }
            }else{
                    $("#crf_default").prop("checked", false);
                    $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for Custom User Registration Form.<br/>');
            }
        }

        //userProfile made easy
        if(!$("#upme_default").is(':checked')){
            if($('input[name=mo_customer_validation_upme_enable_type]').is(':checked'))
                $('input[name=mo_customer_validation_upme_enable_type]').prop("checked", false);
            $('input[name=upme_phone_field_key]').val('');
        }else{
            if($('input[name=mo_customer_validation_upme_enable_type]').is(':checked')){
               
                if($('#upme_phone').is(':checked')){
                    if ($('#upme_phone_field_key').val() === ''){
                        $("#upme_default").prop("checked", false);
                        $('#upme_phone').prop("checked", false);
                        $('input[name=upme_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the Field Key of the phone number field you created in User Profile Made Easy Registration form.<br/>');
                    }else{
                        $('input[name=upme_phone_field_key]').val($('#upme_phone_field_key').val());
                    }
                }else if($('#upme_both').is(':checked')){
                    if ($('#upme_phone_field_key1').val() === ''){
                        $("#upme_default").prop("checked", false);
                        $('#upme_both').prop("checked", false);
                        $('input[name=upme_phone_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the Field Key of the phone number field you created in User Profile Made Easy Registration form.<br/>');
                    }else{
                        $('input[name=upme_phone_field_key]').val($('#upme_phone_field_key1').val());
                    }
                }else{
                     $('input[name=upme_phone_field_key]').val('');
                }
            }else{ 
                   $("#upme_default").prop("checked", false);
                   $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for User Profile Made Easy Registration Form.<br/>');
                   
            }
        }

        //cf7 contact form
        if(!$("#cf7_contact").is(':checked')){
            if($('input[name=mo_customer_validation_cf7_contact_type]').is(':checked'))
                $('input[name=mo_customer_validation_cf7_contact_type]').prop("checked", false);
            $('input[name=cf7_email_field_key]').val('');
        }else{
            if($('input[name=mo_customer_validation_cf7_contact_type]').is(':checked')){
               
                if($('#cf7_contact_email').is(':checked')){
                    if ($('#cf7_email_field_key').val() === ''){
                        $("#cf7_contact").prop("checked", false);
                        $('#cf7_contact_email').prop("checked", false);
                        $('input[name=cf7_email_field_key]').val('');
                        $('#error_message').val($('#error_message').val()+'Please Enter the name of the email address field you created in User Contact Form 7.<br/>');
                    }
                }else{
                     $('input[name=cf7_email_field_key]').val('');
                }
            }else{ 
                   $("#cf7_contact").prop("checked", false);
                   $('#error_message').val($('#error_message').val()+'Please choose a Verification Method for Contact Form 7.<br/>');
            }
        }

	    $("#mo_otp_verification_settings").submit();
    });

});

function extraSettings(host,url){
    document.getElementById('extraSettingsRedirectURL').value = host.concat(url);
    document.getElementById('showExtraSettings').submit();
}

function mo_registration_valid_query(f) {
    !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
            /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
}
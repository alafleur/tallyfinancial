function validate_form_fields()
{
	in_time_validation();	
	in_line_validation();
	
	$('.btn-form-submit').click(function(e){
		e.preventDefault();
		form_submit_validation($(this).closest('form').attr('id'));			
	});
	
	$('.form-control').keypress(function(e){
		if(!$(this).is("textarea") && e.keyCode == 13 && $('.btn-form-submit').length > 0){
			e.preventDefault();
			form_submit_validation($(this).closest('form').attr('id'));
		}
	});

	$('.field-ok').html('&nbsp;');	
}

function form_submit_validation(myForm)
{
	$('#' + myForm + ' .required').each(function(){
		validate_require_field(this);		
	});
	
	$('#' + myForm + ' .email').each(function(){
		validate_email_field(this)
	});
	
	$('#' + myForm + '.unique-email').each(function(){
		validate_unique_email_field(this)
	});
	
	$('#' + myForm + '.registered-email').each(function(){
		validate_registered_email_field(this)
	});

	$('#' + myForm + ' .cellphone').each(function(){
		validate_cellphone_field(this)
	});
	
	$('#' + myForm + ' .min-length').each(function(){
		validate_min_length_field(this)
	});
	
	$('#' + myForm + ' .re-match').each(function(){
		validate_re_match_field(this)
	});
	
	$('#' + myForm + ' .transit-number').each(function(){
		validate_transit_number_field(this)
	});
	
	setTimeout(
	  function() 
	  {
	    if($('#' + myForm + ' .has-error').length > 0)
		{
			$('.has-error').each(function(){
				goToByScroll(this);
				return false;
			});	
		} else {
			$('#' + myForm).submit();
		}
	  }, 500);	
}

function in_line_validation()
{
	$('.required').blur(function(){
		validate_require_field(this);		
	});
	
	$('.email').blur(function(){
		validate_email_field(this);
	});
	
	$('.unique-email').blur(function(){
		validate_unique_email_field(this)
	});
	
	$('.registered-email').blur(function(){
		validate_registered_email_field(this)
	});

	$('.cellphone').blur(function(){
		validate_cellphone_field(this)
	});
	
	$('.min-length').blur(function(){
		validate_min_length_field(this)
	});
	
	$('.re-match').blur(function(){
		validate_re_match_field(this)
	});
	
	$('.transit-number').blur(function(){
		validate_transit_number_field(this)
	});
}

function in_time_validation()
{		
	$('.required').keyup(function(event){
		var key = event.keyCode || event.charCode;
		
		if($(this).val() == '' && (key == 8 || key == 46 || ( event.ctrlKey && ( String.fromCharCode(event.which) === 'x' || String.fromCharCode(event.which) === 'X' )))) {
			validate_require_field(this);
		}
	});
}

function check_add_parent(input_id,input_type,input_class)
{
	if($('#' + input_id).parent().hasClass('form-field') || $('#' + input_id).parent().hasClass('form-group') || $('#' + input_id).parent().hasClass('field-box')){
		if(input_type == '1'){
			$('#' + input_id).parent().addClass(input_class);
		} else {
			$('#' + input_id).parent().removeClass(input_class);
		}
	}
	else if($('#' + input_id).parent().parent().hasClass('form-field') || $('#' + input_id).parent().parent().hasClass('form-group') || $('#' + input_id).parent().parent().hasClass('field-box')){
		if(input_type == '1'){
			$('#' + input_id).parent().parent().addClass(input_class);
		} else {
			$('#' + input_id).parent().parent().removeClass(input_class);
		}
	}
	else if($('#' + input_id).parent().parent().parent().hasClass('form-field') || $('#' + input_id).parent().parent().parent().hasClass('form-group') || $('#' + input_id).parent().parent().parent().hasClass('field-box')){
		if(input_type == '1'){
			$('#' + input_id).parent().parent().parent().addClass(input_class);
		} else {
			$('#' + input_id).parent().parent().parent().removeClass(input_class);
		}
	}
}

function display_info_notification(id_input, text_notify)
{	
	check_add_parent(id_input, 0, 'has-success');
	check_add_parent(id_input, 0, 'has-warning');
	check_add_parent(id_input, 0, 'has-error');
	
	$('#' + id_input).next().remove();
	$('#' + id_input).parent().append('<span class="help-block pull-left field-info">' + text_notify + '</span>');
}

function display_error_notification(id_input, text_notify)
{	
	check_add_parent(id_input, 0, 'has-success');
	check_add_parent(id_input, 0, 'has-warning');
	check_add_parent(id_input, 1, 'has-error');
	
	$('#' + id_input).next().remove();
	$('#' + id_input).parent().append('<span class="help-block pull-left"><i class="fa fa-times-circle"></i> ' + text_notify + '</span>');	
}

function display_success_notification(id_input, text_notify)
{	
	check_add_parent(id_input, 0, 'has-error');
	check_add_parent(id_input, 0, 'has-warning');
	check_add_parent(id_input, 1, 'has-success');
	
	$('#' + id_input).next().remove();
	//$('#' + id_input).parent().append('<span class="help-block pull-left">&nbsp;</span>');
}

function display_warning_notification(id_input, text_notify)
{	
	check_add_parent(id_input, 0, 'has-error');
	check_add_parent(id_input, 0, 'has-success');
	check_add_parent(id_input, 1, 'has-warning');
	
	$('#' + id_input).next().remove();
	$('#' + id_input).parent().append('<span class="help-block pull-left">' + text_notify + '</span>');
}

function display_validating_notification(obj, by_id)
{
	if(!by_id)
		id_input = $(obj).attr('id');
	else
		id_input = obj;
	
	check_add_parent(id_input, 0, 'has-error');
	check_add_parent(id_input, 0, 'has-success');
	check_add_parent(id_input, 0, 'has-warning');
	
	$('#' + id_input).next().remove();
	$('#' + id_input).parent().append('<span class="help-block pull-left field-checking">Validating...</span>');	
}

function remove_all_notification(obj, by_id)
{
	if(!by_id)
		id_input = $(obj).attr('id');
	else
		id_input = obj;
	
	check_add_parent(id_input, 0, 'has-error');
	check_add_parent(id_input, 0, 'has-success');
	check_add_parent(id_input, 0, 'has-warning');
	
	if(!$('#' + id_input).next().hasClass('field-info'))
	{
		$('#' + id_input).next().remove();
	}
}

function goToByScroll(obj)
{    
    $('html,body').animate({
        scrollTop: ($(obj).offset().top-20)},
    'slow');
}

function replaceAll(str, find, replace) {
  var i = str.indexOf(find);
  if (i > -1){
    str = str.replace(find, replace); 
    i = i + replace.length;
    var st2 = str.substring(i);
    if(st2.indexOf(find) > -1){
      str = str.substring(0,i) + replaceAll(st2, find, replace);
    }       
  }
  return str;
}

function removeAllTrailingDotsAndCommasInEmail(email)
{
	email = $.trim(email);
	var l = email.length;
	var c = email.substring(l-1);
	var d = email.substring(l-2);
	
	while(c == '.' || c == ',')
	{		
		var iDots = email.timesCharExist('.');
		if(iDots == 1){
			break;
		} else {
			email = email.substring(0, (l-1));
			l = l - 1;
			c = email.substring(l-1);
		}
	}
	
	return email;
}

function validateEmail(emailAddress)
{
	var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    
    return pattern.test(emailAddress);
}

function validate_require_field(obj)
{
	var require_value = $.trim($(obj).val());
	$(obj).val(require_value);
	var id = $(obj).attr('id');
	
		
	if(require_value == '')
	{		
		notify = $('#' + id).attr('placeholder') + " is required.";
		display_error_notification(id, notify);
	}
	else
	{
		if(!$(obj).hasClass('email'))
		{
			notify = $('#' + id).attr('placeholder') + " looks good.";
			display_success_notification(id, notify);
		}
	}
}

function validate_transit_number_field(obj)
{
	var transit_number = $.trim($(obj).val());
	$(obj).val(transit_number);
		
	if(transit_number != '')
	{		
		display_validating_notification(obj,false);		
		id = $(obj).attr('id');
			
		$.ajax({
	    	type: "POST",
	      	url: JS_BASE_URL + "handle_ajax_request",
	  		async: false,
	      	data: "p_id="+id+"&p_transit_number="+transit_number+"&p_func=VALIDATE_TRANSIT_NUMBER",
	      	success: function(result) {								
				if(result != '')
				{
					ar_result = result.split('|||');
					if(ar_result[0] == "SUCCESS")
					{
						display_success_notification(ar_result[1], ar_result[2]);
					}
					else
					{
						display_error_notification(ar_result[1], ar_result[2]);
					}
				}
			}
		});
	}
	else
	{
		if(!$(obj).hasClass('required'))
		{
			remove_all_notification(obj, false);
		}
	}
}

function validate_email_field(obj)
{
	email = replaceAll($.trim($(obj).val()), ' ', '');
	email = replaceAll(email,',', '.');	
	email = removeAllTrailingDotsAndCommasInEmail(email);
	$(obj).val(email);
		
	if(email != '')
	{		
		display_validating_notification(obj,false);		
		id = $(obj).attr('id');
			
		if(validateEmail(email))
		{
			notify = $('#' + id).attr('placeholder') + " looks good.";
			display_success_notification(id, notify);
		}
		else
		{
			display_error_notification(id, "Doesn't look like a valid email.");
		}
	}
	else
	{
		if(!$(obj).hasClass('required'))
		{
			remove_all_notification(obj, false);
		}
	}
}

function validate_unique_email_field(obj)
{
	email = replaceAll($.trim($(obj).val()), ' ', '');
	email = replaceAll(email,',', '.');	
	email = removeAllTrailingDotsAndCommasInEmail(email);
	$(obj).val(email);
		
	if(email != '')
	{		
		display_validating_notification(obj,false);		
		id = $(obj).attr('id');
			
		if(validateEmail(email))
		{
			var idUser = 0;
			if($('#p_id').length > 0)
				idUser = $('#p_id').val();
				
			$.ajax({
		    	type: "POST",
		      	url: JS_BASE_URL + "handle_ajax_request",
		  		async: false,
		      	data: "id="+id+"&szEmail="+email+"&idUser="+idUser+"&p_func=CHECK_DUPLICATE_EMAIL",
		      	success: function(result) {								
					if(result != '')
					{
						ar_result = result.split('|||');
						if(ar_result[0] == "SUCCESS")
						{
							display_success_notification(ar_result[1], ar_result[2]);
						}
						else
						{
							display_error_notification(ar_result[1], ar_result[2]);
						}
					}
				}
			});
		}
		else
		{
			display_error_notification(id, "Doesn't look like a valid email.");
		}
	}
	else
	{
		if(!$(obj).hasClass('required'))
		{
			remove_all_notification(obj, false);
		}
	}
}

function validate_registered_email_field(obj)
{
	email = replaceAll($.trim($(obj).val()), ' ', '');
	email = replaceAll(email,',', '.');	
	email = removeAllTrailingDotsAndCommasInEmail(email);
	$(obj).val(email);
		
	if(email != '')
	{		
		display_validating_notification(obj,false);		
		id = $(obj).attr('id');
			
		if(validateEmail(email))
		{		
			$.ajax({
		    	type: "POST",
		      	url: JS_BASE_URL + "handle_ajax_request",
		  		async: false,
		      	data: "id="+id+"&szEmail="+email+"&idUser=0&p_func=CHECK_EMAIL_REGISTERED",
		      	success: function(result){								
					if(result != '')
					{
						ar_result = result.split('|||');
						if(ar_result[0] == "SUCCESS")
						{
							display_success_notification(ar_result[1], ar_result[2]);
						}
						else
						{
							display_error_notification(ar_result[1], ar_result[2]);
						}
					}
				}
			});
		}
		else
		{
			display_error_notification(id, "Doesn't look like a valid email.");
		}
	}
	else
	{
		if(!$(obj).hasClass('required'))
		{
			remove_all_notification(obj, false);
		}
	}
}

function validate_cellphone_field(obj)
{
	cellphone = $.trim($(obj).val());
	$(obj).val(cellphone);
	
	if(cellphone != '')
	{		
		display_validating_notification(obj,false);		
		id = $(obj).attr('id');
			
		if(cellphone.length == 10 && /^[0-9]+$/.test(cellphone))
		{
			notify = $('#' + id).attr('placeholder') + " looks good.";
			display_success_notification(id, notify);
		}
		else
		{
			display_error_notification(id, "Doesn't look like a valid mobile phone number.");
		}
	}
	else
	{
		if(!$(obj).hasClass('required'))
		{
			remove_all_notification(obj, false);
		}
	}
}

function validate_min_length_field(obj)
{
	value = $.trim($(obj).val());
	$(obj).val(value);
	
	if(value != '')
	{		
		var max = 0;		
		id = $(obj).attr('id');
		min = parseInt($('#' + id + '_minlength').val());
		
		if($('#' + id + '_maxlength').length > 0){
			max = parseInt($('#' + id + '_maxlength').val());
		}
		
		if(value.length < min)
		{
			notify = $('#' + id).attr('placeholder') + " must be at least " + min + " characters in length.";
			display_error_notification(id, notify);
		}
		else if(max == 0 || value.length <= max)
		{
			notify = $('#' + id).attr('placeholder') + " looks good.";
			display_success_notification(id, notify);
		}
	}
	else
	{
		if(!$(obj).hasClass('required'))
		{
			msg = $('#' + $(obj).attr('id') + '_info').val();
			display_info_notification($(obj).attr('id'), msg);
		}
	}
}

function validate_re_match_field(obj)
{
	value = $.trim($(obj).val());
	$(obj).val(value);
	
	if(value != '')
	{		
		id = $(obj).attr('id');
		match_value = $.trim($('#' + id.replace('_re_', '_')).val());
		
		if(value != match_value)
		{
			notify = $('#' + id).attr('placeholder') + " does not match.";
			display_error_notification(id, notify);
		}
		else
		{
			notify = $('#' + id).attr('placeholder') + " looks good.";
			display_success_notification(id, notify);
		}
	}
	else
	{
		if(!$(obj).hasClass('required'))
		{
			msg = $('#' + $(obj).attr('id') + '_info').val();
			display_info_notification($(obj).attr('id'), msg);
		}
	}
}

String.prototype.timesCharExist=function(c){var t=0,l=0,c=(c+'')[0];while(l=this.indexOf(c,l)+1)++t;return t};
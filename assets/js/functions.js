var mfa_timer;

// Trigger the selected institution login view
function connect_with_bank(id){
	show_container('pp-loading');
	$.ajax({
		type: "POST",
		url: JS_BASE_URL + "handle_ajax_request?p_func=GET_INSTITUTE_LOGIN_FORM",
		data:'p_id='+id,
		success: function(data){
			hide_container('pp-loading');
			arData = data.split('|||');
			if(arData[0] == 'SUCCESS'){
				$('#loginModal .modal-title').html(arData[1]);
				$('#loginModal .modal-body').html(arData[2]);
				show_modal('loginModal');
				
				setTimeout(function(){
					$('#popup-loading').width($('#loginModal .modal-dialog').width()-5).height($('#loginModal .modal-dialog').height()-105);
					validate_form_fields();
			    	$('[data-toggle="tooltip"]').tooltip();
				}, 200);							
			} else {
				$('#loginModal .modal-title').html('Connection Error');
				$('#loginModal .modal-body').html('<div class="alert alert-danger">' + arData[1] + '</div>');
				show_modal('loginModal');
			}
		}
	});
}

function activateCustomerAccount(idInstitution){
	clearInterval(mfa_timer);
	show_container('pp-loading');
	$.ajax({
		type: "POST",
		url: JS_BASE_URL + "handle_ajax_request?p_func=ACTIVATE_CUSTOMER_ACCOUNT",
		data:'arLogin[id]='+idInstitution,
		success: function(data){
			hide_container('pp-loading');
			arData = data.split('|||');
			if(arData[0] == 'SUCCESS'){
				hide_modal('loginModal');
				$('#institution_id').val(arData[1]);
				$('#account_id').val(arData[2]);
				$('#account_number').val(arData[3]);
				$('#statement_file').val(arData[4]);
				$('#frmAuthentication').submit();
			}else if(arData[0] == 'MFA'){
				$('#loginModal .modal-title').html(arData[1]);
				$('#loginModal .modal-body').html(arData[2]);
				show_modal('loginModal');
			    
			    setTimeout(function(){
					$('#popup-loading').width($('#loginModal .modal-dialog').width()-5).height($('#loginModal .modal-dialog').height()-105);
					validate_form_fields();
			    	$('[data-toggle="tooltip"]').tooltip();
				}, 200);	
			    
			    var ctr = 0;
				mfa_timer = setInterval(function(){
					current_time_s = parseInt($('#seconds').html());
					
					if(current_time_s == 0)
					{
						hide_modal('loginModal');
						clearInterval(mfa_timer);
						window.location = JS_BASE_URL + "/users/signup/link-your-bank";
					}
					else
					{
						current_time_s = current_time_s - 1;
						
						if(current_time_s < 10)
							current_time_s = '00' + current_time_s;	
						else if(current_time_s < 100)
							current_time_s = '0' + current_time_s;
	
						$('#seconds').html(current_time_s);
					}					
				}, 1000);
			} else {
				$('#frmBankLogin').before('<div class="alert alert-danger">' + arData[1] + '</div>');
			}
		},
		error: function (jqXHR, exception) {
	        $('#loginModal .modal-title').html('Something went wrong');
			$('#loginModal .modal-body').html('<h6 class="color-red">Something went wrong while processing your request. Please wait for a while, it will again make an attempt.</h6>');
			show_modal('loginModal');
			
	        setTimeout(function(){
		        window.location = JS_BASE_URL + "/users/signup/link-your-bank";
			}, 5000);
	    }
	});
}

function getCoustomerAccountTransaction(idCustomer, idAccount){
	show_container('ts-loading');
	$.ajax({
		type: "POST",
		url: JS_BASE_URL + "handle_ajax_request?p_func=GET_ACCOUNT_TRANSACTIONS",
		data:'p_cid='+idCustomer+'&p_aid='+idAccount,
		success: function(data){
			hide_container('ts-loading');
			arData = data.split('|||');
			if(arData[0] == 'SUCCESS'){
				$('#transactionModal .modal-title').html(arData[1]);
				$('#transactionModal .modal-body').html(arData[2]);
				show_modal('transactionModal');
			} else {
				$('#transactionModal .modal-title').html('Transactions');
				$('#transactionModal .modal-body').html(arData[1]);
				show_modal('transactionModal');
			}
		}
	});
}

function addInstitutionAccounts()
{
	clearInterval(mfa_timer);
	
	$('#frmBankLogin .required').each(function(){
		validate_require_field(this);		
	});
	
	setTimeout(
	  function() 
	  {
	    if($('#frmBankLogin .has-error').length == 0)
		{
			show_container('popup-loading');
			$.ajax({
				type: "POST",
				url: JS_BASE_URL + "handle_ajax_request?p_func=LOGIN_WITH_INSTITUTION",
				data:$('#frmBankLogin').serialize(),
				beforeSend: function(){
					if($('#loginModal .alert-danger').length > 0)
						$('#loginModal .alert-danger').remove();
				},
				success: function(data){
					hide_container('popup-loading');
					arData = data.split('|||');
					if(arData[0] == 'SUCCESS'){
						hide_modal('loginModal');
						$('#institution_id').val(arData[1]);
						$('#account_id').val(arData[2]);
						$('#account_number').val(arData[3]);
						$('#statement_file').val(arData[4]);
						$('#frmAuthentication').submit();
					}else if(arData[0] == 'MFA'){
						$('#loginModal .modal-title').html(arData[1]);
						$('#loginModal .modal-body').html(arData[2]);
						show_modal('loginModal');
					    
					    setTimeout(function(){
							$('#popup-loading').width($('#loginModal .modal-dialog').width()-5).height($('#loginModal .modal-dialog').height()-105);
							validate_form_fields();
					    	$('[data-toggle="tooltip"]').tooltip();
						}, 200);	
					    
					    var ctr = 0;					    
						mfa_timer = setInterval(function(){
							current_time_s = parseInt($('#seconds').html());
							
							if(current_time_s == 0)
							{
								hide_modal('loginModal');
								clearInterval(mfa_timer);
								window.location = JS_BASE_URL + "/users/signup/link-your-bank";
							}
							else
							{
								current_time_s = current_time_s - 1;
								
								if(current_time_s < 10)
									current_time_s = '00' + current_time_s;	
								else if(current_time_s < 100)
									current_time_s = '0' + current_time_s;
			
								$('#seconds').html(current_time_s);
							}					
						}, 1000);
					} else {
						$('#frmBankLogin').before('<div class="alert alert-danger">' + arData[1] + '</div>');
					}
				},
				error: function (jqXHR, exception) {
			        var msg = '';
			        if (jqXHR.status === 0) {
			            msg = 'Not connect.\n Verify Network.';
			        } else if (jqXHR.status == 404) {
			            msg = 'Requested page not found. [404]';
			        } else if (jqXHR.status == 500) {
			            msg = 'Internal Server Error [500].';
			        } else if (exception === 'parsererror') {
			            msg = 'Requested JSON parse failed.';
			        } else if (exception === 'timeout') {
			            msg = 'Time out error.';
			        } else if (exception === 'abort') {
			            msg = 'Ajax request aborted.';
			        } else {
			            msg = 'Uncaught Error.\n' + jqXHR.responseText;
			        }
			        
			        $('#loginModal .modal-title').html('Something went wrong');
					$('#loginModal .modal-body').html('<h6 class="color-red">Something went wrong while processing your request. Please wait for a while, it will again make an attempt.</h6>');
					show_modal('loginModal');
					
			        setTimeout(function(){
				        window.location = JS_BASE_URL + "/users/signup/link-your-bank";
					}, 5000);
			    }
			});
		}
	  }, 500);	
}

function addInstitutionAccountsMFA()
{
	clearInterval(mfa_timer);
	
	$('#frmBankLogin .required').each(function(){
		validate_require_field(this);		
	});
	
	setTimeout(
	  function() 
	  {
	    if($('#frmBankLogin .has-error').length == 0)
		{
			show_container('popup-loading');
			$.ajax({
				type: "POST",
				url: JS_BASE_URL + "handle_ajax_request?p_func=LOGIN_WITH_INSTITUTION_MFA",
				data:$('#frmBankLoginMFA').serialize(),
				beforeSend: function(){
					if($('#loginModal .alert-danger').length > 0)
						$('#loginModal .alert-danger').remove();
				},
				success: function(data){
					hide_container('popup-loading');
					arData = data.split('|||');
					if(arData[0] == 'SUCCESS'){
						hide_modal('loginModal');
						$('#institution_id').val(arData[1]);
						$('#account_id').val(arData[2]);
						$('#account_number').val(arData[3]);
						$('#statement_file').val(arData[4]);
						$('#frmAuthentication').submit();
					}else if(arData[0] == 'MFA'){
						$('#loginModal .modal-title').html(arData[1]);
						$('#loginModal .modal-body').html(arData[2]);
						show_modal('loginModal');
						
						setTimeout(function(){
							$('#popup-loading').width($('#loginModal .modal-dialog').width()-5).height($('#loginModal .modal-dialog').height()-105);
							validate_form_fields();
					    	$('[data-toggle="tooltip"]').tooltip();
						}, 200);
						
						var ctr = 0;
						mfa_timer = setInterval(function(){
							current_time_s = parseInt($('#seconds').html());
							
							if(current_time_s == 0)
							{
								hide_modal('loginModal');
								clearInterval(mfa_timer);
								window.location = JS_BASE_URL + "/users/signup/link-your-bank";
							}
							else
							{
								current_time_s = current_time_s - 1;
								
								if(current_time_s < 10)
									current_time_s = '00' + current_time_s;	
								else if(current_time_s < 100)
									current_time_s = '0' + current_time_s;
			
								$('#seconds').html(current_time_s);
							}					
						}, 1000);	
					} else {
						$('#frmBankLoginMFA').before('<div class="alert alert-danger">' + arData[1] + '</div>');
					}
				},
				error: function (jqXHR, exception) {
			        $('#loginModal .modal-title').html('Something went wrong');
					$('#loginModal .modal-body').html('<h6 class="color-red">Something went wrong while processing your request. Please wait for a while, it will again make an attempt.</h6>');
					show_modal('loginModal');
					
			        setTimeout(function(){
				        window.location = JS_BASE_URL + "/users/signup/link-your-bank";
					}, 5000);
			    }
			});
		}
	  }, 500);	
}

function importCoustomerTransactions(idCustomer)
{
	show_modal('ts-loading');
	$('#ts-loading img').after('<h3 class="color-red">Please wait while importing transaction data...</h3>');
	$.ajax({
		type: "POST",
		url: JS_BASE_URL + "handle_ajax_request?p_func=IMPORT_CUSTOMER_TRANSACTIONS",
		data:'p_cid='+idCustomer,
		success: function(data){
			hide_modal('ts-loading');
		}
	});
}

function show_container(id, w, h)
{
	if(parseInt(w) > 0)
		$('#' + id).width(w+40);
		
	if(parseInt(h) > 0)
		$('#' + id).height(h-160);
		
	$('#' + id).show();
}

function hide_container(id)
{
	$('#' + id).hide();
}

// AJAX call for autocomplete 
$(document).ready(function(){
	header_h = $('header').height();
	footer_h = ($('footer').height()+40);
	container_h = ($('.main-section').height()+70); 
	window_h = $(window).height();
	//alert(header_h + ' | ' + footer_h + ' | ' + container_h + ' | ' + window_h);
	if((header_h + footer_h + container_h) < window_h){
		container_h = container_h + (window_h - (header_h + footer_h + container_h + 70));
		$('.main-section').height(container_h);
	}
	
	$("#search-box").keyup(function(e){
		if(parseInt(e.keyCode) != 40){
			keyword = $.trim($(this).val());
			if(keyword.length >= 3){
				$.ajax({
					type: "POST",
					url: JS_BASE_URL + "handle_ajax_request?p_func=SEARCH_INSTITUTE",
					data:'p_keyword='+keyword+'&p_show_all='+parseInt($('#show-all').val()),
					beforeSend: function(){
						//$("#search-box").attr('disablesd', true).addClass('loading');
					},
					success: function(data){
						$(".suggesstion-box").show();
						$(".suggesstion-box").html(data);
						//$("#search-box").removeAttr('disabled').removeClass('loading');
					}
				});
			}
		}
	});
	
	if($('#ts-loading').length > 0)
		$('#ts-loading').width($('.table-format2').width()).height($('.table-format2').height());
		
	/*$('.cant-find-it').hover(
		function(){
			$('.cant-find-it').parent().parent().parent().parent().after('<div class="cant-find-it-msg">Depending on your bank, Tally may be able to find your transit number for you. If we can\'t, we\'ll reach out to work with you to find it.</div>');
		},
		function(){
			$('.cant-find-it').parent().parent().parent().parent().next().remove();
		}
	);*/
	if($('.cant-find-it').length > 0){
		$('.cant-find-it').parent().parent().parent().parent().after('<div class="cant-find-it-msg">Depending on your bank, Tally may be able to find your transit number for you. If we can\'t, we\'ll reach out to work with you to find it.</div>');
        $('.modal-footer').css({fontSize:14});
	}
	
	 $('#selecctall').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.cbx1').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "cbx1"              
            });
        }else{
            $('.cbx1').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "cbx1"                      
            });        
        }
    });
});

function handle_suggestions_click(obj)
{
	if($("#search-box").hasClass('select-only')){
		$("#search-box").val($(obj).html());
		$("#search-id").val($(obj).attr('id'));
	} else {
		connect_with_bank($(obj).attr('id'));
	}
	$('.suggesstion-box').hide();
}

$(document).keydown(function(e){
	if($(".suggesstion-box ul li a").is(":focus")){
		if(parseInt(e.keyCode) == 40){
			active_elm = $(document.activeElement).parent().next().find('a');
			if(active_elm.length > 0){
				active_elm.focus();
				$("#search-box").val(active_elm.html());
			}
		}
		else if(parseInt(e.keyCode) == 38){
			active_elm = $(document.activeElement).parent().prev().find('a');
			if(active_elm.length > 0){
				active_elm.focus();
				$("#search-box").val(active_elm.html());
			}
		}
		else if(parseInt(e.keyCode) == 13) {
			active_elm = $(document.activeElement);
			$("#search-box").val(active_elm.html());
			$('.suggesstion-box').hide();
			
			if(!$("#search-box").hasClass('select-only')){
				connect_with_bank(active_elm.attr('id'));
			}
		}
	} else if($('#search-box').is(':focus') && parseInt(e.keyCode) == 40){
		active_elm = $(".suggesstion-box ul li").first().find('a');
		active_elm.focus();
		$('#search-box').val(active_elm.html());	
	}
});

function show_modal(id)
{
	$('#' + id).modal('show');
}

function hide_modal(id)
{
	$('#' + id).modal('hide');
}

function updateTransitNumber(idUser, szType, szNumber)
{
	$('#p_transit_number').val(szNumber);
	$('#p_id').val(idUser);
	$('#account-type').html(szType);
	$('#p_type').val(szType);
	$('#p_transit_number').parent().removeClass('has-error');
	$('.alert').remove();
	$('.help-block').remove();
	
	show_modal('updateTransitModal');
}

function updateInstitutionNumber(idInstitute, szNumber)
{
	$('#p_institution_number').val(szNumber);
	$('#p_institution_id').val(idInstitute);
	
	$('#p_institution_number').parent().removeClass('has-error');
	$('.alert').remove();
	$('.help-block').remove();
	
	show_modal('updateInstituteNumberModal');
}

function check_confirm(p_id, p_text, p_key, p_func)
{
	$('#confirmationModal #p_id').val(p_id);
	$('#confirmationModal #p_func').val(p_key);
	$('#confirmationModal #p_re_func').val('');
	$('#confirmationModal #p_msg').val(p_text);
	$('#confirmationModal #p_sub_func').val(p_func);
	$('#confirmationModal .confirm-msg').html(p_text);
	$('#confirmationModal .confirm-type').html(p_key);
	remove_all_notification('p_confirm', true);
	
	show_modal('confirmationModal');
}
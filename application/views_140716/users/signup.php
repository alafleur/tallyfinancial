<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="main-section">
	<div class="container">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<?php if($iSignUpStep == 5){?>
				<h1 class="align-center">Registration Completed</h1>
				<br>
				<div class="login-form">
					<div class="alert alert-success">
						Your sign up is successfully completed. Your account is currently being verified. You'll be notified via email and text message when the verification is complete or if we have questions for you.
					</div>
					<p>
						<a href="<?=__BASE_URL__?>/users/login">Log into your Tally Account</a>
					</p>
				</div>
				<?php } else if($iSignUpStep == 4){?>
				<h1 class="align-center">Enter your account transit number to complete your account information</h1>
				<br>
				<div class="login-form">
					<form id="frmVerifyBankingInfo" method="POST" action="<?=__SECURE_BASE_URL__?>/users/signup/account-information" enctype="multipart/form-data" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-4">Financial institution:</label>
							<div class="col-sm-8"><?=$arInstitution[0]['szName']?></div>
						</div>																	
						
						<div class="form-group">
							<label class="col-sm-4">Account number:</label>
							<div class="col-sm-8"><?=$szFinicityAccountNumber?></div>
						</div>
							
						<div class="form-group<?=(!empty($arErrorMessages['p_transit_number']) ? ' has-error' : '')?>">
							<label class="col-sm-4">Transit number:</label>
							<div class="col-sm-8">
								<input type="text" placeholder="Transit number" name="arBanking[p_transit_number]" value="<?=sanitize_post_field_value($_POST['arBanking']['p_transit_number'])?>" id="p_transit_number" class="form-control required transit-number">
								<?=(!empty($arErrorMessages['p_transit_number']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_transit_number']}</span>" : "")?>
							</div>
						</div>
						
						<!-- <div class="form-group<?=(!empty($arErrorMessages['p_verification_file']) ? ' has-error' : '')?>">
							<label class="col-sm-4">Verification document:</label>
							<div class="col-sm-8">						
								<input type="file" name="p_verification_file" <?php if(!empty($arErrorMessages['p_verification_file'])){?>onchange="$(this).parent().parent().removeClass('has-error');$(this).next().remove();"<?php }?>>
								<?=(!empty($arErrorMessages['p_verification_file']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_verification_file']}</span>" : "")?>
							</div>						
						</div>-->
						
						<div class="row clearfix">
							<div class="col-sm-6">
								<div class="form-group">
									<div class="col-xs-12">
										<input type="submit" name="arBanking[p_submit]" value="Submit" class="btn btn-full btn-form-submit">
										<input type="hidden" name="arBanking[p_id]" value="<?=$id?>">
									</div>
								</div>							
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<div class="col-xs-12">
										<input type="submit" name="arBanking[p_cant_find]" value="Can't find it" class="btn btn-full cant-find-it">
									</div>
								</div>
							</div>
						</div>
					</form>
					
					<!-- <p>
						<a target="_blank" href="<?=__BASE_URL__?>/articles/faqs/where-do-I-find-my-banking-information">FAQs on banking information</a><br>
						<a target="_blank" href="<?=__BASE_URL__?>/articles/faqs/how-do-i-verify-my-banking">How do I verify my bank account?</a>
					</p>-->
				</div>
				<br>
				<h3>Where can I find my transit number?</h3>
				<h6>If you have access to a cheque from your account all the required information (institution number, account number and transit number) will be printed on the bottom of the cheque.</h6>
				<div class="form-group">
					<img src="<?=__BASE_ASSETS_URL__?>/images/account-info.jpg" alt="Account Info">
				</div>				
				<?php } else if($iSignUpStep == 3){?>
				<div id="loginModal" class="modal fade" role="dialog">
			      <div class="modal-dialog">
				    <div class="loading-popup" id="popup-loading">
					  <img src="<?=__BASE_ASSETS_URL__?>/images/loading.gif" alt="Loading">
				  	</div>				  			
				    <!-- Modal content-->
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h3 class="modal-title"></h3>
				      </div>
				      <div class="modal-body">
				      </div>
				      <div class="modal-footer">
				      	<i class="fa fa-lock"></i> HTTPS Secure
				      </div>
				    </div>				
				  </div>
				</div>
				
				<h1 class="align-center">Link your bank account</h1>
				<br>
				<div class="login-form">
					<div class="loading-popup" id="pp-loading">
						<img src="<?=__BASE_ASSETS_URL__?>/images/loading.gif" alt="Loading">
					</div>
					
					<form id="frmAuthentication" method="POST" action="<?=__SECURE_BASE_URL__?>/users/signup/authenticate">
						<input type="hidden" name="arAuth[institution_id]" id="institution_id" value="">
						<input type="hidden" name="arAuth[account_id]" id="account_id" value="">
						<input type="hidden" name="arAuth[account_number]" id="account_number" value="">
						<input type="hidden" name="arAuth[statement_file]" id="statement_file" value="">
						<input type="hidden" name="arAuth[p_id]" value="<?=$id?>">
					</form>
					
					<?php if(!empty($arMainInstitutions)){?>
					<ul class="ins-list">
						<?php foreach($arMainInstitutions as $institute){?>
						<li><a href="javascript:void(0);" onclick="connect_with_bank('<?=$institute['id']?>');" title="<?=$institute['szName']?>"><img src="<?=__BASE_ASSETS_URL__?>/images/icons/<?=$institute['szLogoFile']?>" alt="<?=$institute['szName']?>"></a></li>
						<?php }?>
					</ul>
					<?php }?>
					
					<div class="ins-search-box">
						<h3>We support hundreds of banks. Find yours-</h3>
						<div class="form-group">
							<input type="text" placeholder="Your bank's name" id="search-box" class="form-control">
							<input type="hidden" id="show-all" value="0">
							<div class="suggesstion-box"></div>
						</div>
					</div>				
				</div>				
				<?php } else if($iSignUpStep == 2 && (int)$id > 0){if($verify_mobile){?>
				<h1 class="align-center"><i class="fa fa-mobile"></i> Check Your Messages</h1>
				<br>
				<h6 class="align-center">Please submit the 6-digit confirmation code we've sent to:</h6>
				<h6 class="align-center"><strong><?=trim($arMap['szMobilePhone'])?></strong></h6>
				<div class="login-form">
					<form name="frmRegister" id="frmRegister" action="<?=__SECURE_BASE_URL__?>/users/signup/mobile-number-confirmation" method="post">						
						<div class="form-group<?=(!empty($arErrorMessages['p_vcode']) ? ' has-error' : '')?>">
							<input type="text" name="arRegister21[p_vcode]" id="p_vcode" placeholder="Verification Code" class="form-control required" value="<?=sanitize_post_field_value($_POST['arRegister21']['p_vcode'])?>">
							<?=(!empty($arErrorMessages['p_vcode']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_vcode']}</span>" : "")?>
						</div>
						
						<div class="form-group">
							<input type="submit" name="arRegister21[p_register]" value="Submit Code" class="btn btn-full btn-form-submit">
							<input type="hidden" name="arRegister21[p_id]" value="<?=$id?>">
						</div>
						
						<p><a href="<?=__SECURE_BASE_URL__?>/users/signup/mobile-number-resend-code">Re-send verification code on <?=trim($arMap['szMobilePhone'])?>!</a></p>
						<p>Is <?=trim($arMap['szMobilePhone'])?> not your correct phone number? <a href="<?=__SECURE_BASE_URL__?>/users/signup/mobile-number">Go back and fix it.</a></p>
					</form>
				</div>
				<?php }else{?>
				<h1 class="align-center"><i class="fa fa-mobile"></i> What's your mobile phone number?</h1>
				<?php if((int)$vcode_expired == 1){?>
				<br>
				<div class="alert alert-danger">Your previous verification session has expired, try again by sending new code!</div>
				<?php }?>
				<br>
				<h6 class="align-center">Tally is designed to work with you primarily through text messages. Please provide your mobile phone number so we can reach you.</h6>
				<div class="login-form">
					<form name="frmRegister" id="frmRegister" action="<?=__SECURE_BASE_URL__?>/users/signup/mobile-number" method="post">						
						<div class="form-group<?=(!empty($arErrorMessages['p_mobilephone']) ? ' has-error' : '')?>">
							<input type="text" name="arRegister2[p_mobilephone]" id="p_mobilephone" placeholder="Example: 251-333-9387" class="form-control required">
							<?=(!empty($arErrorMessages['p_mobilephone']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_mobilephone']}</span>" : "")?>
						</div>
						
						<div class="form-group">
							<input type="submit" name="arRegister2[p_register]" value="Continue" class="btn btn-full btn-form-submit">
							<input type="hidden" name="arRegister2[p_id]" value="<?=$id?>">
						</div>
					</form>
				</div>
				<?php }}else{?>
				<h1 class="align-center">Sign up for your Tally Account</h1>
				<?php if($iFinicityAddCustomerFailed){?>
				<br>
				<div class="alert alert-danger">There is some problem to add your account, Please try again after some time. If problem persists, <a href="#">contact us</a>.</div>
				<?php }?>
				<div class="login-form">
					<form name="frmRegister" id="frmRegister" action="<?=__SECURE_BASE_URL__?>/users/signup" method="post" autocomplete="off">
						
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group<?=(!empty($arErrorMessages['p_fname']) ? ' has-error' : '')?>">
									<input type="text" name="arRegister[p_fname]" id="p_fname" placeholder="First name" class="form-control required" value="<?=sanitize_post_field_value($_POST['arRegister']['p_fname'])?>">
									<?=(!empty($arErrorMessages['p_fname']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_fname']}</span>" : "")?>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group<?=(!empty($arErrorMessages['p_lname']) ? ' has-error' : '')?>">
									<input type="text" name="arRegister[p_lname]" id="p_lname" placeholder="Last name" class="form-control required" value="<?=sanitize_post_field_value($_POST['arRegister']['p_lname'])?>">
									<?=(!empty($arErrorMessages['p_lname']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_lname']}</span>" : "")?>
								</div>
							</div>							
						</div>
						
						<div class="form-group<?=(!empty($arErrorMessages['p_email']) ? ' has-error' : '')?>">
							<input type="email" name="arRegister[p_email]" id="p_email" placeholder="Email address" class="form-control required unique-email" value="<?=sanitize_post_field_value($_POST['arRegister']['p_email'])?>" autocomplete="off">
							<?=(!empty($arErrorMessages['p_email']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_email']}</span>" : "")?>
						</div>
							
						<div class="form-group<?=(!empty($arErrorMessages['p_re_email']) ? ' has-error' : '')?>">
							<input type="text" name="arRegister[p_re_email]" id="p_re_email" placeholder="Re-enter email" class="form-control required re-match" autocomplete="off">
							<?=(!empty($arErrorMessages['p_re_email']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_re_email']}</span>" : "")?>
						</div>
						
						<div class="form-group<?=(!empty($arErrorMessages['p_password']) ? ' has-error' : '')?>">
							<input type="password" name="arRegister[p_password]" id="p_password" placeholder="Password" class="form-control required min-length" autocomplete="off">
							<?=(!empty($arErrorMessages['p_password']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_password']}</span>" : "")?>							
						</div>
						
						<div class="form-group<?=(!empty($arErrorMessages['p_re_password']) ? ' has-error' : '')?>">
							<input type="password" name="arRegister[p_re_password]" id="p_re_password" placeholder="Re-enter password" class="form-control required re-match" autocomplete="off">
							<?=(!empty($arErrorMessages['p_re_password']) ? "<span class=\"help-block pull-left\"><i class=\"fa fa-times-circle\"></i> {$arErrorMessages['p_re_password']}</span>" : "")?>							
						</div>
						
						<div class="form-group">
							<input type="submit" name="arRegister[p_register]" value="Sign Up" class="btn btn-full btn-form-submit1">
						</div>

						<p>
							By continuing, I agree to Tally's <a href="#">Terms of Use</a>, <a href="">E-Sign Consent</a>, <a href="">ACH Authorization</a> and <a href="">Privacy Policy</a>. 
						</p>
						
						<input type="hidden" id="p_password_minlength" value="6">
						<input type="hidden" id="p_password_maxlength" value="32">
					</form>
				</div>
				<?php }?>
			</div>
		</div>
	</div>
</section>
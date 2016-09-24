<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
				<div class="left-menu">
					<ul>
						<li<?=($active_menu == "" || $active_menu == "dashboard" ? ' class="active"' : '')?>><a href="<?=__SECURE_BASE_URL__?>/users/dashboard">Dashboard</a></li>
						<li<?=($active_menu == "commands" ? ' class="active"' : '')?>><a href="<?=__SECURE_BASE_URL__?>/users/commands">Commands</a></li>
						<?php if ($aggregationStatusCode != 0) { ?>
							<li><a class="btn btn-danger" onclick="connect_with_bank('<?php echo $idFinicityInstitution; ?>')"  data-toggle="modal" href="#">Reconnect Your account</a></li>
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
						<?php } ?>	
						<li<?=($active_menu == "saving" ? ' class="active"' : '')?>><a href="<?=__SECURE_BASE_URL__?>/users/saving-account">External Savings Account</a></li>
						<li<?=($active_menu == "help" ? ' class="active"' : '')?>><a href="<?=__SECURE_BASE_URL__?>/users/help">Help</a></li>
						<li><a href="<?=__SECURE_BASE_URL__?>/users/logout">Logout (<?=$szFirstName?>)</a></li>		
					</ul>
				</div>
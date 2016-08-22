<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>				<?php if($show_leftmenu){?>
					</div>
				</div>
				<?php }?>
			</div>
		</section>
		
		<footer id="footer">
			<div class="container">
				<div class="copyright">
					&copy; Copyright Tally Financial Technologies, Inc <?=date('Y')?> - <a href="#">Terms & Conditions</a> - <a href="#">Privacy Policy</a> - <a href="#">E-Sign Consent</a>
				</div>
			</div>
		</footer>				
		
		<!-- JavaScript libs are placed at the end of the document so the pages load faster -->
		<script>var JS_BASE_URL = '<?=__BASE_URL__?>/';</script>		
		<script src="<?=__BASE_ASSETS_URL__?>/js/jquery.min.js"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/bootstrap.min.js"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/jquery.autocomplete.min.js"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/bootstrap-datepicker.js"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/functions.js?<?=filemtime(__APP_PATH_ASSETS__."/js/functions.js")?>"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/validate.js?<?=filemtime(__APP_PATH_ASSETS__."/js/validate.js")?>"></script>		
		<script type="text/javascript">
			validate_form_fields();
			
			$(function () {
		        $('.datepicker').datepicker();
		    });
		    
		    <?php if($show_transit_modal){?>
			show_modal('updateTransitModal');
			<?php }if($show_institution_number_modal){?>
			show_modal('updateInstituteNumberModal');
			<?php } if($confirm_error){?>
			show_modal('confirmationModal');
			<?php }?>
		</script>
	</body>
</html>
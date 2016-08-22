<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
		<footer id="footer">
			<div class="container">
				<div class="align-center">
					<i class="fa fa-lock"></i> HTTPS SECURE
				</div>
			</div>
		</footer>				
		
		<!-- JavaScript libs are placed at the end of the document so the pages load faster -->
		<script>var JS_BASE_URL = '<?=__BASE_URL__?>/';</script>		
		<script src="<?=__BASE_ASSETS_URL__?>/js/jquery.min.js"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/bootstrap.min.js"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/jquery.autocomplete.min.js"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/functions.js?<?=filemtime(__APP_PATH_ASSETS__."/js/functions.js")?>"></script>
		<script src="<?=__BASE_ASSETS_URL__?>/js/validate.js?<?=filemtime(__APP_PATH_ASSETS__."/js/validate.js")?>"></script>
		<script type="text/javascript">
			validate_form_fields();
			<?php if($iLoginStep == 1){?>
			connect_with_bank('<?=$idConnectInstitution?>');
			<?php } else if($iLoginStep == 2){?>
			activateCustomerAccount('<?=$idConnectInstitution?>');
			<?php }?>
		</script>
	</body>
</html>
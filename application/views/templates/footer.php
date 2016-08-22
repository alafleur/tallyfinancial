<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
		<footer id="footer">
			<div class="container">
				<div class="copyright">
					&copy; Copyright Tally Financial Technologies, Inc <?=date('Y')?> - <a href="#">Terms & Conditions</a> - <a href="#">Privacy Policy</a> - <a href="#">E-Sign Consent</a>
				</div>
			</div>
		</footer>				
		
		<!-- JavaScript libs are placed at the end of the document so the pages load faster -->
		<script>var JS_BASE_URL = '<?=__BASE_URL__?>/';</script>		
		<script src="<?=asset_url()?>/js/jquery.min.js"></script>
		<script src="<?=asset_url()?>/js/bootstrap.min.js"></script>
		<script src="<?=asset_url()?>/js/jquery.autocomplete.min.js"></script>
		<script src="<?=asset_url()?>/js/functions.js?<?=filemtime(assets_path()."js/functions.js")?>"></script>
		<script src="<?=asset_url()?>/js/validate.js?<?=filemtime(assets_path()."js/validate.js")?>"></script>		
		<script type="text/javascript">
			validate_form_fields();
			$(document).ready(function(){
			    $('[data-toggle="tooltip"]').tooltip();
			});
		</script>
	</body>
</html>
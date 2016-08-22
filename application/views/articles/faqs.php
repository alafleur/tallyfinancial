<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section class="main-section">
	<div class="container">
		<?php if($arg1 == 'where-do-I-find-my-banking-information'){?>
		<h1>Where do I find my banking information?</h1>
		<br>
		<h6>If you have access to a cheque from your account all the required information (institution number, account number and transit number) will be printed on the bottom of the cheque.</h6>
		<div class="form-group">
			<img src="<?=__BASE_ASSETS_URL__?>/images/account-info.jpg" alt="Account Info">
		</div>
		<?php } else if($arg1 == 'how-do-i-verify-my-banking'){?>
		<h1>How do I verify my bank account?</h1>
		<br>
		<p>Tally asks clients to verify bank accounts to make funds transfers to and from Tally. For clients with non-registered (taxable) investment accounts, banking verification is also required to meet Canadian anti-money laundering regulations.</p>
		<p>Here we have given detailed information to verify your Tangerine chequing or savings account with Tally:</p>
		<ul>
			<li>Image of <a href="#Void%20Cheque">Void Cheque</a> (registered accounts only)</li>
			<li>Image of <a href="#Cleared%20Cheque">Cleared Cheque</a></li>
			<li><a href="#Bank%20Screenshot">Bank Screenshot</a> with your full name, bank logo, transit number, account number, and account balance (desktop, not mobile).</li>
			<li><a href="#Bank%20Statement">Bank Statement</a></li>
		</ul>
		<p><a href="<?=__SECURE_BASE_URL__?>/users/signup/verify-banking-info">Upload the document of your choice via the website</a> or submit it via email to <a href="mailto:support@tally.com">support@tally.com</a>!</p>
		<p><a name="Void%20Cheque"></a></p>
		<h3><span class="wysiwyg-font-size-large">Void Cheque</span></h3>
		<p>You can use a void cheque or stamped Direct Deposit form from your Tangerine account. Please note that this form of banking verification suffices for registered account types only. For eligible non-registered banking verification, one of the options below are necessary.</p>
		<p><a name="Cleared%20Cheque"></a></p>
		<h3><span class="wysiwyg-font-size-large">Cleared Cheque</span></h3>
		<p>A cleared cheque is a cheque that has been processed through and certified by your bank. You can find a copy of this by looking through your transaction history in your online banking portal and searching for a cheque that's been processed. You can download an image by following these steps:</p>
		<ol>
			<li>Login to your <span class="wysiwyg-underline"><a href="https://secure.tangerine.ca/web/InitialTangerine.html?command=displayLogin&amp;device=web&amp;locale=en_CA">Tangerine online banking</a></span> </li>
			<li>Look through your transactions to find a cheque that has been cashed against your account</li>
			<li>You should be able to click on the cheque transaction</li>
			<li>The image that is loaded is a cleared cheque. Save the image (both front and back)</li>
			<li><a href="<?=__SECURE_BASE_URL__?>/users/signup/verify-banking-info" target="_blank">Upload</a> both the front and back to your Tally dashboard or send it to us at <a href="mailto:support@tally.com">support@tally.com</a> </li>
		</ol>
		<h3><a name="Bank%20Statement"></a></h3>
		<p class="p1"><span class="wysiwyg-font-size-large"><strong><span class="s1">Bank Statement</span></strong></span></p>
		<ol>
			<li>Login to your <span class="wysiwyg-underline"><a href="https://secure.tangerine.ca/web/InitialTangerine.html?command=displayLogin&amp;device=web&amp;locale=en_CA">Tangerine online banking</a></span> </li>
			<li>Go to 'My Inbox' </li>
			<li>Click on the particular month's statement you wish to upload (the subject line should be 'Your X month chequing statement is ready'</li>
			<li>Download the Statement </li>
			<li><a href="<?=__SECURE_BASE_URL__?>/users/signup/verify-banking-info" target="_blank">Upload it</a> to your Tally account</li>
		</ol>
		<p><a name="Bank%20Screenshot"></a></p>
		<h3><span class="wysiwyg-font-size-large">Bank Screenshot</span></h3>
		<ol>
			<li>Login to your <span class="wysiwyg-underline"><a href="https://secure.tangerine.ca/web/InitialTangerine.html?command=displayLogin&amp;device=web&amp;locale=en_CA">Tangerine online banking</a></span> </li>
			<li>The screen which you land on when you first login shows all the required information. The top left will have the bank logo and your name, beside the account types it will show transit, account number, and balance</li>
			<li>Take a screenshot by pressing Print Screen on your Windows keyboard, or by pressing Cmd+Shift+3 on a Mac</li>
			<li><a href="<?=__SECURE_BASE_URL__?>/users/signup/verify-banking-info" target="_blank">Upload it</a> to your Tally account</li>
		</ol>
		<p class="p1"><span class="s1">Here's what an acceptable online banking screenshot from Tangerine should look like:</span></p>
		<p class="p1"><span class="s1"><img src="<?=__BASE_ASSETS_URL__?>/images/tangerine.png" alt="" style="width:100%"></span></p>
		<p class="p1"> </p>
		<p class="p1"><span class="s1">*Please note that if you click into a specific account you lose some of the required information</span></p>
		<?php }?>
	</div>
</section>
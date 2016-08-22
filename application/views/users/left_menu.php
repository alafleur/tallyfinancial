<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
				<div class="left-menu">
					<ul>
						<li<?=($active_menu == "" || $active_menu == "dashboard" ? ' class="active"' : '')?>><a href="<?=__SECURE_BASE_URL__?>/users/dashboard">Dashboard</a></li>
						<li<?=($active_menu == "commands" ? ' class="active"' : '')?>><a href="<?=__SECURE_BASE_URL__?>/users/commands">Commands</a></li>
						<li<?=($active_menu == "saving" ? ' class="active"' : '')?>><a href="<?=__SECURE_BASE_URL__?>/users/saving-account">External Savings Account</a></li>
						<li<?=($active_menu == "help" ? ' class="active"' : '')?>><a href="<?=__SECURE_BASE_URL__?>/users/help">Help</a></li>
						<li><a href="<?=__SECURE_BASE_URL__?>/users/logout">Logout (<?=$szFirstName?>)</a></li>
					</ul>
				</div>
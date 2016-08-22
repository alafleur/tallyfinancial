<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$admin_data = $obj->session->userdata('arr');?>
<h1 class="align-center">Welcome <?=$admin_data['login']?></h1>
<?php if($obj->session->userdata('reset_pass_success_msg')){?>
<div class="alert alert-success"><?=$obj->session->userdata('reset_pass_success_msg')?></div>
<?php $obj->session->unset_userdata('reset_pass_success_msg');}?>
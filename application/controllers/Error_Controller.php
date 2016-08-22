<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_Model');
	}
	
	public function index()
	{
		$data['is_user_login'] = is_user_login($this);
		$data['szMetaTagTitle'] = "Error";

        $this->load->view('templates/header', $data);
        $this->load->view('error');
        $this->load->view('templates/footer');
	}
}
?>
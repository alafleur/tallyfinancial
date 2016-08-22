<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_Model');
		
		check_for_signing_process($this, '');
	}
	
	public function index()
	{
		$data['is_user_login'] = is_user_login($this);
		$data['szMetaTagTitle'] = "Home";

        $this->load->view('templates/header', $data);
        $this->load->view('home');
        $this->load->view('templates/footer');
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_Model');
	}
	
	public function index()
	{
		$data['is_user_login'] = is_user_login($this);
		$data['szMetaTagTitle'] = "";

        $this->load->view('templates/header', $data);
        $this->load->view('articles/article', $data);
        $this->load->view('templates/footer');
	}
	
	public function faqs($arg1='')
	{		
		$data['is_user_login'] = is_user_login($this);
		$data['arg1'] = $arg1;
		
		if($arg1 == 'where-do-I-find-my-banking-information')
			$data['szMetaTagTitle'] = "Where do I find my banking information?";
		else if($arg1 == 'how-do-i-verify-my-banking')
			$data['szMetaTagTitle'] = "How do I verify my bank account?";

        $this->load->view('templates/header', $data);
        $this->load->view('articles/faqs', $data);
        $this->load->view('templates/footer');
	}
}

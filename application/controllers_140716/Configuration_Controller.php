<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuration_Controller extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->is_admin_login = false;
		
		$this->load->model('Configuration_Model');	
		$this->load->model('Admin_Model');
		$this->load->model('User_Model');
		
		#check for admin login
		if (!$this->Admin_Model->checkAdminLogin())
		{
			$this->session->set_userdata('redir_url', __BASE_URL__ . str_replace("/tally", "", $_SERVER['REQUEST_URI']));
			header( 'Location:'.__BASE_ADMIN_URL__ . "/login");
			die();
		}
		else
		{
			$this->is_admin_login = true;
		}
	}
	
	function index()
	{
		$data['is_admin_login'] = $this->is_admin_login;
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "configurations";
		$data['szMetaTagTitle'] = "Configurations";
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/configurations/list', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function constants($arg1='', $arg2='')
	{
		$data['p_func'] = $arg1;
		$data['arg2'] = $arg2;
		$data['is_admin_login'] = $this->is_admin_login;
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "configurations";
		$data['szMetaTagTitle'] = "Global Values";
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/configurations/constants', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function templates($arg1='', $arg2='')
	{
		$data['arg1'] = $arg1;
		$data['arg2'] = $arg2;
		$data['is_admin_login'] = $this->is_admin_login;
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "sms-templates";
		$data['szMetaTagTitle'] = "SMS Templates";
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/configurations/templates', $data);
        $this->load->view('templates/admin_footer', $data);
	}
}
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Tempo_Controller
 * @property Finicity_Model $Finicity_Model
 */
class Tempo_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_Model');
		$this->load->model('Finicity_Model');
		$this->load->model('Configuration_Model');

        if(!in_array($_SERVER['REMOTE_ADDR'], array("0.0.0.0", "127.0.0.1", "125.20.49.52"))){
            die("Unauthorized access");
        }
	}
	function add(){
		$request = array("szEmail" => "sachinchawlamca@gmail.com", "szFirstName" => "Sachin", "szLastName" => "Chawla");
		$response = $this->Finicity_Model->addCustomertToFinicity($request);
		
		var_dump($response);
		exit(0);
	}

	function customers(){
        $array = $this->Finicity_Model->listCustomersFromFinocity(null, 1, 1, null, 'sachinchawlamca@gmail.com');

		var_dump($array);
	}

	function deleteallcustomers(){
	    if(false) {
            $list = $this->Finicity_Model->listCustomersFromFinocity(null, 1, 1000);
            if ($list !== FALSE && $list['@attributes']['displaying'] > 0) {
                $displaying = $list['@attributes']['displaying'];
                if ($displaying == 1) $list['customer'] = array($list['customer']);
                foreach ($list['customer'] as $customer) {
                    $this->Finicity_Model->deleteCustomerFromFinicity($customer['id']);
                    echo "Deleted Customer ID: " . $customer['id'] . "<br />";
                }
            }
        }
    }
}
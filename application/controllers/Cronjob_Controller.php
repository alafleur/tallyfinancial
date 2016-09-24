<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cronjob_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		echo "Please specify a valid method";
	}
	
	function institutions()
	{
		$this->load->model('Finicity_Model');
		$array = $this->Finicity_Model->getInstitutions();
		
		if(!empty($array['institution']))
		{
			// first clear the temp table
			$query = "TRUNCATE TABLE tbl_institutions_temp";
			$this->Finicity_Model->exeSQL($query);
			
			foreach($array['institution'] as $institution)
			{
				$query = "
					INSERT INTO
						tbl_institutions_temp
					(
						id,
						szName,
						szAccountTypeDescription,
						szURLHomeApp,
						szURLLogonApp,
						szPhone,
						szCurrency,
						szEmail,
						szSpecialText,
						szAddressLine1,
						szAddressLine2,
						szCity,
						szState,
						szPostalCode,
						szCountry
					)
					VALUES
					(
						" . (int)$institution['id'] . ",
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['name'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['accountTypeDescription'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['urlHomeApp'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['urlLogonApp'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['phone'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['currency'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['email'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['specialText'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['address']['addressLine1'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['address']['addressLine2'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['address']['city'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['address']['state'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['address']['postalCode'])) . "',
						'" . $this->Finicity_Model->sql_real_escape_string(trim($institution['address']['country'])) . "'
					)
				";
				//echo $query;die;
				$this->Finicity_Model->exeSQL($query);
			}
				
			// update for main institutes
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  1, szLogoFile = 'rbc.png' WHERE id = 1411";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  2, szLogoFile = 'td.png' WHERE id = 1492";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  3, szLogoFile = 'scotiabank.jpg' WHERE id = 3406";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  4, szLogoFile = 'bmo.jpg' WHERE id = 3415";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  5, szLogoFile = 'cibc.png' WHERE id = 3417";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  6, szLogoFile = 'nbc.png' WHERE id = 13063";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  7, szLogoFile = 'tangerine.png' WHERE id = 5691";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  8, szLogoFile = 'bofa.png' WHERE id = 14007";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  9, szLogoFile = 'us.png' WHERE id = 676";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  10, szLogoFile = 'chase.png' WHERE id = 5";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  11, szLogoFile = 'wells.png' WHERE id = 31";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  12, szLogoFile = 'citi.png' WHERE id = 5819";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  13, szLogoFile = 'usaa.png' WHERE id = 2875";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  14, szLogoFile = 'capone360.png' WHERE id = 3038";
			$this->Finicity_Model->exeSQL($query);
			
			$query = "UPDATE tbl_institutions_temp SET isMain = 1, iOrder =  15, szLogoFile = 'pnc.png' WHERE id = 2866";
			$this->Finicity_Model->exeSQL($query);
			
			
			// Now clear the main table
			$query = "TRUNCATE TABLE tbl_institutions";
			$this->Finicity_Model->exeSQL($query);
			
			// copy data from temp to main
			$query = "INSERT INTO tbl_institutions SELECT * FROM tbl_institutions_temp";
			$this->Finicity_Model->exeSQL($query);
			
			// Again clear the temp
			$query = "TRUNCATE TABLE tbl_institutions_temp";
			$this->Finicity_Model->exeSQL($query);
		}
		else if($this->Finicity_Model->szResolveError != '')
		{
			echo $this->Finicity_Model->szResolveError;
		}
		else
		{
			echo "Something went wrong, try again.";
		}
	}
	
	function transactions()
	{		
		getFinicityTransactions($this);
	}
	
	function calculations($customer_id=0, $opening_balance=0, $today='')
	{
		$this->load->model('Cronjob_Model');
		$this->Cronjob_Model->getCalculations($customer_id, $opening_balance, $today);
	}
	
	function savings($today='')
	{
		$this->load->model('Cronjob_Model');
		$this->Cronjob_Model->getSavingTransactions($today);
	}
	
	function refresh_accounts()
	{		
		refreshFinicityAccounts($this);
	}
}
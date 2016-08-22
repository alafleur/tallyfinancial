<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_Model');
		$this->load->model('Admin_Model');
		
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
	
	public function index()
	{
		$data['is_admin_login'] = $this->is_admin_login;
		$data['show_leftmenu'] = true;
		$data['szMetaTagTitle'] = "Reports";

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/reports/list', $data);
        $this->load->view('templates/admin_footer');
	}
	
	public function users($arg1='', $arg2='')
	{
		$data['is_admin_login'] = $this->is_admin_login;
		$data['show_leftmenu'] = true;
		$data['szMetaTagTitle'] = "Reports";
		
		if(trim($arg1) == "export")
		{			
			$data['arUsers'] = $this->User_Model->getCustomers();
			if(!empty($data['arUsers']))
			{
				$csv_string = "";
				$is_first_found = false;
				foreach($data['arUsers'] as $user)
				{
					if(!$is_first_found)
					{
						$is_first_found = true;
						$csv_string .= "Customer ID, Name, Email, Mobile Phone, Chequing A/C Institution, Chequing Transit #, Chequing A/C #, Chequing A/C Balance, Savings A/C Institution, Savings A/C Transit #, Savings A/C #, Savings A/C Verified, Account Verified, Tally A/C Balance, External A/C Balance, Total Tally Savings, Avg Savings Amount, # of Transactions, Abs Minimum Balance, Surplus Deficit Rate, If Deficit Rate, First Saving On, Days from First Saving, Avg Week1, Avg Week2, Avg Week3, Avg Week4, Avg Week5, Avg Income Monthly, Avg Income Weekly, Max Expense Avg, Max Expense Coverage\r\n";
					}
					
					// get latest chequing balance
					$fChequingbalance = $this->User_Model->getChequingBalance($user['idFinicity']);
					
					$fTotalSaving = 0;
					$fTotalTransfers = 0;
					$fTallyBalance = 0;
					
					// get all saving transactions
					$dtFirstTransaction = '';
					$tFirstTransaction = '';
					$this->load->model('Configuration_Model');
					$arTransactions = $this->Configuration_Model->getCustomerBiMonthlySavingTransactions($user['idFinicity']);
					$iTotalTransactions = count($arTransactions);
					if(!empty($arTransactions))
					{
						foreach($arTransactions as $transaction)
						{
							if($dtFirstTransaction == '')
							{
								$dtFirstTransaction = date("d/m/Y", strtotime($transaction['dtCreatedOn']));
								$tFirstTransaction = ceil(abs(time() - strtotime($transaction['dtCreatedOn'])) / 86400);
							}
							$fTotalSaving += $transaction['fSavingAmount'];
						}
						if($fTotalSaving != 0)
							$fTotalSaving = abs($fTotalSaving);
					}
					
					// get old saving transfers
					$arTransfers = $this->User_Model->getCustomerSavingsTransfers($user['idFinicity']);
					$iTotalTransfers = count($arTransfers);
					if(!empty($arTransfers))
					{
						foreach($arTransfers as $transfer)
						{
							$fTotalTransfers += $transfer['fAmount'];
						}
					}
					
					// get tally balance
					if($fTotalSaving > $fTotalTransfers)
					{
						$fTallyBalance = $fTotalSaving - $fTotalTransfers;
					}
					
					// get last months calculations
					$arLastMonthCalculations = getPreviousMonthsCalculations($user['idFinicity'], date("n"), date("Y"), $this->Configuration_Model, 1);
					$fWeek1ExpenseAverage = format_number($arLastMonthCalculations[0]['fWeek1ExpenseAverage'], false);
					$fWeek2ExpenseAverage = format_number($arLastMonthCalculations[0]['fWeek2ExpenseAverage'], false);
					$fWeek3ExpenseAverage = format_number($arLastMonthCalculations[0]['fWeek3ExpenseAverage'], false);
					$fWeek4ExpenseAverage = format_number($arLastMonthCalculations[0]['fWeek4ExpenseAverage'], false);
					$fWeek5ExpenseAverage = format_number($arLastMonthCalculations[0]['fWeek5ExpenseAverage'], false);

					$fAverageIncomeMonthly = format_number($arLastMonthCalculations[0]['fAverageIncomeMonthly'], false);
					$fAverageIncomeWeekly = format_number($arLastMonthCalculations[0]['fAverageIncomeWeekly'], false);
					
					$fMaxExpenseAverage = format_number($arLastMonthCalculations[0]['fMaxExpenseAverage'], false);
					$fMaxExpenseCover = format_number($arLastMonthCalculations[0]['fMaxExpenseCover'], false);
					
					$fTotalSaving = format_number($fTotalSaving, false);
					$fTotalTransfers = format_number($fTotalTransfers, false);
					$fTallyBalance = format_number($fTallyBalance, false);
					$fAvgSaving = format_number(($iTotalTransactions > 0 ? ($fTotalSaving/$iTotalTransactions) : 0), false);
	
					$csv_string .= "{$user['id']}, {$user['szFirstName']} {$user['szLastName']}, {$user['szEmail']}, {$user['szMobilePhone']},{$user['szFinicityInstitution']},{$user['szFinicityAccountTransitNumber']},{$user['szFinicityAccountNumber']},".format_number($fChequingbalance, false).",{$user['szInstitution']},{$user['szTransitNumber']},{$user['szAccountNumber']}," . ((int)$user['iSavingAccountVerified'] == 1 ? 'Yes' : 'No') . "," . ((int)$user['iSignupStep'] == 5 ? 'Yes' : 'No') . ",$fTallyBalance,$fTotalTransfers,$fTotalSaving,$fAvgSaving,$iTotalTransactions,".format_number($user['fAbsoluteMinBalance'], false).",".format_number($user['fSurplusDeficitRate'], false).",".format_number($user['fIfDeficitRate'], false).",$dtFirstTransaction,$tFirstTransaction,$fWeek1ExpenseAverage,$fWeek2ExpenseAverage,$fWeek3ExpenseAverage,$fWeek4ExpenseAverage,$fWeek5ExpenseAverage,$fAverageIncomeMonthly,$fAverageIncomeWeekly,$fMaxExpenseAverage,$fMaxExpenseCover\r\n";				
				}
				
				if($csv_string != "")
				{
					ob_end_clean();
					header("Pragma: ");
					header("Cache-Control: ");
					header("Content-type: application/csv");
					header("Content-Disposition: attachment; filename=All_Customers.csv;");
					echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", $csv_string);
					exit;
				}
				else
				{
					$data['error_msg'] = "No verified customer found.";
				}
			}
			else
			{
				$data['error_msg'] = "No customer found";
			}
		}
		
		// get user records as per page
		$data['show_pagination'] = false;
		$data['iTotalUsers'] = $this->User_Model->getCustomers(true);
		if($data['iTotalUsers'] > __PAGINATION_RECORD_LIMIT__)
		{
			$data['show_pagination'] = true;
			$data['iPage'] = ($arg1 == "page" && (int)$arg2 > 0 ? (int)$arg2 : 1);
			$this->load->library('pagination');
			$config['base_url'] = __BASE_ADMIN_URL__ . "/reports/users/page/";
			$config['total_rows'] = $data['iTotalUsers'];
			$config['per_page'] = __PAGINATION_RECORD_LIMIT__;
			$config['use_page_numbers'] = TRUE;
			$config['num_links'] = 4;
			$this->pagination->initialize($config);
			//echo $this->pagination->create_links();
			
			$iStartIndex = ($data['iPage']-1)*__PAGINATION_RECORD_LIMIT__;
			$iLimit = __PAGINATION_RECORD_LIMIT__; 
			$data['arUsers'] = $this->User_Model->getCustomers(false, $iStartIndex, $iLimit);
			$data['szPageText'] = "Showing " . ($iStartIndex + 1) . " to " . ($iStartIndex + $iLimit) . " of Total {$data['iTotalUsers']}";
		}
		
		$data['obj'] = $this;

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/reports/users', $data);
        $this->load->view('templates/admin_footer');
	}
}
?>
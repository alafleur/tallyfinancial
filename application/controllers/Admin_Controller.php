<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Admin_Controller
 * @property User_Model $User_Model
 * @property Admin_Model $Admin_Model
 */
class Admin_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->is_admin_login = false;
		$this->load->model('Admin_Model');
		$this->load->model('User_Model');
		
		#check for admin login
		if (!$this->Admin_Model->checkAdminLogin())
		{
			if($this->router->method != 'login' && $this->router->method != 'forgot_password')
			{
				$this->session->set_userdata('redir_url', __BASE_URL__ . str_replace("/tally", "", $_SERVER['REQUEST_URI']));
				header( 'Location:'.__BASE_ADMIN_URL__ . "/login");
				die();
			}
		}
		else
		{
			$this->is_admin_login = true;
			if($this->router->method == 'login')
			{
				if($this->router->method != "change-password")
				{
					ob_end_clean();
					header("Location:" . __BASE_ADMIN_URL__);
					die;
				}
			}
		}
	}
	
	public function index()
	{
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "dashboard";
		$data['obj'] = $this;
		$data['is_admin_login'] = $this->is_admin_login;

        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/home', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	public function login()
	{
		if(!empty($_POST['arLogin']))
		{
			if($this->Admin_Model->validateAdminData($_POST['arLogin']))
			{
				$szPassword = $this->Admin_Model->szPassword;
				if($this->Admin_Model->checkAdminExists($this->Admin_Model->szUsername))
				{
					if(encrypt($szPassword) != $this->Admin_Model->szPassword)
					{
						$this->Admin_Model->addError("p_password", "Your password does not match");
					}
					else
					{
						$_arr['id'] = $this->Admin_Model->id;
						$_arr['login'] = $this->Admin_Model->szUsername;
						$this->session->set_userdata('arr', $_arr);
		                    
		                //set data to cookie and keep for 1 month
		                $encKey = "#12EQ#83#";$encKey2="#AR3UIL#452#";$encKey3="#GBTE#ER23#";
		                $cookieData = $this->Admin_Model->id . "~$encKey" . $this->Admin_Model->szUsername;
		                $encryptedC = base64_encode($cookieData);
		                
		                $cookie = array(
						    'name'   => '__ulfabt_go',
						    'value'  => $encryptedC,
						    'expire' => time()+60*60*24*30
						);
					    
						$this->input->set_cookie($cookie);
		                
		                $redir_url = ($this->session->userdata('redir_url') ? $this->session->userdata('redir_url') : __BASE_ADMIN_URL__);
						$this->session->unset_userdata('redir_url');
						header( "Location: $redir_url");				
						die();
					}
				}
				else
				{
					$this->Admin_Model->addError("p_username", "This username is not registered.");
				}
			}			
		}
		
		$data['szMetaTagTitle'] = "Login";
		$data['is_admin_login'] = $this->is_admin_login;
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/login', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	public function logout()
	{
		AdminLogout($this);
		
		$data['szMetaTagTitle'] = "Login";
		$data['is_admin_login'] = false;
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/login', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	public function forgot_password($arg1='', $arg2='')
	{
		// handle forgot password request
		if(!empty($_POST['forgot_password']))
		{
			# Trim all spaces
			foreach ($_POST['forgot_password'] as $key => $value)
			{
				$_POST['forgot_password'][$key] = trim($value);
			}
			$this->Admin_Model->set_szEmail($_POST['forgot_password']['p_email']);
			
			if($this->Admin_Model->error)
			{			
				$data['szForgotEmailError'] = "Please enter valid email address.";
			}
			else
			{		
				if($this->Admin_Model->szEmail == __ADMIN_USER_EMAIL__)
				{
					if($this->Admin_Model->send_forget_password_link($this->Admin_Model->szEmail))
					{
						$data['szForgotPassSuccess'] = 'A link has been emailed on this address, that will allow you to reset your password.<br><br>
						If you do not receive the password reset email, please check your "spam" folders.';
					}
					else
					{
						$data['szForgotPassError'] = 'Problem while sending password reset link, please try again.';
					}
				}
				else
				{
					$data['szForgotEmailError'] = "This email is not the registered email for admin user.";
				}
			}
		}
		
		// handle reset password attempt
		if(!empty($_POST['reset_password']))
		{
			$data['szPassLink'] = trim($_POST['p_reset_link']);
			$this->Admin_Model->set_szPassword(trim($_POST['reset_password']['p_password']));
			if(!$this->Admin_Model->error)
			{
				$re_password = trim($_POST['reset_password']['p_re_password']);
				if(trim($_POST['reset_password']['p_re_password']) == $this->Admin_Model->szPassword)
				{
					if($this->Admin_Model->updatePassword($this->Admin_Model->szPassword))
					{
						$this->Admin_Model->remove_pass_reset_link_key();
						$this->session->set_userdata('reset_pass_success_msg', 'Your Password has been successfully reset.');
														
						ob_end_clean();
						if($arg1 == 'change-password')
							header( 'Location:'.__BASE_ADMIN_URL__);
						else
							header( 'Location:'.__BASE_ADMIN_URL__ . "/login");
						die();
					}
					else
					{
						$data['szResetPassError'] = 'Reset password link will not Send <a href="#"><strong>Click Here to Contact Us</strong></a>';
						$is_reset_error = true;
					}
				}
				else
				{
					$data['resetRePassError'] = 'Password does not match.';
					$is_reset_error = true;
				}
			}
			else if(!empty($this->Admin_Model->arErrorMessages['p_password']))
			{
				$data['resetPassError'] = $this->Admin_Model->arErrorMessages['p_password'];
				$is_reset_error = true;
			}
		}
		
		$data['isPassLinkExists'] = false;
		if(!$is_reset_error)
		{
			if($arg1 == 'change-password')
				$data['szPassLink'] = sanitize_all_html_input(trim($arg2));
			else
				$data['szPassLink'] = sanitize_all_html_input(trim($arg1));
		}
		
		if($data['szPassLink'] != '' && ($this->Admin_Model->is_link_key_exists($data['szPassLink']) || $is_change_password))
		{
			$data['isPassLinkExists'] = true;
		}
		
		if($this->is_admin_login)
			$data['show_leftmenu'] = true;
			
		$data['szMetaTagTitle'] = "Forgot Password";
		$data['is_admin_login'] = $this->is_admin_login;
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/forgot_password', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function download($arg1='')
	{
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "export";
		$data['is_admin_login'] = $this->is_admin_login;
		$data['szMetaTagTitle'] = "Download Files";
		$data['is_admin_login'] = $this->is_admin_login;
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/download_files', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function list_users($arg1='', $arg2='')
	{
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "users";
		$data['is_admin_login'] = $this->is_admin_login;

		$data['error_msg'] = "";
		
		if(trim($_POST['confirm']['p_func']) == "DELETE")
		{
			if(trim($_POST['confirm']['p_re_func']) == "DELETE")
			{
				if($this->User_Model->deleteCustomer($_POST['confirm']['p_id']))
				{
					$data['success_msg'] = 'User\'s account deleted successfully';
				}
				else if(!empty($this->User_Model->arErrorMessages))
				{
					$data['error_msg'] = $this->User_Model->arErrorMessages['p_customer'];
				}
			}
			else
			{
				$data['error_msg'] = "Confirmation text does not match.";
			}
		}
		
		if(trim($arg1) == "export-customer-import-file")
		{			
			$data['arUsers'] = $this->User_Model->getCustomers();
			if(!empty($data['arUsers']))
			{
				$csv_string = "";
				$is_first_found = false;
				foreach($data['arUsers'] as $user)
				{
					if((int)$user['iSignupStep'] == 5)
					{
						if(!$is_first_found)
						{
							$is_first_found = true;
							$csv_string .= "Name*,ID*,Email,Address1,Address2,City,Province,PostalCode,Homephone,Cellphone,Transit*,Bank*,Account*\r\n";
						}
						$csv_string .= "{$user['szFirstName']} {$user['szLastName']}, {$user['id']}, {$user['szEmail']},,,,,,,{$user['szMobilePhone']},{$user['szFinicityAccountTransitNumber']},{$user['szFinicityInstitutionNumber']},{$user['szFinicityAccountNumber']}\r\n";
					}
				}
				
				if($csv_string != "")
				{
					ob_end_clean();
					header("Pragma: ");
					header("Cache-Control: ");
					header("Content-type: application/csv");
					header("Content-Disposition: attachment; filename=Customer_Import_File.csv;");
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
			$config['base_url'] = __BASE_ADMIN_URL__ . "/users/list/page/";
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
		elseif($data['iTotalUsers'] > 0){
            $data['show_pagination'] = false;
            $data['iPage'] = 1;
            $this->load->library('pagination');
            $config['base_url'] = __BASE_ADMIN_URL__ . "/users/list/page/";
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
		
		$data['szMetaTagTitle'] = "Users";
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users/list', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function details($arg1='', $arg2='')
	{
		$data['szErrorMessage'] = "";
		$data['idUser'] = (int)$arg1;
		$data['szSuccessMessage'] = "";
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "users";
		$data['fTotalSaving'] = 0;
		$data['fTotalTransfers'] = 0;
		$data['fTallyBalance'] = 0;
		$data['fMostRecentTransaction'] = 0;
		$data['szFirstTransactionDate'] = "";
		$data['iFirstTransactionDays'] = "";
		$data['is_admin_login'] = $this->is_admin_login;
		
		if(trim($_POST['confirm']['p_func']) == "VERIFY")
		{
			if(trim($_POST['confirm']['p_re_func']) == "VERIFY")
			{
				if(trim($_POST['confirm']['p_sub_func']) == "ACCOUNT")
				{
					$this->User_Model->updateSignupStep($_POST['confirm']['p_id'], 5);
					$data['szSuccessMessage'] = '<div class="alert alert-success">User account verified successfully</div>';
				}
				else if(trim($_POST['confirm']['p_sub_func']) == "SAVING-ACCOUNT")
				{
					$this->User_Model->updateSavingAccountDetailsStaus($_POST['confirm']['p_id'], 1);
					$data['szSuccessMessage'] = '<div class="alert alert-success">User saving account details verified successfully</div>';
				}
			}
			else
			{
				$data['confirm_error'] = "Confirmation text does not match.";
			}
		}
		
		if(trim($_POST['confirm']['p_func']) == "BLOCK")
		{
			if(trim($_POST['confirm']['p_re_func']) == "BLOCK")
			{
				if(trim($_POST['confirm']['p_sub_func']) == "ACCOUNT")
				{
					$this->User_Model->updateSignupStep($_POST['confirm']['p_id'], 4);
					$data['szSuccessMessage'] = '<div class="alert alert-success">User account blocked successfully</div>';
				}
				else if(trim($_POST['confirm']['p_sub_func']) == "SAVING-ACCOUNT")
				{
					$this->User_Model->updateSavingAccountDetailsStaus($_POST['confirm']['p_id'], 0);
					$data['szSuccessMessage'] = '<div class="alert alert-success">User saving account details blocked successfully</div>';
				}
			}
			else
			{
				$data['confirm_error'] = "Confirmation text does not match.";
			}
		}
		
		if(!empty($_POST['arConstants']))
		{
			if($this->User_Model->saveCustomerConstants($_POST['arConstants']))
			{
				$data['szSuccessMessage'] = '<div class="alert alert-success">User constants successfully</div>';
			}
			else
			{
				$data['szSuccessMessage'] = "<div class=\"alert alert-danger\">Unable to save the details, please fix the error below and try again.</div>";
			}
		}			
		
		$data['institution_number_updated'] = false;
		$data['show_institution_number_modal'] = false;
		if(!empty($_POST['arInstitute']))
		{
			$data['show_institution_number_modal'] = true;
			if($this->User_Model->verifyInstitutionDetails($_POST['arInstitute']))
			{
				if($this->User_Model->updateInstitutionNumber())
					$data['institution_number_updated'] = true;
			}
		}
		
		$data['transit_updated'] = false;
		$data['show_transit_modal'] = false;
		if(!empty($_POST['arTransit']))
		{
			$data['show_transit_modal'] = true;
			if($this->User_Model->validateBankingInformation($_POST['arTransit'], array('p_institution', 'p_institution_id', 'p_account_number')))
			{
				$only_transit = (trim($_POST['p_type']) == 'saving' ? true : false);
				$is_chequing = (trim($_POST['p_type']) == 'chequing' ? true : false);
				if($this->User_Model->updateCustomerBankingInformation($is_chequing, $only_transit))
					$data['transit_updated'] = true;
			}
		}
		
		$is_user_exists = false;
		if($data['idUser'] > 0)
		{
			$this->User_Model->error = false;
			if(!$this->User_Model->loadCustomer($data['idUser']))
			{
				$data['szErrorMessage'] = "<div class=\"alert alert-danger\">Customer #{$data['idUser']} doesn't exists.</div>";
			}
			else
			{
				$is_user_exists = true;
			}
		}
		else
		{
			$data['szErrorMessage'] = "<div class=\"alert alert-danger\">Missing some required information.</div>";
		}
		
		if(empty($_POST['arConstants']) && $is_user_exists)
		{
			$_POST['arConstants']['p_absmin'] = $this->User_Model->fAbsoluteMinBalance;
			$_POST['arConstants']['p_surplusdeficit'] = $this->User_Model->fSurplusDeficitRate;
			$_POST['arConstants']['p_ifdeficit'] = $this->User_Model->fIfDeficitRate;
		}
		
		if($is_user_exists)
		{
			// get all saving transactions
			$this->load->model('Configuration_Model');
			$arTransactions = $this->Configuration_Model->getCustomerBiMonthlySavingTransactions($this->User_Model->idFinicity, 0, 0, "dtCreatedOn");
			if(!empty($arTransactions))
			{
				foreach($arTransactions as $transaction)
				{
					$data['fTotalSaving'] += $transaction['fSavingAmount'];
					$data['fMostRecentTransaction'] = $transaction['fSavingAmount'];
					if($data['szFirstTransactionDate'] == "")
					{
						$data['szFirstTransactionDate'] = date("m/d/Y", strtotime($transaction['dtCreatedOn']));
						$data['iFirstTransactionDays'] = ceil(abs(time() - strtotime($transaction['dtCreatedOn'])) / 86400);
					}
				}
				if($data['fTotalSaving'] != 0)
					$data['fTotalSaving'] = abs($data['fTotalSaving']);
					
				if($data['fMostRecentTransaction'] != 0)
					$data['fMostRecentTransaction']  = abs($data['fMostRecentTransaction']);
			}
			
			// get old saving transfers
			$arTransfers = $this->User_Model->getCustomerSavingsTransfers($this->User_Model->idFinicity);
			$iTotalTransfers = count($arTransfers);
			if(!empty($arTransfers))
			{
				foreach($arTransfers as $transfer)
				{
					$data['fTotalTransfers'] += $transfer['fAmount'];
				}
			}
			
			// get tally balance
			if($data['fTotalSaving'] > $data['fTotalTransfers'])
			{
				$data['fTallyBalance'] = $data['fTotalSaving'] - $data['fTotalTransfers'];
			}
			
			// get last months calculations
			$arLastMonthCalculations = getPreviousMonthsCalculations($this->User_Model->idFinicity, date("n"), date("Y"), $this->Configuration_Model, 1);
			
			// get chequing account balance
			$data['fChequingAccountBalance'] = $this->User_Model->getChequingBalance($this->User_Model->idFinicity);
		
			if(!empty($_POST['arMsg']))
			{
				$is_error = false;
				$p_msg = sanitize_all_html_input(trim($_POST['arMsg']['p_msg']));
				if($p_msg == '')
				{
					$this->User_Model->addError("p_msg", "Message is required.");
					$is_error = true;
				}
				
				if($_FILES['p_media']['name'] != '')
				{
					$arAcceptedMIMEOnTwilio = array("video/mpeg", "video/mp4", "video/quicktime", "video/webm", "video/3gpp", "video/3gpp2", "video/3gpp-tt", "video/H261", "video/H263", "video/H263-1998", "video/H263-2000", "video/H264", "image/jpeg", "image/gif", "image/png", "image/bmp");
					if (!in_array($_FILES["p_media"]["type"], $arAcceptedMIMEOnTwilio))
					{
						$error_msg = "Please select a valid image or video file with following allowed formats-";
						foreach($arAcceptedMIMEOnTwilio as $i=>$mime)
						{
							$error_msg .= "<br>" . ($i+1) . ". $mime";
						}
						
						$this->User_Model->addError("p_media", $error_msg);
						$is_error = true;
					}
					else
					{
						if($_FILES['p_media']['size'] > 5120000) 
						{
				             $this->User_Model->addError("p_media", "File size exceeds allowed limit - 5 MB.");
				             $is_error = true;
				        }
					}
				}
				
				if(!$is_error)
				{
					// send text message
					$replace_ary['FIRSTNAME'] = $this->User_Model->szFirstName;
					$replace_ary['LASTNAME'] = $this->User_Model->szLastName;
					$replace_ary['EMAIL'] = $this->User_Model->szEmail;
					$replace_ary['MOBILE'] = $this->User_Model->szMobilePhone;
					$replace_ary['CHEQUINGBALANCE'] = format_number($data['fChequingAccountBalance'], true);
					$replace_ary['TOTALSAVINGS'] = format_number($data['fTotalSaving'], true);
					$replace_ary['TALLYBALANCE'] = format_number($data['fTallyBalance'], true);
					$replace_ary['EXTERNALBALANCE'] = format_number($data['fTotalTransfers'], true);
					$replace_ary['MOSTRECENT'] = format_number($data['fMostRecentTransaction'], true);
					$replace_ary['AVGWEEK1'] = format_number($arLastMonthCalculations[0]['fWeek1ExpenseAverage'], true);
					$replace_ary['AVGWEEK2'] = format_number($arLastMonthCalculations[0]['fWeek2ExpenseAverage'], true);
					$replace_ary['AVGWEEK3'] = format_number($arLastMonthCalculations[0]['fWeek3ExpenseAverage'], true);
					$replace_ary['AVGWEEK4'] = format_number($arLastMonthCalculations[0]['fWeek4ExpenseAverage'], true);
					$replace_ary['AVGWEEK5'] = format_number($arLastMonthCalculations[0]['fWeek5ExpenseAverage'], true);
					$replace_ary['AVGINCOMEMONTHLY'] = format_number($arLastMonthCalculations[0]['fAverageIncomeMonthly'], true);
					$replace_ary['AVGINCOMEWKY'] = format_number($arLastMonthCalculations[0]['fAverageIncomeWeekly'], true);					
					$replace_ary['MAXEXPENSEAVG'] = format_number($arLastMonthCalculations[0]['fMaxExpenseAverage'], true);
					$replace_ary['MAXEXPENSECOVERAGE'] = format_number($arLastMonthCalculations[0]['fMaxExpenseCover'], true);
					$p_msg = createMessage($p_msg, $replace_ary);
					
					// attach media
					$p_media = '';
					if($_FILES['p_media']['name'] != '')
					{
						if(is_uploaded_file($_FILES['p_media']['tmp_name']))
				     	{
				     		$tmp = explode(".", $_FILES["p_media"]["name"]);
				     		$extension = end($tmp);
				     		$imageName = time() . "." . $extension;
				          	if(move_uploaded_file($_FILES["p_media"]['tmp_name'], __APP_PATH_ASSETS__.'/media/'.$imageName))
				         	{
				         		$p_media = __BASE_ASSETS_URL__ . "/media/" . $imageName;
				         	}
				     	}
					}
					
					sendMessege($this->User_Model->szMobilePhone, $p_msg, $p_media);
					$data['szSuccessMessage'] = "<div class=\"alert alert-success\">Message sent successfully.</div>";
					$_POST['arMsg']['p_msg'] = '';
				}
			}
		}
		
		$data['szMetaTagTitle'] = "User Details";
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users/details', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function transactions($arg1='', $arg2='')
	{
		$data['szErrorMessage'] = "";
		$data['idUser'] = (int)$arg1;
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "users";
		$data['is_admin_login'] = $this->is_admin_login;
		$data['arTransaction'] = array();
		$data['show_search_response'] = false;
		$data['fTotalSaving'] = 0;
		$data['fTotalTransfers'] = 0;
		$data['fTallyBalance'] = 0;
		$data['szFirstTransactionDate'] = '';
		$data['iFirstTransactionDays'] = '';
		
		if($data['idUser'] > 0)
		{
			if(!$this->User_Model->loadCustomer($data['idUser']))
			{
				$data['szErrorMessage'] = "Customer #{$data['idUser']} doesn't exists.";
			}
			else
			{
				// get all saving transactions
				$this->load->model('Configuration_Model');
				$arTransactions = $this->Configuration_Model->getCustomerBiMonthlySavingTransactions($this->User_Model->idFinicity);
				if(!empty($arTransactions))
				{
					foreach($arTransactions as $transaction)
					{
						$data['fTotalSaving'] += $transaction['fSavingAmount'];
						if($data['szFirstTransactionDate'] == "")
						{
							$data['szFirstTransactionDate'] = date("m/d/Y", strtotime($transaction['dtCreatedOn']));
							$data['iFirstTransactionDays'] = ceil(abs(time() - strtotime($transaction['dtCreatedOn'])) / 86400);
						}
					}
					if($data['fTotalSaving'] != 0)
						$data['fTotalSaving'] = abs($data['fTotalSaving']);
				}
				
				// get old saving transfers
				$arTransfers = $this->User_Model->getCustomerSavingsTransfers($this->User_Model->idFinicity);
				$iTotalTransfers = count($arTransfers);
				if(!empty($arTransfers))
				{
					foreach($arTransfers as $transfer)
					{
						$data['fTotalTransfers'] += $transfer['fAmount'];
					}
				}
				
				// get tally balance
				if($data['fTotalSaving'] > $data['fTotalTransfers'])
				{
					$data['fTallyBalance'] = $data['fTotalSaving'] - $data['fTotalTransfers'];
				}
				
				// get chequing account balance
				$data['fChequingAccountBalance'] = $this->User_Model->getChequingBalance($this->User_Model->idFinicity);
			}
		}
		else
		{
			$data['szErrorMessage'] = "Missing some required information.";
		}
		
		if(!empty($_POST['arSearch']))
		{
			$error = false;
			$startDate = trim($_POST['arSearch']['dtStart']);
			$endDate = trim($_POST['arSearch']['dtEnd']);
			if($startDate == '' || date("Y-m-d", strtotime($startDate)) == "1970-01-01")
			{
				$this->User_Model->addError("dtStart", "Start date must be valid");
				$error = true;
			}
			
			if($endDate == '' || date("Y-m-d", strtotime($endDate)) == "1970-01-01")
			{
				$this->User_Model->addError("dtEnd", "End date must be valid.");
				$error = true;
			}
			
			if(!$error)
			{
				$startTime = strtotime($startDate);
				$endTime = strtotime($endDate);
				if($endTime < $startTime)
				{
					$this->User_Model->addError("dtEnd", "End date must be greater than or equal to start date.");
					$error = true;
				}
			}
			
			if(!$error)
			{
				if(trim($_POST['arSearch']['fAmount']) != '' && !is_numeric(trim($_POST['arSearch']['fAmount'])))
				{
					$this->User_Model->addError("fAmount", "Transaction amount must be numeric.");
				}
				else
				{
					$fAmount = trim($_POST['arSearch']['fAmount']);
					$operator = trim($_POST['arSearch']['operator']);
					$data['arTransactions'] = $this->User_Model->searchUserTransactions($this->User_Model->idFinicity, $startDate, $endDate, $fAmount, $operator);
					if(trim($_POST['arSearch']['szType']) == "export")
					{
						$csv_string = "Transaction #, Account #, Amount, Description, Category, Status, Date\r\n";
						if(!empty($data['arTransactions']))
						{
							foreach($data['arTransactions'] as $transaction)
							{
								$csv_string .= "{$transaction['id']}, {$transaction['idAccount']}, ".format_number($transaction['fAmount']).", {$transaction['szDescription']}, {$transaction['szCategory']}, {$transaction['szStatus']}, ".date("m/d/Y", strtotime($transaction['dtDate']))."\r\n";
							}
						}
						ob_end_clean();
						header("Pragma: ");
						header("Cache-Control: ");
						header("Content-type: application/csv");
						header("Content-Disposition: attachment; filename=Customer_Transactions.csv;");
						echo iconv("UTF-8", "ISO-8859-1//TRANSLIT", $csv_string);
						exit;
					}
					else
					{
						$data['show_search_response'] = true;
					}
				}
			}
		}
		
		$data['szMetaTagTitle'] = "User Transactions";
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users/transactions', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function calculations($arg1=0, $arg2='', $arg3='')
	{
		$data['szErrorMessage'] = "";
		$data['is_admin_login'] = $this->is_admin_login;
		$data['idUser'] = (int)$arg1;
		$data['szSuccessMessage'] = "";
		$data['show_leftmenu'] = true;
		$data['active_menu'] = "users";
		$data['show_message'] = false;
		$data['fTotalSaving'] = 0;
		$data['fTotalTransfers'] = 0;
		$data['fTallyBalance'] = 0;
		$data['iFirstTransactionDays'] = "";
		$data['szFirstTransactionDate'] = "";
		$this->load->model("Configuration_Model");
		$data['arMonths'] = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");
		$data['arg2'] = $arg2;
		$data['arg3'] = $arg3;
		
		if($data['idUser'] > 0)
		{
			if(!$this->User_Model->loadCustomer($data['idUser']))
			{
				$data['szErrorMessage'] = "Customer #{$data['idUser']} doesn't exists.";
			}
			else
			{
				if(!empty($_POST['p_edit_transaction']))
				{
					if(!isset($_POST['p_search_calc']))
					{
						if($this->Configuration_Model->updateSavingTransactionAmount($_POST['p_id'], $_POST['p_amount']))
						{
							$_POST['p_search_calc'] = 1;
							$data['szSuccessMessage'] = "Saving transaction amount updated successfully.";
						}
						else
						{
							$data['arg2'] = "edit";$data['arg3'] = $_POST['p_id'];
						}
					}
				}
				
				// get all saving transactions
				$arTransactions = $this->Configuration_Model->getCustomerBiMonthlySavingTransactions($this->User_Model->idFinicity);
				if(!empty($arTransactions))
				{
					foreach($arTransactions as $transaction)
					{
						$data['fTotalSaving'] += $transaction['fSavingAmount'];
						if($data['szFirstTransactionDate'] == "")
						{
							$data['szFirstTransactionDate'] = date("m/d/Y", strtotime($transaction['dtCreatedOn']));
							$data['iFirstTransactionDays'] = ceil(abs(time() - strtotime($transaction['dtCreatedOn'])) / 86400);
						}
					}
					if($data['fTotalSaving'] != 0)
						$data['fTotalSaving'] = abs($data['fTotalSaving']);
				}
				
				// get old saving transfers
				$arTransfers = $this->User_Model->getCustomerSavingsTransfers($this->User_Model->idFinicity);
				$iTotalTransfers = count($arTransfers);
				if(!empty($arTransfers))
				{
					foreach($arTransfers as $transfer)
					{
						$data['fTotalTransfers'] += $transfer['fAmount'];
					}
				}
				
				// get tally balance
				if($data['fTotalSaving'] > $data['fTotalTransfers'])
				{
					$data['fTallyBalance'] = $data['fTotalSaving'] - $data['fTotalTransfers'];
				}
				
				// get chequing account balance
				$data['fChequingAccountBalance'] = $this->User_Model->getChequingBalance($this->User_Model->idFinicity);
			}
		}
		else
		{
			$data['szErrorMessage'] = "Missing some required information.";
		}			
		
		if(!empty($_POST['p_search_calc']))
		{
			$data['show_message'] = true;
			$idCustomer = sanitize_all_html_input(trim($_POST['p_customer']));
			$iYear = sanitize_all_html_input(trim($_POST['p_calculation_year']));
			$iMonth = sanitize_all_html_input(trim($_POST['p_calculation_month']));
			
			$data['arCalculations'] = getPreviousMonthsCalculations($idCustomer, $iMonth, $iYear, $this->Configuration_Model, 1);
			$data['arCalculations'] = $data['arCalculations'][0];
			$data['arTransactions'] = $this->Configuration_Model->getCustomerBiMonthlySavingTransactions($idCustomer, $iMonth, $iYear);
		}
		
		$data['szMetaTagTitle'] = "Calculations";
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users/calculations', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function managebalance($arg1='', $arg2='', $arg3='', $arg4='')
	{
		$data['is_admin_login'] = $this->is_admin_login;
		$data['szErrorMessage'] = "";
		$data['idUser'] = (int)$arg1;
		$data['szSuccessMessage'] = "";
		$data['active_menu'] = "users";
		$data['show_leftmenu'] = true;
		$data['fTotalSaving'] = 0;
		$data['fTotalTransfers'] = 0;
		$data['fTallyBalance'] = 0;
		$data['szFirstTransactionDate'] = '';
		$data['iFirstTransactionDays'] = '';
		$data['arg2'] = $arg2;
		$data['arg3'] = $arg3;
		$data['arg4'] = $arg4;
		
		if($data['idUser'] > 0)
		{
			if(!$this->User_Model->loadCustomer($data['idUser']))
			{
				$szErrorMessage = "Customer #{$data['idUser']} doesn't exists.";
			}
			else
			{
				if($arg2 == "reject" && (int)$arg3 > 0)
				{
					if($this->User_Model->rejectCustomerSavingsTransfer($arg3))
					{
						$data['szSuccessMessage'] = '<div class="alert alert-success">User withdraw request has been rejected.</div>';
						
						// send message to admin
						sendMessege($this->User_Model->szMobilePhone, "Your withdrawal request for $" . format_number($arg4, true) . " has been rejected. Please contact us for more details.");
					}
				}
				
				$this->load->model('Configuration_Model');
				
				if(isset($_POST['p_func']) && trim($_POST['p_func']) == "Transfer Balance")
				{
					$fTotalAvalable = (float)$_POST['p_available_amount'];
					$this->User_Model->validateInput($_POST['p_amount'], __VLD_CASE_NUMERIC__, "p_amount", "Transfer amount", 1.00, ($fTotalAvalable > 0 ? $fTotalAvalable : false), true);
					
					$p_date = trim($_POST['p_date']);
					$a_date = explode("/", $p_date);
					$p_date = "{$a_date[2]}-{$a_date[0]}-{$a_date[1]}";
					$date = date("Y-m-d");
					
					if(strtotime($date) < strtotime($p_date))
					{
						$this->User_Model->addError("p_date", "Transfer date can't be a future date");
					}
					
					if(!$this->User_Model->error)
					{
						$p_date = "$p_date 00:00:00";
						$p_type = $_POST['p_type'];
								
						$fTransferAmount = (float)$_POST['p_amount'];
						if($fTotalAvalable == 0)
						{
							$this->User_Model->addError('p_amount', 'You can\'t transfer any amount as there is no available balance for this user');
						}
						else
						{
							if((int)$_POST['p_approve'] > 0)
							{
								$p_id = (int)$_POST['p_approve'];								
								
								if($this->User_Model->approveCustomerSavingsTransfer($p_id, $p_date, $p_type))
								{
									$data['szSuccessMessage'] = '<div class="alert alert-success">User withdraw request approved successfully</div>';
									$_POST['p_amount'] = "";
									$_POST['p_type'] = 0;
									
									// send message to admin
									sendMessege($this->User_Model->szMobilePhone, "Your withdrawal request for $" . format_number($fTransferAmount, true) . " has been approved.");
								}
							}
							else
							{
								$idCustomer = (int)$_POST['p_customer'];
								if($this->User_Model->addCustomerSavingsTransfer($idCustomer, $fTransferAmount, 0, $p_date, $p_type))
								{
									$data['szSuccessMessage'] = '<div class="alert alert-success">Savings transfer added successfully.</div>';
									$_POST['p_amount'] = "";
									$_POST['p_type'] = 0;
								}
							}
						}
					}
				}
		
				// function get saving transactions
				$arTransactions = $this->Configuration_Model->getCustomerBiMonthlySavingTransactions($this->User_Model->idFinicity);
				if(!empty($arTransactions))
				{
					foreach($arTransactions as $transaction)
					{
						$data['fTotalSaving'] += $transaction['fSavingAmount'];
						if($data['szFirstTransactionDate'] == "")
						{
							$data['szFirstTransactionDate'] = date("m/d/Y", strtotime($transaction['dtCreatedOn']));
							$data['iFirstTransactionDays'] = ceil(abs(time() - strtotime($transaction['dtCreatedOn'])) / 86400);
						}
					}
					if($data['fTotalSaving'] != 0)
						$data['fTotalSaving'] = abs($data['fTotalSaving']);
				}
				
				// get old saving transfers
				$arTransfers = $this->User_Model->getCustomerSavingsTransfers($this->User_Model->idFinicity);
				$iTotalTransfers = count($arTransfers);
				if(!empty($arTransfers))
				{
					foreach($arTransfers as $transfer)
					{
						$data['fTotalTransfers'] += $transfer['fAmount'];
					}
				}
				
				if($data['fTotalSaving'] > $data['fTotalTransfers'])
				{
					$data['fTallyBalance'] = $data['fTotalSaving'] - $data['fTotalTransfers'];
				}
				
				// get chequing account balance
				$data['fChequingAccountBalance'] = $this->User_Model->getChequingBalance($this->User_Model->idFinicity);
				
				// get transfer history
				$data['iPage'] = 1;
				$data['iLimit'] = 5;
				$data['show_more'] = false;
				if(trim($arg2) == 'more' && (int)$arg3 > 0) $data['iPage'] = (int)$arg3;
				$data['iLimit'] = $data['iPage']*$data['iLimit'];
				if($iTotalTransfers> $data['iLimit']) $data['show_more'] = true;
				if($data['iLimit'] > $iTotalTransfers) $data['iLimit'] = $iTotalTransfers;
				$data['arTransfersHistory'] = $this->User_Model->getCustomerSavingsTransfers($this->User_Model->idFinicity, $data['iLimit']);
				$data['iPage']++;
				
				// get un-approved transfer
				$data['arUnApprovedTransfers'] = $this->User_Model->getCustomerSavingsTransfers($this->User_Model->idFinicity, 0, 0);
			}
		}
		else
		{
			$data['szErrorMessage'] = "Missing some required information.";
		}
		
		$data['szMetaTagTitle'] = "Savings";
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users/managebalance', $data);
        $this->load->view('templates/admin_footer', $data);
	}
	
	function document($arg1='', $arg2='')
	{
		$szErrorMessage = "";
		$type = trim($arg1);
		$key = trim($arg2);
		
		if(!$this->User_Model->loadCustomer($key, true))
		{
			echo "Something required information is missing or not correct.";
			die;
		}
		
		if($type == "statement")
		{
			$pdf = __APP_PATH__ . "/statements/{$this->User_Model->szFinicityStatementVerificationFile}";
			if(file_exists($pdf))
			{
				header("Content-type:application/pdf");
				header('Content-Disposition: inline; filename="'.$this->User_Model->szFinicityStatementVerificationFile.'"');
				readfile($pdf);
			}
			else
			{
				echo "Statement file not exists.";
				die;
			}
		}
		else if($type == "chequing")
		{
			$file = __APP_PATH_ASSETS__ . "/images/users_account/{$this->User_Model->szFinicityAccountVerificationFile}";
			if(file_exists($file))
			{
				$filename = basename($file);
				$file_extension = strtolower(substr(strrchr($filename,"."),1));
				$ctype="image/jpeg";
				
				switch( $file_extension ) {
				    case "gif": $ctype="image/gif"; break;
				    case "png": $ctype="image/png"; break;
				    case "jpeg":
				    case "jpg": $ctype="image/jpeg"; break;
				    default:
				}
				
				header('Content-type: ' . $ctype);
				readfile($file);
			}
			else
			{
				echo "Something required information is missing or not correct.";
				die;
			}
		}
		else if($type == "saving")
		{
			$file = __APP_PATH_ASSETS__ . "/images/users_account/{$this->User_Model->szVerificationFile}";
			if(file_exists($file))
			{
				$filename = basename($file);
				$file_extension = strtolower(substr(strrchr($filename,"."),1));
				$ctype="image/jpeg";
				
				switch( $file_extension ) {
				    case "gif": $ctype="image/gif"; break;
				    case "png": $ctype="image/png"; break;
				    case "jpeg":
				    case "jpg": $ctype="image/jpeg"; break;
				    default:
				}
				
				header('Content-type: ' . $ctype);
				readfile($file);
			}
			else
			{
				echo "Something required information is missing or not correct.";
				die;
			}
		}
	}
	
	function message($arg1='', $arg2='', $arg3='', $arg4='')
	{
		$data['is_admin_login'] = $this->is_admin_login;
		$data['active_menu'] = "message";
		$data['show_leftmenu'] = true;
		$data['szMetaTagTitle'] = "Messaging";
		$data['arg1'] = $arg1;
		$data['arg2'] = $arg2;
		$data['arg3'] = $arg3;
		$data['arg4'] = $arg4;
		$data['obj'] = $this;
		
		$this->load->view('templates/admin_header', $data);
        $this->load->view('admin/users/message', $data);
        $this->load->view('templates/admin_footer', $data);
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_Model');
		$this->load->model('Configuration_Model');
	}
	
	public function index()
	{
		$data['szMetaTagTitle'] = "User Signup";
		$data['is_user_login'] = is_user_login($this);

        $this->load->view('templates/header', $data);
        $this->load->view('users/signup.php');
        $this->load->view('templates/footer');
	}
	
	public function login()
	{
		$is_user_login = is_user_login($this);
		
		// redirect to dashboard if already logged in
		if($is_user_login)
		{
			ob_end_clean();
			header("Location:" . __SECURE_BASE_URL__ . "/users/dashboard");
			die;
		}
		
		$szNotVerified = "";
		$arErrorMessages = array();
		if(!empty($_POST['arLogin']))
		{
			if($this->User_Model->validateCustomerData($_POST['arLogin'], array("p_id", "p_fname", "p_lname", "p_mobilephone")))
			{
				if($this->User_Model->checkCustomerExists($this->User_Model->szEmail))
				{
					$szPassword = $this->User_Model->szPassword;
					if($this->User_Model->loadCustomer($this->User_Model->id))
					{
						if(encrypt($szPassword) != $this->User_Model->szPassword)
						{
							$this->User_Model->addError("p_password", "Your password does not match");
						}
						else
						{
							if($this->User_Model->iSignupStep == 1)
							{
								$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
								$arMap = $this->User_Model->getMobileVerificationMapping($this->User_Model->id);
								if(!empty($arMap))
								{
									ob_end_clean();
									header("Location:" . __SECURE_BASE_URL__ . "/users/signup/mobile-number-confirmation");
									die;
								}
								else
								{
									ob_end_clean();
									header("Location:" . __SECURE_BASE_URL__ . "/users/signup/mobile-number");
									die;
								}
							}
							else if($this->User_Model->iSignupStep == 2)
							{
								$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
								ob_end_clean();
								header("Location:" . __SECURE_BASE_URL__ . "/users/signup/link-your-bank");
								die;
							}
							else if($this->User_Model->iSignupStep == 3)
							{
								$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
								ob_end_clean();
								header("Location:" . __SECURE_BASE_URL__ . "/users/signup/account-information");
								die;
							}
							/*else if($this->User_Model->iSignupStep == 4)
							{
								$this->session->unset_userdata('signing_user');
								$szNotVerified = "Your account verification is pending. You'll be able to login as soon as your account is verified.";
							}*/
							else
							{
								$data['id'] = $this->User_Model->id;
								$data['szEmail'] = $this->User_Model->szEmail;
							    $data['szFirstName'] = $this->User_Model->szFirstName;
				    
								set_customer_cookie($this, $data);
								ob_end_clean();
								header("Location:" . __SECURE_BASE_URL__ . "/users/dashboard");
								die;
							}
						}
					}
				}
				else
				{
					$arErrorMessages["p_email"] = "This email is not registered. Want to <a href='" . secure_base_url . "/users/signup'>create a new account</a>?";
				}
			}
		}
		
		$data['szMetaTagTitle'] = "User Login";
		$data['arErrorMessages'] = $arErrorMessages;
		$data['szNotVerified'] = $szNotVerified;
		$data['is_user_login'] = $is_user_login;

        $this->load->view('templates/header', $data);
        $this->load->view('users/login.php');
        $this->load->view('templates/footer');
	}
	
	public function signup($arg1='')
	{
		check_for_signing_process($this, $arg1);
		$is_user_login = is_user_login($this);
		
		if($is_user_login)
		{
			ob_end_clean();
			header("Location:" . __SECURE_BASE_URL__ . "/account");
			die;
		}
				
		$data['iSignUpStep'] = 1;
		$data['verify_mobile'] = false;
		$data['vcode_expired'] = 0;
		$data['iFinicityAddCustomerFailed'] = false;
		
		if(!empty($_POST['arRegister']))
		{
			if($this->User_Model->validateCustomerData($_POST['arRegister'], array("p_id", "p_mobilephone")))
			{
				if($idUser = $this->User_Model->addCustomer())
				{
					$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
					
					ob_end_clean();
					header("Location:" . __SECURE_BASE_URL__ . "/users/signup/mobile-number");
					die;
				}
				else if($this->User_Model->iFinicityAddCustomerFailed)
				{
					$data['iFinicityAddCustomerFailed'] = true;
				}
			}
		}
		else if(!empty($_POST['arRegister2']))
		{
			// reset the signing process session variable
			if($this->User_Model->loadCustomer($_POST['arRegister2']['p_id']))
				$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
					
			if($this->User_Model->validateCustomerData($_POST['arRegister2'], array("p_fname", "p_lname", "p_email", "p_password")))
			{
				/*if(!$this->User_Model->checkMobilePhoneInMapping($this->User_Model->szMobilePhone) && !$this->User_Model->checkCustomerExists($this->User_Model->szMobilePhone, false, true))
				{*/			
					$data['szMessageKey'] = generateRandomString();
					$data['idUser'] = $this->User_Model->id;
					$data['szMobilePhone'] = $this->User_Model->szMobilePhone;
					$data['szMessage'] = "Your Tally verification code is {$data['szMessageKey']}";
					sendMessege($data['szMobilePhone'], $data['szMessage']);
					
					if($this->User_Model->addUserMobileVerificationMapping($data))
					{
						ob_end_clean();
						header("Location:" . __SECURE_BASE_URL__ . "/users/signup/mobile-number-confirmation");
						die;
					}
				/*}
				else
				{
					$this->User_Model->addError("p_mobilephone", "This mobile phone number already exists");
				}*/
			}
		}
		else if(!empty($_POST['arRegister21']))
		{	
			// reset the signing process session variable
			if($this->User_Model->loadCustomer($_POST['arRegister21']['p_id']))
				$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
				
			if(sanitize_all_html_input(trim($_POST['arRegister21']['p_vcode'])) != '')
			{
				if($this->User_Model->validateCustomerData($_POST['arRegister21'], array("p_fname", "p_lname", "p_email", "p_password", "p_mobilephone")))
				{
					$arMap = $this->User_Model->getMobileVerificationMapping($this->User_Model->id);
					if(!empty($arMap))
					{
						if(trim($arMap['szMessageKey']) == sanitize_all_html_input(trim($_POST['arRegister21']['p_vcode'])))
						{
							$this->User_Model->szMobilePhone = $arMap['szMobilePhone'];
							$this->User_Model->updateCustomerPhone();
							
							ob_end_clean();
							header("Location:" . __SECURE_BASE_URL__ . "/users/signup/link-your-bank");
							die;
						}
						else
						{
							$this->User_Model->addError("p_vcode", "Verification code does not match.");
						}
					}
					else
					{
						$this->session->set_userdata('vcode_expired', 1);
						$this->User_Model->deleteUserMobileVerificationMapping($this->User_Model->id);
						
						ob_end_clean();
						header("Location:" . __SECURE_BASE_URL__ . "/users/signup/mobile-number");
						die;
					}
				}
				else
				{
					$this->User_Model->addError("p_vcode", "Verification code can not be verified.");
				}
			}
			else
			{
				$this->User_Model->addError("p_vcode", "Verification code is required.");
			}
		}
		else if(!empty($_POST['arAuth']))
		{	
			// reset the signing process session variable
			if($this->User_Model->loadCustomer($_POST['arAuth']['p_id']))
				$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
				
			if($this->User_Model->validateCustomerData($_POST['arAuth'], array("p_fname", "p_lname", "p_email", "p_password", "p_mobilephone")))
			{
				$institution_id = trim($_POST['arAuth']['institution_id']);
				$account_id = trim($_POST['arAuth']['account_id']);
				$account_number = trim($_POST['arAuth']['account_number']);
				$statement_file = trim($_POST['arAuth']['statement_file']);
				
				if(!empty($institution_id) && !empty($account_id) && !empty($account_number))
				{
					$this->User_Model->updateCustomerInstitution($institution_id, $account_id, $account_number, $statement_file);
					ob_end_clean();
					header("Location:" . __SECURE_BASE_URL__ . "/users/signup/account-information");
					die;
				}
			}
			
			ob_end_clean();
			header("Location:" . __SECURE_BASE_URL__ . "/users/signup/link-your-bank");
			die;	
		}
		else if(!empty($_POST['arBanking']))
		{
			// reset the signing process session variable
			if($this->User_Model->loadCustomer($_POST['arBanking']['p_id']))
				$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
			
			if(!empty($_POST['arBanking']['p_cant_find']))
			{
				$this->User_Model->updateSignupStep($_POST['arBanking']['p_id'], 4, false);
				
				ob_end_clean();
				header("Location:" . __SECURE_BASE_URL__ . "/users/signup/done");
				die;
			}
			else
			{
				if($this->User_Model->validateBankingInformation($_POST['arBanking'], array('p_institution_id', 'p_institution', 'p_account_number')))
				{
					/*if($_FILES['p_verification_file']['name'] == '')
					{
						$this->User_Model->addError("p_verification_file", "Verification file is required.");
					}
					else
					{
						if($this->User_Model->validateInput($_FILES['p_verification_file'], __VLD_CASE_IMAGE__ , "p_verification_file", "Verification file", false, false, true))
						{
							if($this->User_Model->updateCustomerBankingInformation(true, true) && $this->User_Model->updateCustomerBankingInformationVerificationFile($_FILES['p_verification_file'], true))*/
							if($this->User_Model->updateCustomerBankingInformation(true, true))
							{
								ob_end_clean();
								header("Location:" . __SECURE_BASE_URL__ . "/users/signup/done");
								die;
							}
						/*}
					}*/
				}
			}
		}
		
		if((trim($arg1) == "mobile-number" || trim($arg1) == "mobile-number-confirmation" || trim($arg1) == "mobile-number-resend-code" || trim($arg1) ==  "link-your-bank" || trim($arg1) ==  "account-information" || trim($arg1) == "done") && $this->session->userdata('signing_user'))
		{
			if($this->User_Model->checkCustomerExists($this->session->userdata('signing_user')))
			{
				$this->User_Model->loadCustomer($this->User_Model->id);
				if($this->User_Model->iSignupStep != 5)
				{
					$this->session->set_userdata('signing_user', $this->User_Model->szEmail);
					if(trim($arg1) == "done")
					{
						$data['iSignUpStep'] = 5;
						$this->session->unset_userdata('signing_user');
					}
					else if(trim($arg1) ==  "account-information")
					{
						$data['iSignUpStep'] = 4;
						$data['arInstitution'] = $this->User_Model->getInstitution("id = {$this->User_Model->idFinicityInstitution}", "ORDER BY iOrder");
						$data['szFinicityAccountNumber'] = $this->User_Model->szFinicityAccountNumber;
					}
					else if(trim($arg1) ==  "link-your-bank")
					{
						$data['iSignUpStep'] = 3;
						$data['iLoginStep'] = $this->User_Model->iLoginStep;
						$data['idConnectInstitution'] = $this->User_Model->idFinicityInstitution;
						$data['arMainInstitutions'] = $this->User_Model->getInstitution("isMain = 1", "ORDER BY iOrder");
					}
					else
					{
						$data['iSignUpStep'] = 2;
						if((int)$this->session->userdata('vcode_expired') == 1)
						{
							$data['vcode_expired'] = 1;
							$this->session->unset_userdata('vcode_expired');
						}
						
						if(trim($arg1) == "mobile-number-confirmation")
						{
							$data['arMap'] = $this->User_Model->getMobileVerificationMapping($this->User_Model->id);
							if(!empty($data['arMap']))
								$data['verify_mobile'] = true;
						}
						else if(trim($arg1) == "mobile-number-resend-code")
						{
							$arMap = $this->User_Model->getMobileVerificationMapping($this->User_Model->id);
							if(!empty($arMap))
							{
								$data['szMessageKey'] = generateRandomString();
								$data['idUser'] = $this->User_Model->id;
								$data['szMobilePhone'] = trim($arMap['szMobilePhone']);
								$data['szMessage'] = "Your Tally verification code is {$data['szMessageKey']}";
								sendMessege($data['szMobilePhone'], $data['szMessage']);
								
								if($this->User_Model->addUserMobileVerificationMapping($data))
								{
									ob_end_clean();
									header("Location:" . __SECURE_BASE_URL__ . "/users/signup/mobile-number-confirmation");
									die;
								}
							}
							else
							{
								$this->session->set_userdata('vcode_expired', 1);
							}
						}
						else
						{
							$this->User_Model->deleteUserMobileVerificationMapping($this->User_Model->id);
						}
					}
				}
				else
				{
					$this->session->unset_userdata('signing_user');
					$data['id'] = $this->User_Model->id;
					$data['szEmail'] = $this->User_Model->szEmail;
				    $data['szFirstName'] = $this->User_Model->szFirstName;
	    
					set_customer_cookie($this, $data);
						
					ob_end_clean();
					header("Location:" . __SECURE_BASE_URL__ . "/users/dashboard");
					die;
				}
			}
		}
		$data['szMetaTagTitle'] = "User Signup";
		$data['is_user_login'] = $is_user_login;
		$data['arErrorMessages'] = $this->User_Model->arErrorMessages;
		$data['id'] = $this->User_Model->id;
		
		if($data['iSignUpStep'] != 1)
        	$this->load->view('templates/secure_header', $data);
        else
        	$this->load->view('templates/header', $data);
        	
        $this->load->view('users/signup.php', $data);
        
        if($data['iSignUpStep'] != 1)
       		$this->load->view('templates/secure_footer', $data);
       	else
       		$this->load->view('templates/footer', $data);
	}
	
	public function logout()
	{
		CustomerLogout($this);
		
		$data['szMetaTagTitle'] = "User Login";

        $this->load->view('templates/header', $data);
        $this->load->view('users/login.php');
        $this->load->view('templates/footer');
	}
	
	public function dashboard($arg1="", $arg2=0)
	{
		$is_user_login = is_user_login($this);
		$user_session = $this->session->userdata('user_arr');
		$data['transit_updated'] = false;
		
		if(!$this->User_Model->loadCustomer($user_session['id']))
		{
			$is_user_login = false;
		}
		
		/*if($this->User_Model->idFinicityInstitution == '')
		{
			$is_user_login = false;
		}*/
		
		if(!$is_user_login || $this->User_Model->iSignupStep < 4)
		{
			CustomerLogout($this);
			ob_end_clean();
			header("Location:" . __SECURE_BASE_URL__ . "/users/login");
			die;
		}
		
		if(!empty($_POST['arBanking']))
		{			
			if($this->User_Model->validateBankingInformation($_POST['arBanking'], array('p_institution_id', 'p_institution', 'p_account_number')))
			{
				if($this->User_Model->updateCustomerBankingInformation(true))
				{
					$this->User_Model->szFinicityAccountTransitNumber = $this->User_Model->szTransitNumber;
					$data['transit_updated'] = true;
				}
			}
			
			if(!$data['transit_updated'])
			{
				$data['arErrorMessages'] = $this->User_Model->arErrorMessages;
			}
		}
		
		$arTransactions = $this->Configuration_Model->getCustomerBiMonthlySavingTransactions($this->User_Model->idFinicity);
		$fTotalSaving = 0;
		$fAverageDay = 0;
		$iTotalTransactions = count($arTransactions);
		if(!empty($arTransactions))
		{
			$tMinimum = 0;
			$tMaximum = 0;
			foreach($arTransactions as $transaction)
			{
				if($tMaximum == "") $tMaximum = strtotime($transaction['dtCreatedOn']);
				$tMinimum = strtotime($transaction['dtCreatedOn']);
				$fTotalSaving += $transaction['fSavingAmount'];
			}
			if($fTotalSaving != 0)
				$fTotalSaving = abs($fTotalSaving);
			if($tMaximum > 0 && $iTotalTransactions > 0)
			{
				$fAverageDay = ceil(abs($tMaximum - $tMinimum) / 86400);
				$fAverageDay = $fAverageDay/$iTotalTransactions;
			}
		}
		
		$arTransfers = $this->User_Model->getCustomerSavingsTransfers($this->User_Model->idFinicity);
		$iTotalTransfers = count($arTransfers);
		if(!empty($arTransfers))
		{
			foreach($arTransfers as $transfer)
			{
				$fTotalTransfers += $transfer['fAmount'];
			}
		}
		
		if($fTotalSaving > $fTotalTransfers)
			$fTallyBalance = $fTotalSaving - $fTotalTransfers;
		
		$iPage = 1;
		$iLimit = 5;
		$show_more = false;
		if(trim($arg1) == 'more' && (int)$arg2 > 0) $iPage = (int)$arg2;
		$iLimit = $iPage*$iLimit;
		if($iTotalTransactions > $iLimit) $show_more = true;
		if($iLimit > $iTotalTransactions) $iLimit = $iTotalTransactions;
		$arJournalTransactions = $this->Configuration_Model->getCustomerBiMonthlySavingTransactions($this->User_Model->idFinicity, 0, 0, "dtCreatedOn", "DESC", $iLimit);
		$iPage++;
		
		$data['is_user_login'] = $is_user_login;
		$data['fTotalSaving'] = $fTotalSaving;
		$data['fTallyBalance'] = $fTallyBalance;
		$data['fTotalTransfers'] = $fTotalTransfers;
		$data['iTotalTransactions'] = $iTotalTransactions;
		$data['arJournalTransactions'] = $arJournalTransactions;
		$data['show_more'] = $show_more;
		$data['iLimit'] = $iLimit;
		$data['iPage'] = $iPage;
		$data['szMetaTagTitle'] = "User Dashboard";
		$data['id'] = $this->User_Model->id;
		$data['szFirstName'] = $this->User_Model->szFirstName;
		$data['active_menu'] = "dashboard";
		$data['szFinicityInstitution'] = $this->User_Model->szFinicityInstitution;
		$data['szFinicityAccountNumber'] = $this->User_Model->szFinicityAccountNumber;
		$data['szFinicityAccountTransitNumber'] = $this->User_Model->szFinicityAccountTransitNumber;

        $this->load->view('templates/header', $data);
        $this->load->view('users/dashboard');
        $this->load->view('templates/footer');
	}
	
	public function commands()
	{
		$is_user_login = is_user_login($this);
		$user_session = $this->session->userdata('user_arr');
		if(!$this->User_Model->loadCustomer($user_session['id']))
		{
			$is_user_login = false;
		}
		
		/*if($this->User_Model->idFinicityInstitution == '')
		{
			$is_user_login = false;
		}*/
		
		if(!$is_user_login || $this->User_Model->iSignupStep < 4)
		{
			CustomerLogout($this);
			ob_end_clean();
			header("Location:" . __SECURE_BASE_URL__ . "/users/login");
			die;
		}
		
		$data['is_user_login'] = $is_user_login;
		$data['szMetaTagTitle'] = "User Commands";
		$data['szFirstName'] = $this->User_Model->szFirstName;
		$data['active_menu'] = "commands";
		$data['obj'] = $this;

        $this->load->view('templates/header', $data);
        $this->load->view('users/commands');
        $this->load->view('templates/footer');
	}
	
	public function help()
	{
		$is_user_login = is_user_login($this);
		$user_session = $this->session->userdata('user_arr');
		if(!$this->User_Model->loadCustomer($user_session['id']))
		{
			$is_user_login = false;
		}
		
		/*if($this->User_Model->idFinicityInstitution == '')
		{
			$is_user_login = false;
		}*/
		
		if(!$is_user_login || $this->User_Model->iSignupStep < 4)
		{
			CustomerLogout($this);
			ob_end_clean();
			header("Location:" . __SECURE_BASE_URL__ . "/users/login");
			die;
		}
		
		$data['is_user_login'] = $is_user_login;
		$data['szMetaTagTitle'] = "Help Center";
		$data['szFirstName'] = $this->User_Model->szFirstName;
		$data['active_menu'] = "help";
		$data['obj'] = $this;

        $this->load->view('templates/header', $data);
        $this->load->view('users/help');
        $this->load->view('templates/footer');
	}
	
	public function saving_account_details($arg1='')
	{
		$is_user_login = is_user_login($this);
		$user_session = $this->session->userdata('user_arr');
		if(!$this->User_Model->loadCustomer($user_session['id']))
		{
			$is_user_login = false;
		}
		
		/*if($this->User_Model->idFinicityInstitution == '')
		{
			$is_user_login = false;
		}*/
		
		if(!$is_user_login || $this->User_Model->iSignupStep < 4)
		{
			CustomerLogout($this);
			ob_end_clean();
			header("Location:" . __SECURE_BASE_URL__ . "/users/login");
			die;
		}
		
		$data['done'] = false;
		if(!empty($_POST['arBanking']))
		{
			if(!empty($_POST['arBanking']['p_back']))
			{
				$this->User_Model->backToOldSavingAccount($_POST['arBanking']['p_id']);
			}
			else
			{
				$defaultFile = false;
				$arExclude = array();
				if(!empty($_POST['arBanking']['p_confirm']))
				{
					$defaultFile = true;
					$this->User_Model->szVerificationFile = "SAME-AS-CHECKING";
					if(sanitize_all_html_input(trim($_POST['arBanking']['p_transit_number'])) == "")
					{
						$arExclude[] = "p_transit_number";
						$this->User_Model->szTransitNumber = "";			
					}
				}
				
				if($this->User_Model->validateBankingInformation($_POST['arBanking'], $arExclude))
				{
					if((int)$_POST['arBanking']['p_changed'] == 1)
					{
						$this->User_Model->removeAccountChanged($_POST['arBanking']['p_id']);
					}
					$result = $this->User_Model->updateCustomerBankingInformation(false, false, $defaultFile);
					if($result && $defaultFile)
					{
						$data['done'] = true;
					}
				}
			}
		}
		else if(!empty($_POST['arVerify']))
		{
			if($this->User_Model->validateBankingInformation($_POST['arVerify'], array('p_institution', 'p_institution_id', 'p_transit_number', 'p_account_number')))
			{
				if($_FILES['p_verification_file']['name'] == '')
				{
					$this->User_Model->addError("p_verification_file", "Verification file is required.");
				}
				else
				{
					if($this->User_Model->validateInput($_FILES['p_verification_file'], __VLD_CASE_IMAGE__ , "p_verification_file", "Verification file", false, false, true))
					{
						if($this->User_Model->updateCustomerBankingInformationVerificationFile($_FILES['p_verification_file']))
						{
							$data['done'] = true;
						}
					}
				}
			}
		}
		
		if(!empty($_POST['p_change_account']))
		{
			$this->User_Model->removeCustomerBankingInformation();
			$this->User_Model->szInstitution = '';
			$this->User_Model->szTransitNumber = '';
			$this->User_Model->szAccountNumber = '';
			$this->User_Model->szVerificationFile = '';
		}
		
		$data['szMetaTagTitle'] = "Saving Account Details";
		$data['szFirstName'] = $this->User_Model->szFirstName;
		$data['active_menu'] = "saving";
		$data['is_user_login'] = $is_user_login;
		$data['obj'] = $this;
		$data['arg1'] = $arg1;

        $this->load->view('templates/header', $data);
        $this->load->view('users/saving_account.php');
        $this->load->view('templates/footer');
	}
	
	function forgot_password($arg1='')
	{
		$is_user_login = is_user_login($this);
		if($is_user_login)
		{
			ob_end_clean();
			header("Location:" . __SECURE_BASE_URL__ . "/users/dashboard");
			die;
		}
		
		check_for_signing_process($this, '');
		$is_reset_error = false;
		
		// handle forgot password request
		if(!empty($_POST['forgot_password']))
		{
			$show_login_registration_form = false;
			# Trim all spaces
			foreach ($_POST['forgot_password'] as $key => $value)
			{
				$_POST['forgot_password'][$key] = trim($value);
			}
			
			if($this->User_Model->validateCustomerData($_POST['forgot_password'], array("p_id", "p_fname", "p_lname", "p_mobilephone", "p_password")))
			{
				if(!$this->User_Model->checkCustomerExists($_POST['forgot_password']['p_email']))
				{			
					$szForgotEmailError = "This email is not registered. Want to <a href='" . __SECURE_BASE_URL__ . "/signup'>create a new account</a>?";
				}
				else
				{
					if($this->User_Model->send_forget_password_link($_POST['forgot_password']['p_email']))
					{
						$szForgotPassSuccess = 'You have been emailed a link that will allow you to reset your password.<br><br>
						If you do not receive the password reset email, please check your "spam" folders.<br><br>
						If you need assistance, please <a href="#">Click Here to Contact Us</a>.';
					}
					else
					{
						$szForgotPassError = 'Reset password link will not Send <a href="#"><strong>Click Here to Contact Us</strong></a>';
					}
				}
			}
			else if(!empty($this->User_Model->arErrorMessages['uEmail']))
			{
				$szForgotEmailError = $this->User_Model->arErrorMessages['uEmail'];
			}
		}
		
		// handle reset password attempt
		if(!empty($_POST['reset_password']))
		{
			$szPassLink = trim($_POST['p_reset_link']);
			$this->User_Model->set_szPassword(trim($_POST['reset_password']['p_password']));
			if(!$this->User_Model->error)
			{
				$re_password = trim($_POST['reset_password']['p_re_password']);
				if(trim($_POST['reset_password']['p_re_password']) == $this->User_Model->szPassword)
				{
					if($this->User_Model->updatePassword($this->User_Model->szPassword, $_POST['reset_password']['p_userid']))
					{
						$this->User_Model->remove_link_key_by_email($_POST['reset_password']['p_email']);
						$this->session->set_userdata('reset_pass_success_msg', 'Your Password has been successfully reset.');
						
						// login customer
						$this->User_Model->loadCustomer($_POST['reset_password']['p_userid']);
						$data['id'] = $this->User_Model->id;
						$data['szEmail'] = $this->User_Model->szEmail;
					    $data['szFirstName'] = $this->User_Model->szFirstName;
		    
						set_customer_cookie($this, $data);
														
						ob_end_clean();
						header( 'Location:'.__SECURE_BASE_URL__ . "/users/dashboard");
						die();
					}
					else
					{
						$szResetPassError = 'Reset password link will not Send <a href="#"><strong>Click Here to Contact Us</strong></a>';
						$is_reset_error = true;
					}
				}
				else
				{
					$resetRePassError = 'Password does not match.';
					$is_reset_error = true;
				}
			}
			else if(!empty($this->User_Model->arErrorMessages['p_password']))
			{
				$resetPassError = $this->User_Model->arErrorMessages['p_password'];
				$is_reset_error = true;
			}
		}
		
		$isPassLinkExists = false;
		if(!$is_reset_error)
			$szPassLink = sanitize_all_html_input(trim($arg1));
		
		if($szPassLink != '' && $this->User_Model->is_link_key_exists($szPassLink))
		{
			if($this->User_Model->loadCustomer($this->User_Model->id))
				$isPassLinkExists = true;
		}
		else
		{
			$_POST['forgot_password']['p_func'] = "Forgot Password";
		}
		
		$data['is_user_login'] = $is_user_login;
		$data['isPassLinkExists'] = $isPassLinkExists;
		$data['szPassLink'] = $szPassLink;
		$data['id'] = $this->User_Model->id;
		$data['szEmail'] = $this->User_Model->szEmail;
		$data['resetPassError'] = $resetPassError;
		$data['resetRePassError'] = $resetRePassError;
		$data['szForgotPassSuccess'] = $szForgotPassSuccess;
		$data['szForgotEmailError'] = $szForgotEmailError;
		
		$this->load->view('templates/header', $data);
        $this->load->view('users/forgot_password.php');
        $this->load->view('templates/footer');
	}
}

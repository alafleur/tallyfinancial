<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Ajax_Controller
 * @property User_Model $User_Model
 * @property Finicity_Model $Finicity_Model
 */
class Ajax_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('User_Model');
	}

	public function index()
	{
		$p_func = trim($_REQUEST['p_func']);
		
		if($p_func == "CHECK_DUPLICATE_EMAIL")
		{
			$szEmail = trim($_POST['szEmail']);
			$idUser = (int)$_POST['idUser'];
			$id = trim($_POST['id']);
			
			if($idUser == 0)
				$idUser = false;
			
			if($szEmail != "")
			{
				if($this->User_Model->checkCustomerExists($szEmail, $idUser))
				{	
					echo "ERROR|||$id|||This email is already registered. Want to <a href='" . __SECURE_BASE_URL__ . "/users/login'>login</a> or <a href='" . __SECURE_BASE_URL__ . "/users/forgot-password'>reset your password</a>?";
				}
				else
				{
					echo "SUCCESS|||$id|||Email Address looks good.";
				}
			}
			else
			{
				echo "ERROR|||$id|||Email Address is required.";
			}
		}
		else if($p_func == "CHECK_EMAIL_REGISTERED")
		{
			$szEmail = trim($_POST['szEmail']);
			$idUser = (int)$_POST['idUser'];
			$id = trim($_POST['id']);
			
			if($idUser == 0)
				$idUser = false;
			
			if($szEmail != "")
			{
				if($UserExist = $this->User_Model->checkCustomerExists($szEmail, $idUser))
				{	
					echo "SUCCESS|||$id|||Email Address looks good.";
				}
				else
				{
					echo "ERROR|||$id|||This email is not registered. Want to <a href='" . __SECURE_BASE_URL__ . "/users/signup'>create a new account</a>?";
				}
			}
			else
			{
				echo "ERROR|||$id|||Email Address is required.";
			}
		}
		else if($p_func == "VALIDATE_TRANSIT_NUMBER")
		{
			$szTransitNumber = trim($_POST['p_transit_number']);
			$id = trim($_POST['p_id']);
			
			$this->User_Model->set_szTransitNumber($szTransitNumber);
			
			if(empty($this->User_Model->arErrorMessages['p_transit_number']))
			{	
				echo "SUCCESS|||$id|||Transit number looks good.";
			}
			else
			{
				echo "ERROR|||$id|||{$this->User_Model->arErrorMessages['p_transit_number']}";
			}
		}
		else if($p_func == "SEARCH_INSTITUTE")
		{
			$szQuery = sanitize_all_html_input(trim($_REQUEST['p_keyword']));
			$showAll = (int)$_REQUEST['p_show_all'];
			$arInstitutions = $this->User_Model->getInstitution("szName LIKE '%" . $this->User_Model->sql_real_escape_string($szQuery) . "%'" . ($showAll ? "" :  " AND isMain = 0"));
			if(!empty($arInstitutions))
			{
				$iTotal = count($arInstitutions);
				echo "<ul>";
				foreach($arInstitutions as $i=>$institute)
				{
					echo "<li" . ($i == ($iTotal - 1) ? ' class="last"' : '') . "><a href=\"javascript:void(0);\" id=\"{$institute['id']}\" onclick=\"handle_suggestions_click(this);\">{$institute['szName']}</a></li>\n";
				}
				echo "</ul>";
			}
			else
			{
				echo "<ul><li class=\"last\"><a href='#'><span>Look like we don't support that bank yet.</span> <!--span class='link'>Tell us about it.</span--></a></li></ul>";
			}
		}
		else if($p_func == "GET_INSTITUTE_LOGIN_FORM")
		{
			$this->load->model('Finicity_Model');
			$idInstitute = (int)$_POST['p_id'];
			$arInstitutionDetails = $this->User_Model->getInstitution("id = " . (int)$idInstitute);
			
			if(!empty($arInstitutionDetails))
			{	
				$response = $this->Finicity_Model->getInstitutionLoginForm($idInstitute);
				if(!empty($response['loginField']))
				{
					$this->User_Model->checkCustomerExists($this->session->userdata('signing_user'));
					$this->User_Model->updateCustomerInstitution($idInstitute);
					$this->User_Model->updateLoginStep($this->User_Model->id, 1);
					
					echo "SUCCESS|||{$arInstitutionDetails[0]['szName']}|||";?>
					<form name="frmBankLogin" id="frmBankLogin" action="" method="post" class="form-horizontal">
						<?php foreach($response['loginField'] as $key=>$field){?>
						<div class="form-group">
							<div class="col-sm-11 col-xs-10">
								<input type="<?=(strpos(strtolower(trim($field['description'])), 'password') !== false ? 'password' : 'text')?>" id="<?=trim($field['id'])?>" name="arLogin[loginField][<?=$key?>][value]" placeholder="<?=trim($field['description'])?>" class="form-control required">								
							</div>
							<?php /*if(!empty($field['instructions'])){?>
							<div class="col-sm-1 col-xs-2">
								<a href="#" data-toggle="tooltip" title="<?=trim($field['instructions'][0])?>"><i class="fa fa-info-circle"></i></a>						
							</div>
							<?php }*/?>
						</div>
						<input type="hidden" name="arLogin[loginField][<?=$key?>][id]" value="<?=trim($field['id'])?>">
						<input type="hidden" name="arLogin[loginField][<?=$key?>][name]" value="<?=trim($field['name'])?>">
						<?php }?>
						<div class="form-group">
							<div class="col-sm-11 col-xs-10"><button type="button" class="btn" onclick="addInstitutionAccounts();">Login</button></div>
						</div>
						<input type="hidden" name="arLogin[id]" value="<?=$idInstitute?>">
						<div>
							We never see or store your credentials, they are sent directly to your bank.
						</div>
					</form>
					<?php
				}
				else
				{
					echo "ERROR|||Unable to get institution details.";
				}
			}
			else
			{
				echo "ERROR|||Unable to get institution details.";
			}
		}
		else if($p_func == "LOGIN_WITH_INSTITUTION" || $p_func == "LOGIN_WITH_INSTITUTION_MFA" || $p_func == "ACTIVATE_CUSTOMER_ACCOUNT")
		{
			$show_mfa_form = false;
			$this->load->model('Finicity_Model');
			$idInstitute = (int)$_POST['arLogin']['id'];				
			$is_activation = ((int)$_POST['arLogin']['activation-mfa'] == 1 ? true : false);
			$is_get_statement = ((int)$_POST['arLogin']['get-statement-mfa'] == 1 ? true : false);
			
			if($this->User_Model->checkCustomerExists($this->session->userdata('signing_user')))
			{	
				$arInstitutionDetails = $this->User_Model->getInstitution("id = " . (int)$idInstitute);
				if(!empty($arInstitutionDetails))
				{
					$this->User_Model->loadCustomer($this->User_Model->id);
					if($p_func == "ACTIVATE_CUSTOMER_ACCOUNT")
					{
						$arAccounts = $this->Finicity_Model->getCustomerAccountsByInstitution($this->User_Model->idFinicity, $idInstitute);
						if(!isset($arAccounts['account'][0]))
						{
							$arAccounts = $arAccounts['account'];
							$arAccounts['account'] = array();
							$arAccounts['account'][0] = $arAccounts;
						}
						$i = 0;
						$accountData = array();
						if(!empty($arAccounts['account']))
						{
							$isCheckingFound = $isSavingFound = false;
							foreach($arAccounts['account'] as $account)
							{
								if(!$isCheckingFound && trim($account['type']) == "checking")
								{									
									foreach($account as $key=>$value)
									{
										if($key == "id" || $key == "number" || $key == "name" || $key == "type" || $key == "status")
										{
											$accountData[$i][$key] = $value;
										}
									}
									$i++;
									$isCheckingFound = true;
								}
								else if(!$isSavingFound && trim($account['type']) == "savings")
								{
									foreach($account as $key=>$value)
									{
										if($key == "id" || $key == "number" || $key == "name" || $key == "type" || $key == "status")
										{
											$accountData[$i][$key] = $value;
										}
									}
									$i++;
									$isSavingFound = true;
								}
							}
						}
						$activation_response = $this->Finicity_Model->activateCustomerAccounts($this->User_Model->idFinicity, $idInstitute, $accountData);
					}
					else if($p_func == "LOGIN_WITH_INSTITUTION_MFA")
					{
						$mfa_session = trim($_POST['arLogin']['MFA-Session']);
						
						if($is_get_statement)
						{
							$idAccount = (int)$_POST['arLogin']['idAccount'];
							$szAccountNumber = trim($_POST['arLogin']['szAccountNumber']);
							$get_statement_response_mfa = $response = $this->Finicity_Model->getAccountStatementFileMFA($this->User_Model->idFinicity, $idAccount, $mfa_session, $_POST['arLogin']['questions']);
						}
						else if($is_activation)
						{
							$accountData = $_POST['arLogin']['accountData'];
							$response = $this->Finicity_Model->activateCustomerAccountsMFA($this->User_Model->idFinicity, $idInstitute, $mfa_session, $accountData, $_POST['arLogin']['questions']);
						}
						else				
							$response = $this->Finicity_Model->addUserInstitutionAccountsMFA($this->User_Model->idFinicity, $idInstitute, $mfa_session, $_POST['arLogin']['questions']);
					}
					else
						$response = $this->Finicity_Model->addUserInstitutionAccounts($this->User_Model->idFinicity, $idInstitute, $_POST['arLogin']['loginField']);
						
					if($response || $activation_response)
					{
						if($is_activation || $is_get_statement)
						{
							if(!$is_get_statement)
							{
								if(isset($this->Finicity_Model->arLoginResponse['account'][0]) && is_array($this->Finicity_Model->arLoginResponse['account'][0]))
								{
									foreach($this->Finicity_Model->arLoginResponse['account'] as $account)
									{
										if($account['type'] == "checking")
										{
											$idAccount = $account['id'];
											$szAccountNumber = $account['number'];
											break;
										}
									}
								}
								else
								{
									$idAccount = $this->Finicity_Model->arLoginResponse['account']['id'];
									$szAccountNumber = $this->Finicity_Model->arLoginResponse['account']['number'];
								}						
								$get_statement_response = $this->Finicity_Model->getAccountStatementFile($this->User_Model->idFinicity, $idAccount);
							}
							
							if($get_statement_response || $get_statement_response_mfa)
							{
								echo "SUCCESS|||$idInstitute|||$idAccount|||$szAccountNumber|||";
								$statement_file = "{$this->User_Model->id}-customer-statement.pdf";
								$file = fopen(__APP_PATH__."/statements/$statement_file", "w");
								fwrite($file, $this->Finicity_Model->arGetStatementResponse);
								fclose($file);
								echo $statement_file;
							}
							else if($this->Finicity_Model->isGetStatementMFA)
							{
								echo "MFA|||{$arInstitutionDetails[0]['szName']}|||";
								//$mfa_for_message = "Fetching your account details, please answer once more...";
								$mfa_for_message = "Please complete your security questions. Depending on your bank, you may be required to answer more than once.";
								$get_statement_mfa = $show_mfa_form = true;
								$mfa_session = $this->Finicity_Model->szGetStatementMFASession;
							}
							else if($this->Finicity_Model->szGetStatementError)
							{
								echo "ERROR|||{$this->Finicity_Model->szGetStatementError}";
							}
							else
							{
								echo "ERROR|||Unable to make institution login.";
							}
						}
						else
						{
							if(!$activation_response)
							{
								$accountData = array();
								if(!empty($this->Finicity_Model->arLoginResponse['account']))
								{
									$isCheckingFound = $isSavingFound = false;
									foreach($this->Finicity_Model->arLoginResponse['account'] as $account)
									{
										if(!$isCheckingFound && trim($account['type']) == "checking")
										{
											$accountData[] = $account;
											$isCheckingFound = true;
										}
										else if(!$isSavingFound && trim($account['type']) == "savings")
										{
											$accountData[] = $account;
											$isSavingFound = true;
										}
									}
								}
								$this->User_Model->updateLoginStep($this->User_Model->id, 2);
								$activation_response = $this->Finicity_Model->activateCustomerAccounts($this->User_Model->idFinicity, $idInstitute, $accountData);
							}
							
							if($activation_response)
							{	
								if(isset($this->Finicity_Model->arLoginResponse['account'][0]) && is_array($this->Finicity_Model->arLoginResponse['account'][0]))
								{
									foreach($this->Finicity_Model->arLoginResponse['account'] as $account)
									{
										if($account['type'] == "checking")
										{
											$idAccount = $account['id'];
											$szAccountNumber = $account['number'];
											break;
										}
									}
								}
								else
								{
									$idAccount = $this->Finicity_Model->arLoginResponse['account']['id'];
									$szAccountNumber = $this->Finicity_Model->arLoginResponse['account']['number'];
								}
																
								$get_statement_response = $this->Finicity_Model->getAccountStatementFile($this->User_Model->idFinicity, $idAccount);
								
								if($get_statement_response)
								{
									echo "SUCCESS|||$idInstitute|||{$idAccount}|||{$szAccountNumber}|||";
									$statement_file = "{$this->User_Model->id}-customer-statement.pdf";
									$file = fopen(__APP_PATH__."/statements/$statement_file", "w");
									fwrite($file, $this->Finicity_Model->arGetStatementResponse);
									fclose($file);
									echo $statement_file;
								}
								else if($this->Finicity_Model->isGetStatementMFA)
								{
									echo "MFA|||{$arInstitutionDetails[0]['szName']}|||";
									$mfa_for_message = "Fetching your account details, please answer once more...";
									$get_statement_mfa = $show_mfa_form = true;
									$mfa_session = $this->Finicity_Model->szGetStatementMFASession;
								}
								else if($this->Finicity_Model->szGetStatementError)
								{
									echo "ERROR|||{$this->Finicity_Model->szGetStatementError}";
								}
								else
								{
									echo "ERROR|||Unable to make institution login.";
								}
							}
							else if($this->Finicity_Model->isLoginMFA)
							{
								echo "MFA|||{$arInstitutionDetails[0]['szName']}|||";
								$mfa_for_message = "Please complete your security questions. Depending on your bank, you may be required to answer more than once.";
								$activation_mfa = $show_mfa_form = true;
								$mfa_session = $this->Finicity_Model->szLoginMFASession;
							}
							else if($this->Finicity_Model->szLoginError)
							{
								if((int)$this->Finicity_Model->szLoginErrorCode == 325)
								{
									echo "MFA|||{$arInstitutionDetails[0]['szName']}|||";
									echo '
									<div>Your account currently is not responding. Please wait for five minutes to get it resume.</div>
									<div class="form-group">
										<p class="pull-right"><label>Waiting Time Left:</label> <span id="seconds">299</span> seconds</p>
									</div>
									';
								}
								else
								{
									echo "ERROR|||{$this->Finicity_Model->szLoginError}";
								}
							}
							else
							{
								echo "ERROR|||Unable to make institution login.";
							}
						}
					}
					else if($this->Finicity_Model->isLoginMFA)
					{
						echo "MFA|||{$arInstitutionDetails[0]['szName']}|||";
						if($is_activation || $p_func == "ACTIVATE_CUSTOMER_ACCOUNT")
							$mfa_for_message = "Please complete your security questions. Depending on your bank, you may be required to answer more than once.";
						else
							$mfa_for_message = "Please complete your security questions. Depending on your bank, you may be required to answer more than once.";
						$show_mfa_form = true;
						$mfa_session = $this->Finicity_Model->szLoginMFASession;
					}
					else if($this->Finicity_Model->isGetStatementMFA)
					{
						echo "MFA|||{$arInstitutionDetails[0]['szName']}|||";
						$mfa_for_message = "Fetching your account details, please answer once more...";
						$get_statement_mfa = $show_mfa_form = true;
						$mfa_session = $this->Finicity_Model->szGetStatementMFASession;
					}
					else if($this->Finicity_Model->szLoginError)
					{
						if((int)$this->Finicity_Model->szLoginErrorCode == 325)
						{
							echo "MFA|||{$arInstitutionDetails[0]['szName']}|||";
							echo '
							<div>Your account currently is not responding. Please wait for five minutes to get it resume.</div>
							<div class="form-group">
								<p class="pull-right" style="display: none;"><label>Waiting Time Left:</label> <span id="seconds">299</span> seconds</p>
							</div>
							';
						}
						else
						{
							echo "ERROR|||{$this->Finicity_Model->szLoginError}";
						}
					}
					else
					{
						echo "ERROR|||Unable to make institution login.";
					}
				}
				else
				{
					echo "ERROR|||Unable to get institution details.";
				}
			}
			else
			{
				echo "ERROR|||Unable to get customer details.";
			}
			
			if($show_mfa_form){?>
			<form name="frmBankLoginMFA" id="frmBankLoginMFA" action="" method="post">
				<div><?=$mfa_for_message?></div>
				<?php foreach($this->Finicity_Model->arMFA['questions'] as $i=>$question){?>
				<?php if(!empty($question['choice'])){?>
				<div class="form-group">
					<label><?=trim($question['text'])?></label>
					<?php foreach($question['choice'] as $choice){?>
					<div class="checkbox-container"><input type="radio" name="arLogin[questions][<?=$i?>][answer]" value="<?=$choice['id']?>"> <?=$choice['value']?></div>
					<?php }?>						
				</div>
				<?php }else if(!empty($question['imageChoice'])){?>
				<div class="form-group">
					<label><?=trim($question['text'])?></label>
					<?php foreach($question['imageChoice'] as $choice){ $imageSize = getImageSizeByString(trim($choice['value']));?>
					<div class="row">
						<div class="col-sm-1 col-xs-2 checkbox-container"><input type="radio" name="arLogin[questions][<?=$i?>][answer]" value="<?=$choice['id']?>"></div>
						<div class="col-sm-11 col-xs-10"><span class="captch-image" style="background-image:url(<?=trim($choice['value'])?>);width:<?=$imageSize[0]?>px;height:<?=$imageSize[1]?>px;display:inline-block;"></span></div>
					</div>
					<?php }?>						
				</div>
				<?php }else if(!empty($question['image'])){$imageSize = getImageSizeByString(trim($question['image']));?>
				<div class="form-group">
					<label><?=trim($question['text'])?></label>
					<div class="row">
						<div class="col-sm-8"><input type="text" id="question-<?=$i?>" name="arLogin[questions][<?=$i?>][answer]" placeholder="Image code" class="required form-control"></div>
						<div class="col-sm-4"><span class="captch-image pull-right" style="background-image:url(<?=trim($question['image'])?>);width:<?=$imageSize[0]?>px;height:<?=$imageSize[1]?>px;display:block;"></span></div>
					</div>
				</div>
				<?php } else {?>
				<div class="form-group">
					<label><?=trim($question['text'])?></label>
					<input type="text" id="question-<?=$i?>" name="arLogin[questions][<?=$i?>][answer]" placeholder="Your answer" class="form-control required">
				</div>
				<?php }}?>
				
				<div class="form-group">
					<button type="button" class="btn" onclick="addInstitutionAccountsMFA();">Continue</button>
					<p class="pull-right" style="display: none;"><label>Time Left:</label> <span id="seconds">119</span> seconds</p>
				</div>
				<div>
					We never see or store your credentials, they are sent directly to your bank.
				</div>
				<input type="hidden" name="arLogin[questions][<?=$i?>][text]" value="<?=sanitize_post_field_value($question['text'])?>">
				<input type="hidden" name="arLogin[id]" value="<?=$idInstitute?>">
				<input type="hidden" name="arLogin[MFA-Session]" value="<?=$mfa_session?>">
				<?php if($get_statement_mfa){?>
				<input type="hidden" name="arLogin[get-statement-mfa]" value="1">
				<input type="hidden" name="arLogin[idAccount]" value="<?=$idAccount?>">
				<input type="hidden" name="arLogin[szAccountNumber]" value="<?=$szAccountNumber?>">
				<?php }?>
				<?php if($is_activation || $activation_mfa || $p_func == "ACTIVATE_CUSTOMER_ACCOUNT"){?>
				<input type="hidden" name="arLogin[activation-mfa]" value="1">
				<?php if($is_activation || $p_func == "ACTIVATE_CUSTOMER_ACCOUNT"){?>
				<input type="hidden" name="arLogin[accountData]" value="<?=serialize($accountData)?>">
				<?php } else { $accountData = $this->Finicity_Model->arLoginResponse['account']; }?>
				<?php if(!empty($accountData)){$isCheckingFound = $isSavingFound = false;foreach($accountData as $k=>$account){ if((!$isCheckingFound && trim($account['type']) == "checking") || (!$isSavingFound && trim($account['type']) == "savings")){?>							
				<input type="hidden" name="arLogin[accountData][<?=$k?>][id]" value="<?=trim($account['id'])?>">
				<input type="hidden" name="arLogin[accountData][<?=$k?>][number]" value="<?=trim($account['number'])?>">
				<input type="hidden" name="arLogin[accountData][<?=$k?>][name]" value="<?=trim($account['name'])?>">
				<input type="hidden" name="arLogin[accountData][<?=$k?>][type]" value="<?=trim($account['type'])?>">
				<input type="hidden" name="arLogin[accountData][<?=$k?>][status]" value="<?=trim($account['status'])?>">
				<?php if(trim($account['type']) == "checking")$isCheckingFound = true; if(trim($account['type']) == "savings") $isSavingFound = true;}}}}?>
			</form>
			<?php }
		}
		else if($p_func == "GET_ACCOUNT_TRANSACTIONS")
		{
			$this->load->model('Finicity_Model');
			
			$idCustomer = (int)$_POST['p_cid'];
			$idAccount = (int)$_POST['p_aid'];
			
			$arTransactions = array();
			$response = $this->Finicity_Model->getCustomerAccountsTransactions($idCustomer, $idAccount);
			$arTransactions = $response['transaction'];
				
			if(!empty($arTransactions)){
			echo "SUCCESS|||Account Transactions|||";?>
			<table class="table-format2">
				<thead>
					<tr class="thead">
						<td>Amount</td>
						<td>Description</td>
						<td>Type</td>
						<td>Status</td>
						<td>Transaction Date</td>
					</tr>
				</thead>
				<?php foreach($arTransactions as $accounts){?>
				<tr>
					<td><?=$accounts['amount']?></td>
					<td><?=$accounts['description']?></td>
					<td><?=(isset($accounts['type']) ? $accounts['type'] : "NA")?></td>
					<td><?=$accounts['status']?></td>
					<td><?=date("m/d/Y", $accounts['transactionDate'])?></td>
				</tr>
				<?php }?>
			</table>
			<?php } else if(!empty($this->Finicity_Model->szResolveError)){echo "ERROR|||";?>
			<div class="alert alert-danger"><?=trim($this->Finicity_Model->szResolveError)?></div>
			<?php } else {echo "ERROR|||";?>
			<div class="alert alert-danger">There is no transaction to list</div>
			<?php }
		}
		else if($p_func == "IMPORT_CUSTOMER_TRANSACTIONS")
		{
			$idFinCustomer = (int)$_POST['p_cid'];
			if($idFinCustomer > 0)
			{
				getFinicityTransactions($this, $idFinCustomer);
			}
		}
	}
}
?>
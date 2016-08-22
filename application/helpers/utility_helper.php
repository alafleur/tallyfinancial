<?php
function secure_base_url(){
	return base_url();
}

function asset_url(){
   return base_url() . "assets/";
}

function app_path(){
	return FCPATH;
}

function assets_path()
{
	return FCPATH . "assets/";
}

function set_customer_cookie($obj, $data)
{
	$obj->session->unset_userdata('user_arr');
	
	$user_arr['id'] = $data['id'];
	$user_arr['login'] = $data['szEmail'];
    $user_arr['fname'] = $data['szFirstName'];
    $obj->session->set_userdata('user_arr', $user_arr);
    
    //set data to cookie and keep for 1 month
    $encKey1 = "#12EQ#83#";$encKey2="#AR3UIL#452#";
    $cookieData = $data['id'] . "~$encKey1" . $data['szEmail'] . "~$encKey2" . $data['szFirstName'];
    
    $encryptedC = base64_encode($cookieData);
    $cookie = array(
	    'name'   => __FRONT_END_COOKIE__,
	    'value'  => $encryptedC,
	    'expire' => time()+60*60*24*30
	);
	
	$obj->input->set_cookie($cookie);
}

function CustomerLogout($obj)
{
	$obj->session->unset_userdata('user_arr');
	
	$cookie = array(
	    'name'   => __FRONT_END_COOKIE__,
	    'value'  => '',
	    'expire' => time()-60*60*24*30
	);
	
	$obj->input->set_cookie($cookie);
}

function AdminLogout($obj)
{
	$obj->session->unset_userdata('arr');
	
	$cookie = array(
	    'name'   => '__ulfabt_go',
	    'value'  => '',
	    'expire' => time()-60*60*24*30
	);
    
	$obj->input->set_cookie($cookie);
}

function is_user_login($obj)
{
	return $obj->User_Model->checkCustomerExists();
}

function check_for_signing_process($kUser, $arg1)
{
	if($kUser->session->userdata('signing_user'))
	{
		if($kUser->User_Model->checkCustomerExists($kUser->session->userdata('signing_user')))
		{
			$kUser->session->set_userdata('signing_user', $kUser->User_Model->szEmail);
			if($kUser->User_Model->loadCustomer($kUser->User_Model->id))
			{
				if($kUser->User_Model->iSignupStep == 1 && $arg1 != "mobile-number" && $arg1 != "mobile-number-confirmation" && $arg1 != "mobile-number-resend-code")
				{
					$arMap = $kUser->User_Model->getMobileVerificationMapping($kUser->User_Model->id);
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
				else if($kUser->User_Model->iSignupStep == 2 && $arg1 !=  "link-your-bank" && $arg1 != "authenticate")
				{
					ob_end_clean();
					header("Location:" . __SECURE_BASE_URL__ . "/users/signup/link-your-bank");
					die;
				}
				else if($kUser->User_Model->iSignupStep == 3 && $arg1 != "account-information")
				{
					ob_end_clean();
					header("Location:" . __SECURE_BASE_URL__ . "/users/signup/account-information");
					die;
				}
				else if($kUser->User_Model->iSignupStep == 4 && $arg1 != "done")
				{
					$kUser->session->set_userdata('signing_user');
				}
				else if(trim($arg1) != "mobile-number" && trim($arg1) != "mobile-number-confirmation" && trim($arg1) != "mobile-number-resend-code" && trim($arg1) !=  "link-your-bank" && trim($arg1) != "authenticate" && trim($arg1) != "account-information" && trim($arg1) != "done")
				{
					$kUser->session->unset_userdata('signing_user');
					
					$data['id'] = $kUser->User_Model->id;
					$data['szEmail'] = $kUser->User_Model->szEmail;
				    $data['szFirstName'] = $kUser->User_Model->szFirstName;
					set_customer_cookie($kUser, $data);
					
					ob_end_clean();
					header("Location:" . __SECURE_BASE_URL__ . "/users/dashboard");
					die;
				}
			}
		}
	}
}

function sanitize_all_html_input($value)
{
	if(!empty($value))
	{
		$value=strip_tags($value);
	
		if(strpos($value,"'>") !== false)
			$value=str_replace("'>","",$value);
		if(strpos($value,'">') !== false)
			$value=str_replace('">',"",$value);
	}
	
	return $value;
}

function sanitize_post_field_value($value)
{
	return htmlentities(trim($value));
}

function format_number($number,$seprator=false)
{
	if($seprator)
		return number_format((float)$number, 2, ".", ",");
	else
		return number_format((float)$number, 2, ".", "");
}

function checkCustomerLogin($obj)
{
	$login=0;
	$user_session = $obj->session->userdata('user_arr');
	
	// check session variable
	if((int)$user_session['id']>0)
	{
		$login=1;
	}
	else
	{		
      	if($obj->input->cookie(__FRONT_END_COOKIE__))
     	{
       		$encKey1 = "#12EQ#83#";$encKey2="#AR3UIL#452#";
              
         	$decryptedC1 = base64_decode($obj->input->cookie(__FRONT_END_COOKIE__));
         	$decryptedC2 = preg_replace("/$encKey1/", "", $decryptedC1);
          	$decryptedC3 = preg_replace("/$encKey2/", "", $decryptedC2);

        	list($user_arr['id'], $user_arr['login'], $user_arr['fname']) = explode("~", $decryptedC3);
        	$obj->session->set_userdata('user_arr', $user_arr);
         	$login = 1;
		}
            
	}
	
	if($login==1)
	{	
		if($obj->session->userdata('user_arr'))
		{
			//set data to cookie and keep for 1 month
		    $encKey1 = "#12EQ#83#";$encKey2="#AR3UIL#452#";
		    $cookieData = $user_session['id'] . "~$encKey1" . $user_session['login'] . "~$encKey2" . $user_session['fname'];
		    
		    $encryptedC = base64_encode($cookieData);
		    $cookie = array(
			    'name'   => __FRONT_END_COOKIE__,
			    'value'  => $encryptedC,
			    'expire' => time()+60*60*24*30
			);
			
			$obj->input->set_cookie($cookie);
		}
		
		return true;
	}
	else
	{
		return false;
	}
}

function encrypt($string, $encrypt=true)
{
	if($encrypt)
	 	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(__ENCRYPT_KEY__), $string, MCRYPT_MODE_CBC, md5(md5(__ENCRYPT_KEY__))));
	else
	 	return $string;
}

function decrypt($encrypted, $decrypt=true)
{
	if($decrypt)
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(__ENCRYPT_KEY__), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5(__ENCRYPT_KEY__))), "\0");
	else
		return $encrypted;
}

function sendMessege($to, $sms, $media='')
{
	// Get a reference to the controller object
    $CI = get_instance();
    $CI->load->library('CI_Twilio');
     
    $client = new Services_Twilio(__TWILIO_ACCOUNT_SID__, __TWILIO_AUTH_TOKEN__);
    
    try 
    {
    	if($media != '')
        	$send = $client->account->messages->sendMessage(__TWILIO_FROM_NUMBER__, $to, $sms, $media);
     	else
        	$send = $client->account->messages->sendMessage(__TWILIO_FROM_NUMBER__, $to, $sms);
        $success = 1;       
    }
    catch (Exception $e) 
    {
		$success = 0;
		$file = fopen(__APP_PATH_LOGS__ . "/sms.log", "a+");
		fwrite($file, date("Y-m-d H:i:s") . " Send SMS failed for-\n Mobile number $to\nMessage: $sms\nError: " . $e->getMessage() . "\n\n");
		fclose($file);
    }
    return $success;
}

function generateRandomString($characters = "0123456789", $length = 6) 
{
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function jsonDecode($json, $assoc = FALSE){ 
    $json = str_replace(array("\n","\r"),"",$json); 
    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
    $json = preg_replace('/(,)\s*}$/','}',$json);
    return json_decode($json,$assoc); 
}

function sendEmail($to,$from,$subject,$message,$attach_file='')
{
	// Get a reference to the controller object
    $CI = get_instance();
    $CI->load->library('CI_PHPMailer');
    
	$mail = new PHPMailer();
	
	//$mail->isSMTP();
	try
	{
		// Set SMTP Server
		/*$mail->SMTPDebug  = 0;                     	// enables SMTP debug information (for testing)
		if(__SMTP_AUTH__ === true)
		{
			$mail->SMTPAuth   = true;             	// enable SMTP authentication
			$mail->SMTPSecure = "ssl";         		// sets the prefix to the servier
		}
		$mail->Host = __SMTP_HOST__;      			// SMTP server host
		$mail->Port = __SMTP_PORT__;       			// SMTP server port
		if(__SMTP_USERNAME__ != '')
		{
			$mail->Username   = __SMTP_USERNAME__;	// SMTP Server Username
		}
		if(__SMTP_PASSWORD__ != '')
		{
			$mail->Password   = __SMTP_PASSWORD__;	// SMTP Server Password
		}*/
		
		// Manage Sender Address
		$from_name = "";
		$from_email = trim($from);
		if($from_email != "")
		{
			$arFrom = explode("<",$from_email);
			if(count($arFrom) > 1)
			{
				if(strlen(trim($arFrom[0])) > 0)
				{
					$from_name = trim($arFrom[0]);
				}
				$from_email = str_replace(">","",$arFrom[1]);
				//$from_email = "tyagi6931@gmail.com";
			}
		}
		
		// Manage receiver addresses
		$to_addresses = array();
		if($to != '')
		{
			$ar_addresses = explode(",", $to);
			if(!empty($ar_addresses))
			{
				$ctr = 0;
				foreach($ar_addresses as $address)
				{
					$address_name = "";
					$address_email = trim($address);
					$ar_address = explode("<",$address_email);
					if(count($ar_address) > 1)
					{
						if(strlen(trim($ar_address[0])) > 0)
						{
							$address_name = trim($ar_address[0]);
						}
						$address_email = str_replace(">","",$ar_address[1]);
					}
					//$address_email = "lalit@whiz-solutions.com";
					$to_addresses[$ctr]['NAME'] = $address_name;
					$to_addresses[$ctr]['EMAIL'] = $address_email;
					$ctr++;
				}
			}
		}
		
		// Manage Carbon-Copy Addresses
		$cc_addresses = array();
		if($cc != '')
		{
			$ar_addresses = explode(",", $cc);
			if(!empty($ar_addresses))
			{
				$ctr = 0;
				foreach($ar_addresses as $address)
				{
					$address_name = "";
					$address_email = trim($address);
					$ar_address = explode("<",$address_email);
					if(count($ar_address) > 1)
					{
						if(strlen(trim($ar_address[0])) > 0)
						{
							$address_name = trim($ar_address[0]);
						}
						$address_email = str_replace(">","",$ar_address[1]);
					}
					//$address_email = "ltyagi33@yahoo.com";
					$cc_addresses[$ctr]['NAME'] = $address_name;
					$cc_addresses[$ctr]['EMAIL'] = $address_email;
					$ctr++;
				}
			}
		}
		
		// Manage Blind-Carbon-Copy Addresses
		$bcc_addresses = array();
		if($bcc != '')
		{
			$ar_addresses = explode(",", $bcc);
			if(!empty($ar_addresses))
			{
				$ctr = 0;
				foreach($ar_addresses as $address)
				{
					$address_name = "";
					$address_email = trim($address);
					$ar_address = explode("<",$address_email);
					if(count($ar_address) > 1)
					{
						if(strlen(trim($ar_address[0])) > 0)
						{
							$address_name = trim($ar_address[0]);
						}
						$address_email = str_replace(">","",$ar_address[1]);
					}
					//$address_email = "tyagi6931@outlook.com";
					$bcc_addresses[$ctr]['NAME'] = $address_name;
					$bcc_addresses[$ctr]['EMAIL'] = $address_email;
					$ctr++;
				}
			}
		}
		
		//echo "Hello $from_name : $from_email" . " | " . print_r($to_addresses, true) . " | " . print_r($cc_addresses, true) . " | " . print_r($bcc_addresses, true);die; 
		
		$mail->Sender = __CUSTOMER_SUPPORT_EMAIL__;			
		$mail->addReplyTo($from_email, $from_name);
		foreach($to_addresses as $address)$mail->addAddress($address['EMAIL'], $address['NAME']);
		if(!empty($cc_addresses)){foreach($cc_addresses as $address){$mail->addCC($address['EMAIL'], $address['NAME']);}}
		if(!empty($bcc_addresses)){foreach($bcc_addresses as $address){$mail->addBCC($address['EMAIL'], $address['NAME']);}}
		$mail->setFrom(__CUSTOMER_SUPPORT_EMAIL__, "Customer Support");
		$mail->Subject = $subject;
		$mail->CharSet = 'utf-8';
		$body = str_replace('\\r', '', trim($message));
		
		$mail->MsgHTML($body);
		if($attach_file != '' && file_exists($attach_file))
		{
			$mail->AddAttachment($attach_file); // attachment
		}
		
		$mail_sent = $mail->Send();
	}
	catch (phpmailerException $e) {
		$status = 0;
		$szStatusMsg = $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
		$status = 0;
		$szStatusMsg = $e->getMessage(); //Boring error messages from anything else!
	}
	
	//$szStatusMsg .= print_r($mail, true);
	unset($mail);
	
    if($mail_sent)
    {
        $success = 1;
    }
    else
    {
    	$success = 0;
    }
    
    /*$handle = fopen(__APP_PATH_LOGS__."/email.log", "a+");
	fwrite($handle, "#################################".$subject."################################\n\nTo: ".$to."\n$from_name : $from_email\r\n" . print_r($to_addresses, true) . "\r\nMessage:".$body."\n\nStatus = $szStatusMsg");
	fclose($handle);*/
	return $success;
}


function xml2Array($xml_string)
{
	$xmlObj = simplexml_load_string($xml_string);
	$arr = json_decode(json_encode((array) $xmlObj),1);
	
	if(!empty($arr['questions']['question']['imageChoice']))
	{
		foreach($arr['questions']['question']['imageChoice'] as $key=>$value)
		{
			$attr = $xmlObj->questions->question->imageChoice[$key]->attributes();
			$attr = json_decode(json_encode((array) $attr),1);
			
			$arr['questions']['question']['imageChoice'][$key] = array();
			$arr['questions']['question']['imageChoice'][$key]['value'] = $value;
			$arr['questions']['question']['imageChoice'][$key]['id'] = $attr['@attributes']['value'];
		}
	}
	
	if(!empty($arr['questions']['question']['choice']))
	{
		foreach($arr['questions']['question']['choice'] as $key=>$value)
		{
			$attr = $xmlObj->questions->question->choice[$key]->attributes();
			$attr = json_decode(json_encode((array) $attr),1);
			
			$arr['questions']['question']['choice'][$key] = array();
			$arr['questions']['question']['choice'][$key]['value'] = $value;
			$arr['questions']['question']['choice'][$key]['id'] = $attr['@attributes']['value'];
		}
	}
	
	return $arr;
}

function getImageSizeByString($str)
{
	$size = array();
	$str = trim($str);
	if($str != '')
	{
		$ar_str = explode(",", $str);
		$uri = 'data://application/octet-stream;base64,'  . $ar_str[1];
		$size = getimagesize($uri);
	}
	return $size;
}

function getFinicityTransactions($obj, $idFinCustomer=0)
{
	$obj->load->model('User_Model');
	$obj->load->model('Finicity_Model');
	$kUser = $obj->User_Model;
	$kFinicity = $obj->Finicity_Model;

	$query = "TRUNCATE " . __DBC_SCHEMATA_TRANSACTIONS__ . ";";
	$kUser->exeSQL($query);
		
	$query = "
		SELECT
			idFinicityAccount,
			idFinicity,
			idFinicityInstitution,
			dtSignupVerified
		FROM
			" . __DBC_SCHEMATA_USERS__ . "
		WHERE
			iSignupStep > 4
		" . ((int)$idFinCustomer > 0 ? "AND idFinicity = " . (int)$idFinCustomer : "") . "
	";
	if($result = $kUser->exeSQL($query))
	{
		if($kUser->iNumRows > 0)
		{	
			$arr = $kUser->getAssoc($result, true);
			var_dump($arr);
			foreach($arr as $row)
			{
				$idFinicityAccount = (int)$row['idFinicityAccount'];
				$idCustomer = (int)$row['idFinicity'];
				$idFinicityInstitution = (int)$row['idFinicityInstitution'];
				
				// Pull transactions
				$check_query = "
					SELECT
						id,
						dtDate
					FROM
						" . __DBC_SCHEMATA_TRANSACTIONS__ . "
					WHERE
						idCustomer = " . (int)$idCustomer . "
					ORDER BY
						dtDate DESC
					LIMIT 0, 1
				";
				if($check_result = $kUser->exeSQL($check_query))
				{
					if($kUser->iNumRows > 0)
					{
						$check_row = $kUser->getAssoc($check_result);
						$dtLastTransactionDate = trim($check_row['dtDate']);
						
						// get date to compare
						$m = date("m");						
						$y = date("Y");
						$y = ($m == "01" ? ($y - 1) : $y);
						$m = ($m == '01' ? '12' : ($m < 10 ? '0' : '') . ((int)$m - 1));
						$dtCompareDate = "{$y}-{$m}-01 00:00:00";
						if(strtotime($dtLastTransactionDate) < strtotime($dtCompareDate))
						{
							$dtLastTransactionDate = $dtCompareDate;
						}
						
						$array = $kFinicity->getCustomerAccountsTransactions($idCustomer, $idFinicityAccount, strtotime($dtLastTransactionDate), time());
					}
					else
					{
						$array = $kFinicity->getCustomerAccountsTransactions($idCustomer, $idFinicityAccount);
					}
				}
				var_dump($array);
				if((int)$array['@attributes']['found'] == 1)
				{
					$transaction = $array['transaction'];
					$array['transaction'] = array();
					$array['transaction'][0] = $transaction;
				}
				
				if(!empty($array['transaction']))
				{					
					foreach($array['transaction'] as $transaction)
					{
						var_dump($transaction);
						var_dump($transaction.['categorization']);
						$query = "
							INSERT INTO
								" . __DBC_SCHEMATA_TRANSACTIONS__ . "
							(
								idCustomer,
								idAccount,
								idInstitution,
								fAmount,
								szDescription,
								szCategory,
								szStatus,
								dtDate
							)
							VALUES
							(
								" . (int)$idCustomer . ",								
								" . (int)$transaction['accountId'] . ",
								" . (int)$idFinicityInstitution . ",
								" . format_number($transaction['amount']) . ",
								'" . $kUser->sql_real_escape_string(trim($transaction['description'])) . "',
								'" . $kUser->sql_real_escape_string(trim($transaction['categorization']['category'])) . "',
								'" . $kUser->sql_real_escape_string(trim($transaction['status'])) . "',
								'" . (trim($transaction['postedDate']) != '' ? $kUser->sql_real_escape_string(date("Y-m-d H:i:s", trim($transaction['postedDate']))) : '0000-00-00 00:00:00') . "'
							)
							ON DUPLICATE KEY UPDATE
								fAmount = " . format_number($transaction['amount']) . ",
								szDescription = '" . $kUser->sql_real_escape_string(trim($transaction['description'])) . "',
								szCategory = '" . $kUser->sql_real_escape_string(trim($transaction['categorization']['category'])) . "',
								szStatus = '" . $kUser->sql_real_escape_string(trim($transaction['status'])) . "',
								dtDate = '" . (trim($transaction['postedDate']) != '' ? $kUser->sql_real_escape_string(date("Y-m-d H:i:s", trim($transaction['postedDate']))) : '0000-00-00 00:00:00') . "',
								dtUpdatedOn = NOW()
						";
						//echo "$query<br><br>";die;
						$kUser->exeSQL($query);
					}
				}
								
				// Pull account details
				$arAccountDetail = $kFinicity->getCustomerAccountDetails($idCustomer, $idFinicityAccount);
				if(!empty($arAccountDetail))
				{
					$check_query = "
						SELECT
							id
						FROM
							" . __DBC_SCHEMATA_USER_CURRENT_BALANCE__ . "
						WHERE
							idCustomer = " . (int)$idCustomer . "
						AND
							fBalance = " . (float)$arAccountDetail['balance'] . "
						AND
							dtBalanceDate = '" . $kUser->sql_real_escape_string(date("Y-m-d H:i:s", trim($arAccountDetail['balanceDate']))) . "'
						AND
							dtAggregationAttemptDate = '" . $kUser->sql_real_escape_string(date("Y-m-d H:i:s", trim($arAccountDetail['aggregationAttemptDate']))) . "'
						AND
							dtAggregationSussessDate = '" . $kUser->sql_real_escape_string(date("Y-m-d H:i:s", trim($arAccountDetail['aggregationSuccessDate']))) . "'
					";
					//echo "$check_query<br><br>";
					if($check_result = $kUser->exeSQL($check_query))
					{
						if($kUser->iNumRows <= 0)
						{					
							$acc_query = "
								INSERT INTO
									" . __DBC_SCHEMATA_USER_CURRENT_BALANCE__ . "
								(
									idCustomer,
									fBalance,
									dtBalanceDate,
									dtAggregationAttemptDate,
									dtAggregationSussessDate
								)
								VALUES
								(
									" . (int)$idCustomer . ",
									" . (float)$arAccountDetail['balance'] . ",
									'" . $kUser->sql_real_escape_string(date("Y-m-d H:i:s", trim($arAccountDetail['balanceDate']))) . "',
									'" . $kUser->sql_real_escape_string(date("Y-m-d H:i:s", trim($arAccountDetail['aggregationAttemptDate']))) . "',
									'" . $kUser->sql_real_escape_string(date("Y-m-d H:i:s", trim($arAccountDetail['aggregationSuccessDate']))) . "'
								)
							";
							//echo "$acc_query<br><br>";
							$kUser->exeSQL($acc_query);
						}
					}
				}
				
				// check for last month calculations
				$today_month = date("n");
				$today_year = date("Y");
				$arLastMonthCalculations = getPreviousMonthsCalculations($idCustomer, $today_month, $today_year, $kUser, 1);
				
				if(empty($arLastMonthCalculations))
				{
					$opening_balance = 0;
					$current_balance = 0;
					$balance_date = date("Y-m-d H:i:s");
					$last_month = ($today_month == 1 ? 12 : ($today_month -1));					
					$last_month_year = ($today_month == 1 ? ($today_year - 1) : $today_year);
					$last_month_last_day = cal_days_in_month(CAL_GREGORIAN,$last_month,$last_month_year);
					$last_month = ($last_month < 10 ? "0{$last_month}" : "$last_month");
						
					// get opening balance for last month
					$bal_query = "
						SELECT
							fBalance,
							dtBalanceDate
						FROM
							" . __DBC_SCHEMATA_USER_CURRENT_BALANCE__ . "
						WHERE
							idCustomer = " . (int)$idCustomer . "
						AND
							dtBalanceDate >= '{$last_month_year}-{$last_month}-01 00:00:00'
						AND
							dtBalanceDate <= '{$balance_date}'
						ORDER BY
							dtBalanceDate ASC
						LIMIT
							0, 1
					";
					//echo "$bal_query<br><br>";
					if($bal_result = $kUser->exeSQL($bal_query))
					{
						if($kUser->iNumRows > 0)
						{
							$bal_row = $kUser->getAssoc($bal_result);
							$current_balance = format_number($bal_row['fBalance']);
							$balance_date = trim($bal_row['dtBalanceDate']);
						}
					}
					//echo "hello $current_balance | $balance_date<br><br>";
					
					// get sum of income and expence till last balance date
					$sum_query = "
						SELECT
							fAmount
						FROM
							" . __DBC_SCHEMATA_TRANSACTIONS__ . "
						WHERE
							idCustomer = " . (int)$idCustomer . "
						AND
							dtDate >= '{$last_month_year}-{$last_month}-01 00:00:00'
						AND
							dtDate <= '$balance_date'
					";
					//echo "$sum_query<br><br>";
					if($sum_result = $kUser->exeSQL($sum_query))
					{
						if($kUser->iNumRows > 0)
						{
							$ar_sum = $kUser->getAssoc($sum_result, true);
							foreach($ar_sum as $sum_row)
							{
								$current_balance = $current_balance - $sum_row['fAmount'];
							}
						}
					}
					
					$today = "{$last_month_year}-{$last_month}-{$last_month_last_day} 23:59:59";
					$opening_balance = format_number($current_balance);
					
					$obj->load->model('Cronjob_Model');
					$obj->Cronjob_Model->getCalculations($idCustomer, $opening_balance, $today);
				}
			}
		}
	}
}

function getPreviousMonthsCalculations($idCustomer, $iCurrentMonth, $iCurrentYear, $kUser, $iLast = 6)
{
	$month_query = "";
	$arReturn = array();
	$idCustomer = (int)$idCustomer;
	$iCurrentYear = (int)$iCurrentYear;
	$iCurrentMonth = (int)$iCurrentMonth;		
	$iLastMonth = ($iCurrentMonth - $iLast);
	
	if($iLastMonth <= 0)
	{
		for($i = $iLastMonth; $i < $iLastMonth + $iLast; $i++)
		{
			$iMonth = ($i <= 0 ? ($i + 12) : $i);
			$iYear = ($i <= 0 ? ($iCurrentYear - 1) : $iCurrentYear);
			
			$month_query .= ($month_query != '' ? 'OR' : '') . "				
			(
				iMonth = $iMonth
			AND
				iYear = $iYear
			)
			";
		}
		$month_query = "({$month_query})";
	}
	else
	{
		$month_query = "
			iMonth >= $iLastMonth
		AND
			iMonth < $iCurrentMonth
		AND
			iYear = $iCurrentYear
		";
	}
	
	$query = "
		SELECT
			*
		FROM
			" . __DBC_SCHEMATA_MONTHLY_CALCULATIONS__ . "
		WHERE
			idCustomer = $idCustomer
		AND
			$month_query
	";
	if($result = $kUser->exeSQL($query))
	{
		if($kUser->iNumRows > 0)
		{
			$arReturn = $kUser->getAssoc($result, true);
		}
	}
	return $arReturn;
}

function createMessage($message, $replace_ary)
{
	if (count($replace_ary) > 0)
    {
        foreach ($replace_ary as $replace_key => $replace_value)
        {
           	$message = str_replace('{'.$replace_key.'}', $replace_value, $message);
        }
    }
    return $message;
}

function sortArray($arrData, $p_sort_field, $p_sort_type = false ,$secondory_sort=false, $secondory_sort_type=false)
{
	if( !empty($arrData) )
	{
		foreach($arrData as $data)
		{
			$newData [] = $data;
		}
		for($i=0; $i<count($newData); $i++)
		{		                   	 
		 	$ar_sort_field[$i]=strtolower($newData[$i][$p_sort_field]);
		 	if($secondory_sort)
		 	{
		 		$ar_secondory_sort[$i] = strtolower($newData[$i][$secondory_sort]);
		 	}
		}
		if($secondory_sort)
		{
			array_multisort($ar_sort_field, ($p_sort_type ? SORT_DESC : SORT_ASC), $ar_secondory_sort, ($secondory_sort_type ? SORT_DESC : SORT_ASC), $newData);
		}
		else
		{
			array_multisort($ar_sort_field, ($p_sort_type ? SORT_DESC : SORT_ASC), $newData);	
		}		
		return $newData;
	}
}

function getMessageTemplate($szTempName)
{
	$CI = get_instance();
    $CI->load->model('Configuration_Model');
    
    return $CI->Configuration_Model->getTemplateValueByName($szTempName);
}
?>

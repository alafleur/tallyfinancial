<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Finicity_Model extends Database_Model
{	
	var $szFinAppToken;
	var $szLoginError;
	var $szLoginMFASession;
	var $isLoginMFA = false;
	var $arMFA;
	
	function __construct()
	{
		return true;
	}
	
	function authenticatePartner()
	{
		$App_Url = __FINICITY_API_URL__ . "/v2/partners/authentication";
		$xml = "<credentials> <partnerId>" . __FINICITY_PARTNER_ID__ . "</partnerId> <partnerSecret>" . __FINICITY_PARTNER_SECRET__ . "</partnerSecret> </credentials>";
		$Method = "POST";
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__);
		
		if($result = $this->exeAppRequest($App_Url, $Header, $xml, $Method))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
		
			if((int)$curl_info['http_code'] == 200)
			{
				return xml2Array($response);
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessfull with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function addCustomertToFinicity($data)
	{
		if(__FINICITY_TEST_MODE__)
			$App_Url = __FINICITY_API_URL__ . "/v1/customers/testing";
		else
			$App_Url = __FINICITY_API_URL__ . "/v1/customers/active";
			
		$xml = "
			<customer>
			  <username>{$data['szEmail']}</username>
			  <firstName>".str_replace(" ", "", $data['szFirstName'])."</firstName>
			  <lastName>".str_replace(" ", "", $data['szLastName'])."</lastName>
			</customer>
		";
		$this->getPartnerAccessToken();
		
		$Method = "POST";
	    $Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
	    if($result = $this->exeAppRequest($App_Url, $Header, $xml, $Method))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
		
			if((int)$curl_info['http_code'] == 201)
			{
				return xml2Array($response);
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r(xml2Array($response),true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function getPartnerAccessToken()
	{
		$query = "
			SELECT
				szToken
			FROM
				" . __DBC_SCHEMATA_FINICITY_APP_TOKEN__ . "
			WHERE
				dtCreated > '" . date("Y-m-d H:i:s", strtotime("-120 minutes")) . "'
			AND
				szToken != ''
		";
		if($result = $this->exeSQL($query))
		{
			if($this->iNumRows > 0)
			{
				$row = $this->getAssoc($result);
				$this->szFinAppToken = trim($row['szToken']);
				return true;
			}
			else
			{
				$data = $this->authenticatePartner();
				if(trim($data['token']))
				{
					$this->szFinAppToken = trim($data['token']);
					$query = "
						UPDATE
							" . __DBC_SCHEMATA_FINICITY_APP_TOKEN__ . "
						SET
							szToken = '" . $this->sql_real_escape_string($this->szFinAppToken) . "',
							dtCreated = NOW()						
					";
					if($result = $this->exeSQL($query))
					{
						return true;
					}
					else
					{
						$this->error = true;
						$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to update because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
						$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
						return false;
					}
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() unable to create access token";
					$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
					return false;
				}
			}
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
	}
	
	function getInstitutionLoginForm($idInstitution)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/institutions/{$idInstitution}/loginForm";
		$this->getPartnerAccessToken();
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
				
			if((int)$curl_info['http_code'] == 200)
			{
				return xml2Array($response);
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r(xml2Array($response),true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function addUserInstitutionAccounts($idUser, $idInstitution, $loginFields)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/customers/{$idUser}/institutions/{$idInstitution}/accounts";
		$this->getPartnerAccessToken();
		$xml = "";
		if(!empty($loginFields))
		{
			$xml = "
			<accounts>
			  <credentials>";
				foreach($loginFields as $loginField)
				{
					$xml .= "
				    <loginField>
				      <id>{$loginField['id']}</id>
				      <name>{$loginField['name']}</name>
				      <value>{$loginField['value']}</value>
				    </loginField>";
				}
				$xml .= "
			  </credentials>
			</accounts>
			";
		}
		
		$Method = "POST";
	    $Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header, $xml, $Method, true))
		{
			$response = $result['response'];
			list($header, $body) = explode("\r\n\r\n", $response, 2);
			$body = xml2Array($body);
			$header = explode("\r\n", $header);
			$curl_info = $result['curl_info'];
			
			if((int)$curl_info['http_code'] == 200)
			{			
				$this->arLoginResponse = $body;
				return true;
			}
			else if((int)$curl_info['http_code'] == 203)
			{			
				if(!empty($header))
				{
					foreach($header as $key=>$headers)
					{
						if(strpos($headers, "MFA-Session:") !== false)
						{
							$this->szLoginMFASession = trim(str_replace("MFA-Session:", "", $headers));
						}
					}
				}
				
				$this->arMFA = $body;
				$this->isLoginMFA  = true;
				
				return false;
			}
			else if(trim($body['message']) != '')
			{
				$this->szLoginError = trim($body['message']);
				return false;
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r($body,true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function addUserInstitutionAccountsMFA($idUser, $idInstitution, $mfa_session, $mfaChallenges)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/customers/{$idUser}/institutions/{$idInstitution}/accounts/mfa";
		$this->getPartnerAccessToken();
		$xml = "";
		if(!empty($mfaChallenges))
		{
			$xml = "
			<accounts>
			  <mfaChallenges>
			    <questions>";
			      foreach($mfaChallenges as $question){$xml .= "
			      <question>
			        <text>{$question['text']}</text>
			        <answer>{$question['answer']}</answer>
			      </question>";
			      }$xml .= "
			    </questions>
			  </mfaChallenges>
			</accounts>
			";
		}
		
		$Method = "POST";
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}", "MFA-Session: {$mfa_session}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header, $xml, $Method, true))
		{
			$response = $result['response'];
			list($header, $body) = explode("\r\n\r\n", $response, 2);
			$body = xml2Array($body);
			$header = explode("\r\n", $header);
			$curl_info = $result['curl_info'];
								
			if((int)$curl_info['http_code'] == 200)
			{			
				$this->arLoginResponse = $body;
				return true;
			}
			else if((int)$curl_info['http_code'] == 203)
			{	
				if(!empty($header))
				{
					foreach($header as $key=>$headers)
					{
						if(strpos($headers, "MFA-Session:") !== false)
						{
							$this->szLoginMFASession = trim(str_replace("MFA-Session:", "", $headers));
						}
					}
				}
				
				$this->arMFA = $body;
				$this->isLoginMFA  = true;
				
				return false;
			}
			else if(trim($body['message']) != '')
			{
				$this->szLoginError = trim($body['message']);
				return false;
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r($body,true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function activateCustomerAccounts($idUser, $idInstitution, $accountsData)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/customers/{$idUser}/institutions/{$idInstitution}/accounts";
		$this->getPartnerAccessToken();
		$xml = "";
		if(!empty($accountsData))
		{
			$xml = "
			<accounts>";
			foreach($accountsData as $account)
			{
				$xml .= "
			    <account>
			      <id>{$account['id']}</id>
    			  <number>{$account['number']}</number>
    			  <name>{$account['name']}</name>
    			  <type>{$account['type']}</type>
    			  <status>{$account['status']}</status>
			    </account>";
			}
			$xml .= "
			</accounts>
			";
		}
		
		$Method = "PUT";
	    $Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
	    if($result = $this->exeAppRequest($App_Url, $Header, $xml, $Method, true))
	    {
			$response = $result['response'];
			list($hd, $header, $body) = explode("\r\n\r\n", $response, 3);
			if(strpos($header, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>') !== false)
			{
				$body = $header;
				$header = $hd;
			}
			$body = xml2Array($body);
			$header = explode("\r\n", $header);
			$curl_info = $result['curl_info'];
						
			if((int)$curl_info['http_code'] == 200)
			{			
				$this->arLoginResponse = $body;
				return true;
			}
			else if((int)$curl_info['http_code'] == 203)
			{			
				if(!empty($header))
				{
					foreach($header as $key=>$headers)
					{
						if(strpos($headers, "MFA-Session:") !== false)
						{
							$this->szLoginMFASession = trim(str_replace("MFA-Session:", "", $headers));
						}
					}
				}
				
				$this->arMFA = $body;
				$this->isLoginMFA  = true;
				
				return false;
			}
			else if(trim($body['message']) != '')
			{
				$this->szLoginError = trim($body['message']);
				return false;
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r($body,true)."\n\n");
				fclose($handle);
				
				return false;
			}
	    }
	    else
	    {
	    	return false;
	    }
	}
	
	function activateCustomerAccountsMFA($idUser, $idInstitution, $mfa_session, $accountsData, $mfaChallenges)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/customers/{$idUser}/institutions/{$idInstitution}/accounts/mfa";
		$this->getPartnerAccessToken();
		$xml = "";
		if(!empty($mfaChallenges))
		{
			$xml = "
			<accounts>";
			  foreach($accountsData as $account)
			  {
				$xml .= "
			    <account>
			      <id>{$account['id']}</id>
    			  <number>{$account['number']}</number>
    			  <name>{$account['name']}</name>
    			  <type>{$account['type']}</type>
    			  <status>{$account['status']}</status>
			    </account>";
			  }
			  $xml .= "
			  <mfaChallenges>
			    <questions>";
			      foreach($mfaChallenges as $question){$xml .= "
			      <question>
			        <text>{$question['text']}</text>
			        <answer>{$question['answer']}</answer>
			      </question>";
			      }$xml .= "
			    </questions>
			  </mfaChallenges>
			</accounts>
			";
		}
		
		$Method = "PUT";
	    $Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}", "MFA-Session: {$mfa_session}");		
		
	    if($result = $this->exeAppRequest($App_Url, $Header, $xml, $Method, true))
	    {
			$response = $result['response'];
			list($hd, $header, $body) = explode("\r\n\r\n", $response, 3);
			if(strpos($header, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>') !== false)
			{
				$body = $header;
				$header = $hd;
			}
			$body = xml2Array($body);
			$header = explode("\r\n", $header);
			$curl_info = $result['curl_info'];
			
			if((int)$curl_info['http_code'] == 200)
			{			
				$this->arLoginResponse = $body;
				return true;
			}
			else if((int)$curl_info['http_code'] == 203)
			{	
				if(!empty($header))
				{
					foreach($header as $key=>$headers)
					{
						if(strpos($headers, "MFA-Session:") !== false)
						{
							$this->szLoginMFASession = trim(str_replace("MFA-Session:", "", $headers));
						}
					}
				}
				
				$this->arMFA = $body;
				$this->isLoginMFA  = true;
				
				return false;
			}
			else if(trim($body['message']) != '')
			{
				$this->szLoginError = trim($body['message']);
				return false;
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r($body,true)."\n\n");
				fclose($handle);
				
				return false;
			}
	    }
	    else
	    {
	    	return false;
	    }
	}
	
	function getCustomerAccountsByInstitution($idCustomer, $idInstitution)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/customers/{$idCustomer}/institutions/{$idInstitution}/accounts";
		$this->getPartnerAccessToken();		
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
			
			if((int)$curl_info['http_code'] == 200)
			{
				return xml2Array($response);
			}
			else if(trim($response['message']) != '')
			{
				$this->szResolveError = trim($body['message']);
				return false;
			}		
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r(xml2Array($response),true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function getCustomerAccountsTransactions($idCustomer, $idAccount=false, $fromDate = false, $toDate = false)
	{
		if(!$fromDate)
		{
			$m = date("m");						
			$y = date("Y");
			$y = ($m == "01" ? ($y - 1) : $y);
			$m = ($m == '01' ? '12' : ($m < 10 ? '0' : '') . ((int)$m - 1));
			$fromDate = strtotime("{$y}-{$m}-01 00:00:00");
		}
		if(!$toDate)
			$toDate = time();
		
		if($idAccount)
			$App_Url = __FINICITY_API_URL__ . "/v2/customers/{$idCustomer}/accounts/{$idAccount}/transactions?fromDate=$fromDate&toDate=$toDate";
		else
			$App_Url = __FINICITY_API_URL__ . "/v2/customers/{$idCustomer}/transactions?fromDate=$fromDate&toDate=$toDate";
		$this->getPartnerAccessToken();		
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
		
			if((int)$curl_info['http_code'] == 200)
			{
				return xml2Array($response);
			}
			else if(trim($response['message']) != '')
			{
				$this->szResolveError = trim($body['message']);
				return false;
			}		
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r(xml2Array($response),true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function getInstitutions()
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/institutions?start=1&limit=30";
		$this->getPartnerAccessToken();
		
		//setting the curl parameters.
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
		
			if((int)$curl_info['http_code'] == 200)
			{
				return xml2Array($response);
			}
			else if(trim($response['message']) != '')
			{
				$this->szResolveError = trim($body['message']);
				return false;
			}		
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r(xml2Array($response),true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function getCustomerAccountDetails($idCustomer, $idAccount)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/customers/{$idCustomer}/accounts/{$idAccount}";
		$this->getPartnerAccessToken();		
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
		
			if((int)$curl_info['http_code'] == 200)
			{
				return xml2Array($response);
			}
			else if(trim($response['message']) != '')
			{
				$this->szResolveError = trim($body['message']);
				return false;
			}		
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r(xml2Array($response),true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function getAccountStatementFile($idCustomer, $idAccount)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/customers/{$idCustomer}/accounts/{$idAccount}/statement";
		$this->getPartnerAccessToken();		
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header, false, false, true))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
			if((int)$curl_info['http_code'] == 203)
			{
				list($hd, $header, $body) = explode("\r\n\r\n", $response, 3);
				if(strpos($header, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>') !== false)
				{
					$body = $header;
					$header = $hd;
				}
				$body = xml2Array($body);
				$header = explode("\r\n", $header);
			}
			else
			{
				$body = $response;
			}			
			
			if((int)$curl_info['http_code'] == 200)
			{			
				$this->arGetStatementResponse = $body;
				return true;
			}
			else if((int)$curl_info['http_code'] == 203)
			{	
				if(!empty($header))
				{
					foreach($header as $key=>$headers)
					{
						if(strpos($headers, "MFA-Session:") !== false)
						{
							$this->szGetStatementMFASession = trim(str_replace("MFA-Session:", "", $headers));
						}
					}
				}
				
				$this->arMFA = $body;
				$this->isGetStatementMFA  = true;
				
				return false;
			}
			else if(trim($body['message']) != '')
			{
				$this->szGetStatementError = trim($body['message']);
				return false;
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r($body,true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function getAccountStatementFileMFA($idCustomer, $idAccount, $mfa_session, $mfaChallenges)
	{
		$App_Url = __FINICITY_API_URL__ . "/v1/customers/{$idCustomer}/accounts/{$idAccount}/statement/mfa";
		$this->getPartnerAccessToken();
		$xml = "";
		if(!empty($mfaChallenges))
		{
			$xml = "
			  <mfaChallenges>
			    <questions>";
			      foreach($mfaChallenges as $question){$xml .= "
			      <question>
			        <text>{$question['text']}</text>
			        <answer>{$question['answer']}</answer>
			      </question>";
			      }$xml .= "
			    </questions>
			  </mfaChallenges>
			";
		}
		
		$Method = "POST";
		$Header = array("Content-Type: application/xml", "Finicity-App-Key: " . __FINICITY_API_KEY__, "Finicity-App-Token: {$this->szFinAppToken}", "MFA-Session: {$mfa_session}");		
		
		if($result = $this->exeAppRequest($App_Url, $Header, $xml, $Method, true))
		{
			$response = $result['response'];
			$curl_info = $result['curl_info'];
		
			if((int)$curl_info['http_code'] == 203)
			{
				list($hd, $header, $body) = explode("\r\n\r\n", $response, 3);
				if(strpos($header, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>') !== false)
				{
					$body = $header;
					$header = $hd;
				}
				$body = xml2Array($body);
				$header = explode("\r\n", $header);
			}
			else
			{
				$body = $response;
			}	
			
			if((int)$curl_info['http_code'] == 200)
			{			
				$this->arGetStatementResponse = $body;
				return true;
			}
			else if((int)$curl_info['http_code'] == 203)
			{	
				if(!empty($header))
				{
					foreach($header as $key=>$headers)
					{
						if(strpos($headers, "MFA-Session:") !== false)
						{
							$this->szGetStatementMFASession = trim(str_replace("MFA-Session:", "", $headers));
						}
					}
				}
				
				$this->arMFA = $body;
				$this->isGetStatementMFA  = true;
				
				return false;
			}
			else if(trim($body['message']) != '')
			{
				$this->szGetStatementError = trim($body['message']);
				return false;
			}
			else
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity was unsuccessful with following details-\nAPI URL: $App_Url\nHTTP Code: {$curl_info['http_code']}\nResponse:".print_r($body,true)."\n\n");
				fclose($handle);
				
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	function exeAppRequest($App_Url, $Header=false, $Post_fields=false, $Method=false, $Retun_header=false)
	{		
		$App_Url = trim($App_Url);
		if($App_Url != '')
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $App_Url);
		    curl_setopt($ch, CURLOPT_VERBOSE, 1);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    
		    if($Method == "POST")
		    	curl_setopt($ch, CURLOPT_POST, 1);
		    else if($Method == "PUT")
		    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		    
		    if($Retun_header)
		    	curl_setopt($ch, CURLOPT_HEADER, 1);
		    if(!empty($Header))
				curl_setopt($ch, CURLOPT_HTTPHEADER, $Header);
	
			if(!empty($Post_fields))
				curl_setopt($ch, CURLOPT_POSTFIELDS, $Post_fields);
			
			//getting response from server
			$response = curl_exec($ch);
			$curl_info = curl_getinfo($ch);
			$errno = curl_errno($ch);
			
			if ($errno)
			{
				$handle = fopen(__APP_PATH_LOGS__."/finicity.log", "a+");
				fwrite($handle, "Following call to Finicity failed-\nAPI URL: $App_Url\nError: " . curl_strerror($errno) . "\n\n");
				fclose($handle);
				
				return false;
			}
			else
			{
				$ret['response'] = $response;
				$ret['curl_info'] = $curl_info;
			  	curl_close($ch);
			  	
			  	return $ret;
			}
		}
		else
		{
			return false;
		}
	}
}
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends Database_Model
{
	var $id;
	var $szFirstName;
	var $szLastName;
	var $szEmail;
	var $szPassword;
	var $szMobilePhone;
	var $iFinicityAddCustomerFailed = false;
	
	public function __construct()
   	{
      parent::__construct();
   	}
	
	function set_id($value)
	{
		$this->id = $this->validateInput( $value, __VLD_CASE_WHOLE_NUM__, "p_id", "User ID", false, false, true );
	}
	
	function set_idInstitution($value)
	{
		$this->idInstitution = $this->validateInput( $value, __VLD_CASE_WHOLE_NUM__, "p_institution_id", "Institution ID", false, false, true );
	}
	
	function set_szFirstName($value)
	{
		 $this->szFirstName = $this->validateInput($value, __VLD_CASE_ANYTHING__, "p_fname", "First name", false, false, true);
	}
	
	function set_szLastName($value)
	{
		 $this->szLastName = $this->validateInput($value, __VLD_CASE_ANYTHING__, "p_lname", "Last name", false, false, true);
	}
	
	function set_szEmail($value,$flag=true)
    {
        $this->szEmail = $this->validateInput($value, __VLD_CASE_EMAIL__, "p_email", "Email address", false, false, $flag);
    }
	
	function set_szPassword($value, $flag=true)
	{
		 $this->szPassword = $this->validateInput($value, __VLD_CASE_PASSWORD__, "p_password", "Password", 6, 32, $flag);
	}
	
	function set_szProvinceRegion($value, $flag=true)
	{
		 $this->szProvinceRegion = $this->validateInput($value, __VLD_CASE_ANYTHING__, "province", "Province/region of home bank branch", false, false, true);
	}
	
	function set_fAbsoluteMinBalance($value)
	{
		 $this->fAbsoluteMinBalance = $this->validateInput($value, __VLD_CASE_NUMERIC__, "p_absmin", "Absolute minimum of chequing account balance", false, false, false);
	}
	
	function set_fSurplusDeficitRate($value)
	{
		 $this->fSurplusDeficitRate = $this->validateInput($value, __VLD_CASE_NUMERIC__, "p_surplusdeficit", "Surplus deficit rate", false, false, false);
	}
	
	function set_fIfDeficitRate($value)
	{
		 $this->fIfDeficitRate = $this->validateInput($value, __VLD_CASE_NUMERIC__, "p_ifdeficit", "If deficit rate", false, false, false);
	}
	
	function set_iAutoSavings($value)
	{
		$this->iAutoSavings = $this->validateInput($value, __VLD_CASE_WHOLE_NUM__, "p_suto_saving", "Auto savings status", false, false, true );
	}
	
	function set_fMinThreshold($value)
	{
		 $this->fMinThreshold = $this->validateInput($value, __VLD_CASE_NUMERIC__, "p_threshold", "Minimum threshold", false, false, false);
	}
	
	function set_szMobilePhone($value, $flag=true)
    {
    	if($value != '')
    	{
    		// strip all character except +, 0-9
    		$value = preg_replace('/[^\d+]/i', '', $value);
    		if(strpos($value, "+") === false)
    		{
    			if(strlen($value) == 10)
    				$value = "+1" . $value;
    			else
    				$value = "+" . $value;
    		}
    	}
    	
        $this->szMobilePhone = $this->validateInput($value, __VLD_CASE_MOBILE_PHONE__, "p_mobilephone", "Mobile phone", false, false, $flag);
    }
    
    function set_szInstitution($value)
    {
    	$this->szInstitution = $this->validateInput($value, __VLD_CASE_ANYTHING__, "p_institution", "Financial institution", false, false, true);
    }
    
	function set_szTransitNumber($value)
    {
    	$this->szTransitNumber = $this->validateInput($value, __VLD_CASE_DIGITS__, "p_transit_number", "Transit number", 5, 5, true);
    	/*if($this->szTransitNumber = $this->validateInput($value, __VLD_CASE_DIGITS__, "p_transit_number", "Transit number", 9, 9, true))
    	{
    		if(substr($this->szTransitNumber, 0 , 1) != "0")
    		{
    			$this->addError("p_transit_number", "Transit number must start with a 0");
    		}
    	}*/
    }
    
    function set_szInstitutionNumber($value)
    {
    	$this->szInstitutionNumber = $this->validateInput($value, __VLD_CASE_DIGITS__, "p_institution_number", "Institution number", 3, 3, true);
    }
    
	function set_szAccountNumber($value)
    {
    	$this->szAccountNumber = $this->validateInput($value, __VLD_CASE_DIGITS__, "p_account_number", "Account number", false, false, true);
    }
	
	public function checkCustomerExists($szEmail=false,$idUser=false,$byMobilePhone=false)
	{	
		$szEmail = trim($szEmail);
		
		if($szEmail == "")
		{
			checkCustomerLogin($this);			
			$user_session = $this->session->userdata('user_arr');
			$szEmail = $user_session['login'];
		}
		
		if($szEmail != "")
		{
			$whereQuery = "";
		 	if(!empty($idUser))
		 	{
		 		$whereQuery = "
		 			AND
		 				id != '".$this->sql_real_escape_string($idUser)."'
		 		";
		 	}
		 	$query="
	 			SELECT
	 				id,
	 				szEmail,
	 				szMobilePhone
	 			FROM
	 				".__DBC_SCHEMATA_USERS__."
	 			WHERE
	 				" . ($byMobilePhone ? "szMobilePhone=".$this->sql_real_escape_string($szEmail)."" :  "szEmail='".$this->sql_real_escape_string($szEmail)."'") . "
	 				".$whereQuery."
	 		";
	 	 	if($result=$this->exeSQL($query))
            {
	            if ($this->iNumRows > 0)
	            {
	            	$row = $this->getAssoc($result);
	            	$this->id = (int)$row['id'];
	            	$this->szEmail = trim($row['szEmail']);
           			$this->szMobilePhone = trim($row['szMobilePhone']);
	               	return true;
	            }
	            else
	            {
	            	return false;
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
		return false;
	}
	
	function validateCustomerData($data, $arExclude=array())
	{
		if(!empty($data))
		{
			if(!in_array('p_id', $arExclude)) $this->set_id(sanitize_all_html_input(trim($data['p_id'])));
			if(!in_array('p_fname', $arExclude)) $this->set_szFirstName(sanitize_all_html_input(trim($data['p_fname'])));
			if(!in_array('p_lname', $arExclude)) $this->set_szLastName(sanitize_all_html_input(trim($data['p_lname'])));
			if(!in_array('p_email', $arExclude)) $this->set_szEmail(sanitize_all_html_input(trim($data['p_email'])));
			if(!in_array('p_password', $arExclude)) $this->set_szPassword(sanitize_all_html_input(trim($data['p_password'])));
			if(!in_array('province', $arExclude)) $this->set_szProvinceRegion(trim($data['province']));
			if(!in_array('p_mobilephone', $arExclude)) $this->set_szMobilePhone(sanitize_all_html_input(trim($data['p_mobilephone'])));
			
			if($this->szEmail != '' && !isset($this->arErrorMessages['p_email']) && sanitize_all_html_input(trim($data['p_re_email'])) != '' && $this->szEmail != sanitize_all_html_input(trim($data['p_re_email'])))
			{
				$this->addError("p_re_email", "Re-enter email does not match.");
			}
			else if(isset($data['p_re_email']) && sanitize_all_html_input(trim($data['p_re_email'])) == '')
			{
				$this->addError("p_re_email", "Re-enter email required.");
			}
			
			if($this->szPassword != '' && !isset($this->arErrorMessages['p_password']) && sanitize_all_html_input(trim($data['p_re_password'])) != '' && $this->szPassword != sanitize_all_html_input(trim($data['p_re_password'])))
			{
				$this->addError("p_re_password", "Re-enter password does not match.");
			}
			else if(isset($data['p_re_password']) && sanitize_all_html_input(trim($data['p_re_password'])) == '')
			{
				$this->addError("p_re_password", "Re-enter password required.");
			}
			
			if($this->szProvinceRegion != '' && !isset($this->arErrorMessages['province']) && $this->szProvinceRegion != $data['province'] && $data['province'] != '')
			{	
				$this->addError("province", "Province/region of home bank branch is required.");
			}
			
			if(!$this->error)
				return true;
			else
				return false;
		}
		return false;
	}
	
	function loadCustomer($id, $by_key=false)
	{		
		if(!$by_key)
		{
			$this->set_id((int)$id);
			if($this->error)
				return false;
		}
		else if(trim($id) == '')
		{
			return false;
		}
			
		$query = "
			SELECT
				U.id,
				U.idFinicity,
				U.idFinicityAccount,
				U.idFinicityInstitution,
				FI.szName AS szFinicityInstitution,
				FI.szInstitutionNumber AS szFinicityInstitutionNumber,
				U.szFinicityAccountNumber,
				U.szFinicityAccountTransitNumber,
				U.szFinicityStatementVerificationFile,
				U.szFinicityAccountVerificationFile,
				U.iLoginStep,
				U.szFirstName,
				U.szLastName,
				U.szEmail,
				U.szProvinceRegion,
				U.szMobilePhone,
				U.szPassword,
				U.iSignupStep,
				U.idInstitution,
				SI.szName AS szInstitution,
				SI.szInstitutionNumber AS szInstitutionNumber,
				U.szTransitNumber,
				U.szAccountNumber,
				U.szVerificationFile,
				U.iSavingAccountVerified,
				U.fAbsoluteMinBalance,
				U.fSurplusDeficitRate,
				U.fIfDeficitRate,
				U.fMinThreshold,
				U.iAutoSavings,
				U.dtAutoSavingsChanged,
				U.szUniqueKey,
				(CASE WHEN AC.id IS NOT NULL THEN 1 ELSE 0 END) AS iSavingAcountChanged
			FROM
				" . __DBC_SCHEMATA_USERS__ . " U
			LEFT JOIN
				" . __DBC_SCHEMATA_INSTITUTIONS__ . " FI
			ON
				FI.id = U.idFinicityInstitution
			LEFT JOIN
				" . __DBC_SCHEMATA_INSTITUTIONS__ . " SI
			ON
				SI.id = U.idInstitution
			LEFT JOIN
				" . __DBC_SCHEMATA_SAVING_ACCOUNT_CHANGED__ . " AC
			ON
				AC.idUser = U.id
			WHERE
				" . ($by_key ? "U.szUniqueKey = '" . $this->sql_real_escape_string(trim($id)) . "'" : "U.id = " . (int)$this->id) . "
		";
		if($result=$this->exeSQL($query))
      	{
	       	if ($this->iNumRows > 0)
	        {
	         	$row = $this->getAssoc($result);

	           	$this->id = (int)$row['id'];
	           	$this->idFinicity = (int)$row['idFinicity'];
	           	$this->idFinicityInstitution = (int)$row['idFinicityInstitution'];
	           	$this->szFinicityInstitution = trim($row['szFinicityInstitution']);
	           	$this->szFinicityInstitutionNumber = trim($row['szFinicityInstitutionNumber']);
	           	
	           	$this->idFinicityAccount = (int)$row['idFinicityAccount'];
	           	$this->szFinicityAccountNumber = trim($row['szFinicityAccountNumber']);
	           	$this->szFinicityAccountTransitNumber = trim($row['szFinicityAccountTransitNumber']);
	           	$this->szFinicityAccountVerificationFile = trim($row['szFinicityAccountVerificationFile']);
	           	$this->szFinicityStatementVerificationFile = trim($row['szFinicityStatementVerificationFile']);
	           	$this->iLoginStep = (int)$row['iLoginStep'];
	           	
	           	$this->szFirstName = trim($row['szFirstName']);
	           	$this->szLastName = trim($row['szLastName']);
	           	$this->szEmail = trim($row['szEmail']);
	           	$this->szMobilePhone = trim($row['szMobilePhone']);
	           	$this->szPassword = trim($row['szPassword']);
	           	$this->iSignupStep = (int)$row['iSignupStep'];
	           	
	           	$this->idInstitution = (int)$row['idInstitution'];
	           	$this->szInstitution = trim($row['szInstitution']);
	           	$this->szInstitutionNumber = trim($row['szInstitutionNumber']);
	           	
	           	$this->szTransitNumber = trim($row['szTransitNumber']);
	           	$this->szAccountNumber = trim($row['szAccountNumber']);
	           	$this->szVerificationFile = trim($row['szVerificationFile']);
	           	$this->iSavingAccountVerified = (int)$row['iSavingAccountVerified'];
	           	$this->fAbsoluteMinBalance = format_number($row['fAbsoluteMinBalance']);
	           	$this->fSurplusDeficitRate = format_number($row['fSurplusDeficitRate']);
	           	$this->fIfDeficitRate = format_number($row['fIfDeficitRate']);
	           	$this->fMinThreshold = format_number($row['fMinThreshold']);
	           	$this->iAutoSavings = (int)$row['iAutoSavings'];
	           	$this->dtAutoSavingsChanged = trim($row['dtAutoSavingsChanged']);
	           	$this->szUniqueKey = trim($row['szUniqueKey']);
	           	$this->iSavingAcountChanged = (int)$row['iSavingAcountChanged'];
	           	$this->szProvinceRegion = $row['szProvinceRegion'];
	           	
	           	return true;
	        }
	        else
	        {
	           	return false;
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
	
	function addCustomer()
	{
		if($this->error)
			return false;
			
		// First add customer record to finicity
		$this->load->model('Finicity_Model');
		$request = array("szEmail" => $this->szEmail, "szFirstName" => $this->szFirstName, "szLastName" => $this->szLastName);
		$response = $this->Finicity_Model->addCustomertToFinicity($request);
		echo "hidden<div style='display:none;'>";
		var_dump($response);
		echo "</div>";
		
		$id = isset($response['id']) ? $response['id'] : 0;
		
		if((int)$id > 0)
		{			
			$query = "
				INSERT INTO
					" . __DBC_SCHEMATA_USERS__ . "
				(
					idFinicity,
					szFirstName,
					szLastName,
					szEmail,
					szPassword,
					szUniqueKey,
					szProvinceRegion
				)
				VALUES
				(
					" . (int)$id . ",
					'" . $this->sql_real_escape_string($this->szFirstName) . "',
					'" . $this->sql_real_escape_string($this->szLastName) . "',
					'" . $this->sql_real_escape_string($this->szEmail) . "',
					'" . $this->sql_real_escape_string(encrypt(trim($this->szPassword))) . "',
					'" . $this->sql_real_escape_string($this->getUniqueKeyForCustomer()) . "',
					'" . $this->sql_real_escape_string($this->szProvinceRegion) . "'
				)
			";
			if($result=$this->exeSQL($query))
	     	{
		   		if ($this->getRowCnt() > 0)
		    	{
		        	$this->id = $this->iLastInsertID;
		          	return true;
		     	}
		      	else
		     	{
		       		return false;
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
		else
		{
			$this->iFinicityAddCustomerFailed = true;
			return false;
		}
	}
	
	function getUniqueKeyForCustomer()
	{
		$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < 30; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    if($this->isUniqueKeyExists($randomString))
	    {
	    	$randomString = getUniqueKeyForCustomer();
	    }
	    return $randomString;
	}
	
	function isUniqueKeyExists($szKey)
	{
		if(trim($szKey) != '')
		{
			$query = "
				SELECT
					id
				FROM
					" . __DBC_SCHEMATA_USERS__ . "
				WHERE
					szUniqueKey = '" . $this->sql_real_escape_string(trim($szKey)) . "'
			";
			if($result=$this->exeSQL($query))
	     	{
		   		if ($this->iNumRows > 0)
		    	{
		          	return true;
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
		return false;
	}
	
	function addUserMobileVerificationMapping($data)
	{
		if(!empty($data))
		{
			// first clear all old entries
			$dl_query = "DELETE FROM " . __DBC_SCHEMATA_USER_MOBILE_VERIFY_MAP__ . " WHERE idUser = " . (int)$data['idUser'];
			$this->exeSQL($dl_query);
			
			$query = "
				INSERT INTO
					" . __DBC_SCHEMATA_USER_MOBILE_VERIFY_MAP__ . "
				(
					idUser,
					szMobilePhone,
					szMessage,
					szMessageKey
				)
				VALUES
				(
					" . (int)$data['idUser']. ",
					'" . $this->sql_real_escape_string(trim($data['szMobilePhone'])) . "',
					'" . $this->sql_real_escape_string(trim($data['szMessage'])) . "',
					'" . $this->sql_real_escape_string(trim($data['szMessageKey'])) . "'
				)
			";
			if($result=$this->exeSQL($query))
	     	{
		   		if ($this->getRowCnt() > 0)
		    	{
		          	return true;
		     	}
		      	else
		     	{
		       		return false;
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
	}
	
	function deleteUserMobileVerificationMapping($idUser)
	{
		// first clear all old entries
		$dl_query = "DELETE FROM " . __DBC_SCHEMATA_USER_MOBILE_VERIFY_MAP__ . " WHERE idUser = " . (int)$idUser;
		$this->exeSQL($dl_query);
		
		// update user record
		$up_query = "UPDATE " . __DBC_SCHEMATA_USERS__ . " SET szMobilePhone='', iSignupStep = 1 WHERE id = " . (int)$idUser;
		$this->exeSQL($up_query);
		
		return true;
	}
	
	function getMobileVerificationMapping($idUser)
	{
		$arMap = array();
		if((int)$idUser > 0)
		{
			$query = "
				SELECT
					*
				FROM
					" . __DBC_SCHEMATA_USER_MOBILE_VERIFY_MAP__ . "
				WHERE
					idUser = " . (int)$idUser . "
				AND
					dtSentOn > '" . $this->sql_real_escape_string(date("Y-m-d H:i:s", strtotime("-60 minutes"))) . "'
			";
			if($result=$this->exeSQL($query))
	     	{
		   		if ($this->iNumRows > 0)
		    	{
		          	$arMap = $this->getAssoc($result);
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
		return $arMap;
	}
	
	function updateCustomerPhone()
	{
		if($this->error)
			return false;
			
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_USERS__ . "
			SET
				szMobilePhone = '" . $this->sql_real_escape_string($this->szMobilePhone) . "',
				iSignupStep = 2
			WHERE
				id = " . (int)$this->id . "
		";
		if($result=$this->exeSQL($query))
     	{
	   		if ($this->getRowCnt() > 0)
	    	{
	    		// clear mapping
				$dl_query = "DELETE FROM " . __DBC_SCHEMATA_USER_MOBILE_VERIFY_MAP__ . " WHERE idUser = " . (int)$this->id;
				$this->exeSQL($dl_query);
			
	          	return true;
	     	}
	      	else
	     	{
	       		return false;
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
	
	function updateCustomerInstitution($institution_id, $account_id=0, $account_number='', $statement_file='')
	{
		if((int)$institution_id > 0)
		{			
			$query = "
				UPDATE
					" . __DBC_SCHEMATA_USERS__ . "
				SET
					idFinicityInstitution = " . (int)$institution_id . "
					" . ((int)$account_id > 0 ? ", idFinicityAccount = " . (int)$account_id : "") . "
					" . (trim($account_number) != '' ? ", szFinicityAccountNumber = '" . $this->sql_real_escape_string(trim($account_number)) . "'" : "") . "
					" . (trim($statement_file) != '' ? ", szFinicityStatementVerificationFile = '" . $this->sql_real_escape_string(trim($statement_file)) . "'" : "") . "
					" . ((int)$institution_id > 0 && (int)$account_id > 0 && trim($account_number) != '' && trim($statement_file) != '' ? ", iSignupStep = 3" : "") . "
				WHERE
					id = " . (int)$this->id . "
			";
			if($result=$this->exeSQL($query))
	     	{
		   		if ($this->getRowCnt() > 0)
		    	{
		    		return true;
		     	}
		      	else
		     	{
		       		return false;
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
	}
	
	function checkMobilePhoneInMapping($szMobilePhone)
	{
		$szMobilePhone = trim($szMobilePhone);
		if($szMobilePhone != '')
		{
			$query = "
				SELECT
					id
				FROM
					" . __DBC_SCHEMATA_USER_MOBILE_VERIFY_MAP__ . "
				WHERE
					szMobilePhone = '" . $this->sql_real_escape_string($szMobilePhone) . "'
				AND
					dtSentOn > '" . $this->sql_real_escape_string(date("Y-m-d H:i:s", strtotime("-60 minutes"))) . "'
			";
			if($result=$this->exeSQL($query))
	     	{
		   		if ($this->iNumRows > 0)
		    	{
		          	return true;
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
		return false;
	}
	
	function getInstitution($szCodition='', $szOrderBy='')
	{
		$arInstitutions = array();
		$szCodition = trim($szCodition);
		if($szCodition != '')
			$szCodition = "WHERE $szCodition";
			
		$query = "
			SELECT
				*
			FROM
				" . __DBC_SCHEMATA_INSTITUTIONS__ . "
			$szCodition
			$szOrderBy
		";
		if($result=$this->exeSQL($query))
     	{
	   		if ($this->iNumRows > 0)
	    	{
	    		$arInstitutions = $this->getAssoc($result, true);
	     	}
      	}
     	else
     	{ 
        	$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
      	}
      	return $arInstitutions;
	}
	
	function send_forget_password_link($szEmail)
	{
		if(trim($szEmail) != '')
		{					
			if($this->checkCustomerExists($szEmail))
			{
				$this->loadCustomer($this->id);
				
				$link_key = $this->create_link_key();
							
				if($this->is_link_already_sent())
				{
					$query = "
						UPDATE
							" . __DBC_SCHEMATA_FORGOT_PASSWORD_LINK__ . " 
						SET
							szLinkKey = '" . $this->sql_real_escape_string($link_key) . "',
							dtSentOn = '" . $this->sql_real_escape_string(date('Y-m-d H:i:s')) . "'
						WHERE
							szEmail = '" . $this->sql_real_escape_string($szEmail) . "'
						";
				}
				else
				{
					$query = "
						INSERT INTO
							" . __DBC_SCHEMATA_FORGOT_PASSWORD_LINK__ . "
						(
							idUser,
							szEmail,
							szLinkKey
						)
						VALUES
						(
							" . (int)$this->id . ",
							'" . $this->sql_real_escape_string($szEmail) . "',
							'" . $this->sql_real_escape_string($link_key) . "'
						)
					";
				}
				if($this->exeSQL($query))
				{
					if($this->getRowCnt() > 0)
					{
						$message = "
						Hello {$this->szFirstName},<br><br>
						<a href=\"" . __SECURE_BASE_URL__ . "/users/forgot-password/$link_key\"><b>Click Here to reset your password.</b></a><br><br>
						If you need assistance, please email us <a href=\"mailto::info@tallyfinancial.com\">info@tallyfinancial.com</a>.";
						$subject = $this->szFirstName." ".$this->szLastName." Tally Reset Your Password";
						$to = "{$this->szFirstName} {$this->szLastName} <{$this->szEmail}>";
						$from = __CUSTOMER_SUPPORT_EMAIL__;
						
						sendEmail($to, $from, $subject, $message);
						return true;
					}
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to insert because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
					$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
					return false;
				}
			}
			else
			{
				$this->addError("uEmail", "This email is not registered. Want to <a href='" . __BASE_ACCOUNT_SECURE_URL__ . "/ur_registerform'>create a new account</a>?");
				return false;
			}
		}
	}
	
	function create_link_key()
	{
		$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$i = 0;
		$link_key= "";
		while ($i <= 12)
		{
			$link_key.= $chars{mt_rand(0,strlen($chars)-1)};
			$i++;
		}
		
		if($this->is_link_key_exists($link_key))
		{
			$link_key = $this->create_link_key();
		}
		return $link_key;
	}
	
	function is_link_key_exists($key)
	{
		if($key != '')
		{
			$check_time = date('Y-m-d H:i:s',strtotime('-24 hour'));
			$query = "
				SELECT
					id,
					idUser
				FROM
					" . __DBC_SCHEMATA_FORGOT_PASSWORD_LINK__ . "
				WHERE
					szLinkKey = '" . $this->sql_real_escape_string($key) . "'
			";
			if($result = $this->exeSQL($query))
			{
				if($this->iNumRows > 0)
				{
					$row = $this->getAssoc($result);
					$this->id= (int)$row['idUser'];
					return true;
				}
				else
				{
					return false;
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
	}
	
	function is_link_already_sent()
	{
		if($this->szEmail != '')
		{
			$query = "
				SELECT
					*
				FROM
					" . __DBC_SCHEMATA_FORGOT_PASSWORD_LINK__ . "
				WHERE
					szEmail = '" . $this->sql_real_escape_string($this->szEmail) . "'
			";
			if($result = $this->exeSQL($query))
			{
				if($this->iNumRows > 0)
				{
					$row = $this->getAssoc($result);
					return true;
				}
				else
				{
					return false;
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
	}
	
	function remove_link_key_by_email($szEmail)
	{
		if(trim($szEmail) != "")
		{
			$query = "
				DELETE FROM
					" . __DBC_SCHEMATA_FORGOT_PASSWORD_LINK__ . "
				WHERE
					szEmail = '" . $this->sql_real_escape_string(trim($szEmail)) . "'
			";
			if($result = $this->exeSQL($query))
			{
				return true;
			}
			else
			{
				$this->error = true;
				$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
				$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
				return false;
			}
		}
	}
	
	public function updatePassword($szNewPassword, $idUser)
	{
	 	if(!empty($szNewPassword))
	 	{
	 		$query="
	 			UPDATE
					" . __DBC_SCHEMATA_USERS__ . "
				SET	
					szPassword = '" . (!empty($szNewPassword) ? $this->sql_real_escape_string(encrypt(trim($szNewPassword))) : '') . "'
				WHERE
					id = " . (int)$idUser . "
			";
	 		if(($result = $this->exeSQL( $query )))
			{
				return true;
			}
			else
			{
				$this->error = true;
				$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
				$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
				return false;
			}
	 	}
	}
	
	public function validateBankingInformation($data, $arExclude=array())
	{
		if(!in_array('p_id', $arExclude)) $this->set_id(sanitize_all_html_input(trim($data['p_id'])));
		if(!in_array('p_institution_id', $arExclude)) $this->set_idInstitution(sanitize_all_html_input(trim($data['p_institution_id'])));
		if(!in_array('p_institution', $arExclude)) $this->set_szInstitution(sanitize_all_html_input(trim($data['p_institution'])));
		if(!in_array('p_transit_number', $arExclude)) $this->set_szTransitNumber(sanitize_all_html_input(trim($data['p_transit_number'])));
		if(!in_array('p_account_number', $arExclude)) $this->set_szAccountNumber(sanitize_all_html_input(trim($data['p_account_number'])));
		
		if(!$this->error)
			return true;
		else
			return false;
	}
	
	function updateCustomerBankingInformation($chequing=false,$only_transit=false,$defaultVerify=false)
	{
		if($this->error)
			return false;
			
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_USERS__ . "
			SET
				" . ($chequing ? "szFinicityAccountTransitNumber" : "szTransitNumber") . " = '" . $this->sql_real_escape_string(trim($this->szTransitNumber)) . "'
				" . (!$chequing && !$only_transit ? ",idInstitution = '" . $this->sql_real_escape_string(trim($this->idInstitution)) . "'" : "") . "
				" . (!$chequing && !$only_transit ? ",szAccountNumber = '" . $this->sql_real_escape_string(trim($this->szAccountNumber)). "'" : "") . "
				" . (!$chequing && !$only_transit && $defaultVerify ? ",szVerificationFile = 'SAME-AS-CHECKING'" : "") . "
				" . ($chequing && $only_transit ? ",iSignupStep = 4" : "") . "				
			WHERE
				id = " . (int)$this->id . "
		";
		if(($result = $this->exeSQL( $query )))
		{
			if($chequing && $only_transit)
			{
				$this->loadCustomer($this->id);
				$arInstitution = $this->getInstitution("id = {$this->idFinicityInstitution}", "ORDER BY iOrder");
				
				$message = "
				Followings are new user's signup information-<br><br>
				
				<h3>Personal Information</h3>
				<b>Name:</b> {$this->szFirstName} {$this->szLastName}<br>
				<b>Email address:</b> {$this->szEmail}<br>
				<b>Mobile number:</b> {$this->szMobilePhone}<br>
				
				<h3>Chequing Account Information</h3>
				<b>Financial institutin:</b> {$arInstitution[0]['szName']}<br>
				<b>Transit number:</b> {$this->szFinicityAccountTransitNumber}<br>
				<b>Account number:</b> {$this->szFinicityAccountNumber}<br>
				<b>Verification document:</b><br>
				<img border'0' width='400' src='".__BASE_IMAGES_URL__."/users/{$this->szFinicityAccountVerificationFile}' alt='{$this->szFinicityAccountVerificationFile}'><br><br>
				
				You can verify these information <a href=\"".__BASE_ADMIN_URL__."/users/details/{$this->id}\">here</a>.
				";
				$subject = $this->szFirstName." ".$this->szLastName." Tally Signup Completed";
				$from = __CUSTOMER_SUPPORT_EMAIL__;
				$to = __ADMIN_USER_EMAIL__;
				
				
				sendEmail($to, $from, $subject, $message);
			}
			return true;
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
	}
	
	function backToOldSavingAccount($idUser)
	{
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_USERS__ ." U
			INNER JOIN
				" . __DBC_SCHEMATA_SAVING_ACCOUNT_CHANGED__ . " AC
			ON
				AC.idUser = U.id
			SET
				U.idInstitution = AC.idInstitution,
				U.szTransitNumber = AC.szTransitNumber,
				U.szAccountNumber = AC.szAccountNumber,
				U.szVerificationFile = AC.szVerificationFile,
				U.iSavingAccountVerified = AC.iSavingAccountVerified
			WHERE
				AC.idUser = " . (int)$idUser . "
		";
		if($this->exeSQL($query))
		{
			if($this->getRowCnt() > 0)
			{
				$query = "
					DELETE FROM
						" . __DBC_SCHEMATA_SAVING_ACCOUNT_CHANGED__ . "
					WHERE
						idUser = " . (int)$idUser . "
				";
				$this->exeSQL($query);
				$this->loadCustomer($idUser);	
				return true;
			}
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;			
		}
	}
	
	function removeCustomerBankingInformation()
	{
		// first save current values 
		$query = "
			INSERT INTO
				" . __DBC_SCHEMATA_SAVING_ACCOUNT_CHANGED__ . "
			(
				idUser,
				idInstitution,
				szTransitNumber,
				szAccountNumber,
				szVerificationFile,
				iSavingAccountVerified
			)
			SELECT
				id,
				idInstitution,
				szTransitNumber,
				szAccountNumber,
				szVerificationFile,
				iSavingAccountVerified
			FROM
				" . __DBC_SCHEMATA_USERS__ . "
			WHERE
				id = " . (int)$this->id . "
		";
		if($this->exeSQL($query))
		{
			if($this->getRowCnt() > 0)
			{
				$query = "
					UPDATE
						" . __DBC_SCHEMATA_USERS__ . "
					SET
						idInstitution = '0',
						szTransitNumber = '',
						szAccountNumber = '',
						szVerificationFile = '',
						iSavingAccountVerified = 0
					WHERE
						id = " . (int)$this->id . "
				";
				if(($result = $this->exeSQL( $query )))
				{
					$this->loadCustomer($this->id);					
					return true;
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
					$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
					return false;
				}
			}
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
	}
	
	function removeAccountChanged($idUser)
	{
		$query = "
			SELECT
				szVerificationFile
			FROM
				" . __DBC_SCHEMATA_SAVING_ACCOUNT_CHANGED__ . "
			WHERE
				idUser = " . (int)$idUser . "
		";
		if($result = $this->exeSQL($query))
		{
			if($this->iNumRows > 0)
			{		
				$row = $this->getAssoc($result);	 
				$query = "
					DELETE FROM
						" . __DBC_SCHEMATA_SAVING_ACCOUNT_CHANGED__ . "
					WHERE
						idUser = " . (int)$idUser . "
				";
				if($this->exeSQL($query))
				{					
					if(trim($row['szVerificationFile']) != "SAME-AS-CHECKING")
					{
						if(trim($row['szVerificationFile']) != '' && file_exists(__APP_PATH_ASSETS__. "/images/users_account/" . trim($row['szVerificationFile'])))
						{
							unlink(__APP_PATH_ASSETS__. "/images/users_account/" . trim($row['szVerificationFile']));
						}
					}
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to remove because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
					$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
					return false;
				}
			}
			else
			{
				return false;
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
	
	function updateCustomerBankingInformationVerificationFile($aImage, $chequing=false)
	{
		if($this->error)
			return false;
			
		if(is_uploaded_file($aImage['tmp_name']))
     	{
     		$aName = explode(".", $aImage['name']);
     		$type = $aName[(count($aName) - 1)];
     		$imageName = "{$this->id}-customer-bank-" . ($chequing ? "chequing-" : "") . "verification-file.{$type}";
     		
          	if(move_uploaded_file($aImage['tmp_name'], __APP_PATH_ASSETS__.'/images/users_account/'.$imageName))
         	{
         		$query = "
         			UPDATE
         				" . __DBC_SCHEMATA_USERS__ . "
         			SET
         				" . ($chequing ? "szFinicityAccountVerificationFile" : "szVerificationFile") . " = '" . $this->sql_real_escape_string($imageName) . "'
         				" . ($chequing ? ",iSignupStep = 4" : "") . "
         			WHERE
         				id = " . (int)$this->id . "
         		";
         		if(($result = $this->exeSQL( $query )))
				{							
					return true;
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
					$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
					return false;
				}
        	}
          	else
         	{
         		$this->addError("p_verification_file", "Problem in uploading verification file. Please try again");
            	return false;
          	}
     	}
     	else
     	{
     		$this->addError("p_verification_file", "Problem in uploading verification file. Please try again");
            return false;
     	}
	}
	
	function getCustomers($count_only=false, $start_from=0, $limit=0, $where='', $sort_by='', $sort_order='')
	{		
		$arCoustomers = array();

		if($count_only)
		{
			$query = "
				SELECT
					id
				FROM
					" . __DBC_SCHEMATA_USERS__ . "
				" . ($where != "" ? "WHERE $where" : "") . "
			";			
		}
		else
		{
			$query = "
				SELECT
					U.id,
					U.idFinicity,
					U.idFinicityAccount,
					U.szFinicityAccountNumber,
					U.szFinicityAccountTransitNumber,
					U.idFinicityInstitution,
					FI.szName AS szFinicityInstitution,
					FI.szInstitutionNumber AS szFinicityInstitutionNumber,				
					U.szFirstName,
					U.szLastName,
					U.szEmail,
					U.szMobilePhone,
					U.szPassword,
					U.iSignupStep,
					U.idInstitution,
					SI.szName AS szInstitution,
					SI.szInstitutionNumber AS szInstitutionNumber,
					U.szTransitNumber,
					U.szAccountNumber,
					U.szVerificationFile,
					U.iSavingAccountVerified,
					U.fAbsoluteMinBalance,
					U.fSurplusDeficitRate,
					U.fIfDeficitRate
				FROM
					" . __DBC_SCHEMATA_USERS__ . " U
				LEFT JOIN
					" . __DBC_SCHEMATA_INSTITUTIONS__ . " FI
				ON
					FI.id = U.idFinicityInstitution
				LEFT JOIN
					" . __DBC_SCHEMATA_INSTITUTIONS__ . " SI
				ON
					SI.id = U.idInstitution
				" . ($where != "" ? "WHERE $where" : "") . "
				" . ($sort_by != '' ? "ORDER BY $sort_by $sort_order" : "") . "
				" . ((int)$limit > 0 ? "LIMIT " . (int)$start_from . ", " . (int)$limit : "") . "
			";
		}

		if($result=$this->exeSQL($query))
      	{
	       	if ($result->num_rows > 0)
	        {
	        	if($count_only)
	        		return $result->num_rows;
	        	else {
                    $arCoustomers = $this->getAssoc($result, true);
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
        return $arCoustomers;
	}
	
	function updateSignupStep($idUser, $iStep = 1, $notify=true)
	{
		if((int)$iStep == 0) $iStep = 1;
		
		$query = "
         	UPDATE
         		" . __DBC_SCHEMATA_USERS__ . "
         	SET
         		iSignupStep = " . (int)$iStep . ",
         		dtSignupVerified = " . ($iStep == 5 ? "NOW()" : "'0000-00-00 00:00:00'") . "
         	WHERE
         		id = " . (int)$idUser . "
      	";
		if($result=$this->exeSQL($query))
      	{      		
      		$this->loadCustomer($idUser);
      		
      		if($notify)
      		{
	      		if($iStep == 5)
	      		{		
	      			// send notification email
					$message = "
					Congratulation {$this->szFirstName},<br>
					This is Tally, I'll be keeping you posted every step of the way along your savings journey! If you ever have any questions for me, just sign into your account at www.tallyfinancial.com and enter them in the 'Help' tab.<br><br>				
					Thanks,<br>
					Tally Financial
					";
					$subject = $this->szFirstName." ".$this->szLastName." Tally account verified";
					$to = "{$this->szFirstName} {$this->szLastName} <{$this->szEmail}>";
					$from = __CUSTOMER_SUPPORT_EMAIL__;				
					// sendEmail($to, $from, $subject, $message);
					
					// send text message
					$data['szMobilePhone'] = $this->szMobilePhone;
					
					$replace_ary = array();
					$replace_ary['FIRSTNAME'] = $this->szFirstName;
					$data['szMessage'] = createMessage(getMessageTemplate('Account Verify'), $replace_ary);
					
					sendMessege($data['szMobilePhone'], $data['szMessage']);
	      		}
	      		/*else
	      		{
	      			// send notification email
	      			$message = "
					Dear {$this->szFirstName},<br>
					Your Tally account has been blocked. Please <a href='#'>contact us</a> for further assistance.<br><br>
					
					Thanks,<br>
					Tally Team
					";
					$subject = $this->szFirstName." ".$this->szLastName." Tally account blocked";
					$to = "{$this->szFirstName} {$this->szLastName} <{$this->szEmail}>";
					$from = __CUSTOMER_SUPPORT_EMAIL__;				
					sendEmail($to, $from, $subject, $message);
					
					// send text message
					$data['szMobilePhone'] = $this->szMobilePhone;
					$data['szMessage'] = "Your Tally account has been blocked. Contact us for further assistance.";
					sendMessege($data['szMobilePhone'], $data['szMessage']);
	      		}*/
      		}
	       	return true;
        }
        else
        { 
          	$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
        }
	}
	
	function updateSavingAccountDetailsStaus($idUser, $iStatus = 1)
	{
		if((int)$iStatus < 0) $iStatus = 1;
		
		$query = "
         	UPDATE
         		" . __DBC_SCHEMATA_USERS__ . "
         	SET
         		iSavingAccountVerified = " . (int)$iStatus . "
         	WHERE
         		id = " . (int)$idUser . "
      	";
		if($result=$this->exeSQL($query))
      	{
      		$this->loadCustomer($idUser);
      		if($iStatus == 1)
      		{      	
      			// send notification email						
				$message = "
				Congratulation {$this->szFirstName},<br>
				Your saving account has been verified successfully.<br><br>				
				Thanks,<br>
				Tally Team
				";
				$subject = $this->szFirstName." ".$this->szLastName." saving account verified";
				$to = "{$this->szFirstName} {$this->szLastName} <{$this->szEmail}>";
				$from = __CUSTOMER_SUPPORT_EMAIL__;				
				sendEmail($to, $from, $subject, $message);
				
				// send text message
				$data['szMobilePhone'] = $this->szMobilePhone;
				$data['szMessage'] = "Your saving account has been verified successfully.";
				sendMessege($data['szMobilePhone'], $data['szMessage']);
      		}
      		else
      		{
      			// send notification email
      			$message = "
				Dear {$this->szFirstName},<br>
				Your saving account has been blocked. Please <a href='#'>contact us</a> for further assistance.<br><br>				
				Thanks,<br>
				Tally Team
				";
				$subject = $this->szFirstName." ".$this->szLastName." saving account blocked";
				$to = "{$this->szFirstName} {$this->szLastName} <{$this->szEmail}>";
				$from = __CUSTOMER_SUPPORT_EMAIL__;				
				sendEmail($to, $from, $subject, $message);
								
				// send text message
				$data['szMobilePhone'] = $this->szMobilePhone;
				$data['szMessage'] = "Your saving account has been blocked. Contact us for further assistance.";
				sendMessege($data['szMobilePhone'], $data['szMessage']);
      		}
	       	return true;
        }
        else
        { 
          	$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
        }
	}
	
	function searchUserTransactions($idCustomer, $startDate, $endDate, $fAmount, $operator)
	{
		$arTransactions = array();
		if((int)$idCustomer > 0 && $startDate != '' && $endDate != '')
		{
			$dtStart = date("Y-m-d", strtotime($startDate)) . " 00:00:00";
			$dtEnd = date("Y-m-d", strtotime($endDate)) . " 23:59:59";
			
			$query = "
				SELECT
					*
				FROM
					" . __DBC_SCHEMATA_TRANSACTIONS__ . "
				WHERE
					idCustomer = " . (int)$idCustomer . "
				AND
					dtDate >= '" . $this->sql_real_escape_string($dtStart) . "'
				AND
					dtDate <= '" . $this->sql_real_escape_string($dtEnd) . "'
				" . (trim($fAmount) != '' ? "AND fAmount " . ($operator == "gt" ? ">" : ($operator == "gteq" ? ">=" : ($operator == "lt" ? "<" : ($operator == "lteq" ? "<=" : "=")))) . " " . (float)$fAmount : "") . "
				ORDER BY
					dtDate
			";
			if($result=$this->exeSQL($query))
            {
	            if ($this->iNumRows > 0)
	            {
	            	$arTransactions = $this->getAssoc($result, true);
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
		return $arTransactions;
	}
	
	function saveCustomerConstants($data)
	{
		$this->set_id(sanitize_all_html_input(trim($data['p_id'])));
		$this->set_fAbsoluteMinBalance(sanitize_all_html_input(trim($data['p_absmin'])));
		$this->set_fSurplusDeficitRate(sanitize_all_html_input(trim($data['p_surplusdeficit'])));
		$this->set_fIfDeficitRate(sanitize_all_html_input(trim($data['p_ifdeficit'])));
		
		if($this->error)
			return false;
			
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_USERS__ . "
			SET
				fAbsoluteMinBalance = " . (float)$this->fAbsoluteMinBalance . ",
				fSurplusDeficitRate = " . (float)$this->fSurplusDeficitRate . ",
				fIfDeficitRate = " . (float)$this->fIfDeficitRate . "
			WHERE
				id = " . (int)$this->id . "
		";
		if($result=$this->exeSQL($query))
    	{
	    	return true;
    	}
      	else
    	{ 
          	$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
        }
	}
	
	public function verifyInstitutionDetails($data, $arExclude=array())
	{
		if(!in_array('p_institution_id', $arExclude)) $this->set_idInstitution(sanitize_all_html_input(trim($data['p_institution_id'])));
		if(!in_array('p_institution_number', $arExclude)) $this->set_szInstitutionNumber(sanitize_all_html_input(trim($data['p_institution_number'])));
		
		if(!$this->error)
			return true;
		else
			return false;
	}
	
	public function updateInstitutionNumber()
	{
		if($this->error)
			return false;
			
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_INSTITUTIONS__ . "
			SET
				szInstitutionNumber = '" . $this->sql_real_escape_string($this->szInstitutionNumber) . "'
			WHERE
				id = " . (int)$this->idInstitution . "
		";
		if(($result = $this->exeSQL( $query )))
		{
			return true;
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
	}
	
	function updateCustomerUniqueKey($idCustomer)
	{
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_USERS__ . "
			SET
				szUniqueKey = '" . $this->sql_real_escape_string($this->getUniqueKeyForCustomer()) . "'
			WHERE
				id = " . (int)$idCustomer . "
		";
		if($result=$this->exeSQL($query))
    	{
	    	return true;
    	}
      	else
    	{ 
          	$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
        }
	}
	
	function getCustomerSavingsTransfers($idCustomer, $iLimit=0, $iActive=1, $byTransactionID=false)
	{
		$arTransfers = array();
		$iLimit = (int)$iLimit;
		$idCustomer = (int)$idCustomer;
		if($idCustomer > 0)
		{
			$query = "
				SELECT
					*
				FROM
					" . __DBC_SCHEMATA_USER_SAVINGS_TRANSFERS__ . "
				WHERE
					" . ($byTransactionID ? "id" : "idCustomer") . " = $idCustomer
				AND
					iStatus = $iActive
				ORDER BY
					dtCreatedOn DESC
				" . ($iLimit > 0 ? "LIMIT 0, $iLimit" : "") . "
			";
			if($result=$this->exeSQL($query))
	    	{
		    	if($this->iNumRows > 0)
		    	{
		    		$arTransfers = $this->getAssoc($result, true);
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
		return $arTransfers;
	}
	
	function addCustomerSavingsTransfer($idCustomer, $fTransferAmount, $iRequested=0, $p_date='', $p_type=0)
	{
		$idCustomer = (int)$idCustomer;
		$fTransferAmount = (float)$fTransferAmount;
		if($idCustomer > 0 && $fTransferAmount > 0)
		{
			$query = "
				INSERT INTO
					" . __DBC_SCHEMATA_USER_SAVINGS_TRANSFERS__ . "
				(
					idCustomer,
					fAmount,
					dtCreatedOn,
					iTransferType					
					" . ($iRequested > 0 ? ", iType, iStatus" : "") . "
				)
				VALUES
				(
					$idCustomer,
					$fTransferAmount,
					" . ($p_date != '' ? "'" . $this->sql_real_escape_string(trim($p_date)) . "'" : "NOW()") . ",
					" . (int)$p_type . "
					" . ($iRequested > 0 ? ", 2, 0" : "") . "
				)
			";
			if($result=$this->exeSQL($query))
	    	{
		    	return true;
	    	}
	      	else
	    	{ 
	          	$this->error = true;
				$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to load because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
				$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
				return false;
	        }
		}
		else
		{
			$this->addError('p_amount', 'Unable to transfer amount, Please try again.');
			return false;
		}
	}
	
	function approveCustomerSavingsTransfer($idTransfer, $dtApproveDate, $iType)
	{
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_USER_SAVINGS_TRANSFERS__ . "
			SET
				dtApprovedOn = '" . $this->sql_real_escape_string($dtApproveDate) . "',
				iTransferType = " . (int)$iType . ",
				iStatus = '1'
			WHERE
				id = " . (int)$idTransfer . "
		";
		if($result=$this->exeSQL($query))
    	{
	    	return true;
    	}
      	else
    	{ 
          	$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
        }
	}
	
	function rejectCustomerSavingsTransfer($idTransfer)
	{
		$query = "
			DELETE FROM
				" . __DBC_SCHEMATA_USER_SAVINGS_TRANSFERS__ . "
			WHERE
				id = " . (int)$idTransfer . "
		";
		if($result=$this->exeSQL($query))
    	{
	    	return true;
    	}
      	else
    	{ 
          	$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
        }
	}
	
	function getChequingBalance($idUser)
	{
		$fChequingbalance = 0;
		$bal_query = "
			SELECT
				fBalance,
				dtBalanceDate
			FROM
				" . __DBC_SCHEMATA_USER_CURRENT_BALANCE__ . "
			WHERE
				idCustomer = " . (int)$idUser . "
			ORDER BY
				dtBalanceDate DESC
			LIMIT
				0, 1
		";
		if($bal_result = $this->exeSQL($bal_query))
		{
			if($this->iNumRows > 0)
			{
				$bal_row = $this->getAssoc($bal_result);
				$fChequingbalance = format_number($bal_row['fBalance']);
			}
		}
		return $fChequingbalance;
	}
	
	function changeAutoSavingStatus($idUser, $iStatus, $dtLast)
	{
		$isChanged = false;
		$this->iAutoSavings = (int)$iStatus;
		if(date("Y-m-d") != $dtLast)
		{
			$isChanged = true;
			$this->dtAutoSavingsChanged = date("Y-m-d H:i:s");
		}
		
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_USERS__ . "
			SET
				iAutoSavings = {$this->iAutoSavings}
				" . ($isChanged ? ", dtAutoSavingsChanged = '{$this->dtAutoSavingsChanged }'" : "") . "
			WHERE
				id = " . (int)$idUser  . "
		";
		if($this->exeSQL($query))
		{
			if($this->getRowCnt() > 0)
			{
				if(!$isChanged)
				{
					$this->dtAutoSavingsChanged = date("Y-m-d H:i:s");
					
					$query = "
						UPDATE
							" . __DBC_SCHEMATA_USERS__ . "
						SET
							dtAutoSavingsChanged = '{$this->dtAutoSavingsChanged }'
						WHERE
							id = " . (int)$idUser  . "
					";
					$this->exeSQL($query);
				}
				
				return true;
			}
			else
			{
				$this->addError("p_suto_saving", "No change made");
				return false;
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
	
	function changeMinimumThreshold($idUser, $fMinimum)
	{
		$this->set_fMinThreshold(sanitize_all_html_input(trim($fMinimum)));
		
		if($this->error)
			return false;
			
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_USERS__ . "
			SET
				fMinThreshold = " . (float)$this->fMinThreshold . "
			WHERE
				id = " . (int)$idUser  . "
		";
		if($this->exeSQL($query))
		{
			if($this->getRowCnt() > 0)
			{
				return true;
			}
			else
			{
				$this->addError("p_threshold", "No change made");
				return false;
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
	
	function addUserQuery($data)
	{
		$query = "
			INSERT INTO
				" . __DBC_SCHEMATA_USER_QUERIES__ . "
			(
				idUser,
				szComment
			)
			VALUES
			(
				'" . (int)$data['p_id'] . "',
				'" . $this->sql_real_escape_string(trim($data['p_comment'])) . "'
			)
		";
		if($this->exeSQL($query))
		{
			if($this->getRowCnt() > 0)
			{
				// send email to admin
				$message = "User {$data['p_fname']} {$data['p_lname']}({$data['p_email']}) has posted the following comment in Tally Help Center-<br><br>" . nl2br(trim($data['p_comment']));
				$subject = $data['p_fname']." ".$data['p_lname']." need help!";
				$from = __CUSTOMER_SUPPORT_EMAIL__;
				$to = __ADMIN_USER_EMAIL__;
				sendEmail($to, $from, $subject, $message);
		
				return true;
			}
			else
			{
				$this->addError("p_threshold", "No change made");
				return false;
			}
		}
		else
    	{ 
          	$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to add because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
        }
	}
	
	function updateLoginStep($idUser, $iStep = 0)
	{
		$query = "
         	UPDATE
         		" . __DBC_SCHEMATA_USERS__ . "
         	SET
         		iLoginStep = " . (int)$iStep . "
         	WHERE
         		id = " . (int)$idUser . "
      	";
		if($result=$this->exeSQL($query))
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
	
	function deleteCustomer($idCustomer)
	{
		// first check whether the record exists or not
		if($this->loadCustomer($idCustomer))
		{
			// First delete customer record from finicity
			$this->load->model('Finicity_Model');
			if($this->Finicity_Model->deleteCustomerFromFinicity($this->idFinicity))
			{
				// delete all transaction
				$tran_query = "DELETE FROM " . __DBC_SCHEMATA_TRANSACTIONS__ . " WHERE idCustomer = " . (int)$this->idFinicity . "";
				$this->exeSQL($tran_query);
				
				// delete monthly calculations
				$cals_query = "DELETE FROM " . __DBC_SCHEMATA_MONTHLY_CALCULATIONS__ . " WHERE idCustomer = " . (int)$this->idFinicity . "";
				$this->exeSQL($cals_query);
				
				// delete saving transactions
				$sav_query = "DELETE FROM " . __DBC_SCHEMATA_SAVINGS_TRANSACTIONS__ . " WHERE idCustomer = " . (int)$this->idFinicity . "";
				$this->exeSQL($sav_query);
				
				// delete saving transferes
				$trans_query = "DELETE FROM " . __DBC_SCHEMATA_USER_SAVINGS_TRANSFERS__ . " WHERE idCustomer = " . (int)$this->idFinicity . "";
				$this->exeSQL($trans_query);
				
				// delete current balances				
				$bal_query = "DELETE FROM " . __DBC_SCHEMATA_USER_CURRENT_BALANCE__ . " WHERE idCustomer = " . (int)$this->idFinicity . "";
				$this->exeSQL($bal_query);
				
				// delete queries			
				$bal_query = "DELETE FROM " . __DBC_SCHEMATA_USER_QUERIES__ . " WHERE idUser = " . (int)$this->id . "";
				$this->exeSQL($bal_query);
				
				// delete related documents
				if($this->szFinicityAccountVerificationFile != '' && file_exists(__APP_PATH_ASSETS__ . "/images/users/{$this->szFinicityAccountVerificationFile}"))
				{
					unlink(__APP_PATH_ASSETS__ . "/images/users/{$this->szFinicityAccountVerificationFile}");
				}
				if($this->szFinicityStatementVerificationFile != '' && file_exists(__APP_PATH__ . "/statements/{$this->szFinicityStatementVerificationFile}"))
				{
					unlink(__APP_PATH__ . "/statements/{$this->szFinicityStatementVerificationFile}");
				}
				if($this->szVerificationFile != '' && file_exists(__APP_PATH__ . "/statements/{$this->szVerificationFile}"))
				{
					unlink(__APP_PATH__ . "/statements/{$this->szVerificationFile}");
				}
				
				// now delete main user account
				$query = "DELETE FROM " . __DBC_SCHEMATA_USERS__ . " WHERE id = " . (int)$this->id;
				if($this->exeSQL($query))
				{
					return true;
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to delete because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
					$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
					return false;
				}				
			}
			else
			{
				$this->addError('p_customer', 'Problem while deleting record, Please try after some time.');
				return false;
			}
		}
		else
		{
			$this->addError('p_customer', 'User record not exists');
			return false;
		}
	}
}
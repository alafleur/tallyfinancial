<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Admin_Model extends Database_Model
{
	var $szUsername;
	var $szPassword;
	
	function __construct()
	{
		parent::__construct();
		return true;
	}
	
	function set_szUsername($value,$flag=true)
    {
        $this->szUsername = $this->validateInput($value, __VLD_CASE_ANYTHING__, "p_username", "Username", false, false, $flag);
    }
    
	function set_szEmail($value,$flag=true)
    {
        $this->szEmail = $this->validateInput($value, __VLD_CASE_EMAIL__, "p_email", "Email address", false, false, $flag);
    }
	
	function set_szPassword($value, $flag=true)
	{
		 $this->szPassword = $this->validateInput($value, __VLD_CASE_PASSWORD__, "p_password", "Password", 6, 32, $flag);
	}
	
	function validateAdminData($data, $arExclude=array())
	{
		if(!empty($data))
		{
			if(!in_array('p_username', $arExclude)) $this->set_szUsername(sanitize_all_html_input(trim($data['p_username'])));
			if(!in_array('p_password', $arExclude)) $this->set_szPassword(sanitize_all_html_input(trim($data['p_password'])));
			
			if(!$this->error)
				return true;
			else
				return false;
		}
		return false;
	}
	
	public function checkAdminExists($szUsername=false)
	{	
		$szUsername = trim($szUsername);
		if(!$szUsername && !empty($_SESSION['arr']['login']))
		{
			$szUsername = $_SESSION['arr']['login'];
		}
		
		if($szUsername != "")
		{			
		 	$query = "
	 			SELECT
	 				id,
	 				szUsername,
	 				szPassword
	 			FROM
	 				".__DBC_SCHEMATA_ADMIN__."
	 			WHERE
	 				szUsername = '".$this->sql_real_escape_string($szUsername) . "'
	 		";
	 	 	if($result=$this->exeSQL($query))
            {
	            if ($this->iNumRows > 0)
	            {
	            	$row = $this->getAssoc($result);
	            	$this->id = (int)$row['id'];
	            	$this->szUsername = trim($row['szUsername']);
           			$this->szPassword = trim($row['szPassword']);
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
	
	function checkAdminLogin()
	{		
		if((int)$_SESSION['arr']['id'] > 0)
		{
			$login = 1;
		}
		else
		{
			$login = 0;
            if(isset($_COOKIE['__ulfabt_go']) )
            {
             	$encKey = "#12EQ#83#";$encKey2="#AR3UIL#452#";$encKey3="#GBTE#ER23#";                    
                $decryptedC1 = base64_decode($_COOKIE['__ulfabt_go']);
                $decryptedC2 = preg_replace("/$encKey/", "", $decryptedC1);

                list($_SESSION['arr']['id'], $_SESSION['arr']['login']) = explode("~", $decryptedC2);
                $login = 1;
            }
            
		}
		if($login==1)
		{
			return true;	
		}
		else
		{
			return false;
		}		
	}
	
	function send_forget_password_link($szEmail)
	{
		if(trim($szEmail) != '' && trim($szEmail) == __ADMIN_USER_EMAIL__)
		{			
			$link_key = $this->create_link_key();		
			$query = "
				UPDATE
					" . __DBC_SCHEMATA_ADMIN__ . " 
				SET
					szResetPassLinkKey = '" . $this->sql_real_escape_string($link_key) . "'
			";
				
			if($this->exeSQL($query))
			{
				$message = "
				Hello admin,<br><br>
				<a href=\"" . __BASE_ADMIN_URL__ . "/forgot-password/$link_key\"><b>Click Here to reset your password.</b></a><br><br>
				
				Thanks,<br>
				Tally Team
				";
				$subject = "Tally admin Reset Your Password";
				$to = __ADMIN_USER_EMAIL__;
				$from = __CUSTOMER_SUPPORT_EMAIL__;
				
				sendEmail($to, $from, $subject, $message);
				return true;
			}
			else
			{
				$this->error = true;
				$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to insert because of a mysql error. SQL: " . $query . " MySQL Error: " . $this->sql_error();
				$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
				return false;
			}
		}
	}
	
	function is_link_key_exists($key)
	{
		$key = trim($key);
		if($key != '')
		{
			$query = "
				SELECT
					id,
					szResetPassLinkKey
				FROM
					" . __DBC_SCHEMATA_ADMIN__ . "
			";
			if($result = $this->exeSQL($query))
			{
				if($this->iNumRows > 0)
				{
					$row = $this->getAssoc($result);
					if(trim($row['szResetPassLinkKey']) == $key)
					{
						$this->id= (int)$row['id'];
						return true;
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
		return false;
	}
	
	public function updatePassword($szNewPassword)
	{
	 	if(!empty($szNewPassword))
	 	{
	 		$query="
	 			UPDATE
					" . __DBC_SCHEMATA_ADMIN__ . "
				SET	
					szPassword = '" . (!empty($szNewPassword) ? $this->sql_real_escape_string(encrypt(trim($szNewPassword))) : '') . "'				
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
	
	function remove_pass_reset_link_key()
	{
		$query = "
			UPDATE
				" . __DBC_SCHEMATA_ADMIN__ . "
			SET
				szResetPassLinkKey = ''
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
		
		return $link_key;
	}
}
?>
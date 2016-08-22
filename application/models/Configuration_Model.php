<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Configuration_Model extends Database_Model
{
	function __construct()
	{
		parent::__construct();
		return true;
	}
	
	function addConstant($szName, $szValue)
	{
		$szName = trim($szName);
		$szValue = trim($szValue);
		if($szName != '' && $szValue != '')
		{
			if($this->isConstantAlreadyExists($szName))
			{
				$this->addError('p_name', 'Constant name already exists.');	
				return false;
			}
			else
			{
				$query = "
					INSERT INTO
						" . __DBC_SCHEMATA_CONSTANTS__ . "
					(
						szName,
						szValue
					)
					VALUES
					(
						'" . $this->sql_real_escape_string($szName) . "',
						'" . $this->sql_real_escape_string($szValue) . "'
					)
				";
				if($this->exeSQL($query))
				{
					if($this->getRowCnt() > 0)
						$this->id = $this->iLastInsertID;
					
					return true;
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to add because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
					$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
					return false;
				}
			}
		}
		else
		{
			if($szName == '')
				$this->addError('p_name', 'Constant name is required.');
				
			if($szValue == '')
				$this->addError('p_value', 'Constant value is required.');
			
			return false;
		}
	}
	
	function saveConstant($szValue, $idConstant)
	{
		$szValue = trim($szValue);
		$idConstant = (int)$idConstant;
		
		if($szValue != '' && $idConstant > 0)
		{
			$query = "
				UPDATE
					" . __DBC_SCHEMATA_CONSTANTS__ . "
				SET
					szValue = '" . $this->sql_real_escape_string($szValue) . "',
					dtUpdatedOn = NOW()
				WHERE
					id = $idConstant 
			";
			if($this->exeSQL($query))
			{
				return true;
			}
			else
			{
				$this->error = true;
				$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
				$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
				return false;
			}
		}
		else
		{
			if($szValue == '')
				$this->addError('p_value', 'Constant value is required.');
			
			if($idConstant <= 0)
				$this->addError('p_id', 'Constant ID is not valid');
			
			return false;
		}
	}
	
	function isConstantAlreadyExists($szName, $idConstant=0)
	{
		$szName = trim($szName);
		$idConstant = (int)$idConstant;
		if($szName != '')
		{
			$query = "
				SELECT
					id
				FROM
					" . __DBC_SCHEMATA_CONSTANTS__ . "
				WHERE
					szName = '" . $this->sql_real_escape_string($szName) . "'
				" . ($idConstant > 0 ? "AND id != $idConstant" : "") . "
			";
			if($this->exeSQL($query))
			{
				if($this->iNumRows > 0)
					return true;
				else
					return false;
			}
			else
			{
				$this->error = true;
				$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
				$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
				return false;
			}
		}
	}
	
	function getConstants($idConstant=0)
	{
		$arr = array();
		$idConstant = (int)$idConstant;
		
		$query = "
			SELECT
				*
			FROM
				" . __DBC_SCHEMATA_CONSTANTS__ . "
			" . ($idConstant > 0 ? "WHERE id = $idConstant" : "") . "
			ORDER BY
				szName
		";
		if($result = $this->exeSQL($query))
		{
			if($this->iNumRows > 0 )
			{
				$arr = $this->getAssoc($result, true);
			}
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to add because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
		return $arr;
	}
	
	function getConstantValueByName($szName)
	{
		$szValue = "";
		$szName = trim($szName);
		
		$query = "
			SELECT
				szValue
			FROM
				" . __DBC_SCHEMATA_CONSTANTS__ . "
			WHERE
				szName = '" . $this->sql_real_escape_string($szName) . "'
		";
		if($result = $this->exeSQL($query))
		{
			if($this->iNumRows > 0 )
			{
				$row = $this->getAssoc($result);
				$szValue = trim($row['szValue']);
			}
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to add because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
		return $szValue;
	}
	
	function getCustomerBiMonthlySavingTransactions($idUser, $iMonth=0, $iYear=0, $szSortBy="iTransactionNumber", $szSortOrder="ASC", $iLimit=0, $byTransaction=false)
	{
		$arTransactions = array();
		$query = "
			SELECT
				*
			FROM
				" . __DBC_SCHEMATA_SAVINGS_TRANSACTIONS__ . "
			WHERE
				" . ($byTransaction ? "id" : "idCustomer") . " = " . (int)$idUser . "
			" . ($iMonth > 0 ? "AND iYear = " . (int)$iYear : "") . "
			" . ($iYear > 0 ? "AND iMonth = " . (int)$iMonth : "") . "
			ORDER BY
				$szSortBy $szSortOrder
			" . ($iLimit > 0 ? "LIMIT 0, $iLimit" : "") . "
		";
		if($result = $this->exeSQL($query))
		{
			if($this->iNumRows > 0 )
			{
				$arTransactions = $this->getAssoc($result, true);
			}
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to add because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
		
		return $arTransactions;
	}
	
	function updateSavingTransactionAmount($idTransaction, $fAmount)
	{
		$ar = $this->getCustomerBiMonthlySavingTransactions($idTransaction, 0, 0, "dtCreatedOn", "ASC", 0, true);		
		if(!empty($ar))
		{
			$fAmount = abs($fAmount);
			if($this->validateInput($fAmount, __VLD_CASE_NUMERIC__, 'p_amount', 'Transaction amount', false, false, true))
			{
				$query = "
					UPDATE
						" . __DBC_SCHEMATA_SAVINGS_TRANSACTIONS__ . "
					SET
						fSavingAmount = " . (float)(0 - $fAmount) . "
					WHERE
						id = " . (int)$idTransaction . "
				";
				if($this->exeSQL($query))
				{
					if($this->getRowCnt() > 0)
					{
						return true;
					}
					else
					{
						$this->addError('p_amount', 'No change made');
						return false;
					}
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
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
			$this->addError('p_id', 'Not a valid transaction.');
			return false;
		}
	}
	
	function addTemplate($szName, $szValue)
	{
		$szName = trim($szName);
		$szValue = trim($szValue);
		if($szName != '' && $szValue != '')
		{
			if($this->isTemplateAlreadyExists($szName))
			{
				$this->addError('p_name', 'Template name already exists.');	
				return false;
			}
			else
			{
				$query = "
					INSERT INTO
						" . __DBC_SCHEMATA_TEMPLATES__ . "
					(
						szName,
						szValue
					)
					VALUES
					(
						'" . $this->sql_real_escape_string($szName) . "',
						'" . $this->sql_real_escape_string($szValue) . "'
					)
				";
				if($this->exeSQL($query))
				{
					if($this->getRowCnt() > 0)
						$this->id = $this->iLastInsertID;
					
					return true;
				}
				else
				{
					$this->error = true;
					$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to add because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
					$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
					return false;
				}
			}
		}
		else
		{
			if($szName == '')
				$this->addError('p_name', 'Template name is required.');
				
			if($szValue == '')
				$this->addError('p_value', 'Template value is required.');
			
			return false;
		}
	}
	
	function saveTemplate($szValue, $idConstant)
	{
		$szValue = trim($szValue);
		$idConstant = (int)$idConstant;
		
		if($szValue != '' && $idConstant > 0)
		{
			$query = "
				UPDATE
					" . __DBC_SCHEMATA_TEMPLATES__ . "
				SET
					szValue = '" . $this->sql_real_escape_string($szValue) . "',
					dtUpdatedOn = NOW()
				WHERE
					id = $idConstant 
			";
			if($this->exeSQL($query))
			{
				return true;
			}
			else
			{
				$this->error = true;
				$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
				$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
				return false;
			}
		}
		else
		{
			if($szValue == '')
				$this->addError('p_value', 'Template value is required.');
			
			if($idConstant <= 0)
				$this->addError('p_id', 'Template ID is not valid');
			
			return false;
		}
	}
	
	function isTemplateAlreadyExists($szName, $idConstant=0)
	{
		$szName = trim($szName);
		$idConstant = (int)$idConstant;
		if($szName != '')
		{
			$query = "
				SELECT
					id
				FROM
					" . __DBC_SCHEMATA_TEMPLATES__ . "
				WHERE
					szName = '" . $this->sql_real_escape_string($szName) . "'
				" . ($idConstant > 0 ? "AND id != $idConstant" : "") . "
			";
			if($this->exeSQL($query))
			{
				if($this->iNumRows > 0)
					return true;
				else
					return false;
			}
			else
			{
				$this->error = true;
				$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to save because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
				$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
				return false;
			}
		}
	}
	
	function getTemplates($idConstant=0)
	{
		$arr = array();
		$idConstant = (int)$idConstant;
		
		$query = "
			SELECT
				*
			FROM
				" . __DBC_SCHEMATA_TEMPLATES__ . "
			" . ($idConstant > 0 ? "WHERE id = $idConstant" : "") . "
			ORDER BY
				szName
		";
		if($result = $this->exeSQL($query))
		{
			if($this->iNumRows > 0 )
			{
				$arr = $this->getAssoc($result, true);
			}
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to add because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
		return $arr;
	}
	
	function getTemplateValueByName($szName)
	{
		$szValue = "";
		$szName = trim($szName);
		
		$query = "
			SELECT
				szValue
			FROM
				" . __DBC_SCHEMATA_TEMPLATES__ . "
			WHERE
				szName = '" . $this->sql_real_escape_string($szName) . "'
		";
		if($result = $this->exeSQL($query))
		{
			if($this->iNumRows > 0 )
			{
				$row = $this->getAssoc($result);
				$szValue = trim($row['szValue']);
			}
		}
		else
		{
			$this->error = true;
			$szErrorMessage = __CLASS__ . "::" . __FUNCTION__ .  "() failed to add because of a mysql error. SQL: " . $query . " MySQL Error: " .  $this->sql_error();
			$this->logError( "mysql", $szErrorMessage, "PHP", __CLASS__, __FUNCTION__, __LINE__, "critical");
			return false;
		}
		return $szValue;
	}
}
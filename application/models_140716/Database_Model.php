<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
		
	class Database_Model extends Error_Model
	{
		var $mysqli;
		
		/** @var string */
		var $szSQL;
		
		/** @var int */
		var $iLastInsertID;
		
		/** @var int */
		var $iNumRows;
		
		/** @var string */
		var $szTableName;
		
		/** @var string */
		var $szDatabase;
		
		/** @var int */
		var $iMySQLError;
		
		/** @var string */
		var $szMySQLError;
		
		function __construct()
		{
			parent::__construct();
		}
		
		function setSQL( $sql )
		{
			$this->szSQL = trim( (string)$sql );
		}
		
		function sql_real_escape_string($str)
		{
			return $this->db->escape_str($str);
		}
		
		function sql_error()
		{
			$arError = $this->db->error();
			return $arError['code'];
		}
		
		function exeSQL( $sql = false )
		{					
			$this->setSQL( $sql );
			$result = $this->db->query($sql);			
			
			$arError = $this->db->error();
			$this->iMySQLError = $arError['code'];
			$this->szMySQLError = $arError['message'];		

			if( $this->iMySQLError != 0 )
			{		
				$message = "MySQL Error: " . $this->szMySQLError . "\n\n" . $sql;
				
				// log the message
				$this->logError( "database", $message, "MySQL", __CLASS__, __FUNCTION__, __LINE__, "critical");							
				
				//redirect the header to a default page if the flag is set				
				ob_end_clean();
				header('location:'.__BASE_URL__.'/error');
				die;
			}
			else
			{
				if( preg_match( '/^INSERT INTO/i', $this->szSQL ) )
				{
					$this->iLastInsertID = $this->db->insert_id();					
					return true;
				}
				elseif( preg_match( '/^SELECT/i', $this->szSQL ) )
				{
					//$query = $this->db->get(); // get query result
     				//$count = $query->num_rows(); //get current query record.
					$this->iNumRows = $result->num_rows();
				}

				return $result;
			}
		}

		function getAssoc($result, $ret_ori=false)
		{	
			$results = $result->result_array();
			if(!$ret_ori && count($results) == 1)
			{
				$results = $results[0];
			}
			return $results;
		}

		function getRowCnt()
		{
			return $this->db->affected_rows();
		}			
	}
?>

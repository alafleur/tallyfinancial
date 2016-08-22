<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('Configuration_Model.php');

Class Cronjob_Model extends Configuration_Model{
	function __construct()
	{
		parent::__construct();
		return true;
	}
	
	function getCalculations($customer_id=0, $opening_balance=0, $today='')
	{
		$today = ($today != '' ? date("Y-m-d ", strtotime($today)) . "23:59:59" : date("Y-m-d H:i:s"));
		$today_year = date("Y", strtotime($today));
		$today_month = date("n", strtotime($today));
		$today_day = date("d", strtotime($today));
		$tomorrow_month = date("n", strtotime("+1 day" . $today));
		//echo "Hello $today";die;
		
		// run only at end of month 
		if($today_month != $tomorrow_month)
		{
			$today_month = ($today_month < 10 ? "0$today_month" : $today_month);
			
			// get the customer records
			$cus_query = "
				SELECT
					idFinicity,
					fAbsoluteMinBalance
				FROM
					" . __DBC_SCHEMATA_USERS__ . "
				WHERE
					iSignupStep > 4
				" . ((int)$customer_id > 0 ? "AND idFinicity = " . (int)$customer_id : "") . "
			";
			if($cus_result = $this->exeSQL($cus_query))
			{
				if($this->iNumRows > 0)
				{
					$ar_customers = $this->getAssoc($cus_result, true);
					foreach($ar_customers as $cus_row)
					{
						$idCustomer = (int)$cus_row['idFinicity'];
						
						// get last and previous months calculations
						$arLastMonthCalculations = getPreviousMonthsCalculations($idCustomer, $today_month, $today_year, $this, 1);
						$arPrevMonthsCalculations = getPreviousMonthsCalculations($idCustomer, $today_month, $today_year, $this);
											
						$fOpeningBalance = $fTotalExpense = $fTotalIncome = $fClosingBalance = $fSavingRate = 0;
						$fWeek1Expense = $fWeek2Expense = $fWeek3Expense = $fWeek4Expense = $fWeek5Expense = 0;
						$fWeek1ExpenseAverage = $fWeek2ExpenseAverage = $fWeek3ExpenseAverage = $fWeek4ExpenseAverage = $fWeek5ExpenseAverage = 0;
						$fAverageIncomeMonthly = $fAverageIncomeWeekly = 0;
						$fMaxExpense = $fMaxExpenseAverage = $fMaxExpenseCover = $fMinimumCushion = 0;
						if((float)$cus_row['fAbsoluteMinBalance'] > 0)
						{
							$fAbsoluteMinBalance = format_number($cus_row['fAbsoluteMinBalance']);
						}
						else
						{
							$fAbsoluteMinBalance = format_number($this->getConstantValueByName('Absolute minimum of chequing account balance'));
							if((float)$fAbsoluteMinBalance == 0)
								$fAbsoluteMinBalance = format_number(500);
						}
											
						// get transaction of the month
						$tran_query = "
							SELECT
								fAmount,
								dtDate
							FROM
								" . __DBC_SCHEMATA_TRANSACTIONS__ . "
							WHERE
								idCustomer = " . (int)$idCustomer . "
							AND
								dtDate >= '" . $this->sql_real_escape_string("{$today_year}-{$today_month}-01 00:00:00") . "'
							AND
								dtDate <= '" . $this->sql_real_escape_string("{$today_year}-{$today_month}-{$today_day} 23:59:59") . "'						
						";
						//echo "$tran_query<br><br>";die;
						if($tran_result = $this->exeSQL($tran_query))
						{
							if($this->iNumRows > 0)
							{
								$ar_transactions = $this->getAssoc($tran_result, true);
								foreach($ar_transactions as $tran_row)
								{								
									$fAmount = format_number($tran_row['fAmount']);
									$week_day = date("j", strtotime($tran_row['dtDate']));
									
									if($fAmount < 0)
									{
										$fTotalExpense += $fAmount;
										if($fAmount < $fMaxExpense)
										{
											$fMaxExpense = $fAmount;
										}
									}
									else
									{
										$fTotalIncome += $fAmount;
									}
																								
									if($week_day >= 1 && $week_day <= 7)
									{
										if($fAmount < 0)
											$fWeek1Expense += $fAmount;
									}
									else if($week_day >= 8 && $week_day <= 14)
									{
										if($fAmount < 0)
											$fWeek2Expense += $fAmount;
									}
									else if($week_day >= 15 && $week_day <= 21)
									{
										if($fAmount < 0)
											$fWeek3Expense += $fAmount;
									}
									else if($week_day >= 22 && $week_day <= 28)
									{
										if($fAmount < 0)
											$fWeek4Expense += $fAmount;
									}
									else
									{
										if($fAmount < 0)
											$fWeek5Expense += $fAmount;
									}
								}
							}
							else
							{
								continue;
							}
						}
						
						// previous month calculation totals
						$fPrevMonthsTotalIncomeTotal = $fPrevMonthsMaxExpenseAverageTotal = 0;
						$fPrevMonthsWeek1ExpenseTotal = $fPrevMonthsWeek2ExpenseTotal = $fPrevMonthsWeek3ExpenseTotal = $fPrevMonthsWeek4ExpenseTotal = $fPrevMonthsWeek5ExpenseTotal = 0;
						$fPrevMonthsCount = count($arPrevMonthsCalculations);
						if(!empty($arPrevMonthsCalculations))
						{
							foreach($arPrevMonthsCalculations as $cals)
							{
								$fPrevMonthsWeek1ExpenseTotal += $cals['fWeek1Expense'];
								$fPrevMonthsWeek2ExpenseTotal += $cals['fWeek2Expense'];
								$fPrevMonthsWeek3ExpenseTotal += $cals['fWeek3Expense'];
								$fPrevMonthsWeek4ExpenseTotal += $cals['fWeek4Expense'];
								$fPrevMonthsWeek5ExpenseTotal += $cals['fWeek5Expense'];
								
								$fPrevMonthsTotalIncomeTotal += $cals['fTotalIncome'];
								$fPrevMonthsMaxExpenseAverageTotal += $cals['fMaxExpenseAverage'];
							}
						}
						
						$fOpeningBalance = ((float)$arLastMonthCalculations[0]['fClosingBalance'] != 0 ? number_format((float)$arLastMonthCalculations[0]['fClosingBalance'], 2, ".", "") : ((float)$opening_balance > 0 ? $opening_balance : 1700.15));
						$fTotalExpense = format_number($fTotalExpense);
						$fTotalIncome = format_number($fTotalIncome);					
						$fClosingBalance = format_number($fOpeningBalance + $fTotalIncome + $fTotalExpense);
						$fSavingRate = (format_number(($fTotalExpense != 0 ? $fTotalIncome : ($fTotalIncome > 0 ? 2 : 1))/($fTotalExpense != 0 ? abs($fTotalExpense) : 1)) - 1)*100;
						
						$fWeek1Expense = format_number($fWeek1Expense);
						$fWeek2Expense = format_number($fWeek2Expense);
						$fWeek3Expense = format_number($fWeek3Expense);
						$fWeek4Expense = format_number($fWeek4Expense);
						$fWeek5Expense = format_number($fWeek5Expense);
						
						$fWeek1ExpenseAverage = format_number(($fWeek1Expense+$fPrevMonthsWeek1ExpenseTotal)/($fPrevMonthsCount+1));
						$fWeek2ExpenseAverage = format_number(($fWeek2Expense+$fPrevMonthsWeek2ExpenseTotal)/($fPrevMonthsCount+1));
						$fWeek3ExpenseAverage = format_number(($fWeek3Expense+$fPrevMonthsWeek3ExpenseTotal)/($fPrevMonthsCount+1));
						$fWeek4ExpenseAverage = format_number(($fWeek4Expense+$fPrevMonthsWeek4ExpenseTotal)/($fPrevMonthsCount+1));
						$fWeek5ExpenseAverage = format_number(($fWeek5Expense+$fPrevMonthsWeek5ExpenseTotal)/($fPrevMonthsCount+1));
						
						$fAverageIncomeMonthly = format_number(($fPrevMonthsTotalIncomeTotal+$fTotalIncome)/($fPrevMonthsCount+1));
						$fAverageIncomeWeekly = format_number($fAverageIncomeMonthly/4);
						
						$fMaxExpenseAverage = format_number(($fPrevMonthsMaxExpenseAverageTotal + $fMaxExpense)/($fPrevMonthsCount+1));
						$fMaxExpenseCover = format_number(-($fMaxExpenseAverage + $fAverageIncomeWeekly));
						
						$fMinimumCushion = max($fMaxExpenseCover, $fAbsoluteMinBalance);
						
						$fTotalSaving = format_number(0);
						// get total saving for the month
						$total_query = "
							SELECT
								SUM(fSavingAmount) AS fTotalSaving
							FROM
								tbl_user_biweekly_bimonthly_saving_transactions
							WHERE
								idCustomer = " . (int)$idCustomer . "
							AND
								iMonth = " . (int)$today_month . "
							AND
								iYear = " . (int)$today_year . "
						";
						if($total_result = $this->exeSQL($total_query))
						{
							if($this->iNumRows > 0)
							{
								$total_row = $this->getAssoc($total_result);
								$fTotalSaving = format_number($total_row['fTotalSaving']);
							}
						}
						
						$in_query = "
							INSERT INTO
								" . __DBC_SCHEMATA_USER_BIWEEKLY_BIMONTHLY_CALCULATIONS__ . "
							(
								`idCustomer`,
								`iMonth`,
								`iYear`,
								`fOpeningBalance`,
								`fTotalExpense`,
								`fTotalIncome`,
								`fClosingBalance`,
								`fSavingRate`,
								`fWeek1Expense`,
								`fWeek2Expense`,
								`fWeek3Expense`,
								`fWeek4Expense`,
								`fWeek5Expense`,
								`fWeek1ExpenseAverage`,
								`fWeek2ExpenseAverage`,
								`fWeek3ExpenseAverage`,
								`fWeek4ExpenseAverage`,
								`fWeek5ExpenseAverage`,
								`fAverageIncomeMonthly`,
								`fAverageIncomeWeekly`,
								`fMaxExpense`,
								`fMaxExpenseAverage`,
								`fMaxExpenseCover`,
								`fAbsoluteMinBalance`,
								`fMinimumCushion`,
								`fTotalSaving`,
								`dtCreatedOn`
							)
							VALUES
							(
								$idCustomer,
								$today_month,
								$today_year,
								$fOpeningBalance,
								$fTotalExpense,
								$fTotalIncome,
								$fClosingBalance,
								$fSavingRate, 
								$fWeek1Expense,
								$fWeek2Expense,
								$fWeek3Expense,
								$fWeek4Expense,
								$fWeek5Expense,
								$fWeek1ExpenseAverage,
								$fWeek2ExpenseAverage,
								$fWeek3ExpenseAverage,
								$fWeek4ExpenseAverage,
								$fWeek5ExpenseAverage,
								$fAverageIncomeMonthly,
								$fAverageIncomeWeekly,
								$fMaxExpense,
								$fMaxExpenseAverage,
								$fMaxExpenseCover,
								$fAbsoluteMinBalance,
								$fMinimumCushion,
								$fTotalSaving,
								'$today'
							)
						";
						//echo "$in_query<br><br>";die;
						$this->exeSQL($in_query);
					}
				}
			}
		}
	}
	
	function getSavingTransactions($today='')
	{		
		$bool_run_for_all = false;
		$is_first_time = false;
		$today = (trim($today) != '' ? date("Y-m-d ", strtotime($today)) . "23:59:59" : date("Y-m-d H:i:s"));
		
		$today_year = date("Y", strtotime($today));
		$today_month = date("n", strtotime($today));
		$today_day = date("l", strtotime($today));	
		//echo "Hello $today, today is $today_day.";die;
		
		if($today_day == "Monday" || $today_day == "Thursday")
		{
			// transaction start date
			$startDate = ($today_day == "Monday" ? date("Y-m-d ", strtotime("-4 day" . $today)) . "00:00:00" : date("Y-m-d ", strtotime("-3 day" . $today)) . "00:00:00");
			$toDate = date("Y-m-d", strtotime($today)) . " 00:00:00";
			
			$debit_output_file = __APP_PATH__ . "/output/Debit Transaction on $today_day " . date("jS F Y", strtotime($today)) . ".csv";
			$credit_output_file = __APP_PATH__ . "/output/Credit Transaction on $today_day " . date("jS F Y", strtotime($today)) . ".csv";
			$debit_csv_string = "";
			$credit_csv_string = "";
			
			// get the customer records
			$cus_query = "
				SELECT
					id,				
					idFinicity,
					szFirstName,
					szLastName,
					szFinicityAccountNumber,
					szFinicityAccountTransitNumber,
					(SELECT szInstitutionNumber FROM " . __DBC_SCHEMATA_INSTITUTIONS__ . " WHERE id = idFinicityInstitution) AS szFinicityInstitutionNumber,
					szTransitNumber,
					szAccountNumber,
					(SELECT szInstitutionNumber FROM " . __DBC_SCHEMATA_INSTITUTIONS__ . " WHERE id = idInstitution) AS szInstitutionNumber,
					iSavingAccountVerified,
					fSurplusDeficitRate,
					fIfDeficitRate,
					fMinThreshold,
					iAutoSavings,
					dtAutoSavingsChanged
				FROM
					" . __DBC_SCHEMATA_USERS__ . "
				WHERE
					iSignupStep > 4
			";
			//echo $cus_query;die;
			if($cus_result = $this->exeSQL($cus_query))
			{
				if($this->iNumRows > 0)
				{
					$ar_customers = $this->getAssoc($cus_result, true);
					foreach($ar_customers as $cus_row)
					{
						$idCustomer = (int)$cus_row['idFinicity'];
						$szChequingAccountNumber = trim($cus_row['szFinicityAccountNumber']);
						$szChequingAccountTransitNumber = trim($cus_row['szFinicityAccountTransitNumber']);
						$szChequingInstitutionNumber = trim($cus_row['szFinicityInstitutionNumber']);
						
						$id = (int)$cus_row['id'];
						$szName = trim($cus_row['szFirstName']) . " " . trim($cus_row['szLastName']);
						
						$iAutoSavings = (int)$cus_row['iAutoSavings'];
						if($iAutoSavings != '')
						{
							// resume auto saving
							$dtAutoSavingsChanged = trim($cus_row['dtAutoSavingsChanged']);
							
							if(strtotime("+$iAutoSavings day" . $dtAutoSavingsChanged) <= time())
							{
								
							}
						}
						
						// get saving accounts details
						$iSavaingDetailsVerified = (int)$cus_row['iSavingAccountVerified'];
						if($iSavaingDetailsVerified)
						{
							$szSavingAccountNumber = trim($cus_row['szAccountNumber']);
							$szSavingAccountTransitNumber = trim($cus_row['szTransitNumber']);
							$szSavingInstitutionNumber = trim($cus_row['szInstitutionNumber']);
						}
						
						$fTotalTransactionAmount = 0;
						
						// get last months calculations
						$arLastMonthCalculations = getPreviousMonthsCalculations($idCustomer, $today_month, $today_year, $this, 1);													
						
						// get most recent saving transaction
						$fLastSavingTransactionAmount = 0;
						$fLastSavingTransactionBalance = 0;
						$iTransactionNumber = 1;
						$boolLastMonthSavingTarnsactionExists = false;
											
						$number_query = "
							SELECT
								*
							FROM
								" . __DBC_SCHEMATA_USER_BIWEEKLY_BIMONTHLY_TRANSACTIONS__ . "
							WHERE
								idCustomer = " . (int)$idCustomer . "
							AND
								iYear = " . (int)$today_year . "
							AND
								iMonth = " . (int)$today_month . "
							ORDER BY
								iTransactionNumber DESC
							LIMIT
								0, 1
						";
						//echo "$number_query<br><br>";
						if($number_result = $this->exeSQL($number_query))
						{
							if($this->iNumRows > 0)
							{
								$number_row = $this->getAssoc($number_result);
								
								$iTransactionNumber = (int)$number_row['iTransactionNumber'] + 1;
								$fLastSavingTransactionAmount = format_number($number_row['fSavingAmount']);
								$fLastSavingTransactionBalance = format_number($number_row['fBalanceAmount']);
							}
						}
						
						// check for last month saving transaction
						if($iTransactionNumber == 1)
						{
							$last_transaction_month = ($today_month == 1 ? 12 : ($today_month -1));
							$last_transaction_year = ($today_month == 1 ? ($today_year - 1) : $today_year);
							
							// get last transaction saving amount
							$number_query = "
								SELECT
									*
								FROM
									" . __DBC_SCHEMATA_USER_BIWEEKLY_BIMONTHLY_TRANSACTIONS__ . "
								WHERE
									idCustomer = " . (int)$idCustomer . "
								AND
									iYear = " . (int)$last_transaction_year . "
								AND
									iMonth = " . (int)$last_transaction_month . "
								ORDER BY
									iTransactionNumber DESC
								LIMIT
									0, 1
							";
							//echo "$number_query<br><br>";
							if($number_result = $this->exeSQL($number_query))
							{
								if($this->iNumRows > 0)
								{
									$number_row = $this->getAssoc($number_result);
									$fLastSavingTransactionAmount = format_number($number_row['fSavingAmount']);
									$fLastSavingTransactionBalance = format_number($number_row['fBalanceAmount']);
									$boolLastMonthSavingTarnsactionExists = true;
								}
								else
								{
									// get last month closing balance
									$fLastSavingTransactionBalance = $arLastMonthCalculations[0]['fClosingBalance'];								
								}
							}
						}
						
						// manage transaction date
						if($iTransactionNumber == 1 && !$boolLastMonthSavingTarnsactionExists)
						{
							if(date("n", strtotime($startDate)) != $today_month)
								$startDate = date("Y-m-", strtotime($today)) . "01 00:00:00";
						}
						
						// get transaction of the month
						$tran_query = "
							SELECT
								fAmount,
								dtDate
							FROM
								" . __DBC_SCHEMATA_TRANSACTIONS__ . "
							WHERE
								idCustomer = " . (int)$idCustomer . "
							AND
								dtDate >= '" . $this->sql_real_escape_string($startDate) . "'
							AND
								dtDate < '" . $this->sql_real_escape_string($toDate) . "'						
						";
						//echo "$tran_query<br><br>";
						if($tran_result = $this->exeSQL($tran_query))
						{
							if($this->iNumRows > 0)
							{
								$ar_transactions = $this->getAssoc($tran_result, true);
								foreach($ar_transactions as $tran_row)
								{
									$fAmount = format_number($tran_row['fAmount']);
									
									$fTotalTransactionAmount += $fAmount;
								}
							}
						}
	
						$fExpenseAmount = 0;
						$fBalanceAmount = $fLastSavingTransactionBalance + $fTotalTransactionAmount + $fLastSavingTransactionAmount;					
						if($iTransactionNumber == 1)
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek1ExpenseAverage']*0.50 + $arLastMonthCalculations[0]['fWeek2ExpenseAverage']);
						else if($iTransactionNumber == 2)
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek2ExpenseAverage']);
						else if($iTransactionNumber == 3)
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek2ExpenseAverage']*0.50);
						else if($iTransactionNumber == 4)
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek3ExpenseAverage'] + $arLastMonthCalculations[0]['fWeek4ExpenseAverage'] + $arLastMonthCalculations[0]['fWeek5ExpenseAverage']);
						else if($iTransactionNumber == 5)
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek3ExpenseAverage']*0.50 + $arLastMonthCalculations[0]['fWeek4ExpenseAverage'] + $arLastMonthCalculations[0]['fWeek5ExpenseAverage']);
						else if($iTransactionNumber == 6)
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek4ExpenseAverage'] + $arLastMonthCalculations[0]['fWeek5ExpenseAverage']);
						else if($iTransactionNumber == 7)
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek4ExpenseAverage']*0.50 + $arLastMonthCalculations[0]['fWeek5ExpenseAverage']);
						else if($iTransactionNumber == 8)
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek5ExpenseAverage']);
						else
							$fExpenseAmount = format_number($arLastMonthCalculations[0]['fWeek5ExpenseAverage']*0.50);
						
						if((float)$cus_row['fSurplusDeficitRate'] > 0)
						{
							$fSurplusDeficitRate = format_number($cus_row['fSurplusDeficitRate']);
						}
						else
						{
							$fSurplusDeficitRate = format_number($this->getConstantValueByName('Surplus deficit rate'));
							if((float)$fSurplusDeficitRate == 0)
								$fSurplusDeficitRate = format_number(7.5);
						}
						$fSurplusDeficitAmount = format_number(((-($fBalanceAmount - $arLastMonthCalculations[0]['fMinimumCushion'] + ($fExpenseAmount)))*$fSurplusDeficitRate)/100);
						
						$fIfDeficitRate = $fIfDeficitAmount = format_number(0);
						if($fSurplusDeficitAmount >= 0)
						{
							if($fBalanceAmount > $arLastMonthCalculations[0]['fMinimumCushion'])
							{
								if((float)$cus_row['fIfDeficitRate'] > 0)
								{
									$fIfDeficitRate = format_number($cus_row['fIfDeficitRate']);
								}
								else
								{
									$fIfDeficitRate = format_number($this->getConstantValueByName('If deficit rate'));
									if((float)$fIfDeficitRate == 0)
										$fIfDeficitRate = format_number(5);
								}
								
								$fIfDeficitAmount = format_number(((-($fBalanceAmount - $arLastMonthCalculations[0]['fMinimumCushion']))*$fIfDeficitRate)/100);
							}
						}
						
						$fSavingAmmount = format_number($fSurplusDeficitAmount >= 0 ? $fIfDeficitAmount : $fSurplusDeficitAmount);
						
						$fChequingbalance = 0;
						$bal_query = "
							SELECT
								fBalance,
								dtBalanceDate
							FROM
								" . __DBC_SCHEMATA_USER_CURRENT_BALANCE__ . "
							WHERE
								idCustomer = " . (int)$idCustomer . "
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
						
						//echo "hello $idCustomer | $iTransactionNumber | $fLastSavingTransactionBalance | $fTotalTransactionAmount | $fLastSavingTransactionAmount | $fBalanceAmount | $fExpenseAmount | $fSurplusDeficitRate | $fSurplusDeficitAmount | $fIfDeficitRate | $fIfDeficitAmount | $fSavingAmmount<br><br>";
						
						// save transaction in database
						$in_query = "
							INSERT INTO 
								" . __DBC_SCHEMATA_USER_BIWEEKLY_BIMONTHLY_TRANSACTIONS__ . " 
							(
								`idCustomer`,
								`iMonth`, 
								`iYear`, 
								`iTransactionNumber`, 
								`fBalanceAmount`, 
								`fExpenseAmount`, 
								`fSurplusDeficitAmount`, 
								`fIfDeficitAmount`, 
								`fSavingAmount`,
								`dtCreatedOn`
							)
							VALUES
							(
								$idCustomer,
								$today_month,
								$today_year,
								$iTransactionNumber,
								$fBalanceAmount,
								$fExpenseAmount,
								$fSurplusDeficitAmount,
								$fIfDeficitAmount,
								$fSavingAmmount,
								'$today'
							)
						";
						//echo "$in_query<br><br>";die;
						$this->exeSQL($in_query);
						
						// create csv string
						$fAbsSavingAmmount = abs($fSavingAmmount);
						if((float)$fAbsSavingAmmount > 0)
						{
							$debit_csv_string .= "$id,$fAbsSavingAmmount,".date("m/d/Y",strtotime($today)).",Tally savings\r\n";
							if($iSavaingDetailsVerified)
							{									
								$credit_csv_string .= "$szName,$id,0{$szSavingInstitutionNumber}{$szSavingAccountTransitNumber},$szSavingAccountNumber,200,$fAbsSavingAmmount\r\n";
							}
						}
					}
				}
			}
			
			// save the output files
			if(!empty($debit_csv_string))
			{
				$debit_csv_string = "ID,Amount,Date,Comment\r\n$debit_csv_string";
				$file = fopen($debit_output_file, "w");
				fwrite($file, $debit_csv_string);
				fclose($file);
			}
			
			if(!empty($credit_csv_string))
			{
				$credit_csv_string = "Name,ID#,0-Institution#-Transit# (0IIITTTTT),Account#,Tran Code,Amount\r\n$credit_csv_string";
				$file = fopen($credit_output_file, "w");
				fwrite($file, $credit_csv_string);
				fclose($file);
			}
		}
		else
		{
			// get the customer records
			$cus_query = "
				SELECT
					id,				
					idFinicity,
					szFirstName,
					szMobilePhone
				FROM
					" . __DBC_SCHEMATA_USERS__ . "
				WHERE
					iSignupStep > 4
			";
			//echo $cus_query;die;
			if($cus_result = $this->exeSQL($cus_query))
			{
				if($this->iNumRows > 0)
				{
					$ar_customers = $this->getAssoc($cus_result, true);
					foreach($ar_customers as $cus_row)
					{
						$idCustomer = (int)$cus_row['idFinicity'];
						$szFirstName = trim($cus_row['szFirstName']);
						$szMobilePhone = trim($cus_row['szMobilePhone']);
						
						$fChequingbalance = 0;
						$bal_query = "
							SELECT
								fBalance,
								dtBalanceDate
							FROM
								" . __DBC_SCHEMATA_USER_CURRENT_BALANCE__ . "
							WHERE
								idCustomer = " . (int)$idCustomer . "
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
						
						// send message
						sendMessege($szMobilePhone, "Hi {$szFirstName}, your chequing account balance as of yesterday was ${$fChequingbalance}.");
					}
				}
			}
		}
	}
}
?>
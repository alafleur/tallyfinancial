<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Error_Model extends CI_Model
	{
		var $error = false;
		var $errMessages = array(); 
		var $arErrorMessages;
		
		function __construct()
		{
			$this->error = false;
			$this->arErrorMessages = array();
			parent::__construct();
		}

		
		function CError()
		{
			$argcv = func_get_args();
	 		call_user_func_array( array( &$this, '__construct' ), $argcv );
		}

		
		function formatHTMLError( $message, $format = __VLD_ERROR_DISPLAY__ )
		{
			if( !empty( $message ) )
			{
				if( is_array( $message) )
				{
					$message = implode( "<br>", $message );
				}
				if(!empty($format))
				{
					return str_replace( "{ERROR}", $message, $format );
				}
				else
				{
					return $message;
				}
			}
			else
			{
				return false;
			}
		}

		
		function addError( $error_field, $message )
		{
			$this->error = true;

			if( isset( $this->arErrorMessages[$error_field] ) )
			{
				if( is_array( $this->arErrorMessages[$error_field] ) )
				{
					if( !in_array( $message, $this->arErrorMessages[$error_field] ) )
					{
						$this->arErrorMessages[$error_field][] = $message;
					}
				}
				else
				{
					if( $message != $this->arErrorMessages[$error_field] )
					{
						$this->arErrorMessages[$error_field] = $this->arErrorMessages[$error_field]."<br>".$message;
					}
				}
			}
			else
			{
				$this->arErrorMessages[$error_field] = $message;
			}
		}

        function getErrorMessage($msg, $class)
        {
            return "<div class='$class'>$msg</div>";
        }
		
		function validateInput( $value, $validation, $error_field, $error_message, $min_length = false, $max_length = false, $required = false )
		{
            
			if(!is_array($value)) $value = trim( $value );
			$szErrorMessage = $error_message;
			$error = false;

			if( $required === true )
			{
				if( empty( $value ) && $value !== "0" && $value !== 0 )
				{
					if( ( $validation != __VLD_CASE_BOOL__ || $validation != __VLD_CASE_STRICTBOOL__ ) && $value !== false )
					{
						$error = true;
						$this->addError( $error_field, $szErrorMessage . " is required." );
						return false;
					}
				}
			}

			if( !empty( $value ) )
			{
				switch( $validation )
				{
					case "NUMERIC":
						if( ( !is_numeric( $value )) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be only numbers." );
							return false;
						}
						elseif( $min_length !== false || $max_length !== false )
						{
						
							$numericDollarArray=array('Deposit Amount');
							if( $min_length !== false && $value  < $min_length )
							{
								if(in_array($szErrorMessage,$numericDollarArray))
								{
									$min_length="$".$min_length;
								}
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be " . $min_length . " or more." );
								return false;
							}
							if( $max_length !== false && $value > $max_length )
							{
								if(in_array($szErrorMessage,$numericDollarArray))
								{
									$max_length="$".$max_length;
								}
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be no more than " . $max_length . "." );
								return false;
							}
						}
						break;
					case "DIGITS":
						if(!preg_match("/^[0-9]*$/",$value))
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be only digits." );
							return false;
						}
						elseif( $min_length !== false || $max_length !== false )
						{
							if( $min_length !== false && strlen( (string)$value ) < $min_length )
							{
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be " . $min_length . " digits long." );
								return false;
							}
							if( $max_length !== false && strlen( (string)$value ) > $max_length )
							{
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must not be long more than " . $max_length . " digits." );
								return false;
							}
						}
						break;
					case "CARD":
						if( ( !is_numeric( $value ) && $required === true ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be only numbers." );
							return false;
						}
						elseif( $min_length !== false || $max_length !== false )
						{
							if( $min_length !== false && strlen( (string)$value ) < $min_length )
							{
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be " . $min_length . " digits" );
								return false;
							}
							if( $max_length !== false && strlen( (string)$value ) > $max_length )
							{
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be no more than " . $max_length . " digits." );
								return false;
							}
						}
						break;
					case "ALPHA":
						if( !preg_match( "/^[a-z]+$/i", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be only letters." );
							return false;
						}
						break;
					case "ALPHANUMERIC":
						if( !preg_match( "/^[a-z0-9\-\_]+$/i", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be only letters and numbers." );
							return false;
						}
						break;
					case "URI":
						if( !preg_match( "/^[a-z0-9\-\_\.]+$/i", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be only letters and numbers." );
							return false;
						}
						break;
					case "EMAIL":
						if( !preg_match( "/^[_a-z0-9-\+\$\!\%\=\&\^]+(\.[_a-z0-9-\+\$\!\%\=\&\^]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z0-9]{2,4})$/i", $value ) )
						{
							$error = true;
							$this->addError( $error_field, "Doesn't look like a valid email." );
							return false;
						}
						break;
					case "BOOL":
						if( !is_bool( $value ) && !( $value != "true" || $value != "false" ) && !( $value != "1" || $value != "0" ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be boolean value.");
							return false;
						}
						break;
					case "STRICTBOOL":
						if( !is_bool( $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be boolean value." );
							return false;
						}
						break;
					case "ADDRESS":
					case "NAME":
						if( !preg_match( "/^[a-z0-9\,\.\#\-\_\s\']+$/i", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " can only be letters, numbers, spaces, underscores, dashes, periods, commas, and pound signs." );
							return false;
						}
						break;
					case "URL":
						if( !preg_match( "/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be a valid web address." );
							return false;
						}
						break;
					case "USERNAME":
						if( !preg_match( "/^[\S]+$/i", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " cannot contain any spaces or special characters." );
							return false;
						}
					break;
					case "PASSWORD":
						if( !preg_match( "/^[\S]+$/i", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " cannot contain any space." );
							return false;
						}
						else if(!preg_match('/[^a-zA-Z]+/', $value))
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must have at least one special character or number ." );
							return false;
						}
						elseif( $min_length !== false || $max_length !== false )
						{
							if( $min_length !== false && strlen( (string)$value ) < $min_length )
							{
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be at least $min_length characters in length.");
								return false;
							}
							if( $max_length !== false && strlen( (string)$value ) > $max_length )
							{
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be no more than $max_length characters in length." );
								return false;
							}
						}
						break;
					case "DATE":
                        if( !strtotime( $value ) || strtotime( $value ) == -1 )
						{
                            $error = true;
							$this->addError( $error_field, $szErrorMessage . " must be a valid date [ie. YYYY-MM-DD]." );
							return false;
						}
						/*else
                                        {
                                            $value = strtotime( $value );
                                        }*/
						break;
					case "PHONE":
						if( !preg_match( "/^\d{3}-\d{3}-\d{4}$/", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be a valid phone number." );
							return false;
						}
						break;
					case "MOBILE_PHONE":
						if(preg_match( "/^\+[1-9]{1}[0-9]{1,14}$/", $value ) )
						{
							if(strlen($value) < 12 || strlen($value) > 15)
							{
								$error = true;
								$this->addError( $error_field,  "Doesn't look like a valid mobile phone number");
								return false;
							}
						}
						else
						{
							$error = true;
							$this->addError( $error_field,  "Doesn't look like a valid mobile phone number");
							return false;
						}
						break;
					case "PHONE_2":
						if( !preg_match( "/^\d{10}$/", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " : enter 10 digit number." );
							return false;
						}                                                
						break;
					case "DOLLARS":
					case "MONEY_US":
						if( !preg_match( "/^[0-9]+(\.[0-9]{2})*$/", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " must be a valid money format. (Ex. 00.00 )" );
							return false;
						}
						break;
                    case "DECIMAL":
						if( !preg_match( "/^[0-9]+(\.[0-9]{2})*$/", $value ) )
						{
							$error = true;
							$this->addError( $error_field, $szErrorMessage . " -- format allowed =  00.00 , 22, 0.5" );
							return false;
						}
						break;
					case "CC_EXP":
						if( !preg_match( "/^[0-9]{2}[-][0-9]{2}$/", $value ) )
						{
							$error = true;
							$this->error = true;
							$this->addError( $error_field, $szErrorMessage . " must be MM/YY." );
							return false;
						}
						break;
                    case "DD_NON_0" :
                        if(!$value) {
                            $error = true;
                            $this->addError( $error_field, $szErrorMessage . " must be selected." );
							return false;
                        }
                        break;
                    case "WHOLE_NUM" :
                        if(!preg_match("/^[0-9]*$/",$value)) {
                            $error = true;
                            $this->addError( $error_field, $szErrorMessage . " must be a whole number." );
							return false;
                        }
                        else
                        {
                        	if($min_length != false && $value < $min_length)
                        	{
                        		$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be  " . $min_length . " or more." );
								return false;
                        	}
                        	if( $max_length !== false && $value > $max_length )
							{
								$error = true;
								$this->addError( $error_field, $szErrorMessage . " must be no more than " . $max_length . " ." );
								return false;
							}
                        }
                        break;
                    case "IMAGE" :
                        $allowed_type = array("image/pjpeg", "image/jpeg", "image/png", "image/bmp", "image/jpg", "image/gif");

                        if($required === true && $value['tmp_name'] == "")
                        {
                            $error = true;
                            $this->addError( $error_field, $szErrorMessage . " is required." );
							return false;
                        }
                        if(!in_array($value['type'], $allowed_type))
                        {
                            $error = true;
                            $this->addError( $error_field, $szErrorMessage . " file type not allowed." );
							return false;
                        }
                        if($value['error'])
                        {
                            $error = true;
                            $this->addError( $error_field, $szErrorMessage . " upload error. Try Again" );
							return false;
                        }
                        break;
                    case "FILE_NAME" :
						if(strpos($value,'"') !== false || strpos($value,'|') !== false ||strpos($value,'?') !== false ||strpos($value,"*") !== false || strpos($value,"<") !== false || strpos($value,">") !== false || strpos($value,':') !== false || strpos($value,'/') !== false || strpos($value,'\\') !== false)
						{
							$error = true;
							$this->addError('INVALIDFILENAME', $szErrorMessage . " can't contains (/\*?<>\"?|:)");
							return false;
						}
                    	break;
					case "ANYTHING":
						break;
					default:
						$error = true;
						$this->addError( "error", "Unknown validation type. I was sent this type: " . $validation );
						break;
				}
			}
			else
            {
                switch( $validation )
				{
                    case "DD_NON_0" :
                        if(!$value) {
                            $error = true;
                            $this->addError( $error_field, $szErrorMessage . " must be selected." );
							return false;
                        }
                        break;

    				default:
						break;
                }
            }
            
			if( $min_length !== false && ( $validation == "NUMERIC" || $validation == "WHOLE_NUM" ) &&  $value!="" )
			{
				if( $value < $min_length )
				{
					$error = true;
					$this->addError( $error_field, $szErrorMessage . " must be " . $min_length . " or more." );
					return false;
				}
			}
			
			if( $min_length !== false && ( $validation != "NUMERIC" && $validation != "WHOLE_NUM" ) && !empty( $value ) )
			{
				if( strlen( $value ) < $min_length )
				{
					$error = true;
					$this->addError( $error_field, $szErrorMessage . " must be at least " . $min_length . " characters in length." );
					return false;
				}
			}

			if( $max_length !== false && $validation != "NUMERIC" && $validation != "WHOLE_NUM" && !empty( $value ) )
			{
				if( strlen( $value ) > $max_length )
				{
					$error = true;
					$this->addError( $error_field, $szErrorMessage . " must not be longer than " . $max_length . " characters in length." );
					return false;
				}
			}

			if( $error === true )
			{
				$this->addError( "error", "Unknown error validating field. The field was: " . $error_field . ". Validation: " . $validation . ". Value: " . $value );
				return false;
			}
			else
			{
				//return str_replace( "\$", "\\\$", $value );
				return $value;
			}
		}

		
		function resetErrors()
		{
			$this->error = false;
			$this->arErrorMessages = array();
		}

		
		function formatError( $message, &$container )
		{
			$container .= str_replace( "{ERROR}", $message, __VLD_ERROR_DISPLAY__ );
		}

		function logError( $error_field, $message, $error_type, $class_name, $function, $line)
		{
			$class_name = strtolower(trim($class_name));
			$function = trim($function);
			$line = (int)$line;
			$message = trim($message);
			$error_field = trim($error_field);
			

			$find_ary = array("{TIME}", "{SEVERITY}", "{ERROR_TYPE}", "{FILE}", "{FUNCTION}", "{LINE}", "{ERROR}");
			$replace_ary = array(date(__LOG_DATE_FORMAT__, time()), strtoupper($error_severity), $error_type, $class_name, $function, $line, $message);
			$error_string = str_replace($find_ary, $replace_ary, __LOG_LINE_FORMAT__);

			$log_file = __APP_PATH_LOGS__."/critical.log";

			$handle = fopen($log_file, "a+");
			fwrite($handle, $error_string);
			fclose($handle);
		}
	}
?>

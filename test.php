<?php
	require_once('application/libraries/Twilio/Twilio.php');
	
	$SID = "ACf3cef6647f7f2674e5ea82cdc8b06191";
	$TOKEN = "c3b13eed7c4529bb91d81d1470854312";
	$NUMBER = "+12513339387";

	$client = new Services_Twilio($SID, $TOKEN);
	$to = "+919718238798";
	$sms = "Hello";
	$media = "http://52.200.157.158/tally/assets/images/logo.png";
    
    try 
    {
        $send = $client->account->messages->sendMessage($NUMBER, $to, $sms, $media);
        echo "Done";   
    }
    catch (Exception $e) 
    {
		echo $e->getMessage();
    }
    
?>
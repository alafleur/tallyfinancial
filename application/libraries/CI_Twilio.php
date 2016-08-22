<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Twilio {
    public function CI_Twilio() {
        require_once('Twilio/Twilio.php');
    }
}
?>
<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facebook extends App_Controller
{
    public function leadads()
    {
        $challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];

        if ($verify_token === 'abc123') {
            echo $challenge;
        }
    }
}
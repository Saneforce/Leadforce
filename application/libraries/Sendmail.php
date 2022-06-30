<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Sendmail
{

	public function load()
	{
		$mail = new PHPMailer(true);
		return $mail;
	}

}
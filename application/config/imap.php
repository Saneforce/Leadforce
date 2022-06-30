<?php
defined('BASEPATH') || exit('No direct script access allowed');

$config['encrypto'] = 'tls';
$config['validate'] = true;
$config['host']     = 'smtp.gmail.com';
$config['port']     = 587;
$config['username'] = 'ttstestsmtp@gmail.com';
$config['password'] = 'ttsTest_123';

$config['folders'] = [
	'inbox'  => 'INBOX',
	'sent'   => 'Sent',
	'trash'  => 'Trash',
	'spam'   => 'Spam',
	'drafts' => 'Drafts',
];

$config['expunge_on_disconnect'] = false;

$config['cache'] = [
	'active'     => false,
	'adapter'    => 'file',
	'backup'     => 'file',
	'key_prefix' => 'imap:',
	'ttl'        => 60*60*24,
];

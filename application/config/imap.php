<?php
defined('BASEPATH') || exit('No direct script access allowed');

$config['encrypto'] = 'ssl';
$config['validate'] = true;
$config['host']     = 'imap.zoho.com:993/imap/ssl/novalidate-cert';
$config['username'] = 'closing.sourcepoint@direct2title.com';
$config['password'] = 'May@2020!';

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
	'ttl'        => 60,
];

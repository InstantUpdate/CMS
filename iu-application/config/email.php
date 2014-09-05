<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* EMAIL SENDING CONFIGURATION */

//set mail sending protocol (mail, sendmail or smtp)
$config['protocol'] = 'mail';
//sendmail path (applies only if you are using sendmail protocol)
$config['mailpath'] = '/usr/sbin/sendmail';
//smtp settings (applies only if you are using smtp protocol)
$config['smtp_host'] = '';
$config['smtp_port'] = 25;
$config['smtp_user'] = '';
$config['smtp_pass'] = '';

/* DO NOT CHANGE LINES BELOW UNLESS YOU KNOW WHAT ARE YOU DOING */

$config['wordwrap'] = FALSE;
$config['crlf'] = $config['newline'] = "\r\n";
$config['useragent'] = "CubeScripts Media / Real Estate Script";
$config['mailtype'] = 'html';

/* End of file email.php */
/* Location: ./application/config/email.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* SET UP YOUR MYSQL ACCESS DATA HERE */

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = '';
$db['default']['password'] = '';
$db['default']['database'] = '';

/* DO NOT CHANGE LINES BELOW UNLESS YOU KNOW WHAT ARE YOU DOING */

$db['default']['dbdriver'] = extension_loaded('mysqli') ? 'mysqli' : 'mysql';
$db['default']['dbprefix'] = 'iu4_';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = FALSE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_unicode_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

$active_group = 'default';
$active_record = TRUE;

/* End of file database.php */
/* Location: ./application/config/database.php */
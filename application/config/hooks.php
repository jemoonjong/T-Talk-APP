<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_controller'][] = array(
	'class' => 'Pear_hook',
	'function' => 'index',
	'filename' => 'pear_hook.php',
	'filepath' => 'hooks'
);

$hook['post_controller_constructor'][] = array(
    'class'     => 'Acl_hook',
    'function'  => 'checkPermission',
    'filename'  => 'acl_hook.php',
    'filepath'  => 'hooks'
);

$hook['pre_controller'][] = array(
	'class' => 'Samurai_hook',
	'function' => 'index',
	'filename' => 'samurai_hook.php',
	'filepath' => 'hooks'
);
/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
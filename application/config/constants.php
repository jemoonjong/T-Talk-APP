<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');



//Asset Helper
define('PACKED', FALSE);    // FALSE 지정시 압축 하지 않고 그냥 출력 합니다.
define('CHAR_SET', 'UTF-8');
define('PHP5', (PHP_VERSION >= 5));

define('DS', DIRECTORY_SEPARATOR);
define('IMG_ROOT', dirname(APPPATH).DS.'assets'.DS.'images'.DS);    // 이미지 절대경로
define('IMG_URL', '/assets/images/');    // 이미지 경로
define('CSS_ROOT', dirname(APPPATH).DS.'assets'.DS.'css'.DS);    // CSS 절대경로
define('CSS_URL', '/assets/css');    // CSS 경로
define('JS_ROOT', dirname(APPPATH).DS.'assets'.DS.'js'.DS);    // JS 절대경로
define('JS_URL', '/assets/js');    // JS 경로
define('CACHE_ASSET', dirname(APPPATH).DS.'assets'.DS.'cache'.DS.'asset'.DS);    // 압축된 CSS, JS 파일 경로
define('CACHE_URL', '/assets/cache/asset/');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
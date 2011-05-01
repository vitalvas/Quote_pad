<?php
if (!defined('PROTECT')) { header('Location: ../'); exit(); };

if (isset($_SERVER['HTTP_REFERER'])) $ref = $_SERVER['HTTP_REFERER']; else 	$ref = './';

if (isset($_COOKIE['user_login']) && isset($_COOKIE['user_passw']) && $none_auth == 'none') {
	
	setcookie('user_login', '', time()-86400);
	setcookie('user_passw', '', time()-86400);
	header('Location: '.$ref);
	
} else {
	header('Location: '.$ref);
}










?>
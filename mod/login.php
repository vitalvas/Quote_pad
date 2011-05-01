<?php
if (!defined('PROTECT')) { header('Location: ../'); exit(); };
$tpl_login = load_tpl('login');
if(isset($_COOKIE['user_login']) && isset($_COOKIE['user_passw'])) {
	$user_login = $_COOKIE['user_login'];
	$user_passw = $_COOKIE['user_passw'];
	$row = sql_array("SELECT COUNT(id) AS name FROM users WHERE sha1(username)='$user_login'");
	$check = $row['name'];
	if ($check == '1') {
		$row = sql_array("SELECT COUNT(id) AS name FROM users WHERE sha1(password)=sha1('$user_passw')");
		$check = $row['name'];
		if ($check == '1') {
			header('Location: ./?do=users&act=panel');
		} else {
			$main_to_login = 'go';
		}
	} else {
		$main_to_login = 'go';
	}
} else {
	$main_to_login = 'go';
}
if (isset($_POST['login']) && isset($_POST['pass']) && $main_to_login == 'go') {
	$user_login = $_POST['login'];
	$user_pass = $_POST['pass'];
	$user_login_sha = sha1($user_login);
	$user_pass_cr = sha1(sha1($user_pass));
	$row = sql_array("SELECT COUNT(id) AS name FROM users WHERE sha1(username)='$user_login_sha' and state='enabled'");
	$check = $row['name'];
	if ($check !='1') {
		$login_error = '<tr><td class="login_form_errors">{lang-login-error-login-pass}</td></tr>';
	} else {
		$row = sql_array("SELECT COUNT(id) AS name FROM users WHERE password='$user_pass_cr' AND sha1(username)='$user_login_sha'");
		$check = $row['name'];
		if ($check != '1') {
			$login_error = '<tr><td class="login_form_errors">{lang-login-error-login-pass}</td></tr>';
		} else {
			$row = sql_array("SELECT password FROM users WHERE sha1(username)='$user_login_sha'");
			$pwd = $row['password'];
			if ($user_pass_cr == $pwd) {
				setcookie('user_login', $user_login_sha, time()+86400);
				setcookie('user_passw', $user_pass_cr, time()+86400);
				header('Location: ./');
			} else {
				$login_error = '<tr><td class="login_form_errors">{lang-login-error-login-pass}</td></tr>';
			}
		}
	}	
}
if (!isset($login_error)) $login_error = '';
$content = str_replace("{login-form}", '<div class="inform_user">{lang-login-form}<div>', $tpl_login);
$content = str_replace("{login-errors}", $login_error, $content);
?>

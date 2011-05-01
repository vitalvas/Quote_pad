<?php
if (!defined('PROTECT')) { header('Location: ../'); exit(); };
if (!isset($_POST['user']) && !isset($_POST['fio']) && !isset($_POST['pass']) && !isset($_POST['pass2']) &&	!isset($_POST['email'])) {
	$tpl_register = load_tpl('register');
} elseif (empty($_POST['user'])) {
	$tpl_register = load_tpl('register');
	$reg_error = "{lang-register-error-username}";
} elseif (empty($_POST['pass'])) {
	$tpl_register = load_tpl('register');
	$reg_error = "{lang-register-error-pass}";
} elseif (empty($_POST['pass2'])) {
	$tpl_register = load_tpl('register');
	$reg_error = "{lang-register-error-pass2}";
} elseif (empty($_POST['email'])) {
	$tpl_register = load_tpl('register');
	$reg_error = "{lang-register-error-email}";
} elseif (isset($_POST['user']) && isset($_POST['fio']) && isset($_POST['pass']) && isset($_POST['pass2']) && isset($_POST['email'])) {
	$reg_username = $_POST['user'];
	$reg_fio = $_POST['fio'];
	$reg_pass = $_POST['pass'];
	$reg_pass2 = $_POST['pass2'];
	$reg_email = $_POST['email'];
	$reg_pass_sha = sha1(sha1($reg_pass));
	$row = sql_array("SELECT COUNT(id) AS name FROM users WHERE username='$reg_username'");
	if ($row['name'] == '1') {
		$tpl_register = load_tpl('register');
		$reg_error = "{lang-register-error-user-is}";
	} else {
		if (preg_match("/^(?:[a-z0-9]+(?:[-_]?[a-z0-9]+)?@[a-z0-9]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i",trim($reg_email))) {
			$reg_email = addslashes(htmlspecialchars(trim($reg_email)));
			$row = sql_array("SELECT COUNT(id) AS name FROM users WHERE email='$reg_email'");
			$check_mail = $row['name'];
			if ($check_mail == '1') {
				$tpl_register = load_tpl('register');
				$reg_error = "{lang-register-error-email-use}";
			} else { 
				if ($reg_pass == $reg_pass2) {
					$tpl_register = load_tpl('content');
					$reg_username = addslashes(htmlspecialchars(trim($reg_username)));
					$reg_fio = addslashes(htmlspecialchars(trim($reg_fio)));
					$reg_email = addslashes(htmlspecialchars(trim($reg_email)));
					$query = mysql_query("INSERT INTO users (username,password,fio,email) VALUES ('$reg_username','$reg_pass_sha','$reg_fio','$reg_email')");
					if ($query) {
						$reg_ok = '{lang-register-ok}';
					} else {
						$reg_ok = '{lang-login-error-login-pass}';
					}
					$tpl_register = str_replace("{content-tpl}", $reg_ok, $tpl_register);
				} else {
					$tpl_register = load_tpl('register');
					$reg_error = "{lang-register-error-pass-check}";
				}
			}
		} else {
			$tpl_register = load_tpl('register');
			$reg_error = "{lang-register-error-email-check}";
		}
	}
}
if (!isset($reg_error)) $reg_error = ''; else $reg_error = "<tr><td colspan='2' class='register_form_errors'>$reg_error</td></tr>";
$content = str_replace("{register-errors}", $reg_error, $tpl_register);
$content = str_replace("{register-form}", "<div class='inform_user'>{lang-form-register}</div>", $content);
?>
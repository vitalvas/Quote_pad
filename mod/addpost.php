<?php
if (!defined('PROTECT')) { header('Location: ../'); exit(); };
$tpl_content = load_tpl('content');
if (!isset($_POST['text'])) {
	if ($none_auth == 'go') {
	$tpl_addpost = load_tpl('addpost1');
	} else {
	$tpl_addpost = load_tpl('addpost');
	}
	if ($none_auth == 'none') {
		$guest_mess = '';
		$guest_check_img = '';
	} elseif ($none_auth == 'go') {
		$guest_mess = '{lang-add-post-non-auth}';
		$rand = rand(1, 10);
		$rand2 = rand(1, 10);
		$randa = array('+','-','*');
		$randk = rand(0, 2);
		$randl = $randa["$randk"];
		$sum = "$rand $randl $rand2";
		if ($randk == '0') { $hash = $rand+$rand2; } elseif ($randk == '1') { $hash = $rand-$rand2; } elseif ($randk == '2') { $hash = $rand*$rand2; }
		$hash = md5($hash);
		$guest_check_img = "$sum = <input type='text' name='img'><input type='hidden' name='hash' value='$hash'>";
	}
	$out = str_replace("{add-post-guest-message}", $guest_mess, $tpl_addpost);
	$out = str_replace("{guest-check-image}", $guest_check_img, $out);
	$out = str_replace("{textarea-text}", '', $out);
} elseif (isset($_POST['text']) && isset($_POST['hash']) && $none_auth == 'go') {
	$add_text = $_POST['text'];
	$hash = $_POST['hash'];
	$user_hash = $_POST['img'];
	if (md5($user_hash) != $hash) {
		$tpl_addpost = load_tpl('addpost');
		if ($none_auth == 'none') {
			$guest_mess = '';
			$guest_check_img = '';
		} elseif ($none_auth == 'go') {
			$guest_mess = '{lang-add-post-non-auth}';
			$rand = rand(1, 10);
			$rand2 = rand(1, 10);
			$randa = array('+','-','*');
			$randk = rand(0, 2);
			$randl = $randa["$randk"];
			$sum = "$rand $randl $rand2";
			if ($randk == '0') { $hash = $rand+$rand2; } elseif ($randk == '1') { $hash = $rand-$rand2; } elseif ($randk == '2') { $hash = $rand*$rand2; }
			$hash = md5($hash);
			$guest_check_img = "$sum = <input type='text' name='img'>&nbsp;<b><font color='red'>!</font></b><input type='hidden' name='hash' value='$hash'>";
		}
		$out = str_replace("{add-post-guest-message}", $guest_mess, $tpl_addpost);
		$out = str_replace("{guest-check-image}", $guest_check_img, $out);
		$out = str_replace("{textarea-text}", $add_text, $out);
	} else {
		if ($add_text != '') {
			$add_text = trim($add_text);
			$add_text = addslashes(htmlspecialchars($add_text));
			$add_text = nl2br($add_text);
			$query = mysql_query("INSERT INTO posts (text,author) VALUES ('$add_text','0')");
			if ($query) {
				$out = "{lang-add-post-ok}";
			} else {
				$out = "{lang-add-post-error}";
			}
		} else {
			$out = "{lang-add-post-error}";
		}
	}
} elseif (isset($_POST['text']) && $none_auth == 'none') {
	$add_text = $_POST['text'];
	if ($add_text != '') {
		$add_text = trim($add_text);
		$add_text = addslashes(htmlspecialchars($add_text));
		$add_text = nl2br($add_text);
		$query = mysql_query("INSERT INTO posts (text,author) VALUES ('$add_text','$pub_user_id')");
		if ($query) {
			$out = "{lang-add-post-ok}";
		} else {
			$out = "{lang-add-post-error}";
		}
	} else {
		$out = "{lang-add-post-error}";
	}
} else {
	header('Location: ./');
}
$content = str_replace("{content-tpl}", $out, $tpl_content);
?>
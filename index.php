<?php
define('PROTECT', true);
if (file_exists('config.php')) {
	require_once('config.php');
} else {
	if (file_exists('install.php'))
		header('Location: ./install.php');
	else {
		echo 'Error load "config.php"';
		exit();
	}
};
if ($debug) error_reporting(E_ALL);
$dbh = mysql_connect($db_host,$db_user,$db_pass) or  die('No connected : '.mysql_error());
mysql_select_db($db_name,$dbh) or die("Can't select db : ".mysql_error());
mysql_query("SET NAMES cp1251");
function load_tpl($name) {
	if (file_exists("tpl/$name.tpl")) 
		return file_get_contents("tpl/$name.tpl");
	else {
		echo "Error load $name.tpl";
		exit();
	}
}
function sql_array($name) {
	$query = mysql_query($name);
	if (!$query) 
		return false;
	else {
		$row = mysql_fetch_array($query);
		if (!$row)
			return false;
		else 
			return $row;
	}
}
$tpl_main = load_tpl('main');
$tpl_content = load_tpl('content');
//-------SHAPKA---------------------------
$row = sql_array("SELECT COUNT(id) AS num FROM quotes");
$quote_count = $row['num'];
if ($quote_count > '0') {
    $row = sql_array("SELECT text,id FROM quotes WHERE status = 'enabled' AND count <= (SELECT MIN(count) FROM quotes) ORDER BY RAND() LIMIT 1");
    $quote = $row['text'];
    $quote_id = $row['id'];
    $rowq = mysql_query("UPDATE quotes SET count=count+1 WHERE id='$quote_id'");
};
$row = sql_array("SELECT COUNT(id) AS num FROM users");
$users_count = $row['num'];
$row = sql_array("SELECT COUNT(id) AS num FROM posts");
$posts_count = $row['num'];
//----------LOAD--CONFIG-----------------
$row = sql_array("SELECT value AS name FROM config WHERE ckey='site_name'");
$site_name = $row['name'];
$row = sql_array("SELECT value AS name FROM config WHERE ckey='site_lang'");
$site_lang = $row['name'];
//-------------------COUNT--POSTS--------------------
if ($posts_count > '0') {
    $row = sql_array("SELECT COUNT(id) AS num FROM posts WHERE status='enabled'");
    $posts_enabled = $row['num'];
    $row = sql_array("SELECT COUNT(id) AS num FROM posts WHERE status='deleted'");
    $posts_deleted = $row['num'];
} else {
    $posts_enabled = '0';
    $posts_deleted = '0';
};
//---------------CHECK--USER--------------------
if (isset($_COOKIE['user_login']) && isset($_COOKIE['user_passw'])) {
	$user_login = $_COOKIE['user_login'];
	$user_passw = $_COOKIE['user_passw'];
	$row = sql_array("SELECT COUNT(id) AS name FROM users WHERE sha1(username)='$user_login'");
	$check = $row['name'];
	if ($check == '1') {
		$row = sql_array("SELECT password FROM users WHERE sha1(username)='$user_login'");
		$check_pass = $row['password'];
		if ($check_pass == $user_passw) {
			$row = sql_array("SELECT id,username,prem FROM users WHERE sha1(username)='$user_login'");
			$pub_user_name = $row['username'];
			$pub_user_id = $row['id'];
			$pub_user_prem = $row['prem'];
			$none_auth = 'none';
		} else {
			$none_auth = 'go';
			$pub_user_prem = 'user';
		}
	} else {
		$none_auth = 'go';
		$pub_user_prem = 'user';
	}
} else {
	$none_auth = 'go';
	$pub_user_prem = 'user';
}
//-----------------------------------------------
if (isset($_GET['do'])) $do = $_GET['do']; else $do = 'NULL';
if (!isset($content)) $content = '';
$site_title = $site_name;
switch ($do) {
    case 'rating':
		if (file_exists('mod/rating.php')) 
			require_once('mod/rating.php');
		else
			$content = '<br><b>Error load module rating.php in directory "mod"</b>';
		break;
    case 'users':
		if (file_exists('mod/users.php'))
        	require_once('mod/users.php');
		else 
			$content = '<br><b>Error load module users.php in directory "mod"</b>';
		break;
	case 'login':
		if (file_exists('mod/login.php'))
			require_once('mod/login.php');
		else
			$content = '<br><b>Error load module login.php in directory "mod"</b>';
		break;
	case 'addpost':
		if (file_exists('mod/addpost.php'))
			require_once('mod/addpost.php');
		else
			$content = '<br><b>Error load module addpost.php in directory "mod"</b>';
		break;
	case 'logout':
		if (file_exists('mod/logout.php'))
			require_once('mod/logout.php');
		else
			$content = '<br><b>Error load module logout.php in directory "mod"</b>';
		break;
	case 'register':
		if (file_exists('mod/register.php'))
			require_once('mod/register.php');
		else
			$content = '<br><b>Error load module register.php in directory "mod"</b>';
		break;
	case 'admin':
		if (file_exists('mod/admin.php'))
			require_once('mod/admin.php');
		else
			$content = '<br><b>Error load module admin.php in directory "mod"</b>';
		break;
	default:
		if (file_exists('mod/posts.php'))
			require_once('mod/posts.php');
		else 
			$content = '<br><b>Error load module posts.php in directory "mod"</b>';
		break;
};
if ($none_auth == 'go') {
	$userpanel = load_tpl('userpanel_in');
} elseif ($none_auth == 'none') {
	$userpanel = load_tpl('userpanel_auth');
	if ($pub_user_prem == 'admin') {
		$userpanel .= "&nbsp;[<a href='/?do=admin'>{lang-adminka}</a>]";
	}
}
if (!isset($posts_count)) $posts_count = '0';
if (!isset($users_count)) $users_count = '0';
if (!isset($quote)) $quote = '&nbsp;';
if (!isset($content)) $content = '';
if (!isset($pub_user_name)) $pub_user_name = '';
$out = str_replace("{title}", $site_title, $tpl_main);
$out = str_replace("{title-site}", $site_name, $out);
$out = str_replace("{content}", $content, $out);
$out = str_replace("{userpanel}", $userpanel, $out);
$out = str_replace("{username}", $pub_user_name, $out);
$out = str_replace("{posts-count}", $posts_count, $out);
$out = str_replace("{posts-enabled}", $posts_enabled, $out);
$out = str_replace("{posts-deleted}", $posts_deleted, $out);
$out = str_replace("{users-count}", $users_count, $out);
$out = str_replace("{random-quotes}", $quote, $out);
$out = str_replace("\n", '', $out);
$query = mysql_query("SELECT langkey,langvalue FROM lang WHERE langname='$site_lang' ORDER BY langkey");
while ($row = mysql_fetch_array($query)){
	$langkey = $row['langkey'];
	$out = str_replace("{lang-$langkey}", $row['langvalue'], $out);
}
echo $out;
?>
<?php
if (!defined('PROTECT')) { header('Location: ../'); exit(); };
if ($pub_user_prem == 'admin') {
	$tpl = load_tpl('content');

	$up = "<div align='center'>"
		  ."[<a href='./?do=admin&act=users'>{lang-admin-users}</a>]&nbsp;"
		  ."[<a href='./?do=admin&act=posts'>{lang-admin-posts}</a>]&nbsp;"
		  ."[<a href='./?do=admin&act=general'>{lang-admin-general}</a>]&nbsp;"
		  ."</div>";
	$content = str_replace("{content-tpl}", $up, $tpl);
	if (isset($_GET['act'])) $act = $_GET['act']; else $act = '';
	if (isset($_GET['id'])) $goid = $_GET['id']; else $goid = '';
	if (isset($_GET['a'])) $as = $_GET['a']; else $as = '';
	if ($act == 'users' && !$goid) {
		$content .= $tpl;
		$out = "<table width=100% class='user_list'><tr align='center'>"
		."<td class='user_list'>ID</td><td class='user_list'>{lang-admin-username}</td>"
		."<td class='user_list'>{lang-admin-fio}</td><td class='user_list'>{lang-admin-email}</td>"
		."<td class='user_list'>{lang-admin-posts}</td>"
		."<td class='user_list'>{lang-admin-rating}</td><td class='user_list'>{lang-admin-prem}</td></tr>";
		$nums = '50';
		if ($nums < $users_count) {
			if (isset($_GET['page'])) $page = intval($_GET['page']); else $page = '1';
			$pages = ceil($users_count/$nums);
			if ($page < '1') $page = '1'; elseif ($page > $pages) $page = $pages;
			$start = ($page-1)*$nums;
			if ($start < '0') $start = '0';
			$sql = mysql_query("SELECT id,username,fio,email,prem,state FROM users ORDER BY id LIMIT {$start}, {$nums}");
			$pages_open = 'yes';
		} else {
			$sql = mysql_query("SELECT id,username,fio,email,prem,state FROM users ORDER BY id");
			$pages_open = 'no';
			$page_list = '';
		}
		while ($row = mysql_fetch_array($sql)) {
			$id = $row['id'];
			$username = $row['username'];
			$fio = $row['fio'];
			$email = $row['email'];
			$state = $row['state'];
			if ($state == 'disabled') {
				$color = "bgcolor='#FFF99'"; 
			 } else {
				$color = '';
			}
			if ($row['prem'] == 'admin') $prem = "{lang-admin-admin}"; else $prem = "{lang-admin-user}";
			$row1 = sql_array("SELECT COUNT(id) AS name FROM posts WHERE author='$id' AND status='enabled'");
			$posts = $row1['name'];
			$row2 = sql_array("SELECT SUM(rating) AS sum FROM posts WHERE author='$id' AND status='enabled' LIMIT 1");
			$sum = $row2['sum'];
			if ($posts == '0') {
				$rating = '0';
			} else {
				$rating = $sum/$posts;
				$rating = sprintf("%.1f",$rating);
			}
			$out .= "<tr $color><td class='user_list' align='center'><a href='./?do=admin&act=users&id=$id'>$id</a></td>"
			."<td class='user_list'>$username</td><td class='user_list'>$fio</td><td class='user_list'>$email</td>"
			."<td class='user_list' align='center'>$posts</td>"
			."<td class='user_list' align='center'>$rating</td><td class='user_list' align='center'>$prem</td></tr>";
		}
		$out .= "<table>";
		if ($pages_open == 'yes') {
			$neigtbours = '5';
			if (!isset($page_list)) $page_list = '';
			$left_pagelist = $page - $neigtbours;
			if ($left_pagelist < '1') $left_pagelist = '1';
			$right_pagelist = $page + $neigtbours;
			if ($right_pagelist > $pages) $right_pagelist = $pages;
			if ($pages > '1') $page_list .= '[';
			if ($page > '1') {
				$page_1 = $page - '1';
				if ($page_1 == '1') $page_list .= "&nbsp;<a href='./?do=admin&act=users'><<</a>";
				else $page_list .= "&nbsp;<a href='./?do=admin&act=users&page=$page_1'><<</a>";
			}
			$page_2 = $page - $neigtbours;
			if ($page_2 > '1') $page_list .= "&nbsp;<a href='./?do=admin&act=users'>1</a>&nbsp;...";
			for ($i=$left_pagelist; $i<=$right_pagelist; $i++) {
				if ($i != $page) $page_list .= '&nbsp;<a href=./?do=admin&act=users&page='.$i.'>'.$i.'</a>';
				else $page_list .= "&nbsp;<b>$i</b>";
			}
			$page_2 = $page + $neigtbours;
			if ($pages > $page_2) $page_list .= "&nbsp;...&nbsp;<a href='./?do=admin&act=users&page=$pages'>$pages</a>";
			if ($page < $pages) {
				$page_1 = $page + '1';
				$page_list .= "&nbsp;<a href='./?do=admin&act=users&page=$page_1'>>></a>";
			}
			if ($pages > '1') $page_list .= '&nbsp;]';
		}
		$out .= "<div align='center'>$page_list</div>";
		$content = str_replace("{content-tpl}", $out, $content);
	} elseif ($act == 'users' && $goid) {
		if ($as == 'block') {
			$sql = mysql_query("UPDATE users SET state='disabled' WHERE id='$goid'");
			header('Location: ./?do=admin&act=users');
		} elseif ($as == 'unblock') {
			$sql = mysql_query("UPDATE users SET state='enabled' WHERE id='$goid'");
			header('Location: ./?do=admin&act=users');
		} elseif ($as == 'getuser') {
			$sql = mysql_query("UPDATE users SET prem='user' WHERE id='$goid'");
			header('Location: ./?do=admin&act=users');
		} elseif ($as == 'getadmin') {
			$sql = mysql_query("UPDATE users SET prem='admin' WHERE id='$goid'");
			header('Location: ./?do=admin&act=users');
		}
		$row = sql_array("SELECT id,username,fio,email,prem,state FROM users WHERE id='$goid'");
		$username = $row['username'];
		$fio = $row['fio'];
		$email = $row['email'];
		$prem = $row['prem'];
		$state = $row['state'];
		if ($state == 'disabled') {
			$sstate = "{lang-admin-blocketd}";
			$link = "<a href='./?do=admin&act=users&id=$goid&a=unblock'>{lang-admin-unblock}</a>";
		} elseif ($state == 'enabled') {
			$sstate = "{lang-admin-unblocketd}";
			$link = "<a href='./?do=admin&act=users&id=$goid&a=block'>{lang-admin-block}</a>";
		}
		if ($prem == 'admin') {
			$sprem = "{lang-admin-admin}";
			$slink = "<a href='./?do=admin&act=users&id=$goid&a=getuser'>{lang-admin-getuser}</a>";
		} elseif ($prem == 'user') {
			$sprem = "{lang-admin-user}";
			$slink = "<a href='./?do=admin&act=users&id=$goid&a=getadmin'>{lang-admin-getadmin}</a>";
		}
		$content .= $tpl;
		$out = "<br><table align='center' class='user_list' width='40%'>"
			  ."<tr><td class='user_list'>ID</td><td class='user_list'>$goid</td></tr>"
			  ."<tr><td class='user_list'>{lang-admin-username}</td><td class='user_list'>$username</td></tr>"
			  ."<tr><td class='user_list'>{lang-admin-fio}</td><td class='user_list'>$fio</td></tr>"
			  ."<tr><td class='user_list'>{lang-admin-email}</td><td class='user_list'>$email</td></tr>"
			  ."<tr><td></td><td></td></tr>"
			  ."<tr><td class='user_list'>$sstate</td><td class='user_list'>$link</td></tr>"
			  ."<tr><td class='user_list'>$sprem</td><td class='user_list'>$slink</td></tr>"
			  ."</table><br>";
		$content = str_replace("{content-tpl}", $out, $content);
	} elseif ($act == 'posts') {
		if ($as == 'deletepost') {
			$sql = mysql_query("UPDATE posts SET status='deleted' WHERE id='$goid'");
			header('Location: ./?do=admin&act=posts');
		}
		$out = "<table align='center' width='60%' class='user_list'><tr align='center'><td class='user_list'>ID</td>"
			   ."<td class='user_list'>{lang-admin-postdate}</td><td class='user_list'>{lang-admin-rating}</td>"
			   ."<td class='user_list'>{lang-admin-username}</td><td class='user_list'>{lang-admin-delete}</td></tr>";
		$rw = sql_array("SELECT COUNT(id) AS name FROM posts WHERE status='enabled'");
		$posts_count = $rw['name'];
		$nums = '50';
		if ($nums < $posts_count) {
			if (isset($_GET['page'])) $page = intval($_GET['page']); else $page = '1';
			$pages = ceil($posts_count/$nums);
			if ($page < '1') $page = '1'; elseif ($page > $pages) $page = $pages;
			$start = ($page-1)*$nums;
			if ($start < '0') $start = '0';
			$sql = mysql_query("SELECT id,postdate,rating,author FROM posts WHERE status='enabled' ORDER BY id DESC LIMIT {$start}, {$nums}");
			$pages_open = 'yes';
		} else {
			$sql = mysql_query("SELECT id,postdate,rating,author FROM posts WHERE status='enabled' ORDER BY id DESC");
			$pages_open = 'no';
			$page_list = '';
		}
		while ($row = mysql_fetch_array($sql)) {
			$id = $row['id'];
			$postdate = $row['postdate'];
			$rating = $row['rating'];
			$author = $row['author'];
			if ($author != '0') {
				$row1 = sql_array("SELECT username FROM users WHERE id='$author'");
				$username = $row1['username'];
				$username = "<a href='./?do=admin&act=users&id=$author'>$username<a>";
			} else {
				$username = "{lang-anonim}";
			}
			$out .= "<tr align='center' class='user_list'><td class='user_list'>$id</td><td class='user_list'>$postdate</td>"
				   ."<td class='user_list'>$rating</td><td class='user_list'>$username</td>"
			       ."<td class='user_list'><a href='./?do=admin&act=posts&id=$id&a=deletepost'>>>|<<</a></td></tr>";
		}
		$out .= "</table>";
		if ($pages_open == 'yes') {
			$neigtbours = '5';
			if (!isset($page_list)) $page_list = '';
			$left_pagelist = $page - $neigtbours;
			if ($left_pagelist < '1') $left_pagelist = '1';
			$right_pagelist = $page + $neigtbours;
			if ($right_pagelist > $pages) $right_pagelist = $pages;
			if ($pages > '1') $page_list .= '[';
			if ($page > '1') {
				$page_1 = $page - '1';
				if ($page_1 == '1') $page_list .= "&nbsp;<a href='./?do=admin&act=posts'><<</a>";
				else $page_list .= "&nbsp;<a href='./?do=admin&act=posts&page=$page_1'><<</a>";
			}
			$page_2 = $page - $neigtbours;
			if ($page_2 > '1') $page_list .= "&nbsp;<a href='./?do=admin&act=posts'>1</a>&nbsp;...";
			for ($i=$left_pagelist; $i<=$right_pagelist; $i++) {
				if ($i != $page) $page_list .= '&nbsp;<a href=./?do=admin&act=posts&page='.$i.'>'.$i.'</a>';
				else $page_list .= "&nbsp;<b>$i</b>";
			}
			$page_2 = $page + $neigtbours;
			if ($pages > $page_2) $page_list .= "&nbsp;...&nbsp;<a href='./?do=admin&act=posts&page=$pages'>$pages</a>";
			if ($page < $pages) {
				$page_1 = $page + '1';
				$page_list .= "&nbsp;<a href='./?do=admin&act=posts&page=$page_1'>>></a>";
			}
			if ($pages > '1') $page_list .= '&nbsp;]';
		}
		$out .= "<div align='center'>$page_list</div>";
		$content .= $tpl;
		$content = str_replace("{content-tpl}", $out, $content);
	} elseif ($act == 'general') {
		if (!isset($_POST['site_name']) && !isset($_POST['page_posts_num']) && !isset($_POST['page_posts_get_num']) && !isset($_POST['page_user_list'])) {
			$row = sql_array("SELECT value FROM config WHERE ckey='site_name'");
			$site_names = $row['value'];
			$row = sql_array("SELECT value FROM config WHERE ckey='page_posts_num'");
			$page_posts_num = $row['value'];
			$row = sql_array("SELECT value FROM config WHERE ckey='page_posts_get_num'");
			$page_posts_get_num = $row['value'];
			$row = sql_array("SELECT value FROM config WHERE ckey='page_user_list'");
			$page_user_list = $row['value'];
			$out = "<br><form action='./?do=admin&act=general' method='POST'>"
				  ."<table width='64%' class='user_list' align='center'>"
				  ."<tr><td class='user_list'>{lang-admin-site-name}:</td><td class='user_list'><input type='text' name='site_names' value='$site_names' size='41'></td></tr>"
				  ."<tr><td class='user_list'>{lang-admin-page-posts-num}:</td><td class='user_list'><input type='text' name='page_posts_num' value='$page_posts_num' size='41'></td></tr>"
				  ."<tr><td class='user_list'>{lang-admin-page-get-num}:</td><td class='user_list'><input type='text' name='page_posts_get_num' value='$page_posts_get_num' size='41'></td></tr>"
				  ."<tr><td class='user_list'>{lang-admin-user-list}:</td><td class='user_list'><input type='text' name='page_user_list' value='$page_user_list' size='41'></td></tr>"
				  ."<tr><td colspan='2' align='center' class='user_list'><input type='submit' value='{lang-submit}'></td></tr>"
				  ."</table></form><br>";
		} else {
			$site_names = $_POST['site_names'];
			$site_names = addslashes(htmlspecialchars(trim($site_names)));
			$page_posts_num = $_POST['page_posts_num'];
			$page_posts_num = (int) ($page_posts_num);
			$page_posts_get_num = $_POST['page_posts_get_num'];
			$page_posts_get_num = (int) ($page_posts_get_num);
			$page_user_list = $_POST['page_user_list'];
			$page_user_list = (int) ($page_user_list);
			$sql = mysql_query("UPDATE config SET value='$site_names' WHERE ckey='site_name'");
			$sql = mysql_query("UPDATE config SET value='$page_posts_num' WHERE ckey='page_posts_num'");
			$sql = mysql_query("UPDATE config SET value='$page_posts_get_num' WHERE ckey='page_posts_get_num'");
			$sql = mysql_query("UPDATE config SET value='$page_user_list' WHERE ckey='page_user_list'");
			header('Location: ./?do=admin&act=general');
		}
		$content .= $tpl;
		$content = str_replace("{content-tpl}", $out, $content);
	}
} else {
	header('Location: ./');
}
?>
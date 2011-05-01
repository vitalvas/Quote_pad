<?php
if (!defined('PROTECT')) { header('Location: ../'); exit(); };
if (!isset($_GET['id'])) $userid = ''; else { $userid = $_GET['id']; $userid = (int) ($userid); }
if (isset($_GET['act'])) { $act = $_GET['act']; } else { $act = ''; }
if ((!$userid && $act && $none_auth == 'none') or ($userid && $act && $none_auth == 'none')) { 
	if ($act == 'panel' && $pub_user_id) {
		$tpl_user_panel = load_tpl('userpanel');
		$sql = "SELECT users.id,users.username,users.fio,users.email,COUNT(posts.id) AS cp,SUM(posts.rating) AS sr
				FROM users
				LEFT OUTER JOIN posts ON users.id=posts.author
				WHERE users.id='$pub_user_id' LIMIT 1";
		$row = sql_array($sql);
		$count_posts_user= $row['cp'];
		$sum = $row['sr'];
		if ($count_posts_user == '0') $rating = '0';
		else {
			$rating = $sum/$count_posts_user;
			$rating = sprintf("%.1f",$rating);
		}
		$content = str_replace("{user-panel}", "{lang-user-panel}", $tpl_user_panel);
		$content = str_replace("{user-show-fio}", $row['fio'], $content);
		$content = str_replace("{user-show-username}", $row['username'], $content);
		$content = str_replace("{user-show-email}", $row['email'], $content);
		$content = str_replace("{user-show-posts}", $row['cp'], $content);
		$content = str_replace("{user-show-rating}", $rating, $content );
	} else {
		header('Location: ./');
	}
} elseif (!$userid && !$act) {
	$tpl_userlist = load_tpl('userlist');
	$content2 = '';
	$row1 = sql_array("SELECT value FROM config WHERE ckey='page_user_list'");
	$nums = $row1['value'];
	if ($nums < $users_count) {
		if (isset($_GET['page'])) $page = intval($_GET['page']); else $page = '1';
		$pages = ceil($users_count/$nums);
		if ($page < '1') $page = '1'; elseif ($page > $pages) $page = $pages;
		$start = ($page-1)*$nums;
		if ($start < '0') $start = '0';
		$query = mysql_query("SELECT id,username,fio FROM users ORDER BY username LIMIT {$start}, {$nums}");
		$pages_open = 'yes';
	} else {
		$query = mysql_query("SELECT id,username,fio FROM users ORDER BY username");
		$pages_open = '';
	}
	while ($row = mysql_fetch_array($query)) {
		$id = $row['id'];
		$fio = $row['fio'];
		$row1 = sql_array("SELECT COUNT(id) AS num FROM posts WHERE author='$id' AND status='enabled'");
		$count_posts_user = $row1['num'];
		$row2 = sql_array("SELECT SUM(rating) AS sum FROM posts WHERE author='$id' AND status='enabled' LIMIT 1");
		$sum = $row2['sum'];
		if ($count_posts_user == '0') {
			$rating = '0';
		} else {
			$rating = $sum/$count_posts_user;
			$rating = sprintf("%.1f",$rating);
		}
		$content1 = str_replace("{users-show}", '', $tpl_userlist);
		$content1 = str_replace("{users-show-id}", $id, $content1);
		$content1 = str_replace("{users-show-username}", $row['username'], $content1);
		$content1 = str_replace("{users-show-fio}", $fio, $content1);
		$content1 = str_replace("{users-show-rating}", $rating, $content1);
		$content2 .= str_replace("{users-show-posts}", $row1['num'], $content1);
	}
	$page_list = '';
	if ($pages_open == 'yes') {
		$neigtbours = '5';
		$left_pagelist = $page - $neigtbours;
		if ($left_pagelist < '1') $left_pagelist = '1';
		$right_pagelist = $page + $neigtbours;
		if ($right_pagelist > $pages) $right_pagelist = $pages;
		if ($pages > '1') $page_list .= '[';
		if ($page > '1') {
			$page_1 = $page - '1';
			if ($page_1 == '1') $page_list .= "&nbsp;<a href='/users'><<</a>";
			else $page_list .= "&nbsp;<a href='/?do=users&page=$page_1'><<</a>";
		}
		$page_2 = $page - $neigtbours;
		if ($page_2 > '1') $page_list .= "&nbsp;<a href='/?do=users'>1</a>&nbsp;...";
		for ($i=$left_pagelist; $i<=$right_pagelist; $i++) {
			if ($i != $page) $page_list .= '&nbsp;<a href=/?do=users&page='.$i.'>'.$i.'</a>';
			else $page_list .= "&nbsp;<b>$i</b>";
		}
		$page_2 = $page + $neigtbours;
		if ($pages > $page_2) $page_list .= "&nbsp;...&nbsp;<a href='/?do=users&page=$pages'>$pages</a>";
		if ($page < $pages) {
			$page_1 = $page + '1';
			$page_list .= "&nbsp;<a href='/?do=users&page=$page_1'>>></a>";
		}
		if ($pages > '1') $page_list .= '&nbsp;]';
	}
	$content .= '<div class="content"><table width=100% class="user_list"><tr align=center><td class="user_list">{lang-username}</td>'
	.'<td class="user_list">{lang-fio}</td><td class="user_list">{lang-posts}</td><td class="user_list">{lang-count-rating}</td>'
	.'<td class="user_list">{lang-detali}</td></tr>'.$content2.'</table><div align="center">'.$page_list.'</div></div>';
} elseif ($userid) { 
	$tpl_usershow = load_tpl('usershow');
	$sql = "SELECT users.id,users.username,users.fio,COUNT(posts.id) AS cp,SUM(posts.rating) AS sr
			FROM users
			LEFT OUTER JOIN posts ON users.id=posts.author
			WHERE users.id='$userid' LIMIT 1";
	$row = sql_array($sql);
	$fio = $row['fio'];
	$count_posts_user= $row['cp'];
	$sum = $row['sr'];
	if ($count_posts_user == '0') $rating = '0';
	else {
		$rating = $sum/$count_posts_user;
		$rating = sprintf("%.1f",$rating);
	}
	$content = str_replace("{users-show}", '<div class=inform_user>{lang-info-show-user}</div><br>', $tpl_usershow);
	$content = str_replace("{users-show-id}", $row['id'],$content);
	$content = str_replace("{users-show-username}", $row['username'],$content);
	$content = str_replace("{users-show-fio}", $fio, $content);
	$content = str_replace("{users-show-posts}", $row['cp'], $content);
	$content = str_replace("{users-show-rating}", $rating, $content);
} else {
	header('Location: ./');
}
?>
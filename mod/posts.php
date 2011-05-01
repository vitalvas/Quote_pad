<?php
if (!defined('PROTECT')) { header('Location: ../'); exit(); };
$tpl_postshow = load_tpl('postshow');
if ($posts_count > '0') {
    if (isset($_GET['postid'])) {
		$postid = $_GET['postid'];
		$postid = (int) $postid;
		$row = sql_array("SELECT COUNT(id) AS num FROM posts WHERE id='$postid' AND status='enabled'");
		$check = $row['num'];
		if ($check == '1') {
			$row = sql_array("SELECT id,postdate,text,rating,author FROM posts WHERE id='$postid'");
			$postdate = $row['postdate'];
			$text = $row['text'];
			$rating = $row['rating'];
			$author_id = $row['author'];
			$row1 = sql_array("SELECT COUNT(id) AS num FROM users WHERE id='$author_id'");
			$row1_check = $row1['num'];
			if ($row1_check == '1') {
				$row1 = sql_array("SELECT username FROM users WHERE id='$author_id'");
				$author = $row1['username'];
				$author = "<a href='/users/$author_id'>$author</a>";
			} else $author = "<a href='/users'>{lang-anonim}</a>";
			$site_title = "{lang-post-num}$postid | $site_name";
			$c_tpl = str_replace("{content-post}", $text, $tpl_postshow);
			$c_tpl = str_replace("{content-post-id}", $postid, $c_tpl);
			$c_tpl = str_replace("{content-post-rating}", $rating, $c_tpl);
			$c_tpl = str_replace("{content-post-date}", $postdate, $c_tpl);
			$c_tpl = str_replace("{content-post-author}", $author, $c_tpl);
			$content.=  $c_tpl;
		} else header('Location: ./');
	} elseif (isset($_GET['act'])) {
		$row = sql_array("SELECT value FROM config WHERE ckey='page_posts_get_num'");
		$posts_limit = $row['value'];
		$act = $_GET['act'];
		if ($act == 'best') {
			$sql = "rating DESC";
		} elseif ($act == 'bestusers') {
			$sql = "rating DESC";
			$sql1 = "AND author!='0'";
		} elseif ($act == 'rand') {
			$sql = "RAND()";
		} elseif ($act == 'abyss') {
			$sql = "rating ASC";
		} else {
			header('Location: ./');
			exit();
		}
		if (!isset($sql1)) $sql1 = '';
		$query = mysql_query("SELECT id,postdate,text,rating,author FROM posts WHERE status='enabled' $sql1 ORDER BY $sql LIMIT {$posts_limit}");
		while ($row = mysql_fetch_array($query)) {
			$id = $row['id'];
			$postdate = $row['postdate'];
			$text = $row['text'];
			$rating = $row['rating'];
			$author_id = $row['author'];
			$row1 = sql_array("SELECT COUNT(id) AS num FROM users WHERE id='$author_id'");
			$row1_check = $row1['num'];
			if ($row1_check == '1') {
				$row1 = sql_array("SELECT username FROM users WHERE id='$author_id'"); 
				$author = $row1['username'];
				$author = "<a href='/users/$author_id'>$author</a>";
			} else $author = "<a href='/users'>{lang-anonim}</a>";
			$c_tpl = str_replace("{content-post}", $text, $tpl_postshow);
			$c_tpl = str_replace("{content-post-id}", $id, $c_tpl);
			$c_tpl = str_replace("{content-post-rating}", $rating, $c_tpl);
			$c_tpl = str_replace("{content-post-date}", $postdate, $c_tpl);
			$c_tpl = str_replace("{content-post-author}", $author, $c_tpl);
			$content.=  $c_tpl;    
		}
	} else {
		$row = sql_array("SELECT value FROM config WHERE ckey='page_posts_num'");
		$page_nums = $row['value'];
		if (isset($_GET['page'])) $page = intval($_GET['page']); else $page = '1';
		$pages = ceil($posts_count/$page_nums);
		if ($pages > '1') { 
			if ($page < '1') { $page = '1'; } elseif ($page > $pages) { $page = $pages; }
			$page_start = ($page-1)*$page_nums;
			$neighbours = '5';
			$left_pagelist = $page - $neighbours;
			if ($left_pagelist < '1') $left_pagelist = '1';
			$right_pagelist = $page + $neighbours;
			if ($right_pagelist > $pages) $right_pagelist = $pages;
			$page_list = '<br>';
			if ($pages > '1') $page_list .= '[';
			if ($page > '1') {
				$page_1 = $page - '1';
				if ($page_1 == '1') {
					$page_list .= "&nbsp;<a href='/'><<</a>";
				} else {
					$page_list .= "&nbsp;<a href='/?page=$page_1'><<</a>";
				}
			}
			$page_2 = $page - $neighbours;
			if ($page_2 > '1') {
				$page_list .= "&nbsp;<a href='/'>1</a>&nbsp;...";
			}
			for ($i=$left_pagelist; $i<=$right_pagelist; $i++) {
				if ($i != $page) {
					$page_list .= '&nbsp;<a href=/?page='.$i.'>'.$i.'</a>';
				} else {
					$page_list .= "&nbsp;<b>$i</b>";
				}
			}
			$page_2 = $page + $neighbours;
			if ($pages > $page_2) {
				$page_list .= "&nbsp;...&nbsp;<a href='/?page=$pages'>$pages</a>";
			}
			if ($page < $pages) {
				$page_1 = $page + '1';
				$page_list .= "&nbsp;<a href='/?page=$page_1'>>></a>";
			}
			if ($pages > '1') $page_list .= '&nbsp;]';
			$query = mysql_query("SELECT id,postdate,text,rating,author FROM posts WHERE status='enabled' ORDER BY id DESC LIMIT {$page_start}, {$page_nums}");
		} else {
			$page_list = '';
			$query = mysql_query("SELECT id,postdate,text,rating,author FROM posts WHERE status='enabled' ORDER BY id DESC");
		}
		while ($row = mysql_fetch_array($query)) {
			$id = $row['id'];
			$postdate = $row['postdate'];
			$text = $row['text'];
			$rating = $row['rating'];
			$author_id = $row['author'];
			$row1 = sql_array("SELECT COUNT(id) AS num FROM users WHERE id='$author_id'");
			$row1_check = $row1['num'];
			if ($row1_check == '1') {
				$row1 = sql_array("SELECT username FROM users WHERE id='$author_id'"); 
				$author = $row1['username'];
				$author = "<a href='/users/$author_id'>$author</a>";
			} else $author = "<a href='/users'>{lang-anonim}</a>";
			$c_tpl = str_replace("{content-post}", $text, $tpl_postshow);
			$c_tpl = str_replace("{content-post-id}", $id, $c_tpl);
			$c_tpl = str_replace("{content-post-rating}", $rating, $c_tpl);
			$c_tpl = str_replace("{content-post-date}", $postdate, $c_tpl);
			$c_tpl = str_replace("{content-post-author}", $author, $c_tpl);
			$content.=  $c_tpl;
		};
		$content .= $page_list;
	};
};
?>
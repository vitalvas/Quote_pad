<?php
if (!defined('PROTECT')) { header('Location: ../'); exit(); };

if (isset($_GET['postid'])) {
    $postid = $_GET['postid'];
    $postid = (int) ($postid);
    if (isset($_SERVER['HTTP_REFERER'])) $ref = $_SERVER['HTTP_REFERER']; else 	$ref = './';
    if (isset($_COOKIE["vote_post_$postid"]) && $_COOKIE["vote_post_$postid"] == 'lock') {
		header('Location: '.$ref);
		exit();
    };
    $row = sql_array("SELECT COUNT(id) AS num FROM posts WHERE id='$postid' AND status='enabled'");
    $check = $row['num'];
    if ($check == '1') {
		if (isset($_GET['get'])) {
	    	$get = $_GET['get'];
	    	if ($get == 'up') {	
				$query = mysql_query("UPDATE posts SET rating=rating+1 WHERE id='$postid'");
				setcookie("vote_post_$postid","lock",time()+86400);
				header('Location: '.$ref);
				exit();
	    	} elseif ($get == 'down') {
				$row = sql_array("SELECT rating FROM posts WHERE id='$postid'");
				$rating = $row['rating'];
				if ($rating <= '-9') {
					$query = mysql_query("UPDATE posts SET status='deleted' WHERE id='$postid'");
					setcookie("vote_post_$postid","lock",time()+86400);    
					header('Location: '.$ref);
					exit();
				} else {
					$query = mysql_query("UPDATE posts SET rating=rating-1 WHERE id='$postid'");
					setcookie("vote_post_$postid","lock",time()+86400);
					header('Location: '.$ref);
					exit();
				};
			} else {
				header('Location: '.$ref);	
				exit();
			};
		} else {
			header('Location: '.$ref);
			exit();
		};   
	} else {
		header('Location: '.$ref);        
    };
} else {
    header('Location: ./');
};



?>
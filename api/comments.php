<?php 
//接收客户端的ajax请求 返回评论数据
	require '../functions.php';
	$page=empty($_GET['page'])?1:(int)$_GET['page'];
	$length=2;
	$offset=($page-1)*$length;
	$sql=sprintf('SELECT comments.*,
		posts.title as post_title 
		FROM comments
		INNER JOIN posts
		ON comments.post_id=posts.id
		ORDER BY comments.created DESC
		LIMIT %d,%d;', $offset, $length);
	$comments=xiu_fetch($sql);
	$total_count=xiu_fetch_one("SELECT count(1) as count FROM comments INNER JOIN posts ON comments.post_id=posts.id")['count'];
	$total_pages=ceil($total_count/$length);
	$json=json_encode(array(
		'comments'=>$comments,
		'total_pages'=>$total_pages
	));
	header('Content-Type:application/json');
	echo $json;
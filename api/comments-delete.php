<?php 
	//根据客户端传递过来的Id删除对应的数据
	require '../functions.php';
	// if (empty($_GET['id']){
	// 	exit('缺少必要参数');
	// }

	$id=$_GET['id'];
	$row=xiu_execute('DELETE FROM comments WHERE id in(' .$id. ');');
	header('Content-Type:application/json');
	echo json_encode($row>0);
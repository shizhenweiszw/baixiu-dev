<?php
	require '../functions.php';
	$id=$_GET['id'];
	xiu_execute('DELETE FROM posts WHERE id IN(' .$id. ')');

	header('Location:' . $_SERVER['HTTP_REFERER']);

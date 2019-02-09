<?php
	require_once '../functions.php';
	$id=$_GET['id'];
	xiu_execute('delete from users where id in('.$id.')');
	header('Location: users.php');
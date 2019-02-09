<?php
	require_once '../functions.php';
	$id=$_GET['id'];
	xiu_execute('delete from categories where id in('.$id.')');
	header('Location: categories.php');

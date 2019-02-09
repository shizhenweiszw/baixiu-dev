<?php
	require_once 'config.php';
	//封装公用函数
	session_start();

	//获取当前用户信息，如果为空则跳转到登陆界面
	function baixiu_get_current_user(){
		if (empty($_SESSION['current_login_user'])) {
			header('Location: /admin/login.php');
			exit();
		}
		return $_SESSION['current_login_user'];
	}

	//连接数据库
	function baixiu_mysql_connect($sql){
		$conn=mysqli_connect(DB_HOST,DB_USER,DB_USER,DB_NAME);
		if (!$conn) {
			exit('连接失败');
		}
    	$query=mysqli_query($conn,$sql);
    	if (!$query) {
    		exit('查询失败');
    	}
    	$arr['conn']=$conn;
    	$arr['query']=$query;
    	return $arr;
	}

	//获取数据库数据
	function xiu_fetch($sql){
		$query=baixiu_mysql_connect($sql)['query'];
		$result=array();
    	while ($row=mysqli_fetch_assoc($query)) {
    	  	$result[]=$row;
    	}  
    	return $result;	
	}
	//获取一条数据
	function xiu_fetch_one($sql){
		$res=xiu_fetch($sql);
		return isset($res[0])?$res[0]:'';
	}

	//添加数据
	function xiu_execute($sql){
		$conn=mysqli_connect(DB_HOST,DB_USER,DB_USER,DB_NAME);
		if (!$conn) {
			exit('连接失败');
		}
    	$query=mysqli_query($conn,$sql);
    	if (!$query) {
    		exit('查询失败');
    	}
		$affected_rows=mysqli_affected_rows($conn);
		return $affected_rows;
	}

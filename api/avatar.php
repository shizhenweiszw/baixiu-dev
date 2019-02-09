<?php 
	require '../config.php';
 	$email=$_GET['email'];
    $conn=mysqli_connect(DB_HOST,DB_USER,DB_USER,DB_NAME);
    $query=mysqli_query($conn,"SELECT avatar FROM users WHERE email='$email' LIMIT 1;");
    $avatar=mysqli_fetch_assoc($query);
    echo($avatar['avatar']);

<?php
session_start();
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "<script>window.alert('请先进行系统设置!')</script>";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}

if (($_SESSION['role']!='admin')and($_SESSION['role']!='diradmin')){	
	if(!$scheme)echo "您无权限进行此操作，请用管理员身份<a href='../user/loginfrm.php'>登录</a> ！";
//	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	if(!$scheme)exit;
}

?>

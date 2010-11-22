<?php
session_start();
include('../include/charset.php');
error_reporting(0);
/*
   文件名：monitor_modify.php
   功能：删除、修改svn监控。
   输入：
   输出：
   逻辑：
*/
include('../../../config.inc');
include('../include/dbconnect.php');
$id=$_GET['id'];
$action=$_GET['action'];
if(!is_numeric($id))
{
	echo "非法";
	exit;
}
if(empty($id))
{
	echo " <script>window.alert(\"输入信息不全!\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
  exit;
	
}
$user_id=$_SESSION['uid'];
if('del' == $action)
{
	$query="delete from monitor_user where user_id=$user_id and id=$id";
	$result=mysql_query($query);
	$num=mysql_affected_rows(); 
	if($num == 0)
	{
		echo "<script>alert('删除失败，只能删除自己的订阅!')</script>";
	}else
		echo "<script>alert('该订阅已删除!')</script>";
	echo " <script>setTimeout('document.location.href=\"svn_monitor.php\"',5)</script>";
}


<?php
session_start();
include('../include/charset.php');
  // error_reporting(0);
if (!isset($_SESSION['username'])){	
	echo "请先<a href='./loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"./loginfrm.php\"',0)</script>";  	
	exit;
}
if ($_SESSION['role'] !='admin')
{
	echo "您无权进行此操作！";
	exit;
}
include('../../../config.inc');
function safe($str)
{ 
	return "'".mysql_real_escape_string($str)."'";
}
$action= trim($_POST["action"]);
include('../include/dbconnect.php');
$userArray=$_POST["userArray"];
	$paras_array='';
	if(empty($userArray))
	{
	  echo " <script>window.alert(\"选择为空！\")</script>";
			echo " <script>setTimeout('document.location.href=\"javascript:history.back()\"',3)</script>";
			exit;	
	}			
	foreach($userArray as $value)
	{
		$value= safe($value);
		if(!empty($value))$paras_array[]= ' user_id='.$value;
	}
 
	$paras=implode(' or ',$paras_array);
	$sc=true;
	if($action == '重设有效期')
	{
		$expire=$_POST['expire'];
		if(is_numeric($expire)){
			//$expire=mktime(0, 0, 0, date("m")  , date("d")+$expire, date("Y"));
			$expire=date('Y-m-d' , strtotime("+$expire day"));
			$query="update svnauth_user set expire=\"$expire\" where $paras";
			mysql_query($query) or $sc=false;
		}
	}
	if($action == '到期不通知')
	{
		$query="update svnauth_user set infotimes=4 where $paras";
		mysql_query($query) or $sc=false;
	}
	if($sc)
	{
		echo "<p style='text-align:center;line-height:2;border:solid 1px;background:#ecf0e1;margin-top:200px;'><br>设置成功！<br>你可立刻<a href='./scheme.php'>运行清理计划</a>进行清理！";
		echo "<br>或者<a href=./cleanuser.php>返回继续操作</a>！<br></p>";
	}
?>

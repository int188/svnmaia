<?php
session_start();
include('../include/charset.php');
error_reporting(0);
/*
   文件名：sendmail.php
   功能：处理权限申请，依据url和规则发送邮件给对应管理员。
   输入：url、用户名、申请权限类型、申请说明
   输出：发送邮件
   逻辑：
*/
include('../../../config.inc');
include('../config/config.php');
include('../include/dbconnect.php');
foreach($_POST as $k=>$v)
{
	$v=htmlspecialchars($v,ENT_QUOTES);
	$_POST[$k]=mysql_real_escape_string($v);
}
$wurl=$_POST['wurl'];
if(empty($wurl))
{
	echo " <script>window.alert(\"输入信息不全!\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
  exit;
	
}
function safe($str)
{ 
	return "'".mysql_real_escape_string($str)."'";
}
//*******
//创建数据库
//*******
$createtb = "create table IF NOT EXISTS monitor_url(
		`monitor_id` INT(20) NOT NULL UNIQUE AUTO_INCREMENT, PRIMARY KEY (`monitor_id`),		
  `url` varchar(255) NOT NULL  UNIQUE, 
  `version` int(40) NOT NULL
		)ENGINE=MyISAM;";
mysql_query($createtb);
//echo mysql_error();
$createtb = "create table IF NOT EXISTS monitor_user(
	`id` INT NOT NULL AUTO_INCREMENT, 		
	`monitor_id` INT(20) NOT NULL , 		
   `user_id` int(11) NOT NULL, 
   `pattern` varchar(40),
   PRIMARY KEY (`id`),
   UNIQUE KEY (`user_id`,`monitor_id`)
		)ENGINE=MyISAM;";
mysql_query($createtb);
//echo mysql_error();
//***
//记录数据
//***
include('./geturl.php');
$ver=-1;
$wurl=geturl($wurl);
if(($ver < 0 )or(empty($ver)))
{
	echo " <script>window.alert(\"无法获取该url的版本信息，请确认输入是否正确!\")</script>";
	echo " <a href='javascript:history.back()'>点击这里返回</a>";
	echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
	exit;

}
#$monitor_id=md5($wurl);
$wurl=safe($wurl);
$query="insert into monitor_url set url=$wurl,version=$ver";
mysql_query($query);
$error=mysql_query();
echo $error;
$nameflag=true;
$u_ID=$_SESSION['uid'];
$_POST['pattern']=trim($_POST['pattern']);
$pattern=str_replace('*','.*',str_replace('.','\.',str_replace(' ','|',$_POST['pattern'])));	
$pattern=safe($pattern);
if (($_SESSION['role'] == 'admin')or($_SESSION['role'] == 'diradmin')){
	$usrArray=preg_split('/[;, ]/',$_POST['notelist']);
	foreach($usrArray as $i=>$e)
	{
		if(empty($e))continue;
		list($u,$ot)=explode('@',$e);
		$u=safe($u);
		$query="insert into monitor_user (pattern,monitor_id,user_id) select $pattern,monitor_url.monitor_id,svnauth_user.user_id from svnauth_user,monitor_url where svnauth_user.user_name=$u and monitor_url.url=$wurl;";
			//	echo $query;
		mysql_query($query);
		$error=mysql_error();
		$nameflag=false;
		if (mysql_affected_rows() > 0)
		{
			unset($usrArray[$i]);
			//更新svn邮箱地址~~
			if(strpos($e,'@'))
			{
				$e=safe($e);
				$query="update svnauth_user set email=$e where user_name=$u and email=''";
				mysql_query($query);
			}

		}else
		{
			echo $error;
			echo "<br><b>Error</b>: $u not found in svn username lists! 该用户订阅失败！ ";
		}

	}
	if($nameflag)
	{		
		$query="insert into monitor_user (pattern,monitor_id,user_id) select $pattern,monitor_url.monitor_id,$u_ID from monitor_url where monitor_url.url=$wurl;";
		mysql_query($query);
		$error=mysql_error();
	}
}else{
	$query="insert into monitor_user (pattern,monitor_id,user_id) select $pattern,monitor_url.monitor_id,$u_ID from monitor_url where monitor_url.url=$wurl;";
	mysql_query($query);
	$error=mysql_error();	
}
echo $error;
$pos=strpos($error,"Duplicate entry");
if($pos !== false)
{
	echo "<script>alert('该url监控已在，不能重复添加！')</script>";
	echo " <script>setTimeout('document.location.href=\"svn_monitor.php\"',5)</script>";
}
//***
//如果用户没有email信息，引导其填写email。
//***
$query="select email from svnauth_user where user_id=$u_ID and email=''";
$result=mysql_query($query);
$num_rows = mysql_num_rows($result);
if($num_rows > 0)
{
	echo "<br>订阅成功！";
	echo "<script>alert('你的邮件地址为空，请完善你的邮件地址及个人信息！')</script>";
	$url="../user/user_modify.php?userArray[]=${u_ID}&action=编辑";
	echo" <script>setTimeout('document.location.href=\"$url\"',0)</script>";  	
	exit;
}
if(empty($error))
{
	echo "<br>处理完成！";
	echo " <script>setTimeout('document.location.href=\"svn_monitor.php\"',5)</script>";
}






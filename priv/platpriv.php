<?php
session_start();
include('../include/charset.php');
error_reporting(0);
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	exit;
}
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('请先进行系统设置!')";
}
include('../../../config.inc');
include('../include/basefunction.php');
include('../include/dbconnect.php');
echo "<link rel=\"stylesheet\" href=\"../css/base.css\" type=\"text/css\">";
echo "
<style type='text/css'>
a.g:Link,a.g:Visited{
        color:#0044DD;
        text-decoration:none;
}
a.g:Hover,a.g:Active{
        color:#FF5500;
        text-decoration:underline;
}
</style>
	";
function safe($str)
{ 
	$str=htmlspecialchars($str,ENT_QUOTES);
	return mysql_real_escape_string($str);
}
$dir=safe($_GET['p']);
$repos=safe($_GET['r']);
$fromurl=safe($_GET['fromurl']);
if($repos=='/')
{
	echo "不能对根目录进行递归！";
	exit;
}
#1、取得递归的dir名，从组权限表和用户权限表
$dir_p=($dir=='/')?("$dir%"):("$dir/%");
$a_path=array();
$a_upath=array();
$a_gpath=array();

$query="select distinct path  from svnauth_permission where svnauth_permission.repository='$repos' and svnauth_permission.path like '$dir_p' ";
$result = mysql_query($query);
while($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) 	
{
	if(!empty($row['path']))$a_upath[]=$row['path'];
}
$query="select distinct path  from svnauth_g_permission where svnauth_g_permission.repository='$repos' and svnauth_g_permission.path like '$dir_p' ";
$result = mysql_query($query);
while($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) 	
{
	if(!empty($row['path']))$a_gpath[]=$row['path'];
}

$a_path=array_merge($a_upath,$a_gpath);
$a_upath[]=$dir;
$a_path[]=$dir;
$a_gpath[]=$dir;
$a_path=array_unique($a_path);
sort($a_path);
#2、将dir唯一化，逐个遍历组权限表、用户权限表

echo "<a href='$fromurl'>返回</a>";
foreach($a_path as $path)
{
	echo "<a class='g' href='./dirpriv.php?d=$repos$path'><h5>$repos$path</h5></a>";
	echo "<table>";
	$p_a=array('n'=>'无权限','r'=>'只读','w'=>'读写');
	if(in_array($path,$a_upath))
	{
		$query="select svnauth_user.user_id,user_name,full_name,permission from svnauth_permission,svnauth_user where svnauth_user.user_id=svnauth_permission.user_id and repository='$repos' and path='$path' order by user_name";
		$result=mysql_query($query);
		while($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) 	
		{
			$user_name=$row['user_name'];
			$full_name='';
			if(!empty($row['full_name']))$full_name='('.$row['full_name'].')';
			$permission=$row['permission'];
			echo "<tr><td>$user_name$full_name</td><td> $p_a[$permission]</td></tr>";
		}
	}
	if(in_array($path,$a_gpath))
	{
		$query="select svnauth_group.group_id,group_name,permission from svnauth_g_permission,svnauth_group where svnauth_group.group_id=svnauth_g_permission.group_id and repository='$repos' and path='$path' order by group_name";
		$result=mysql_query($query);
		while($result and($row= mysql_fetch_array($result, MYSQL_BOTH))) 	
		{
			$group_name=$row['group_name'];
			$per=$row['permission'];
			$gid=$row['group_id'];
			echo "<tr><td><a class='g' href='../user/viewgroup.php?gid=$gid&grp=$group_name&fromurl=$fromurl'>$group_name(组)</a></td><td> $p_a[$per]</td></tr>";
		}
	}
	echo "</table>";
}
echo "<a href='$fromurl'>返回</a>";

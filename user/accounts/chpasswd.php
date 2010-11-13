<?php
include('../../include/charset.php');
/*
   文件名：chpasswd.php
   功能：修改密码
   输入：用户名、签名、新密码
   输出：无
   逻辑： 验证签名是否正确
					修改密码
					删除pwdurl的用户名和参数
*/
$user=stripslashes(trim($_POST["user"]));
$sig=stripslashes($_POST["sig"]);
$para_str=stripslashes($_POST["para"]);
$newpasswd=escapeshellcmd($_POST['pswd']);
$newpasswd1=trim($_POST['pswd0']);
include('../../../../config.inc');
include('../../include/basefunction.php');
$sig0=md5($para_str.$user.SECRET_KEY);
if(strcasecmp(urlencode($sig0),trim($sig))!=0)
{
	echo "<font color=red><h2>无效请求</h2></font>";
	echo "<meta http-equiv=\"Refresh\" content=\"2;url=/\">";
	exit;
};

$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("数据库链接失败！请联系管理员");
mysql_select_db(DBNAME) or die("不能选择数据库！");

if (($newpasswd != $newpasswd1)or(strlen($newpasswd)<6))  
{ echo " <script>window.alert(\"两次输入的密码不一致，请重新输入!\")</script>";
   echo " <a href='javascript:history.back()'>点击这里返回</a>";
   echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>
      ";
   exit;
} 
include('../../config/config.php');
$pwdpath=$passwdfile;
$cmdpath=$htpasswd;

$usr= mysql_real_escape_string($user,$mlink);
$passwd1= mysql_real_escape_string($newpasswd,$mlink);
$passwd1=cryptMD5Pass($passwd1);
if(($passwd1 == "")||($usr ==""))
{
	echo " <script>window.alert(\"密码和用户名不能为空，请输入!\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo "<script>history.go(-1);</script>";
  exit;
}


//SQL查询语句;
//$query = "SELECT user_name,password FROM svnauth_user WHERE user_name =\"$usr\""; 
$query = "update svnauth_user set password=\"$passwd1\" WHERE user_name =\"$usr\";";
// 执行查询
$result =mysql_query($query);
if (mysql_affected_rows($mlink) == 0){
		echo "<script>window.alert(\"用户名不存在！或新密码与原密码相同！\")</script>"; 
		echo "<script>history.go(-1);</script>";
		mysql_close($mlink);
		exit;
}else
{
	exec($cmdpath.' -m -b '. $pwdpath . ' '.$usr.' '.$newpasswd);
 $query="delete from svn_chpwd  where username=\"$usr\";";
	mysql_query($query);
  echo "<script>window.alert(\"密码重置成功！　\")</script>"; 
	 echo "    <script>setTimeout('document.location.href=\"/\"',5)</script>
    ";
		mysql_close($mlink);
		exit;
}	
echo "<meta http-equiv=\"Refresh\" content=\"1;url=/\">";
?>

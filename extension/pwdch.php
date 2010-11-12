<?php
include('../config/config.php');
include('../../../config.inc');
include('../include/dbconnect.php');
$usr= mysql_real_escape_string($_POST['username'],$mlink);
$oldpwd= mysql_real_escape_string($_POST['oldpasswd'],$mlink);
$passwd0= mysql_real_escape_string($_POST['newpaswd0'],$mlink);
$passwd= mysql_real_escape_string($_POST['newpasswd'],$mlink);
include('../include/basefunction.php');
$pwdpath=$passwdfile;
$cmdpath=$htpasswd;
//phpinfo();
//$authed_username = $_SERVER["PHP_AUTH_USER"]; //经过 AuthType Basic 认证的用户名
//$authed_pass = $_SERVER["PHP_AUTH_PW"]; //经过 AuthType Basic 认证的密码
//echo "username.passwd:".$authed_username.$authed_pass;
if(($oldpwd == "")||($usr ==""))
{
	echo " <script>window.alert(\"原密码和用户名不能为空，请输入!\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo "<script>history.go(-1);</script>";
  exit;
}
if ($passwd != $passwd0)  
{ echo " <script>window.alert(\"两次输入的新密码不一致，请重新输入!\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo "<script>history.go(-1);</script>";
  exit;
}

//SQL查询语句;
$query = "SELECT user_name,password FROM svnauth_user WHERE user_name ='$usr'"; 
$result =mysql_query($query);
if($result)$totalnum=mysql_num_rows($result);
if($totalnum>0){
  while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	  $fpasswd=$row['password'];
	  if(verifyPasswd($oldpwd,$fpasswd))
	  {	  
		$passwd=cryptMD5Pass($passwd);
		$query = "update svnauth_user set password='$passwd' WHERE user_name ='$usr'";
// 执行查询
		mysql_query($query);
		$err=mysql_error();
		if (empty($err)){
			$passwd0=escapeshellcmd($passwd0);
			$usr=escapeshellcmd($usr);
	          exec($cmdpath.' -m -b '. $pwdpath . ' '.$usr.' '.$passwd0);
	//echo  ($cmdpath.' -m -b '. $pwdpath . ' '.$usr.' '.$passwd);
  	         echo "<script>window.alert(\"密码更改成功！　\")</script>"; 
	          echo "    <script>setTimeout('document.location.href=\"javascript:history.back()\"',5)</script>";
		mysql_close($mlink);
	        exit;
		}
	  }else
	  {
		echo "<script>window.alert(\"原密码输入错误！\")</script>"; 
		echo "<script>history.go(-1);</script>";
	  }
	}
}
else{
		echo "<script>window.alert(\"用户名不存在！\")</script>"; 
		echo "<script>history.go(-1);</script>";
		mysql_close($mlink);
		exit;
}		



?>

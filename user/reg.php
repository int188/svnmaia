<?php
//用户注册处理
include('../include/charset.php');
error_reporting(0);
include('../../../config.inc');
include('../include/basefunction.php');
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "请先进行系统设置!";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}
include('../include/dbconnect.php');
$passwd=$_POST['passwd'];
$passwd0=$_POST['passwd0'];
foreach($_POST as $key=>$value){
	$value=htmlspecialchars($value,ENT_QUOTES);
	$_POST["$key"]=mysql_real_escape_string($value,$mlink);
#	if (function_exists('iconv'))$_POST["$key"]=iconv("GB2312","UTF-8",$_POST["$key"]);
}
$username=trim($_POST['username']);
$username=str_replace(' ','',$username);
if(!checkUserGroup($username))
{
	echo "用户名非法！";
	exit;
}
$fullname=trim($_POST['fullname']);
$staff_no=$_POST['staff_no'];
$department=$_POST['department'];
$email=$_POST['email'];
if(!empty($_POST['randompwd']))
{
	$passwd=$passwd0=rand().rand();
}
if($email=="")$email=$username.$email_ext;
if(($passwd == "")||($username =="")||($fullname ==""))
{
	echo " 密码和用户名不能为空，请输入!";
  exit;
}
if ($passwd != $passwd0)  
{ echo " 两次输入的密码不一致，请重新输入!";
 
  exit;
}
if(empty($_POST['randompwd']))
{
	if(isSamplePassword($passwd,$username))
	{
		echo "密码过于简单,密码由至少6个英文字母和数字/符号组成，且不能包含用户名。";
		exit;
	}
}
//$passwd= system($htpasswd.' -m -b -n '.escapeshellarg($usr).' '.escapeshellarg($passwd));
//list($ot,$passwd)=explode(':',$passwd);
$passwd=cryptMD5Pass($passwd);

//设置字符集
$query = "select user_name from svnauth_user WHERE user_name ='$username'; ";
$result =mysql_query($query);
if (mysql_num_rows($result) > 0){
		echo "用户名已存在！如果忘记密码，请使用“找回密码”功能。"; 
		mysql_close($mlink);
		exit;
}else
{
  $expire=date("Y-m-d" , strtotime("+$user_t day"));
  $query = "insert into svnauth_user (user_name,password,full_name,email,staff_no,department,supervisor,expire) values ('$username','$passwd','$fullname','$email','$staff_no','$department',0,'$expire')";
  $result =mysql_query($query) or die('注册失败：'.mysql_error());
  if($result){
	$username=escapeshellarg($username); 
	$passwd0=escapeshellarg($passwd0);
  	exec($htpasswd.' -m -b '. $passwdfile . ' '.$username.' '.$passwd0);
  	echo "用户注册成功！"; 
		mysql_close($mlink);
		exit;
  }
}


?>

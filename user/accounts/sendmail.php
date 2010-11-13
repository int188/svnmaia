<?php
session_start();
include('../../include/charset.php');
/*
   文件名：sendmail.php
   功能：处理忘记密码
   输入：用户名
   输出：url、发送邮件
   逻辑：根据用户名找到邮箱，根据用户名和随机数生成加密字符串
         将字符串参数存入文件pwdurl,生成url类似 accounts/?u=urlencode(username)&c=字符串
				将字符串发给对应信箱。
*/
include('../../../../config.inc');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("数据库链接失败！请联系管理员");
if (!mysql_select_db(DBNAME))
{
  exit;
}
$reg_usr=mysql_real_escape_string($_POST['username'],$mlink);
//对用户邮件和cookie进行验证，如果用户邮件和域用户一致则继续，否则提示错误。
include('../../config/config.php');
$email=$reg_usr.$email_ext;
if(empty($reg_usr))
{
	echo " <script>window.alert(\"请输入用户名!\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>
      ";
  exit;
	
}
$query="select email from svnauth_user where user_name =\"$reg_usr\";";
$result = mysql_query($query);
if($result)$totalnum=mysql_num_rows($result); 
if($totalnum>0){
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	if(empty($row['email']))
	{
	    $email=$reg_usr.$email_ext;
	}
	else
	  $email=$row['email'];
}
$cookie=explode('&',$_COOKIE['CNSSO']);
foreach($cookie as $name)
{
	if(stristr($name,'userid='))
	{
		$uEmail=str_replace('userid=','',$name).$email_ext;
		break;
	}
}
/**
if($email != $uEmail)
{
	echo " <script>window.alert(\"该邮箱地址与您真实邮箱地址不符！\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>
      ";
  exit;
	
}
**/
$salt=mt_rand();
$ss=microtime();
$ss=str_replace(" ","",$ss);
$ss=base64_encode(str_replace("0.","",$ss));
$para_str=urlencode(md5($reg_usr.$salt.SECRET_KEY.$ss));
$u=urlencode($reg_usr);
$url="http://".$_SERVER['HTTP_HOST']. rtrim(dirname($_SERVER['PHP_SELF']))."/index.php?u=$u&c=$para_str&ss=$ss";
//$para_str=urldecode($para_str);
//记录申请链接

	$createtb = "create table IF NOT EXISTS svn_chpwd(
		`autoid` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (autoid),		
		`username` varchar(40) UNIQUE,
		`hexkey` varchar(255)
		)ENGINE=MyISAM;";
	mysql_query($createtb);
 $query="update svn_chpwd set hexkey=\"$para_str\" where username=\"$reg_usr\";";
		mysql_query($query);
		if (mysql_affected_rows() == 0)
		{
			$query="insert IGNORE into svn_chpwd set username=\"$reg_usr\",hexkey=\"$para_str\";";
			mysql_query($query);
		}


//将字符串发给对应邮箱
		include("../../include/email.php");
		$addr=$_SERVER['REMOTE_ADDR'];
$body="您于今天申请了重置svn密码服务，
要启动重置您的 $reg_usr 帐户密码的过程，请访问以下链接\n

$url

如果通过点击以上链接无法访问，请将该网址复制并粘贴至新的浏览器窗口中。
（如果你多次使用了找回密码功能，请以最新一封邮件为准）

如果您对您的帐户有任何问题或疑问，请回复和我们联系。
重要：如果您不重置密码，请删除本邮件！

这只是一封系统自动发出的邮件，我们不监控它的发送。

触发此邮件来自IP:$addr

--------------------
配置管理组\n
";
$subject="svn密码帮助";
$sendinfo =send_mail($email,$subject,$body);
if ($sendinfo === true) {
    echo "<br>邮件已发出<br>请注意查收您的邮件 $email";
}else {
	echo(is_string($sendinfo) ? $sendinfo : 'reg_email_fail');
}
#echo "    <script>setTimeout('document.location.href=\"javascript:window.history.back(-3)\"',5)</script> ";
if($_SESSION['role']=='admin')
{
	echo <<<HTML
<hr>
<strong>Hi,管理员:</strong>
<br>&nbsp;&nbsp;&nbsp;或许{$email}已经收到了系统发出的重置密码邮件，但作为管理员，你也可以以其他方式告知对方如下链接，
通过此链接可以直接进行密码重置，而无需原密码：
<br><a href="$url">$url</a>
<p><strong>请注意</strong>：勿让其他人获得此链接地址。</p>
<p><a href='../../default.htm'>返回svnMaia管理系统</a></p>
HTML;
exit;
}
echo "<meta http-equiv=\"Refresh\" content=\"3;url=../../default.htm \">";
?>

<?php
include('../../include/charset.php');
/*
   文件名：getusers.php
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

$usr=mysql_real_escape_string($_GET['username'],$mlink);
if(empty($usr))
{
	echo "none!";
	exit;
}
include('../../config/config.php');
$email=$usr.$email_ext;
$query="select email from svnauth_user where user_name =\"$usr\";";
$result = mysql_query($query);
if($result)$totalnum=mysql_num_rows($result); 
if($totalnum>0){
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	if(empty($row['email']))
	{
	   echo $usr.$email_ext;
	}
	else
	  echo $row['email'];
}else
  echo '用户不存在！';
?>

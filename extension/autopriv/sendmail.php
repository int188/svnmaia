<?php
session_start();
include('../../include/charset.php');
error_reporting(0);
/*
   文件名：sendmail.php
   功能：处理权限申请，依据url和规则发送邮件给对应管理员。
   输入：url、用户名、申请权限类型、申请说明
   输出：发送邮件
   逻辑：
*/
include('../../../../config.inc');
include('../../config/config.php');
include('./autopriv.conf');
include('../../include/dbconnect.php');
foreach($_POST as $k=>$v)
{
	$v=htmlspecialchars($v,ENT_QUOTES);
	$_POST[$k]=mysql_real_escape_string($v);
}
$reg_usr=$_POST['username'];
$b_email=$_POST['email'];
$wurl=$_POST['wurl'];
$wpriv=$_POST['wpriv'];
$comment=$_POST['comment'];
//******输入合法性检查********
if((empty($reg_usr))or(empty($wpriv))or(empty($wurl))or(empty($comment)))
{
	echo " <script>window.alert(\"输入信息不全!\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
  exit;
	
}
function checkurl($t_url)
{
	global $svnparentpath,$svn;
	if($t_url=='')return true;
	if(strpos($t_url,':'))return false;
//中文目录判断有问题
//	if(isset($_GET['from_d']))
	{
	  $t_url=escapeshellcmd($t_url);
 	  $localurl=($svnparentpath{0}=='/')?("file://$svnparentpath/$t_url"):("file:///$svnparentpath/$t_url");
	  exec("{$svn}svn info \"$localurl\"",$dirs_arr);
	  if(count($dirs_arr)>1)
	  {
		return true;
	  }else
		return false;
	}
	return true;
}
$dir=trim($wurl);
$dir=str_replace($svnurl,'',$dir);
if(preg_match("/^http:/i",$dir)){
	$dir=str_replace("http://",'',$dir);
	list($tmp1,$tmp2,$dir)=explode('/',$dir,3);
}
$dir=($dir{0}=='/')?(substr($dir,1)):($dir);
$dir=str_replace('//','/',$dir);
if(!checkurl($dir))
{
  echo " <script>window.alert(\"url不正确!\")</script>";
  echo " <a href='javascript:history.back()'>点击这里返回</a>";
  echo " <script>setTimeout('document.location.href=\"javascript:history.go(-1)\"',5)</script>";
  exit;
}
list($repos,$dir)=explode('/',$dir,2);
$dir=($dir{strlen($dir)-1}=='/')?('/'.substr($dir,0,-1)):('/'.$dir);
if(empty($repos) and ($dir=='/'))
{
	$repos='/';
	$dir='';
}
$subdir=$dir;
$maillist=array();
//*****目录管理员审批
if(($dir_admin_op=='checked')or($thenlist_op=='checked'))
 for($ii=0;$ii<20;$ii++)
{
	$query="select user_name,email from svnauth_dir_admin,svnauth_user where svnauth_dir_admin.user_id=svnauth_user.user_id and repository='$repos' and path='$subdir' order by user_name";
	//echo $query;exit;
	$result = mysql_query($query);
	$t_found=false;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$uname=trim($row['user_name']);
		if(empty($uname))continue;
		$t_email=$row['email'];
		if(empty($t_email))$t_email=$uname.$email_ext;
		$maillist[]=$t_email;
		$t_found=true;
	}
	if($t_found)break;
	if(($subdir=='/') or (empty($subdir)))break;
	if(strlen($subdir)>1)$subdir=dirname($subdir);
	if($subdir=='\\')$subdir='/';
}
//********发送到超级管理员
if(($tosuper_op=='checked')or((!$t_found)and ($dir_admin_op=='checked')))
{
  $query="select user_name,email from svnauth_user where supervisor=1";
  $result = mysql_query($query);
  while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$uname=trim($row['user_name']);
	$t_email=$row['email'];
	if(empty($uname))continue;	
	if(($uname=='root')and (empty($t_email)))continue;
	if(empty($t_email))$t_email=$uname.$email_ext;	
	$maillist[]=$t_email;
  }
}
//*********发送到指定邮件列表
if($tolist_op=='checked')
{
	$listArray=preg_split('/[;, ]/',$email_list);
	foreach($listArray as $v)
		if(strpos($v,'@'))$maillist[]=$v;
}
//******无目录管理员是发送到指定列表
if((!$t_found)and ($thenlist_op=='checked'))
{
	$listArray=preg_split('/[;, ]/',$email_list2);
	foreach($listArray as $v)
		if(strpos($v,'@'))$maillist[]=$v;
}
//****************	
if(count($maillist)==0)
{
	echo "无法发现该url的审批者！你的申请没有发送成功！请与配管组联系。";
	exit;
}
//$para_str=urldecode($para_str);
//记录申请链接
$createtb = "create table IF NOT EXISTS rt_svnpriv(
		`id` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`),		
		`username` varchar(40) NOT NULL,
  `repository` varchar(50) NOT NULL,
  `path` varchar(255) NOT NULL,
  `email` varchar(80) default NULL,
  `permission` varchar(1) NOT NULL,
`rtdate` date, 
  `ops` varchar(40),
  `optype` ENUM('agreed','denied','other')
		)ENGINE=MyISAM;";
	mysql_query($createtb);
$query="select max(`id`) + 1 from rt_svnpriv";
$result=mysql_query($query);
if($result)
{
	$row= mysql_fetch_array($result, MYSQL_BOTH);
	$id=$row[0];
	if(empty($id))$id=1;
}
$query="select full_name from svnauth_user where user_name ='$reg_usr';";
$result = mysql_query($query);
if($result)$totalnum=mysql_num_rows($result); 
if($totalnum>0){
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	if(!empty($row['full_name']))
	{
		$full_name="(".$row['full_name'].")";
	}
}
$query="insert into rt_svnpriv (`id`,`username`,`repository`,`path`,`permission`,`email`,`rtdate`) values($id,'$reg_usr','$repos','$dir','$wpriv','$b_email',NOW())";
mysql_query($query);
$rc_error=mysql_error();
//******生成处理链接*******
$salt=mt_rand();
$para_str=urlencode(md5($salt.SECRET_KEY.$id));
$url_raw="http://".$_SERVER['HTTP_HOST']. rtrim(dirname($_SERVER['PHP_SELF']))."/index.php?c=$para_str&ss=$salt&s=$id";

//**记录链接********
$createtb = "create table IF NOT EXISTS svn_hex(
		`id`  INT UNIQUE, PRIMARY KEY (id),
		`hexkey` varchar(255)
		)ENGINE=MyISAM;";
	mysql_query($createtb);
$query="insert  into svn_hex set id=$id,hexkey='$para_str';";
mysql_query($query);
$rc_error=$rc_error.mysql_error();
if(!empty($rc_error))
{
	echo "记录请求时发生错误，无法发送申请，请稍侯再试。错误信息：".$rc_error;
	exit;
}
//将字符串发给对应邮箱
include("../../include/email.php");
$addr=$_SERVER['REMOTE_ADDR'];
($wpriv=='w')?($priv='读写'):($priv='只读');
foreach($maillist as $mail)
{
  list($user_raw,$m)=explode('@',$mail);
  $user=urlencode(base64_encode($user_raw));
  $url=$url_raw."&u=$user";
  $body="Hi,$user_raw\n
   $reg_usr $full_name 申请svn访问权限，需要您的处理，详情如下：
	申请访问路径：$wurl
	申请权限：$priv
	申请说明：$comment

无论您是同意还是拒绝，请点击如下链接进行处理：
$url

如果通过点击以上链接无法访问，请将该网址复制并粘贴至新的浏览器窗口中。

这只是一封系统自动发出的邮件，我们不监控它的发送。

触发此邮件来自IP:$addr

--------------------
配置管理组\n
";
 $subject="svn权限申请-$reg_usr";
 $windid="svn-rt";
 $sendinfo =send_mail($mail,$subject,$body);
 if ($sendinfo === true) {
    echo "<br>申请已发出至 $mail";
 }else {
	echo(is_string($sendinfo) ? $sendinfo : ' 发送失败:'.$mail);
 }
}
echo "<br>点击<a href='' onclick='javascript:self.close();'>关闭</a>";
?>

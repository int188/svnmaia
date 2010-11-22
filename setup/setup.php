<?php
session_start();
include('../include/charset.php');
$_SESSION['role']='admin';
error_reporting(0);
$succ='disabled';
//检测系统配置
if(!function_exists('mysql_connect'))
	$sys='<strong>Error</strong>:检测到php不支持mysql，请在安装编译php时开启--with-mysql参数；并确认php.ini加载了php_mysql模块<br>';
$php_v=phpversion();
if(($php_v{0})<5)
	$sys .= "<strong>Error</strong>:php版本太低($php_v)，程序无法正常运行<br>";
if(!empty($_POST['dbname']))
{
	$server=$_POST['server'];
	$dbname=$_POST['dbname'];
	$dbuser=$_POST['dbuser'];
	$dbpasswd=$_POST['dbpasswd'];
	$svnpasswd=$_POST['svnpasswd'];
	$svnpasswd0=$_POST['svnpasswd0'];
	if($svnpasswd != $svnpasswd0)
	{
		echo " <script>window.alert(\"svn超级用户密码不一致！请确认并牢记！\")</script>";
		echo "<script>setTimeout('document.location.href=\"./setup.php\"',3)</script>";
		exit;
	}
	include('../include/basefunction.php');
	$svnpasswd= cryptMD5Pass($svnpasswd);	
	$mlink=mysql_connect($server,$dbuser,$dbpasswd);
	$conn_error=mysql_error();
 	$sql_enc = "set names 'utf8'";
  	mysql_query($sql_enc);
	$query="create database IF NOT EXISTS $dbname";
	mysql_query($query);
	//get mysql version
	$pattern='/(\d+)\.\d+\.\d+/i';
	preg_match($pattern,mysql_get_server_info(),$out);
	$encode='';
	if($out[1] > 4) //mysql version > 4
	{
		echo "Mysql version:".mysql_get_server_info()."<br>";
		$encode=" DEFAULT CHARSET=utf8 ";
	}
	//------
	$db_error=mysql_error();
	if(empty($db_error))
	{
		mysql_select_db($dbname);
		//create user table
		$query="
CREATE TABLE IF NOT EXISTS `svnauth_user` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_name` varchar(40) NOT NULL,
  `full_name` varchar(40) default NULL,
  `password` varchar(128) NOT NULL,
  `staff_no` int(11) default NULL,
  `department` varchar(100) default NULL,
  `email` varchar(80) default NULL,
  `supervisor` bit(1) NOT NULL,
  `fresh` bit(1) default 0,
  `expire` date NOT NULL,
  `infotimes` int(1) default 0,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM $encode ;";
	mysql_query($query);
		$usertb_err=mysql_error();
	//create group table
	$query="
CREATE TABLE IF NOT EXISTS `svnauth_group` (
  `group_id` int(11) NOT NULL auto_increment,
  `group_name` varchar(40) NOT NULL UNIQUE,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM $encode ;";
	mysql_query($query);
		//create group_user table;
		$query="
CREATE TABLE IF NOT EXISTS `svnauth_groupuser` (
  `group_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
 `isowner` bit(1) default 0,
  PRIMARY KEY  (`group_id`,`user_id`)
) ENGINE=MyISAM $encode ;";
	mysql_query($query);
		//create permission table
		$query="CREATE TABLE IF NOT EXISTS `svnauth_permission` (
  `user_id` varchar(11) NOT NULL,
  `repository` varchar(50) NOT NULL,
  `path` varchar(255) NOT NULL,
  `permission` varchar(1) NOT NULL,
  `expire` date,
  PRIMARY KEY  (`user_id`,`repository`,`path`,`permission`)
) ENGINE=MyISAM $encode ;";
		mysql_query($query);
	//create group_permission table
		$query="CREATE TABLE IF NOT EXISTS `svnauth_g_permission` (
  `id` int(11) NOT NULL auto_increment,
 `group_id` varchar(11) NOT NULL, 
  `repository` varchar(50) NOT NULL,
  `path` varchar(255) NOT NULL,
  `permission` varchar(1) NOT NULL,
  `expire` date,
  PRIMARY KEY (`id`),
  UNIQUE KEY  (`group_id`,`repository`,`path`)
) ENGINE=MyISAM $encode ;";
		mysql_query($query);
		$perstb_err=mysql_error();
		//create dir admin table
		$query="CREATE TABLE IF NOT EXISTS `svnauth_dir_admin` (
  `user_id` int(11) NOT NULL,
  `repository` varchar(50) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY  (`user_id`,`repository`,`path`)
) ENGINE=MyISAM $encode;";
		mysql_query($query);
		$admintb_err=mysql_error();
		//create para table
		$query="CREATE TABLE IF NOT EXISTS `svnauth_para` (
  `para_id` int(11) NOT NULL auto_increment,PRIMARY KEY (para_id),
  `para` varchar(250) NOT NULL UNIQUE,
  `value` varchar(255)
  )ENGINE=MyISAM $encode;";
		mysql_query($query);
		$paratb_err=mysql_error();
		//insert a super user of svn
		$query = "insert into svnauth_user (user_name,password,full_name,email,staff_no,department,supervisor,expire) values (\"*\",\"scmbbs\",\"everyone\",\" \",\"0\",\" \",0,'2110-01-01')";
		mysql_query($query);
		$query = "insert into svnauth_user (user_name,password,full_name,email,staff_no,department,supervisor,expire) values (\"root\",\"$svnpasswd\",\"super admin\",\" \",\"0\",\" \",1,'2110-01-01')";
		mysql_query($query);
	}
	if(empty($conn_error) and empty($db_error) and empty($usertb_err) and empty($perstb_err) and empty($admintb_err) and empty($paratb_err))
	{
		$succinfo="<br>&nbsp;数据库创建成功！请单击下一步进行配置！<br>&nbsp;";
		$succ='';
		$notsucc='disabled';
		//保存配置文件
		$file_str="<?php \n";
		$file_str .="define(\"SERVER\",\"$server\");\n";
		$file_str .="define(\"USERNAME2\",\"$dbuser\");\n";
		$file_str .="define(\"PASSWORD2\",\"$dbpasswd\");\n";
		$file_str .="define(\"DBNAME\",\"$dbname\");\n";
		$temp=md5($dbname.$dbuser.$dbpasswd);
		$file_str .="define(\"SECRET_KEY\",\"$temp\");\n";
		$file_str .="?>\n";
		$handle=fopen('../../../config.inc','w+');
		$confpath=realpath('../../../').'/config.inc';
		$allsecc=true;
		if (fwrite($handle, $file_str) === FALSE) {
			$err_str= "<strong>Fatal Error:</strong>不能写入到文件 $confpath ! 保存失败！原因可能是此程序owner没有足够权限修改此目录文件，请修复！<br>
				这是个致命错误，建议您手工创建此文件，并包含如下内容：<br>&lt;?php <br>".str_replace("\n",'<br>',$file_str);
			$succinfo=$succinfo.'点击下一步前，请确保'.$confpath.'文件已创建。<br>&nbsp;';
			$allsecc=false;
    		}
		fclose($handle);
		//*****
		// 创建并重写 ../index.php;
		// 删除本安装文件setup.php
		if($allsecc){
			$indexf=file_get_contents('../default.htm');
			$handle=fopen('../index.php','w');
			fwrite($handle,$indexf);
			fclose($handle);
			unlink('./setup.php');
		}
		//*****

	}else{
		$succ='disabled';
		$notsucc='';
		if(!empty($conn_error))$conn_error ="链接数据库失败，请确认您的mysql是否在运行：".$conn_error;
		$err_str="创建数据库时遇到如下致命错误：<br>".$conn_error.'<br>'.$db_error.'<br>'.$usertb_err.'<br>'.$perstb_err.'<br>'.$admintb_err.$paratb_err;
	}
	
}

?>
<h1>Maia svn用户管理系统安装向导</h1>
<script language="javascript">
<!--
	function submit()
	{
		formconf.submit();
		return true;
	}
	function next()
	{
		self.location='../config/index.php';
	}
-->
</script>
<style type='text/css'>
input{position:absolute;left:250px;clear:both;float:left;width:250px;}
br{clear:both;}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;font-weight:bold;}
legend{color:#1E7ACE;padding:3px 20px;border:2px solid #A4CDF2;background:#FFFFFF;}
.tip{text-decoration:none;color:green;font-size:11pt;background:#FFFFCC; }
</style>
<div class='tip'>
<strong>说明：</strong>进行本程序安装前，请确认您的apache已经正确配置并启用，以及已经安装好了mysql-4.0以上版本；并确认您的mysql服务处于运行状态。
<br>本程序要求配环境：
<br>&nbsp;  php：5.1以上
<br>&nbsp;  mysql：4.0以上（推荐5.*）
<br>&nbsp;  apache2以上
<br>&nbsp;  svn1.2以上
<br>详情参阅<a href='http://www.scmbbs.com/cn/maia/2009/5/maia001.php'>Maia SVN 管理系统安装说明</a>
<br>&nbsp;
</div>
<?php
if(!empty($sys))
{
	$notsucc='disabled';	
	echo "<div style='color:red;'>系统检测到如下错误，请先修正：<br>".$sys.'</div>';
}
?>
<div id='step1'>
<form method='post' action='' name='formconf'>
<fieldset>
<legend>数据库设置</legend>
<div id='errdiv' style='color:red;'>
<?php echo $err_str;?>
</div>
<br>请输入数据库地址：<input type='text' name='server' value='localhost:3306'>
<p><br>请输入数据库名字：<input type='text' name='dbname'>
<p><br>请输入数据库链接用户名：<input type='text' name='dbuser'> 
<p><br>请输入该用户密码：<input type='password' name='dbpasswd'>
<hr>
<br>请指定svn超级用户root密码：<input type='password' name='svnpasswd'>
<p><br>请确认svn超级用户root密码：<input type='password' name='svnpasswd0'>
<br>&nbsp;
</fieldset>
</form>
<div id='infodiv' style='background:#fff0f5;color:green;border:1px solid;'>
<?php echo $succinfo;?>
</div>
<div style='text-align:center;margin-top:30px;'>
<button <?php echo $notsucc;?> onclick="submit()">确定</button> 
&nbsp; <button <?php echo $succ;?> onclick="next()">下一步</button>
</div>
</div>


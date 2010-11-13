<?php
session_start();
include('../include/charset.php');
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('请先进行系统设置!')";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}
 error_reporting(0);
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>";  	
	exit;
}
if (($_SESSION['role'] !='admin')and($_SESSION['role'] !='diradmin'))
{
	echo "您无权进行此操作！";
	exit;
}
include('../../../config.inc');
include('../include/basefunction.php');
function safe($str)
{ 
	$str=htmlspecialchars($str,ENT_QUOTES);
	return "'".mysql_real_escape_string($str)."'";
}
include('../include/dbconnect.php');
$is_effected=false;
if (mysql_select_db(DBNAME))
{
	//校验参数正确性
	$repos=mysql_real_escape_string($_POST['repos']);
	$path=mysql_real_escape_string($_POST['path']);
	$url="./dirpriv.php?d=$repos{$path}";
	$para=array($repos,$path);
	if(keygen($para) != $_POST['sig'])
	{
		echo "参数非法！请勿越权操作！";
		exit;
	}
	$adminonly=$_POST['adminonly'];
	if($adminonly =='true')
	{
		$admin_array=$_POST['manager'];
		$clear=false;
		foreach($admin_array as $v)
		{
			list($user,$uid,$is_c)=explode(' ',$v);
			$is_c=trim($is_c);
			if($is_c == 'c')continue;
			if(! $clear)
			{
				$clear=true;
				$query="delete from svnauth_dir_admin where repository='$repos' and path='$path'";
				mysql_query($query);
				$err=mysql_error();
			}
			if(! is_numeric($uid))continue;
			$query="insert into svnauth_dir_admin (repository,path,user_id) values('$repos','$path',$uid)";
			mysql_query($query);
			$err .= mysql_error();
			$is_effected=true;
		}


	}else
		if(!empty($_POST['fromdir']))
		{
			//处理目录
$dir=trim(mysql_real_escape_string($_POST['fromdir']));
$dir=str_replace($svnurl,'',$dir);
$dir=($dir{0}=='/')?(substr($dir,1)):($dir);
$dir=str_replace('//','/',$dir);
list($f_repos,$dir)=explode('/',$dir,2);
$dir=($dir{strlen($dir)-1}=='/')?('/'.substr($dir,0,-1)):('/'.$dir);
				$query="select * from svnauth_permission where repository='$f_repos' and path = '$dir' ";
$result=mysql_query($query);
if (mysql_num_rows($result) > 0){
	$clear=false;
		if(! $clear)
		{
				$clear=true;
				$query="delete from svnauth_permission where repository='$repos' and path='$path'";
				mysql_query($query);
				$err=mysql_error();
		}
	$query="insert into svnauth_permission (user_id,repository,path,permission,expire) select user_id,'$repos','$path',permission,expire from svnauth_permission where  repository='$f_repos' and path = '$dir' ";
	mysql_query($query);
	$query="insert into svnauth_g_permission (group_id,repository,path,permission,expire) select group_id,'$repos','$path',permission,expire from svnauth_g_permission where  repository='$f_repos' and path = '$dir' ";
	mysql_query($query);
//	$err .= mysql_error();

}else
{
	$err .= "<strong>Error：</strong>$f_repos{$dir} 该目录还没有设置权限,无法从该目录复制权限。";
}

	}else{
		$detail_array=$_POST['permission_detail'];
		$clear=false;
		foreach($detail_array as $v)
		{
			list($rights,$user,$uid,$type)=explode(' ',$v);
			$user_expire="d_{$uid}";
			if(! $clear)
			{
				$clear=true;
				$query="delete from svnauth_permission where repository='$repos' and path='$path'";
				mysql_query($query);
				$err=mysql_error();
			}
			if(trim($type)=='c')continue;
			if(empty($uid))continue;
			$user=safe($user);
			$uid=safe($uid);
			if(is_numeric($_POST[$user_expire])){
				$expire=mktime(0, 0, 0, date("m")  , date("d")+$_POST[$user_expire], date("Y"));
			}else
			{
			 switch($rights)
      	    		{
      	    		case 'r':
      	    			 $expire=mktime(0, 0, 0, date("m")  , date("d")+$read_t, date("Y"));
      	    		 	break;
      	    		case 'w':
      	    		  	$expire=mktime(0, 0, 0, date("m")  , date("d")+$write_t, date("Y"));
      	    		  	break;
      	    		default:
      	    		  	$expire=mktime(0, 0, 0, date("m")  , date("d"), date("Y")+2);
      	    		}}      	    	
			$rights=safe($rights);
      	    		$expire=strftime("%Y-%m-%d",$expire);		
			$query="insert into svnauth_permission(user_id,repository,path,permission,expire)values($uid,'$repos','$path',$rights,'$expire')";
			mysql_query($query);
			$err .= mysql_error();
		}
		$detail_array=array();
	        $detail_array=$_POST['group_detail'];
		$clear=false;
		foreach($detail_array as $v)
		{
			list($rights,$group,$t_gid,$type)=explode(' ',$v);
			if(! $clear)
			{
				$clear=true;
				$query="delete from svnauth_g_permission where repository='$repos' and path='$path'";
				mysql_query($query);
				$err=mysql_error();
			}
			if(trim($type)=='c')continue;
			if(empty($t_gid))continue;
			$t_gid=safe($t_gid);
			$rights=safe($rights);
			$query="insert into svnauth_g_permission(group_id,repository,path,permission)values($t_gid,'$repos','$path',$rights)";
			mysql_query($query);
			$err .= mysql_error();
		}

	}
	if(!empty($err))
		echo "保存权限过程中发生错误，可能权限没有设置成功！出错信息：<br>$err";
	else
	{
		if($is_effected)
		{
	echo <<<HTML
<p style='text-align:center;line-height:2;border:solid 1px;background:#ecf0e1;margin-top:100px;'>
<br>保存成功！
<br>
<a href="$url">返回继续操作</a> 
</p>
HTML;

		}else
	echo <<<HTML
<p style='text-align:center;line-height:2;border:solid 1px;background:#ecf0e1;margin-top:100px;'>
<br>保存成功，但尚未生效！
<br>您要：
<a href="$url">返回继续操作</a> <br>还是：
<a href="./gen_access.php?fromurl=$url">立刻生效（生成access文件)</a>?
</p>
HTML;
	}

}
?>

<?php
session_start();
 error_reporting(0);
include('../include/charset.php');
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	exit;
}
include('../../../config.inc');
include('../include/dbconnect.php');
$user_id=$_GET['u'];
if(!is_numeric($user_id))
{
	echo "参数非法！转向显示你自己的权限。";
	$user_id=$_SESSION['uid'];	
}
$querystr=$_SERVER['QUERY_STRING'];
pri_modify();
function pri_modify()
{
	global $uinfo,$querystr;
	if($_SESSION['role']!='admin')return 1;
	$user_id=$_GET['u'];
	$action=$_GET['action'];
	$repos=mysql_real_escape_string($_GET['repos']);
	$path=mysql_real_escape_string($_GET['path']);
	if(empty($user_id)or empty($repos) or empty($path))return 1;
	if($action=='degrade')
	{
		$right=mysql_real_escape_string($_GET['right']);
		switch($right){
	case 'w':
		$right='r';
		break;
	case 'r':
		$right='n';
		break;
	case 'n':
		return 0;
		break;
		}
		$query="update svnauth_permission set permission='$right' where user_id=$user_id and repository='$repos' and path='$path'";
		mysql_query($query);
		echo mysql_error();
	}
	if($action=='del')
	{
		$query="delete from svnauth_permission where user_id=$user_id and repository='$repos' and path='$path' ";
		mysql_query($query);
	}
	$uinfo="变更已保存，但尚未生效，请点击<a href='./gen_access.php?fromurl=./viewpriv.php?$querystr'>【立刻生效】</a>";
}
group_modify();
function group_modify()
{
	global $uinfo,$querystr;
	if($_SESSION['role']!='admin')return 1;
	$user_id=$_GET['u'];
	$gid=$_GET['gid'];
	if(!is_numeric($user_id))return 1;
	if(!is_numeric($gid))return 1;
	$action=$_GET['action'];
	if($action=='out')
	{
		$query="delete from svnauth_groupuser where user_id=$user_id and group_id=$gid";
		mysql_query($query);
		$uinfo="变更已保存，但尚未生效，请点击<a href='./gen_access.php?fromurl=./viewpriv.php?$querystr'>【立刻生效】</a>";
	}
}
$query="select repository,path,permission from svnauth_permission where user_id = $user_id";
//echo $query;exit;
$result = mysql_query($query);
if(! $result)
{
	echo "该用户没有权限！";
	exit;
}
if($_SESSION['role']=='admin')
{
	$str='操作';
}
echo <<<HTML

<style type='text/css'>
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt
</style>
HTML;
echo $uinfo;
$query="select fresh from svnauth_user where user_id=$user_id and fresh=1";
$result=mysql_query($query);
if(($result) and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$stat=$row['fresh'];
	if(!empty($stat))echo"<b><font color=red>此用户已被冻结，所列权限不生效！</font></b>";
}
echo "<table><tr><th>路径</th><th>权限</th><th>$str</th></tr>";
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$repos=$row['repository'];
	$path=$row['path'];
	$permission=$row['permission'];
	$act='';
	if($_SESSION['role']=='admin')
	{
		$act="<a href='./viewpriv.php?action=degrade&u={$user_id}&right={$permission}&path={$path}&repos=$repos'>降权</a>&nbsp;&nbsp;<a href='./viewpriv.php?action=del&path={$path}&u={$user_id}&repos=$repos'>删除</a>";
	}
	$path=$repos.$path;
	switch($permission){
	case 'w':
		$permission='write';
		break;
	case 'r':
		$permission='readOnly';
		break;
	case 'n':
		$permission='none';
		break;
	}
	($tr_class=="trc1")?($tr_class="trc2"):($tr_class="trc1");	
	echo "<tr class=$tr_class><td>$path</td><td>$permission</td><td>$act</td></tr>";
}
echo "</table>";
$query="select svnauth_group.group_name,svnauth_group.group_id from svnauth_groupuser,svnauth_group where svnauth_groupuser.group_id=svnauth_group.group_id and svnauth_groupuser.user_id = $user_id";
$result = mysql_query($query);
echo "<h4>所在权限组:</h4>";
echo "<table>";
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$groupname=$row['group_name'];
	$gid=$row['group_id'];
	if ($tr_class=="trc1"){
		$tr_class="trc2";
	}else
	{			
		$tr_class="trc1";
	}
	$act='';
	if($_SESSION['role']=='admin')
	{
		$act="&nbsp;&nbsp;<a href='./viewpriv.php?action=out&u={$user_id}&gid={$gid}'>退除</a>";
	}

	echo "<tr class=$tr_class><td><a href='../user/viewgroup.php?gid=$gid&grp=$groupname&fromurl=../priv/viewpriv.php?$querystr'>$groupname</a></td><td>$act</td></tr>";
}
echo "</table>";
//--------
$query="select repository,path from svnauth_dir_admin where svnauth_dir_admin.user_id=$user_id";
$result =mysql_query($query);
$adminpath='';
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$path=$row['repository'].$row['path'];
	if(empty($path))continue;
	($tr_class=="trc1")?($tr_class="trc2"):($tr_class="trc1");
	$adminpath .= "<tr class=$tr_class><td>$path</td></tr>";
}
if(!empty($adminpath))
{
	echo "<h4>所管理的目录:</h4>";
	echo "<table>";
	echo $adminpath."</table>";
}

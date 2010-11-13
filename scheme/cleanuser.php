<?php
session_start();
include('../include/charset.php');
if(!isset($_SESSION['username']))
{
	echo "请先登录！";
	exit;
}
if ($_SESSION['role'] !='admin')
{
	echo "您无权进行此操作！";
	exit;
}

?>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<style type='text/css'>
.title{background: #007ED1 url(../img/bg.png)  100% 100%;font-size:11pt;color:white;}
.subtitle{background: #007ED1;color:white;}
.detail{width:680px}
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt}
.tb1{width:70%; border:1;text-align:center; background:#ecf0e1;}
</style>
<strong>说明：</strong>
在此页面中，您可以为所有用户设定一个有效期(从今天开始算起的有效天数，如有效期设为1天，则明天到期）。当用户有效期到时，系统会提前2个星期为每个用户发激活邮件。用户可以通过邮件的链接对有效期进行重新续订。如果用户不续订，则有效期过后，系统会自动删除用户及其对应权限信息，以达到清理无效用户的目的。
<br><strong>重设有效期：</strong>此功能将为选定用户指定“用户有效期限”，过期用户将被删除。（默认下，用户被删除前2周内会收到即将过期的邮件通知，并可通过邮件进行激活。）
<br><strong>到期不通知：</strong>此功能将使得所选定的用户在到期时，会被默默删除，而不会给用户发任何提醒激活邮件(状态值>3的用户将不发送激活邮件)。
<br><strong>状态：</strong>激活邮件的发送次数，当状态值>3时将不再发送激活邮件。
<p class='tb1'>
<?php echo '今天：'.date("Y-m-d");?>
</p>
<p>
<form action="" method="get" name="searchform">
用户过滤：<input type="text" size="20" name="username"><input type="submit" onclick="return searchform.username.value;" value="搜索">&nbsp;&nbsp;&nbsp;&nbsp;
输入组名：<input type="text" size="20" name="groupname"><input type="submit" onclick="return searchform.groupname.value;" value="列出组用户">
</form>

</p>
<script language="javascript">
<!--
var odd=true;
function fCheck(ii){
  	if(checkuser(ii))
  	{ return true;
	}else 
	{
		alert('请勾选用户');
		return false;
	}
}	

function checkuser(ii)
{ 
	var ii;
	var s=false;
	for(var i=1;i<=ii;i++)
	{ 
		var uid='userArray['+i+']';	 
		if(document.getElementById( uid ) )
		{
		     if ((document.getElementById( uid ).checked)){
			s=true;
			break;
		    }
		} 
	
	}
	return s;
}
function selall(ii)
{
	var ii;
	for(var i=1;i<=ii;i++)
	{ 
		var uid='userArray['+i+']';	 
		if(document.getElementById( uid ) )
		{
			if(odd)
			{
				document.getElementById( uid ).checked = 'true';
			}else
			{
				document.getElementById( uid ).checked = '';
			}
		}
	}
	if(odd){odd=false;}
	else odd=true;
}
-->
</script>
<?php
include('../../../config.inc');
include('../include/dbconnect.php');
$para='';
$user=trim(mysql_real_escape_string($_GET['username']));
$group=trim(mysql_real_escape_string($_GET['groupname']));
if(!empty($user))
{
	$para=" where user_name like '%$user%' ";
}
$query="select user_id,user_name,full_name,expire,infotimes from svnauth_user  $para order by expire ASC";
if(!empty($group))
{
	if(!empty($para))$para=" and user_name like '%$user%' ";
	$query="select svnauth_user.user_id,user_name,full_name,expire,infotimes from svnauth_user,svnauth_group,svnauth_groupuser where svnauth_group.group_name = '$group'  and svnauth_group.group_id=svnauth_groupuser.group_id and svnauth_groupuser.user_id=svnauth_user.user_id $para order by expire ASC";
}
$result = mysql_query($query);
	$ii=mysql_num_rows($result);
	echo  <<<SCMBBS
	<form method="post" action="user_expire.php" name='userform' onsubmit="return fCheck($ii)">	
		<table class='subtitle'>
	   <tr>
	 <td><input type=button value='全选' onclick="selall($ii)"/></td><td width=180>&nbsp;</td><td>用户有效期:<input type=text name='expire' value='14'/>天</td><td><input name="action" type=submit value='重设有效期' onclick="return confirm('确实要重设有效期吗？注意：有效期过后其用户名及权限信息会被删除。开始前请确保邮件发送功能正常，否则用户将得不到激活通知。');"/></td><td><input name="action" type='submit' value='到期不通知' onclick="return confirm('用户到期后将被无通告地删除，你确认吗？');"/></td>
	   </tr>
	</table>
	
	<table class=detail cellpadding=5px>
	  <tr class=title>
	     <td></td><td>用户名</td><td>有效期</td><td>状态</td>
	  </tr>
SCMBBS;
$i=0;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		//定义行的颜色相隔
				if ($tr_class=="trc1"){
					$tr_class="trc2";
				}else
				{			
					$tr_class="trc1";
				}
		$user_id=$row['user_id'];
		$user_name=$row['user_name'];
		$full_name=$row['full_name'];
		$status=$row['infotimes'];
		$expire=$row['expire'];		$i++;
		echo "<tr class=$tr_class><td><input  name=\"userArray[$i]\"  id=\"userArray[$i]\"  value=\"$user_id\" type=checkbox></td>
		<td>$user_name($full_name)</td><td>$expire</td><td>$status</td></tr>";
		
	}
	echo "</table>";
?>

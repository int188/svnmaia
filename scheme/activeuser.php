<?php
include('../include/charset.php');
//本单元的作用：激活用户，并重置用户有效期\提醒次数重置为0；
//处理：验证用户输入连接是否正确，如果正确，则处理之。
include('../include/basefunction.php');
include('../../../config.inc');
include('../config/config.php');
echo "<h3>用户信息确认与续订</h3>";
	$user=$_GET['u'];
	$email=$_GET['email'];
	$uid=$_GET['uid'];
	$sig0=$_GET['sig'];
	$action=$_GET['action'];
	$para=array($user,$email,$uid);
	$sig=keygen($para);
	if($sig != $sig0)
	{
		echo "<h3>此激活连接不存在！请确认。</h3>";
		exit;
	}
	$hidden_str="<input type=hidden name='email' value='$email'><input type=hidden name='sig' value='$sig'>";
include('../include/dbconnect.php');
if (mysql_select_db(DBNAME))
{
	if($action == 'actived'){
		if($_GET['sure']=='确认续订')
		{
		$fullname=mysql_real_escape_string($_GET['fullname'],$mlink);
		$email_n=mysql_real_escape_string($_GET['email_n'],$mlink);
		$staff_no=mysql_real_escape_string($_GET['staff_no'],$mlink);
		$department=mysql_real_escape_string($_GET['department'],$mlink);
		$expire=date("Y-m-d" , strtotime("+$user_t day"));
		$query="update svnauth_user set full_name=\"$fullname\",email=\"$email_n\",staff_no=\"$staff_no\",department=\"$department\",expire=\"$expire\",infotimes=0 where user_id=$uid";
		mysql_query($query) or die('<strong>激活失败:</strong>'.mysql_error());
		echo " <script>window.alert(\"激活成功！\")</script>";
		echo "<h3>激活成功！返回<a href='/'>svn</a></h3>";
		echo " <script>self.close();</script>";
		exit;
		}else
			if($_GET['sure']=='我已不需要，删吧')
			{
				$query="delete from svnauth_user where user_id=$uid";
				mysql_query($query);
				echo "<h3>删除成功！请关闭本页。</h3>";
				exit;
			}
		
	}

	$query="select * from svnauth_user where user_id=$uid";
	$result = mysql_query($query); 			
	$exist=false;
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$exist=true;
				$user_id=$row['user_id'];
				$user_name=$row['user_name'];
				$full_name=$row['full_name'];
				$staff_no=$row['staff_no'];
				$department=$row['department'];
				$email_n=$row['email'];
				$expire=$row['expire'];
				$tb_str="
			<tr><td><input type=hidden name='uid' value=$user_id>	
				 <input type=text readonly style='background:#ece9d8;' name='u' value=$user_name ></td>			 			 <td><input type=text name='fullname' value=$full_name></td>
				 <td><input type=text name='staff_no' value=$staff_no></td>
				  <td><input type=text name='department' value=$department></td>
				 <td><input type=text  name='email_n' value=$email_n></td></tr>

";
	}
	if(!$exist)$tb_str='<tr><td>该用户已被删除！</td></tr>';

			

}
$exp=date('Y-m-d' , strtotime('+2 week')); 
if($expire>$exp)
{
		echo " <script>window.alert(\"你已激活过！你的用户无需再次激活！\")</script>";
		echo "<h3>你已激活过！返回<a href='/'>svn</a></h3>";
		echo " <script>self.close();</script>";
		exit;
}
?>
<p>
<strong>说明：</strong>您的svn用户有效期至：<?php echo $expire ?>，过期用户名将被自动删除，如果您需要继续使用svn，请确认如下信息，并点击确认续订；
<br>如果您已不需要使用svn，请关闭本窗口。
<div style='text-align:center';>
<form method="get" action="">

		<fieldset>

		<legend>确认用户信息</legend>

		<input type=hidden name='action' value='actived'>

		<table  cellspacing='1' cellpadding='0' width='70%' border='0' >

		<tr><th>用户名</th><th>真实姓名</th><th>工号</th><th>部门</th><th>邮件</th></tr>

<?php echo $tb_str.$hidden_str;?>
	</table>

		<table style="position:relative;top:20px" >

		<tr><td><input name='sure' title='birth' style="width:80" type=submit value="确认续订"/></td><td><input  name='sure'  type=submit value='我已不需要，删吧' onclick="return confirm('你选择了删除此用户名，确认么？'); "/></td></tr>

	</table>

		</fieldset></form>

</div>

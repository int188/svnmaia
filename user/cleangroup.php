<?php
session_start();
include("../include/charset.php");
include('../../../config.inc');
include('../include/dbconnect.php');
if( $_SESSION['role'] !="admin")
{
	echo "无权操作！";
	exit;
}
if(!empty($_POST['gArray']))
{
	$dirArray=$_POST["gArray"];
	foreach($dirArray as $gid)
	{
		$gid= trim($gid);
		if(is_numeric($gid))
		{
			$query="delete from svnauth_group where group_id=$gid";
			mysql_query($query);
		}
	}
}
?>
<style type='text/css'>
.title{background: #007ED1 url(../img/bg.png)  100% 100%;font-size:11pt;color:white;}
.subtitle{background: #007ED1;color:white;}
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt}
.detail{width:680px}
</style>
<strong>说明：</strong>下面列出了成员为空的空权限组，过多的权限组将影响性能，建议您删掉冗余组。
<form method="post" action="" name='dirform' onsubmit="return fCheck()">	
	<table class='subtitle'>
	   <tr>
	  <td><input type=button value='全选' onclick="selall()"/></td><td width=280>&nbsp;</td><td>操作:<input name="action" type='submit' value='删除' onclick="return confirm('将删除该空组，你确认吗？');"/></td>
	   </tr>
	</table>
	
	<table class=detail cellpadding=5px>
	  <tr class=title>
	     <td></td><td>组名（空组）</td>
	  </tr>

<?php
$query="select group_name,group_id from svnauth_group order by group_name";
$result = mysql_query($query);
$i=0;
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {	
	$gid=$row['group_id'];
	$gname=$row['group_name'];
	$sql="select user_name from svnauth_groupuser,svnauth_user where svnauth_user.user_id=svnauth_groupuser.user_id and group_id=$gid group by user_name";
	$result_u=mysql_query($sql);
	$num=mysql_num_rows($result_u);
	if($num==0)
	{

		if ($tr_class=="trc1"){
			$tr_class="trc2";
		}else
		{			
			$tr_class="trc1";
		}

		$i++;
		echo"<tr class=$tr_class><td><input  name=\"gArray[$i]\"  id=\"gArray[$i]\"  value=\"$gid\" type=checkbox></td>
			<td><a href='./viewgroup.php?gid=$gid&grp=$gname&fromurl=cleangroup.php'>$gname</td></tr>";

	}
}

?>
</table>
	<table class='subtitle'>
	   <tr>
	  <td><input type=button value='全选' onclick="selall()"/></td><td width=280>&nbsp;</td><td>操作:<input name="action" type='submit' value='删除' onclick="return confirm('将删除该空组，你确认吗？');"/></td>
	   </tr>
	</table>

</form>
<script language="javascript">
<!--
var odd=true;
var ii=<?php echo $i ?>;
	
function selall()
{
	for(var i=1;i<=ii;i++)
	{ 
		var uid='gArray['+i+']';	 
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

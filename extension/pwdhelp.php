<?php
include('../include/charset.php');
?>
<html>
<head>
<title>svn用户权限管理系统---密码修改</title>
<style type="text/css">
form{margin:100px 170px;padding:20px;}
.in{width:140px}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:400px;}
 legend{color:#1E7ACE;padding:3px 20px;border:2px solid #A4CDF2;background:#FFFFFF;}
.tp{margin:40px 40px 0px 0px;}
</style>
</head>
<body>
</p>
  <form id="chpasswd" method="post" action="pwdch.php" onSubmit="return pcheck()">
<fieldset>
	<legend>修改svn密码</legend>
	<table>
		<tr>
			<td width="122" height="19">用户名：</td>
			<td height="19"><input type="text" name="username" size="20" > *</td>
  	</tr>

		<tr>
			<td width="122" height="19">原密码：</td>
			<td height="19"><input type="password" name="oldpasswd" size="20"> * </td>
	</tr>
	<tr>
		<td width="122">新密码：</td>
		<td><input type=password name="newpasswd" size="20"> *		</td>
	</tr>
	<tr>
		<td width="122">确认新密码：</td>
		<td><input type=password name="newpaswd0" size="20"> *</td>
	</tr>
	</table>
	
	
	<table border="0" width="84%" id="table2">
	<tr>
		<td width="104"><input type=submit value="确定" ></td>
		<td><input type=reset value="取消"></td>
		<td><a href='topwd.php'>找回密码</a></td>
	</tr>
</table>
</fieldset>
</form>
<script language="javascript">
	<!--
function pcheck(){

   if( chpasswd.oldpasswd.value =="") 
 {
       alert("\请输入原密码 !");
       chpasswd.oldpasswd.focus();
       return false;
  }

   if( ! isPassword( chpasswd.newpasswd.value ) )
   {
        alert("\请重新输入密码,密码由至少6个英文字母或数字组成 !"); 
        chpasswd.newpasswd.select();
        chpasswd.newpasswd.focus();
        return false;
   }
  if( chpasswd.newpaswd0.value =="" )
  {
      alert("\请输入密码确认 !");
      chpasswd.newpaswd0.select();
      chpasswd.newpaswd0.focus();
      return false;
  }
  if(  chpasswd.newpaswd0.value != chpasswd.newpasswd.value ) {
     alert("\两次密码输入不一致 !");
     chpasswd.newpasswd.focus();
     return false;
  }

function isPassword( password )
  {
     return /^[\w\W]{6,20}$/.test( password );
  }
}  


	-->
</script>
</body>
</html>

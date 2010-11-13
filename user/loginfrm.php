<?php
include('../include/charset.php');
?>
<html>
<head>
<title>svn用户权限管理系统---用户登陆</title>
<style type="text/css">
form{margin:100px 170px;padding:20px;}
.in{width:140px}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:400px;}
 legend{color:#1E7ACE;padding:3px 20px;border:2px solid #A4CDF2;background:#FFFFFF;}
.tp{margin:40px 40px 0px 0px;}
</style>
</head>
<body>
<form name="loginform" method="post" action="login.php?action=login">
<fieldset>
<legend>登陆svn用户系统</legend>
<div class='tp'>
	<table border="0" width="100%" id="table1">
		<!-- MSTableType="nolayout" -->
		<tr>
			<td width="68">用户名：</td>
			<td ><input type="text" name="username" class='in' >　</td>
		</tr>
		<tr>
			<td>密码：</td>
			<td ><input type=password name="pswd"  class='in' >&nbsp;
			<a href="../extension/topwd.php">找回密码</a></td>
		</tr>
	</table>
</div>
<div class='tp' >
　<table border="0" width="100%" id="table2">
		<!-- MSTableType="nolayout" -->
		<tr>
			<td width="105"><input type=submit value="确定" style="width:80"></td>
			<td><input type=reset value="取消" style="width:80"> </td>
            <td><a href="reg_user.php">注册</a></td>
		</tr>
	</table>
</div>
</fieldset>
</form>
<script>loginform.username.focus();</script>
</body>
</html>

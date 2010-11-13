<?php header("Cache-Control: no-cache"); 
include('../../include/charset.php');
/*
   文件名：index.php
   功能：密码帮助的修改密码界面
   输入：用户名、加密字符串
   输出：签名、用户名、新密码
   逻辑：根据用户名和加密字符串，寻找pwdurl文件，判断是否存在相同信息
         没有则显示无效链接，给出返回主页的url。
				 有，则显示修改密码界面，传入给chpasswd.php；生成user和某字符串的加密签名参数
*/
include('../../../../config.inc');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("数据库链接失败！请联系管理员");
if (!mysql_select_db(DBNAME))
{
  exit;
}
$user=urldecode(trim($_GET["u"]));
$para_str=stripslashes(trim($_GET["c"]));
$ss=base64_decode(stripslashes(trim($_GET["ss"])));
$nt=microtime();
$nt=str_replace(" ","",$nt);
$nt=str_replace("0.","",$nt);
$nt=substr($nt,6);
$nt=$nt-substr($ss,6);
if(($nt>3600*23)||($nt<0))
{
	echo "链接已过期！请重新获取激活链接。";
	exit;
}
//验证输入是否正确
$trueurl=false;
$user=mysql_real_escape_string($user,$mlink);
$para_str=mysql_real_escape_string($para_str,$mlink);
$query="select username from svn_chpwd where username=\"$user\" and hexkey=\"$para_str\";";
$result =mysql_query($query);
if (mysql_num_rows($result) == 0){
	$trueurl=false;
}else
  $trueurl=true;
//没有找到，这显示无效链接
if (!$trueurl)
{
	echo "<font color=red><h2>出错啦！无效链接</h2></font>";
	echo "<p>点击<a href=/>返回主页</a>";
	echo "<p><IMG  src='../../img/waiting.gif'>";
	exit;
}

//有，则生成签名，显示修改密码界面。
$sig=md5($para_str.$user.SECRET_KEY);
?>
<style type='text/css'>
 fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:60%}
 legend{color:#1E7ACE;padding:3px 20px;border:1px solid #A4CDF2;background:#FFFFFF;}
</style>
<h1>svn密码帮助，修改密码</h1>
<form name=regform action="./chpasswd.php" method="post"  onSubmit="return fCheck()">
	<fieldset>
		<legend>重置密码</legend>
<table >
	<tr>
		<th>用户名：</th>
		<td><input type=text readonly name=user value="<?php echo $user ?>">
			<input name=sig type=hidden value=<?php echo urlencode($sig) ?>>
		<input name=para type=hidden value=<?php echo $para_str ?>>
		<td>
	</tr>
	<tr>
		<th>请输入新密码：</th>
		<td><input name=pswd type=password></td>
	</tr>
	<tr>
		<th>请再输入一次：</th>
		<td><input name=pswd0 type=password></td>
	</tr>
	<tr>
		<td><input type=reset value=取消></td>
		<td><input type=submit value=提交></td>
	</tr>
</table>
</fieldset>
</form>


<script language="javascript">
<!--
function fCheck(){
	
	if( ! isPassword( regform.pswd.value ) )
   {
        alert("\请重新输入密码,密码由至少6个英文字母或数字组成 !"); 
        regform.pswd.select();
        regform.pswd.focus();
        return false;
   }
  if( regform.pswd0.value =="" ) {
      alert("\请输入密码确认 !");
      regform.pswd0.select();
      regform.pswd0.focus();
      return false;
  }
  if( regform.pswd0.value != regform.pswd.value ) {
     alert("\两次密码输入不一致 !");
     regform.pswd.focus();
     return false;
  }
  function isPassword( password )
  {
     return /^[\w\W]{6,20}$/.test( password );
  }
}
-->
</script>

<?php
	include('../config/config.php');
include('../include/charset.php');
?>
<!--
Author:lixuejiang
Site:http://www.scmbbs.com
Date:2009-02-19
-->
<html>
<head>
  <title>svn用户注册</title>
</head>
<style type='text/css'>
h1{text-align:center;}
p{margin-left:40px}
div{margin:15px}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#FFFFFF;width:60%}
 legend{color:#1E7ACE;padding:3px 20px;border:2px solid #A4CDF2;background:#FFFFFF;}
 a{color:green;text-decoration:underline;}
.m{color:red;font-size:12px}
td{height:35px;}
.lb{width:122px;text-align:right;}
</style>
<body>

  <p>
  <noscript><strong>您的浏览器不支持script脚本。<br>用户注册功能将不能正常使用！<br></noscript>
  	<form name='regform' action=reg.php method=post onsubmit="return fCheck()">
	<fieldset>
  	 <legend>注册subversion用户</legend>
   	<table>
	<tr>
		<td  class='lb'>用户名：</td>
		<td ><input type="text" name="username" size="20" onblur="addemail()"> * <span class='m'>请与您的邮箱前缀保持一致，由字母组成</span></td>
 	</tr>
 
	<tr>
		<td  class='lb'>密码：</td>
		<td ><input type="password" name="passwd" size="20"> * </td>
	</tr>
	<tr>
		<td  class='lb'>确认新密码：</td>
		<td><input type=password name="passwd0" size="20"> *</td>
	</tr>
	<tr>
		<td  class='lb'>中文姓名：</td>
		<td ><input type="text" name="fullname" size="20" > * <span class='m'>请填写真实姓名</span></td>
 	</tr>
 	<tr>
		<td  class='lb'>工号：</td>
		<td ><input type="text" name="staff_no" size="20" >  <span class='m'>实习生可不填</span></td>
 	</tr>	
 	<tr>
		<td  class='lb'>部门：</td>
		<td ><input type="text" name="department" size="20" >  <span class='m'></span></td>
	</tr>	
		<tr>
		<td  class='lb'>电子邮件：</td>
		<td ><input type="text" name="email" size="20" > </td>
 	</tr>	
 </table>
	<table border="0" width="84%" id="table2">
	<tr>
		<td width="104"><input type=button value="提交" onclick="return tCheck()"></td>
		<td><input type=reset value="取消"></td>
		<td align=right><a href='./viewuser.php'>修改用户</a></td>
		</tr>
	</table>
</fieldset>
	</form>

</body>
</html>
<script language="javascript">
	<!--
	

function addemail()
{
	regform.email.value=regform.username.value+"<?php echo $email_ext;?>";
}
 function fCheck(){
 		 
 
 if( regform.username.value =="") 
 {
       alert("\用户名不能为空!");
       regform.username.focus();
       return false;
  }
 if( regform.passwd.value =="") 
 {
       alert("\密码不能为空!");
       regform.passwd.focus();
       return false;
  }

   if( ! isPassword( regform.passwd.value ) )
   {
        alert("\请重新输入密码,密码由至少6个英文字母或数字组成 !"); 
        regform.passwd.select();
        regform.passwd.focus();
        return false;
   }
  if( regform.passwd0.value =="" )
  {
      alert("\请输入密码确认 !");
      regform.passwd0.select();
      regform.passwd0.focus();
      return false;
  }
  if(  regform.passwd0.value != regform.passwd.value ) {
     alert("\两次密码输入不一致 !");
     regform.passwd.focus();
     return false;
  }
 if( regform.fullname.value =="" )
  {
      alert("\请输入您的真实姓名!");
      regform.fullname.select();
      regform.fullname.focus();
      return false;
  }
function isPassword( password )
  {
     return /^[\w\W]{6,20}$/.test( password );
  }
  return true;
}  
function tCheck()
{
	if(!fCheck())return false;
	loadTip();
	return true;
}

//用于创建XMLHttpRequest对象
function createXmlHttp() {
    //根据window.XMLHttpRequest对象是否存在使用不同的创建方式
    if (window.XMLHttpRequest) {
       xmlHttp = new XMLHttpRequest();                  //FireFox、Opera等浏览器支持的创建方式
    } else {
       xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE浏览器支持的创建方式
    }
}
function displayTip(content) {
	alert(content);
}
//从服务器加载关键词的详细信息
function loadTip() {
    if(!fCheck())return false;
   var username="username="+regform.username.value
      +"&passwd="+regform.passwd.value+"&passwd0="+regform.passwd0.value
      +"&fullname="+regform.fullname.value
      +"&staff_no="+regform.staff_no.value
      +"&email="+regform.email.value;
    createXmlHttp();                                //创建XMLHttpRequest对象
    xmlHttp.onreadystatechange = loadTipCallBack;   //设置回调函数
    xmlHttp.open("POST", "./reg.php", true);
    xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlHttp.send(username);
}

//获取查询选项的回调函数
function loadTipCallBack() {
    if (xmlHttp.readyState == 4) {
        displayTip(xmlHttp.responseText);           //显示加载完毕的详细信息
        if(xmlHttp.responseText=="用户注册成功！")regform.reset();
        regform.username.focus();
        
    }
}

	-->
</script>


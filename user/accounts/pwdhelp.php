<?php
include('../../include/charset.php');
?>
<html>
<head>
  <title>找回svn密码向导</title>
</head>
<style type='text/css'>
div{margin:15px}
fieldset{border:solid 1px gray;}
</style>
<body>
   <h2>找回密码帮助</h2>
   <?php
//校验调用合法性。必须通过seraph认证用户才可以调用本文件。
$nt=microtime();
$nt=str_replace(" ","",$nt);
$nt=str_replace("0.","",$nt);
$nt=substr($nt,6);
$ss=$_GET['ss'];
$nt=$nt-substr($ss,6);
if($nt>3600*2)
{
	echo "链接已过期！";
	exit;
}
$sig1=$_GET['sig'];
$addr=$_SERVER['REMOTE_ADDR'];
include('../../../../config.inc');
include('../../config/config.php');
$sig=md5($ss.SECRET_KEY.$addr);
if($sig1 != $sig)
{
	echo "非法调用！";
	exit;
}
//获取cookie，提取email信息。
$cookie=explode('&',$_COOKIE['CNSSO']);
foreach($cookie as $name)
{
	if(stristr($name,'userid='))
	{
		$uEmail=str_replace('userid=','',$name).$email_ext;
		break;
	}
}
?>
   <form action=./sendmail.php name=pwdform method=post onSubmit="return tCheck()">
   	<fieldset>
   <div id='inputblock'>
   <h4>请在下列表格中输入您的svn用户名，系统会根据用户名将密码重置链接发送到您的邮箱中<br>请注意查收邮件。</h4>
   		
   <table>
   <tr><td>请输入svn用户名：</td><td><input type=text name=username></td></tr>
   <tr><td>&nbsp; </td></tr>
   <tr><td><input type=reset value="取消"></td><td><input type=button value="下一步" onclick="loadTip()"></td></tr>
   </table>  
  </div>
  <div id='confirmblock' style="display:none;">
  	<h4>请确认如下信息：</h4>
  	<table>
  		<tr><td>您的svn用户名：<input type=text readonly id='user'></td></tr>
  		<tr><td>公司邮箱：<input type=text readonly id='email' style="width:250px"></td></tr>
  		<tr><td>您的新密码将被发送到该邮箱中，操作前请仔细确认<font color=red>邮箱地址</font>！
  			<br>如果邮箱地址不对，请到<a href='../viewuser.php' target=_blank>svn用户系统</a>登陆修改，或者联系我们修改。</td></tr>
  		<tr><td>&nbsp; </td></tr>
  		<tr><td><input type=button value="上一步" onclick='turnback()'>&nbsp;<input type=button style="width:80;margin-left:180px" value="确认" onclick="return tCheck()"></td></tr>  		
  	</table>
  </div>
  </fieldset>
   </form>
</body>

</html>
<script language="javascript">
<!--
function turnback(){
	window.location.href = window.location.href;	
}
function fCheck(){
	
  if( pwdform.username.value =="" ) {
      alert("\请输入用户名 !");
      pwdform.username.select();
      pwdform.username.focus();
      return false;
  }
  return true;
}
function tCheck()
{
	if(!fCheck())return false;
	if(document.getElementById('email').value == '用户不存在！')
	{
		alert('该用户名不存在，请确认！');
		return false;
	}
	if(document.getElementById('email').value != "<?php echo $uEmail ?>")
	{
	//	alert('该邮箱地址与您真实邮箱地址不符！');
	//	return false;
	}
	pwdform.submit();
	return true;
}
//将详细信息的具体内容写入tipDiv中
function displayTip(content) {
    document.getElementById('confirmblock').style.display='';
    document.getElementById('inputblock').style.display='none';
    document.getElementById('user').value =pwdform.username.value;
    document.getElementById('email').value = content;
    
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

//从服务器加载关键词的详细信息
function loadTip() {
    if(!fCheck())return false;
    displayTip("正在加载……");                  //显示“正在加载……”提示信息

    createXmlHttp();                                //创建XMLHttpRequest对象
    xmlHttp.onreadystatechange = loadTipCallBack;   //设置回调函数
    xmlHttp.open("GET", "./getusers.php?username=" + pwdform.username.value +"&"+Math.round(Math.random()*100), true);
    xmlHttp.send(null);
}

//获取查询选项的回调函数
function loadTipCallBack() {
    if (xmlHttp.readyState == 4) {
        displayTip(xmlHttp.responseText);           //显示加载完毕的详细信息
    }
}

-->
</script>

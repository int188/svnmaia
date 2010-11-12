<?php
include('../../include/charset.php');
error_reporting(0);
$url=$_GET['url'];
?>
<html>
<head>
  <title>svn权限申请向导</title>
</head>
<style type='text/css'>
div{margin:15px;}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#DFE8F6;width:70%}
 legend{color:#AA0000;font-weight:bold;padding:3px 20px;border:2px solid #A4CDF2;}
</style>
<body>

<form action=./sendmail.php name=pwdform method=post  onSubmit="return tCheck()">
   	<fieldset>
	<legend>svn权限申请</legend>
   <div id='inputblock'>
   		
   <table valign=top>
 <tr><td>申请的url:<input type=text name='wurl' size='45' value="<?php echo $url; ?>" onBlur="checkurl();"></td>
       <td>&nbsp;&nbsp;权限:<select name="wpriv"><option value='r' label='只读'>只读</option>
<option value='w' label='读写'>读写</option>
</select></td></tr>
  <tr><td colspan=3><label id='urltip' style='color:red;font-size:12px;'></label></td></tr>
   <tr><td colspan=3>申请理由:</td></tr>
   <tr><td colspan=3> <textarea id="comment" name="comment" cols="65" rows="5"></textarea></td></tr>
  <tr><td colspan=3>你的svn用户名：<input type='text' name='username' size='14'  onBlur="loadTip();">* &nbsp;&nbsp;<a href='../../user/reg_user.php' target=_blank>注册</a>&nbsp;&nbsp;&nbsp; 邮箱：<input type=text name='email' id='email'></td></tr>
<tr><td colspan=3><label id='unametip' style='color:red;font-size:12px;'></label></td></tr>
   <tr align=center bgcolor='#B6C6D6'><td colspan='3'><input type=button value="提交" style='width:80px'  onclick="return tCheck()"></td></tr>
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
pwdform.wurl.focus();
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
	if(document.getElementById('unametip').innerHTML == '用户不存在！')
	{
		alert('该用户名不存在，请确认！');
		return false;
	}
	if(document.getElementById('urltip').innerHTML == 'URL不存在!')
	{
		alert('该url不正确，请确认！');
		return false;
	}
	if(document.getElementById('email').value != "<?php echo $uEmail ?>")
	{
	//	alert('该邮箱地址与您真实邮箱地址不符！');
	//	return false;
	}
  	if( pwdform.wurl.value =="" )return false;
	pwdform.submit();
	return true;
}
//将详细信息的具体内容写入tipDiv中
function displayTip(content) {
	document.getElementById('unametip').innerHTML = ''; 
	document.getElementById('email').value = content;    
	if(content == '用户不存在！')document.getElementById('unametip').innerHTML = content;    
}
function displayUrlTip(content) {
    document.getElementById('urltip').innerHTML = content;    
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
    xmlHttp.open("GET", "../../user/accounts/getusers.php?username=" + pwdform.username.value +"&"+Math.round(Math.random()*100), true);
    xmlHttp.send(null);
}
function checkurl() {

    if( pwdform.wurl.value =="" )return false;
    displayUrlTip("正在检查url...");                  //显示“正在加载……”提示信息

    createXmlHttp();                                //创建XMLHttpRequest对象
    xmlHttp.onreadystatechange = loadurlCallBack;   //设置回调函数
    xmlHttp.open("GET", "./checkurl.php?wurl=" + pwdform.wurl.value +"&"+Math.round(Math.random()*100), true);
    xmlHttp.send(null);
}
function loadurlCallBack() {
    if (xmlHttp.readyState == 4) {
        displayUrlTip(xmlHttp.responseText);           //显示加载完毕的详细信息
    }
}
//获取查询选项的回调函数
function loadTipCallBack() {
    if (xmlHttp.readyState == 4) {
        displayTip(xmlHttp.responseText);           //显示加载完毕的详细信息
    }
}

-->
</script>

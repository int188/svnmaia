<?php
session_start();
include('../include/charset.php');
include('../../../config.inc');
include('../include/dbconnect.php');
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	exit;
}
if (($_SESSION['role'] == 'admin')or($_SESSION['role'] == 'diradmin')){
	$extstr="<tr><td colspan=3>通知到：<input type=text name='notelist' id='notelist' size=35 value='多用户之间用分号或逗号分割；不填则只通知到自己。' onclick=\"cleantip();\"></td></tr>";
	$adminflag='admin=true;';
}
error_reporting(0);
$url=$_GET['url'];
?>
<html>
<head>
  <title>监控svn代码提交</title>
</head>
<style type='text/css'>
div{margin:15px;}
.tb1{ cellspacing:1; cellpadding:0; width:70%; border:0; background:#aaa}
.tb1 tr{background:#ecf0e1;}
fieldset{border:2px solid #A4CDF2;padding:20px;background:#DFE8F6;width:70%}
 legend{color:#AA0000;font-weight:bold;padding:3px 20px;border:2px solid #A4CDF2;}
</style>
<body>

   <form action=./insertmonitor.php name=urlform method=post onSubmit="return tCheck()">
   	<fieldset>
	<legend>增加svn代码监控</legend>
   <div id='inputblock'>
   		
   <table valign=top>
   <tr><td colspan=3>监控的svn url:</td><tr><td><input type=text name='wurl' size='65' value="<?php echo $url ?>" onBlur="checkurl();"></td>
<td><input type=button value="提交" style='width:80px'  onclick="return tCheck()">&nbsp;&nbsp;&nbsp;<a href='http://www.scmbbs.com/maia' target=_blank>want more</a></td></tr>
  <tr><td colspan=3><label id='urltip' style='color:red;font-size:12px;'></label></td></tr>
<?php echo $extstr;?>
   </table>  
  </div>
  </fieldset>
   </form>
<?php
//*********
//列出当前用户的监控列表
//*********
$u_ID=$_SESSION['uid'];
if (($_SESSION['role'] == 'admin'))
{
	$para="monitor_user.user_id=$u_ID";
	$title="<h2>我的订阅/<a href='?s=all'>查看所有订阅</a></h2>";
	if ('all'==$_GET['s'])
	{
		$para='1=1';
		$title="<h2><a href=?reflash>我的订阅</a>/查看所有订阅</h2>";
	}
}else{
	$title="<h2>我的订阅</h2>";
	$para="monitor_user.user_id=$u_ID";
}
	
$query="select url,version,id from monitor_url,monitor_user where $para";
$result=mysql_query($query);
$num_rows = mysql_num_rows($result);
if($num_rows > 0)
{
	echo $title;
	echo "<table class='tb1'>
	<tr><td>svn地址</td><td>当前版本</td><td>操作</td>";
}
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$url=$row['url'];
	$id=$row['id'];
	$ver=$row['version'];
	echo "<tr><td>$url</td><td>$ver</td><td><a href='monitor_modify.php?id=$id&action='del'>删除</a></td></tr>";
}
?>
</body>

</html>
<script language="javascript">
<!--
	firstflag=true;
<?php echo $adminflag; ?>
function turnback(){
	window.location.href = window.location.href;	
}

function tCheck()
{
	if( urlform.wurl.value =="" )return false;
	if(document.getElementById('urltip').innerHTML == 'URL不存在!')
	{
		if (! confirm('此URL可能不存在或者为外部服务器的，您确实要添加此监控吗？'))return false;
	}
	if(firstflag && admin)document.getElementById('notelist').value ='';
	urlform.submit();
	return true;
}
function cleantip()
{
	if(firstflag){
		document.getElementById('notelist').value ='';
		firstflag=false;
	}
}
function displayUrlTip(content) {
    if(content != 'URL不存在!')content='';
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


function checkurl() {

    if( urlform.wurl.value =="" )return false;
    displayUrlTip("正在检查url...");                  //显示“正在加载……”提示信息

    createXmlHttp();                                //创建XMLHttpRequest对象
    xmlHttp.onreadystatechange = loadurlCallBack;   //设置回调函数
    xmlHttp.open("GET", "./autopriv/checkurl.php?wurl=" + urlform.wurl.value +"&"+Math.round(Math.random()*100), true);
    xmlHttp.send(null);
}
function loadurlCallBack() {
    if (xmlHttp.readyState == 4) {
        displayUrlTip(xmlHttp.responseText);           //显示加载完毕的详细信息
    }
}


-->
</script>

<?php
   session_start();
include('../include/charset.php');
   error_reporting(0);
?>
<style type="text/css">
.focus{background:url(../img/u2.png);}
.bottom{background:#934caa;height:4px;}
.g2{background:url(../img/u1.png);}
.g2 a{color:black;font-weight:bold;text-decoration:none;}
.focus a{color:white;font-weight:bold;text-decoration:none;}
.t1{font-size:12px;}
.t1 a{color:green;text-decoration:underline;}
#menu{position:absolute;
right:0px;top:20px;
* right:20px;
* top:25px;
text-align:center;
}
ul
{
float:right;
padding:0;
margin:0;
list-style-type:none;
font-size:12px;
}
li a
{
float:right;
width:7em;
text-decoration:none;
color:#777;
background-color:#fff;
padding:0.2em;
border:1px solid #ccc;
}
li a:hover {background-color:#ff3300}
li {display:inline;}
li ul{
  display:none;
}
li:hover ul, li.over ul{display:block;}
</style>
<table>
<tr>
<td width="20%"><map name="mymap"><area href='http://www.scmbbs.com' target='_blank' shape=rect coords="0,0,60,60"></map>
<img src='../img/logo.gif' border='0' usemap=#mymap></td>
<td width="80%">
<table align="center" width="90%" border="0"  cellspacing="0"  cellpadding="4" class='g2' >
<tr>
<td align="center" id='usrf'  width="14%" ><a href='../user/viewuser.php' target='main1' onclick="setfocus('usrf');">用户管理</a></td>
<td align="center" id='grpf'  width="14%" ><a href='../user/viewgroup.php' target='main1' onclick="setfocus('grpf');">组管理</a></td>
<td align="center" id='privf' width="14%"><a href='../template/index.htm' target='main1' onclick="setfocus('privf');">权限管理</a></td>
<td align="center" id='stf' width="12%" ><a href='../config/index.php' target='main1' onclick="setfocus('stf');">设置</a></td>
<td align="center" id='toolf' width="12%" ><a href='../extension/index.php' target='main1' onclick="setfocus('toolf');">工具</a></td>

<td>
<div ALIGN="right" class='t1'>欢迎您，
  <?php 
     
      if (isset($_SESSION['username'])) 
      {echo $_SESSION['username']."！";
              echo "&nbsp;&nbsp;<a href=\"../user/logout.php\" target='_parent'>退出</a>" ;
      } else{ 
        echo "游侠！";echo "！&nbsp;&nbsp;<a href=\"../user/loginfrm.php\" target='_parent'>登录</a>" ;
      }
 ?>
&nbsp;&nbsp;<a href="/" target='_parent'>返回首页</a>
</div>
</td>
</tr>
<tr class='bottom'><td colspan=5></td></tr>
</table>
</td>
</tr></table>
<ul id='menu'>
<li><a href='http://www.scmbbs.com/cn/maia.php' target='_blank' style="width:40;">帮助</a>
  <ul>
     	<li><a href='http://www.scmbbs.com/cn/maia/2009/6/maia1.php' target='_blank'>关于</a></li>
	<li><a href='' target='_blank'>查询授权号</a></li>
	<li><a href='http://code.google.com/p/svnmaia/issues/list' target='_blank'>反馈</a></li>
  </ul>
</li>
</ul>

<hr width=90% color="#DBDDEC" noShade size=1>
<script language='javascript'>
	<!--

function setfocus(myid)
{
  //firefox
  document.getElementById(myid).setAttribute("class",'focus');
  if(myid != 'usrf')document.getElementById('usrf').setAttribute("class",'');
  if(myid != 'grpf')document.getElementById('grpf').setAttribute("class",'')
  if(myid != 'privf')document.getElementById('privf').setAttribute("class",'');
  if(myid != 'stf')document.getElementById('stf').setAttribute("class",'');
  if(myid != 'toolf')document.getElementById('toolf').setAttribute("class",'');
  //ie
    document.getElementById(myid).setAttribute("className",'focus');
  if(myid != 'usrf')document.getElementById('usrf').setAttribute("className",'');
  if(myid != 'grpf')document.getElementById('grpf').setAttribute("className",'');
  if(myid != 'privf')document.getElementById('privf').setAttribute("className",'');
  if(myid != 'stf')document.getElementById('stf').setAttribute("className",'');
  if(myid != 'toolf')document.getElementById('toolf').setAttribute("className",'');	
}	
startlist = function(){
 if(document.all && document.getElementById){
  navRoot=document.getElementById('menu');
  for(i=0;i<navRoot.childNodes.length;i++){
	node= navRoot.childNodes[i];
	if(node.nodeName=='LI'){
		node.onmouseover=function(){
			this.className+=" over";
		}
		node.onmouseout=function(){
			this.className=this.className.replace(" over","");
		}
	}
  }
 }
}
window.onload=startlist;
	-->
</script>

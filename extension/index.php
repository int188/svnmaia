<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['username'])){	
//	exit;
}
include('../include/charset.php');
if (($_SESSION['role'] !='admin')and($_SESSION['role'] !='diradmin'))
{
//	echo "您无权进行此操作！";
//	exit;
}
?>
<style type='text/css'>
.left{float:left;width:49%;border:1px solid lightpink;}
.right{float:right;width:49%;border:1px solid lightblue;}
br{clear:both;}
.st{margin-left:10px;line-height:30px;}
.ft{background:#B6C6D6;text-align:center;margin:20px 0 20px 0;}
</style>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<h2>工具</h2>
<br>
<div class='left'>
 <h3>权限控制与用户文件</h3>
 <div class='st'>
 <a href='./showaccess.php' target='_blank'>查看权限控制文件</a>
 <br><a href='../user/gen_passwd.php'>生成用户文件</a>
 <br><a href='../priv/gen_access.php'>生成权限控制文件</a>
 </div>
 <h3>计划任务</h3>
 <div class='st'>
 <a href='../scheme/scheme.php' target='_blank'>启动任务计划设置</a>
 <br><a href='../scheme/cleanuser.php'>发起用户清理计划</a>
 </div>
 <h3>初始化</h3>
 <div class='st'>
 <a href='../user/import_user.php' onclick="">从passwd文件导入用户密码</a>
 <br><a href='../priv/import_access.php' onclick="">重新导入access权限配置</a>
 </div>
 </div>
<div class='right'>
 <h3>用户工具</h3>
 <div class='st'>
  <a href='../extension/pwdhelp.php' target='_blank'>修改密码工具</a>
  <br><a href='../user/reg_user.php' target='_blank'>用户注册工具</a>
  <br><a href='../extension/topwd.php' target='_blank'>找回密码工具</a>
  <br><a href='../extension/svn_monitor.php' target='_blank'>监控svn代码提交</a>
 </div>
 <h3>权限工具</h3>
  <div class='st'>
<a href='./autopriv/setting.php'>自动审批权限设置</a>/<a href='./autopriv/viewrequest.php'>查看申请列表</a>
<br><a href='./autopriv/rtpriv.php' target='_blank'>权限申请</a>
<br><a href='../priv/checkDirPriv.php'>权限冗余校验与清理</a>
  </div>
<h3>自定义工具集</h3>
<div class='st'>
<?php
if(file_exists('./addon.ini'))
{
	$f=true;
	$url_array=parse_ini_file('./addon.ini',true);
	foreach($url_array as $comment=>$url_det)
	{
		$br='<br>';
		if($f)
		{
			$f=false;
			$br='';
		}
		if(!isset($url_det['url']))continue;
		$target=(isset($url_det['target']))?('target='.$url_det['target']):("");
		echo "$br <a href=".$url_det['url']." $target>$comment</a>\n";
	}
}
?>
</div>
</div>


<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['username'])){	
	exit;
}
include('../../include/charset.php');
if (($_SESSION['role'] !='admin'))
{
	echo "您无权进行此操作！";
	exit;
}
if(isset($_POST['flag']))
{
	$str="<?php\n";
	if($_POST['dir_admin_op'])$str.='$'."dir_admin_op='checked';\n";
	if($_POST['tosuper_op'])$str .='$'."tosuper_op='checked';\n";
	if($_POST['tolist_op'])
	{
		$str .= '$'."tolist_op='checked';\n";
		$str .= '$'."email_list='".$_POST['email_list']."';\n";
	}
	if($_POST['thenlist_op'])
	{
		$str .= '$'."thenlist_op='checked';\n";
		$str .= '$'."email_list2='".$_POST['email_list2']."';\n";
	}
	$str .="?>\n";
	$handle=fopen('./autopriv.conf','w+');
	if (fwrite($handle, $str) === FALSE) {
		$tmppath=realpath('./');
   		echo "<strong>Error:</strong>不能写入到文件 $tmppath/autopriv.conf ! 保存失败！";
 	}else echo "<font color=red>保存成功！</font>";
	fclose($handle);
}
if(file_exists('./autopriv.conf'))include('./autopriv.conf');
?>
<style type='text/css'>
.ipt{position:absolute;left:220px;clear:both;float:left;background:#ece9d8;width:250px;}
br{clear:both;}
.st{margin-left:10px;}
.st p{border:solid 1px;}
.ft{background:#B6C6D6;text-align:center;margin:20px 0 20px 0;}
.rt{position:absolute;left:480px;}
.rt2{position:absolute;left:520px;}
.sf{color:blue;font-size:10pt;CURSOR:pointer;background:#FFFFCC;}
.err{color:red;}
</style>
<link rel="stylesheet" href="../css/base.css" type="text/css">
<form method='post' action=''>
<fieldset>
<h3>权限自动审批设置</h3>
<div class='st'>
<input type='hidden' name='flag' value='1'>
<br><input type='checkbox' name='dir_admin_op' value='true' <?php echo $dir_admin_op ?> id='diradmin'><label for='diradmin'>发送到目录管理员审批权限。若无，则发送到超级管理员</label>
<br><input type='checkbox' name='thenlist_op' value='true' <?php echo $thenlist_op ?> id='thenlist'><label for='thenlist'>发送到目录管理员审批权限。无,则发送到如下列表：</label>
<input type=text name='email_list2' value="<?php echo $email_list2 ?>">
<br><input type='checkbox' name='tosuper_op' value='true' <?php echo $tosuper_op ?> id='superadmin'><label for='superadmin'>发送到超级管理员</label>
<br><input type='checkbox' name='tolist_op' value='true' <?php echo $tolist_op ?> id='tolist'><label for='tolist'>发送到如下列表：</label>
<input type=text name='email_list' value="<?php echo $email_list ?>">
</div>
<div class='ft'>
<input type='submit' value='提交保存'>
</div>
</fieldset>
<a href='viewrequest.php'>查看申请列表</a>
</form>


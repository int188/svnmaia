<?php
session_start();
 error_reporting(0);
include('../../include/charset.php');
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../../user/loginfrm.php\"',0)</script>"; 	
	exit;
}
include('../../../../config.inc');
include('../../include/dbconnect.php');
echo <<<HTML
<style type='text/css'>
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt
</style>
HTML;
echo "<table><tr><th>申请者</th><th>申请路径</th><th>申请权限</th><th>申请日期</th><th>处理人</th><th>处理结果</th></tr>";
$query="select `username`,`repository`,`path`,`permission`,`rtdate`,`ops`, `optype` from rt_svnpriv ORDER BY id DESC limit 200;";
$result=mysql_query($query);
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$repos=$row['repository'];
	$path=$row['path'];
	$permission=$row['permission'];
	$rtdate=$row['rtdate'];
	$ops=$row['ops'];
	$optype=$row['optype'];
	$un=$row['username'];
	$path=$repos.$path;
	switch($permission){
	case 'w':
		$permission='write';
		break;
	case 'r':
		$permission='readOnly';
		break;
	case 'n':
		$permission='none';
		break;
	}
	($tr_class=="trc1")?($tr_class="trc2"):($tr_class="trc1");	
	echo "<tr class=$tr_class><td>$un</td><td>$path</td><td>$permission</td><td>$rtdate</td><td>$ops</td><td>$optype</td></tr>";
}
echo "</table>";


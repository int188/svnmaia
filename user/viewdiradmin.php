<?php
session_start();
 error_reporting(0);
include('../include/charset.php');
if (!isset($_SESSION['username'])){	
	echo "请先<a href='../user/loginfrm.php'>登录</a> ！";
	echo" <script>setTimeout('document.location.href=\"../user/loginfrm.php\"',0)</script>"; 	
	exit;
}
include('../../../config.inc');
include('../include/dbconnect.php');
echo <<<HTML
<style type='text/css'>
.trc2{background: #d7d7d7;font-size:10pt;}
.trc1{font-size:10pt}
</style>
HTML;
//--------
('d'==$_GET['o'])?($od=" order by repository,path"):($od=" order by user_name,repository");
$query="select user_name,full_name,repository,path,email from svnauth_dir_admin,svnauth_user where svnauth_user.user_id=svnauth_dir_admin.user_id $od";
$result =mysql_query($query);
$adminpath='';
$oun='';
$opath='';
while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
	$path=$row['repository'].$row['path'];
	$un=$row['user_name'];
	$fn=$row['full_name'];
	$email=$row['email'];
	if(!empty($fn))$un="$un($fn)";
	if(empty($path))continue;
	$ustr=$un."($email)";
	$pstr=$path;
	if('n'==$_GET['o'])
	{
		$ustr=$un."($email)";
	        ($oun != $un)?($oun=$un):($ustr='');
		(empty($ustr))?(true):(($ud_cls=="trc1")?($ud_cls="trc2"):($ud_cls="trc1"));
		$cls=$ud_cls;

	}else  {
	 	$pstr=$path;
	        ($path != $opath)?($opath=$path):($pstr='');
		(empty($pstr))?(true):(($pd_cls=="trc1")?($pd_cls="trc2"):($pd_cls="trc1"));
		$cls=$pd_cls;
	}
        $adminpath .= "<tr class=$cls><td>$ustr</td><td>&nbsp;$pstr</td></tr>";
}
echo "<h4>目录管理员: <a href='?o=n'>按姓名</a>   <a href='?o=d'>按目录</a></h4>";
echo "<table border=0 cellspacing=0>";
echo $adminpath."</table>";


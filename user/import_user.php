<?php
include('../include/charset.php');
include('../include/requireAuth.php');
//import user from passwd file
if(! file_exists($passwdfile))
{
  echo "File not found! Please check your <a href='../config/index.php'>config</a>!";
  exit;
}
include('../../../config.inc');
include('../include/dbconnect.php');

$handle = fopen($passwdfile, "r");
$correct = false;
if ($handle) {
    while (!feof($handle)) {
        $buffer = trim(fgets($handle));
	if(($buffer[0] == '#')or empty($buffer))continue;
	list($user,$passwd)=explode(':',$buffer,2);
	if($firstline and (empty($user)))
        {
        	echo "file not correct!";
        	exit;
	}
	$firstline=false;
	$expire=mktime(0, 0, 0, date("m")  , date("d")+$user_t, date("Y"));
	$expire=strftime("%Y-%m-%d",$expire);
	$query="insert into svnauth_user (user_name,password,supervisor,fresh,expire) values(\"$user\",\"$passwd\",0,0,\"$expire\")";
	mysql_query($query);
	$err=mysql_error();
	if(!empty($err)){
		$query="update svnauth_user set password=\"$passwd\" where user_name=\"$user\"";
		mysql_query($query);
	}

    }
}
echo "导入成功！";



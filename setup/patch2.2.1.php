<?php
include('../config/config.php');
include('../../../config.inc');
include('../include/dbconnect.php');
$query="alter table svnauth_g_permission modify repository varchar(35)";
mysql_query($query);
echo mysql_error();
$query="alter table svnauth_dir_admin modify repository varchar(35)";
mysql_query($query);
echo mysql_error();
$query="alter table svnauth_permission modify repository varchar(35)";
mysql_query($query);
$query="alter table dir_des modify repository varchar(35)";
mysql_query($query);
echo mysql_error();
echo "如无其他错误显示，则数据库已更改成功！";
unlink('./patch2.2.1.php');



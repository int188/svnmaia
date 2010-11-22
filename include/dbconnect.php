<?php
	$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2) or die("数据库链接失败！请联系管理员");
	mysql_select_db(DBNAME) or die("不能选择数据库！");
//	mysql_query("SET NAMES UTF8"); 
	$program_char='gbk';

	$pattern='/(\d+)\.\d+\.\d+/i';
# 如果数据中文乱码，请去掉下面句子的注释符'#'
#	preg_match($pattern,mysql_get_server_info(),$out);
	if($out[1] > 4) //mysql version > 4
	{

//取出当前数据库的字符集

	$sql='SELECT @@character_set_database';
	$result = mysql_query($sql);
	$char=mysql_result($result,0);
	mysql_query("SET character_set_connection=$char, character_set_results=$program_char, character_set_client=binary",$mlink);

	}

?>

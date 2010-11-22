<?php
include('../include/charset.php');
?>
<meta http-equiv="Refresh" content="60">
<strong>说明：</strong>本页面对svn代码变更进行监控，并将变更发送给订阅者。
<p>
本页面将会自动刷新，以实现监控变更目的，建议你不要关闭本页面。
<br>
<p>
或者您可以将本页面作为计划任务执行，定时执行一次（如2分钟）。
<br><br>方法如下：
<br><strong>Linux系统</strong>：在crontab中添加一行：
<br>  */2 * * * * "wget --delete-after http://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']; ?>"
<br><strong>Windows系统</strong>:在计划任务中：
打开“控制面板”-->双击“计划任务”-->添加新任务-->选择运行程序中，点击浏览，在弹出对话框中，输入本页面的url
如：http://www.example.com/svnmaia/scheme/monitor.php，然后一直点下一步，直到完成。
<br>
<div style="background:#B6C6D6;text-align:center;color:#fe392a;margin:20px 0 20px 0;">
<?php
include('../../../config.inc');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2)or die("<br>数据库链接失败！请联系管理员");
if (mysql_select_db(DBNAME))
{
	include('../include/email.php');
	$query="select monitor_id,url,version from monitor_url";
	$myurl="http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
	$myurl=str_replace('scheme/monitor.php','extension/svn_monitor.php',$myurl);
	$tail="
		------
		您收到本邮件是因为您或者某管理员为您订阅了此目录svn代码变更监控。
如要退订此邮件，请登录此地址操作： ".$myurl ;
	$result=mysql_query($query);
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH))) {
		$monitor_id=$row['monitor_id'];
		$url=$row['url'];
		$oldver=$row['version'];
		if(preg_match("/^http:/i",$url)){
			$localurl=$url;
		}else
		{
			$localurl=($svnparentpath{0}=='/')?("file://$svnparentpath/$url"):("file:///$svnparentpath/$url");
		}
		unset($dirs_arr);
		 exec("{$svn}svn log --limit 1 -q \"$localurl\"",$dirs_arr);
	  if(count($dirs_arr)>1)
	  {
		$ver=current($dirs_arr);
		$ver=next($dirs_arr);
		list($ver,$ot)=explode(' ',$ver);
		list($ot,$ver)=explode('r',$ver);
		if($oldver == $ver)continue;
		unset($logarr);
		$v1=$oldver+1;
		exec("{$svn}svn log -v -r${v1}:$ver \"$localurl\"",$logarr);
		$filestr='';
		list($repos,$ot)=explode('/',$url,2);
		foreach($logarr as $k => $v)
		{
			if(preg_match("/^[\t\s]+(\w)\s+(.*)/",$v,$matches))
			{				
				$f=$matches[2];
				$fn=basename($f);
				$filestr .= ' '.$f;
				if($matches[1] == 'M')
					$logarr[]="查看$fn diff:  "."http://".$_SERVER['SERVER_NAME']."/viewvc/$repos/$f?r1=$oldver&r2=$ver";
			}
		}
		$body=implode("\n\r",$logarr);
		$query="update monitor_url set version=$ver where monitor_id=$monitor_id";
		$result2=mysql_query($query);
		if($result2)
		{
			$query="select email,user_name,pattern from svnauth_user,monitor_user where monitor_id=$monitor_id and svnauth_user.user_id=monitor_user.user_id";
			$result3=mysql_query($query);
			$found=false;			
			while (($result3)and($userrow= mysql_fetch_array($result3, MYSQL_BOTH))) {
				$email=$userrow['email'];
				$user=$userrow['email'];
				$email=(empty($email))?($user.$email_ext):$email;
				$pattern=$userrow['pattern'];
				$found=true;
				if(!empty($pattern))
				{
					if(! preg_match("/$pattern/",$filestr))continue;
				}
				$subject="代码变更 r$ver:$url";	
				$windid='svn-changed';
				$mail_info=send_mail($email,$subject,$body.$tail);
				if($mail_info === true)
				{
					echo "<br>$url 变更通知已发送！";
				}else
				{
					echo "发送到$user时发生错误：$mail_info";
				}
	
			}
			if(!$found)
			{
				$myquery="delete from monitor_url where monitor_id=$monitor_id";
				mysql_query($myquery);
			}			
		}
	  }

	}


}
?>
</div>

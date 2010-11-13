<?php
include('../include/charset.php');
?>
<meta http-equiv="Refresh" content="3600">
<strong>说明：</strong>当你打开本页面时，本页面会按照系统设定的用户、权限有效期对权限控制文件进行更新。
<p>
本页面将会自动刷新，以维护用户、权限文件的更新，建议你不要关闭本页面。
<br>
<p>
或者您可以将本页面作为计划任务执行，建议每天执行一次，已达到及时清理权限的目的。
<br><br>方法如下：
<br><strong>页面加载方式</strong>：找到最常访问的页面，编辑该页面HTML代码，在其body标签内，添加如下js代码：
<br>&lt;script src="<?php echo $_SERVER['PHP_SELF'] ?>"&gt;&lt;/script&gt;
<br> 其中src="**"语句指明了本页面的url位置。 这样，当有人访问该页面时就会自动运行本清理脚本。
<br><strong>Linux系统</strong>：在crontab中添加一行：
<br>  0 0 * * * "wget --delete-after <?php echo $_SERVER['SERVER_NAME']$_SERVER['PHP_SELF'] ?>"
<br><strong>Windows系统</strong>:在计划任务中：
打开“控制面板”-->双击“计划任务”-->添加新任务-->选择运行程序中，点击浏览，在弹出对话框中，输入本页面的url
如：http://www.example.com/svnauth/scheme/scheme.php，然后一直点下一步，直到完成。
<br>
<div style="background:#B6C6D6;text-align:center;color:#fe392a;margin:20px 0 20px 0;">
<?php
include('../include/basefunction.php');
include('../../../config.inc');
$mlink=mysql_connect(SERVER,USERNAME2,PASSWORD2)or die("<br>数据库链接失败！请联系<a href='mailto:xuejiang.li@yahoo.com.cn'>管理员</a>");
if (mysql_select_db(DBNAME))
{
	//用户是否即将到期，提前2个星期发信通知进行激活
	//
	//
	//如果用户已过期，并且提醒次数超过3次，则删除用户，并立刻生效。
	//$expire=mktime(0, 0, 0, date("m")  , date("d")-14, date("Y"));
	//$expire=strftime("%Y-%m-%d",$expire);
	$expire=date('Y-m-d' , strtotime('+2 week')); 
	$query="delete from svnauth_user where infotimes > 2 and expire < NOW()";
	$valuechanged=false;
	mysql_query($query);
	if(mysql_affected_rows()>0)$valuechanged=true;
	$query="select user_id,user_name,email,infotimes,expire from svnauth_user where expire < \"$expire\"";
	$result=mysql_query($query);
	include('../include/email.php');
	if(file_exists('./tmp'))
	{
		$d=file_get_contents('./tmp');
		$tmpd=(strtotime($d)-strtotime(date('Y-m-d')))/86400;
		if($tmpd==0){
			echo "今天已执行过本程序！";
			exit;
		}
	}
	$handle=fopen('./tmp','w+');
	fwrite($handle,date('Y-m-d'));
	fclose($handle);
	while (($result)and($row= mysql_fetch_array($result, MYSQL_BOTH)))
	{
		$infot=trim($row['infotimes']);
		$infotimes=(empty($infot))?0:$infot;
		if($infotimes>3)continue;
		$expire=$row['expire'];
		//$expire=strtotime("+7 day",strtotime($expire)");//后推1week
		$user=$row['user_name'];
		$uid=$row['user_id'];
		$email=(empty($row['email']))?($user.$email_ext):$row['email'];
		$para=array($user,$email,$uid);
		$sig=keygen($para);
		//发邮件通知激活
		$url="http://".$_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']))."/activeuser.php";
	        $url=$url."?sig=$sig&u=$user&uid=$uid&email=$email";
		$body="请注意：您的svn用户(http://".$_SERVER['HTTP_HOST']." )即将于 $expire 过期，用户名：$user\n

			过期后，您的svn账户将被自动删除。\n

如果您需要继续访问本svn，请点击如下链接进行信息确认，并激活续订：\n
			$url

			如果您已不需要，请忽略本邮件！
			
本邮件系统自动发出，回复无效。有疑问请找配管组。
---";
		$subject="通知：您的svn账户即将过期！";
		$mail_info=send_mail($email,$subject,$body);
		//记录本次发邮件事件
		if($mail_info === true)
		{
			echo "<br>$user 用户即将过期，已发邮件通知其激活续订！";
			$infotimes++;
		}
		else{
			echo "<br>$user 用户即将过期，但发通知邮件时遇到错误，可能该用户没有收到。<br>$mail_info";
			openlog("svnMaiaLog", LOG_PID | LOG_PERROR, LOG_LOCAL0);
			$access = date("Y/m/d H:i:s");
			syslog(LOG_ERR, "$user: this svn username is being expired. But we counld't not mail to him/her which Error: $mail_info. $access");
		}
		$query="update svnauth_user set infotimes=$infotimes where user_id=$uid";
		mysql_query($query);
		echo mysql_error();

	}


	//判断写权限是否过期，如果已过期，则改为只读权限。
	$expire=date('Y-m-d' , strtotime('+20 week'));
	$query="update svnauth_permission set permission='r', expire=\"$expire\" where expire <= NOW() and permission='w' ";
	mysql_query($query);
	if(mysql_affected_rows()>0)$valuechanged=true;


	//判断读权限是否过期，如果已过期，则改为无权限。
	$query="update svnauth_permission set  permission='n', expire=\"$expire\" where expire <= NOW() and permission='r' ";
	mysql_query($query);
	if(mysql_affected_rows()>0)$valuechanged=true;
	//生效
	if($valuechanged)
	{
		$scheme=true;
		@include('../priv/gen_access.php');
	}
}
?>
</div>

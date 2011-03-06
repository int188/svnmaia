<?php
session_start();
// error_reporting(0);
include('../include/charset.php');
if (!isset($_SESSION['username'])){	
	echo "请先登录!";
	exit;
}
if ($_SESSION['role'] !='admin')
{
	echo "您无权进行此操作！";
	exit;
}
?>
<div id='info'>
 正在导入！
</div>
 <div id='step'>
 </div>
<?php
if(file_exists('../config/config.php'))
{
	include('../config/config.php');
}else
{
	echo "window.alert('请先进行系统设置!')";
	echo" <script>setTimeout('document.location.href=\"../config/index.php\"',0)</script>";  	
	exit;
}
if(! file_exists($accessfile))
{
  echo "file not found! Please check your input!";
  exit;
}
include('../../../config.inc');
include('../include/dbconnect.php'); 
//import前备份
$today = date("Ymd_His");
$backupfile=$accessfile.$today;
if (!copy($accessfile, $backupfile)) {
    echo "failed to backup $accessfile...\n";
}
$handle = fopen($accessfile, "r");
$correct = false;
$firstline = true;
$groupstart = false;
$dirstart = false;
$notfounduser="";
$groupinfo=array();
$group_parent=array();
$p_info=array();
//所有用户
$query="select user_id,user_name from svnauth_user order by user_name";
$result = mysql_query($query);
$uid_array=array();
while (($result)and($row= mysql_fetch_row($result))) {
	$uid=$row[0];
	$user=$row[1];
	$uid_array[$user]=$uid;
}	
function getmember($parent,$k,$detail,&$member,$c)
{
  	$c++;
  	foreach($parent[$k] as $value)
  	{
  		if(array_key_exists($value,$parent))
  		{
  			 if($c>5)return;//只递归5层
  			  getmember($parent,$value,$detail,$member,$c);
  		}else
  		  $member[]=&$detail[$value];
  	}
  	if(array_key_exists($k,$detail))
  	  $member[]=&$detail[$k];
}

if ($handle) {
	echo "<script>document.getElementById('info').innerHTML='正在执行导入！'</script>";
	$i=1;
    while (!feof($handle)) {
	    $buffer = trim(fgets($handle));
	    echo "<script>document.getElementById('step').innerHTML='第 $i 行数据导入成功！还在继续中...'</script><br>";
	    $i++;
        if(($buffer[0] == '#')or empty($buffer))continue;
        if($firstline and ($buffer[0] != '['))
        {
        	echo "file not correct!";
        	exit;
        }
        $firstline=false;
        if($buffer == '[groups]')
        {
        	$groupstart = true;
        	continue;
        }
        if($groupstart and ($buffer[0] == '['))
        {
        	if($buffer != '[groups]')$groupstart=false;
        	//获取节点的库名和path信息
        	if(ereg("^\[(.*)\]$",$buffer,$matches))$buffer=$matches[1];
		if(! $groupstart)
		{
			list($repos,$path)=explode(':',$buffer,2);//如果path是null呢？[/]
			if(($repos != '/')and(empty($path)))$path='/';
			if(strpos($path,':'))echo "<br><b>Warning:</b>$buffer maybe a wrong input in your authz file";
		}
        	continue;
        }
        if($groupstart)
        {
          list($group,$group_member)=explode('=',$buffer,2);
          $group=trim($group);
          $member=explode(',',$group_member);
          foreach($member as $key=>$value)
          {
            $value=trim($value);
            if($value[0] == '@')
            {
            	$group_parent['@'.$group][]=trim($value);
            	unset($member[$key]);//删除是group的单元
            }
          }
          if(!empty($member))$groupinfo['@'.$group]=$member;
        }else{
          if($buffer[0] == '[')
          {
            if(ereg("^\[(.*)\]$",$buffer,$matches))$buffer=$matches[1];
	    list($repos,$path)=explode(':',$buffer,2);//如果path是null呢？[/]
	    if(($repos != '/')and(empty($path)))$path='/';
	    if(strpos($path,':'))echo "<br><b>Warning:</b>$buffer maybe a wrong input in your authz file";
            continue;
          }
          list($group,$permission)=explode('=',$buffer,2); 
          $group=trim($group);         
          switch(trim($permission))//存入只读组
          {
          	case 'r':
          		$p_info[$repos][$path]['r'][]=$group;//如果path是null呢？
          		break;
          	case 'rw':
          		$p_info[$repos][$path]['w'][]=$group;
          		break;
          	case 'wr':
          		$p_info[$repos][$path]['w'][]=$group;
          		break;
          	default:
          		$p_info[$repos][$path]['n'][]=$group;
          }
          
	}
    }
    fclose($handle);
    if(count($p_info)>1)
    {
    	$query="delete from svnauth_permission";
    	mysql_query($query);
	$query="delete from svnauth_g_permission";
    	mysql_query($query);
	$query="delete from svnauth_group";
	mysql_query($query);
	$query="delete from svnauth_groupuser";
	mysql_query($query);
	foreach($groupinfo as $group => $v)
	{
		$g1=str_replace('@','',$group);
		if(function_exists('preg_match'))
		{
			if(preg_match("/_(w|r|n)[0-9]+$/",$g1))
			{ echo "found $group is not a group <br>";
			  continue;
			}
		}else 
			echo "你的php不支持正则表达式<br>";
		$query="insert into svnauth_group (group_name) values ('$g1')";
		mysql_query($query);
	}
	foreach($group_parent as $group => $v)
	{
		$g1=str_replace('@','',$group);
		if(function_exists('preg_match'))
		{
			if(preg_match("/_(w|r|n)[0-9]+$/",$g1))
			{ echo "found $group is not a group <br>";
			  continue;
			}
		}else 
			echo "你的php不支持正则表达式<br>";
		$query="insert into svnauth_group (group_name) values ('$g1')";
		mysql_query($query);
	}
	$query="select group_id,group_name from svnauth_group";
	$result = mysql_query($query);
	$gid_array=array();
	while (($result)and($row= mysql_fetch_row($result))) {
		$gid=$row[0];
		$group=$row[1];
		$gid_array[$group]=$gid;
	}
    }
    date_default_timezone_set('PRC');
  
  //  print_r($p_info);print_r($group_parent);print_r($groupinfo);    
    foreach($p_info as $repos => $value)
    {
    	foreach($value as $path => $v)
    	{
      	    foreach($v as $pm => $uv)
      	    {
      	    	switch($pm)
      	    	{
      	    		case 'r':
      	    		 $expire=mktime(0, 0, 0, date("m")  , date("d")+$read_t, date("Y"));
      	    		 break;
      	    		case 'w':
      	    		  $expire=mktime(0, 0, 0, date("m")  , date("d")+$write_t, date("Y"));
      	    		  break;
      	    		default:
      	    		  $expire=mktime(0, 0, 0, date("m")  , date("d"), date("Y")+2);
      	    	}      	    	
		$expire=strftime("%Y-%m-%d",$expire);
		//echo "<script>document.getElementById('step').innerHTML +='..'</script>";
      	    	foreach($uv as $goru)
      	    	{
      	    	   if($goru[0]=='@')
      	    	   {
      	    	     //判断是否group_parent成员，如果是，则找出该子组的所有成员,多级子组呢？需要递归。
      	    	     if(array_key_exists($goru,$group_parent))
      	    	     {
      	    	     	$member=array();
      	    	     	getmember($group_parent,$goru,$groupinfo,&$member,0);	
      	    	    // 	print_r ($member);
      	    	     	foreach($member as $ii)
      	    	     	  foreach($ii as $user){
      	    	     	  	$user=trim($user);
				//找出组成员插入表中
				# $query="insert into svnauth_permission (repository,path,user_id,permission,expire) values (\"$repos\",\"$path\",$uid_array[$user],\"$pm\",\"$expire\")";
				$g1=str_replace('@','',$goru);
				if(function_exists('preg_match'))
				{
					if(preg_match("/_(w|r|n)[0-9]+$/",$g1))
					{ 
						if(empty($uid_array[$user]))
						{
							$notfounduser .= "$user <br>";
							continue;
						}
						$query="insert into svnauth_permission (repository,path,user_id,permission,expire) values (\"$repos\",\"$path\",$uid_array[$user],\"$pm\",\"$expire\")";
						mysql_query($query);
					}else{
						$query="insert into svnauth_groupuser(group_id,user_id) values ($gid_array[$g1],$uid_array[$user])";
						mysql_query($query);
					}
				}else 
					echo "error: preg_match function not found!";			
			//	echo $query."$user<br>";	
			  }
		
      	    	     }
      	    	     //判断是否groupinfo的键名，如果是，则找出改组成员。
      	    	     if(array_key_exists($goru,$groupinfo)){
      	    	     	foreach($groupinfo[$goru] as $user){
      	    	     //找出组成员插入表中
				$user=trim($user);
				if(empty($uid_array[$user]))
				{
					$notfounduser .= "$user <br>";
					continue;
				}
				$g1=str_replace('@','',$goru);
				if(preg_match("/_(w|r|n)[0-9]+$/",$g1))
				{ 
					if(empty($uid_array[$user]))
					{
						$notfounduser .= "$user <br>";
						continue;
					}
					$query="insert into svnauth_permission (repository,path,user_id,permission,expire) values (\"$repos\",\"$path\",$uid_array[$user],\"$pm\",\"$expire\")";
					mysql_query($query);
					continue;
				}
				$query="insert into svnauth_groupuser(group_id,user_id) values ($gid_array[$g1],$uid_array[$user])";      	    	      
				mysql_query($query);
			//	echo $query."$user<br>";
      	    		}
		     }
	$g1=str_replace('@','',$goru);
	$query="insert into svnauth_g_permission (repository,path,group_id,permission,expire) values (\"$repos\",\"$path\",$gid_array[$g1],\"$pm\",\"$expire\")";
      	    	       //  echo "<br>$query";
      	    	        mysql_query($query);
      	    	   }else
      	    	   {
			$goru=trim($goru);
			if(empty($uid_array[$goru]))
			{
				$notfounduser .= "$goru <br>";
				continue;
			}
      	    	      $query="insert into svnauth_permission (repository,path,user_id,permission,expire) values (\"$repos\",\"$path\",$uid_array[$goru],\"$pm\",\"$expire\")";
      	    	     mysql_query($query);
      	    	   }
       	       }
      	   }
       }
    }
   echo "<script>document.getElementById('step').innerHTML='全部导入成功！'</script>";
   if(!empty($notfounduser))echo"导入过程中，如下用户没有在 $passwdfile 找到，因此他们没被导入：<br>$notfounduser";
}else{
  echo "Cann't read this access file, please check the private of the file";
  exit;
}
	


?>

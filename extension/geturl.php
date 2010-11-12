<?php
function geturl($t_url)
{
	global $svnparentpath,$svn,$svnurl,$ver;
	if($t_url=='')return false;
	$t_url=($t_url{strlen($t_url)-1}=='/')?(substr($t_url,0,-1)):($t_url); 
	$dir=str_replace($svnurl,'',$t_url);
	if(preg_match("/^http:/i",$dir)){
		$dir=str_replace("http://",'',$dir);
		list($tmp1,$tmp2,$dir)=explode('/',$dir,3);
	}
	$dir=($dir{0}=='/')?(substr($dir,1)):($dir);
	$dir=str_replace('//','/',$dir);

	  $dir=escapeshellcmd($dir);
 	  $localurl=($svnparentpath{0}=='/')?("file://$svnparentpath/$dir"):("file:///$svnparentpath/$dir");
	  exec("{$svn}svn log --limit 1 -q \"$localurl\"",$dirs_arr);
	  if(count($dirs_arr)>1)
	  {
		$ver=current($dirs_arr);
		$ver=next($dirs_arr);
		echo $ver;
		list($ver,$ot)=splite(' ',$ver);
		list($ot,$ver)=splite('r',$ver);
		return $dir;
	  }else
	  {
		  exec("{$svn}svn info \"$t_url\"",$info_arr,$ret);
		  if($ret){
			  $ver=current($info_arr);
			  $ver=next($info_arr);
			echo $ver;
			list($ver,$ot)=splite(' ',$ver);
			list($ot,$ver)=splite('r',$ver);
	
			  return $t_url;
		  }else
			 return false; 
	  }
	
}

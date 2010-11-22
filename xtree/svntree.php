<?php
header("Content-Type: text/xml;  charset='utf-8'");
$d=$_GET['parentId'];
$path=escapeshellcmd($_GET['d']);
include('../config/config.php');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<tree>";
if($d == '0')
{
    $sp = opendir( $svnparentpath );
    if( $sp ) {
	    $id=1;
        while( $dir = readdir( $sp ) ) {
            $svndir = $svnparentpath . "/" . $dir;
	    $svndbdir = $svndir . "/db";
	    $svnhooksdir=$svndir ."/hooks";
	    if( is_dir( $svndir ) && is_dir( $svndbdir ) && is_dir($svnhooksdir)) {
		    //$mytag=substr($dir,0,-1);
		    $url2="javascript:setpath('$dir');"; 
		    $url="./svntree.php?d=$dir";
		    echo "<tree src=\"$url\"  action=\"$url2\" text=\"$dir\"/>\n";
		    $id++;
	    }
	}
    }

}else
{
	$dirs_arr=array();
	$localurl=($svnparentpath{0}=='/')?("file://$svnparentpath/$path"):("file:///$svnparentpath/$path");
	$svnlist=exec("{$svn}svn list ".$localurl,$dirs_arr);
	$i=1;
	foreach($dirs_arr as $dir)
	{
		if($dir{strlen($dir)-1}=='/')
		{
			$mytag=substr($dir,0,-1);
			$url2="javascript:setpath('$path/$mytag');";
			$url="./svntree.php?d=$path/$dir";
			echo"<tree src=\"$url\"  action=\"$url2\" text=\"$dir\"/>\n";
		    $i++;
		}
	}
}
?>
</tree>

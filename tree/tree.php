<?php
header("Content-Type: text/xml; charset='utf-8'");
$parentId=$_GET['parentId'];
$path=escapeshellcmd($_GET['d']);
list($repos,$path)=explode('/',$path,2);
$path=($path{strlen($path)-1}=='/')?('/'.substr($path,0,-1)):('/'.$path);
$firstdir=$repos.$path;
include('../config/config.php');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
if($parentId == '0')
{
    $sp = opendir( $svnparentpath );
    if( $sp ) {
	    $id=1;
        while( $dir = readdir( $sp ) ) {
            $svndir = $svnparentpath . "/" . $dir;
	    $svndbdir = $svndir . "/db";
	    $svnhooksdir=$svndir ."/hooks";
	    if( is_dir( $svndir ) && is_dir( $svndbdir ) && is_dir($svnhooksdir)) {
		    $url="../priv/dirpriv.php?d=$dir"; 
		    echo "<item id=\"$id\" isFolder=\"true\" link=\"$url\" dir=\"$dir\">$dir</item>\n";
		    $id++;
	    }
	}
    }

}else
{
	$svnlist=system('svn list',$svnparentpath."/$firstdir");
	$dirs=explode("\n",$svnlist);
	$i=1;
	foreach($dirs as $dir)
	{
		if($dir{strlen($dir)-1}=='/')
		{
			$id=$parentId.'_'.$i;
			$url="../priv/dirpriv.php?d=$firstdir/$dir";
			echo"<item id=\"$id\" isFolder=\"true\" link=\"$url\" dir=\"$dir\">$dir</item>\n";
		    $i++;
		}
	}
}
?>
</tree>

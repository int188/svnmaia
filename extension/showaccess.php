<?php
include('../config/config.php');
$str=file_get_contents($accessfile);
echo str_replace("\n",'<br>',$str);

?>

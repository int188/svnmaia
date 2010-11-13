<?php
session_start();
 //error_reporting (E_ALL ^ E_WARNING);//屏蔽所有报错

// echo "dd".$_SERVER['HTTP_REFERER'];
$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

session_destroy();

 echo "    <script>setTimeout('document.location.href=\"javascript:history.back()\"',5)</script>     ";
 //echo "<meta http-equiv=\"Refresh\" content=\"1;url=javascript:history.back()\">";
?>

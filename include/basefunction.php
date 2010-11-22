<?php
function keygen($para)
{
	ksort($para);
	if(! defined('SECRET_KEY'))define("SECRET_KEY","0273cdbfewy243744540c3gs26f74e4b");
	$str=SECRET_KEY;
	foreach($para as $k => $v){   
         $str .= $k.'='.$v;   
        }
        return sha1($str);
}
function genSalt ()
{
		$random = 0;
		$rand64 = "";
		$salt = "";

		$random=rand();	// Seeded via initialize()

		// Crypt(3) can only handle A-Z a-z ./

		$rand64= "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$salt=substr($rand64,$random  %  64,1).substr($rand64,($random/64) % 64,1);
		$salt=substr($salt,0,2); // Just in case

		return($salt);

}
function cryptMD5Pass($plainpasswd,$salt="") {
  if ($salt =="") {
	            $salt = substr(str_shuffle("./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 8);
  } else {
            if (substr($salt, 0, 6) == '$apr1$') {
	                $salt = substr($salt, 6, 8);
            } else {
	                $salt = substr($salt, 0, 8);
            }
  }
  $len = strlen($plainpasswd);
  $text = $plainpasswd.'$apr1$'.$salt;
  $bin = pack("H32", md5($plainpasswd.$salt.$plainpasswd));
  for($i = $len; $i > 0; $i -= 16) { $text .= substr($bin, 0, min(16, $i)); }
  for($i = $len; $i > 0; $i >>= 1) { $text .= ($i & 1) ? chr(0) : $plainpasswd{0}; }
  $bin = pack("H32", md5($text));
  for($i = 0; $i < 1000; $i++) {
      $new = ($i & 1) ? $plainpasswd : $bin;
      if ($i % 3) $new .= $salt;
      if ($i % 7) $new .= $plainpasswd;
      $new .= ($i & 1) ? $bin : $plainpasswd;
      $bin = pack("H32", md5($new));
  }
  $tmp = "";
  for ($i = 0; $i < 5; $i++) {
      $k = $i + 6;
      $j = $i + 12;
      if ($j == 16) $j = 5;
      $tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
  }
  $tmp = chr(0).chr(0).$bin[11].$tmp;
  $tmp = strtr(strrev(substr(base64_encode($tmp), 2)),
	        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
	        "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
  return '$apr1$'.$salt.'$'.$tmp;
 }


function genPass ($para_arr,$hash='DES')
{
	if ($hash == 'SHA') {
		return ('{SHA}'.base64_encode(pack('H*',sha1($para_arr['passwd']))));
		//return ('{SHA}' . base64_encode(sha1($para_arr['passwd'], TRUE)) );
	}
	if ($hash == 'MD5') {
	  return (cryptMD5Pass($para_arr['passwd'],$para_arr['salt']));
	}
	if($hash == 'DES'){
		return md5($para_arr['user'] . ':' . $para_arr['realm'] . ':' .$para_arr['passwd']);
	}


		if (!($passwd))
		{
			// Return what we were given

			// If calling this directly, do something like
			// $enc_pass = $Htpasswd->cryptPass($pass);
			// if (empty($enc_pass)) { BARF! }

			// You should really verify the data before calling
			// this though - I do.			

			return "";

		}

		if (!empty($salt))
		{
			//# Make sure only use 2 chars

			$salt = substr ($salt, 0, 2);
		}
		else
		{
			// If no salt, generate a (pseudo) random one

			$salt =genSalt();
		}

		return (crypt($passwd, $salt));

} // end cryptPass
function verifyPasswd($inputPass,$correctPass)
{
	if(empty($correctPass))return false;
	if (substr($correctPass, 0, 6) == '$apr1$') {
	            if (cryptMD5Pass($inputPass, $correctPass) == $correctPass) {
	                return true;
	            }
	            return false;
	}
	if (substr($correctPass, 0, 6) == '{SHA}') {
		$para_arr=array('passwd'=>$inputPass);
		if(genPass($para_arr,'SHA') == $correctPass)
			return true;
		return false;
	}
	if (crypt($inputPass, $correctPass) == $correctPass) {
	            return true;
	}
	return false;	
}
function checkUserGroup($str)
{
	$p="/^[\w._\-\/]{2,50}$/";
	if(preg_match($p,$str))
	{
		return true;
	}else
		return false;
}
?>

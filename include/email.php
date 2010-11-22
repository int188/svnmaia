<?php
@ include(dirname(__FILE__).'/../config/config.php');
$db_charset='utf-8';
$M_db= new Mailconfig(
	array(
		'ifopen'=> 1,
		'method'=> $mail_method,
		'host'	=> $smtp_server,
		'port'	=> $smtp_port,
		'auth'	=> $use_smtp_authz,
		'from'	=> $email_from,
		'user'	=> $smtp_user,
		'pass'	=> base64_decode($smtp_passwd),
		'smtphelo'=>$ml_smtphelo,
		'smtpmxmailname' =>$ml_smtpmxmailname,
		'mxdns'=>$ml_mxdns,
		'mxdnsbak'=>$ml_mxdnsbak
	)
);
Class Mailconfig {
	var $S_method = 1;
	var $smtp;
	function Mailconfig($smtp=array()){
		$this->S_method = $smtp['method'];
		if(!$this->smtp['ifopen'] = $smtp['ifopen']) {
			//Showmsg('mail_close');
			echo "mail close";
		}
		if ($this->S_method == 1){
			//不用设置
		} elseif($this->S_method == 2){
			$this->smtp['host'] = $smtp['host'];
			$this->smtp['port'] = $smtp['port'];
			$this->smtp['auth'] = $smtp['auth'];
			$this->smtp['from'] = $smtp['from'];
			$this->smtp['user'] = $smtp['user'];
			$this->smtp['pass'] = $smtp['pass'];
		} elseif($this->S_method == 3){
			$this->smtp['port'] = $smtp['port'];
			$this->smtp['auth'] = $smtp['auth'];
			$this->smtp['from'] = $smtp['from'];
			$this->smtp['smtphelo']=$smtp['smtphelo'];
			$this->smtp['smtpmxmailname']=$smtp['smtpmxmailname'];
			$this->smtp['mxdns']=$smtp['mxdns'];
			$this->smtp['mxdnsbak']=$smtp['mxdnsbak'];
			//hacker
		} else{
			//hacker
		}
	}

}

function send_mail($toemail,$subject,$message,$additional=null){
	global $M_db,$db_charset,$windid;
	 if (!strstr($toemail,'@'))$toemail=$toemail.$email_ext;
	$sendtoname = $toemail;	
	!$windid && $windid = 'svn-info';
	$send_subject = "=?$db_charset?B?".base64_encode(str_replace(array("\r","\n"), array('',' '),$subject)).'?=';
	$send_message = chunk_split(base64_encode(str_replace("\r\n.", " \r\n..", str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message)))))));
	$send_from = "=?$db_charset?B?".base64_encode($windid)."?= <$fromemail>";
	$send_to = "=?$db_charset?B?".base64_encode($sendtoname)."?= <$toemail>";
	!empty($additional) && $additional && substr(str_replace(array("\r","\n"),array('','<rn>'),$additional),-4) != '<rn>' && $additional .= "\r\n";
	$additional = "To: $send_to\r\nFrom: $send_from\r\nMIME-Version: 1.0\r\nContent-type: text/plain; charset=$db_charset\r\n{$additional}Content-Transfer-Encoding: base64\r\n";
	if($M_db->S_method == 1){
		if(@mail($toemail,$send_subject,$send_message,$additional)){
			return true;
		} else{
			return false;
		}
	} elseif($M_db->S_method == 2){
		if(!$fp=fsockopen($M_db->smtp['host'],$M_db->smtp['port'],$errno,$errstr)){
			//Showmsg('email_connect_failed');
			echo "email connect failed";
		}
		if(strncmp(fgets($fp,512),'220',3)!=0){
			//Showmsg('email_connect_failed');
			echo "email connect failed";
		}
		if($M_db->smtp['auth']){
			fwrite($fp,"EHLO phpwind\r\n");
			while($rt=strtolower(fgets($fp,512))){
				if(strpos($rt,"-")!==3 || empty($rt)){
					break;
				} elseif(strpos($rt,"2")!==0){
					return false;
				}
			}
			fwrite($fp, "AUTH LOGIN\r\n");
			if(strncmp(fgets($fp,512),'334',3)!=0){
				return false;
			}
			fwrite($fp, base64_encode($M_db->smtp['user'])."\r\n");
			if(strncmp(fgets($fp,512),'334',3)!=0){
				return 'email_user_failed';
			}
			fwrite($fp, base64_encode($M_db->smtp['pass'])."\r\n");
			if(strncmp(fgets($fp,512),'235',3)!=0){
				return 'email_password_failed';
			}
		} else{
			fwrite($fp, "HELO phpwind\r\n");
		}
		$from = $M_db->smtp['from'];
		$from = preg_replace("/.*\<(.+?)\>.*/", "\\1", $from);
		fwrite($fp, "MAIL FROM: <$from>\r\n");
		if(strncmp(fgets($fp,512),'250',3)!=0){
			return 'email_from_failed';
		}
		fwrite($fp, "RCPT TO: <$toemail>\r\n");
		if(strncmp(fgets($fp,512),'250',3)!=0){
			return 'email_toemail_failed';
		}
		fwrite($fp, "DATA\r\n");
		$result_fp=fgets($fp,512);
		if(strncmp($result_fp,'250',3)==0){
			$result_fp=fgets($fp,512);
		}
		if(strncmp($result_fp,'354',3)!=0){
			echo $result_fp;
			return 'email_data_failed';
		}
		$msg  = "Date: ".Date("r")."\r\n";
		$msg .= "Subject: $send_subject\r\n";
		$msg .= "$additional\r\n";
		$msg .= "$send_message\r\n.\r\n";
		fwrite($fp, $msg);
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250)
		{
			//Showmsg('email_connect_failed');
			echo $lastmessage;
			return "email connect failed";
		}
		fwrite($fp, "QUIT\r\n");
		fclose($fp);
		return true;
	}  else{
		//hacker
	}
}
?>

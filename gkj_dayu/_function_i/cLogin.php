<?php
class cLogin {
	function goLogin($un,$pw) {
		settype($login,"boolean");
		settype($logout,"boolean");

		$conn  = @ftp_connect("222.124.22.23");
		$okein = @ftp_login($conn,$un,$pw);

		if ($okein==1) {
			$logintrue=1;	
		} else {
			$logintrue=0;	
		}
		return $logintrue;
	}
}
?>
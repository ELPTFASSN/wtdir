<?php
function CreatePassword($CPUser,$CPPass){
	$aucount = strlen($CPUser);
	$salt = substr(md5($CPUser),$aucount);
	$pwhash = md5($CPPass);
	$pwhashsount=strlen($pwhash);
	return substr($pwhash,$aucount).$salt.substr($pwhash,$aucount+1,$pwhashsount-$aucount);
}
?>
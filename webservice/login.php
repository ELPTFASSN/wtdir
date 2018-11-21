<?php
include 'include/config.inc.php';
	
if(!empty($_POST)){
	$mysqli = new mysqli('localhost','waff01_dev','M!s119.93.224.26','waff01_webservice');
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare($query="SELECT count(idAccountUser) as UserCount FROM accountuser WHERE AUName=? And AUPassword=?")){
		$stmt->bind_param('ss',$_POST['txtusername'],$_POST['txtpassword']);
		$stmt->execute();
		$stmt->bind_result($UserCount);
		$stmt->fetch();
		$stmt->close();
	}
	if(!isset($UserCount) || $UserCount==0){
		$response['success']=0;
		$response['message']='Invalid Username or Password';
		die(json_encode($response));
	}else{
		$response['success']=1;
		$response['message']='Login Successful';
		die(json_encode($response));
	}
}else{
?>
	<form name="frmlogin" method="post" action="login.php">
    	<input type="text" name="txtusername" />
        <input type="password" name="txtpassword" />
        <input type="submit" name="Login" />
    </form>
<?php
}
?>
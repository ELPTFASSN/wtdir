<?php
	include 'include/config.inc.php';
	
	/*$mysqli = new mysqli($server,$username,$password,$database);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare($query="SELECT AUUserName,AUPassword FROM accountuser")){
		$stmt->execute();
		$stmt->bind_result($AUName,$AUPassword);
		$stmt->store_result();
		if ($stmt->num_rows>0){
			$arrResult = array();
			$response['accountuser']=array();
			
			while($stmt->fetch()){
				$arrResult['AUName']=$AUName;
				$arrResult['AUPassword']=$AUPassword;
				array_push($response['accountuser'],$arrResult);
			}
			$response['success']=1;
		}else{
			$response['success']=0;
			$response['message']='No records found';
		}
		$stmt->close();
		die(json_encode($response));
	}*/
?>
<?php
	include 'include/config.inc.php';
	$response = array();
	
	$mysqli = new mysqli($server,$username,$password,$database);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select idProductGroup,PGName From productgroup Where Status = 1 and idProductType = (Select idProductType From producttype 
					Where PTName = 'Products' and Status = 1)")){
		$stmt->execute();
		$stmt->bind_result($idProductGroup,$PGName);
		$stmt->store_result();
		if($stmt->num_rows > 0){
			$group = array();
			$response['productgroup'] = array();
			while($stmt->fetch()){
				$group['idProductGroup'] = $idProductGroup;
				$group['PGName'] = $PGName;
				array_push($response['productgroup'],$group);
			}
			$response['success'] = 1;
		}else{
			$response['success'] = 0;
			$response['message'] = 'No record(s) found';
		}
		echo json_encode($response);
	}else{
		$response['success'] = 0;
		$response['message'] = $stmt->error;
		echo json_encode($response);
	}
?>
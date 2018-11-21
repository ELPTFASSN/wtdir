<?php
	include 'include/config.inc.php';
	if(isset($_POST['getPaymentTypes'])){
		$mysqli = new mysqli($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("SELECT unPaymentType,PTName,PTFixedAmount,PTReference 
				FROM paymenttype 
				WHERE `Status` = 1")){
			$stmt->execute();
			$stmt->bind_result($unPaymentType,$PTName,$PTFixedAmount,$PTReference);
			$stmt->store_result();
			if($stmt->num_rows > 0){
				$item = array();
				$response['paymenttype'] = array();
				while($stmt->fetch()){
					$item['unPaymentType'] = $unPaymentType;
					$item['PTName'] = $PTName;
					$item['PTFixedAmount'] = $PTFixedAmount;
					$item['PTReference'] = $PTReference;					
					array_push($response['paymenttype'],$item);
				}
				$response['Success'] = 1;
			}else{
				$response['Success'] = 0;
				$response['Message'] = 'No record(s) found';
			}
			$stmt->close();			
			echo json_encode($response);
		}
	}
?>
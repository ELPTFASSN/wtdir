<?php
	include 'include/config.inc.php';
	if(isset($_POST['getDiscounts'])){
		$mysqli = new mysqli($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("SELECT unDiscountType,DTName,DTPercent,DTAmount,DTVatExempt 
				FROM discounttype 
				WHERE `Status`=1")){
			$stmt->execute();
			$stmt->bind_result($unDiscountType,$DTName,$DTPercent,$DTAmount,$DTVatExempt);
			$stmt->store_result();
			if($stmt->num_rows > 0){
				$item = array();
				$response['discounttype'] = array();
				while($stmt->fetch()){
					$item['unDiscountType'] = $unDiscountType;
					$item['DTName'] = $DTName;
					$item['DTPercent'] = $DTPercent;
					$item['DTAmount'] = $DTAmount;
					$item['DTVatExempt'] = $DTVatExempt;
					array_push($response['discounttype'],$item);
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
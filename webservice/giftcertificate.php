<?php
	include 'include/config';
	
	if(isset($_POST['getGiftCertificates'])){
		$mysqli = new mysqli($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("SELECT idGiftCertificate,unCode,GCName,GCAmount FROM giftcertificate WHERE `Status`=1")){
			$stmt->execute();
			$stmt->bind_result($idGiftCertificate,$unCode,$GCName,$GCAmount);
			$stmt->store_result();
			if($stmt->num_rows > 0){
				$item = array();
				$response['giftcertificate'] = array();
				while($stmt->fetch()){
					$item['idGiftCertificate'] = $idGiftCertificate;
					$item['unCode'] = $unCode;
					$item['GCName'] = $GCName;
					$item['GCAmount'] = $GCAmount;
					array_push($response['giftcertificate'],$item);
				}
				$response['Success'] = 1;
			}else{
				$response['Success'] = 0;
				$response['Message'] = 'No record(s) found';
			}
			$stmt->close();			
			echo json_encode($response);
		}else{
			$response['Success'] = 0;
			$response['Message'] = $stmt->error;
			echo json_encode($response);
		}
	}else{
		$response['Success'] = 0;
		$response["Message"] = "Required field(s) is missing";
		echo json_encode($response);	
	}
?>
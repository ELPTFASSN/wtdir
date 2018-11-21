<?php
	include 'include/config.inc.php';

	if(isset($_POST['getDenominations'])){
		$mysqli = new mysqli($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unDenomination,DValue From denomination where `Status`=1 Order By DValue")){
			$stmt->execute();
			$stmt->bind_result($idDenomination,$DValue);
			$stmt->store_result();
			if ($stmt->num_rows>0){
				$denomination = array();
				$response['denomination'] = array();
				while($stmt->fetch()){
					$denomination['unDenomination']=$idDenomination;
					$denomination['DValue']=$DValue;
					array_push($response['denomination'],$denomination);				
				}
				$response['Success']=1;
			}else{
				$response['Success']=0;
				$response['Message']='No record(s) found';
			}
		}
		$stmt->close();
		echo json_encode($response);
	}
?>
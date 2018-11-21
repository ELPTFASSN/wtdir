<?php
	include 'include/config.inc.php';

	if(isset($_POST['getEmployeeGroups'])){
		$mysqli = new mysqli($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unEmployeeGroup,EGName,EGLevel 
					From employeegroup 
					Where `Status` = 1 
					Order By EGName")){
			$stmt->execute();
			$stmt->bind_result($unEmployeeGroup,$EGName,$EGLevel);
			$stmt->store_result();
			if($stmt->num_rows > 0){
				$employeegroup = array();
				$response['employeegroup'] = array();
				while($stmt->fetch()){
					$employeegroup['unEmployeeGroup'] = $unEmployeeGroup;
					$employeegroup['EGName'] = $EGName;
					$employeegroup['EGLevel'] = $EGLevel;
					array_push($response['employeegroup'],$employeegroup);
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
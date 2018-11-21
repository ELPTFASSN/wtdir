<?php
//$_POST['Serial'] = 'A5A580702861D348';
//$_POST['Mac'] = '7D:92:46:86:32:6E';

	include 'include/config.inc.php';
	
	if(isset($_POST['getProductGroups'])){
		$mysqli = new mysqli($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unProductGroup,PGName,PGPriority 
						From productgroup 
						Where Status = 1 and unProductType = (Select unProductType From producttype Where PTName = 'Products' and Status = 1)")){
			$stmt->execute();
			$stmt->bind_result($unProductGroup,$PGName,$PGPriority);
			$stmt->store_result();
			if($stmt->num_rows > 0){
				$group = array();
				$response['productgroup'] = array();
				while($stmt->fetch()){
					$group['unProductGroup'] = $unProductGroup;
					$group['PGName'] = $PGName;
					$group['PGPriority'] = $PGPriority;
					array_push($response['productgroup'],$group);
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
<?php 
	include 'include/config.inc.php';

	if(isset($_POST['getEmployees'])){
		$_POST['idArea']= 1;
		$mysqli = new mysqli($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select employee.unEmployee,unEmployeeGroup,ELastName,EFirstName,EMiddleName,EUsername,EPassword From employee 
						Inner Join employeearea on employee.unEmployee = employeearea.unEmployee 
						Where unArea = ? and employee.Status = 1 and employeearea.Status = 1 
						Order By ELastName,EFirstName,EMiddleName")){
			$stmt->bind_param('i',$_POST['idArea']);
			$stmt->execute();
			$stmt->bind_result($unEmployee,$unEmployeeGroup,$ELastName,$EFirstName,$EMiddleName,$EUsername,$EPassword);
			$stmt->store_result();
			if($stmt->num_rows>0){
				$employee=array();
				$response['employee']=array();
				while($stmt->fetch()){
					$employee['unEmployee'] = $unEmployee;
					$employee['unEmployeeGroup'] = $unEmployeeGroup;
					$employee['ELastName'] = $ELastName;
					$employee['EFirstName'] = $EFirstName;
					$employee['EMiddleName'] = $EMiddleName;
					$employee['EUsername'] = $EUsername;
					$employee['EPassword'] = $EPassword;
					array_push($response['employee'],$employee);
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
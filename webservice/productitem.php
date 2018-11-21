<?php 
	include 'include/config.inc.php';
	
	if(isset($_POST['getProductItems'])){
		$mysqli = new mysqli($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select productitem.unProductItem,productitem.unProductGroup,PIName,TIDPrice,PIBulkDiscount From productitem
						Inner Join templateitemdata on productitem.unProductItem = templateitemdata.unProductItem
						Inner Join productgroup on productitem.unProductGroup = productgroup.unProductGroup
						Where unTemplateItemControl = ? and productitem.Status = 1 and templateitemdata.Status = 1 and productgroup.Status = 1
						and unProductType = (Select unProductType From producttype Where PTName = 'Products' and Status = 1)
						Order by PIName Asc")){
			$stmt->bind_param('i',$_POST['unTemplateItemControl']);
			$stmt->execute();
			$stmt->bind_result($unProductItem,$unProductGroup,$PIName,$TIDPrice,$PIBulkDiscount);
			$stmt->store_result();
			if($stmt->num_rows > 0){
				$item = array();
				$response['productitem'] = array();
				while($stmt->fetch()){
					$item['unProductItem'] = $unProductItem;
					$item['unProductGroup'] = $unProductGroup;
					$item['PIName'] = $PIName;
					$item['TIDPrice'] = $TIDPrice;
					$item['PIBulkDiscount'] = $PIBulkDiscount;
					array_push($response['productitem'],$item);
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
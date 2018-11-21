<?php 
	include 'var.inc.php';
	include 'class.inc.php';
	session_start();
	if ($_SESSION['Session'] == '') {header("location:end.php");}
	
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	
	if ($_SESSION['Session'] != $sessionid) {header("location:end.php");}

	if(isset($_POST['btnsavedelivery'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO deliverydata(unAccountUser,unProductItem,unProductUOM,DDQuantity,DDSAPQuantity)
VALUES (?,?,?,?,?)")){
			for($i=1;$i<=$_POST['hdnCount'];$i++){
				if(isset($_POST['txt-'.$i.'-qty'])){
					$stmt->bind_param('iiidd',$oAccountUser->unAccountUser,$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$_POST['txt-'.$i.'-qty'],$_POST['txt-'.$i.'-sapqty']);
					$stmt->execute();				
				}
			}
			$stmt->close();
		}
		//die($_POST['txtDCDocNum']);
		$unDeliveryControl = getMax("unDeliveryControl","deliverycontrol");
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Call CreateDeliveryControl(?,?,?,?,?,?,?,?)")){
			$stmt->bind_param('iiisiiss',$_SESSION['area'],$oAccountUser->unAccountUser,$unDeliveryControl,$_POST['txtDCDocNum'],$_POST['cmbDCBranchFrom'],$_POST['cmbDCBranchTo'],$_POST['dtpDCDate'],$_POST['txtDCComment']);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
	if(isset($_POST['btnupdatedelivery'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update deliverycontrol set unAccountUser=?,DCDocNum=?,DCDate=?,DCComments=?,unBranchFrom=?,unBranchTo=? Where unDeliveryControl=?")){
			$stmt->bind_param('iissiii',$oAccountUser->unAccountUser,$_POST['txtDCDocNum'],$_POST['dtpDCDate'],$_POST['txtDCComment'],$_POST['cmbDCBranchFrom'],$_POST['cmbDCBranchTo'],$_POST['undeliverycontrol']);
			$stmt->execute();
			$stmt->close();
		}
		
/*		ExecuteNonQuery("Update deliverydata set `Status` = 0 Where unDeliveryControl = ".$_POST['undeliverycontrol']);
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		for($i=1;$i<=$_POST['hdnCount'];$i++){
			if(isset($_POST['hdn-'.$i.'-undeliverydata'])){
				if($_POST['hdn-'.$i.'-undeliverydata']==0){
					if($stmt->prepare("Insert Into deliverydata (unDeliveryControl,unAccountUser,unProductItem,unProductUOM,DDQuantity) Values (?,?,?,?,?)")){
						$stmt->bind_param('iiiid',$_POST['undeliverycontrol'],$oAccountUser->unAccountUser,$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$_POST['txt-'.$i.'-qty']);
						$stmt->execute();
					}
				}else{
					if($stmt->prepare("Update deliverydata set unAccountUser=?,unProductItem=?,unProductUOM=?,DDQuantity=?,`Status`=1 Where unDeliveryData=?")){
						$stmt->bind_param('iiidi',$oAccountUser->unAccountUser,$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$_POST['txt-'.$i.'-qty'],$_POST['hdn-'.$i.'-undeliverydata']);
						$stmt->execute();
					}
				}
			}
		}
		$stmt->close();*/
		
		
		// ----- set status of existing entries to false
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update deliverydata set `Status` = 0 Where unDeliveryControl = ?")){
			$stmt->bind_param("i",$_POST['undeliverycontrol']);
			$stmt->execute();
			$stmt->close();	
		}
		
		// ----- allocate/overwrite/re-use entries who's statuses were set to false
		$i=0;
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select idDeliveryData From deliverydata Where unDeliveryControl = ? Order by idDeliveryData Asc")){
			$stmt->bind_param("i",$_POST['undeliverycontrol']);
			$stmt->execute();
			$stmt->bind_result($idDeliveryData);
			while($stmt->fetch()){
				for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
					if(isset($_POST['hdn-'.$j.'-iddeliverydata'])){
						$i=$j;
						break;
					}
				}
				$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt1 = $mysqli1->stmt_init();
				if($stmt1->prepare("Update deliverydata set unAccountUser=?,unProductItem=?,unProductUOM=?,DDQuantity=?,`Status`=1 Where idDeliveryData=?")){
					$stmt1->bind_param('iiidi',$oAccountUser->unAccountUser,$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$_POST['txt-'.$i.'-qty'],$idDeliveryData);
					$stmt1->execute();
					$stmt1->close();
				}
				$mysqli1->close();
				if($i==$_POST['hdnCount']){break;}
			}
			$stmt->close();
		}
	
		// ----- insert excess entries when needed
		for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
			if(isset($_POST['hdn-'.$j.'-product'])){
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Insert Into deliverydata (unDeliveryControl,unAccountUser,unProductItem,unProductUOM,DDQuantity) Values (?,?,?,?,?)")){
					$stmt->bind_param('iiiid',$_POST['undeliverycontrol'],$oAccountUser->unAccountUser,$_POST['hdn-'.$j.'-product'],$_POST['hdn-'.$j.'-unit'],$_POST['txt-'.$j.'-qty']);
					$stmt->execute();
					$stmt->close();
				}
			}
		}
		
		$mysqli->close();
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	if(isset($_POST['btnSaveMapping'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		$stmt1 = $mysqli->stmt_init();
		$stmt2 = $mysqli->stmt_init();
		$stmt3 = $mysqli->stmt_init();
		//die($_SESSION['bid']);
		//?><!--<script>alert('<?php echo $_POST['hdnidDC'];?>');</script>--><?php
		if($stmt->prepare("Call MAPDelivery(?,?)")){
			$stmt->bind_param('ii',$_POST['cmbDIRTo'],$_POST['hdnidDC']);
			$stmt->execute();
			$stmt->close();
		}
		if($stmt2->prepare("Call FinalResultRawMat(?,?)")){
			$stmt2->bind_param('ii',$_SESSION['did'],$_SESSION['bid']); //die($_SESSION['bid']);
			$stmt2->execute();
		}else{
			echo $stmt2->error();
			die();
		}
		if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			if($stmt3->prepare("Call FinalResultMix(?,?)")){
				$stmt3->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
				$stmt3->execute();
			}else{
				echo $stmt3->error();
				die();
			}
		}
		if($stmt1->prepare("Call FinalResultProduct(?,?)")){
			$stmt1->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
			$stmt1->execute();
		}else{
			echo $stmt1->error();
			die();
		}
		header('location:'.$_SERVER['HTTP_REFERER']);
		
	}
?>

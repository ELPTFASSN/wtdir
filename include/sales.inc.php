<?php 
	include 'var.inc.php';
	include 'class.inc.php';
	
	session_start();
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	
	if ($_SESSION['Session'] == '') {header("location:../end.php");}
	if ($_SESSION['Session'] != $sessionid) {header("location:../end.php");}
	
	if(isset($_POST['btnSaveSale'])){
		//SaveSales($_POST['idInventoryControl'],$_POST['txtBeginningBalance'],$_POST['txtTotalSales'],$_POST['txtCashDeposit'],$_POST['txtPettyCash'],$_POST['txtDiscount'],$_POST['txtGC'],$_POST['txtCC'],$_POST['txtLOA'],$_POST['txtEndingBalance'],$_POST['txtCashCount'],$_POST['txtShortage'],$_POST['idSales']);
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Call UpdateSales(?,?,?,?,?,?,?,?,?,?,?,?,?)")){
			$stmt->bind_param('iiddddddddddd',$_POST['unSales'],$_POST['unInventoryControl'],$_POST['txtBeginningBalance'],$_POST['txtTotalSales'],$_POST['txtCashDeposit'],$_POST['txtPettyCash'],$_POST['txtDiscount'],$_POST['txtGC'],$_POST['txtCC'],$_POST['txtLOA'],$_POST['txtEndingBalance'],$_POST['txtCashCount'],$_POST['txtShortage']);
			$stmt->execute();
			$stmt->close();
		}
		
		$ShortageSales = ExecuteReader("Select If(SShortage>0,0.0000,SShortage * -1) as result From sales Where unInventoryControl = ".$_POST['unInventoryControl']);
		$ShortageInventory = ExecuteReader("Select If(Sum(IDVarianceAmount)>0,Sum(IDVarianceAmount), 0.0000) as result From inventorydata 
											Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
											Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
											Where IDVarianceAmount > 0 and unInventoryControl = ".$_POST['unInventoryControl']." and not unShortageType = 1");
		$TotalQuota = ExecuteReader("Select SQuotaTotalAmount as result From sales Where `Status` = 1 and unInventoryControl = ".$_POST['unInventoryControl']);
		
		// ---------- Save Crew --------- //
		// ----- set status of existing entries to false
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update inventoryemployee Set `Status`=0 Where unInventoryControl = ?")){
			$stmt->bind_param("i",$_POST['unInventoryControl']);
			$stmt->execute();
			$stmt->close();
		}
		
		// ----- allocate/overwrite/re-use entries who's statuses were set to false
		$i=0;
		$unEmployee = 0;
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unInventoryEmployee From inventoryemployee Where unInventoryControl=? Order by unInventoryEmployee")){
			$stmt->bind_param("i",$_POST['unInventoryControl']);
			$stmt->execute();
			$stmt->bind_result($unInventoryEmployee);
			while($stmt->fetch()){
				for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
					if(isset($_POST['hdn-'.$j.'-name'])){
						$i=$j;
						break;
					}
				}
				if($unEmployee != $_POST['hdn-'.$i.'-name']){
					$share=0;
					switch($_POST['txt-'.$i.'-role']){
						case 'C':
							$share = $ShortageSales * ($_POST['txt-'.$i.'-cash'] / 100);
							break;
						case 'SC':
							$share = ($ShortageInventory * ($_POST['txt-'.$i.'-inventory'] / 100));
							break;
						case 'CSC':
							$share = ($ShortageInventory * ($_POST['txt-'.$i.'-inventory'] / 100)) + ($ShortageSales * ($_POST['txt-'.$i.'-cash'] / 100));
							break;
					}
					
					$quota = $TotalQuota * ($_POST['txt-'.$i.'-quota'] / 100);
					
					$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt1 = $mysqli1->stmt_init();
					if($stmt1->prepare("Update inventoryemployee Set unEmployee=?,IEAssignment=?,IECashPercent=?,IEInventoryPercent=?,IEQuotaPercent=?,IEAmount=?,IEQuotaAmount=?,`Status`=1 Where unInventoryEmployee=?")){
						$stmt1->bind_param('isdddddi',$_POST['hdn-'.$i.'-name'],$_POST['txt-'.$i.'-role'],$_POST['txt-'.$i.'-cash'],$_POST['txt-'.$i.'-inventory'],$_POST['txt-'.$i.'-quota'],$share,$quota,$unInventoryEmployee);
						$stmt1->execute();
						$stmt1->close();
					}
					$unEmployee = $_POST['hdn-'.$i.'-name'];
				}
				if($i==$_POST['hdnCount']){break;}
			}
			$stmt->close();
		}
	
		// ----- insert excess entries when needed
		for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
			if(isset($_POST['hdn-'.$j.'-name'])){
				$share=0;
				switch($_POST['txt-'.$j.'-role']){
					case 'C':
						$share = $ShortageSales * ($_POST['txt-'.$j.'-cash'] / 100);
						break;
					case 'SC':
						$share = ($ShortageInventory * ($_POST['txt-'.$j.'-inventory'] / 100));
						break;
					case 'CSC':
						$share = ($ShortageInventory * ($_POST['txt-'.$j.'-inventory'] / 100)) + ($ShortageSales * ($_POST['txt-'.$j.'-cash'] / 100));
						break;
				}
				
				$quota = $TotalQuota * ($_POST['txt-'.$j.'-quota'] / 100);
				
				//die($_POST['unInventoryControl'].'-'.$_POST['hdn-'.$j.'-name'].'-'.$_POST['txt-'.$j.'-role'].'-'.$_POST['txt-'.$j.'-cash'].'-'.$_POST['txt-'.$j.'-inventory'].'-'.$_POST['txt-'.$j.'-quota'].'-'.$share.'-'.$quota);
				
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Insert Into inventoryemployee (unInventoryControl,unEmployee,IEAssignment,IECashPercent,IEInventoryPercent,IEQuotaPercent,IEAmount,IEQuotaAmount) Values(?,?,?,?,?,?,?,?)")){
					$stmt->bind_param('iisddddd',$_POST['unInventoryControl'],$_POST['hdn-'.$j.'-name'],$_POST['txt-'.$j.'-role'],$_POST['txt-'.$j.'-cash'],$_POST['txt-'.$j.'-inventory'],$_POST['txt-'.$j.'-quota'],$share,$quota);
					$stmt->execute();
					$stmt->close();
				}
			}
		}
			
		// ---------- Save Denomination Count --------- //
		// ----- set status of existing entries to false
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("UPDATE denominationcount SET `Status`=0 WHERE unInventoryControl=?")){
			$stmt->bind_param("i",$_POST['unInventoryControl']);
			$stmt->execute();
			$stmt->close();
		}
		
		// ----- allocate/overwrite/re-use entries who's statuses were set to false
		$i=0;
		$unDenomination=0;
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unDenominationCount From denominationcount Where unInventoryControl=? Order by unDenominationCount")){
			$stmt->bind_param("i",$_POST['unInventoryControl']);
			$stmt->execute();
			$stmt->bind_result($unDenominationCount);
			while($stmt->fetch()){
				for($j=$i+1;$j<=$_POST['hdnDCount'];$j++){
					if(!empty($_POST['txtQty-'.$j])){
						$i=$j;
						break;
					}
				}

				if($unDenomination != $_POST['hdnDenomination-'.$i]){
					$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt1 = $mysqli1->stmt_init();
					if($stmt1->prepare("UPDATE denominationcount SET unDenomination=?,DCQuantity=?,DCAmount=?,`Status`=1 WHERE unDenominationCount=?")){
						$stmt1->bind_param("iddi",$_POST['hdnDenomination-'.$i],$_POST['txtQty-'.$i],$_POST['txtAmount-'.$i],$unDenominationCount);
						$stmt1->execute();
						$stmt1->close();
					}
					$unDenomination = $_POST['hdnDenomination-'.$i];
				}
				if($i==$_POST['hdnDCount']){break;}
			}
			$stmt->close();
		}
	
		// ----- insert excess entries when needed
		for($j=$i+1;$j<=$_POST['hdnDCount'];$j++){
			if($_POST['txtQty-'.$j] != ''){
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Insert Into denominationcount (unInventoryControl,unDenomination,DCQuantity,DCAmount) Values(?,?,?,?)")){
					$stmt->bind_param("iidd",$_POST['unInventoryControl'],$_POST['hdnDenomination-'.$j],$_POST['txtQty-'.$j],$_POST['txtAmount-'.$j]);
					$stmt->execute();
					$stmt->close();
				}
			}
		}
		$mysqli->close();
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
?>
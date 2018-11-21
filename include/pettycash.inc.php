<?php 
include 'var.inc.php';
include 'class.inc.php';

session_start();


if(isset($_POST['btnsavepettycash'])){
	$unPTC = $_POST['idPTC'];
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($unPTC != 0){
		if($stmt->prepare("Update pettycashcontrol Set unEmployee=?,PCCReferenceNumber=?,PCCDate=?,PCCAmount=? Where unPettyCashControl = ?")){
			$stmt->bind_param("issdi",$_POST['cmbEmployee'],$_POST['txtReferenceNumber'],$_POST['dtpDate'],$_POST['txtTotalAmount'],$unPTC);
			$stmt->execute();
			$stmt->close();
		}
	}else{	
		$unPTC = ExecuteReader("Select Max(unPettyCashControl) as `result` From pettycashcontrol");
		$unPTC = $unPTC + 1;
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Insert Into pettycashcontrol (unEmployee,PCCReferenceNumber,PCCDate,PCCAmount,unPettyCashControl) Values (?,?,?,?,?)")){
			$stmt->bind_param("issdi",$_POST['cmbEmployee'],$_POST['txtReferenceNumber'],$_POST['dtpDate'],$_POST['txtTotalAmount'],$unPTC);
			$stmt->execute();
			$stmt->close();	
		}	
	}
	
	// ---------- Petty Cash Control --------- //
	// ----- set status of existing entries to false
	/*$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Update pettycashdata Set `Status`=0 Where unPettyCashControl = ?")){
		$stmt->bind_param("i",$unPTC);
		$stmt->execute();
		$stmt->close();
	}
	
	// ----- allocate/overwrite/re-use entries who's statuses were set to false
	$i=0;
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unPettyCashData From pettycashdata Where unPettyCashControl = ? Order By unPettyCashData Asc")){
		$stmt->bind_param("i",$unPTC);
		$stmt->execute();
		$stmt->bind_result($unPettyCashData);
		while($stmt->fetch()){
			for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
				if(isset($_POST['txtdescription-'.$j])){
					$i=$j;
					break;
				}
			}
			$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt1 = $mysqli1->stmt_init();
			if($stmt1->prepare("Update pettycashdata Set PCDDescription=?,PCDAmount=?,`Status`=1 Where unPettyCashData=?")){
				$stmt1->bind_param('sii',$_POST['txtdescription-'.$i],$_POST['txtamount-'.$i],$unPettyCashData);
				$stmt1->execute();
				$stmt1->close();
			}
			if($i==$_POST['hdnCount']){break;}
		}
		$stmt->close();
	}*/

	// ----- insert excess entries when needed
	for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
		if(isset($_POST['txtdescription-'.$j])){
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("Insert Into pettycashdata (unPettyCashControl,PCDDescription,PCDAmount) Values (?,?,?)")){
				$stmt->bind_param('isi',$unPTC,$_POST['txtdescription-'.$j],$_POST['txtamount-'.$j]);
				$stmt->execute();
				$stmt->close();
			}
		}
	}
	
	$mysqli->close();
	header('location:'.$_SERVER['HTTP_REFERER']);
}

if(isset($_POST['hdnSaveMapping'])){
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Update pettycashcontrol Set unInventoryControl=? Where unPettyCashControl=?")){
		$stmt->bind_param("ii",$_POST['hdnInventoryControl'],$_POST['hdnSaveMapping']);
		$stmt->execute();
		$stmt->close();
	}
	
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Update sales Set SPettyCash=(Select PCCAmount From pettycashcontrol Where unPettyCashControl=? and `Status` = 1),
						SEndingBalance=(STotalSales - (SCashDeposit + SPettyCash + SDiscount + SGiftCertificate + SCreditCard + SLOA)),SShortage=(SCashCount - SEndingBalance) 
						Where unInventoryControl = ?")){
		$stmt->bind_param("ii",$_POST['hdnSaveMapping'],$_POST['hdnInventoryControl']);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
	header('location:'.$_SERVER['HTTP_REFERER']);
}

?>
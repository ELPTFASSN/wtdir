<?php 
	include 'var.inc.php';
	include 'class.inc.php';
	//include 'function.inc.php';
	
	session_start();
	if ($_SESSION['Session'] == '') {header("location:../end.php");}
	
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	if ($_SESSION['Session'] != $sessionid) {header("location:end.php");}
	
	if(isset($_POST['btnsavetransfer'])){
		/*for($i=0;$i<=$_POST['hdnCount'];$i++){
			if(isset($_POST['txt-'.$i.'-qty'])){
				echo 'hdnCount='.$_POST['hdnCount'].'<br>product='.$_POST['hdn-'.$i.'-product'].'<br>unit='.$_POST['hdn-'.$i.'-unit'].'<br>qty='.$_POST['txt-'.$i.'-qty'];	
			}
		}*/
		if(ExecuteReader('Select ifNull(unTransferControl,0) as `result` From transfercontrol Where `Status`=1 and TCNumber='.$_POST['txtitfno'])!=0){die('ITF Number exists.');}
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('INSERT INTO transferdata(unAccountUser,unProductItem,unProductUOM,TDQuantity) VALUES (?,?,?,?)')){
			for($i=0;$i<=$_POST['hdnCount'];$i++){
				if(isset($_POST['txt-'.$i.'-qty'])){
					$stmt->bind_param('iiii',$oAccountUser->unAccountUser,$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$_POST['txt-'.$i.'-qty']);
					//$stmt->bind_param('iii',$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$_POST['txt-'.$i.'-qty']);
					$stmt->execute();
					//echo '<br>i='.$i.'<br>hdnCount='.$_POST['hdnCount'].'<br>product='.$_POST['hdn-'.$i.'-product'].'<br>unit='.$_POST['hdn-'.$i.'-unit'].'<br>qty='.$_POST['txt-'.$i.'-qty'];
				}
			}
			$stmt->close();
		}
		$unTransferControl = getMax("unTransferControl","transfercontrol");
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('Call CreateTransferControl(?,?,?,?,?,?,?,?,?,?,?)')){
			$reason = '';
			if($_POST['itftxt-2']!=''){
				$reason = $_POST['rdreason'].' - '.$_POST['itftxt-2'];
			}else{
				$reason = $_POST['rdreason'];
			}
			$stmt->bind_param('iiiiiiiiiss',$_SESSION['area'],$oAccountUser->unAccountUser,$_POST['cmbbranchfrom'],$_POST['cmbbranchto'],$_POST['cmbfrom'],$_POST['cmbto'],$_POST['cmbdelivered'],$unTransferControl,$_POST['txtitfno'],$_POST['dtpdate'],$reason);
			$stmt->execute();
			$stmt->close();
		}
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
	if(isset($_POST['btnupdatetransfer'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update transfercontrol Set unBranchFrom=?,unBranchTo=?,unEmployeeFrom=?,unEmployeeTo=?,unEmployeeDelivery=?,unAccountUser=?,TCNumber=?,TCDate=? Where unTransferControl=?")){
			//die($_POST['cmbbranchfrom'].' - '.$_POST['cmbbranchto'].' - '.$_POST['cmbfrom'].' - '.$_POST['cmbto'].' - '.$_POST['cmbdelivered'].' - '.$oAccountUser->idAccountUser.' - '.$_POST['txtitfno'].' - '.$_POST['dtpdate'].' - '.$_POST['hdniditf']);
			$stmt->bind_param('iiiiiissi',$_POST['cmbbranchfrom'],$_POST['cmbbranchto'],$_POST['cmbfrom'],$_POST['cmbto'],$_POST['cmbdelivered'],$oAccountUser->unAccountUser,$_POST['txtitfno'],$_POST['dtpdate'],$_POST['idtransfercontrol']);
			$stmt->execute();
			$stmt->close();
		}
		
		// ----- set status of existing entries to false
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update transferdata Set `Status` = 0 Where unTransferControl = ?")){
			$stmt->bind_param("i",$_POST['idtransfercontrol']);
			$stmt->execute();
			$stmt->close();
		}
		
		// ----- allocate/overwrite/re-use entries who's statuses were set to false
		$i=0;
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select idTransferData From transferdata Where unTransferControl = ? Order By unTransferData Asc")){
			$stmt->bind_param("i",$_POST['idtransfercontrol']);
			$stmt->execute();
			$stmt->bind_result($unTransferData);
			while($stmt->fetch()){
				for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
					if(isset($_POST['hdn-'.$j.'-idtransferdata'])){
						$i=$j;
						break;
					}
				}
				$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt1 = $mysqli1->stmt_init();
				if($stmt1->prepare("Update transferdata Set unProductItem=?,unProductUOM=?,unAccountUser=?,TDQuantity=?,`Status`=1 Where idTransferData=?")){
					$stmt1->bind_param('iiidi',$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$oAccountUser->unAccountUser,$_POST['txt-'.$i.'-qty'],$unTransferData);
					$stmt1->execute();
					$stmt1->close();
				}
				if($i==$_POST['hdnCount']){break;}
			}
			$stmt->close();
		}
	
		// ----- insert excess entries when needed
		for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
			if(isset($_POST['hdn-'.$j.'-idtransferdata'])){
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Insert Into transferdata (unTransferControl,unProductItem,unProductUOM,TDQuantity,unAccountUser) Values (?,?,?,?,?)")){
					$stmt->bind_param('iiidi',$_POST['idtransfercontrol'],$_POST['hdn-'.$j.'-product'],$_POST['hdn-'.$j.'-unit'],$_POST['txt-'.$j.'-qty'],$oAccountUser->unAccountUser);
					$stmt->execute();
					$stmt->close();
				}
			}
		}

		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select ifNull(unInventoryControlFrom,0),ifNull(unInventoryControlTo,0) From transfercontrol Where unTransferControl=?")){
			$stmt->bind_param('i',$_POST['idtransfercontrol']);
			$stmt->execute();
			$stmt->bind_result($unInventoryControlFrom,$unInventoryControlTo);
			$stmt->fetch();
			$stmt->close();
		}
		
		if($unInventoryControlFrom != 0){
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare('Call MAPTransferIUpdate(?)')){
				$stmt->bind_param('i',$unInventoryControlFrom);
				$stmt->execute();
				$stmt->close();
			}
		}
		
		if($unInventoryControlTo != 0){
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare('Call MAPTransferIUpdate(?)')){
				$stmt->bind_param('i',$unInventoryControlTo);
				$stmt->execute();
				$stmt->close();
			}
		}
				
		$mysqli->close();
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
	if(isset($_POST['btnSaveMapping'])){
		$isfrom = (isset($_POST['cmbinventorysheetfrom'])?$_POST['cmbinventorysheetfrom']:0);
		$isto = (isset($_POST['cmbinventorysheetto'])?$_POST['cmbinventorysheetto']:0);
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		/*$stmt = $mysqli->stmt_init();
		if($stmt->prepare('Call MAPTransfer(?,?,?)')){
			$stmt->bind_param('iii',$isfrom,$isto,$_POST['hdnselected']);
			$stmt->execute();
			$stmt->close();
		}*/
		
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('Call MAPTransferFrom(?,?)')){
			$stmt->bind_param('ii',$isfrom,$_POST['hdnselected']);
			$stmt->execute();
			$stmt->close();
		}
		
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('Call MAPTransferTo(?,?)')){
			$stmt->bind_param('ii',$isto,$_POST['hdnselected']);
			$stmt->execute();
			$stmt->close();
		}
		
		if($isfrom != 0){
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare('Call MAPTransferIUpdate(?,?)')){
				$stmt->bind_param('ii',$isfrom,$_POST['hdnselected']);
				$stmt->execute();
				$stmt->close();
			}
		}
		
		if($isto != 0){
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare('Call MAPTransferIUpdate(?,?)')){
				$stmt->bind_param('ii',$isto,$_POST['hdnselected']);
				$stmt->execute();
				$stmt->close();
			}
		}
		$stmt1 = $mysqli->stmt_init();
		$stmt2 = $mysqli->stmt_init();
		$stmt3 = $mysqli->stmt_init();
		if($stmt2->prepare("Call FinalResultRawMat(?,?)")){
			$stmt2->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
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
		$mysqli->close();
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
?>
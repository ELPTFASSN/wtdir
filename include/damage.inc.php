<?php
	include 'var.inc.php';
	include 'class.inc.php';
	
	session_start();
	if ($_SESSION['Session'] == '') {header("location:../end.php");}
	
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	if ($_SESSION['Session'] != $sessionid) {header("location:../end.php");}
	
	if(isset($_POST['btnsavedamage'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Call CreateDamageData(?,?,?,?)")){
			for($i=1;$i<=$_POST['hdnCount'];$i++){
				if(isset($_POST['txt-'.$i.'-qty'])){
					$stmt->bind_param('iiii',$oAccountUser->unAccountUser,$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$_POST['txt-'.$i.'-qty']);
					$stmt->execute();
				}
			}	
			$stmt->close();
		}
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Call CreateDamageControl(?,?,?,?,?,?,?,?)")){
			$stmt->bind_param('iiiiiiss',$_SESSION['area'],$oAccountUser->unAccountUser,getMax("unDamageControl","damagecontrol"),$_POST['txtDCDocNum'],$_POST['cmbDCBranchFrom'],$_POST['cmbDCBranchTo'],$_POST['dtpDCDate'],$_POST['txtDCComment']);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	if(isset($_POST['btnupdatedamage'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update damagecontrol set unAccountUser=?,DCDocNum=?,DCDate=?,DCComments=?,unBranchFrom=?,unBranchTo=? Where unDamageControl=?")){
			$stmt->bind_param('iissiii',$oAccountUser->unAccountUser,$_POST['txtDCDocNum'],$_POST['dtpDCDate'],$_POST['txtDCComment'],$_POST['cmbDCBranchFrom'],$_POST['cmbDCBranchTo'],$_POST['undamagecontrol']);
			$stmt->execute();
			$stmt->close();
		}
			
		// ----- set status of existing entries to false
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update damagedata set `Status` = 0 Where unDamageControl = ? ")){
			$stmt->bind_param("i",$_POST['undamagecontrol']);
			$stmt->execute();
			$stmt->close();	
		}
		
		// ----- allocate/overwrite/re-use entries who's statuses were set to false
		$i=0;
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unDamageData From damagedata Where unDamageControl = ? Order by unDamageData Asc")){
			$stmt->bind_param("i",$_POST['undamagecontrol']);
			$stmt->execute();
			$stmt->bind_result($unDamageData);
			while($stmt->fetch()){
				for($j=$i+1;$j<=$_POST['hdnCount'];$j++){
					if(isset($_POST['hdn-'.$j.'-undamagedata'])){
						$i=$j;
						break;
					}
				}
				$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt1 = $mysqli1->stmt_init();
				if($stmt1->prepare("Update damagedata set unAccountUser=?,unProductItem=?,unProductUOM=?,DDQuantity=?,`Status`=1 Where unDamageData=?")){
					$stmt1->bind_param('iiidi',$oAccountUser->unAccountUser,$_POST['hdn-'.$i.'-product'],$_POST['hdn-'.$i.'-unit'],$_POST['txt-'.$i.'-qty'],$unDamageData);
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
			if(isset($_POST['hdn-'.$j.'-undamagedata'])){
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Insert Into damagedata (unDamageControl,unAccountUser,unProductItem,unProductUOM,DDQuantity) Values (?,?,?,?,?)")){
					$stmt->bind_param('iiiid',$_POST['undamagecontrol'],$oAccountUser->unAccountUser,$_POST['hdn-'.$j.'-product'],$_POST['hdn-'.$j.'-unit'],$_POST['txt-'.$j.'-qty']);
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
		if($stmt->prepare("Call MAPDamage(?,?)")){
			$stmt->bind_param('ii',$_POST['cmbDIRFrom'],$_POST['hdnunDC']);
			$stmt->execute();
			$stmt->close();
		}
		
		$stmt1 = $mysqli->stmt_init();
		$stmt2 = $mysqli->stmt_init();
		$stmt3 = $mysqli->stmt_init(); 
		if($stmt1->prepare("Call FinalResultProduct(?)")){
			$stmt1->bind_param('i',$_SESSION['did']);
			$stmt1->execute();
		}else{
			echo $stmt1->error();
			die();
		}
		if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			if($stmt2->prepare("Call FinalResultRawMat(?,?)")){
				$stmt2->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
				$stmt2->execute();
			}else{
				echo $stmt2->error();
				die();
			}
		}
		if($stmt3->prepare("Call FinalResultMix(?,?)")){
			$stmt3->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
			$stmt3->execute();
		}else{
			echo $stmt3->error();
			die();
		}
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
?>
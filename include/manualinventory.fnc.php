<?php 
	include 'var.inc.php';
	include 'class.inc.php';
	
	session_start();
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	
	if ($_SESSION['Session'] == '') {header("location:../end.php");}
	if ($_SESSION['Session'] != $sessionid) {header("location:../end.php");}
	
	if(isset($_POST['hdnCreateInventory'])){
		CreateNewInventorySheet($_POST['cmbBranch'],$oAccountUser->unAccountUser,$_POST['dtpDate'],$_POST['txtSheetNumber'],$_POST['txtRemark']);
		$unInventoryControl = ExecuteReader("Select Max(unInventoryControl) as `result` From inventorycontrol");
		if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			header('location:../manualinventory.php?&bid='.$_POST['cmbBranch'].'&did='.$unInventoryControl.'&temp=1&type=2');
		}else{
			header('location:../inventory.php?&bid='.$_POST['cmbBranch'].'&did='.$unInventoryControl.'&temp=1&type=2');
		}
	}
	
	if(isset($_POST['btnSaveCC'])){
		$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		//die(str_replace(",", "", $_POST['txt-27-5']));
		if($stmt->prepare('Call FinalResultCashCount(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')){
			$stmt->bind_param('iiiiiiiiiiiiddddddddddddddddddii',str_replace(",", "", $_POST['txt-1-4']),str_replace(",", "", $_POST['txt-2-4']),str_replace(",", "", $_POST['txt-3-4']),str_replace(",", "", $_POST['txt-4-4']),str_replace(",", "", $_POST['txt-5-4']),str_replace(",", "", $_POST['txt-6-4']),str_replace(",", "", $_POST['txt-1-5']),str_replace(",", "", $_POST['txt-2-5']),str_replace(",", "", $_POST['txt-3-5']),str_replace(",", "", $_POST['txt-4-5']),str_replace(",", "", $_POST['txt-5-5']),str_replace(",", "", $_POST['txt-6-5']),str_replace(",", "", $_POST['txt-8-5']),str_replace(",", "", $_POST['txt-9-5']),str_replace(",", "", $_POST['txt-10-5']),str_replace(",", "", $_POST['txt-11-5']),str_replace(",", "", $_POST['txt-12-5']),str_replace(",", "", $_POST['txt-13-5']),str_replace(",", "", $_POST['txt-18-5']),str_replace(",", "", $_POST['txt-17-5']),str_replace(",", "", $_POST['txt-19-5']),str_replace(",", "", $_POST['txt-14-5']),str_replace(",", "", $_POST['txt-15-5']),str_replace(",", "", $_POST['txt-16-5']),str_replace(",", "", $_POST['txt-27-5']),str_replace(",", "", $_POST['txt-adjpmix']),str_replace(",", "", $_POST['txt-adjwater']),str_replace(",", "", $_POST['txt-adjsugar']),str_replace(",", "", $_POST['txt-adjsupplies']),str_replace(",", "", $_POST['txt-adjcashfund']),$_SESSION['did'],$_SESSION['bid']);
			$stmt->execute();
			//die($_POST['txt-1-4'].'---'.$_POST['txt-2-4'].'---'.$_POST['txt-3-4'].'---'.$_POST['txt-4-4'].'---'.$_POST['txt-5-4'].'---'.$_POST['txt-6-4'].'---'.$_POST['txt-1-5'].'---'.$_POST['txt-2-5'].'---'.$_POST['txt-3-5'].'---'.$_POST['txt-4-5'].'---'.$_POST['txt-5-5'].'---'.$_POST['txt-6-5'].'---'.$_POST['txt-8-5'].'---'.$_POST['txt-9-5'].'---'.$_POST['txt-10-5'].'---'.$_POST['txt-11-5'].'---'.$_POST['txt-12-5'].'---'.$_POST['txt-13-5'].'---'.$_POST['txt-18-5'].'---'.$_POST['txt-17-5'].'---'.$_POST['txt-19-5'].'---'.$_POST['txt-14-5'].'---'.$_POST['txt-15-5'].'---'.$_POST['txt-16-5'].'---'.$_POST['txt-27-5'].'---'.$_POST['txt-adjpmix'].'---'.$_POST['txt-adjwater'].'---'.$_POST['txt-adjsugar'].'---'.$_POST['txt-adjsupplies'].'---'.$_POST['txt-adjcashfund'].'---'.$_SESSION['did'].'---'.$_SESSION['bid']);
		}else{
			echo $stmt->error();
			die('sa save may sala');
		}
		$stmt->close();
		for( $i = 1; $i<9; $i++ ){
				//if($_POST['cod-0-'.$i]>0){
					$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmtcheck=$mysqli->stmt_init();
					if($stmtcheck->prepare('Select unSalesCrew From salescrew Where unSalesCrew=? AND `unBranch`=? AND `unInventoryControl`=?')){
						$stmtcheck->bind_param('iii',$i,$_SESSION['bid'],$_SESSION['did']);
						$stmtcheck->execute();
						$stmtcheck->bind_result($ifExists);
						$stmtcheck->fetch();
					}
					$stmtcheck->close();
					
					if($ifExists==$i){
						$stmtup=$mysqli->stmt_init();
						if($stmtup->prepare('UPDATE `salescrew` SET `unEmployee`=?,`SCCode1`=?, `SCCode2`=?, `SCInAM`=?,`SCOutAM`=?,`SCInPM`=?,`SCOutPM`=?,`SCHours`=? WHERE `unSalesCrew`=? AND`unBranch`=? AND `unInventoryControl`=?')){
							$stmtup->bind_param('issssssiiii',$_POST['cod-0-'.$i],$_POST['cod-1-'.$i],$_POST['cod-2-'.$i],$_POST['cod-3-'.$i],$_POST['cod-4-'.$i],$_POST['cod-5-'.$i],$_POST['cod-6-'.$i],$_POST['cod-7-'.$i],$ifExists,$_SESSION['bid'],$_SESSION['did']);
							$stmtup->execute();
						}
						$stmtup->close();
					}else{
						$stmtins=$mysqli->stmt_init();
						if($stmtins->prepare('INSERT INTO `salescrew` (unSalesCrew,unEmployee,SCCode1,SCCode2,SCInAM,SCOutAM,SCInPM,SCOutPM,SCHours,unBranch,unInventoryControl) VALUES (?,?,?,?,?,?,?,?,?,?,?) ')){
							$stmtins->bind_param('iissssssiii',$i,$_POST['cod-0-'.$i],$_POST['cod-1-'.$i],$_POST['cod-2-'.$i],$_POST['cod-3-'.$i],$_POST['cod-4-'.$i],$_POST['cod-5-'.$i],$_POST['cod-6-'.$i],$_POST['cod-7-'.$i],$_SESSION['bid'],$_SESSION['did']);
							$stmtins->execute();
						}
						$stmtins->close();
					}
				//}
		}
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	if(isset($_POST['btnMISave'])){		
		
		$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if ($_SESSION['type'] == ExecuteReader("Select unProductType as `result` From producttype Where PTName='Products'")){
			if($stmt->prepare('Call UpdateItemProduct(?,?,?,?,?,?,?,?,?,?,?,?,?)')){
				for($i=0;$i<$_SESSION['rowcount'];$i++){
					list($price,$uninventorydata,$unproductitem) = explode('-',$_POST['hdn-'.$i.'-pip']);
					
					/*$processin = $_POST['txt-'.$i.'-0'] + $_POST['txt-'.$i.'-transfer'] - $_POST['txt-'.$i.'-damage'] - $_POST['txt-'.$i.'-sold'] - $_POST['txt-'.$i.'-end'];*/
					$soldamount = $price * $_POST['txt-'.$i.'-sold'];
					$endtotal = $_POST['txt-'.$i.'-end'];
					$dirusage = 0;
					
					$stmt->bind_param('iiidddddddddi',$_SESSION['bid'],$_SESSION['did'],$uninventorydata,$_POST['txt-'.$i.'-0'],$_POST['txt-'.$i.'-transfer'],$_POST['txt-'.$i.'-damage'],$_POST['txt-'.$i.'-processin'],$dirusage,$_POST['txt-'.$i.'-end'],$_POST['txt-'.$i.'-sold'],$soldamount,$endtotal,$unproductitem);
					$stmt->execute();
				}	
			}else{
				echo $stmt->error();
				die('sa save may sala');
			}
			
			if($stmt->prepare("Call FinalResultProduct(?,?)")){
				$stmt->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
				$stmt->execute();
			}else{
				echo $stmt->error();
				die();
			}
			
		}elseif ($_SESSION['type'] == ExecuteReader("Select unProductType as `result` From producttype Where PTName='Rawmats'")){
						
			if($stmt->prepare('Call UpdateItemRawMat(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')){
				for($i=0;$i<$_SESSION['rowcount'];$i++){
					list($cost,$uninventorydata,$ratio,$processout,$unproductitem) = explode('-',$_POST['hdn-'.$i.'-cidrpp']);
					
					$filler = 0.0000;
					$endtotal = $_POST['txt-'.$i.'-6'];
					$dirusage = $_POST['txt-'.$i.'-2'] + $_POST['txt-'.$i.'-3'] + $_POST['txt-'.$i.'-4'] - $_POST['txt-'.$i.'-5'] - $_POST['txt-'.$i.'-6'];
					$varianceqty = $dirusage + $_POST['txt-'.$i.'-3']  - $processout;
					$varianceamt = $cost * $varianceqty;
					$stmt->bind_param('iiidddddddddddddi',$_SESSION['bid'],$_SESSION['did'],$uninventorydata,$_POST['txt-'.$i.'-2'],$_POST['txt-'.$i.'-3'],$_POST['txt-'.$i.'-4'],$_POST['txt-'.$i.'-5'],$dirusage,$filler,$filler,$_POST['txt-'.$i.'-6'],$dirusage,$varianceqty,$cost,$varianceamt,$filler,$unproductitem);
					$stmt->execute();
				}
			}
			if($stmt->prepare("Call FinalResultRawMat(?,?)")){
				$stmt->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
				$stmt->execute();
				
				/*function backup FinalResultRawMat:
					DROP PROCEDURE `FinalResultRawMat`//
					CREATE DEFINER=`root`@`%` PROCEDURE `FinalResultRawMat`(
					IN punInventoryControl INT(11)
					)
					BEGIN
					Update inventorydata as Dest,(
					Select inventorydata.unInventoryControl as unCtrl,inventorydata.unProductItem as unProd,
					productconversion.PCRatio as ConvRatio
					from inventorydata inner join productconversion on 
					(inventorydata.unProductItem = productconversion.unProductItem)
					where productconversion.PCSet = 'F' and inventorydata.unInventoryControl = punInventoryControl
					) as Src
					Set Dest.IDEndTotal = round(Dest.IDEndFraction * Src.ConvRatio,4) + Dest.IDEndWhole
					,Dest.IDDIRUsage  = (Dest.IDStart + Dest.IDDelivery + Dest.IDTransfer - Dest.IDDamage) - (round(Dest.IDEndFraction * Src.ConvRatio,4) + Dest.IDEndWhole)
					,Dest.IDVarianceQTY = ((Dest.IDStart + Dest.IDDelivery + Dest.IDTransfer - Dest.IDDamage) - (round(Dest.IDEndFraction * Src.ConvRatio,4) + Dest.IDEndWhole)) - Dest.IDProcessOut
					,Dest.IDVarianceAmount = (((Dest.IDStart + Dest.IDDelivery + Dest.IDTransfer - Dest.IDDamage) - (round(Dest.IDEndFraction * Src.ConvRatio,4) + Dest.IDEndWhole)) - Dest.IDProcessOut) * Dest.IDCharge
					Where (Dest.unInventoryControl = punInventoryControl and Dest.unProductItem = Src.unProd);
					
					
					 Set @MyBranchID = (Select ifnull(unBranch,0) From inventorycontrol Where unInventoryControl = punInventoryControl LIMIT 1);
					 Set @MyICInventoryNumber = (Select ifnull(ICInventoryNumber,0) From inventorycontrol Where unInventoryControl = punInventoryControl LIMIT 1);
					 Set @Nextuninventorycontrol = (Select ifnull(unInventoryControl,0) From inventorycontrol where ICInventoryNumber = @MyICInventoryNumber + 1 And unBranch = @MyBranchID);
					
					 if @Nextuninventorycontrol > 0 then
					Update inventorydata as Dest,(Select unProductItem,IDEndTotal from inventorydata Where unInventoryControl = punInventoryControl) as Src
					set Dest.IDStart = Src.IDEndTotal 
					Where (unInventoryControl = @Nextuninventorycontrol) And (Dest.unProductItem = Src.unProductItem) ;
					END IF;
					
					
					END

				*/
			}else{
				die($_SESSION['did'].'------------------'.$_SESSION['bid']);
			}
			if($stmt->prepare("Call FinalResultProduct(?,?)")){
				$stmt->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
				$stmt->execute();
			}else{
				echo $stmt->error();
				die();
			}
			$stmt->close();
		}elseif ($_SESSION['type'] == ExecuteReader("Select unProductType as `result` From producttype Where PTName='Mix'")){
			if($stmt->prepare('Call UpdateItemMix(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')){
				for($i=0;$i<$_SESSION['rowcount'];$i++){
					list($cost,$uninventorydata,$ratio,$processout,$unproductitem) = explode('-',$_POST['hdn-'.$i.'-cidrpp']);
					
					//$endtotal = ($_POST['txt-'.$i.'-1'] * $ratio) + $_POST['txt-'.$i.'-0'];
					$filler = 0.0000;
					$endtotal = $_POST['txt-'.$i.'-6'];
					$dirusage = ($_POST['txt-'.$i.'-2'] + $_POST['txt-'.$i.'-3'] + $_POST['txt-'.$i.'-4'] - $_POST['txt-'.$i.'-5'] - $endtotal) * 8.5;
					$varianceqty = $dirusage - $processout;/*NO FUNCTION*/
					$varianceamt = $cost * $varianceqty;/*NO FUNCTION*/ 
					
					//echo $i.'---'.$_SESSION['bid'].'---'.$_SESSION['did'].'---'.$uninventorydata.'---'.$_POST['txt-'.$i.'-0'].'---'.$_POST['txt-'.$i.'-delivery'].'---'.$_POST['txt-'.$i.'-transfer'].'---'.$_POST['txt-'.$i.'-damage'].'---'.$processout.'---WHOLE:'.$_POST['txt-'.$i.'-1'].'---FRACTION:'.$_POST['txt-'.$i.'-2'].'---TOTAL:'.$_POST['txt-'.$i.'-endtotal'].'---'.$dirusage.'---'.$varianceqty.'---'.$cost.'---'.$varianceamt.'---'.$unproductitem.'<br>';
					$stmt->bind_param('iiidddddddddddddi',$_SESSION['bid'],$_SESSION['did'],$uninventorydata,$_POST['txt-'.$i.'-2'],$_POST['txt-'.$i.'-3'],$_POST['txt-'.$i.'-4'],$_POST['txt-'.$i.'-5'],$processout,$filler,$filler,$endtotal,$dirusage,$varianceqty,$cost,$varianceamt,$filler,$unproductitem);
					$stmt->execute();
				}
				//die('');
			}
			
			if($stmt->prepare("Call FinalResultRawMat(?,?)")){
				$stmt->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
				$stmt->execute();
			}
			
			if($stmt->prepare("Call FinalResultMix(?,?)")){
				$stmt->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
				$stmt->execute();
			}
			
			if($stmt->prepare("Call FinalResultProduct(?,?)")){
				$stmt->bind_param('ii',$_SESSION['did'],$_SESSION['bid']);
				$stmt->execute();
			}else{
				echo $stmt->error();
				die();
			}
			$stmt->close();
		}		
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
	if(isset($_POST['btnEditInventorySheet'])){
		ExecuteNonQuery("Update inventorycontrol Set unBranch=".$_POST['cmbBranch'].", ICDate='".$_POST['dtpDate']."', ICRemarks='".$_POST['txtRemark']."', ICNumber='".$_POST['txtSheetNumber']."' Where unInventoryControl=".$_SESSION['did']);
		if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			header('location:../manualinventory.php?&bid='.$_POST['cmbBranch'].'&did='.$_SESSION['did'].'&type=2');
		}else{
			header('location:../inventory.php?&bid='.$_POST['cmbBranch'].'&did='.$_SESSION['did'].'&type=2');
		}
	}
?>
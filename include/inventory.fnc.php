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
	
	if(isset($_POST['btnSave'])){										
		$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if ($_SESSION['type'] == ExecuteReader("Select unProductType as `result` From producttype Where PTName='Products'")){
			if($stmt->prepare('Call UpdateItemProduct(?,?,?,?,?,?,?,?,?,?,?,?,?)')){
				for($i=0;$i<$_SESSION['rowcount'];$i++){
					list($price,$uninventorydata,$unproductitem) = explode('-',$_POST['hdn-'.$i.'-pip']);
					
					$processin = $_POST['txt-'.$i.'-0'] + $_POST['txt-'.$i.'-transfer'] - $_POST['txt-'.$i.'-damage'] - $_POST['txt-'.$i.'-sold'] - $_POST['txt-'.$i.'-end'];
					$soldamount = $price * $_POST['txt-'.$i.'-sold'];
					$endtotal = $_POST['txt-'.$i.'-end'];
					$dirusage = 0;
					
					$stmt->bind_param('iiidddddddddi',$_SESSION['bid'],$_SESSION['did'],$uninventorydata,$_POST['txt-'.$i.'-0'],$_POST['txt-'.$i.'-transfer'],$_POST['txt-'.$i.'-damage'],$processin,$dirusage,$_POST['txt-'.$i.'-end'],$_POST['txt-'.$i.'-sold'],$soldamount,$endtotal,$unproductitem);
					$stmt->execute();
				}	
			//$stmt->close();
			//die("----");
			}else{
				echo $stmt->error();
				die('sa save may sala');
			}
			
			//$stmt=$mysqli->stmt_init();
			if($stmt->prepare("Call FinalResultProduct(?)")){
				$stmt->bind_param('i',$_SESSION['did']);
				$stmt->execute();
			}else{
				echo $stmt->error();
				die();
			}
			
			// ----- deprecated
			//$stmt=$mysqli->stmt_init();
			/*if($stmt->prepare('Call CreateProcessOut(?)')){ 
				$stmt->bind_param('i',$_SESSION['did']);
				$stmt->execute();
				//$stmt->close();
			}
			*/
			
			/*$sQuery = "Select Sum(IDSoldAmount) as `result` From inventorydata
						Inner Join productitem ON inventorydata.unProductItem = productitem.unProductItem
						Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
						Where unInventoryControl = ".$_SESSION['did']." and unProductType = (Select unProductType From producttype Where Status=1 and PTName='Products')";
			
			$TotalSales = ExecuteReader($sQuery);*/
			
			//$stmt=$mysqli->stmt_init();
			/*if($stmt->prepare('Update sales Set STotalSales=?,SEndingBalance=(Select SBeginningBalance + STotalSales - SCashDeposit - SPettyCash - SDiscount as `EndingBalance`),SShortage=(SCashCount - SEndingBalance) Where unInventoryControl = ?')){
				$stmt->bind_param('di',$TotalSales,$_SESSION['did']);
				$stmt->execute();
				//$stmt->close();
			}
			$stmt->close();
			*/
		}elseif ($_SESSION['type'] == ExecuteReader("Select unProductType as `result` From producttype Where PTName='Rawmats'")){
						
			if($stmt->prepare('Call UpdateItemRawMat(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')){
				for($i=0;$i<$_SESSION['rowcount'];$i++){
					list($cost,$uninventorydata,$ratio,$processout,$unproductitem) = explode('-',$_POST['hdn-'.$i.'-cidrpp']);
					
					$endtotal = ($_POST['txt-'.$i.'-2'] * $ratio) + $_POST['txt-'.$i.'-1'];
					$dirusage = $_POST['txt-'.$i.'-0'] + $_POST['txt-'.$i.'-delivery'] + $_POST['txt-'.$i.'-transfer'] - $_POST['txt-'.$i.'-damage'] - $endtotal;
					$varianceqty = $dirusage + $_POST['txt-'.$i.'-3']  - $processout;
					$varianceamt = $cost * $varianceqty;
					
					//echo $i.'---'.$_SESSION['bid'].'---'.$_SESSION['did'].'---'.$uninventorydata.'---'.$_POST['txt-'.$i.'-0'].'---'.$_POST['txt-'.$i.'-delivery'].'---'.$_POST['txt-'.$i.'-transfer'].'---'.$_POST['txt-'.$i.'-damage'].'---'.$processout.'---WHOLE:'.$_POST['txt-'.$i.'-1'].'---FRACTION:'.$_POST['txt-'.$i.'-2'].'---TOTAL:'.$_POST['txt-'.$i.'-endtotal'].'---'.$dirusage.'---'.$varianceqty.'---'.$cost.'---'.$varianceamt.'---'.$unproductitem.'<br>';
					$stmt->bind_param('iiidddddddddddddi',$_SESSION['bid'],$_SESSION['did'],$uninventorydata,$_POST['txt-'.$i.'-0'],$_POST['txt-'.$i.'-delivery'],$_POST['txt-'.$i.'-transfer'],$_POST['txt-'.$i.'-damage'],$processout,$_POST['txt-'.$i.'-1'],$_POST['txt-'.$i.'-2'],$endtotal,$dirusage,$varianceqty,$cost,$varianceamt,$_POST['txt-'.$i.'-3'],$unproductitem);
					$stmt->execute();
				}
				//die('');
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
			}
			$stmt->close();
		}		
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
	if(isset($_POST['btnEditInventorySheet'])){
		ExecuteNonQuery("Update inventorycontrol Set unBranch=".$_POST['cmbBranch'].", ICDate='".$_POST['dtpDate']."', ICRemarks='".$_POST['txtRemark']."', ICNumber='".$_POST['txtSheetNumber']."' Where unInventoryControl=".$_SESSION['did']);
		if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			header('location:../REVinventory.php?&bid='.$_POST['cmbBranch'].'&did='.$_SESSION['did'].'&type=2');
		}else{
			header('location:../inventory.php?&bid='.$_POST['cmbBranch'].'&did='.$_SESSION['did'].'&type=2');
		}
	}
?>
<?php
	include 'var.inc.php';
	include 'class.inc.php';
	
	session_start();
	if ($_SESSION['Session'] == '') {header("location:end.php");}
	
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	
	if ($_SESSION['Session'] != $sessionid) {header("location:end.php");}
	
	/* ----- Functions ----- */
	
	function addcustomer($i){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO customer(CLastName,CFirstName,CMiddleName,CAlias) VALUES (?,?,?,?)")){
			$stmt->bind_param('ssss',$_POST['txtlastname-'.$i],$_POST['txtfirstname-'.$i],$_POST['txtmiddlename-'.$i],$_POST['txtalias-'.$i]);
			$stmt->execute();
			$stmt->close();			
		}
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unCustomer From customer Where `Status`=1 and CLastName=? and CFirstName=? and CMiddleName=? and CAlias=?")){
			$stmt->bind_param('ssss',$_POST['txtlastname-'.$i],$_POST['txtfirstname-'.$i],$_POST['txtmiddlename-'.$i],$_POST['txtalias-'.$i]);
			$stmt->execute();
			$stmt->bind_result($unCustomer);
			$stmt->fetch();
			$stmt->close();			
		}
		return $unCustomer;
	}
	
	function addcustomercard($i,$unCustomer){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO customercard(unCustomer,unDiscountType,CCNumber) VALUES (?,?,?)")){
			$stmt->bind_param('iis',$unCustomer,$_POST['hdnuncardtype-'.$i],$_POST['txtcard-'.$i]);
			$stmt->execute();
			$stmt->close();			
		}
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unCustomerCard From customercard Where `Status`=1 and unCustomer=? and unDiscountType=? and CCNumber=?")){
			$stmt->bind_param('iis',$unCustomer,$_POST['hdnuncardtype-'.$i],$_POST['txtcard-'.$i]);
			$stmt->execute();
			$stmt->bind_result($unCustomerCard);
			$stmt->fetch();
			$stmt->close();			
		}
		return $unCustomerCard;
	}

	/* ----- POST Actions ----- */

	if(isset($_POST['btnSaveMapping'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update discountcontrol Set unInventoryControl=? where unDiscountControl=?")){
			$stmt->bind_param('ii',$_POST['hdndun'],$_POST['hdnunDC']);
			$stmt->execute();
			$stmt->close();
		}

		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select Sum(DCDiscount) as DCDiscount From discountcontrol Where `Status`=1 and unInventoryControl=?")){
			$stmt->bind_param('i',$_POST['hdndun']);
			$stmt->execute();
			$stmt->bind_result($DCDiscount);
			$stmt->fetch();
			$stmt->close();			
		}

		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update sales set SDiscount=?,SEndingBalance=(Select SBeginningBalance+STotalSales-SCashDeposit-SPettyCash-SDiscount) Where unInventoryControl=?")){
			$stmt->bind_param('di',$DCDiscount,$_POST['hdndun']);
			$stmt->execute();
			$stmt->close();			
		}

		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
	if(isset($_POST['btnadddiscount'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Insert Into discountcontrol 
						(unArea,unBranch,unDiscountType,unEmployeePrepared,unEmployeeReceived,DCReference,DCInvoice,DCPax,DCDate)
						values (?,?,?,?,?,?,?,?,?)")){
			$stmt->bind_param('iiiiissis',$_SESSION['area'],$_POST['cmbbranch'],$_POST['cmbtype'],$_POST['cmbpreparedby'],$_POST['cmbreceivedby'],$_POST['txtreference'],$_POST['txtinvoice'],$_POST['txtpax'],$_POST['dtdate']);
			$stmt->execute();
			$stmt->close();			
		}
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unDiscountControl from discountcontrol 
						where `Status`=1 and unArea=? and unBranch=? and unDiscountType=? and DCReference=? and DCDate=? Order by `TimeStamp`")){
			$stmt->bind_param('iiiss',$_SESSION['area'],$_POST['cmbbranch'],$_POST['cmbtype'],$_POST['txtreference'],$_POST['dtdate']);
			$stmt->execute();
			$stmt->bind_result($unDiscountControl);
			$stmt->fetch();
			$stmt->close();			
		}
		
		if(isset($_POST['hdncount'])){
			for($i=1;$i<=$_POST['hdncount'];$i++){

				if(isset($_POST['hdnuncustomer-'.$i])){
					if($_POST['hdnuncustomer-'.$i]==0){
						$unCustomer=addcustomer($i);
					}else{
						$unCustomer=$_POST['hdnuncard-'.$i];
					}
				}
				if(isset($_POST['hdnuncard-'.$i])){
					if($_POST['hdnuncard-'.$i]==0){
						$unCustomerCard=addcustomercard($i,$unCustomer);
					}else{
						$unCustomerCard=$_POST['hdnuncustomer-'.$i];
					}
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("INSERT INTO discountcustomer(unDiscountControl,unCustomer) VALUES (?,?)")){
						$stmt->bind_param('ii',$unDiscountControl,$unCustomer);
						$stmt->execute();
						$stmt->close();			
					}
				}

			}
		}

		$DCTotal=0;
		$DCNetOfVat=0;
		$DCDiscount=0;
		$DCNetPrice=0;
		if(isset($_POST['hdnitemcount'])){
			for($i=1;$i<=$_POST['hdnitemcount'];$i++){
				if(isset($_POST['hdnitemun-'.$i])){
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("INSERT INTO discountdata(unDiscountControl,unProductItem,DDQuantity,DDPrice) VALUES (?,?,?,?)")){
						$stmt->bind_param('iidd',$unDiscountControl,$_POST['hdnitemun-'.$i],$_POST['txtitemquantity-'.$i],$_POST['txtitemprice-'.$i]);
						$stmt->execute();
						$stmt->close();			
					}
					$DCTotal+=$_POST['txtitemquantity-'.$i]*$_POST['txtitemprice-'.$i];
				}
			}
		}
		
		$DCNetOfVat = $DCTotal / 1.12;
		$DCDiscount = $DCNetOfVat * .2;
		$DCNetPrice = $DCNetOfVat - $DCDiscount;
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update discountcontrol set DCTotal=?,DCNetOfVat=?,DCDiscount=?,DCNetPrice=? Where unDiscountControl=?")){
			$stmt->bind_param('ddddi',$DCTotal,$DCNetOfVat,$DCDiscount,$DCNetPrice,$unDiscountControl);
			$stmt->execute();
			$stmt->close();			
		}

		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
	if(isset($_POST['btnupdatediscount'])){
		print_r($_POST);

		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("UPDATE discountcontrol SET unBranch=?,unDiscountType=?,unEmployeePrepared=?,unEmployeeReceived=?,DCReference=?,DCInvoice=?,DCPax=?,DCDate=? WHERE unDiscountControl=?")){
			$stmt->bind_param("iiiissisi",$_POST['cmbbranch'],$_POST['cmbtype'],$_POST['cmbpreparedby'],$_POST['cmbreceivedby'],$_POST['txtreference'],$_POST['txtinvoice'],$_POST['txtpax'],$_POST['dtdate'],$_GET['un']);
			$stmt->execute();
			$stmt->close();
		}
		

		// ----- customer discount data
		// ----- set status of existing entries to false
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("UPDATE discountcustomer SET `Status`=0 WHERE unDiscountControl=?")){
			$stmt->bind_param("i",$_GET['un']);
			$stmt->execute();
			$stmt->close();
		}
		
		// ----- allocate/overwrite/re-use entries who's statuses were set to false
		$i=0;
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unTemplateProductionData From templateproductiondata Where unTemplateProductionControl=? Order by unTemplateProductionData")){
			$stmt->bind_param("i",$_POST['bid']);
			$stmt->execute();
			$stmt->bind_result($unTemplateProductionData);
			while($stmt->fetch()){
				for($j=$i+1;$j<=$_POST['hdncount'];$j++){
					if(isset($_POST['hdnproduct-'.$j])){
						$i=$j;
						break;
					}
				}
				$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt1 = $mysqli1->stmt_init();
				if($stmt1->prepare("UPDATE templateproductiondata SET unProductItem=?,unProductUOM=?,TPDCost=?,TPDQuantity=?,TPDAmount=?,`Status`=1 WHERE unTemplateProductionData=?")){
					$stmt1->bind_param("iddddi",$_POST['hdnproduct-'.$i],$_POST['hdnunit-'.$i],$_POST['txtcost-'.$i],$_POST['txtquantity-'.$i],$_POST['txtamount-'.$i],$unTemplateProductionData);
					$stmt1->execute();
					$stmt1->close();
				}
				if($i==$_POST['hdncount']){break;}
			}
			$stmt->close();
		}
	
		// ----- insert excess entries when needed
		for($j=$i+1;$j<=$_POST['hdncount'];$j++){
			if(isset($_POST['hdnproduct-'.$j])){
				$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("INSERT INTO templateproductiondata(unTemplateProductionControl,unProductItem,unProductUOM,TPDCost,TPDQuantity,TPDAmount) VALUES (?,?,?,?,?,?)")){
					$stmt->bind_param("iiiddd",$_POST['bid'],$_POST['hdnproduct-'.$j],$_POST['hdnunit-'.$j],$_POST['txtcost-'.$j],$_POST['txtquantity-'.$j],$_POST['txtamount-'.$j]);
					$stmt->execute();
					$stmt->close();
				}
			}
		}

		
	}
?>
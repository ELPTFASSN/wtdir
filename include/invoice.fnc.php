<?php
	session_start();
	/* ----- FUNCTIONS ----- */
	function fncCalcInvoice($unInvoiceControl){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select ICPax,ICTotalSales From invoicecontrol Where unInvoiceControl=?")){
			$stmt->bind_param("i",$unInvoiceControl);
			$stmt->execute();
			$stmt->bind_result($ICPax,$ICTotalSales);
			$stmt->fetch();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		
		$Discount=0;
		$NetSales=$ICTotalSales-$Discount;
		
		$VATable=$NetSales; 
		$NetOfVat=$NetSales / 1.12;
		$TaxAmount=$NetSales * 0.12;
		
		$VATExempt=0;
		$VATExSales=0;
		$VATExAmount=0;
		
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("UPDATE invoicecontrol SET ICTotalDiscount=?,ICVatable=?,ICNetOfVaT=?,ICTaxAmount=?,
						ICVatExempt=?,ICVatExSales=?,ICVatExAmount=?,ICNetSales=?
						WHERE unInvoiceControl=?")){
			$stmt->bind_param("ddddddddi",$Discount,$VATable,$NetOfVat,$TaxAmount,$VATExempt,$VATExSales,$VATExAmount,$NetSales,$unInvoiceControl);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
	}
	
	function fncSumPaid($unInvoiceControl){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update invoicecontrol Set ICPaidAmount=ICCash+ICCard+ICGC+ICLOA Where unInvoiceControl=?")){
			$stmt->bind_param("i",$unInvoiceControl);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}		
	}
	
	if(isset($_POST['btnCreateInvoice'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);	
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Insert Into invoicecontrol(unArea,unBranch,ICNumber,ICDate,ICPax) Values (?,?,?,?,?)")){
			$stmt->bind_param("iiisi",$_SESSION['area'],$_POST['cmbBranch'],$_POST['txtinvoice'],$_POST['dtpDate'],$_POST['txtpax']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select Max(unInvoiceControl) From invoicecontrol Where unArea=? and unBranch=? and `Status`=1")){
			$stmt->bind_param("ii",$_SESSION['area'],$_POST['cmbBranch']);
			$stmt->execute();
			$stmt->bind_result($unInvoiceControl);
			$stmt->fetch();
			$stmt->close();
			header('location:../invoice.php?&id='.$unInvoiceControl);
		}else{
			die($stmt->error);
		}
	/*----- Cash -----*/
	}elseif($_POST['btnsavecash']){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update invoicecontrol Set ICCash=? Where unInvoiceControl=?")){
			$stmt->bind_param("di",$_POST['txtpopupcash'],$_POST['hdntxtic']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		fncSumPaid($_POST['hdntxtic']);
		header('location:../invoice.php?&id='.$_POST['hdntxtic']);
		
	/*----- Credit Card -----*/	
	}elseif($_POST['btnsavecreditcard']){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO cardtransaction(unInventoryControl,unInvoiceControl,unCard,unCardTerminal,CTNumber,CTCustomer,CTTrace,CTApproval,CTAmount) Values (0,?,?,?,?,?,?,?,?)")){
			$stmt->bind_param("iiissssd",$_POST['hdntxtic'],$_POST['cmbpopuptype'],$_POST['cmbpopupterminal'],$_POST['txtpopupnumber'],$_POST['txtpopupcustomer'],$_POST['txtpopuptrace'],$_POST['txtpopupapproval'],$_POST['txtpopupamount']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update invoicecontrol set ICCard=(Select Sum(CTAmount) From cardtransaction Where `Status`=1 And unInvoiceControl=?) Where unInvoiceControl=?")){
			$stmt->bind_param("ii",$_POST['hdntxtic'],$_POST['hdntxtic']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		fncSumPaid($_POST['hdntxtic']);
		header('location:../invoice.php?&id='.$_POST['hdntxtic'].'#popupcreditcard');
		
	/*----- Gift Certificate -----*/
	}elseif($_POST['btnsavegc']){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO giftcertificatetransaction(unInventoryControl,unInvoiceControl,unGiftCertificate,GCTReferenceNumber,GCTAmount,GCTForfeit) Values (0,?,?,?,(Select GCAmount From giftcertificate Where unGiftCertificate=?),0)")){
			$stmt->bind_param("iisi",$_POST['hdntxtic'],$_POST['cmbpopupcardtype'],$_POST['txtpopupreference'],$_POST['cmbpopupcardtype']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update invoicecontrol set ICGC=(Select Sum(GCTAmount) From giftcertificatetransaction Where `Status`=1 And unInvoiceControl=?) Where unInvoiceControl=?")){
			$stmt->bind_param("ii",$_POST['hdntxtic'],$_POST['hdntxtic']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		fncSumPaid($_POST['hdntxtic']);
		header('location:../invoice.php?&id='.$_POST['hdntxtic'].'#popupgc');
		
	/*----- Letter Of Authorization -----*/
	}elseif($_POST['btnsaveloa']){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unLetterOfAuthorizationTransaction From letterofauthorizationtransaction Where unInvoiceControl=?")){
			$stmt->bind_param("i",$_POST['hdntxtic']);
			$stmt->execute();
			$stmt->store_result();
			$rowcount=$stmt->num_rows();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		
		if($rowcount<1){
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("INSERT INTO letterofauthorizationtransaction(unInventoryControl,unInvoiceControl,LOATCompanyName,LOATReferenceNumber,LOATAmount,LOATForfeit) Values (0,?,?,?,?,0)")){
				$stmt->bind_param("issd",$_POST['hdntxtic'],$_POST['txtpopuploacompany'],$_POST['txtpopuploareference'],$_POST['txtpopuploaamount']);
				$stmt->execute();
				$stmt->close();
			}else{
				die($stmt->error);
			}			
		}else{
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("UPDATE letterofauthorizationtransaction SET LOATCompanyName=?,LOATReferenceNumber=?,LOATAmount=? Where unInvoiceControl=?")){
				$stmt->bind_param("ssdi",$_POST['txtpopuploacompany'],$_POST['txtpopuploareference'],$_POST['txtpopuploaamount'],$_POST['hdntxtic']);
				$stmt->execute();
				$stmt->close();
			}else{
				die($stmt->error);
			}			
		}
		
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update invoicecontrol set ICLOA=(Select Sum(LOATAmount) From letterofauthorizationtransaction Where `Status`=1 And unInvoiceControl=?) Where unInvoiceControl=?")){
			$stmt->bind_param("ii",$_POST['hdntxtic'],$_POST['hdntxtic']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		fncSumPaid($_POST['hdntxtic']);
		header('location:../invoice.php?&id='.$_POST['hdntxtic']);
		
	/*----- Add Item -----*/
	}elseif($_POST['btnadditem']){
		if(!empty($_POST['txtquantity'])){
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("INSERT INTO invoicedata (unInvoiceControl,unProductItem,IDQuantity,IDUnitPrice,IDTotalAmount) VALUES (?,?,?,?,?)")){
				$stmt->bind_param("iiddd",$_POST['hdntxtic'],$_POST['hdnunproductitem'],$_POST['txtquantity'],$_POST['txtunitprice'],$_POST['txttotal']);
				$stmt->execute();
				$stmt->close();
			}else{
				die($stmt->error);
			}
		}
		
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update invoicecontrol set ICTotalSales=(Select Sum(IDTotalAmount) From invoicedata Where `Status`=1 And unInvoiceControl=?) Where unInvoiceControl=?")){
			$stmt->bind_param("ii",$_POST['hdntxtic'],$_POST['hdntxtic']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		fncCalcInvoice($_POST['hdntxtic']);	
		header('location:../invoice.php?&id='.$_POST['hdntxtic']);
	}
?>
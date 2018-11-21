<?php
	include 'var.inc.php';
	include 'class.inc.php';
	
	session_start();
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	
	if ($_SESSION['Session'] == '') {header("location:../end.php");}
	if ($_SESSION['Session'] != $sessionid) {header("location:../end.php");}
	
if (isset($_POST['btnCreateSale'])){
		$httpreferer=parse_url($_SERVER['HTTP_REFERER'],PHP_URL_PATH);
		if(strpos($httpreferer,'createinvoice')!==false){
			$headerLoc='../createinvoice.php?&bid='.$_POST['cmbBranch'].'#createshift';
		}else{
			$headerLoc=$_SERVER['HTTP_REFERER'];
		}
		if($_POST['scquota']==''){
			$scquota=0.0000;
		}else{
			$scquota=$_POST['scquota'];
		}
		if($_POST['scquotaint']==''){
			$scquotaint=0.0000;
		}
		else{
			$scquotaint=$_POST['scquotaint'];
		}
		if($_POST['scquotap']==''){
			$scquotap=0.0000;
		}
		else{
			$scquotap=$_POST['scquotap'];
		}
		//die($_SESSION['area'].'---'.$_POST['cmbBranch'].'---'.$_POST['cmbEOpen'].'---'.$_POST['SCtimestart'].'---'.$scquota.'---'.$scquotaint.'---'.$scquotap);
		CreateSales($_SESSION['area'],$_POST['cmbBranch'],$_POST['cmbEOpen'],$_POST['SCtimestart'],$_POST['scquota'],$_POST['scquotaint'],$_POST['scquotap']);
		header('location:'.$headerLoc);
}

if (isset($_POST['btnCreateShift'])){
		$httpreferer=parse_url($_SERVER['HTTP_REFERER'],PHP_URL_PATH);
		if(strpos($httpreferer,'createinvoice')!==false){
			$headerLoc='../createinvoice.php?&bid='.$_POST['cmbBranch'].'#selectSCSD';
		}else{
			$headerLoc=$_SERVER['HTTP_REFERER'];
		}
		CreateShift($_SESSION['area'],$_POST['cmbBranch'],$_POST['cmbSalesDay'],$_POST['cmbSEOpen'],$_POST['SDtimestart'],$_POST['sdbalancestart']);
		header('location:'.$headerLoc);
}	

function CreateSales($Area,$Branch,$EmployeeOpen,$TimeStart,$SCQuota,$SCQuotaInterval,$SCQuotaPointAmoun)
{
		$query="INSERT INTO salescontrol (unSalesControl,unArea,unBranch,unEmployeeOpen,SCState,SCTimeStart,SCQuota,SCQuotaInterval,SCQuotaPointAmount) VALUES ('".getMaxPerBranch('unSalesControl','salescontrol',$Branch)."','".$Area."','".$Branch."','".$EmployeeOpen."','Open','".$TimeStart."','".$SCQuota."','".$SCQuotaInterval."','".$SCQuotaPointAmount."')";
		ExecuteNonQuery($query);
		return $Area.$Branch.$EmployeeOpen.$TimeStart;
}

function CreateShift($Area,$Branch,$SalesControl,$Employee,$TimeStart,$StartBal)
{
		$query="INSERT INTO salesdata (unSalesData,unSalesControl,unArea,unBranch,unEmployee,SDState,SDTimeStart,SDBalanceStart) VALUES ('".getMaxPerBranch('unSalesData','salesdata',$Branch)."','".$SalesControl."','".$Area."','".$Branch."','".$Employee."','Open','".$TimeStart."','".$StartBal."')";
		ExecuteNonQuery($query);
		return $Area.$Branch.$SalesControl.$Employee.$TimeStart.$StartBal;
}

function getMaxPerBranch($field,$table,$unBranch){
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("SELECT ifnull(MAX(".$field."),0)+1 FROM ".$table." WHERE unBranch =".$unBranch)){
		$stmt->execute();
		$stmt->bind_result($max);
		$stmt->fetch();
		$stmt->close();
	}
	return $max;
	}

if ($_POST['isExist']==0){
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	$maxICnum = $_POST['InvCtrlun'];
	if($stmt->prepare("INSERT INTO invoicecontrol (unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unEmployee,ICState,ICTimeStamp,ICPax,ICTotalAmount,ICDiscount,ICVatable,ICNetOfVat,ICTaxAmount,ICVatExempt,ICVatExemptSales,ICVatExemptAmount,ICNetSales,ICPaymentCash,ICPaymentOther,ICChange) VALUES (?,?,?,?,?,?,'Paid',CURRENT_TIMESTAMP,?,?,?,?,?,?,?,?,?,?,?,?,?)")){
		$stmt->bind_param('iiiiiiidddddddddddd',$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata'],$maxICnum,$_POST['employee'],$_POST['Pax'],$_POST['totalAmount'],$_POST['TDiscount'],$_POST['VATS'],$_POST['NetVAT'],$_POST['TaxAmount'],$_POST['VATExIndP'],$_POST['VATex'],$_POST['VATExAmount'],$_POST['TDue'],$_POST['TPCash'],$_POST['TPOthers'],$_POST['Change']);
		$stmt->execute();
		$stmt->close();
	}
	for($i=1;$i<=$_POST['hdnitemcount'];$i++){
		$stmt = $mysqli->stmt_init();		
		if($stmt->prepare("INSERT INTO invoicedata (unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unProductItem,IDQuantity,IDPrice,IDTotalAmount,IDState) VALUES (?,?,?,?,?,?,?,?,?,'Paid')")){
		$stmt->bind_param('iiiiiiddd',$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata'],$maxICnum,$_POST['hdnitemid-'.$i],$_POST['txtitemquantity-'.$i],$_POST['txtitemprice-'.$i],$_POST['txtitemtotal-'.$i]);
		$stmt->execute();
		$stmt->close();
		}		
	}
	for($i=1;$i<=$_POST['hdndiscountcount'];$i++){
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO discountcontrol (unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unDiscountType,unEmployeePrepared,unEmployeeReceived,DCReference,DCTotalAmount) VALUES (?,?,?,?,?,?,?,?,?,?)")){
		$stmt->bind_param('iiiiiiiisd',$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata'],$maxICnum,$_POST['hdndiscountid-'.$i],$_POST['employee'],$_POST['employee'],$_POST['hdndiscountreference-'.$i],$_POST['txtdiscountamount-'.$i]);
		$stmt->execute();
		$stmt->close();
		}
	}
	for($i=1;$i<=$_POST['hdnpaymentcount'];$i++){
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO payment (unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unPaymentType,PAmount,PReference) VALUES (?,?,?,?,?,?,?,?)")){
		$stmt->bind_param('iiiiiids',$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata'],$maxICnum,$_POST['hdnpaymentid-'.$i],$_POST['txtpaymentamount-'.$i],$_POST['hdnpaymentreference-'.$i]);
		$stmt->execute();
		$stmt->close();
		}
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
}
else{
	//die('UPDATE ON-HOLD --- INV# :'.$_POST['InvCtrlun']."<br />".$_POST['employee']."<br />".$_POST['Pax']."<br />".$_POST['totalAmount']."<br />".$_POST['TDiscount']."<br />".$_POST['VATS']."<br />".$_POST['NetVAT']."<br />".$_POST['TaxAmount']."<br />".$_POST['VATExIndP']."<br />".$_POST['VATex']."<br />".$_POST['VATExAmount']."<br />".$_POST['TDue']."<br />".$_POST['TPCash']."<br />".$_POST['TPOthers']."<br />".$_POST['Change']."<br />".$_SESSION['area']."<br />".$_POST['branch']."<br />".$_POST['salescontrol']."<br />".$_POST['salesdata']);
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	$maxICnum = $_POST['InvCtrlun'];
	if($stmt->prepare("UPDATE invoicecontrol SET unEmployee=?,ICState='Paid',ICPax=?,ICTotalAmount=?,ICDiscount=?,ICVatable=?,ICNetOfVat=?,ICTaxAmount=?,ICVatExempt=?,ICVatExemptSales=?,ICVatExemptAmount=?,ICNetSales=?,ICPaymentCash=?,ICPaymentOther=?,ICChange=? WHERE unInvoiceControl=? AND unArea=? AND unBranch=? AND unSalesControl=? AND unSalesData=?")){
		$stmt->bind_param('iiddddddddddddiiiii',$_POST['employee'],$_POST['Pax'],$_POST['totalAmount'],$_POST['TDiscount'],$_POST['VATS'],$_POST['NetVAT'],$_POST['TaxAmount'],$_POST['VATExIndP'],$_POST['VATex'],$_POST['VATExAmount'],$_POST['TDue'],$_POST['TPCash'],$_POST['TPOthers'],$_POST['Change'],$_POST['InvCtrlun'],$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata']);
		$stmt->execute();
		$stmt->close();
	}
	if(isset($maxICnum)){
		$stmt = $mysqli->stmt_init();	
		if($stmt->prepare("UPDATE invoicedata SET invoicedata.Status=0 WHERE unInvoiceControl=? AND unArea=? AND unBranch=? AND unSalesControl=? AND unSalesData=?")){
			$stmt->bind_param('iiiii',$maxICnum,$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata']);
			$stmt->execute();
			$stmt->close();
		}
		$stmt = $mysqli->stmt_init();	
		if($stmt->prepare("UPDATE discountcontrol SET discountcontrol.Status=0 WHERE unInvoiceControl=? AND unArea=? AND unBranch=? AND unSalesControl=? AND unSalesData=?")){
			$stmt->bind_param('iiiii',$maxICnum,$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata']);
			$stmt->execute();
			$stmt->close();
		}
		$stmt = $mysqli->stmt_init();	
		if($stmt->prepare("UPDATE payment SET payment.Status=0 WHERE unInvoiceControl=? AND unArea=? AND unBranch=? AND unSalesControl=? AND unSalesData=?")){
			$stmt->bind_param('iiiii',$maxICnum,$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata']);
			$stmt->execute();
			$stmt->close();
		}
	}
	for($i=1;$i<=$_POST['hdnitemcount'];$i++){
		$stmt = $mysqli->stmt_init();		
		if($stmt->prepare("INSERT INTO invoicedata (unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unProductItem,IDQuantity,IDPrice,IDTotalAmount,IDState) VALUES (?,?,?,?,?,?,?,?,?,'Paid')")){
		$stmt->bind_param('iiiiiiddd',$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata'],$maxICnum,$_POST['hdnitemid-'.$i],$_POST['txtitemquantity-'.$i],$_POST['txtitemprice-'.$i],$_POST['txtitemtotal-'.$i]);
		$stmt->execute();
		$stmt->close();
		}		
	}
	for($i=1;$i<=$_POST['hdndiscountcount'];$i++){
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO discountcontrol (unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unDiscountType,unEmployeePrepared,unEmployeeReceived,DCReference,DCTotalAmount) VALUES (?,?,?,?,?,?,?,?,?,?)")){
		$stmt->bind_param('iiiiiiiisd',$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata'],$maxICnum,$_POST['hdndiscountid-'.$i],$_POST['employee'],$_POST['employee'],$_POST['hdndiscountreference-'.$i],$_POST['txtdiscountamount-'.$i]);
		$stmt->execute();
		$stmt->close();
		}
	}
	for($i=1;$i<=$_POST['hdnpaymentcount'];$i++){
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO payment (unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unPaymentType,PAmount,PReference) VALUES (?,?,?,?,?,?,?,?)")){
		$stmt->bind_param('iiiiiids',$_SESSION['area'],$_POST['branch'],$_POST['salescontrol'],$_POST['salesdata'],$maxICnum,$_POST['hdnpaymentid-'.$i],$_POST['txtpaymentamount-'.$i],$_POST['hdnpaymentreference-'.$i]);
		$stmt->execute();
		$stmt->close();
		}
	}
	header('location:'.$_SERVER['HTTP_REFERER'].'#INVEditSaved');
}

if (isset($_GET['closeshift'])){
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	$SDPax = 0;
	$SDTotalAmount = 0;
	$SDDiscount = 0;
	$SDVatable = 0;
	$SDNetOfVat = 0;
	$SDTaxAmount = 0;
	$SDVatExempt = 0;
	$SDVatExemptSales = 0;
	$SDVatExemptAmount = 0;
	$SDNetSales = 0;
	$SDPaymentCash = 0;
	$SDPaymentOther = 0;
	$SDDiscrepancy = 0;
	$SDCashCount = 0;
	if($stmt->prepare("SELECT ICPax,ICTotalAmount,ICDiscount,ICVatable,ICNetOfVat,ICTaxAmount,ICVatExempt,ICVatExemptSales,ICVatExemptAmount,ICNetSales,ICPaymentCash,ICPaymentOther 
	FROM invoicecontrol WHERE unBranch = ? AND unSalesControl = ? AND unSalesData = ? AND invoicecontrol.Status=1 ")){
		$stmt->bind_param('iii',$_GET['bid'],$_GET['unsc'],$_GET['idsd']);
		$stmt->execute();
		$stmt->bind_result($ICPax,$ICTotalAmount,$ICDiscount,$ICVatable,$ICNetOfVat,$ICTaxAmount,$ICVatExempt,$ICVatExemptSales,$ICVatExemptAmount,$ICNetSales,$ICPaymentCash,$ICPaymentOther);
		while($stmt->fetch()){
			$SDPax = $SDPax+$ICPax;
			$SDTotalAmount = $SDTotalAmount+$ICTotalAmount;
			$SDDiscount = $SDDiscount+$ICDiscount;
			$SDVatable = $SDVatable+$ICVatable;
			$SDNetOfVat = $SDNetOfVat+$ICNetOfVat;
			$SDTaxAmount = $SDTaxAmount+$ICTaxAmount;
			$SDVatExempt = $SDVatExempt+$ICVatExempt;
			$SDVatExemptSales = $SDVatExemptSales+$ICVatExemptSales;
			$SDVatExemptAmount = $SDVatExemptAmount+$ICVatExemptAmount;
			$SDNetSales = $SDNetSales+$ICNetSales; 
			$SDPaymentCash = $SDPaymentCash+$ICPaymentCash;
			$SDPaymentOther = $SDPaymentOther+$ICPaymentOther;
		}
	}
	//die ($ICNetSales);
	$stmt->close();
	$stmt1 = $mysqli->stmt_init();
	if($stmt1->prepare("SELECT SDBalanceStart FROM salesdata WHERE  unBranch = ? AND unSalesControl = ? AND unSalesData = ? ")){
		$stmt1->bind_param('iii',$_GET['bid'],$_GET['unsc'],$_GET['idsd']);
		$stmt1->execute();
		$stmt1->bind_result($SDBalanceStart);
		$stmt1->fetch();
		$SDCashCount = $_GET['bal']-$SDBalanceStart;
		$SDDiscrepancy = $SDCashCount-$SDPaymentCash;
	}
	$stmt1->close();
	$stmt2 = $mysqli->stmt_init();
	if($stmt2->prepare("UPDATE salesdata SET SDDiscrepancy = '".$SDDiscrepancy."', SDPax = '".$SDPax."', SDTotalAmount = '".$SDTotalAmount."', SDDiscount = '".$SDDiscount."', SDVatable = '".$SDVatable."', SDNetOfVat = '".$SDNetOfVat."', SDTaxAmount = '".$SDTaxAmount."', SDVatExempt = '".$SDVatExempt."', SDVatExemptSales = '".$SDVatExemptSales."', SDVatExemptAmount = '".$SDVatExemptAmount."', SDNetSales = '".$SDNetSales."', SDPaymentCash = '".$SDPaymentCash."', SDPaymentOther = '".$SDPaymentOther."', SDCashCount = '".$SDCashCount."', SDBalanceEnd = '".$_GET['bal']."', SDTimeEnd = '".$_GET['closeshift']."', SDState = 'Close' WHERE unSalesData=".$_GET['idsd']." AND unBranch=".$_GET['bid'])){
		$stmt2->execute();
	}
	$stmt2->close();
	header('location:../createinvoice.php?&bid='.$_GET['bid'].'&unsc='.$_GET['unsc'].'#selectSCSD');
}

if(isset($_GET['closeday'])){
	$maxSC=getMaxPerBranch('unSalesControl','salescontrol',$_GET['bid']);
	//die($maxSC);
	$stmt = $mysqli->stmt_init();
	$stmt0 = $mysqli->stmt_init();
	$SCPax = 0;
	$SCTotalAmount = 0;
	$SCDiscount = 0;
	$SCVatable = 0;
	$SCNetOfVat = 0;
	$SCTaxAmount = 0;
	$SCVatExempt = 0;
	$SCVatExemptSales = 0;
	$SCVatExemptAmount = 0;
	$SCNetSales = 0;
	$SCPaymentCash = 0;
	$SCPaymentOther = 0;
	$SCItemCount = 0;
	$SCItemPaid = 0;
	$SCItemVoided = 0;
	$SCItemCancelled = 0;
	$SCItemRefunded = 0;
	$SCTransactionCount = 0;
	$SCTransactionPaid = 0;
	$SCReadingCurrent = 0;
	$SCQuotaPoint = 0;
	$SCQuotaTotalAmount = 0;
	if($stmt0->prepare("SELECT MIN(unInvoiceControl) AS InvoiceStart, MAX(unInvoiceControl) AS InvoiceEnd  
	FROM invoicecontrol WHERE unSalesControl = ? AND unBranch=? AND invoicecontrol.Status=1")){
		$stmt0->bind_param('ii',$_GET['idsc'],$_GET['bid']);
		$stmt0->execute();
		$stmt0->bind_result($SCInvoiceStart,$SCInvoiceEnd);
		$stmt0->fetch();
	}
	$stmt0->close();
	if($stmt->prepare("SELECT ICPax,ICTotalAmount,ICDiscount,ICVatable,ICNetOfVat,ICTaxAmount,ICVatExempt,ICVatExemptSales,ICVatExemptAmount,ICNetSales,ICPaymentCash,ICPaymentOther 
	FROM invoicecontrol WHERE unSalesControl = ?  AND unBranch=? AND invoicecontrol.Status=1")){
		$stmt->bind_param('ii',$_GET['idsc'],$_GET['bid']);
		$stmt->execute();
		$stmt->bind_result($ICPax,$ICTotalAmount,$ICDiscount,$ICVatable,$ICNetOfVat,$ICTaxAmount,$ICVatExempt,$ICVatExemptSales,$ICVatExemptAmount,$ICNetSales,$ICPaymentCash,$ICPaymentOther);
		while($stmt->fetch()){
			$SCPax = $SCPax+$ICPax;
			$SCTotalAmount = $SCTotalAmount+$ICTotalAmount;
			$SCDiscount = $SCDiscount+$ICDiscount;
			$SCVatable = $SCVatable+$ICVatable;
			$SCNetOfVat = $SCNetOfVat+$ICNetOfVat;
			$SCTaxAmount = $SCTaxAmount+$ICTaxAmount;
			$SCVatExempt = $SCVatExempt+$ICVatExempt;
			$SCVatExemptSales = $SCVatExemptSales+$ICVatExemptSales;
			$SCVatExemptAmount = $SCVatExemptAmount+$ICVatExemptAmount;
			$SCNetSales = $SCNetSales+$ICNetSales;
			$SCPaymentCash = $SCPaymentCash+$ICPaymentCash;
			$SCPaymentOther = $SCPaymentOther+$ICPaymentOther;
		}
	}
	$stmt->close();
	$stmt1 = $mysqli->stmt_init();
	if($stmt1->prepare("SELECT IDQuantity FROM invoicedata WHERE  unBranch = ? AND unSalesControl = ? AND invoicedata.Status=1 ")){
		$stmt1->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt1->execute();
		/*$stmt1->bind_result($IDQuantity);
		while($stmt1->fetch()){
			$SCItemCount = $SCItemCount+$IDQuantity;
		}*/
		$stmt1->store_result();
		if($stmt1->num_rows()>0){
			$stmt1->bind_result($IDQuantity);
			while($stmt1->fetch()){
			$SCItemCount = $SCItemCount+$IDQuantity;
		}
		}else{$SCItemCount='0';}
	}
	$stmt1->close();
	$stmt2 = $mysqli->stmt_init();
	if($stmt2->prepare("SELECT IDQuantity FROM invoicedata WHERE IDState = 'Paid' AND unBranch = ? AND unSalesControl = ?  AND invoicedata.Status=1")){
		$stmt2->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt2->execute();
		/*$stmt2->bind_result($IDPaid);
		while($stmt2->fetch()){
			$SCItemPaid = $SCItemPaid+$IDPaid;
		}*/
		$stmt2->store_result();
		if($stmt2->num_rows()>0){
			$stmt2->bind_result($IDPaid);
			while($stmt2->fetch()){
			$SCItemPaid = $SCItemPaid+$IDPaid;
		}
		}else{$SCItemPaid='0';}
	}
	$stmt2->close();
	$stmt3 = $mysqli->stmt_init();
	if($stmt3->prepare("SELECT IDQuantity FROM invoicedata WHERE IDState = 'Voided' AND unBranch = ? AND unSalesControl = ?  AND invoicedata.Status=1 ")){
		$stmt3->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt3->execute();
		$stmt3->store_result();
		if($stmt3->num_rows()>0){
			$stmt3->bind_result($IDVoided);
			while($stmt3->fetch()){
				$SCItemVoided = $SCItemVoided+$IDVoided;
			}
		}else{$SCItemVoided='0';}
	}
	$stmt3->close();
	$stmt4 = $mysqli->stmt_init();
	if($stmt4->prepare("SELECT IDQuantity FROM invoicedata WHERE IDState = 'Cancelled' AND unBranch = ? AND unSalesControl = ?  AND invoicedata.Status=1 ")){
		$stmt4->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt4->execute();
		$stmt4->bind_result($IDCancelled);
		while($stmt4->fetch()){
			$SCItemCancelled = $SCItemCancelled+$IDCancelled;
		}
	}
	$stmt4->close();
	$stmt5 = $mysqli->stmt_init();
	if($stmt5->prepare("SELECT IDQuantity FROM invoicedata WHERE IDState = 'Refunded' AND unBranch = ? AND unSalesControl = ? AND invoicedata.Status=1 ")){
		$stmt5->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt5->execute();
		$stmt5->bind_result($IDRefunded);
		while($stmt5->fetch()){
			$SCItemRefunded = $SCItemRefunded+$IDRefunded;
		}
	}
	$stmt5->close();
	$stmt6 = $mysqli->stmt_init();
	if($stmt6->prepare("SELECT unInvoiceControl FROM invoicecontrol WHERE unBranch=? AND unSalesControl = ? AND invoicecontrol.Status=1 ")){
		$stmt6->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt6->execute();
		$stmt6->store_result();
		$SCTransactionCount = $stmt6->num_rows();
	}
	$stmt6->close();
	$stmt7 = $mysqli->stmt_init();
	if($stmt7->prepare("SELECT unInvoiceControl FROM invoicecontrol WHERE unBranch=? AND unSalesControl = ? AND ICState = 'Paid' AND invoicecontrol.Status=1 ")){
		$stmt7->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt7->execute();
		$stmt7->store_result();
		$SCTransactionPaid = $stmt7->num_rows();
	}
	$stmt7->close();
	$stmt8 = $mysqli->stmt_init();
	$currentSC = $_GET['idsc']-1;
	if($stmt8->prepare("SELECT SCReadingCurrent FROM salescontrol WHERE  unBranch = ? AND unSalesControl = ? ")){
		$stmt8->bind_param('ii',$_GET['bid'],$currentSC);
		$stmt8->execute();
		$stmt8->bind_result($SCReadingPrevious);
		$stmt8->fetch();
		$SCReadingCurrent=$SCReadingPrevious+$SCNetSales;
	}
	$stmt8->close();
	$stmt9 = $mysqli->stmt_init();
	if($stmt9->prepare("SELECT SCQuota, SCQuotaInterval, SCQuotaPointAmount FROM salescontrol WHERE  unBranch = ? AND unSalesControl = ? ")){
		$stmt9->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt9->execute();
		$stmt9->bind_result($SCQuota,$SCQuotaInterval,$SCQuotaPointAmount);
		$stmt9->fetch();
		$SCNetSalesSCQuota = $SCNetSales-$SCQuota;
		$SCNetSalesSCQuotaInt = $SCNetSalesSCQuota+$SCQuotaInterval;
		$SCQuotaPoint = $SCNetSalesSCQuotaInt/$SCQuotaInterval;
		$SCQuotaTotalAmount = $SCQuotaPoint*$SCQuotaPointAmount;
	}
	$stmt9->close();
	//die("UPDATE salescontrol SET unEmployeeClose = '".$_GET['emp']."',SCTimeEnd = '".$_GET['closeday']."', SCState = 'Close', SCPax = '".$SCPax."',SCTotalAmount  = '".$SCTotalAmount."',SCDiscount  = '".$SCDiscount."',SCVatable  = '".$SCVatable."',SCNetOfVat  = '".$SCNetOfVat."',SCTaxAmount  = '".$SCTaxAmount."',SCVatExempt  = '".$SCVatExempt."',SCVatExemptSales  = '".$SCVatExemptSales."',SCVatExemptAmount  = '".$SCVatExemptAmount."',SCNetSales  = '".$SCNetSales."',SCPaymentCash  = '".$SCPaymentCash."',SCPaymentOther  = '".$SCPaymentOther."',SCInvoiceStart  = '".$SCInvoiceStart."',SCInvoiceEnd  = '".$SCInvoiceEnd."',SCItemCount  = '".$SCItemCount."',SCItemPaid  = '".$SCItemPaid."',SCItemVoided  = '".$SCItemVoided."',SCItemCancelled  = '".$SCItemCancelled."',SCItemRefunded  = '".$SCItemRefunded."',SCTransactionCount  = '".$SCTransactionCount."',SCTransactionPaid  = '".$SCTransactionPaid."',SCReadingPrevious  = '".$SCReadingPrevious."',SCReadingCurrent  = '".$SCReadingCurrent."',SCQuotaPoint  = '".$SCQuotaPoint."',SCQuotaTotalAmount  = '".$SCQuotaTotalAmount."' WHERE unSalesControl=".$_GET['idsc']." AND unBranch=".$_GET['bid']);
	$stmt10 = $mysqli->stmt_init();
	if($stmt10->prepare("UPDATE salescontrol SET unEmployeeClose = '".$_GET['emp']."',SCTimeEnd = '".$_GET['closeday']."', SCState = 'Close', SCPax = '".$SCPax."',SCTotalAmount  = '".$SCTotalAmount."',SCDiscount  = '".$SCDiscount."',SCVatable  = '".$SCVatable."',SCNetOfVat  = '".$SCNetOfVat."',SCTaxAmount  = '".$SCTaxAmount."',SCVatExempt  = '".$SCVatExempt."',SCVatExemptSales  = '".$SCVatExemptSales."',SCVatExemptAmount  = '".$SCVatExemptAmount."',SCNetSales  = '".$SCNetSales."',SCPaymentCash  = '".$SCPaymentCash."',SCPaymentOther  = '".$SCPaymentOther."',SCInvoiceStart  = '".$SCInvoiceStart."',SCInvoiceEnd  = '".$SCInvoiceEnd."',SCItemCount  = '".$SCItemCount."',SCItemPaid  = '".$SCItemPaid."',SCItemVoided  = '".$SCItemVoided."',SCItemCancelled  = '".$SCItemCancelled."',SCItemRefunded  = '".$SCItemRefunded."',SCTransactionCount  = '".$SCTransactionCount."',SCTransactionPaid  = '".$SCTransactionPaid."',SCReadingPrevious  = '".$SCReadingPrevious."',SCReadingCurrent  = '".$SCReadingCurrent."',SCQuotaPoint  = '".$SCQuotaPoint."',SCQuotaTotalAmount  = '".$SCQuotaTotalAmount."' WHERE unBranch=? AND unSalesControl=?") ){
		$stmt10->bind_param('ii',$_GET['bid'],$_GET['idsc']);
		$stmt10->execute();
	}
	$stmt10->close();
	settype($maxSC, "integer");
	$currSC=$_GET['idsc'];
	settype($currSC,"integer");
	$actMaxSC=($maxSC-1);
	settype($actMaxSC,"integer");
	if($currSC<$actMaxSC){
		for($i=$currSC+1;$i<=$actMaxSC;$i++){
			$currentSC = $i-1;
			$SCUpReadingCurrent=0;
			$stmt11 = $mysqli->stmt_init();
			if($stmt11->prepare("SELECT SCNetSales FROM salescontrol WHERE  unBranch = ? AND unSalesControl = ? ")){
				$stmt11->bind_param('ii',$_GET['bid'],$i);
				$stmt11->execute();
				$stmt11->bind_result($SCUpNetSales);
				$stmt11->fetch();
			}
			$stmt11->close();
			$stmt12 = $mysqli->stmt_init();
			if($stmt12->prepare("SELECT SCReadingCurrent FROM salescontrol WHERE  unBranch = ? AND unSalesControl = ? ")){
				$stmt12->bind_param('ii',$_GET['bid'],$currentSC);
				$stmt12->execute();
				$stmt12->bind_result($SCUpReadingPrevious);
				$stmt12->fetch();
				$SCUpReadingCurrent=$SCUpReadingPrevious+$SCUpNetSales;
			}
			$stmt12->close();
			$stmt13 = $mysqli->stmt_init();
			if($stmt13->prepare("UPDATE salescontrol SET SCReadingPrevious = ?, SCReadingCurrent = ? WHERE  unBranch = ? AND unSalesControl = ? ")){
				$stmt13->bind_param('ddii',$SCUpReadingPrevious,$SCUpReadingCurrent,$_GET['bid'],$i);
				$stmt13->execute();
			}
			$stmt13->close();
		}
		header('location:../createinvoice.php?&bid='.$_GET['bid'].'#selectSCSD');
	}else{
		header('location:../createinvoice.php?&bid='.$_GET['bid'].'#selectSCSD');
	}
}

?>
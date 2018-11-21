<?php
	include 'include/config.inc.php';
	if(isset($_POST['getunSalesControl'])){
		$mysql = new mysqli($server,$username,$password,$database);
		$stmt = $mysql->stmt_init();
		if($stmt->prepare("SELECT ifnull(Max(unSalesControl),0) as unSalesControl From salescontrol Inner Join device on device.unBranch=salescontrol.unBranch Where DSerialNumber=? And DMacAddress=?")){
			$stmt->bind_param("ss",$_POST['Serial'],$_POST['Mac']);
			$stmt->execute();
			$stmt->bind_result($unSalesControl);
			$stmt->store_result();
			if($stmt->num_rows>0){
				$stmt->fetch();
				$response['Success']=1;
				$response['unSalesControl']=$unSalesControl;
			}else{
				$response['Success']=0;
				$response['Message']='Unrecognized Device';
				die(json_encode($response));
			}
		}
		echo json_encode($response);
		$stmt->close();
	}

	if(isset($_POST['setImport'])){
		$contents = $_POST['setImport'];
		$data = json_decode($contents,true);
		$unBranch = (int)$data['salescontrol'][0]['unBranch'];

		$iSalesControlMax=count($data['salescontrol']);
		$iSalesDataMax=count($data['salesdata']);
		$iInvoiceControlMax=count($data['invoicecontrol']);
		$iInvoiceDataMax=count($data['invoicedata']);
		$iDiscountControlMax=count($data['discountcontrol']);
		$iPaymentMax=count($data['payment']);
		$iDenominationCountMax = count($data['denominationcount']);

		$mysqli = new MySQLi($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		$query = "Select ifNull(Max(unSalesControl),0) as iDBunSalesControl From salescontrol where unBranch=?";
		if($stmt->prepare($query)){
			$stmt->bind_param("i",$unBranch);
			$stmt->execute();
			$stmt->bind_result($iDBunSalesControl);
			$stmt->fetch();
		}else{
			die($stmt->error);
		}

		/* ----- SalesControl ----- */
		$query = "INSERT INTO salescontrol(unArea,unBranch,unSalesControl,unEmployeeOpen,unEmployeeClose,SCState,SCTimeStart,SCTimeEnd,SCPax,SCTotalAmount,SCDiscount,SCVatable,SCNetOfVat,SCTaxAmount,SCVatExempt,SCVatExemptSales,SCVatExemptAmount,SCNetSales,SCPaymentCash,SCPaymentOther,SCInvoiceStart,SCInvoiceEnd,SCItemCount,SCItemPaid,SCItemVoided,SCItemCancelled,SCItemRefunded,SCTransactionCount,SCTransactionPaid,SCTransactionVoided,SCTransactionCancelled,SCTransactionRefunded,SCReadingPrevious,SCReadingCurrent,SCQuota,SCQuotaInterval,SCQuotaPointAmount,SCQuotaPoint,SCQuotaTotalAmount) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,(Select BQuota From branch where idBranch=?),(Select BQuotaInterval From branch where idBranch=?),(Select BQuotaPointAmount From branch where idBranch=?),0,0)";
		if($stmt->prepare($query)){
			for ($i=0;$i<$iSalesControlMax;$i++){
				if ($data['salescontrol'][$i]['unSalesControl']>$iDBunSalesControl && ExecuteReader('Select ifNull(Sum(unSalesControl),0) as `result` From salescontrol where unBranch='.$unBranch.' and unSalesControl ='.$data['salescontrol'][$i]['unSalesControl'])==0){
					$stmt->bind_param("iiiiisssidddddddddddiiiiiiiiiiiiddiii",$data['salescontrol'][$i]['unArea'],$data['salescontrol'][$i]['unBranch'],$data['salescontrol'][$i]['unSalesControl'],$data['salescontrol'][$i]['unEmployeeOpen'],$data['salescontrol'][$i]['unEmployeeClose'],$data['salescontrol'][$i]['SCState'],date('Y-m-d H:i:s',strtotime($data['salescontrol'][$i]['SCTimeStart'])),date('Y-m-d H:i:s',strtotime($data['salescontrol'][$i]['SCTimeEnd'])),$data['salescontrol'][$i]['SCPax'],$data['salescontrol'][$i]['SCTotalAmount'],$data['salescontrol'][$i]['SCDiscount'],$data['salescontrol'][$i]['SCVatable'],$data['salescontrol'][$i]['SCNetOfVat'],$data['salescontrol'][$i]['SCTaxAmount'],$data['salescontrol'][$i]['SCVatExempt'],$data['salescontrol'][$i]['SCVatExemptSales'],$data['salescontrol'][$i]['SCVatExemptAmount'],$data['salescontrol'][$i]['SCNetSales'],$data['salescontrol'][$i]['SCPaymentCash'],$data['salescontrol'][$i]['SCPaymentOther'],
										$data['salescontrol'][$i]['SCInvoiceStart'],$data['salescontrol'][$i]['SCInvoiceEnd'],$data['salescontrol'][$i]['SCItemCount'],$data['salescontrol'][$i]['SCItemPaid'],$data['salescontrol'][$i]['SCItemVoided'],$data['salescontrol'][$i]['SCItemCancelled'],$data['salescontrol'][$i]['SCItemRefunded'],$data['salescontrol'][$i]['SCTransactionCount'],$data['salescontrol'][$i]['SCTransactionPaid'],$data['salescontrol'][$i]['SCTransactionVoided'],$data['salescontrol'][$i]['SCTransactionCancelled'],$data['salescontrol'][$i]['SCTransactionRefunded'],$data['salescontrol'][$i]['SCReadingPrevious'],$data['salescontrol'][$i]['SCReadingCurrent'],$unBranch,$unBranch,$unBranch);
					$stmt->execute();
				}
			}
		}
		
		/* ----- SalesData ----- */
		$query="INSERT INTO salesdata(unArea,unBranch,unSalesControl,unSalesData,unEmployee,SDTimeStart,SDTimeEnd,SDState,SDBalanceStart,SDBalanceEnd,SDDiscrepancy,SDPax,SDTotalAmount,SDDiscount,SDVatable,SDNetOfVat,SDTaxAmount,SDVatExempt,SDVatExemptSales,SDVatExemptAmount,SDNetSales,SDPaymentCash,SDPaymentOther,SDCashCount) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		if($stmt->prepare($query)){
			for($i=0;$i<$iSalesDataMax;$i++){
				if($data['salesdata'][$i]['unSalesControl']>$iDBunSalesControl && ExecuteReader('Select ifNull(Sum(unSalesData),0) as `result` From salesdata where unBranch='.$unBranch.' and unSalesData ='.$data['salesdata'][$i]['unSalesData'])==0){
					$stmt->bind_param("iiiiisssdddddddddddddddd",$data['salesdata'][$i]['unArea'],$data['salesdata'][$i]['unBranch'],$data['salesdata'][$i]['unSalesControl'],$data['salesdata'][$i]['unSalesData'],$data['salesdata'][$i]['unEmployee'],date('Y-m-d H:i:s',strtotime($data['salesdata'][$i]['SDTimeStart'])),date('Y-m-d H:i:s',strtotime($data['salesdata'][$i]['SDTimeEnd'])),$data['salesdata'][$i]['SDState'],$data['salesdata'][$i]['SDBalanceStart'],$data['salesdata'][$i]['SDBalanceEnd'],$data['salesdata'][$i]['SDDiscrepancy'],$data['salesdata'][$i]['SDPax'],$data['salesdata'][$i]['SDTotalAmount'],$data['salesdata'][$i]['SDDiscount'],$data['salesdata'][$i]['SDVatable'],$data['salesdata'][$i]['SDNetOfVat'],$data['salesdata'][$i]['SDTaxAmount'],$data['salesdata'][$i]['SDVatExempt'],$data['salesdata'][$i]['SDVatExemptSales'],$data['salesdata'][$i]['SDVatExemptAmount'],$data['salesdata'][$i]['SDNetSales'],$data['salesdata'][$i]['SDPaymentCash'],
										$data['salesdata'][$i]['SDPaymentOther'],$data['salesdata'][$i]['SDCashCount']);
					$stmt->execute();
				}
			}
		}
		
		/* ----- InvoiceControl ----- */
		$query="INSERT INTO invoicecontrol(unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unEmployee,ICState,ICCustomer,ICAddress,ICTimeStamp,ICPax,ICTotalAmount,ICDiscount,ICVatable,ICNetOfVat,ICTaxAmount,ICVatExempt,ICVatExemptSales,ICVatExemptAmount,ICNetSales,ICPaymentCash,ICPaymentOther,ICChange,ICRefundEmployee,ICRefundReason,ICRefundTimeStamp) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		if($stmt->prepare($query)){
			for($i=0;$i<$iInvoiceControlMax;$i++){
				if($data['invoicecontrol'][$i]['unSalesControl']>$iDBunSalesControl && ExecuteReader('Select ifNull(Sum(unInvoiceControl),0) as `result` From invoicecontrol where unBranch='.$unBranch.' and unInvoiceControl ='.$data['invoicecontrol'][$i]['unInvoiceControl'])==0){
					$stmt->bind_param("iiiiiissssiddddddddddddiss",$data['invoicecontrol'][$i]['unArea'],$data['invoicecontrol'][$i]['unBranch'],$data['invoicecontrol'][$i]['unSalesControl'],$data['invoicecontrol'][$i]['unSalesData'],$data['invoicecontrol'][$i]['unInvoiceControl'],$data['invoicecontrol'][$i]['unEmployee'],$data['invoicecontrol'][$i]['ICState'],$data['invoicecontrol'][$i]['ICCustomer'],$data['invoicecontrol'][$i]['ICAddress'],date('Y-m-d H:i:s',strtotime($data['invoicecontrol'][$i]['ICTimeStamp'])),$data['invoicecontrol'][$i]['ICPax'],$data['invoicecontrol'][$i]['ICTotalAmount'],$data['invoicecontrol'][$i]['ICDiscount'],$data['invoicecontrol'][$i]['ICVatable'],$data['invoicecontrol'][$i]['ICNetOfVat'],$data['invoicecontrol'][$i]['ICTaxAmount'],$data['invoicecontrol'][$i]['ICVatExempt'],$data['invoicecontrol'][$i]['ICVatExemptSales'],$data['invoicecontrol'][$i]['ICVatExemptAmount'],$data['invoicecontrol'][$i]['ICNetSales'],$data['invoicecontrol'][$i]['ICPaymentCash'],
											$data['invoicecontrol'][$i]['ICPaymentOther'],$data['invoicecontrol'][$i]['ICChange'],$data['invoicecontrol'][$i]['ICRefundEmployee'],$data['invoicecontrol'][$i]['ICRefundReason'],date('Y-m-d H:i:s',strtotime($data['invoicecontrol'][$i]['ICRefundTimeStamp'])));
					$stmt->execute();
				}
			}
		}
		
		/* ----- InvoiceData ----- */		
		$query="INSERT INTO invoicedata(unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unProductItem,IDQuantity,IDPrice,IDTotalAmount,IDState) VALUES (?,?,?,?,?,?,?,?,?,?)";
		if($stmt->prepare($query)){
			for($i=0;$i<$iInvoiceDataMax;$i++){
				if($data['invoicedata'][$i]['unSalesControl']>$iDBunSalesControl && ExecuteReader('Select ifNull(Sum(unInvoiceData),0) as `result` From invoicedata where unBranch='.$unBranch.' and unInvoiceData ='.$data['invoicedata'][$i]['unInvoiceData'])==0){
					$stmt->bind_param("iiiiiiddds",$data['invoicedata'][$i]['unArea'],$data['invoicedata'][$i]['unBranch'],$data['invoicedata'][$i]['unSalesControl'],$data['invoicedata'][$i]['unSalesData'],$data['invoicedata'][$i]['unInvoiceControl'],$data['invoicedata'][$i]['unProductItem'],$data['invoicedata'][$i]['IDQuantity'],$data['invoicedata'][$i]['IDPrice'],$data['invoicedata'][$i]['IDTotalAmount'],$data['invoicedata'][$i]['IDState']);
					$stmt->execute();
				}
			}
		}
		
		/* ----- DiscountControl ----- */		
		$query="INSERT INTO discountcontrol(unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unDiscountType,unEmployeePrepared,DCReference,DCTotalAmount) VALUES (?,?,?,?,?,?,?,?,?)";
		if($stmt->prepare($query)){
			for($i=0;$i<$iDiscountControlMax;$i++){
				if($data['discountcontrol'][$i]['unSalesControl']>$iDBunSalesControl){
					$stmt->bind_param("iiiiiiisd",$data['discountcontrol'][$i]['unArea'],$data['discountcontrol'][$i]['unBranch'],$data['discountcontrol'][$i]['unSalesControl'],$data['discountcontrol'][$i]['unSalesData'],$data['discountcontrol'][$i]['unInvoiceControl'],$data['discountcontrol'][$i]['unDiscountType'],$data['discountcontrol'][$i]['unEmployeePrepared'],$data['discountcontrol'][$i]['DCReference'],$data['discountcontrol'][$i]['DCTotalAmount']);
					$stmt->execute();
				}
			}
		}
		
		/* ----- PaymentControl ----- */		
		$query="INSERT INTO payment(unArea,unBranch,unSalesControl,unSalesData,unInvoiceControl,unPaymentType,PAmount,PReference,PRemark) VALUES (?,?,?,?,?,?,?,?,?)";
		if($stmt->prepare($query)){
			for($i=0;$i<$iPaymentMax;$i++){
				if($data['payment'][$i]['unSalesControl']>$iDBunSalesControl){
					$stmt->bind_param("iiiiiidss",$data['payment'][$i]['unArea'],$data['payment'][$i]['unBranch'],$data['payment'][$i]['unSalesControl'],$data['payment'][$i]['unSalesData'],$data['payment'][$i]['unInvoiceControl'],$data['payment'][$i]['unPaymentType'],$data['payment'][$i]['PAmount'],$data['payment'][$i]['PReference'],$data['payment'][$i]['PRemark']);
					$stmt->execute();
				}
			}
		}
		
		/* ----- DenominationCount ----- */		
		$query="INSERT INTO denominationcount(unArea,unBranch,unSalesControl,unSalesData,unDenomination,DCStart,DCEnd) VALUES (?,?,?,?,?,?,?)";
		if($stmt->prepare($query)){
			for($i=0;$i<$iDenominationCountMax;$i++){
				if($data['denominationcount'][$i]['unSalesControl']>$iDBunSalesControl){
					$stmt->bind_param("iiiiidd",$data['denominationcount'][$i]['unArea'],$data['denominationcount'][$i]['unBranch'],$data['denominationcount'][$i]['unSalesControl'],$data['denominationcount'][$i]['unSalesData'],$data['denominationcount'][$i]['unDenomination'],$data['denominationcount'][$i]['DCStart'],$data['denominationcount'][$i]['DCEnd']);
					$stmt->execute();
				}
			}
		}

	}
?>
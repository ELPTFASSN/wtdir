<?php 
	include 'header.php';
	if(isset($_POST['hdndata'])){
		$contents = $_POST['hdndata'];
	}else{
		$contents = file_get_contents($_FILES['flimport']['tmp_name']);
	}
	$data = json_decode($contents,true);
	$_SESSION['import_unBranch'] = (int)$data['salescontrol'][0]['unBranch'];
	
	$iSalesControlMax=count($data['salescontrol']);
	$iSalesDataMax=count($data['salesdata']);
	$iInvoiceControlMax=count($data['invoicecontrol']);
	$iInvoiceDataMax=count($data['invoicedata']);
	$iDiscountControlMax=count($data['discountcontrol']);
	$iPaymentMax=count($data['payment']);
	$iDenominationCountMax = count($data['denominationcount']);
	
	$rowcount=$iSalesControlMax + $iSalesDataMax + $iInvoiceControlMax + $iInvoiceDataMax + $iDiscountControlMax + $iPaymentMax + $iDenominationCountMax;
	
	if(isset($_GET['setbranch'])){
		$_SESSION['import_unBranch']=$_GET['ibid'];
	}
	
	if(empty($_SESSION['import_unBranch'])){
		echo "<script>location.href='#popupbranch'</script>";
	}
	
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	$query = "Select ifNull(Max(SCTimeStart),'1970-01-01') as sDBTimeStart, ifNull(Max(unSalesControl),0) as iDBunSalesControl From salescontrol where unBranch=?";
	if($stmt->prepare($query)){
		$stmt->bind_param("i",$_SESSION['import_unBranch']);
		$stmt->execute();
		$stmt->bind_result($sDBTimeStart,$iDBunSalesControl);
		$stmt->fetch();
	}else{
		die($stmt->error);
	}

	if(isset($_POST['btnimportnow'])){
		$mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();

		/* ----- SalesControl ----- */
		$query = "INSERT INTO salescontrol(unArea,unBranch,unSalesControl,unEmployeeOpen,unEmployeeClose,SCState,SCTimeStart,SCTimeEnd,SCPax,SCTotalAmount,SCDiscount,SCVatable,SCNetOfVat,SCTaxAmount,SCVatExempt,SCVatExemptSales,SCVatExemptAmount,SCNetSales,SCPaymentCash,SCPaymentOther,SCInvoiceStart,SCInvoiceEnd,SCItemCount,SCItemPaid,SCItemVoided,SCItemCancelled,SCItemRefunded,SCTransactionCount,SCTransactionPaid,SCTransactionVoided,SCTransactionCancelled,SCTransactionRefunded,SCReadingPrevious,SCReadingCurrent,SCQuota,SCQuotaInterval,SCQuotaPointAmount,SCQuotaPoint,SCQuotaTotalAmount) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,(Select BQuota From branch where idBranch=?),(Select BQuotaInterval From branch where idBranch=?),(Select BQuotaPointAmount From branch where idBranch=?),0,0)";
		if($stmt->prepare($query)){
			for ($i=0;$i<$iSalesControlMax;$i++){
				if ($data['salescontrol'][$i]['unSalesControl']>$iDBunSalesControl && ExecuteReader('Select ifNull(Sum(unSalesControl),0) as `result` From salescontrol where unBranch='.$_SESSION['import_unBranch'].' and unSalesControl ='.$data['salescontrol'][$i]['unSalesControl'])==0){
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
				if($data['salesdata'][$i]['unSalesControl']>$iDBunSalesControl && ExecuteReader('Select ifNull(Sum(unSalesData),0) as `result` From salesdata where unBranch='.$_SESSION['import_unBranch'].' and unSalesData ='.$data['salesdata'][$i]['unSalesData'])==0){
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
				if($data['invoicecontrol'][$i]['unSalesControl']>$iDBunSalesControl && ExecuteReader('Select ifNull(Sum(unInvoiceControl),0) as `result` From invoicecontrol where unBranch='.$_SESSION['import_unBranch'].' and unInvoiceControl ='.$data['invoicecontrol'][$i]['unInvoiceControl'])==0){
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
				if($data['invoicedata'][$i]['unSalesControl']>$iDBunSalesControl){
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
		
		$stmt->close();
		?>
        	<script type="text/javascript">
				//msgbox('Import Complete','','');
            </script>
		<?php
	}	
	if(!isset($_SESSION['import_unBranch'])){
		echo "<script>location.href='#popupbranch'</script>";
	}
?>
<link rel="stylesheet" type="text/css" href="css/import.css">
<div id="importcontainer">
	<div id="importtitlebar">
	    <div id="showimporttitleicon"></div>
        <div id="importtitle">IMPORT SALES [ <a href="#popupbranch" style="text-decoration:none;color:#DDD;"> Branch <?php echo ExecuteReader('Select BName as `result` from branch where unBranch='.$_SESSION['import_unBranch']); ?></a> ]</div>
    </div>
    
	<div class="importsection">
		<h2 align="center"><?php 
			echo date('F Y',strtotime($sDBTimeStart)); 
		?></h2>
    	<div id="calendarbody">
        	<div class="calendarheaderrow">
                <div class="calendarheadercolumn">Sun</div>
                <div class="calendarheadercolumn">Mon</div>
                <div class="calendarheadercolumn">Tue</div>
                <div class="calendarheadercolumn">Wed</div>
                <div class="calendarheadercolumn">Thu</div>
                <div class="calendarheadercolumn">Fri</div>
                <div class="calendarheadercolumn">Sat</div>
            </div>
            <?php
				$iMinDBCalendarDay = date('w',strtotime(date('Y-m-01',strtotime($sDBTimeStart))));
				$iMaxDBCalendarDay = date('w',strtotime(date('Y-m-t',strtotime($sDBTimeStart))));
				$iMaxDBCalendarDate = date('t',strtotime($sDBTimeStart));
				$iMaxDBPreviousCalendarDate = date('t',strtotime($sDBTimeStart.'-1 month'));
				$iCounter = 0;
				$iDayAfter = 0;
				$sBackGround = '#F000';
				$iDaysBeforeCalendar=($iMinDBCalendarDay) *-1;
				$sIcon = '<img src="img/icon/empty16.png" width="16" height="16" />';

				$iJSONIndexMin = 0;
				$iJSONIndexMax = 0;
				for ($i=0;$i<count($data['salescontrol']);$i++){
					if ($data['salescontrol'][$i]['unSalesControl']>$iDBunSalesControl){
						if($i>$iJSONIndexMax){$iJSONIndexMax=$i;}
					}else{
						if($i>$iJSONIndexMin){$iJSONIndexMin=$i;}
					}
				}
				
				for($i=$iDaysBeforeCalendar;$i<0;$i++){
					$iCounter++;
					$iDate = $iMaxDBPreviousCalendarDate+$i+1;
					$sDate = date('Y-m-'.$iDate,strtotime($sDBTimeStart.'-1 month'));
					$iSalesCount = ExecuteReader("Select count(unSalesControl) as `result` From salescontrol where Date(SCTimeStart)='".$sDate."'");
					$sIcon = ($iSalesCount>0)?'<img src="img/icon/check16.png" width="16" height="16" />':'<img src="img/icon/empty16.png" width="16" height="16" />';
					echo '<div class="calendaritemcolumn" style="color:#999;">'.$iDate.$sIcon.'</div>';
				}
				for($i=1;$i<=$iMaxDBCalendarDate;$i++){
					$iCounter++;
					$iDate = $i;
					$sDate = date('Y-m-'.$iDate,strtotime($sDBTimeStart));
					$iSalesCount = ExecuteReader("Select count(unSalesControl) as `result` From salescontrol where Date(SCTimeStart)='".$sDate."'");
					for($j=$iJSONIndexMin;$j<=$iJSONIndexMax;$j++){
						if(date('Y-m-d',strtotime($sDate)) == date('Y-m-d',strtotime($data['salescontrol'][$j]['SCTimeStart'])) && $data['salescontrol'][$j]['unSalesControl']>$iDBunSalesControl){
							$sIcon = '<img src="img/icon/import16.png" width="16" height="16" />';
							break;
						}
					}
					if ($iSalesCount>0){$sIcon='<img src="img/icon/check16.png" width="16" height="16" />';}
					echo '<div class="calendaritemcolumn" style="color:#333;">'.$iDate.$sIcon.'</div>';
					$sIcon = '<img src="img/icon/empty16.png" width="16" height="16" />';
				}
				for($i=$iCounter;$i<42;$i++){
					$iDayAfter++;
					$iDate=$iDayAfter;
					$sDate = date('Y-m-'.$iDate,strtotime($sDBTimeStart.'+1 month'));
					$iSalesCount = ExecuteReader("Select count(unSalesControl) as `result` From salescontrol where Date(SCTimeStart)='".$sDate."'");
					$sIcon = ($iSalesCount>0)?'<img src="img/icon/check16.png" width="16" height="16" />':'<img src="img/icon/empty16.png" width="16" height="16" />';
					echo '<div class="calendaritemcolumn" style="color:#999;">'.$iDate.$sIcon.'</div>';
				}
			?>
        </div>
        <div style="width:98%;float:left;">
	        <div class="buttons" style="margin:20px auto;padding-left:10px;padding-right:10px;" onClick="location.href='#showimport'" title="Import"><img src="img/icon/import.png" width="16" height="16" style="padding-right:10px;">Choose file...</div>
        </div>    
        
	</div>
    
	<div class="importsection">
		<div class="importrow">
            <div class="importcolumn"><img src="img/icon/employeearea.png" width="16" height="16"> Area</div>
            <div class="importcolumn"><?php echo $data['AName']; ?></div>
        </div>
		<div class="importrow">
        	<div class="importcolumn"><img src="img/icon/branch.png" width="16" height="16"> Branch</div>
            <div class="importcolumn"><?php echo $data['BName']; ?></div>
        </div>
    	<div class="importrow" style="border-bottom:thin solid #DDD;">
        	<div class="importcolumn">TYPE</div>
            <div class="importcolumn">FROM</div>
            <div class="importcolumn">TO</div>
            <div class="importcolumn">TOTAL</div>
        </div>
		<div class="importrow" style="border-top:thin solid #FFF;">
	        <div class="importcolumn"><img src="img/icon/sales.png" width="16" height="16" /> Sales Control</div>
            <div class="importcolumn"><?php echo date('Y-m-d',strtotime($data['salescontrol'][0]['SCTimeStart'])); ?></div>
            <div class="importcolumn"><?php echo date('Y-m-d',strtotime($data['salescontrol'][$iSalesControlMax-1]['SCTimeStart'])); ?></div>
            <div class="importcolumn"><?php printf('%04d', count($data['salescontrol'])); ?></div>
        </div>
		<div class="importrow">
        	<div class="importcolumn"><img src="img/icon/user.png" width="16" height="16"> Sales Data</div>
            <div class="importcolumn"><?php echo date('Y-m-d',strtotime($data['salesdata'][0]['SDTimeStart'])); ?></div>
            <div class="importcolumn"><?php echo date('Y-m-d',strtotime($data['salesdata'][$iSalesDataMax-1]['SDTimeStart'])); ?></div>
            <div class="importcolumn"><?php printf('%04d', count($data['salesdata'])); ?></div>
		</div>
		<div class="importrow">
        	<div class="importcolumn"><img src="img/icon/invoice.png" width="16" height="16"> Denomination</div>
            <div class="importcolumn"><?php printf('%06d', $data['denominationcount'][0]['unSalesControl']); ?></div>
            <div class="importcolumn"><?php printf('%06d', $data['denominationcount'][$iDenominationCountMax-1]['unSalesControl']); ?></div>
            <div class="importcolumn"><?php printf('%04d', count($data['denominationcount'])); ?></div>
        </div>
		<div class="importrow">
        	<div class="importcolumn"><img src="img/icon/invoice.png" width="16" height="16"> Invoice Control</div>
            <div class="importcolumn"><?php printf('%06d', $data['invoicecontrol'][0]['unInvoiceControl']); ?></div>
            <div class="importcolumn"><?php printf('%06d', $data['invoicecontrol'][$iInvoiceControlMax-1]['unInvoiceControl']); ?></div>
            <div class="importcolumn"><?php printf('%04d', count($data['invoicecontrol'])); ?></div>
        </div>
		<div class="importrow">
        	<div class="importcolumn"><img src="img/icon/invoice.png" width="16" height="16"> Invoice Data</div>
            <div class="importcolumn"><?php printf('%06d', $data['invoicedata'][0]['unInvoiceControl']); ?></div>
            <div class="importcolumn"><?php printf('%06d', $data['invoicedata'][$iInvoiceDataMax-1]['unInvoiceControl']); ?></div>
            <div class="importcolumn"><?php printf('%04d', count($data['invoicedata'])); ?></div>
        </div>
		<div class="importrow">
        	<div class="importcolumn"><img src="img/icon/discounttype.png" width="16" height="16"> Discount</div>
            <div class="importcolumn"><?php printf('%06d', $data['discountcontrol'][0]['unInvoiceControl']); ?></div>
            <div class="importcolumn"><?php printf('%06d', $data['discountcontrol'][$iDiscountControlMax-1]['unInvoiceControl']); ?></div>
            <div class="importcolumn"><?php printf('%04d', count($data['discountcontrol'])); ?></div>
		</div>
		<div class="importrow" style="border-bottom:thin solid #DDD;">
        	<div class="importcolumn"><img src="img/icon/paymenttype.png" width="16" height="16"> Payment</div>
            <div class="importcolumn"><?php printf('%06d', $data['payment'][0]['unInvoiceControl']); ?></div>
            <div class="importcolumn"><?php printf('%06d', $data['payment'][$iPaymentMax-1]['unInvoiceControl']); ?></div>
            <div class="importcolumn"><?php printf('%04d', count($data['payment'])); ?></div>
		</div>
    	<div class="importrow" style="border-top:thin solid #FFF;">
        	<div class="importcolumn">TOTAL</div>
            <div class="importcolumn"></div>
            <div class="importcolumn"></div>
            <div class="importcolumn"><?php printf('%04d', $rowcount); ?></div>
        </div>
        <div class="importrow" style="text-align:center;margin-top:20px;">
        <form action="import.php" method="post">
            <input name="hdndata" type="hidden" value="<?php echo htmlspecialchars($contents,ENT_QUOTES); ?>" />
            <input name="btnimportnow" type="submit" value="Start Import Now" title="Import" class="buttons">
        </form>
        </div>
    </div>
</div>

<div id="popupbranch" class="popup">
    <div class="popupcontainer" style="height:400px;">
        <div class="popuptitle" align="center">SELECT BRANCH</div>
        <div class="popupitem">
        
            <div class="listview" style="height:350px;">
                <div class="column">
                    <div class="columnheader">Branch</div>
                </div>
                <div class="row">
                <?php
                    $mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysqli->stmt_init();
                    if($stmt->prepare("Select branch.unBranch,BName 
                                        from branch 
                                        where branch.`Status` = 1 and Btype=1 and unArea=? 
                                        Order by BName")){
                        $stmt->bind_param('i',$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
                        while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="location.href='import.php?&setbranch=true&ibid=<?php echo $unBranch; ?>'" style="cursor:pointer;">
                                <div class="listviewsubitem"><?php echo $BName; ?></div>
                            </div>
                        <?php
                        }
                        $stmt->close();
                    }
                ?>                   
                </div>
            </div>

            <div align="center" style="padding-top:10px;">
                <input type="button" value="Close" title="Close" onClick="location.href='#close'" class="buttons" >
            </div>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>
<?php
	session_start();
	switch($_POST['qid']){
	case 'LoadShift':
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("SELECT unSalesData,SDState,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee,SDNetSales
											FROM salesdata
											LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
											WHERE unBranch=? AND unSalesControl = ? ORDER BY unBranch DESC")){
							$stmt->bind_param('ii',$_POST['bid'],$_POST['scid']);
							$stmt->execute();
							$stmt->bind_result($unSalesData,$SDState,$SDTimeStart,$unEmployee,$SDNetSales);
							
							while($stmt->fetch()){
								$unSalesData1=sprintf('%06d', $unSalesData);
								?>
								<div class="listviewitem" id="listviewitemSD-<?php echo $unSalesData; ?>" style="cursor:default;"  onClick="SEThdnunSD(<?php echo $unSalesData; ?>,<?php if($SDState=='Close'){echo '0';}else{echo '1';};?>)">
									<div class="listviewsubitem" style="width:80px; text-align:left;background-image:url(img/icon/<?php if($SDState=='Close'){echo 'SDclosed';}else{echo 'SDopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesData1; ?></div>
									<div class="listviewsubitem" style="width:100px;"><?php echo date('H:i:sa',strtotime($SDTimeStart)); ?></div>
                                    <div class="listviewsubitem" style="width:150px;"><?php echo $unEmployee; ?></div>
									<div class="listviewsubitem" style="width:150px;"><?php echo $SDNetSales; ?></div>
                                    <div class="selectedSD" id="selectedSD-<?php echo $unSalesData; ?>" style="padding-left:30px;padding-top:5px;color:<?php if($SCState=='Close'){echo 'red';}else{echo 'green';};?>;"></div>
								</div>
								<?php
							}
							$stmt->close();
						}
	break;
	case 'LoadShiftINV':
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("SELECT unSalesData,SDState,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee,SDNetSales
											FROM salesdata
											LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
											WHERE unBranch=? AND unSalesControl = ? ORDER BY unBranch DESC")){
							$stmt->bind_param('ii',$_POST['bid'],$_POST['scid']);
							$stmt->execute();
							$stmt->bind_result($unSalesData,$SDState,$SDTimeStart,$unEmployee,$SDNetSales);
							
							while($stmt->fetch()){
								$unSalesData1=sprintf('%06d', $unSalesData);
								?>
								<div class="listviewitem" id="listviewitemSDINV-<?php echo $unSalesData; ?>" style="cursor:default;"  onClick="SEThdnunSDINV(<?php echo $unSalesData; ?>,<?php if($SDState=='Close'){echo '0';}else{echo '1';};?>)">
									<div class="listviewsubitem" style="width:80px; text-align:left;background-image:url(img/icon/<?php if($SDState=='Close'){echo 'SDclosed';}else{echo 'SDopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesData1; ?></div>
									<div class="listviewsubitem" style="width:100px;"><?php echo date('H:i:sa',strtotime($SDTimeStart)); ?></div>
                                    <div class="listviewsubitem" style="width:150px;"><?php echo $unEmployee; ?></div>
									<div class="listviewsubitem" style="width:150px;"><?php echo $SDNetSales; ?></div>
                                    <div class="selectedSDINV" id="selectedSDINV-<?php echo $unSalesData; ?>" style="padding-left:30px;padding-top:5px;color:<?php if($SCState=='Close'){echo 'red';}else{echo 'green';};?>;"></div>
								</div>
								<?php
							}
							$stmt->close();
						}
	break;
	case 'LoadShiftINVEdit':
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("SELECT unInventoryControl,unSalesData,SDState,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee,SDNetSales
											FROM salesdata
											LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
											WHERE unBranch=? AND unSalesControl = ? AND salesdata.Status=1 ORDER BY unBranch DESC")){
							$stmt->bind_param('ii',$_POST['bid'],$_POST['scid']);
							$stmt->execute();
							$stmt->bind_result($unInventoryControl,$unSalesData,$SDState,$SDTimeStart,$unEmployee,$SDNetSales);
							
							while($stmt->fetch()){
								$unSalesData1=sprintf('%06d', $unSalesData);
								?>
								<div class="listviewitem" id="listviewitemSDINVEdit-<?php echo $unSalesData; ?>" style="cursor:default;"  onClick="SEThdnunSDINVEdit(<?php echo $unSalesData; ?>,<?php echo $_POST['bid'];?>,<?php echo $unInventoryControl; ?>)">
									<div class="listviewsubitem" style="width:80px; text-align:left;background-image:url(img/icon/<?php if($SDState=='Close'){echo 'SDclosed';}else{echo 'SDopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;"><?php echo $unSalesData1; ?></div>
									<div class="listviewsubitem" style="width:100px;"><?php echo date('H:i:sa',strtotime($SDTimeStart)); ?></div>
                                    <div class="listviewsubitem" style="width:150px;"><?php echo $unEmployee; ?></div>
									<div class="listviewsubitem" style="width:150px;"><?php echo $SDNetSales; ?></div>
                                    <div class="selectedSDINVEdit" id="selectedSDINVEdit-<?php echo $unSalesData; ?>" style="padding-left:30px;padding-top:5px;color:<?php if($unInventoryControl==0){echo 'green';}else{echo 'red';};?>;"></div>
								</div>
								<?php
							}
							$stmt->close();
						}
	break;
	case 'saveDateSC':
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("UPDATE salescontrol SET SCTimeStart = '".$_POST['scts']."' WHERE unBranch=? AND unSalesControl = ? AND Status=1")){
							$stmt->bind_param('ii',$_POST['bid'],$_POST['scid']);
							$stmt->execute();
							$stmt->close();
							//echo 'SAVED';
						}else{
							//echo "UPDATE salescontrol SET SCTimeStart = '".$_POST['scts']."' WHERE unBranch=".$_POST['bid']." AND unSalesControl = ".$_POST['scid']." AND Status=1";
						}
	break;
	case 'searchstring':
		echo '<div id="row">';
		$i=0;
		$ti=0;
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();	
		if($stmt->prepare("Select templateitemdata.unProductItem,PIName,TIDPrice from branch 
							inner join templateitemcontrol on templateitemcontrol.unTemplateItemControl=branch.unTemplateItemControl 
							inner join templateitemdata on templateitemcontrol.unTemplateItemControl=templateitemdata.unTemplateItemControl 
							inner join productitem on templateitemdata.unProductItem=productitem.unProductItem 
							inner join productgroup on productitem.unProductGroup=productgroup.unProductGroup 
							where unProductType=(Select unProductType from producttype where PTName='Products') and templateitemdata.`status`=1 and unBranch=? and PIName like ? Order by PIName limit 10")){
			$likestring='%'.$_POST['query'].'%';
			$stmt->bind_param('is',$_POST['bid'],$likestring);
			$stmt->execute();
			$stmt->bind_result($unProductItem,$PIName,$TIDPrice);
			while($stmt->fetch()){
			$ti=$ti+1;
			?>
				<div class="listboxitem resultitemname" id="lvresult-<?php echo $i; ?>" onClick="selectresult('<?php echo $PIName; ?>',<?php echo $unProductItem; ?>,'<?php echo $TIDPrice; ?>')" style="cursor:pointer;" tabindex="<?php echo $i; ?>">
					<?php echo $PIName.' @ '.$TIDPrice; ?>
				</div>
			<?php
				$i++;
			}
			?>
            <script>
				$('.resultitemname').keyup(function(e) {
					if(e.keyCode==13){
						//alert('selected');
					}else{
						//alert('tab');
					}
				});
			</script>
			<?php
			$stmt->close();
		}else{
			die($stmt->error);
		}
		echo '</div>';
		break;
		
	case 'getcash':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Select ICCash From invoicecontrol Where unInvoiceControl=?")){
			$stmt->bind_param("i",$_POST['idic']);
			$stmt->execute();
			$stmt->bind_result($ICCash);
			$stmt->fetch();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		echo $ICCash;
		break;
		
	case 'delcreditcard':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Update cardtransaction set `Status`=0 Where unCardTransaction=?")){
			$stmt->bind_param("i",$_POST['idct']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}

		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update invoicecontrol set ICCard=(Select Sum(CTAmount) From cardtransaction Where `Status`=1 And unInvoiceControl=?) Where unInvoiceControl=?")){
			$stmt->bind_param("ii",$_POST['idic'],$_POST['idic']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		echo 'invoice.php?&id='.$_POST['idic'];		
		break;
		
	case 'delgc':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Update giftcertificatetransaction set `Status`=0 Where unGiftCertificateTransaction=?")){
			$stmt->bind_param("i",$_POST['idgct']);
			$stmt->execute(); 
			die($stmt->error);
		}

		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update invoicecontrol set ICGC=(Select Sum(GCTAmount) From giftcertificatetransaction Where `Status`=1 And unInvoiceControl=?) Where unInvoiceControl=?")){
			$stmt->bind_param("ii",$_POST['idic'],$_POST['idic']);
			$stmt->execute();
			$stmt->close();
		}else{
			die($stmt->error);
		}
		
		echo 'invoice.php?&id='.$_POST['idic'];		
		break;

	case 'delitem':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Update invoicedata set `Status`=0 Where unInvoiceData=?")){
			$stmt->bind_param("i",$_POST['idid']);
			$stmt->execute(); 
			die($stmt->error);
		}
		
		echo 'invoice.php?&id='.$_POST['idic'];		
		break;
		
	case 'ViewSoldInvoice':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("SELECT unInvoiceControl,ICNetSales,ICDiscount,ICTotalAmount FROM invoicecontrol
							WHERE Status=1 AND invoicecontrol.unSalesData = ? AND invoicecontrol.unBranch = ?")){
			$stmt->bind_param('ii',$_POST['idSD'],$_POST['bid']);
			$stmt->execute();
			$stmt->bind_result($unInvoiceControl,$ICNetSales,$ICDiscount,$ICTotalAmount);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" id="listviewitem-<?php echo $unInvoiceControl; ?>" style="padding-top:2px; padding-bottom:2px; padding-left:0px;" onClick="viewinvoicedata('<?php echo $unInvoiceControl; ?>','<?php echo $_POST['bid'] ?>')">
					<div class="listviewsubitem" style="width:30px; text-align:center;"><?php echo $unInvoiceControl; ?></div>
					<div class="listviewsubitem" style="width:85px; text-align:right;"><?php echo $ICNetSales; ?></div>
					<div class="listviewsubitem" style="width:85px; text-align:right;"><?php echo $ICDiscount; ?></div>
                    <div class="listviewsubitem" style="width:90px; text-align:right;"><?php echo $ICTotalAmount; ?></div>	
				</div>
				<?php
			}
		}
	break;
	case 'ViewEditInvoice':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("SELECT unInvoiceControl,ICNetSales,ICDiscount,ICTotalAmount FROM invoicecontrol
							WHERE Status=1 AND invoicecontrol.unSalesData = ? AND invoicecontrol.unBranch = ?")){
			$stmt->bind_param('ii',$_POST['unsd'],$_POST['bid']);
			$stmt->execute();
			$stmt->bind_result($unInvoiceControl,$ICNetSales,$ICDiscount,$ICTotalAmount);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" id="listviewitem-<?php echo $unInvoiceControl; ?>" onClick="SEThdnunINVEdit('<?php echo $unInvoiceControl; ?>','<?php echo $_POST['bid'] ?>')">
					<div class="listviewsubitem" style="width:100px;"><?php echo $unInvoiceControl; ?></div>
					<div class="listviewsubitem" style="width:100px;"><?php echo $ICNetSales; ?></div>
					<div class="listviewsubitem" style="width:150px;"><?php echo $ICDiscount; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $ICTotalAmount; ?></div>
                    <div class="selectedINVEdit" id="selectedINVEdit-<?php echo $unInvoiceControl; ?>" style="padding-left:30px;padding-top:5px; color:green;"></div>	
				</div>
				<?php
			}
		}
	break;
	case 'ViewInvoiceData':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("SELECT IDQuantity,PIName,IDPrice,IDTotalAmount FROM invoicedata
							INNER JOIN productitem ON invoicedata.unProductItem = productitem.unProductItem
							WHERE invoicedata.Status=1 AND unInvoiceControl = ? AND unBranch = ? ")){
			$stmt->bind_param('ii',$_POST['idID'],$_POST['bid']);
			$stmt->execute();
			$stmt->bind_result($IDQuantity,$PIName,$IDPrice,$IDTotalAmount);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" style="padding-top:2px; padding-bottom:2px; padding-left:0px; background-color:#EEE;">
					<div class="listviewsubitem" style="width:15px; text-align:center; margin-right:15px"><?php echo $IDQuantity; ?></div>
					<div class="listviewsubitem" style="width:240px; text-align:left;"><?php echo $PIName; ?></div>
					<div class="listviewsubitem" style="width:100px; text-align:right;"><?php echo $IDPrice; ?></div>
                    <div class="listviewsubitem" style="width:100px; text-align:right;"><?php echo $IDTotalAmount; ?></div>	
				</div>
				<?php
			}
		}
	break;
	}
	
?>

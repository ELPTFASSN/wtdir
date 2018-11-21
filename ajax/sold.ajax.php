<?php include '../include/var.inc.php';
include '../include/class.inc.php';
session_start();

switch($_POST['qid']){
	case 'LoadShift':
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("SELECT unSalesData,SDState,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee,SDNetSales
											FROM salesdata
											LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
											WHERE unInventoryControl = 0 AND unBranch=? AND unSalesControl = ? ORDER BY unBranch DESC")){
							$stmt->bind_param('ii',$_POST['bid'],$_POST['scid']);
							$stmt->execute();
							$stmt->store_result();
							$stmt->bind_result($unSalesData,$SDState,$SDTimeStart,$unEmployee,$SDNetSales);
							while($stmt->fetch()){
								$unSalesData1=sprintf('%06d', $unSalesData);
								?>
								<div class="listviewitem" id="listviewitem-<?php echo $unSalesData; ?>" style="cursor:default;"  onClick="SEThdnunSD(<?php echo $_POST['bid']; ?>,<?php echo $unSalesData; ?>,<?php if($SDState=='Close'){echo '1';}else{echo '0';};?>)">
									<div class="listviewsubitem" style="width:80px; text-align:left;background-image:url(img/icon/<?php if($SDState=='Close'){echo 'SDclosed';}else{echo 'SDopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesData1; ?></div>
									<div class="listviewsubitem" style="width:100px;"><?php echo date('H:i:sa',strtotime($SDTimeStart)); ?></div>
                                    <div class="listviewsubitem" style="width:150px;"><?php echo $unEmployee; ?></div>
									<div class="listviewsubitem" style="width:150px;"><?php echo $SDNetSales; ?></div>
                                    <div class="selected" id="selected-<?php echo $unSalesData; ?>" style="padding-left:30px;padding-top:5px"></div>
								</div>
								<?php
							}
							$rowcount = $stmt->num_rows;
							if($rowcount<1){
								?>
                                <div style="font-size:14px; text-align:center; width:100%; height:auto; padding:10px;">All shifts are already mapped.</div>
                                <?php
							}
							$stmt->close();
						}
	break;
	case 'ViewSoldData':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("SELECT PIName,productuom.PUOMName,SUM(IDQuantity) as IDQuantity FROM invoicedata
							INNER JOIN productitem ON invoicedata.unProductItem = productitem.unProductItem
							INNER JOIN productuom  ON productitem.unProductUOM = productuom.unProductUOM
							WHERE invoicedata.unSalesData = ? AND unBranch =? GROUP BY productitem.unProductItem")){
			$stmt->bind_param('ii',$_POST['idSD'],$_POST['bid']);
			$stmt->execute();
			$stmt->bind_result($PIName,$PUOMName,$IDQuantity);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" style="padding-top:2px; padding-bottom:2px;">
					<div class="listviewsubitem" style="width:241px;"><?php echo $PIName; ?></div>
					<div class="listviewsubitem" style="width:51px; text-align:right;"><?php echo $IDQuantity; ?></div>
					<div class="listviewsubitem" style="width:51px; text-align:center;"><?php echo $PUOMName; ?></div>	
				</div>
				<?php
			}
		}
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
				<div class="listviewitem" id="listviewitem-<?php echo $unInvoiceControl; ?>" style="padding-top:2px; padding-bottom:2px; padding-left:0px;" onClick="executeaction('invoicedata','<?php echo $_POST['bid'] ?>','<?php echo $unInvoiceControl; ?>')">
					<div class="listviewsubitem" style="width:30px; text-align:center;"><?php echo $unInvoiceControl; ?></div>
					<div class="listviewsubitem" style="width:85px; text-align:right;"><?php echo $ICNetSales; ?></div>
					<div class="listviewsubitem" style="width:85px; text-align:right;"><?php echo $ICDiscount; ?></div>
                    <div class="listviewsubitem" style="width:90px; text-align:right;"><?php echo $ICTotalAmount; ?></div>	
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
							WHERE invoicedata.Status=1 AND unInvoiceControl = ? AND unBranch = ?")){
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
	case 'LoadFraction':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		$stmt2 = $mysqli->stmt_init();
		$unProductUOMarr=array();
		$PCSetarr=array();
		$PCRatioarr=array();
		if($stmt->prepare("SELECT unProductConversion,UOM.PUOMName AS unProductUOM,PCSet,PCRatio FROM productconversion
						   LEFT JOIN productuom AS `UOM` ON productconversion.unProductUOM = UOM.unProductUOM WHERE unProductItem=? AND PCSet='W' ORDER BY PCRatio,PCSet DESC")){
			$stmt->bind_param('i',$_POST['unPI']);
			$stmt->execute();
			$stmt->bind_result($unProductConversion,$unProductUOM,$PCSet,$PCRatio);
			/*$unProductUOMarr=array();
			$PCSetarr=array();
			$PCRatioarr=array();*/
			while($stmt->fetch()){
				//$stmt->fetch();
				//echo "<br>".$unProductConversion."<br>".$unProductUOM."<br>".$PCSet;
				$unProductUOMarr[]=$unProductUOM;	
				$PCSetarr[]=$PCSet;
				$PCRatioarr[]=$PCRatio;	
			}
		}
		if($stmt2->prepare("SELECT unProductConversion,UOM.PUOMName AS unProductUOM,PCSet,PCRatio FROM productconversion
						   LEFT JOIN productuom AS `UOM` ON productconversion.unProductUOM = UOM.unProductUOM WHERE unProductItem=? AND PCSet!='W' ORDER BY PCSet,PCRatio DESC")){
			$stmt2->bind_param('i',$_POST['unPI']);
			$stmt2->execute();
			$stmt2->bind_result($unProductConversion,$unProductUOM,$PCSet,$PCRatio);
			while($stmt2->fetch()){
				$unProductUOMarr[]=$unProductUOM;	
				$PCSetarr[]=$PCSet;
				$PCRatioarr[]=$PCRatio;	
			}
		}

		$conversion=array();
		$conversion['PCUnit']=$unProductUOMarr;
		$conversion['PCSet']=$PCSetarr;
		$conversion['PCRatio']=$PCRatioarr;
		echo json_encode($conversion);
	break;
}
?>


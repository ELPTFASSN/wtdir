<?php 
require 'header.php'; 
?>
			
<link rel="stylesheet" type="text/css" href="css/inventory.css">
<script src="js/inventory.js"></script>
<form name="frmcashcrew" id="frmcashcrew" action="include/manualinventory.fnc.php" method="post">
<div id="toolbar">
	<button type="submit" class="toolbarbutton" title="Save" name="btnSaveCC" id="btnSaveCC" style="background-image:url(img/icon/save.png);"></button>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(".codtxt").change(function() {
			// alert(this.value);
			if(this.value==0){
				var thisID1 = $(this).attr('id').split('-')[1];
				var thisID2 = $(this).attr('id').split('-')[2];
				$("#cod-1-"+thisID2).val(0);
				$("#cod-7-"+thisID2).val(0);
			}
		});
	});
</script>
	
<div class="listview">
	<?php
		//$TotalPettyCash=0;
		$TotalBills=0;
		$TotalCoins=0;
		$TotalCashCount=0;
		$CashVariance=0;
		$BankVariance=0;
		$EndBalanace=0;
		$_SESSION['did']=$_GET['did'];
		$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT SUM(  `PCCAmount` ) FROM pettycashcontrol WHERE  `unInventoryControl` = ?")){
			$stmt->bind_param('i',$_GET['did']);
            $stmt->execute();
			$stmt->bind_result($TotalPettyCash);
			$stmt->fetch();
			$stmt->close();
		}
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT SUM(  `IDSoldAmount` ) 
			FROM inventorydata
			INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
			INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
			WHERE  `unInventoryControl` =?
			AND unProductType = '1'")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($TotalSales);
				$stmt->fetch();
				$stmt->close();
		}
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT  `SD1000` ,  `SD500` ,  `SD200` ,  `SD100` ,  `SD50` ,  `SD20` ,  `SD10` ,  `SD5` ,  `SD1` ,  `SDp25` ,  `SDp10` ,  `SDp05` ,  `SDBegBalance` ,  `SDTotalSales` ,  `SDTotalDeposit` , `SDPettyCash` ,  `SDSpecialDisc` ,  `SDEndBalance` ,  `SDDEndBalance` ,  `SDTotalCashCount` ,  `SDCashOnBank` ,  `SDGC` ,  `SDSC` ,  `SDBulk` , `SDShortPMix`,`SDShortWater`,`SDShortSugar`,`SDOtherSupplies`,`SDShortCashFund`, `SDAdjPMix`,`SDAdjWater`,`SDAdjSugar`,`SDAdjSupplies`,`SDAdjCashFund`,`SDShortCashDeposit`,`SDShortTotal`, `ICDate`
				FROM  `salesdata` 
				INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
				WHERE  salesdata.`unInventoryControl` =?")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($SD1000,$SD500,$SD200,$SD100,$SD50,$SD20,$SD10,$SD5,$SD1,$SDp25,$SDp10,$SDp05,$SDBegBalance,$SDTotalSales,$SDTotalDeposit,$SDPettyCash,$SDSpecialDisc,$SDEndBalance,$SDDEndBalance,$SDTotalCashCount,$SDCashOnBank,$SDGC,$SDSC,$SDBulk,$SDShortPMix,$SDShortWater,$SDShortSugar,$SDOtherSupplies,$SDShortCashFund,$SDAdjPMix,$SDAdjPWater,$SDAdjSugar,$SDAdjSupplies,$SDAdjCashFund,$SDShortCashDeposit,$SDShortTotal,$ICDate);
				$stmt->fetch();
				$stmt->close();
		}
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT PIName, ifnull((SELECT  SUM(IDProcessOut) FROM  inventorydata INNER JOIN productitem ON productitem .unProductItem = inventorydata.unProductItem WHERE unInventoryControl=? AND PIName LIKE '%Greaseproof%'),0) as GPOut,  ifnull((SELECT  IDCharge FROM  inventorydata INNER JOIN productitem ON productitem .unProductItem = inventorydata.unProductItem WHERE unInventoryControl=? AND PIName LIKE '%Greaseproof%'),0) as GPCharge,
		ifnull((SELECT SUM(IDSoldQuantity) FROM inventorydata INNER JOIN productitem ON productitem .unProductItem = inventorydata.unProductItem WHERE unInventoryControl=? AND PIName LIKE '%Baked%'),0) as GPSold
		FROM inventorydata 
		INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
		INNER JOIN templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
		INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
		INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
		WHERE inventorydata.unInventoryControl =?
		AND unProductType =2
		AND templateitemdata.unTemplateItemControl = ( 
		SELECT unTemplateItemControl
		FROM branch
		WHERE unBranch =? ) 
		AND PIName LIKE  '%Greaseproof%'
		LIMIT 0 , 30")){
				$stmt->bind_param('iiiii',$_GET['did'],$_GET['did'],$_GET['did'],$_GET['did'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($GPPIName,$GPOut,$GPCharge,$GPSold);
				$stmt->fetch();
				$stmt->close();
		}
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT PIName,
		ifnull((SELECT SUM( IDProcessOut ) FROM inventorydata INNER JOIN productitem ON productitem .unProductItem = inventorydata.unProductItem WHERE unInventoryControl=? AND PIName = 'Cold Cups 12oz'),0) as CCOut,
		ifnull((SELECT IDCharge FROM inventorydata INNER JOIN productitem ON productitem .unProductItem = inventorydata.unProductItem WHERE unInventoryControl=? AND PIName = 'Cold Cups 12oz'),0) as CCCharge
		FROM inventorydata 
		INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
		INNER JOIN templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
		INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
		INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
		WHERE inventorydata.unInventoryControl =?
		AND unProductType =2
		AND templateitemdata.unTemplateItemControl = ( 
		SELECT unTemplateItemControl
		FROM branch
		WHERE unBranch =? ) 
		AND PIName = 'Cold Cups 12oz'
		LIMIT 0 , 30")){
				$stmt->bind_param('iiii',$_GET['did'],$_GET['did'],$_GET['did'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($CCPIName,$CCOut,$CCCharge);
				$stmt->fetch();
				$stmt->close();
		}
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT PIName,
		ifnull((SELECT SUM( IDSoldQuantity ) FROM inventorydata INNER JOIN productitem ON productitem.unProductItem = inventorydata.unProductItem INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup WHERE unInventoryControl =? AND PGName Like  '%Ice Tea%'),0) as CCLSold,
		ifnull((SELECT SUM( IDProcessOut ) FROM inventorydata INNER JOIN productitem ON productitem .unProductItem = inventorydata.unProductItem WHERE unInventoryControl=? AND PIName LIKE '%Lids%'),0) as CCLOut,
		ifnull((SELECT IDCharge FROM inventorydata INNER JOIN productitem ON productitem .unProductItem = inventorydata.unProductItem WHERE unInventoryControl=? AND PIName LIKE '%Lids%'),0) as CCLCharge
		FROM inventorydata 
		INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
		INNER JOIN templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
		INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
		INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
		WHERE inventorydata.unInventoryControl =?
		AND unProductType =2
		AND templateitemdata.unTemplateItemControl = ( 
		SELECT unTemplateItemControl
		FROM branch
		WHERE unBranch =? ) 
		AND PIName LIKE  '%Lids%'
		LIMIT 0 , 30")){
				$stmt->bind_param('iiiii',$_GET['did'],$_GET['did'],$_GET['did'],$_GET['did'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($CCLPIName,$CCLSold,$CCLOut,$CCLCharge);
				$stmt->fetch();
				$stmt->close();
		}
		$GPVar = $GPOut-$GPSold;
		$CCVar = $CCOut-$CCLSold;
		$CCLVar = $CCLOut-$CCLSold;
		$TotalBills=($SD1000*1000)+($SD500*500)+($SD200*200)+($SD100*100)+($SD50*50)+($SD20*20);
		$TotalCoins=($SD10*10)+($SD5*5)+($SD1*1)+($SDp25*.25)+($SDp10*.10)+($SDp05*.05);
		$SDChargePMix = $SDShortPMix - $SDAdjPMix;
		$SDChargeWater = $SDShortWater - $SDAdjPWater;
		$SDChargeSugar = $SDShortSugar - $SDAdjSugar;
		$SDChargeSupplies = $SDOtherSupplies - $SDAdjSupplies;
		$SDChargeCashFund = $SDShortCashFund - $SDAdjCashFund;
		/*$TotalCashCount=$TotalBills+$TotalCoins;
		$EndBalanace=$SDBegBalance+$SDTotalSales-$SDTotalDeposit-$TotalPettyCash-$SDSpecialDisc;
		$CashVariance=$EndBalanace-$TotalCashCount;
		$BankVariance=$SDTotalDeposit-$SDCashOnBank;*/
	?>
    <div class="group" style="padding-left: 100px">CASH COUNT</div>
    	<div class="misdenocol" style="display:none; float:left"> <!--display:inline-table -->
            <div style="height:23px"><div class="misdenolabel">1000 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-1-4" readonly style=" color:#999" name="txt-1-4" placeholder="0" value="<?php echo $SD1000; ?>"/><input type="hidden" id="Bx-1" value="1000" /></div></div>
            <div style="height:23px"><div class="misdenolabel">500 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-2-4" readonly style=" color:#999" name="txt-2-4" placeholder="0" value="<?php echo $SD500; ?>"/><input type="hidden" id="Bx-2" value="500" /></div></div>
            <div style="height:23px"><div class="misdenolabel">200 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-3-4" readonly style=" color:#999" name="txt-3-4" placeholder="0" value="<?php echo $SD200; ?>"/><input type="hidden" id="Bx-3" value="200" /></div></div>
            <div style="height:23px"><div class="misdenolabel">100 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-4-4" readonly style=" color:#999" name="txt-4-4" placeholder="0" value="<?php echo $SD100; ?>"/><input type="hidden" id="Bx-4" value="100" /></div></div>
            <div style="height:23px"><div class="misdenolabel">50 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-5-4" readonly style=" color:#999" name="txt-5-4" placeholder="0" value="<?php echo $SD50; ?>"/><input type="hidden" id="Bx-5" value="50" /></div></div>
            <div style="height:23px"><div class="misdenolabel">20 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-6-4" readonly style=" color:#999" name="txt-6-4" placeholder="0" value="<?php echo $SD20; ?>"/><input type="hidden" id="Bx-6" value="20" /></div></div>
            <div style="height:23px; color:#da251d"><div class="misdenolabel"><b>TOTAL BILLS</b> </div><div class="misdenoholder"><input class="misdenomination" readonly type="text" id="txt-7-4" name="txt-7-4" value="<?php echo number_format($TotalBills,2); ?>" style="text-align:right; color:#999"/></div></div>
        </div>
        <div class="misdenocol" style="display:none; float:left">
        	<div style="height:23px"><div class="misdenolabel">10.00 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-1-5" readonly style=" color:#999" name="txt-1-5" placeholder="0" value="<?php echo $SD10; ?>"/><input type="hidden" id="Cx-1" value="10" /></div></div>
            <div style="height:23px"><div class="misdenolabel">5.00 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-2-5" readonly style=" color:#999" name="txt-2-5" placeholder="0" value="<?php echo $SD5; ?>"/><input type="hidden" id="Cx-2" value="5" /></div></div>
            <div style="height:23px"><div class="misdenolabel">1.00 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-3-5" readonly style=" color:#999" name="txt-3-5" placeholder="0" value="<?php echo $SD1; ?>"/><input type="hidden" id="Cx-3" value="1" /></div></div>
            <div style="height:23px"><div class="misdenolabel">0.25 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-4-5" readonly style=" color:#999" name="txt-4-5" placeholder="0" value="<?php echo $SDp25; ?>"/><input type="hidden" id="Cx-4" value=".25" /></div></div>
            <div style="height:23px"><div class="misdenolabel">0.10 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-5-5" readonly style=" color:#999" name="txt-5-5" placeholder="0" value="<?php echo $SDp10; ?>"/><input type="hidden" id="Cx-5" value=".10" /></div></div>
            <div style="height:23px"><div class="misdenolabel">0.05 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-6-5" readonly style=" color:#999" name="txt-6-5" placeholder="0" value="<?php echo $SDp05; ?>"/><input type="hidden" id="Cx-6" value=".05" /></div></div>
            <div style="height:23px; color:#da251d"><div class="misdenolabel"><b>TOTAL COINS</b> </div><div class="misdenoholder"><input class="misdenomination" readonly type="text" id="txt-7-5" name="txt-7-5" value="<?php echo number_format($TotalCoins,2); ?>" style="text-align:right; color:#999"/></div></div>
        </div>
        <!--<div class="misdenocol" style="width:300px; border-top:#999 solid thin; display:inline-block; float:left">
        	<div style="height:23px;"><div class="misdenolabel" style="width:150px; font-weight:bold; color:#da251d">OPENING CHANGE FUND</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-8-4" name="txt-8-4" value="500.00"/></div>
    		<div style="height:23px;"><div class="misdenolabel" style="width:150px; font-weight:bold; color:#da251d">CLOSING CHANGE FUND</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-9-4" name="txt-9-4" value="500.00"/></div>
        </div>-->
        <div class="misdenocol" style="width:auto; margin-left: 50px;"> <!-- "width:auto; border-left:#999 solid thin;" -->
        	<div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold"  >OPENING CHANGE FUND</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right;<?php if(date('d', strtotime($ICDate)) === '01') {} else { echo 'color:#999 '; }?>" type="text" id="txt-8-5" name="txt-8-5" placeholder="0.00" value="<?php echo number_format($SDBegBalance,2,'.',','); ?>" <?php if(date('d', strtotime($ICDate)) === '01') {} else { echo 'readonly '; }?> /></div></div>
            <div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">TOTAL SALES</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" readonly type="text" id="txt-9-5" name="txt-9-5" placeholder="0.00"  value="<?php echo  number_format($SDTotalSales,2,'.',','); ?>"/></div></div>
            <div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">PETTY CASH</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" readonly type="text" id="txt-11-5"  name="txt-11-5" placeholder="0.00"  value="<?php echo   number_format($TotalPettyCash,2,'.',','); ?>"/></div></div>
            <div style="height:23px; width:400px; max-width:400px"><div class="misdenolabel" style="width:150px; font-weight:bold">SPECIAL DISCOUNT</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" readonly type="text" id="txt-12-5" name="txt-12-5" placeholder="0.00" value="<?php echo  number_format($SDSpecialDisc,2,'.',','); ?>"/></div>
            	<div style="width:120px; max-width:120px; height:auto; float:right; padding-bottom:23px;">
                	<div class="misdenolabel" style="width:30px; font-weight:bold">GC</div><div class="misdenoholder"><input class="misdenomination" style="max-width:60px;text-align:right;" type="text" id="txt-14-5" name="txt-14-5" placeholder="0.00" value="<?php echo  number_format($SDGC,2,'.',','); ?>"/></div>
                	<div class="misdenolabel" style="width:30px; font-weight:bold">SC</div><div class="misdenoholder"><input class="misdenomination" style="max-width:60px;text-align:right;" type="text" id="txt-15-5" name="txt-15-5" placeholder="0.00" value="<?php echo  number_format($SDSC,2,'.',','); ?>"/></div>
                	<div class="misdenolabel" style="width:30px; font-weight:bold">Bulk</div><div class="misdenoholder"><input class="misdenomination" style="max-width:60px;text-align:right;" type="text" id="txt-16-5" name="txt-16-5" placeholder="0.00" value="<?php echo  number_format($SDBulk,2,'.',','); ?>"/></div>
            	</div>
            </div>
            <div style="height:46px; width:400px"></div>
            <div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">TOTAL CASH</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" readonly type="text" id="txt-17-5" name="txt-17-5" placeholder="0.00" value="<?php echo  number_format($SDTotalCashCount,2,'.',','); ?>"/></div></div>
            <div style="height:23px; display: none"><div class="misdenolabel" style="width:150px; font-weight:bold">TOTAL DEPOSIT</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-10-5" name="txt-10-5" placeholder="0.00" value="<?php echo  number_format($SDTotalDeposit,2,'.',','); ?>"/></div></div>
            <div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">CASH ON BANK</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-19-5" name="txt-19-5" placeholder="0.00" value="<?php echo  number_format($SDCashOnBank,2,'.',','); ?>"/></div></div>
            <div style="height:23px;"><div class="misdenolabel" style="width:150px; font-weight:bold;">ACTUAL CHANGE FUND</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-13-5" name="txt-13-5" placeholder="500.00" readonly value="<?php echo  number_format($SDEndBalance,2,'.',','); ?>"/></div></div>
            <div style="height:23px;"><div class="misdenolabel" style="width:150px; font-weight:bold">DECLARED CHANGE FUND</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-18-5" name="txt-18-5" placeholder="0.00" value="<?php echo  number_format($SDDEndBalance,2,'.',','); ?>"/></div>
            </div>
            <!--<div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">CASH COUNT VARIANCE</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" readonly type="text" id="txt-18-5" name="txt-18-5" placeholder="0.00" value="<?php echo $SDShortCashFund; ?>"/></div></div>
            <div style="height:23px; width:400px"></div>      
            <div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">DEPOSIT VARIANCE</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" readonly type="text" id="txt-20-5" name="txt-20-5" placeholder="0.00" value="<?php echo $SDShortCashDeposit; ?>"/></div></div>-->
		</div>
        <div class="misdenocol" style="width:auto; border-left:#999 solid thin; margin-left:1%; padding-bottom: 25px">
        	<div style="height:23px">
        		<div class="misdenolabel" style="width:120px; font-weight:bold; margin-left:150px; text-align: center">VARIANCE</div>
        		<div class="misdenolabel" style="width:120px; font-weight:bold;margin-left: 20px; text-align: center">ADJUSTMENT</div>
        		<div class="misdenolabel" style="width:120px; font-weight:bold;margin-left: 20px; text-align: center">CHARGE</div>
			</div>
        	<div style="height:23px">
          		<div class="misdenolabel" style="width:150px; font-weight:bold;">MIX</div>
          		<div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-21-5" name="txt-21-5" placeholder="0" readonly value="<?php if($SDShortPMix<=0){echo  number_format($SDShortPMix,2,'.',',');}else{echo  number_format($SDShortPMix,2,'.',',');}  ?>"/></div>
           		<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-adjpmix" name="txt-adjpmix" placeholder="0" value="<?php if($SDAdjPMix<=0){echo  number_format($SDAdjPMix,2,'.',',');}else{echo  number_format($SDAdjPMix,2,'.',',');}  ?>"/></div>
           		<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-chargepmix" name="txt-chargepmix" placeholder="0" readonly value="<?php if($SDChargePMix<=0){echo  "0.00";}else{echo  number_format($SDChargePMix,2,'.',',');}  ?>"/></div>
           	</div>
<!--
            <div style="height:23px">
            	<div class="misdenolabel" style="width:150px; font-weight:bold;color:">WATER</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-22-5" name="txt-22-5" placeholder="0" readonly value="<?php if($SDShortWater<=0){echo '0.00';}else{echo  number_format($SDShortWater,2,'.',',');} ?>"/></div>
            	<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-adjwater" name="txt-adjwater" placeholder="0" value="<?php if($SDAdjPWater<=0){echo  number_format($SDAdjPWater,2,'.',',');}else{echo  number_format($SDAdjPWater,2,'.',',');}  ?>"/></div>
           		<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-chargewater" name="txt-chargewater" placeholder="0" readonly value="<?php if($SDChargeWater<=0){echo  "0.00";}else{echo  number_format($SDChargeWater,2,'.',',');}  ?>"/></div>
            </div>
            <div style="height:23px">
            	<div class="misdenolabel" style="width:150px; font-weight:bold;">SUGAR</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-23-5" name="txt-23-5" placeholder="0" readonly value="<?php if($SDShortSugar<=0){echo '0.00';}else{echo  number_format($SDShortSugar,2,'.',',');} ?>"/></div>
            	<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-adjsugar" name="txt-adjsugar" placeholder="0" value="<?php if($SDAdjSugar<=0){echo  number_format($SDAdjSugar,2,'.',',');}else{echo  number_format($SDAdjSugar,2,'.',',');}  ?>"/></div>
           		<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-chargesugar" name="txt-chargesugar" placeholder="0" readonly value="<?php if($SDChargeSugar<=0){echo  "0.00";}else{echo  number_format($SDChargeSugar,2,'.',',');}  ?>"/></div>
            </div>
            <div style="height:23px;">
            	<div class="misdenolabel" style="width:150px; font-weight:bold;">OTHER SUPPLIES</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-24-5" name="txt-24-5" placeholder="0" readonly value="<?php if($SDOtherSupplies<=0){echo  number_format($SDOtherSupplies,2,'.',',');}else{ echo  number_format($SDOtherSupplies,2,'.',',');} ?>"/></div>
            	<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-adjsupplies" name="txt-adjsupplies" placeholder="0" value="<?php if($SDAdjSupplies<=0){echo  number_format($SDAdjSupplies,2,'.',',');}else{echo  number_format($SDAdjSupplies,2,'.',',');}  ?>"/></div>
           		<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-chargesupplies" name="txt-chargesupplies" placeholder="0" readonly value="<?php if($SDChargeSupplies<=0){echo  "0.00";}else{echo  number_format($SDChargeSupplies,2,'.',',');}  ?>"/></div>
            </div>
-->
            <div style="height:46px; width:400px; padding-bottom: 70px; padding-top: 25px">
            	<div style="width:250x; max-width:250x; height:auto; float:right;">
               		<div class="misdenolabel" style="width:30px; font-weight:bold;">__</div>
               		<div class="misdenoholder" style="border:none;width:200px; max-width:200px">
               			<input class="misdenomination" style="width:40px; font-size: 10px; font-weight: bold; max-width:40px;text-align:center;" type="text" placeholder="0.00" value="QTY." readonly/> x  
               			<input class="misdenomination" style="width:40px; font-size: 10px; font-weight: bold; max-width:40px;text-align:center;" type="text" placeholder="0.00" value="CHARGE" readonly/> = 
               			<input class="misdenomination" style="width:60px; font-size: 10px; font-weight: bold; max-width:60px;text-align:center;" type="text" placeholder="0.00" value="AMOUNT" readonly/>
               		</div>
            	</div>
            	<div style="width:250x; max-width:250x; height:auto; float:right;">
               		<div class="misdenolabel" style="width:30px; font-weight:bold;">GP</div>
               		<div class="misdenoholder" style="border:none;width:200px; max-width:200px">
               			<input class="misdenomination" style="width:40px; max-width:40px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo floor($GPVar); ?>" readonly/> x  
               			<input class="misdenomination" style="width:40px; max-width:40px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo number_format($GPCharge,2,'.',','); ?>" readonly/> = 
               			<input class="misdenomination" style="width:60px; max-width:60px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo number_format($GPVar*$GPCharge,2,'.',','); ?>" readonly/>
               		</div>
            	</div>
            	<div style="width:250x; max-width:250x; height:auto; float:right;">
               		<div class="misdenolabel" style="width:30px; font-weight:bold;">CC</div>
               		<div class="misdenoholder" style="border:none;width:200px; max-width:200px">
               			<input class="misdenomination" style="width:40px; max-width:40px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo floor($CCVar); ?>" readonly/> x 
               			<input class="misdenomination" style="width:40px; max-width:40px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo number_format($CCCharge,2,'.',','); ?>" readonly/> = 
               			<input class="misdenomination" style="width:60px; max-width:60px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo number_format($CCVar*$CCCharge,2,'.',','); ?>" readonly/>
               		</div>
            	</div>
            	<div style="width:250x; max-width:250x; height:auto; float:right;">
               		<div class="misdenolabel" style="width:30px; font-weight:bold;">CCL</div>
               		<div class="misdenoholder" style="border:none;width:200px; max-width:200px">
               			<input class="misdenomination" style="width:40px; max-width:40px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo floor($CCLVar); ?>" readonly/> x 
               			<input class="misdenomination" style="width:40px; max-width:40px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo number_format($CCLCharge,2,'.',','); ?>" readonly/> = 
               			<input class="misdenomination" style="width:60px; max-width:60px;text-align:center;color:#999; border-bottom: solid thin #000" type="text" placeholder="0.00" value="<?php echo number_format($CCLVar*$CCLCharge,2,'.',','); ?>" readonly/>
               		</div>
            	</div>
            </div>
            <div style="height:23px; padding-top:30px">
            	<div class="misdenolabel" style="width:150px; font-weight:bold;">CASH FUND</div><div class="misdenoholder"><input class="misdenomination" readonly style="max-width:120px;text-align:right; color:#999" type="text" id="txt-25-5" name="txt-25-5" placeholder="0" readonly value="<?php if($SDShortCashFund<=0){echo  number_format($SDShortCashFund,2,'.',',');}else{echo  number_format($SDShortCashFund,2,'.',',');} ?>"/></div>
            	<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right;" type="text" id="txt-adjcashfund" name="txt-adjcashfund" placeholder="0" value="<?php if($SDAdjCashFund<=0){echo  number_format($SDAdjCashFund,2,'.',',');}else{echo  number_format($SDAdjCashFund,2,'.',',');}  ?>"/></div>
           		<div class="misdenoholder" style="margin-left: 20px;"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-chargecashfund" name="txt-chargecashfund" placeholder="0" readonly value="<?php if($SDChargeCashFund<=0){echo  "0.00";}else{echo  number_format($SDChargeCashFund,2,'.',',');}  ?>"/></div>
            </div>
            <div style="height:23px; display: none"><div class="misdenolabel" style="width:150px; font-weight:bold;">DEPOSIT VARIANCE </div><div class="misdenoholder"><input class="misdenomination" readonly style="max-width:120px;text-align:right; color:#999" type="text" id="txt-26-5" name="txt-26-5" placeholder="0" readonly value="<?php if($SDShortCashDeposit<=0){echo  number_format($SDShortCashDeposit,2,'.',',');}else{echo  number_format($SDShortCashDeposit,2,'.',',');} ?>"/></div></div>
            <div style="height:23px; color:#da251d;display: none"><div class="misdenolabel" style="width:150px;font-weight:bold"><b>TOTAL VARIANCE</b> </div><div class="misdenoholder"><input class="misdenomination" readonly style="max-width:120px;text-align:right; color:#999" type="text" id="txt-27-5" name="txt-27-5" value="<?php if($SDShortTotal<=0){echo '0.00';}else{echo  number_format($SDShortTotal,2,'.',',');} ?>" style="text-align:right; color:#999"/></div></div>
        </div>
        <!--<div class="misdenocol" style="width:auto; padding-left: 0px">
        	<div style="height:23px"><div class="misdenolabel" style="width:100px; font-weight:bold;">MIX ADJUSTMENT</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;text-align:right; color:#999" type="text" id="txt-21-5" name="txt-21-5" placeholder="0" readonly value="<?php if($SDShortPMix<=0){echo  number_format($SDShortPMix,2,'.',',');}else{echo  number_format($SDShortPMix,2,'.',',');}  ?>"/></div></div>
		</div>-->
    <div class="group"></div>
    <div class="misdenocol" style="width:auto;margin-left:5%;">
         <table id="crewonduty">
         	<tr>
                <th class="codhead" style="width:200px; margin:0;"></th>
                <th class="codhead" style="width:100px; margin:0;"></th>
                <th class="codhead" style="width:100px; margin:0;"></th>
                <th class="codhead" style="width:150px; border:thin #000 solid; margin:0;" colspan="4">DAILY TIME RECORD</th>
                <th class="codhead" style="width:150px; margin:0;"></th>
             </tr>
             <tr>
                <th class="codhead" style="width:200px; margin:0;">PERSONNEL ON DUTY</th>
                <th class="codhead" style="width:100px; margin:0;"></th>
                <th class="codhead" style="width:100px; margin:0;"></th>
                <th class="codhead" style="width:150px; border:thin #000 solid; margin:0;">IN</th>
                <th class="codhead" style="width:150px; border:thin #000 solid; margin:0;">OUT</th>
                <th class="codhead" style="width:150px; border:thin #000 solid; margin:0;">IN</th>
                <th class="codhead" style="width:150px; border:thin #000 solid; margin:0;">OUT</th>
                <th class="codhead" style="width:150px; border:thin #000 solid; margin:0;"> HRS </th>
             </tr>
             <?php
			 	for($i=1;$i<9;$i++){
					$ifExists=ExecuteReader("Select unSalesCrew as `result` From salescrew Where unSalesCrew=".$i." AND `unBranch`=".$_GET['bid']." AND `unInventoryControl`=".$_GET['did']."");
					
					if($ifExists>0){
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmtcheck=$mysqli->stmt_init();
						if($stmtcheck->prepare("Select unSalesData, unEmployee, SCCode1, SCCode2, SCInAM, SCOutAM, SCInPM, SCOutPM, SCHours From salescrew Where unSalesCrew=? AND `unBranch`=? AND `unInventoryControl`=?")){
							$stmtcheck->bind_param('iii',$ifExists,$_SESSION['bid'],$_SESSION['did']);
							$stmtcheck->execute();
							$stmtcheck->bind_result($unSalesData,$SCEmployee,$SCCode1,$SCCode2,$SCInAM,$SCOutAM,$SCInPM,$SCOutPM,$SCHours);
							$stmtcheck->fetch();
							$stmtcheck->close();
							//die("Select ".$unSalesData.", ".$SCEmployee.", ".$SCCode1.", SCCode2, SCInAM, SCOutAM, SCInPM, SCOutPM, SCHours From salescrew Where unSalesCrew=".$ifExists." AND `unBranch`=".$_SESSION['bid']." AND `unInventoryControl`=".$_SESSION['did']);
						}
						?>
                        	<tr>
                            	<td class="codbod"> 
                                	<select class="crewOD codtxt" style="width:200px; margin-left:5px;" id="cod-0-<?php echo $ifExists; ?>" name="cod-0-<?php echo $ifExists; ?>" required >
                                    	 <option value="0">-----------</option>
                                         <?php 
                                         $stmt=$mysqli->stmt_init();
                                         if($stmt->prepare("SELECT employee.unEmployee,ELastName,EFirstName,EMiddleName FROM employee INNER JOIN employeearea ON employee.unEmployee=employeearea.unEmployee WHERE employee.Status=1 AND employeearea.Status=1 AND unArea=? ORDER BY ELastName")){
                                         	$stmt->bind_param('i',$_SESSION['area']);
                                            $stmt->execute();
                                         	$stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName);
                                         	while($stmt->fetch()){
                                         	?>
                                            	<option value="<?php echo $unEmployee; ?>"<?php echo ($unEmployee==$SCEmployee)?'Selected':''; ?> ><?php echo $ELastName.", ".$EFirstName." ".$EMiddleName ; ?></option>
                                            <?php
                                            }
                                            $stmt->close();
                                         }
                                         ?>
                                  	</select>
                                    </td>
                                    <td class="codbod" style="width:100px" align="center"><!--<input class="codcode codtxt" type="text" id="cod-1-<?php echo $i; ?>" name="cod-1-<?php echo $i; ?>" value="<?php echo $SCCode1; ?>"/>-->
                                    <select class="codcode codtxt" type="text" id="cod-1-<?php echo $i; ?>" name="cod-1-<?php echo $i; ?>">
	<?php /*
										<option value="0" style="text-align: center">-----------</option>
										<option value="SC" style="text-align: center" <?php echo (strtoupper($SCCode1)=='SC')?'Selected':''; ?>>SC</option>
										<option value="C" style="text-align: center" <?php echo (strtoupper($SCCode1)=='C')?'Selected':''; ?>>C</option>
										<option value="CSC" style="text-align: center" <?php echo (strtoupper($SCCode1)=='CSC')?'Selected':''; ?>>CSC</option>
										<option value="SP" style="text-align: center" <?php echo (strtoupper($SCCode1)=='SP')?'Selected':''; ?>>SP</option>
										<option value="T" style="text-align: center" <?php echo (strtoupper($SCCode1)=='T')?'Selected':''; ?>>T</option>
										<option value="F" style="text-align: center" <?php echo (strtoupper($SCCode1)=='F')?'Selected':''; ?>>F</option>
	*/ ?>
										<option value="0" style="text-align: center">-----------</option>
										<option value="CSC" style="text-align: center" <?php echo (strtoupper($SCCode1)=='CSC')?'Selected':''; ?>>CSC</option>
										<option value="SP" style="text-align: center" <?php echo (strtoupper($SCCode1)=='SP')?'Selected':''; ?>>SP</option>
										<option value="T" style="text-align: center" <?php echo (strtoupper($SCCode1)=='T')?'Selected':''; ?>>T</option>
										<option value="F" style="text-align: center" <?php echo (strtoupper($SCCode1)=='F')?'Selected':''; ?>>F</option>
									</select></td>
                                    <td class="codbod" style="width:100px" align="center"><!--<input class="codcode codtxt" type="text" id="cod-2-<?php echo $i; ?>" name="cod-2-<?php echo $i; ?>" value="<?php echo $SCCode2; ?>"/>-->
                                    <select class="codcode codtxt" type="text" id="cod-2-<?php echo $i; ?>" disabled name="cod-2-<?php echo $i; ?>">
<?php /*	
										<option value="0" style="text-align: center">-----------</option>
										<option value="SC" style="text-align: center"> <?php echo (strtoupper($SCCode2)=='SC')?'Selected':''; ?>SC</option>
										<option value="C" style="text-align: center" <?php echo (strtoupper($SCCode2)=='C')?'Selected':''; ?>>C</option>
										<option value="CSC" style="text-align: center" <?php echo (strtoupper($SCCode2)=='CSC')?'Selected':''; ?>>CSC</option>
										<option value="T" style="text-align: center" <?php echo (strtoupper($SCCode2)=='T')?'Selected':''; ?>>T</option>
										<option value="F" style="text-align: center" <?php echo (strtoupper($SCCode2)=='F')?'Selected':''; ?>>F</option>
	*/ ?>
										<option value="0" style="text-align: center">-----------</option>
										<option value="CSC" style="text-align: center" <?php echo (strtoupper($SCCode1)=='CSC')?'Selected':''; ?>>CSC</option>
										<option value="SP" style="text-align: center" <?php echo (strtoupper($SCCode1)=='SP')?'Selected':''; ?>>SP</option>
										<option value="T" style="text-align: center" <?php echo (strtoupper($SCCode1)=='T')?'Selected':''; ?>>T</option>
										<option value="F" style="text-align: center" <?php echo (strtoupper($SCCode1)=='F')?'Selected':''; ?>>F</option>
									</select></td>
                                    <td class="codbod" style="width:150px"><input class="codhrs codtxt" disabled type="time" id="cod-3-<?php echo $i; ?>" name="cod-3-<?php echo $i; ?>" value="<?php echo $SCInAM; ?>"/></td>
                                    <td class="codbod" style="width:150px"><input class="codhrs codtxt" disabled type="time" id="cod-4-<?php echo $i; ?>" name="cod-4-<?php echo $i; ?>" value="<?php echo $SCOutAM; ?>"/></td>
                                    <td class="codbod" style="width:150px"><input class="codhrs codtxt" disabled type="time" id="cod-5-<?php echo $i; ?>" name="cod-5-<?php echo $i; ?>" value="<?php echo $SCInPM; ?>"/></td>
                                    <td class="codbod" style="width:150px"><input class="codhrs codtxt" disabled type="time" id="cod-6-<?php echo $i; ?>" name="cod-6-<?php echo $i; ?>" value="<?php echo $SCOutPM; ?>"/></td>
                                    <td class="codbod" style="width:150px"><input class="codhrsdiff codtxt" type="number"  id="cod-7-<?php echo $i; ?>" name="cod-7-<?php echo $i; ?>" value="<?php echo $SCHours; ?>"/></td>      
                                </tr>
          				<?php
					}else{
						?>
                        <tr>
                            <td class="codbod"> 
                                <select class="crewOD codtxt" style="width:200px; margin-left:5px;" id="cod-0-<?php echo $i; ?>" name="cod-0-<?php echo $i; ?>" required >
                                    <option value="0">-----------</option>
                                    <?php 
										$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
										$stmt=$mysqli->stmt_init();
										if($stmt->prepare("SELECT employee.unEmployee,ELastName,EFirstName,EMiddleName,unArea FROM employee INNER JOIN employeearea ON employee.unEmployee=employeearea.unEmployee WHERE employee.Status=1 AND employeearea.Status=1 AND unArea=? ORDER BY ELastName")){
											$stmt->bind_param('i',$_SESSION['area']);
											$stmt->execute();
											$stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName,$unEA);
											while($stmt->fetch()){
											?>
												<option value="<?php echo $unEmployee; ?>" ><?php echo $ELastName.", ".$EFirstName." ".$EMiddleName ; ?></option>
											<?php
											}
											$stmt->close();
										}
									 ?>
                                </select>
                            </td>
                            <td class="codbod" style="width:100px" align="center"><!--<input class="codcode codtxt" type="text" id="cod-1-<?php echo $i; ?>" name="cod-1-<?php echo $i; ?>"/>-->
                            <select class="codcode codtxt" type="text" id="cod-1-<?php echo $i; ?>" name="cod-1-<?php echo $i; ?>">
                            	<option value="0" style="text-align: center">-----------</option>
<!--
                            	<option value="SC" style="text-align: center">SC</option>
                            	<option value="C" style="text-align: center">C</option>
-->
                            	<option value="CSC" style="text-align: center">CSC</option>
                            	<option value="SP" style="text-align: center">SP</option>
                            	<option value="T" style="text-align: center">T</option>
                            	<option value="F" style="text-align: center">F</option>
                            </select></td>
                            <td class="codbod" style="width:100px" align="center"><!--<input class="codcode codtxt" type="text" id="cod-2-<?php echo $i; ?>" name="cod-2-<?php echo $i; ?>"/>-->
                            <select class="codcode codtxt" type="text" id="cod-2-<?php echo $i; ?>" disabled name="cod-2-<?php echo $i; ?>">
                            	<option value="0" style="text-align: center">-----------</option>
<!--
                            	<option value="SC" style="text-align: center">SC</option>
                            	<option value="C" style="text-align: center">C</option>
-->
                            	<option value="CSC" style="text-align: center">CSC</option>
                            	<option value="T" style="text-align: center">T</option>
                            	<option value="F" style="text-align: center">F</option>
                            </select></td>
                            </td>
                            <td class="codbod" style="width:150px"><input class="codhrs codtxt" disabled type="time" id="cod-3-<?php echo $i; ?>" name="cod-3-<?php echo $i; ?>"/></td>
                            <td class="codbod" style="width:150px"><input class="codhrs codtxt" disabled type="time" id="cod-4-<?php echo $i; ?>" name="cod-4-<?php echo $i; ?>"/></td>
                            <td class="codbod" style="width:150px"><input class="codhrs codtxt" disabled type="time" id="cod-5-<?php echo $i; ?>" name="cod-5-<?php echo $i; ?>"/></td>
                            <td class="codbod" style="width:150px"><input class="codhrs codtxt" disabled type="time" id="cod-6-<?php echo $i; ?>" name="cod-6-<?php echo $i; ?>"/></td>
                            <td class="codbod" style="width:150px"><input class="codhrsdiff codtxt" type="number"  id="cod-7-<?php echo $i; ?>" name="cod-7-<?php echo $i; ?>"/></td>      
                         </tr>
                        <?php
					}
				}
			 ?>	
     	</table>
        <table style="margin-bottom:30px">
        	<tr>
            	<th>**CODE</th>
                <th></th>
           	</tr>
<!--
            	<td>SC</td>
                <td> - Service Crew</td>
            <tr>
            </tr>
            	<td>C</td>
                <td> - Cashier</td>
            <tr>
            </tr>
-->
            <tr>
            	<td>CSC</td>
                <td> - Cashier & Service Crew</td>
            </tr>
            <tr>
            	<td>SP</td>
                <td> - Supervisor</td>
            </tr>
            <tr>
            	<td>T</td>
                <td> - Trainee</td>
            </tr>
            <tr>
            	<td>F</td>
                <td> - Franchise</td>
            </tr>
        </table>
     </div>
</div>
</form>


<?php require 'footer.php'; ?>

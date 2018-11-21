<?php 
require 'reportsheader.php';
function PMixPeriod(){
	$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select Concat(MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate),' - ',ICNumber) as `ICPeriod` from inventorycontrol Inner Join branch On inventorycontrol.unBranch=branch.unBranch Where unInventoryControl=?")){
		$stmt->bind_param('i',$_GET['did']);
		$stmt->execute();
		$stmt->bind_result($ICPeriod);
		$stmt->fetch();
		$stmt->close();
	}
	return $ICPeriod;
}
function ShowDateFilter(){
	$datefrom = date_create($_GET['dfrom']);
	$dateto = date_create($_GET['dto']);
	return date_format($datefrom,'F d, Y').' - '.date_format($dateto,'F d, Y');
} 
?>
<script type="text/javascript">
function FilterReport(dFrom,dTo){
	if(dFrom == '' || dTo == ''){
		msgbox('From and To cannot contain a null value. Select date.','','');
		return false;
	}
	if(dTo < dFrom){
		msgbox('Date To cannot be earlier than Date From.','','');
		return false;
	}
	redirect('<?php echo $_SERVER['PHP_SELF'].'?&bid='.$_GET['bid'].'&did='.$_GET['did'].'&type=1'; ?>&filter=1&dfrom='+dFrom+'&dto='+dTo);
}
</script>

<div id="toolbar" style="width:inherit; margin:auto;">
	<input class="exemptPrint" type="text" value="From" style="width:35px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="date" id="dtpFrom" name="dtpFrom" value="<?php echo (isset($_GET['dfrom']))?$_GET['dfrom']:'';?>" >
    <input class="exemptPrint" type="text" value="To" style="width:20px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="date" id="dtpTo" name="dtpTo" value="<?php echo (isset($_GET['dto']))?$_GET['dto']:'';?>" >
    <input class="exemptPrint" type="button" value="Go" onClick="FilterReport(dtpFrom.value,dtpTo.value)">
	<input type="text" style="border:none; background-color:transparent; float:right; margin-top:5px; margin-right:5px; text-align:right; width:300px;" value="<?php echo (isset($_GET['dfrom']))?ShowDateFilter():PMixPeriod();?>" readonly>
</div>

<div class="rptlistview" style="width:100%;">
	<div class="rptcolumn">
    	<div class="rptcolumnheader" style="width:204px;">Doc Number</div>
        <div class="rptcolumnheader" style="width:204px;">Date</div>
    </div>
    <div class="rptrow">
    	<?php
		$i=0;
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if(isset($_GET['filter'])){
			if($stmt->prepare("Select Concat(MonthName(`DCDate`), ' ',DayOfMonth(DCDate),', ', Year(DCDate)) as DCDate,DCDocNum
								From deliverycontrol
								Inner Join inventorycontrol On deliverycontrol.unInventoryControl = inventorycontrol.unInventoryControl
								Where unBranch = ? and (ICDate Between ? and ?)")){
				$stmt->bind_param('iss',$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
				$stmt->execute();
				$stmt->bind_result($DCDate,$DCDocNum);
				while($stmt->fetch()){
					?>
                    	<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                        	<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="width:inherit; background-color:transparent; border:none;" value="<?php echo $DCDocNum; ?>"></div>
                        	<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="width:inherit; background-color:transparent; border:none;" value="<?php echo $DCDate; ?>"></div>                            
                        </div>
					<?php
					$i++;
				}
				$stmt->close();
			}
		}else{
			if($stmt->prepare("Select Concat(MonthName(`DCDate`), ' ',DayOfMonth(DCDate),', ', Year(DCDate)) as DCDate,DCDocNum
								From deliverycontrol
								Where unInventoryControl = ?")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($DCDate,$DCDocNum);
				while($stmt->fetch()){
					?>
                    	<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                        	<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="width:inherit; background-color:transparent; border:none;" value="<?php echo $DCDocNum; ?>"></div>
                        	<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="width:inherit; background-color:transparent; border:none;" value="<?php echo $DCDate; ?>"></div>                            
                        </div>
					<?php
					$i++;
				}
				$stmt->close();
			}
		}
		$mysqli->close();
		?>
    </div>
</div>

<div class="rptlistview" style="width:100%;">
	<div class="rptcolumn">
    	<div class="rptcolumnheader" style="width:404px;">Description</div>
        <div class="rptcolumnheader" style="width:104px;">Unit</div>
        <div class="rptcolumnheader" style="width:84px; text-align:right;">Quantity</div>
    </div>
    <div class="rptrow">
    	<?php
		$OldPTName='';
		$OldPGName='';
		$i=0;
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if(isset($_GET['filter'])){
			if($stmt->prepare("Select PIName,
								(
									Select Sum(DDQuantity) From deliverydata 
									Inner Join deliverycontrol On deliverydata.unDeliveryControl = deliverycontrol.unDeliveryControl
									Inner Join inventorycontrol On deliverycontrol.unInventoryControl = inventorycontrol.unInventoryControl
									Where unBranch = ? and (ICDate Between ? and ?) and unProductItem = src.unProductItem and unProductUOM = src.unProductUOM and deliverydata.Status = 1 and deliverycontrol.Status = 1
								) as `Quantity`,
								(
									Select PUOMName From productuom Where unProductUOM = src.unProductUOM 
								) as `PUOMName`,
								(
									Select PTName From productgroup	
									Inner Join producttype On productgroup.unProductType = producttype.unProductType
									Where unProductGroup = (Select unProductGroup From productitem Where unProductItem = src.unProductItem)
								) as `PTName`,
								(
									Select PGName From productitem	
									Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
									Where unProductItem = src.unProductItem
								) as `PGName`
								From deliverydata as `src`
								Inner Join productitem On src.unProductItem = productitem.unProductItem
								Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
								Inner Join templateitemdata On productitem.unProductItem = templateitemdata.unProductItem 
								Inner Join deliverycontrol On src.unDeliveryControl = deliverycontrol.unDeliveryControl
								Inner Join inventorycontrol On deliverycontrol.unInventoryControl = inventorycontrol.unInventoryControl
								Where unBranch = ? and (ICDate Between ? and ?)
								Group by PIName,src.unProductUOM
								Order by PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('ississ',$_GET['bid'],$_GET['dfrom'],$_GET['dto'],$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
				$stmt->execute();
				$stmt->bind_result($PIName,$Quantity,$PUOMName,$PTName,$PGName);
				while($stmt->fetch()){
					if($OldPTName!=$PTName){
					$OldPTName=$PTName;	
					?>
						<div class="rptgroup" style="text-transform:uppercase;"><?php echo $PTName; ?></div>
					<?php
					}
					if($OldPGName!=$PGName){
						$OldPGName=$PGName;	
						?>
							<div class="rptgroup"><?php echo $PGName; ?></div>
						<?php
					}
					?>
                    <div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                        <div class="rptlistviewsubitem" style="width:400px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PIName; ?>"></div>
                        <div class="rptlistviewsubitem" style="width:100px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PUOMName; ?>"></div>
                        <div class="rptlistviewsubitem" style="width:80px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit; text-align:right;" value="<?php echo $Quantity; ?>"></div>
                    </div>
                    <?php
                    $i++;
				}
				$stmt->close();
			}
		}else{
			if($stmt->prepare("Select PIName,DDQuantity as `Quantity`,
								(
									Select PUOMName From productuom Where unProductUOM = src.unProductUOM 
								) as `PUOMName`,
								(
									Select PTName From productgroup	
									Inner Join producttype On productgroup.unProductType = producttype.unProductType
									Where unProductGroup = (Select unProductGroup From productitem Where unProductItem = src.unProductItem)
								) as `PTName`,
								(
									Select PGName From productitem	
									Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
									Where unProductItem = src.unProductItem
								) as `PGName`
								From deliverydata as `src`
								Inner Join productitem On src.unProductItem = productitem.unProductItem
								Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
								Inner Join templateitemdata On productitem.unProductItem = templateitemdata.unProductItem 
								Inner Join deliverycontrol On src.unDeliveryControl = deliverycontrol.unDeliveryControl
								Where unInventoryControl = ?
								Order by PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($PIName,$Quantity,$PUOMName,$PTName,$PGName);
				while($stmt->fetch()){
					if($OldPTName!=$PTName){
					$OldPTName=$PTName;	
					?>
						<div class="rptgroup" style="text-transform:uppercase;"><?php echo $PTName; ?></div>
					<?php
					}
					if($OldPGName!=$PGName){
						$OldPGName=$PGName;	
						?>
							<div class="rptgroup"><?php echo $PGName; ?></div>
						<?php
					}
					?>
                    <div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                        <div class="rptlistviewsubitem" style="width:400px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PIName; ?>"></div>
                        <div class="rptlistviewsubitem" style="width:100px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PUOMName; ?>"></div>
                        <div class="rptlistviewsubitem" style="width:80px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit; text-align:right;" value="<?php echo $Quantity; ?>"></div>
                    </div>
                    <?php
                    $i++;
				}
				$stmt->close();
			}
		}
		$mysqli->close();
		?>
    </div>
</div>

<?php require 'footer.php'; ?>
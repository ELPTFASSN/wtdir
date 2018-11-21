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
<style>
input{
	font-family:calibri;
}
</style>
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
	redirect('<?php echo $_SERVER['PHP_SELF'].'?&bid='.$_GET['bid'].'&did='.$_GET['did'].'&type=1&ttype='.$_GET['ttype']; ?>&filter=1&dfrom='+dFrom+'&dto='+dTo);
}
function ChangeType(targ,selObj)
{
	var rep;
	var url = "<?php echo $_SERVER['REQUEST_URI']; ?>";
	url = url.split('?')[1];
	var type = url.replace('ttype='+<?php echo $_GET['ttype']; ?>,'ttype='+selObj.options[selObj.selectedIndex].value);
	eval(targ+".location='reportitf.php?"+type+"'");
}
</script>

<div id="toolbar" style="width:inherit; margin:auto;">
	<input class="exemptPrint" type="text" value="Type" style="width:25px; margin-left:5px; border:none; background-color:transparent;" readonly >
    <select class="exemptPrint" id="cmbTransferType" onChange="ChangeType('parent',this)">
    	<option value="1" <?php echo ($_GET['ttype']==1)?'Selected':'';?>>Transfer In</option>
    	<option value="0" <?php echo ($_GET['ttype']==0)?'Selected':'';?> >Transfer Out</option>
    </select>
	<input class="exemptPrint" type="text" value="From" style="width:35px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="date" id="dtpFrom" name="dtpFrom" value="<?php echo (isset($_GET['dfrom']))?$_GET['dfrom']:'';?>" >
    <input class="exemptPrint" type="text" value="To" style="width:20px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="date" id="dtpTo" name="dtpTo" value="<?php echo (isset($_GET['dto']))?$_GET['dto']:'';?>" >
    <input class="exemptPrint" type="button" value="Go" onClick="FilterReport(dtpFrom.value,dtpTo.value)">
	<input type="text" style="border:none; background-color:transparent; float:right; margin-top:5px; margin-right:5px; text-align:right; width:250px;" value="<?php echo (isset($_GET['dfrom']))?ShowDateFilter():PMixPeriod();?>" readonly>
</div>

<div class="rptlistview" style="width:100%;">
	<div class="rptcolumn">
    	<div class="rptcolumnheader" style="width:204px;">ITF Number</div>
    	<div class="rptcolumnheader" style="width:204px;"><?php echo ($_GET['ttype']==1)?'Branch From':'Branch To';?></div> 
        <div class="rptcolumnheader" style="width:204px;">Date</div>      
    </div>
    <div class="rptrow">
    	<?php
		$i=0;
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($_GET['ttype']==1){
			if(isset($_GET['filter'])){
				if($stmt->prepare("Select Concat(MonthName(`TCDate`), ' ',DayOfMonth(TCDate),', ', Year(TCDate)) as TCDate,TCNumber,
									(Select BName From branch Where unBranch = transfercontrol.unBranchFrom) as `Branch From`,
									(Select BName From branch Where unBranch = transfercontrol.unBranchTo) as `Branch To` 
									From transfercontrol
									Inner Join inventorycontrol On transfercontrol.unInventoryControlTo = inventorycontrol.unInventoryControl
									Where unBranchTo = ? and (ICDate Between ? and ?) and transfercontrol.`Status` = 1")){
					$stmt->bind_param('iss',$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
					$stmt->execute();
					$stmt->bind_result($TCDate,$TCNumber,$BranchFrom,$BranchTo);
					while($stmt->fetch()){
						?>
						<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
							<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $TCNumber; ?>"></div>
							<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $BranchFrom; ?>"></div>
                            <div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $TCDate; ?>"></div>
						</div>
						<?php
						$i++;
					}
				}
			}else{
				if($stmt->prepare("Select Concat(MonthName(`TCDate`), ' ',DayOfMonth(TCDate),', ', Year(TCDate)) as TCDate,TCNumber,
									(Select BName From branch Where unBranch = transfercontrol.unBranchFrom) as `Branch From`,
									(Select BName From branch Where unBranch = transfercontrol.unBranchTo) as `Branch To` 
									From transfercontrol
									Where unInventoryControlTo = ?")){
					$stmt->bind_param('i',$_GET['did']);
					$stmt->execute();
					$stmt->bind_result($TCDate,$TCNumber,$BranchFrom,$BranchTo);
					while($stmt->fetch()){
						?>
						<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
							<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $TCNumber; ?>"></div>
							<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $BranchFrom; ?>"></div>
                            <div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $TCDate; ?>"></div>
						</div>
						<?php
						$i++;
					}
					$stmt->close();
				}
			}
		}else{
			if(isset($_GET['filter'])){
				if($stmt->prepare("Select Concat(MonthName(`TCDate`), ' ',DayOfMonth(TCDate),', ', Year(TCDate)) as TCDate,TCNumber,
									(Select BName From branch Where unBranch = transfercontrol.unBranchFrom) as `Branch From`,
									(Select BName From branch Where unBranch = transfercontrol.unBranchTo) as `Branch To` 
									From transfercontrol
									Inner Join inventorycontrol On transfercontrol.unInventoryControlFrom = inventorycontrol.unInventoryControl
									Where unBranchFrom = ? and (ICDate Between ? and ?) and transfercontrol.`Status` = 1")){
					$stmt->bind_param('iss',$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
					$stmt->execute();
					$stmt->bind_result($TCDate,$TCNumber,$BranchFrom,$BranchTo);
					while($stmt->fetch()){
						?>
						<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
							<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $TCNumber; ?>"></div>
							<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $BranchTo; ?>"></div>
                            <div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $TCDate; ?>"></div>
						</div>
						<?php
						$i++;
					}
				}
			}else{
				if($stmt->prepare("Select Concat(MonthName(`TCDate`), ' ',DayOfMonth(TCDate),', ', Year(TCDate)) as TCDate,TCNumber,
									(Select BName From branch Where unBranch = transfercontrol.unBranchFrom) as `Branch From`,
									(Select BName From branch Where unBranch = transfercontrol.unBranchTo) as `Branch To` 
									From transfercontrol
									Where unInventoryControlFrom = ?")){
					$stmt->bind_param('i',$_GET['did']);
					$stmt->execute();
					$stmt->bind_result($TCDate,$TCNumber,$BranchFrom,$BranchTo);
					while($stmt->fetch()){
						?>
						<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
							<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $TCNumber; ?>"></div>
							<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $BranchTo; ?>"></div>
                            <div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $TCDate; ?>"></div>
						</div>
						<?php
						$i++;
					}
					$stmt->close();
				}
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
		if($_GET['ttype']==1){
			if(isset($_GET['filter'])){
				if($stmt->prepare("Select Distinct PIName,
									(	
										Select Sum(TDQuantity) From transferdata 
										Inner Join transfercontrol On transferdata.unTransferControl = transfercontrol.unTransferControl
										Inner Join inventorycontrol On transfercontrol.unInventoryControlTo = inventorycontrol.unInventoryControl
										Where unBranchTo = ? and (ICDate Between ? and ?) and transfercontrol.`Status` = 1 and unProductItem = src.unProductItem and transferdata.Status = 1
									) as `Quantity`,
									(
										Select PGName From productitem
										Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
										Where productitem.unProductItem = src.unProductItem
									) as `Group`,
									(
										Select PTName From productgroup
										Inner Join producttype On productgroup.unProductType = producttype.unProductType
										Inner Join productitem On productgroup.unProductGroup = productitem.unProductGroup
										Where productitem.unProductItem = src.unProductItem
									) as `Type`,
									(
										Select PUOMName From productuom Where unProductUOM = (Select unProductUOM From productitem Where unProductItem = src.unProductItem)
									) as `Unit`
									From transferdata as `src`
									Inner Join productitem On src.unProductItem = productitem.unProductItem
									Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
									Inner Join templateitemdata On productitem.unProductItem = templateitemdata.unProductItem
									Inner Join transfercontrol On src.unTransferControl = transfercontrol.unTransferControl
									Inner Join inventorycontrol On transfercontrol.unInventoryControlTo = inventorycontrol.unInventoryControl
									Where unBranchTo = ? and (ICDate Between ? and ?) and transfercontrol.`Status` = 1
									Order by PGPriority Asc, TIDPriority Asc")){
					$stmt->bind_param('ississ',$_GET['bid'],$_GET['dfrom'],$_GET['dto'],$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
					$stmt->execute();
					$stmt->bind_result($PName,$Quantity,$PGName,$PTName,$PUOMName);
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
							<div class="rptlistviewsubitem" style="width:400px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:100px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PUOMName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:80px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit; text-align:right;" value="<?php echo $Quantity; ?>"></div>
						</div>
						<?php
						$i++;
					}
					$stmt->close();
				}
			}else{
				if($stmt->prepare("Select PTName,PGName,PIName,PUOMName,TDQuantity 
								From transferdata
								Inner Join transfercontrol On transferdata.unTransferControl = transfercontrol.unTransferControl
								Inner Join productitem On transferdata.unProductItem = productitem.unProductItem
								Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
								Inner Join producttype On productgroup.unProductType = producttype.unProductType
								Inner Join templateitemdata On productitem.unProductItem = templateitemdata.unProductItem
								Inner Join productuom On productitem.unProductUOM = productuom.unProductUOM
								Where transfercontrol.unInventoryControlTo = ?
								Order by PGPriority Asc, TIDPriority Asc")){
					$stmt->bind_param('i',$_GET['did']);
					$stmt->execute();
					$stmt->bind_result($PTName,$PGName,$PName,$PUOMName,$Quantity);
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
							<div class="rptlistviewsubitem" style="width:400px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:100px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PUOMName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:80px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit; text-align:right;" value="<?php echo $Quantity; ?>"></div>
						</div>
						<?php
						$i++;
					}
					$stmt->close();
				}
			}
		}else{
			if(isset($_GET['filter'])){
				if($stmt->prepare("Select Distinct PIName,
									(Select Sum(TDQuantity) From transferdata Where unProductItem = src.unProductItem) as `Quantity`,
									(
										Select PGName From productitem
										Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
										Where productitem.unProductItem = src.unProductItem
									) as `Group`,
									(
										Select PTName From productgroup
										Inner Join producttype On productgroup.unProductType = producttype.unProductType
										Inner Join productitem On productgroup.unProductGroup = productitem.unProductGroup
										Where productitem.unProductItem = src.unProductItem
									) as `Type`,
									(
										Select PUOMName From productuom Where unProductUOM = (Select unProductUOM From productitem Where unProductItem = src.unProductItem)
									) as `Unit`
									From transferdata as `src`
									Inner Join productitem On src.unProductItem = productitem.unProductItem
									Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
									Inner Join templateitemdata On productitem.unProductItem = templateitemdata.unProductItem
									Inner Join transfercontrol On src.unTransferControl = transfercontrol.unTransferControl
									Inner Join inventorycontrol On transfercontrol.unInventoryControlFrom = inventorycontrol.unInventoryControl
									Where unBranchFrom = ? and (ICDate Between ? and ?) and transfercontrol.`Status` = 1
									Order by PGPriority Asc, TIDPriority Asc")){
					$stmt->bind_param('iss',$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
					$stmt->execute();
					$stmt->bind_result($PName,$Quantity,$PGName,$PTName,$PUOMName);
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
							<div class="rptlistviewsubitem" style="width:400px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:100px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PUOMName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:80px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit; text-align:right;" value="<?php echo $Quantity; ?>"></div>
						</div>
						<?php
						$i++;
					}
					$stmt->close();
				}
			}else{
				if($stmt->prepare("Select PTName,PGName,PIName,PUOMName,TDQuantity 
								From transferdata
								Inner Join transfercontrol On transferdata.unTransferControl = transfercontrol.unTransferControl
								Inner Join productitem On transferdata.unProductItem = productitem.unProductItem
								Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
								Inner Join producttype On productgroup.unProductType = producttype.unProductType
								Inner Join templateitemdata On productitem.unProductItem = templateitemdata.unProductItem
								Inner Join productuom On productitem.unProductUOM = productuom.unProductUOM
								Where transfercontrol.unInventoryControlFrom = ?
								Order by PGPriority Asc, TIDPriority Asc")){
					$stmt->bind_param('i',$_GET['did']);
					$stmt->execute();
					$stmt->bind_result($PTName,$PGName,$PName,$PUOMName,$Quantity);
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
							<div class="rptlistviewsubitem" style="width:400px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:100px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit;" value="<?php echo $PUOMName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:80px;"><input readonly type="text" style="border:none; background-color:transparent; width:inherit; text-align:right;" value="<?php echo $Quantity; ?>"></div>
						</div>
						<?php
						$i++;
					}
					$stmt->close();
				}
			}
		}
		$mysqli->close();
        ?>
    </div>
</div>

<?php require 'footer.php'; ?>
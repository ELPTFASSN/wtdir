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
		$mysqli->close();
		return $ICPeriod;
	}
	function ShowDateFilter(){
		$datefrom = date_create($_GET['dfrom']);
		$dateto = date_create($_GET['dto']);
		return date_format($datefrom,'F d, Y').' - '.date_format($dateto,'F d, Y');
	}
	function GetCOGS(){
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if(isset($_GET['filter'])){
			if($stmt->prepare("Select Sum(IDDIRUsage * TIDCost) as COGS From inventorydata
								Inner Join inventorycontrol On inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
								Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
								Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
								Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
								Where unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
								(productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Savory Fillings' and unProductType = 2) or 
								productitem.unProductGroup  = (Select unProductGroup From productgroup Where PGName = 'Sweet Fillings' and unProductType = 2) or 
								productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Fillins of the Month' and unProductType = 2) or 
								productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Beverages' and unProductType = 2) or 
								productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Hot Drinks' and unProductType = 2)) and
								(ICDate Between ? and ?) and unBranch = ?")){
				$stmt->bind_param('ssi',$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($COGS);
				$stmt->fetch();
				$stmt->close();
			}	
		}else{
			if($stmt->prepare("Select Sum(IDDIRUsage * TIDCost) as COGS From inventorydata
							Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
							Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
							Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
							Where unInventoryControl = ? and unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
							(productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Savory Fillings' and unProductType = 2) or 
							productitem.unProductGroup  = (Select unProductGroup From productgroup Where PGName = 'Sweet Fillings' and unProductType = 2) or 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Fillins of the Month' and unProductType = 2) or 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Beverages' and unProductType = 2) or 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Hot Drinks' and unProductType = 2))")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($COGS);
				$stmt->fetch();
				$stmt->close();
			}
		}
		
		$mysqli->close();
		return $COGS;
	}
	function GetPackagingSupplies(){
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if(isset($_GET['filter'])){
			if($stmt->prepare("Select Sum(IDDIRUsage * TIDCost) as PackagingSupplies From inventorydata
								Inner Join inventorycontrol On inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
								Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
								Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
								Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
								Where unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
								productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Packaging Supplies' and unProductType = 2) and
								(ICDate Between ? and ?) and unBranch = ?")){
				$stmt->bind_param('ssi',$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($PackagingSupplies);
				$stmt->fetch();
				$stmt->close();
			}
		}else{
			if($stmt->prepare("Select Sum(IDDIRUsage * TIDCost) as PackagingSupplies From inventorydata
							Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
							Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
							Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
							Where unInventoryControl = ? and unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Packaging Supplies' and unProductType = 2)")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($PackagingSupplies);
				$stmt->fetch();
				$stmt->close();
			}
		}
		$mysqli->close();
		return $PackagingSupplies;
	}
	function GetGeneralSupplies(){
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if(isset($_GET['filter'])){
			if($stmt->prepare("Select Sum(IDDIRUsage * TIDCost) as GeneralSupplies From inventorydata
								Inner Join inventorycontrol On inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
								Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
								Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
								Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
								Where unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
								productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'General Supplies' and unProductType = 2) and
								(ICDate Between ? and ?) and unBranch = ?")){
				$stmt->bind_param('ssi',$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($GeneralSupplies);
				$stmt->fetch();
				$stmt->close();
			}
		}else{
			if($stmt->prepare("Select Sum(IDDIRUsage * TIDCost) as GeneralSupplies From inventorydata
							Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
							Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
							Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
							Where unInventoryControl = ? and unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'General Supplies' and unProductType = 2)")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($GeneralSupplies);
				$stmt->fetch();
				$stmt->close();
			}
		}
		$mysqli->close();
		return $GeneralSupplies;
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

$(window).scroll(function() {

	<?php
	echo "columnheader('colCOS','lvCOS');";
	?>

});
</script>

<div id="toolbar" style="width:inherit; margin:auto;">
	<input class="exemptPrint" type="text" value="From" style="width:35px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="date" id="dtpFrom" name="dtpFrom" value="<?php echo (isset($_GET['dfrom']))?$_GET['dfrom']:'';?>" >
    <input class="exemptPrint" type="text" value="To" style="width:20px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="date" id="dtpTo" name="dtpTo" value="<?php echo (isset($_GET['dto']))?$_GET['dto']:'';?>" >
    <input class="exemptPrint" type="button" value="Go" onClick="FilterReport(dtpFrom.value,dtpTo.value)">
	<input type="text" style="border:none; background-color:transparent; float:right; margin-top:5px; margin-right:5px; text-align:right; width:300px;" value="<?php echo (isset($_GET['dfrom']))?ShowDateFilter():PMixPeriod();?>" readonly>
</div>

<div class="rptlistview" id="lvCOS" style="width:100%">
	<div class="rptcolumn" id="colCOS">
    	<div class="rptcolumnheader" style="width:404px;">Description</div>
        <div class="rptcolumnheader" style="width:104px; text-align:right;">Usage</div>
        <div class="rptcolumnheader" style="width:104px; text-align:right;">Amount</div>
    </div>
    <div class="rptrow">
    	<div class="rptgroup">COGS</div>
        <?php
		$TotalCOGS=0;
		$OldPGName='';
		$i=0;
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if(isset($_GET['filter'])){
			if($stmt->prepare("
								Select productitem.unProductItem as `pid`,PIName,PGName,
								(
									Select Sum(IDDIRUsage) From inventorydata 
									Inner Join inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl 
									Where inventorydata.unProductitem = pid and (ICDate Between ? and ?) and unBranch = ?
								) as `Usage`,
								(
									Select Sum(IDDIRUsage) From inventorydata 
									Inner Join inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl 
									Where inventorydata.unProductitem = pid and (ICDate Between ? and ?) and unBranch = ?
								) * TIDCost as `COGS` 
								From productitem
								Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
								Inner Join templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
								Where unProductType = (Select unProductType From producttype Where PTName = 'Rawmats' and `Status` = 1) and productitem.`Status` = 1 and templateitemdata.`Status` = 1 and
								(
									productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Savory Fillings' and unProductType = 2) or 
									productitem.unProductGroup  = (Select unProductGroup From productgroup Where PGName = 'Sweet Fillings' and unProductType = 2) or 
									productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Fillins of the Month' and unProductType = 2) or 
									productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Beverages' and unProductType = 2) or 
									productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Hot Drinks' and unProductType = 2) or
									productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Waffle Mix' and unProductType = 2) or
									productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Iced Tea in Packs' and unProductType = 2)
								)
								Order By PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('ssissi',$_GET['dfrom'],$_GET['dto'],$_GET['bid'],$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($unProductItem,$PIName,$PGName,$IDDIRUsage,$COGS);
				while($stmt->fetch()){
					$TotalCOGS += $COGS;
					if ($OldPGName!=$PGName){
						$OldPGName=$PGName;	
						?>
							<div class="rptgroup"><?php echo $PGName; ?></div>
						<?php
					}
					?>
					<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
						<div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($IDDIRUsage,4); ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($COGS,4); ?>"></div>
					</div>
					<?php
					$i++;
				}
				$stmt->close();
			}
		}else{
			if($stmt->prepare("Select PIName,IDDIRUsage,IDDIRUsage * TIDCost as COGS,PGName From inventorydata
							Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
							Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
							Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
							Where unInventoryControl = ? and unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
							(productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Savory Fillings' and unProductType = 2) or 
							productitem.unProductGroup  = (Select unProductGroup From productgroup Where PGName = 'Sweet Fillings' and unProductType = 2) or 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Fillins of the Month' and unProductType = 2) or 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Beverages' and unProductType = 2) or 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Hot Drinks' and unProductType = 2) or
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Waffle Mix' and unProductType = 2) or
									productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Iced Tea in Packs' and unProductType = 2))
							Order by PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($PIName,$IDDIRUsage,$COGS,$PGName);
				while($stmt->fetch()){
					$TotalCOGS += $COGS;
					if ($OldPGName!=$PGName){
						$OldPGName=$PGName;	
						?>
							<div class="rptgroup"><?php echo $PGName; ?></div>
						<?php
					}
					?>
					<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
						<div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($IDDIRUsage,4); ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($COGS,4); ?>"></div>
					</div>
					<?php
					$i++;
				}
				$stmt->close();
			}
		}
		$mysqli->close();
		?>
        <div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>; border-top:#333 solid thin;">
            <div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold;" type="text" value="Total COGS"></div>
            <div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text"></div>
            <div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold; text-align:right;" type="text" value="<?php echo number_format($TotalCOGS,4); ?>"></div>
        </div>
        
        <?php
		$TotalPS=0;
		$OldPGName='';
		$i=0;
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if(isset($_GET['filter'])){
			if($stmt->prepare("
								Select productitem.unProductItem as `pid`,PIName,PGName,
								(
									Select Sum(IDDIRUsage) From inventorydata 
									Inner Join inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl 
									Where inventorydata.unProductitem = pid and (ICDate Between ? and ?) and unBranch = ?
								) as `Usage`,
								(
									Select Sum(IDDIRUsage) From inventorydata 
									Inner Join inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl 
									Where inventorydata.unProductitem = pid and (ICDate Between ? and ?) and unBranch = ?
								) * TIDCost as `PS` 
								From productitem
								Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
								Inner Join templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
								Where unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
								productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Packaging Supplies' and unProductType = 2)
								Order By PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('ssissi',$_GET['dfrom'],$_GET['dto'],$_GET['bid'],$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($unProductItem,$PIName,$PGName,$IDDIRUsage,$PS);
				while($stmt->fetch()){
					$TotalPS += $PS;
					if ($OldPGName!=$PGName){
						$OldPGName=$PGName;	
						?>
							<div class="rptgroup"><?php echo $PGName; ?></div>
						<?php
					}
					?>
					<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
						<div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($IDDIRUsage,4); ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($PS,4); ?>"></div>
					</div>
					<?php
					$i++;
				}
				$stmt->close();
			}
		}else{
			if($stmt->prepare("Select PIName,IDDIRUsage,IDDIRUsage * TIDCost as PS,PGName From inventorydata
							Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
							Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
							Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
							Where unInventoryControl = ? and unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'Packaging Supplies' and unProductType = 2)
							Order by PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($PIName,$IDDIRUsage,$PS,$PGName);
				while($stmt->fetch()){
					$TotalPS += $PS;
					if ($OldPGName!=$PGName){
						$OldPGName=$PGName;	
						?>
							<div class="rptgroup"><?php echo $PGName; ?></div>
						<?php
					}
					?>
					<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
						<div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($IDDIRUsage,4); ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($PS,4); ?>"></div>
					</div>
					<?php
					$i++;
				}
				$stmt->close();
			}
		}
		$mysqli->close();
		?>
        <div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>; border-top:#333 solid thin;">
            <div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold;" type="text" value="Total Packaging Supplies"></div>
            <div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text"></div>
            <div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold; text-align:right;" type="text" value="<?php echo number_format($TotalPS,4); ?>"></div>
        </div>
        
        <?php
		$TotalGS=0;
		$OldPGName='';
		$i=0;
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if(isset($_GET['filter'])){
			if($stmt->prepare("
								Select productitem.unProductItem as `pid`,PIName,PGName,
								(
									Select Sum(IDDIRUsage) From inventorydata 
									Inner Join inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl 
									Where inventorydata.unProductitem = pid and (ICDate Between ? and ?) and unBranch = ?
								) as `Usage`,
								(
									Select Sum(IDDIRUsage) From inventorydata 
									Inner Join inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl 
									Where inventorydata.unProductitem = pid and (ICDate Between ? and ?) and unBranch = ?
								) * TIDCost as `GS` 
								From productitem
								Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
								Inner Join templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
								Where unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
								productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'General Supplies' and unProductType = 2)
								Order By PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('ssissi',$_GET['dfrom'],$_GET['dto'],$_GET['bid'],$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($unProductItem,$PIName,$PGName,$IDDIRUsage,$GS);
				while($stmt->fetch()){
					$TotalGS += $GS;
					if ($OldPGName!=$PGName){
						$OldPGName=$PGName;	
						?>
							<div class="rptgroup"><?php echo $PGName; ?></div>
						<?php
					}
					?>
					<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
						<div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($IDDIRUsage,4); ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($GS,4); ?>"></div>
					</div>
					<?php
					$i++;
				}
				$stmt->close();
			}
		}else{
			if($stmt->prepare("Select PIName,IDDIRUsage,IDDIRUsage * TIDCost as GS,PGName From inventorydata
							Inner Join templateitemdata On inventorydata.unProductItem = templateitemdata.unProductItem
							Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
							Inner Join productgroup On productitem.unProductGroup = productgroup.unProductGroup
							Where unInventoryControl = ? and unProductType = (Select unProductType From producttype Where PTName = 'Rawmats') and 
							productitem.unProductGroup = (Select unProductGroup From productgroup Where PGName = 'General Supplies' and unProductType = 2)
							Order by PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('i',$_GET['did']);
				$stmt->execute();
				$stmt->bind_result($PIName,$IDDIRUsage,$GS,$PGName);
				while($stmt->fetch()){
					$TotalGS += $GS;
					if ($OldPGName!=$PGName){
						$OldPGName=$PGName;	
						?>
							<div class="rptgroup"><?php echo $PGName; ?></div>
						<?php
					}
					?>
					<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
						<div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($IDDIRUsage,4); ?>"></div>
						<div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($GS,4); ?>"></div>
					</div>
					<?php
					$i++;
				}
				$stmt->close();
			}
		}
		$mysqli->close();
		?>
        <div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>; border-top:#333 solid thin;">
            <div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold;" type="text" value="Total General Supplies"></div>
            <div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text"></div>
            <div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold; text-align:right;" type="text" value="<?php echo number_format($TotalGS,4); ?>"></div>
        </div>
        
        <div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>; border-top:#666 solid 2px;">
            <div class="rptlistviewsubitem" style="width:400px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold;" type="text" value="Total COS"></div>
            <div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; text-align:right;" type="text"></div>
            <div class="rptlistviewsubitem" style="width:100px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold; text-align:right;" type="text" value="<?php echo number_format($TotalGS + $TotalCOGS + $TotalPS,4); ?>"></div>
        </div>
        
    	<!--<div class="rptlistviewitem" style="background-color:#FFF; border-top:solid thin #999;">
        	<div class="rptlistviewsubitem" style="width:49%;"><input readonly style="width:400px; border:none; background-color:transparent;" type="text" value="COGS"></div>
			<div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:79px; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format(GetCOGS(),4); ?>"></div>
        </div>
        <div class="rptlistviewitem" style="background-color:#EEE;">
        	<div class="rptlistviewsubitem" style="width:49%;"><input readonly style="width:400px; border:none; background-color:transparent;" type="text" value="Packaging Supplies"></div>
			<div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:79px; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format(GetPackagingSupplies(),4); ?>"></div>
        </div>
        <div class="rptlistviewitem" style="background-color:#FFF;">
        	<div class="rptlistviewsubitem" style="width:49%;"><input readonly style="width:400px; border:none; background-color:transparent;" type="text" value="General &amp Cleaning Supplies"></div>
			<div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:79px; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format(GetGeneralSupplies(),4); ?>"></div>
        </div>
        <div class="rptlistviewitem" style="background-color:#EEE; border-top:2px solid #666;">
        	<div class="rptlistviewsubitem" style="width:49%;"><input readonly style="width:400px; border:none; background-color:transparent; font-weight:bold;" type="text" value="Total"></div>
			<div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:79px; border:none; background-color:transparent; font-weight:bold; text-align:right;" type="text" value="<?php echo number_format(GetCOGS() + GetPackagingSupplies() + GetGeneralSupplies(),4); ?>"></div>
        </div>-->
    </div>
</div>

<?php require('footer.php'); ?>
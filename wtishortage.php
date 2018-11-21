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
	function GetSalesShortage(){
		if(isset($_GET['filter'])){
			$query = "Select Sum(SShortage) * -1 as Shortage From sales
					Inner Join inventorycontrol On sales.unInventoryControl = inventorycontrol.unInventoryControl
					Where (ICDate Between ? and ?) and unBranch = ? and SShortage < 0";
		}else{
			$query = "Select If(SShortage>0,0.0000,SShortage * -1) as Shortage From sales Where unInventoryControl = ?";
		}

		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare($query)){
			if(isset($_GET['filter'])){
				$stmt->bind_param('ssi',$_GET['dfrom'],$_GET['dto'],$_GET['bid']);	
			}else{
				$stmt->bind_param('i',$_GET['did']);
			}
			$stmt->execute();
			$stmt->bind_result($SShortage);
			$stmt->fetch();
			$stmt->close();
		}
		$mysqli->close();
		return $SShortage;
	}
	function GetShortage($unProductItem){
		if(isset($_GET['filter'])){ 
			$query = "Select If(Sum(IDVarianceAmount)>0,Sum(IDVarianceAmount), 0.0000) From inventorydata 
						Inner Join inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
						Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
						Where inventorydata.unProductItem = ? and unBranch = ? and (ICDate Between ? and ?) and IDVarianceAmount < 0";
		}else{ 
			$query = "Select If(Sum(IDVarianceAmount)>0,Sum(IDVarianceAmount), 0.0000) From inventorydata 
					Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
					Where inventorydata.unInventoryControl = ? and inventorydata.unProductItem = ?";
		}
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare($query)){
			if(isset($_GET['filter'])){
				$stmt->bind_param('iiss',$unProductItem,$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
			}else{
				$stmt->bind_param('ii',$_GET['did'],$unProductItem);
			}
			$stmt->execute();
			$stmt->bind_result($Amount);
			$stmt->fetch();
			$stmt->close();
		}
		$mysqli->close();
		return $Amount;
	}
	function GetOverage($unProductItem){
		/*$query = "Select If(Sum(IDVarianceAmount)<0,Sum(IDVarianceAmount) * -1, 0.0000) From inventorydata 
					Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
					Where inventorydata.unProductItem = ?";*/
		if(isset($_GET['filter'])){
			$query = "Select If(Sum(IDVarianceAmount)<0,Sum(IDVarianceAmount) * -1, 0.0000) From inventorydata 
						Inner Join inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
						Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
						Where inventorydata.unProductItem = ? and unBranch = ? and (ICDate Between ? and ?) and IDVarianceAmount < 0";
		}else{
			$query = "Select If(Sum(IDVarianceAmount)>0,0.0000,Sum(IDVarianceAmount) * -1) From inventorydata 
					Inner Join productitem On inventorydata.unProductItem = productitem.unProductItem
					Where inventorydata.unInventoryControl = ? and inventorydata.unProductItem = ?";
		}
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare($query)){
			if(isset($_GET['filter'])){
				$stmt->bind_param('iiss',$unProductItem,$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
			}else{
				$stmt->bind_param('ii',$_GET['did'],$unProductItem);
			}
			$stmt->execute();
			$stmt->bind_result($Amount);
			$stmt->fetch();
			$stmt->close();
		}
		$mysqli->close();
		return $Amount;
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

<div class="rptgroup" style="width:809px;">OVERAGES</div>
<div class="rptlistview" style="width:100%;">
	<div class="rptcolumn">
    	<div class="rptcolumnheader" style="width:17%;">Item Name</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">Start</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">Deliver</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">InOut</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">Return</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">InvEndT</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">InvUsage</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">POSUsage</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">VarQTY</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">VarAMT</div>
    </div>
    <div class="rptrow">
    	<?php
		$export=array();
		$row=array('OVERAGES');
		$export[]=$row;
		$row = array('Item Name','Start','Delivery','InOut','Return','InvEndT','InvUsage','POSUsage','VarQTY','VarAMT');
		$export[]=$row;
        $i=0;
        $TotalOver=0;

        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
		
		if(isset($_GET['filter'])){
			$query = "SELECT inventorydata.unProductItem, IDStart, IDDelivery, IDTransfer, IDDamage, IDEndTotal, IDDIRUsage, IDProcessOut, PIName, SUM(IDVarianceQTY), IDVarianceAmount
					FROM inventorydata
					INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
					INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
					INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
					WHERE IDVarianceAmount <0
					AND unProductType > 1
					AND unBranch = ? 
					AND (ICDate Between ? and ?) 
					GROUP BY PIName
					ORDER BY PIName";
		}else{
			$query = "SELECT inventorydata.unProductItem, IDStart, IDDelivery, IDTransfer, IDDamage, IDEndTotal, IDDIRUsage, IDProcessOut, PIName, SUM(IDVarianceQTY), IDVarianceAmount
					FROM inventorydata
					INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
					INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
					INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
					WHERE IDVarianceAmount <0
					AND unProductType > 1
					AND inventorydata.unInventoryControl = ? 
					GROUP BY PIName
					ORDER BY PIName";
		}
        if($stmt->prepare($query)){
			if(isset($_GET['filter'])){
				$stmt->bind_param('iss',$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
			}else{
            	$stmt->bind_param('i',$_GET['did']);
			}
			$stmt->execute();
            $stmt->bind_result($unProductItem,$IDStart,$IDDelivery,$IDTransfer,$IDDamage,$IDEndTotal,$IDDIRUsage,$IDProcessOut,$PIName,$IDVarianceQTY,$IDVarianceAmount);
            while ($stmt->fetch()){
				$row = array($PIName,$IDStart,$IDDelivery,$IDTransfer,$IDDamage,$IDEndTotal,$IDDIRUsage,$IDProcessOut,$IDVarianceQTY,$IDVarianceAmount);
				$export[]=$row;
                ?>
                    <div class="rptlistviewitem" id="lvitem-<?php echo $i; ?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                        <div class="rptlistviewsubitem" style="width:17%;"><input readonly style="width:450px; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
                        <div class="rptlistviewsubitem" style="width:9%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDStart; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDDelivery; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDTransfer; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDDamage; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDEndTotal; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDDIRUsage; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDProcessOut; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDVarianceQTY; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo GetOverage($unProductItem); $TotalOver += GetOverage($unProductItem); ?>" ></div>
                    </div>
                <?php
                $i++;
            }
            ?>
            <?php
        }else{
			echo $stmt->error;
		}
        ?>
        <div class="rptlistviewitem" style="border-top:2px solid #666; background-color:#FFF;">
        	<div class="rptlistviewsubitem" style="width:65%;"><input readonly style="width:100%; border:none; background-color:transparent; font-weight:bold;" type="text" value="Total Overage"></div>
            <div class="rptlistviewsubitem" style="width:30%;"><input readonly style="width:100%; border:none; background-color:transparent; font-weight:bold; text-align:right;" type="text" value="<?php echo number_format($TotalOver,2); ?>"></div>
        </div>
    </div>
</div>

<div style="width:100%; height:20px;"></div>
<div class="rptgroup" style="width:809px;">SHORTAGES</div>
<div class="rptlistview" style="width:100%; margin-bottom:50px">
	<div class="rptcolumn">
    	<div class="rptcolumnheader" style="width:17%;">Item Name</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">Start</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">Deliver</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">InOut</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">Return</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">InvEndT</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">InvUsage</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">POSUsage</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">VarQTY</div>
        <div class="rptcolumnheader" style="width:9%; text-align:right;">VarAMT</div>
    </div>
    <div class="rptrow">
        <?php
		$row=array('SHORTAGES');
		$export[]=$row;
		$row = array('Item Name','Start','Delivery','InOut','Return','InvEndT','InvUsage','POSUsage','VarQTY','VarAMT');
		$export[]=$row;
        $i=0;
        $TotalOver=0;

        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
		
		if(isset($_GET['filter'])){
			$query = "SELECT inventorydata.unProductItem, IDStart, IDDelivery, IDTransfer, IDDamage, IDEndTotal, IDDIRUsage, IDProcessOut, PIName, SUM(IDVarianceQTY), SUM(IDVarianceAmount)
					FROM inventorydata
					INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
					INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
					INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
					WHERE IDVarianceAmount >0
					AND unProductType = 2
					AND unBranch = ? 
					AND (ICDate Between ? and ?) 
					GROUP BY PIName
					ORDER BY PIName";
		}else{
			$query = "SELECT inventorydata.unProductItem, IDStart, IDDelivery, IDTransfer, IDDamage, IDEndTotal, IDDIRUsage, IDProcessOut, PIName, SUM(IDVarianceQTY), IDVarianceAmount
					FROM inventorydata
					INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
					INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
					INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
					WHERE IDVarianceAmount >0
					AND unProductType = 2
					AND inventorydata.unInventoryControl = ? 
					GROUP BY PIName
					ORDER BY PIName";
		}
        if($stmt->prepare($query)){
			if(isset($_GET['filter'])){
				$stmt->bind_param('iss',$_GET['bid'],$_GET['dfrom'],$_GET['dto']);
			}else{
            	$stmt->bind_param('i',$_GET['did']);
			}
			$stmt->execute();
            $stmt->bind_result($unProductItem,$IDStart,$IDDelivery,$IDTransfer,$IDDamage,$IDEndTotal,$IDDIRUsage,$IDProcessOut,$PIName,$IDVarianceQTY,$IDVarianceAmount);
            while ($stmt->fetch()){
				$row = array($PIName,$IDStart,$IDDelivery,$IDTransfer,$IDDamage,$IDEndTotal,$IDDIRUsage,$IDProcessOut,$IDVarianceQTY,$IDVarianceAmount);
				$export[]=$row;
                ?>
                    <div class="rptlistviewitem" id="lvitem-<?php echo $i; ?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                        <div class="rptlistviewsubitem" style="width:17%;"><input readonly style="width:450px; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
                        <div class="rptlistviewsubitem" style="width:9%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDStart; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDDelivery; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDTransfer; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDDamage; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDEndTotal; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDDIRUsage; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDProcessOut; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDVarianceQTY; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:8%; font-size:6px"><input readonly style="width:100%; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDVarianceAmount;/*GetShortage($unProductItem); $TotalAmount += GetShortage($unProductItem);*/ $TotalAmount += $IDVarianceAmount; ?>" ></div>
                    </div>
                <?php
                $i++;
            }
            ?>
            <?php
        }else{
			echo $stmt->error;
		}
        ?>
        <div class="rptlistviewitem" style="border-top:2px solid #666; background-color:#FFF;">
        	<div class="rptlistviewsubitem" style="width:65%;"><input readonly style="width:100%; border:none; background-color:transparent; font-weight:bold;" type="text" value="Total Shortage"></div>
            <div class="rptlistviewsubitem" style="width:30%;"><input readonly style="width:100%; border:none; background-color:transparent; font-weight:bold; text-align:right;" type="text" value="<?php echo number_format($TotalAmount,2); ?>"></div>
        </div>
    </div>
</div>



<?php require('footer.php'); $_SESSION['shortagereport'] = $export; ?>
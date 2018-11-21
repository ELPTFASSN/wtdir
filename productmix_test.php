
<!-- Old product mix-->

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
	
	//EXPORT TO CSV--------------------------------------------------------------------
	//function convert_to_csv($input_array, $output_file_name, $delimiter)
	//{
		/** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	//	$f = fopen('php://memory', 'w');
		/** loop through array  */
	//	foreach ($input_array as $line) {
			/** default php csv handler **/
	//		fputcsv($f, $line, $delimiter);
	//	}
		/** rewrind the "file" with the csv lines **/
	//	fseek($f, 0);
		/** modify header to be downloadable csv file **/
	//	header('Content-Type: application/force-download');
	//	header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
		/** Send file to browser for download */
	//	fpassthru($f);
	//}
	/** Array to convert to csv */
	//$array_to_csv = Array(
	//	Array(12566,
	//		'Enmanuel',
	//		'Corvo'
	//	),
	//	Array(56544,
	//		'John',
	//		'Doe'
	//	),
	//	Array(78550,
	//		'Mark',
	//		'Smith'
	//	)
	//);
	//convert_to_csv($array_to_csv, 'report.csv', ',');
	
?>
<script type="text/javascript">
function FilterReport(dFrom,dTo){
	//alert('Date To: '.dTo.' < Date From: '.dFrom);
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
	columnheader('colProductMix','lvProductMix');
});
</script>
<style>
input{
	font-family:calibri;
}
</style>

<div id="toolbar" style="width:inherit; margin:auto;">
	<!--<input class="exemptPrint" type="text" value="From" style="width:35px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="date" id="dtpFrom" name="dtpFrom" value="<?php echo (isset($_GET['dfrom']))?$_GET['dfrom']:'';?>" >	
    <input class="exemptPrint" type="text" value="To" style="width:20px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="date" id="dtpTo" name="dtpTo" value="<?php echo (isset($_GET['dto']))?$_GET['dto']:'';?>" >-->
    <input type="text" value="From" style="width:35px; margin-left:5px; border:none; background-color:transparent;" readonly><select id="dtpFrom" name="dtpFrom">
    	<?php
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
       		$stmt=$mysqli->stmt_init();
			if($stmt->prepare("SELECT unInventoryControl,Concat('[',MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate),' - ',ICNumber,']') as `ICPeriod` FROM inventorycontrol WHERE unBranch=? AND Status = 1")){
			$stmt->bind_param('i',$_GET['bid']);
            $stmt->execute();
            $stmt->bind_result($unInventoryControl,$ICPeriod);
            while($stmt->fetch()){
					$unInventoryControl1=sprintf('%06d', $unInventoryControl);
					?><option value="<?php echo $unInventoryControl1?>" <?php if(isset($_GET['filter'])&&$unInventoryControl==$_GET['dfrom']){echo 'selected';}else if(!isset($_GET['filter'])&&$unInventoryControl==$_GET['did']){echo 'selected';} ?>><?php echo $unInventoryControl1." - ".$ICPeriod ?></option><?php
				}
            $stmt->close();
        	}
		?>
    </select>
    <input type="text" value="To" style="width:20px; margin-left:5px; border:none; background-color:transparent;" readonly><select  id="dtpTo" name="dtpTo">
    	<?php
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
       		$stmt=$mysqli->stmt_init();
			if($stmt->prepare("SELECT unInventoryControl,Concat('[',MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate),' - ',ICNumber,']') as `ICPeriod` FROM inventorycontrol WHERE unBranch=? AND Status = 1")){
			$stmt->bind_param('i',$_GET['bid']);
            $stmt->execute();
            $stmt->bind_result($unInventoryControl,$ICPeriod);
            while($stmt->fetch()){
					$unInventoryControl1=sprintf('%06d', $unInventoryControl);
					?><option value="<?php echo $unInventoryControl1?>" <?php if(isset($_GET['filter'])&&$unInventoryControl==$_GET['dto']){ echo 'selected';}else if(!isset($_GET['filter'])&&$unInventoryControl==$_GET['did']){echo 'selected';} ?>><?php echo $unInventoryControl1." - ".$ICPeriod ?></option><?php
				}
            $stmt->close();
        	}
		?>
    </select>
    <input class="exemptPrint" type="button" value="Go" onClick="FilterReport(dtpFrom.value,dtpTo.value)">
	<!--<input type="text" style="border:none; background-color:transparent; float:right; margin-top:5px; margin-right:5px; text-align:right; width:300px;" value="<?php echo (isset($_GET['dfrom']))?ShowDateFilter():PMixPeriod();?>" readonly>-->
</div>

<?php 
?>

<div class="rptlistview" style="width:100%; font-family:calibri;" id="lvProductMix">
    <div class="rptcolumn" id="colProductMix">
        <div class="rptcolumnheader" style="width:58%;">Products</div>
        <div class="rptcolumnheader" style="width:10%; text-align:right;">Quantity</div>
        <div class="rptcolumnheader" style="width:10%; text-align:right;">Unit Price</div>
        <div class="rptcolumnheader" style="width:10%; text-align:right;">Amount</div>
        <div class="rptcolumnheader" style="width:11%; text-align:right;">Percentage</div>
    </div>
    
    <div class="rptrow">
        <?php 
		$export=array();
        $OldPGName='';
        $i=0;
        $TotalPercent=0;
		
		 
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
		
		if(isset($_GET['filter'])){
			if($stmt->prepare("
						Select Sum(IDQuantity) as `TotalQuantity`, Sum(IDTotalAmount) as `TotalAmount`
						From invoicedata
						Inner Join productitem ON invoicedata.unProductItem = productitem.unProductItem
						Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
						Where (unInventoryControl Between ? and ?) and unProductType =1 AND invoicedata.unBranch = ?") ){
			$stmt->bind_param('iii',$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
            $stmt->execute();
            $stmt->bind_result($TotalQuantity,$TotalAmount);
            $stmt->fetch();
            $stmt->close();
        	}
		}else{
			if($stmt->prepare("Select Sum(IDQuantity) as `TotalQuantity`, Sum(IDTotalAmount) as `TotalAmount`
								From invoicedata
								Inner Join productitem ON invoicedata.unProductItem = productitem.unProductItem
								Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
								Where invoicedata.unInventoryControl = ? and unProductType = 1  AND invoicedata.unBranch = ?
								Order by productgroup.PGPriority Asc")){
				$stmt->bind_param('ii',$_GET['did'],$_GET['bid']);
				$stmt->execute();
				$stmt->bind_result($TotalQuantity,$TotalAmount);
				$stmt->fetch();
				$stmt->close();
			}
		}
		
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
		
		if(isset($_GET['filter'])){
			$query = "SELECT PIName, SUM( IDSoldQuantity ) , SUM( IDSoldAmount ) , PGName
				FROM inventorydata
				INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
				INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
				INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
				WHERE (
				inventorydata.unInventoryControl
				BETWEEN ? 
				AND ?
				)
				AND IDSoldQuantity > 0
				AND unProductType =1
				AND productitem.`Status` =1
				AND inventorycontrol.unBranch =?
				GROUP BY PIName
				ORDER BY productitem.unProductGroup,PIName ASC 
				LIMIT 0 , 30";
		}else{
			$query = "SELECT PIName, SUM( IDSoldQuantity ) , SUM( IDSoldAmount ) , PGName
				FROM inventorydata
				INNER JOIN inventorycontrol ON inventorydata.unInventoryControl = inventorycontrol.unInventoryControl
				INNER JOIN productitem ON inventorydata.unProductItem = productitem.unProductItem
				INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
				WHERE (
				inventorydata.unInventoryControl=?
				)
				AND IDSoldQuantity > 0
				AND unProductType =1
				AND productitem.`Status` =1
				AND inventorycontrol.unBranch =?
				GROUP BY PIName
				ORDER BY productitem.unProductGroup,PIName ASC";
		}
        if($stmt->prepare($query)){
			if(isset($_GET['filter'])){
				$stmt->bind_param('iii',$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
			}else{
            	$stmt->bind_param('ii',$_GET['did'],$_GET['bid']);
			}
			$stmt->execute();
			$stmt->store_result();
            $stmt->bind_result($PIName,$IDSoldQuantity,$IDSoldAmount,$PGName);
			$totalPract =0;
			while ($stmt->fetch()){
				$totalPract += $IDSoldQuantity;
			}
			$stmt->data_seek(0);
            while ($stmt->fetch()){
				$PITPrice =  $IDSoldAmount / $IDSoldQuantity;
				$TotalPercent += ($IDSoldQuantity==0)?0:$IDSoldQuantity/$totalPract * 100;
				$TotalQuantity += $IDSoldQuantity;
				$TotalAmount += $IDSoldAmount;
                if ($OldPGName!=$PGName){
                    $OldPGName=$PGName;
					$row = array($PGName, '', '', '', '');
					$export[]=$row;
                    ?>
                        <div class="rptgroup"><?php echo $PGName; ?></div>
                    <?php
                }
				$row = array($PIName,$IDSoldQuantity,$PITPrice,$IDSoldAmount,($TotalQuantity==0)?'0.00':number_format(round($IDSoldQuantity/$TotalQuantity * 100,2),'2'));
				 $export[]=$row;
                ?>
                    <div class="rptlistviewitem" id="lvitem-<?php echo $i; ?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                        <div class="rptlistviewsubitem" style="width:57.5%;"><input readonly style="width:450px; border:none; background-color:transparent;" type="text" value="<?php echo $PIName; ?>"></div>
                        <div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:82px; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo round($IDSoldQuantity); ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:82px; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $PITPrice; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:82px; border:none; text-align:right; background-color:transparent;" type="text" value="<?php echo $IDSoldAmount; ?>" ></div>
                        <div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:82px; border:none; text-align:right; background-color:transparent;" type="text" value="<?php  echo ($TotalQuantity==0)?'0.00':number_format(round($IDSoldQuantity/$totalPract * 100,2),'2'); ?>" ></div>
                    </div>
                <?php
                $i++;
            }
			$stmt->close;
            ?>
            <?php
        }else{
			echo $stmt->error;
			$stmt->close;
		}
		$row = array('TOTAL',$totalPract,'',$TotalAmount,$TotalPercent);
		$export[]=$row;
        ?>
		<div class="rptlistviewitem" style="background-color:#FFF; border-top:2px solid #000;">
            <div class="rptlistviewsubitem" style="font-weight:bold; width:55%;" >Total</div>
            <div class="rptlistviewsubitem" style="width:10%;"><input readonly class="listviewsubitem" type="text" style="font-weight:bold; width:82px; border:none; text-align:right; background-color:transparent;" value="<?php echo number_format($totalPract); ?>" ></div>
            <div class="rptlistviewsubitem" style="width:10%;"><input readonly class="listviewsubitem" type="text" style="font-weight:bold; width:82px; border:none; text-align:right; background-color:transparent;" ></div>
            <div class="rptlistviewsubitem" style="width:10%;"><input readonly class="listviewsubitem" type="text" style="font-weight:bold; width:82px; border:none; text-align:right; background-color:transparent;" value="<?php echo number_format($TotalAmount,2); ?>" ></div>
            <div class="rptlistviewsubitem" style="width:10%;"><input readonly class="listviewsubitem" type="text" style="font-weight:bold; width:82px; border:none; text-align:right; background-color:transparent;" value="<?php echo number_format($TotalPercent,'2'); ?>" ></div>
        </div>
    </div>
</div>
<?php //require('footer.php');
$_SESSION['productmixreport'] = $export;
?>


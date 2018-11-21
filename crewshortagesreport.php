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
?>
<script type="text/javascript">
	/*$(document).ready(function(){
		$('#export').remove();
	});*/
function FilterReport(dFrom,dTo){
	if(dFrom == '' || dTo == ''){
		//msgbox('From and To cannot contain a null value. Select date.','','');
		return false;
	}
	if(dTo < dFrom){
		//msgbox('Date To cannot be earlier than Date From.','','');
		return false;
	}
	window.location.href = 'http://10.1.1.3<?php echo $_SERVER['PHP_SELF'].'?&bid='.$_GET['bid'].'&did='.$_GET['did'].'&type=1'; ?>&filter=1&dfrom='+dFrom+'&dto='+dTo;
	//redirect('<?php echo $_SERVER['PHP_SELF'].'?&bid='.$_GET['bid'].'&did='.$_GET['did'].'&type=1'; ?>&filter=1&dfrom='+dFrom+'&dto='+dTo);
}
</script>
<div id="toolbar" style="width:inherit; margin:auto;">
	<?php /*if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){ }else{ */ ?>
   		<?php
                $mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt = $mysqli->stmt_init();
                if($stmt->prepare("Select BName from inventorycontrol Inner Join branch On inventorycontrol.unBranch=branch.unBranch Where unInventoryControl=?")){
                    $stmt->bind_param('i',$_GET['did']);
                    $stmt->execute();
                    $stmt->bind_result($BName);
                    $stmt->fetch();
                    $stmt->close();
					echo '<input type="text" style="border:none; background-color:transparent; float:left; margin-top:5px; margin-left:5px; text-align:left; width:auto; font-weight:bold" value="'.strtoupper($BName).'" readonly>';
                }
         ?>
    	<input class="exemptPrint" type="text" value="From" style="width:35px; margin-left:5px; border:none; background-color:transparent;" readonly>
    	<input class="exemptPrint" type="date" id="dtpFrom" name="dtpFrom" value="<?php echo (isset($_GET['dfrom']))?$_GET['dfrom']:'';?>" >
    	<input class="exemptPrint" type="text" value="To" style="width:20px; margin-left:5px; border:none; background-color:transparent;" readonly>
    	<input class="exemptPrint" type="date" id="dtpTo" name="dtpTo" value="<?php echo (isset($_GET['dto']))?$_GET['dto']:'';?>" >
    	<input class="exemptPrint" type="button" value="Go" onClick="FilterReport(dtpFrom.value,dtpTo.value)">
    	
    	<!--<input type="text" style="border:none; background-color:transparent; float:right; margin-top:5px; margin-right:5px; text-align:right; width:300px;" value="<?php if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){}else{ echo (isset($_GET['dfrom']))?ShowDateFilter():PMixPeriod(); }?>" readonly>-->
	<?php  // } ?>
</div>
<div class="rptlistview" style="width:auto; padding-right: 10px">
	<div class="rptcolumn">
   		<?php if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){?>
   			<!--<div class="rptcolumnheader" style="width:10%;">Branch</div>
			<div class="rptcolumnheader" style="width:20%;">Date</div>
			<div class="rptcolumnheader" style="width:9%; text-align:right;">Net Sales</div>-->
		<?php }else{ ?>
			<div class="rptcolumnheader" style="width:10%;">ID</div>
			<div class="rptcolumnheader" style="width:20%;">Date</div>
			<div class="rptcolumnheader" style="width:9%; text-align:right;">Net Sales</div>
        <?php } ?>
    </div>
    <div class="rptrow" style="text-align: center">
    	
    	<?php
			/*
			SELECT inventorycontrol.ICDate AS DATE, inventorycontrol.ICNumber AS DIR, SDShortPMix AS MIX, SDOtherSupplies AS  'Other Supplies', SDShortCashFund AS  'Cash Fund', SDShortTotal AS  'Total Shortages', COUNT( salescrew.unEmployee ) AS 'No. of Crew',
ifnull(sum(case when CONCAT_WS(', ',`ELastName`,`EFirstName`) = 'Andrade, Lucil' then SDShortTotal end)/ COUNT( salescrew.unEmployee ),0.0000) 'Andrade, Lucil',
ifnull(sum(case when CONCAT_WS(', ',`ELastName`,`EFirstName`) = 'Alba, Nina Janine' then SDShortTotal end)/ COUNT( salescrew.unEmployee ),0.0000) 'Alba, Nina Janine',  
ifnull(sum(case when CONCAT_WS(', ',`ELastName`,`EFirstName`) = 'Abogada, Endrico' then SDShortTotal end)/ COUNT( salescrew.unEmployee ),0.0000) 'Abogada, Endrico',  
ifnull(sum(case when CONCAT_WS(', ',`ELastName`,`EFirstName`) = 'Gambalan, Clia Mae' then SDShortTotal end)/ COUNT( salescrew.unEmployee ),0.0000) 'Gambalan, Clia Mae',  
ifnull(sum(case when CONCAT_WS(', ',`ELastName`,`EFirstName`) = 'Hurtada, Lily' then SDShortTotal  end)/ COUNT( salescrew.unEmployee ),0.0000) 'Hurtada, Lily',  
ifnull(sum(case when CONCAT_WS(', ',`ELastName`,`EFirstName`) = 'Sumagaysay, Shiela Mae' then SDShortTotal end)/ COUNT( salescrew.unEmployee ),0.0000) 'Sumagaysay, Shiela Mae',  
ifnull(sum(case when CONCAT_WS(', ',`ELastName`,`EFirstName`) = 'Gantinao, Rea Jade' then SDShortTotal end)/ COUNT( salescrew.unEmployee ),0.0000) 'Gantinao, Rea Jade'  
FROM  `salesdata`
INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salesdata.`unInventoryControl` 
INNER JOIN salescrew ON salescrew.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee 
WHERE salesdata.`unBranch`= 1
AND  `ICDate` 
BETWEEN  '2016-12-31'
AND  '2018-01-01'
GROUP BY ICDate, ICNumber
ORDER BY ICDate ASC 
LIMIT 0 , 30
			*/
			if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			?><table><?PHP
						if(isset($_GET['filter'])){
							$prevdate = "'".$_GET['dfrom']."'";
							$nextdate = "'".$_GET['dto']."'";
							$prevdateS = $_GET['dfrom'];
							$nextdateS = $_GET['dto'];
							$prevMonthName = date('F',strtotime($prevdateS));
							$nextMonthName = date('F',strtotime($nextdateS));
							$prevDateName = date('d',strtotime($prevdateS));
							$nextDateName = date('d',strtotime($nextdateS));
							$prevYearName = date('Y',strtotime($prevdateS));
							$nextYearName = date('Y',strtotime($nextdateS));
							$prevdateF = $prevMonthName.' '.$prevDateName.', '.$prevYearName;
							$nextdateF = $nextMonthName.' '.$nextDateName.', '.$nextYearName;
						}else{
							$currdate = ExecuteReader("Select ICDate as `result` From inventorycontrol Where `unBranch`=".$_SESSION['bid']." AND `unInventoryControl`=".$_SESSION['did']);
							$dateset = explode('-', $currdate);
							$lastday = date('t',strtotime($currdate));
							$curryear = $dateset[0];
							$currmonth = $dateset[1];
							$prevmonth = (int)$currmonth-1;
							$nextmonth = (int)$currmonth+1;
							$prevdate = "'".$curryear."-".$currmonth."-01'";
							$nextdate = "'".$curryear."-".$currmonth."-".$lastday."'";
							$prevdateS = $curryear."-".$currmonth."-01";
							$nextdateS = $curryear."-".$currmonth."-".$lastday;
							$prevMonthName = date('F',strtotime($prevdateS));
							$nextMonthName = date('F',strtotime($nextdateS));
							$prevDateName = date('d',strtotime($prevdateS));
							$nextDateName = date('d',strtotime($nextdateS));
							$prevYearName = date('Y',strtotime($prevdateS));
							$nextYearName = date('Y',strtotime($nextdateS));
							$prevdateF = $prevMonthName.' '.$prevDateName.', '.$prevYearName;
							$nextdateF = $nextMonthName.' '.$nextDateName.', '.$nextYearName;
						}
						$summaryname = array();
						$summaryamt = array();
						$export=array();
						$row = array();
						$CSVTotal = array('GRAND TOTAL');
						$col = array('DATE','DIR','MIX','Other Supplies','Cash Fund','Total Shortages','No. Of Crew');
						//$export[]=$row;	
						$fieldss = array();
						$fieldss[] = '$DATE';
						$fieldss[] = '$DIR';
						$fieldss[] = '$MIX';
						$fieldss[] = '$OtherSupplies';
						$fieldss[] = '$CashFund';
						$fieldss[] = '$TotalShortages';
						$fieldss[] = '$NoOfCrew';
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						$stmtT = $mysqli->stmt_init();
						$stmthead = $mysqli->stmt_init();
						$stmtheadT = $mysqli->stmt_init();
						$queryheader="SELECT CONCAT_WS(  ', ', ELastName, EFirstName )  'Crew', SCCode1
							FROM  `salescrew` 
							INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salescrew.`unInventoryControl` 
							INNER JOIN salesdata ON salesdata.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
							INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee
							WHERE salesdata.unBranch = ?
							AND salescrew.Status =1
							AND salescrew.unEmployee > 0
							AND `ICDate`
							BETWEEN   ".$prevdate." AND ".$nextdate."  AND salesdata.unArea=? 
							GROUP BY employee.unEmployee
							ORDER BY employee.unEmployee, ICDate  ASC";
						$queryheaderT="SELECT CONCAT_WS(  ', ', ELastName, EFirstName )  'Crew', SCCode1
							FROM  `salescrew` 
							INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salescrew.`unInventoryControl` 
							INNER JOIN salesdata ON salesdata.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
							INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee
							WHERE salesdata.unBranch = ?
							AND salescrew.Status =1
							AND SCCode1 != 'T'
							AND salescrew.unEmployee > 0
							AND `ICDate`
							BETWEEN   ".$prevdate." AND ".$nextdate."  AND salesdata.unArea=? 
							GROUP BY employee.unEmployee 
							ORDER BY employee.unEmployee , ICDate  ASC";	
						$pivotstmt =' ';
						$pivottrainee = ' ';
						if($stmthead->prepare($queryheader)){
							$stmthead->bind_param('ii',$_GET['bid'],$_SESSION['area']);
							$stmthead->execute();
							$stmthead->store_result(); 
							$stmthead->bind_result($Crew,$SCCode);
							$SHrowcount = $stmthead->num_rows;
							$counter = 0;
							echo '<tr><th style="width:100px; margin:0;">'.$curryear.'</th><th>DIR</th><th>Mix</th><th>Other Supplies</th><th>Cash Fund</th><th>Total Shortages</th><th>No. Of Crew</th>';
							while($stmthead->fetch()){
								if (++$counter == $SHrowcount) {
										$pivotstmt=$pivotstmt."ifnull((sum(case when CONCAT_WS(', ', ELastName, EFirstName  ) = '".$Crew."'  then SDShortTotal end) * (salescrew.SCHours/SUM( salescrew.SCHours ))),0.0000) '".str_replace(' ', '', $Crew)."' ";
								} else {
										$pivotstmt=$pivotstmt."ifnull((sum(case when CONCAT_WS(', ', ELastName, EFirstName  ) = '".$Crew."' then SDShortTotal end) * (salescrew.SCHours/SUM( salescrew.SCHours ))),0.0000) '".str_replace(' ', '', $Crew)."', ";
								}
								echo '<th style="width:100px; margin:0;">'.$Crew.'</th>';	
								$fieldss[] = '$'.str_replace(' ', '', $Crew);
								$summaryname[] = '<tr><th></th><th style="text-align:left;">'.$Crew.'</th>';
								//$fieldss[] = $Crew;
								$col[] = $Crew;
							}
							echo '<th>TOTAL</th></tr>';
							$col[] = 'TOTAL';
							$export[]=$col;
						}
						/*if($stmtheadT->prepare($queryheaderT)){
							$stmtheadT->bind_param('ii',$_GET['bid'],$_SESSION['area']);
							$stmtheadT->execute();
							$stmtheadT->store_result(); 
							$stmtheadT->bind_result($Crew,$SCCode);
							$SHrowcountT = $stmtheadT->num_rows;
							$counterT = 0;
							while($stmtheadT->fetch()){
								if (++$counterT == $SHrowcountT) {
									$pivottrainee=$pivottrainee."ifnull((sum(case when CONCAT_WS(', ', ELastName, EFirstName  ) = '".$Crew."'  AND SCCode1 != 'T'   then GREATEST(SDShortPMix,0) end) * (salescrew.SCHours/SUM( case when SCCode1 != 'T' then salescrew.SCHours end ))),0.0000) '".str_replace(' ', '', $Crew)."' ";
								} else {
									$pivottrainee=$pivottrainee."ifnull((sum(case when CONCAT_WS(', ', ELastName, EFirstName  ) = '".$Crew."'  AND SCCode1 != 'T'   then GREATEST(SDShortPMix,0) end) * (salescrew.SCHours/SUM( case when SCCode1 != 'T' then salescrew.SCHours end ))),0.0000) '".str_replace(' ', '', $Crew)."', ";
								}
							}						
						}*/
						$query="SELECT inventorycontrol.ICDate AS DATE, inventorycontrol.ICNumber AS DIR, SDShortPMix AS MIX, SDOtherSupplies AS  'OtherSupplies', SDShortCashFund AS  'CashFund', SDShortTotal AS  'TotalShortages', COUNT( salescrew.unEmployee ) AS 'NoOfCrew', SDAdjPMix, SDAdjWater, SDAdjSugar, SDAdjSupplies, SDAdjCashFund,
							".$pivotstmt."  
							FROM  `salesdata`
							INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salesdata.`unInventoryControl` 
							INNER JOIN salescrew ON salescrew.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
							INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee 
							WHERE salesdata.unBranch = ? AND salesdata.unArea= ? AND salescrew.unEmployee > 0 AND salescrew.Status = 1
							AND  `ICDate` 
							BETWEEN   ".$prevdate." AND ".$nextdate."
							GROUP BY ICDate, ICNumber
							ORDER BY ICDate ASC ";
						$queryT="SELECT inventorycontrol.ICDate AS DATE,
							".$pivottrainee."  
							FROM  `salesdata`
							INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salesdata.`unInventoryControl` 
							INNER JOIN salescrew ON salescrew.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
							INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee 
							WHERE salesdata.unBranch = ? AND salesdata.unArea= ? AND salescrew.unEmployee > 0 AND salescrew.Status = 1 
							AND  `ICDate` 
							BETWEEN   ".$prevdate." AND ".$nextdate."
							GROUP BY ICDate, ICNumber
							ORDER BY ICDate ASC ";
						if($stmtT->prepare($queryT)){
							$stmtT->bind_param('ii',$_GET['bid'],$_SESSION['area']);
							$stmtT->execute();
							$metaT = $stmtT->result_metadata();
							$resultsT[$i] = array();
							
							while ($fieldT = $metaT->fetch_field()) { 
								$varT = $fieldT->name;
								$$varT = null; 
								$helloT = &$$varT;
								$fieldsT[$varT] = &$$varT;
							}
							
							
							call_user_func_array(array($stmtT,'bind_result'),$fieldsT);
							$h = 1;
							while ($stmtT->fetch()) {
								$mT = date_parse_from_format("Y-m-d", $DATE);	
								foreach($fieldsT as $k => $v):
									$resultsT[$h][$k] = $v; 
								endforeach;	
								$h++;
							}
							
						}
						if($stmt->prepare($query)){
							$stmt->bind_param('ii',$_GET['bid'],$_SESSION['area']);
							$stmt->execute();
							$TotalSales=0;
							$rowcount=0;
							
							
							// Get metadata for field names
							$meta = $stmt->result_metadata();

							// This is the tricky bit dynamically creating an array of variables to use
							// to bind the results
							while ($field = $meta->fetch_field()) { 
								$var = $field->name;
								$$var = null; 
								$hello = &$$var;
								$fields[$var] = &$$var;
							}
							
							$fieldCount = count($fields);
							call_user_func_array(array($stmt,'bind_result'),$fields);
							$currMonth = '';
							$i = 1;
							$totalShortPerCrew = array();
							foreach ($fieldss as $j) {
								$totalShortPerCrew[$j] = 0;
							} 
							//echo $totalShortPerCrew['CashFund'];
							while ($stmt->fetch()) {
								$m = date_parse_from_format("Y-m-d", $DATE);
								$monthObj   = DateTime::createFromFormat('!m', $m["month"]);
								$monthName = $monthObj->format('F');
								$currBranch = 0;
								$results[$i] = array();
								$totalSalesPerBr = 0;
								foreach($fields as $k => $v):
									$results[$i][$k] = $v;
									if($k=='DATE'){
										if($currMonth!=$monthName){
											echo '<tr><th>'.strtoupper($monthName).'</th></tr>';
											$currMonth=$monthName;
											unset($row);
											$row = array();
											$row[] = $monthName;
											$export[]=$row;
										}
										echo '<tr><th>'.date('d', strtotime($DATE)).'</th>';
										unset($row);
										$row = array();
										$row[] = date('d', strtotime($DATE));
									}else if($k=='DIR'){
										echo '<td style="text-align: center">'.$v.'</td>';
										$row[] = $v;
									}else if($k=='MIX'){
										echo '<td style="text-align: center">'.number_format(max(0, $v),2).'</td>';
										$row[] = number_format(max(0, $v),2);
										$totalShortPerCrew[$k] += max(0, $v);
									}else if($k=='OtherSupplies'){
										echo '<td style="text-align: center">'.number_format(max(0, $v),2).'</td>';
										$row[] = number_format(max(0, $v),2);
										$totalShortPerCrew[$k] += max(0, $v);
									}else if($k=='CashFund'){
										echo '<td style="text-align: center">'.number_format(max(0, $v),2).'</td>';
										$row[] = number_format(max(0, $v),2);
										$totalShortPerCrew[$k] += max(0, $v);
									}else if($k=='TotalShortages'){ 
										echo '<td style="text-align: center">'.number_format(max(0, $v),2).'</td>';
										$row[] = number_format(max(0, $v),2);
										$totalShortPerCrew[$k] += max(0, $v);
									}else if($k=='NoOfCrew'){
										echo '<td style="text-align: center">'.$v.'</td>';
										$row[] = $v;
									}else if($k=='SDAdjPMix'){
									}else if($k=='SDAdjWater'){
									}else if($k=='SDAdjSugar'){
									}else if($k=='SDAdjSupplies'){
									}else if($k=='SDAdjCashFund'){
									}else{
										  //$w = $resultsT[$i][$k] + $v;
										  echo '<td style="text-align: center">'.number_format(max(0, $v),2).'</td>'; 
										  $row[] = number_format(max(0, $v),2);
										  $totalSalesPerBr += max(0, $v); $totalShortPerCrew[$k] += max(0, $v);
									}
								endforeach;
								echo '<th>'.number_format($totalSalesPerBr,2).'</th>';
								$row[] = $totalSalesPerBr;
								$export[]=$row;
								$TotalSales += $totalSalesPerBr;
								$i++;
								echo '</tr>';
								/*if(date("Y-m-t", strtotime($Date)) == $Date ){ //|| idate('d', $Date)
											echo '<tr><th></th>';
											foreach($fieldss as $l):
												echo '<td><b>'.number_format($totalSalesPerMo[$l],2).'</b></td>';
												//$totalSalesPerMo[$totalPerMo] = 0;
											endforeach;
											echo '</tr>';
										}*/
							}
							foreach($fieldss as $l):
								$l=str_replace('$', '', $l);
								if($l=='DATE'){
									//$totalSalesPerMo[$totalPerMo] = 0;
									 echo '<th><b></b></th>';
									 //$CSVTotal[] = 'DATE';
								}else if($l=='DIR'||$l=='NoOfCrew'){
									//$totalSalesPerMo[$totalPerMo] = 0;
									 echo '<th><b></b></th>';
									 $CSVTotal[] = '';
								}else if($l=='MIX'||$l=='OtherSupplies'||$l=='CashFund'||$l=='TotalShortages'){ 
									echo '<th><b>'.number_format($totalShortPerCrew[$l],2).'</b></th>'; 
									$CSVTotal[] = number_format($totalShortPerCrew[$l],2);
								}else{ 
									echo '<th><b>'.number_format($totalShortPerCrew[$l],2).'</b></th>'; 
									$CSVTotal[] = number_format($totalShortPerCrew[$l],2);
									if($totalShortPerCrew[$l]>0){$summaryamt[] = '<th>'.number_format($totalShortPerCrew[$l],2).'</th><th>_________________________</th></tr>';}
								}
							endforeach;
						}
						//$row = array('TOTAL','',number_format($TotalSales,2));
						$CSVTotal[] = number_format($TotalSales,2);
						$export[]=$CSVTotal;
				?></table><?PHP
			}else{
				$export=array();
						$row = array('ID','DATE','NET SALES');
						$export[]=$row;
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if(isset($_GET['filter'])){
							/*PROOF
								SELECT unSalesData, SDTimeStart, SDNetSales
								FROM salesdata
								WHERE (
								SDTimeStart
								BETWEEN  '2015-01-01'
								AND  '2015-01-31'
								)
								AND unBranch =5
								AND SDState =  'Close'
								AND unInventoryControl !=0
								ORDER BY SDTimeStart
								LIMIT 0 , 30
							*/
							$query = "SELECT salescontrol.unSalesControl, SCTimeStart, SUM(SCNetSales)
											FROM  salescontrol 
											INNER JOIN salesdata
											ON salesdata.unSalesControl=salescontrol.unSalesControl
											WHERE (salesdata.SDTimeStart BETWEEN ? AND ?) AND salescontrol.unBranch=?
											AND SDState ='Close' GROUP BY SCTimeStart";
									/*"SELECT idSalesData, ICDate, SUM( SDTotalSales ) 
									FROM salesdata
									INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
									WHERE 
									(
									ICDate
									BETWEEN  ?
									AND  ?
									)
									AND salesdata.unInventoryControl !=0
									AND salesdata.unBranch =?
									GROUP BY idSalesData";*/
						}else{
							$query = "SELECT salescontrol.unSalesControl, SCTimeStart, SUM(SCNetSales)
											FROM  salescontrol 
											INNER JOIN salesdata
											ON salesdata.unSalesControl=salescontrol.unSalesControl
											WHERE salesdata.unInventoryControl=? AND salescontrol.unBranch=?
											AND SDState ='Close' GROUP BY SCTimeStart";
									/*"SELECT idSalesData, ICDate, SUM( SDTotalSales ) 
									FROM salesdata
									INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
									WHERE salesdata.unInventoryControl =?
									AND salesdata.unBranch =?
									GROUP BY ICDate";*/
						}
						if($stmt->prepare($query)){
							if(isset($_GET['filter'])){
								$stmt->bind_param('ssi',$_GET['dfrom'],$_GET['dto'],$_GET['bid']);
							}else{
								$stmt->bind_param('ii',$_GET['did'],$_GET['bid']);
							}
							//$stmt->bind_param('ii',$_GET['did'],$_GET['bid']);
							$stmt->execute();
							$stmt->bind_result($unSalesControl,$SCTimeStart,$SCNetSales);
							$TotalSales=0;
							while($stmt->fetch()){
								$TotalSales+=$SCNetSales;
								$unSalesControl1=sprintf('%06d',$unSalesControl);
								$row = array($unSalesControl1,date('F d, Y',strtotime($SCTimeStart)),$SCNetSales);
								$export[]=$row;
								?>
								<div class="rptlistviewitem" style="cursor:default; background-color:transparent;">
									<div class="rptlistviewsubitem"  style="width:10%;"><?php echo $unSalesControl1; ?></div>
									<div class="rptlistviewsubitem" style="width:20%;"><?php echo date('F d, Y',strtotime($SCTimeStart)); ?></div>
									<div class="rptlistviewsubitem" style="width:9%;"><?php echo $SCNetSales; ?></div>
								</div>
								<?php
							}
						}
						$row = array('TOTAL','',number_format($TotalSales,2));
						$export[]=$row;
			}

		?>	
             <div class="rptlistviewitem" style="background-color:#FFF; border-top:2px solid #000; width: 100%">
                    <div class="rptlistviewsubitem" style="font-weight:bold; width:30%; position: relative; display: inline-grid" >GRAND TOTAL</div>
                    <?php
				 		
				 	?>
                    <div class="rptlistviewsubitem" style="width:10%;"><input readonly class="listviewsubitem" type="text" style="font-weight:bold; border:none; background-color:transparent; float: right; position: absolute; right:8%" value="<?php echo number_format($TotalSales,2); ?>" ></div>
            </div>
            
            <div class="rptsum" id="shortagerptsum" style=" width:60%; padding:50px 0; position: relative; display: inline-block; margin:50px 0; border-top: #000 thick dashed;">
            	 <h2>WAFFLETIME INC.</br>SHORTAGES PER OUTLET</br><?php echo $prevdateF.' - '.$nextdateF; ?></h2>
            	 <table style="width:80%; margin:0 10%; border: none; border-collapse: collapse;" id="shortagesrptperstoretab">
            	 	<tr><th style="float: left"><?php echo strtoupper($BName); ?></th><th></th><th></th><td><i>SIGNATURE</i></td></tr>
            	 	<?php 
					 	/*for(i=0;i<50;i++;){
							echo '<tr>----------------------------</tr>';	
						}*/
					 	 $m = 0;
						 foreach($summaryamt as $l):
					 		echo $summaryname[$m];
					 		echo $l;
					 		$m++;
						 endforeach;
					 	echo '<tr><th></th><th></th><th style="padding-top:5px; border-top: thin solid #000;">'.number_format($TotalSales,2).'</th><th></th></tr>';
					?>
					<tr style="height: 50px"></tr>
					<tr><td></td><td>Prepared by:</td><td></td><td>Checked by:</td></tr>
					<tr style="height: 50px"></tr>
					<tr><td></td><th><input type="text" value="Ma. Caressa Estimada" style="text-align: center"></th><th></th><th><input type="text" value="Ajie C. Tudeja" style="text-align: center"></th></tr>
					<tr><td></td><th>Acct. Officer</th><th></th><th>Bus. Unit Accountant</th></tr>
					<tr style="height: 30px"></tr>
					<tr><td></td><td></td><td>Received by:</td><td></td></tr>
					<tr style="height: 50px"></tr>
					<tr><td></td><th><input type="text" value="Gellyn Ann Casiano" style="text-align: center"></th><th></th><th><input type="text" value="Ethel Leigh Chin" style="text-align: center"></th></tr>
					<tr><td></td><th>HR Manager</th><th></th><th>Op. Director</th></tr>
					<tr style="height: 50px"></tr>
				 </table>
			</div>
            <div class="rptsum" id="shortagerptsumwhole" style=" width:100%; padding:50px 0; position: relative; display: inline-block; margin-bottom:50px; border-top: #000 thick dashed;">
            	<h2>WAFFLETIME INC.</br>SUMMARY of SHORTAGES</br><?php echo $prevdateF.' - '.$nextdateF; ?></h2>
				<table style="width:60%; margin:0 20%; border: 1px solid #000; border-collapse: collapse;" id="shortagesrptsumtab">
					<tr><th style="width:50%; border: 1px solid #000; border-collapse: collapse;">Name</th><th  style="width:50%; border: 1px solid #000; border-collapse: collapse;">Amount</th></tr>
					<?php
						$sumcrewkey = array();
						$sumcrewval = array();
						$recentday = '';
						$recentshort = 0;
						$recentcrewcnt = 0;
						$recenthrs = 0;
						$prevday = '';
						$prevshort = 0;
						$prevcrewcnt = 0;
						$prevhrs = 0;
						$res = 1;
						$totalShortSummary = 0;
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						/*$stmtsum = $mysqli->stmt_init();
						$stmtsumT = $mysqli->stmt_init();
						$stmtheadsum = $mysqli->stmt_init();
						$stmtheadsumT = $mysqli->stmt_init();
						$sumheader="SELECT CONCAT_WS(  ', ', ELastName, EFirstName )  'Crew', SCCode1
							FROM  `salescrew` 
							INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salescrew.`unInventoryControl` 
							INNER JOIN salesdata ON salesdata.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
							INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee
							WHERE salescrew.Status =1
							AND salescrew.unEmployee > 0
							AND `ICDate`
							BETWEEN   ".$prevdate." AND ".$nextdate."  AND salesdata.unArea=? 
							GROUP BY ELastName
							ORDER BY ELastName, ICDate  ASC";
						$sumheaderT="SELECT CONCAT_WS(  ', ', ELastName, EFirstName )  'Crew', SCCode1
							FROM  `salescrew` 
							INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salescrew.`unInventoryControl` 
							INNER JOIN salesdata ON salesdata.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
							INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee
							WHERE salescrew.Status =1
							AND SCCode1 != 'T'
							AND salescrew.unEmployee > 0
							AND `ICDate`
							BETWEEN   ".$prevdate." AND ".$nextdate."  AND salesdata.unArea=? 
							GROUP BY ELastName
							ORDER BY ELastName, ICDate  ASC";
						$pivotstmtsum =' ';
						$pivottraineesum = ' ';
						if($stmtheadsum->prepare($sumheader)){
							$stmtheadsum->bind_param('i',$_SESSION['area']);
							$stmtheadsum->execute();
							$stmtheadsum->store_result(); 
							$stmtheadsum->bind_result($Crew,$SCCode);
							$SHrowcountSum = $stmtheadsum->num_rows;
							$counter = 0;
							while($stmtheadsum->fetch()){
								if (++$counter == $SHrowcountSum) {
										$pivotstmtsum=$pivotstmtsum."ifnull((sum(case when CONCAT_WS(', ', ELastName, EFirstName  ) = '".$Crew."'  then SDShortWOMix end) * (salescrew.SCHours/SUM( salescrew.SCHours ))),0.0000) '".str_replace(' ', '', $Crew)."' ";
								} else {
										$pivotstmtsum=$pivotstmtsum."ifnull((sum(case when CONCAT_WS(', ', ELastName, EFirstName  ) = '".$Crew."' then SDShortWOMix end) * (salescrew.SCHours/SUM( salescrew.SCHours ))),0.0000) '".str_replace(' ', '', $Crew)."', ";
								}
							}
						}
						if($stmtheadsumT->prepare($sumheaderT)){
							$stmtheadsumT->bind_param('i',$_SESSION['area']);
							$stmtheadsumT->execute();
							$stmtheadsumT->store_result(); 
							$stmtheadsumT->bind_result($Crew,$SCCode);
							$SHrowcountSumT = $stmtheadsumT->num_rows;
							$counterT = 0;
							while($stmtheadsumT->fetch()){
								if (++$counterT == $SHrowcountSumT) {
									$pivottraineesum=$pivottraineesum."ifnull((sum(case when CONCAT_WS(', ', ELastName, EFirstName  ) = '".$Crew."'  AND SCCode1 != 'T'   then GREATEST(SDShortPMix,0) end) * (salescrew.SCHours/SUM( case when SCCode1 != 'T' then salescrew.SCHours end ))),0.0000) '".str_replace(' ', '', $Crew)."' ";
								} else {
									$pivottraineesum=$pivottraineesum."ifnull((sum(case when CONCAT_WS(', ', ELastName, EFirstName  ) = '".$Crew."'  AND SCCode1 != 'T'   then GREATEST(SDShortPMix,0) end) * (salescrew.SCHours/SUM( case when SCCode1 != 'T' then salescrew.SCHours end ))),0.0000) '".str_replace(' ', '', $Crew)."', ";
								}
							}
						}
						$sumquery="SELECT inventorycontrol.ICDate AS DATE, BName AS BRANCH,
							".$pivotstmtsum."  
							FROM  `salesdata`
							INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salesdata.`unInventoryControl` 
							INNER JOIN branch ON branch.unBranch = inventorycontrol.unBranch
							INNER JOIN salescrew ON salescrew.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
							INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee 
							WHERE salesdata.unArea= ? AND salescrew.unEmployee > 0 AND salescrew.Status = 1
							AND  `ICDate` 
							BETWEEN   ".$prevdate." AND ".$nextdate."
							GROUP BY ICDate, ICNumber
							ORDER BY ICDate ASC ";
						$sumqueryT="SELECT inventorycontrol.ICDate AS DATE, BName AS BRANCH,
							".$pivottraineesum."  
							FROM  `salesdata`
							INNER JOIN inventorycontrol ON inventorycontrol.`unInventoryControl` = salesdata.`unInventoryControl` 
							INNER JOIN branch ON branch.unBranch = inventorycontrol.unBranch
							INNER JOIN salescrew ON salescrew.`unInventoryControl` = inventorycontrol.`unInventoryControl` 
							INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee 
							WHERE salesdata.unArea= ? AND salescrew.unEmployee > 0 AND salescrew.Status = 1 
							AND  `ICDate` 
							BETWEEN   ".$prevdate." AND ".$nextdate."
							GROUP BY ICDate, ICNumber
							ORDER BY ICDate ASC ";
						//echo $sumqueryT;
						if($stmtsum->prepare($sumquery)){
							$stmtsum->bind_param('i',$_SESSION['area']);
							$stmtsum->execute();
							$metasum = $stmtsum->result_metadata();
							$resultsSum[$i] = array();
							
							while ($fieldsum = $metasum->fetch_field()) { 
								$varsum = $fieldsum->name;
								$$varsum = null; 
								$hellosum = &$$varsum; 
								$fieldssum[$varsum] = &$$varsum;
							}
							
							
							call_user_func_array(array($stmtsum,'bind_result'),$fieldssum);
							$h = 1;
							while ($stmtsum->fetch()) {
								$msum = date_parse_from_format("Y-m-d", $DATE);	
								foreach($fieldssum as $k => $v):
									$resultsSum[$h][$k] = $v; 
								endforeach;	
								$h++;
							}
							
						}
						if($stmtsumT->prepare($sumqueryT)){
							$stmtsumT->bind_param('i',$_SESSION['area']);
							$stmtsumT->execute();
							$metasumT = $stmtsumT->result_metadata();
							$resultsSumT[$i] = array();
							
							while ($fieldT = $metasumT->fetch_field()) { 
								$varT = $fieldT->name;
								$$varT = null; 
								$helloT = &$$varT;
								$fieldsT[$varT] = &$$varT;
							}
							
							
							call_user_func_array(array($stmtsumT,'bind_result'),$fieldsT);
							$h = 1;
							while ($stmtsumT->fetch()) {
								$mT = date_parse_from_format("Y-m-d", $DATE);
								foreach($fieldsT as $k => $v):
									$resultsSumT[$h][$k] = $v; 
								endforeach;	
								$h++;
							}
						}
						foreach($resultsSum as $array){
							foreach($array as $key=>$value){
								if($value > 0 && $key != 'DATE' ){
									//if($key == 'BRANCH'){ echo $value ; }
									echo $key;
								}	  
							}
						}*/
						/*foreach($resultsSumT as $array){
							foreach($array as $key=>$value){
								if($value > 0 && $key != 'DATE'){
									if($key == 'BRANCH'){ echo $value ; }else{echo " $key / $value <br />";}
								}	  
							}
						}*/
						$stmtsumshort = $mysqli->stmt_init();
						if($stmtsumshort->prepare("SELECT CONCAT_WS(  ', ', ELastName , EFirstName)  'Crew', count(salescrew.unEmployee), SCHours, IFNULL(`SDShortTotal`,0), ICDate, salesdata.unBranch FROM `salesdata` INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl INNER JOIN salescrew ON salescrew.`unInventoryControl` = inventorycontrol.`unInventoryControl` INNER JOIN employee ON employee.unEmployee = salescrew.unEmployee WHERE ICDate BETWEEN   ".$prevdate." AND ".$nextdate." AND salesdata.unArea=? AND salescrew.Status=1 AND SDShortTotal >0 GROUP BY salescrew.unEmployee, ICDate, unBranch  ORDER BY unBranch, ICDate, ELastName")){
							$stmtsumshort->bind_param('i',$_SESSION['area']);
							$stmtsumshort->execute();
							$stmtsumshort->store_result(); 
							$stmtsumshort->bind_result($Crew, $count, $Hours, $TotalShort, $ICDate, $branch);
							$rescnt = $stmtsumshort->num_rows;
							while($stmtsumshort->fetch()){
								if($res==$rescnt){
									if($recentday!=$ICDate){
										foreach($reccrewkey as $pck => $pckhr){
											$recentcrewcnt = 0;
											$recenthrs = 0;
											$recentday = $ICDate;
											$recentshort = max(floatval($TotalShort),0);
											$recentcrewcnt += floatval($count);
											$recenthrs += floatval($Hours);
											unset($reccrewkey);
											$reccrewkey = array();
											//$crewarr = str_replace(' ', '', $Crew);
											$reccrewkey = array_merge($reccrewkey, array($Crew=>$Hours));
											if($recenthrs>0){
												$percpercrew = (floatval($pckhr)/floatval($recenthrs))*100;
												$shortpercrew = (floatval($recentshort)*floatval($percpercrew))/100;
												if($shortpercrew > 0){
													if (array_key_exists($pck, $sumcrewval)) {
														$currval = $sumcrewval[$pck];
														$newval = floatval($currval)+floatval($shortpercrew);
														$sumcrewval = array_merge($sumcrewval, array($pck=>$newval));
														$totalShortSummary += $shortpercrew;
														//$sumcrewval = array_merge($sumcrewval, array($pck.'-'.$res.'-'.$recentday.'-duplicate' => $recenthrs.'--'.$percpercrew.' x '.$recentshort.' = '.$shortpercrew));
													}else{
														$sumcrewval = array_merge($sumcrewval, array($pck=>$shortpercrew));
														$totalShortSummary += $shortpercrew;
													}
													//$sumcrewval = array_merge($sumcrewval, array($pck=>$shortpercrew));
													//echo '<tr><th style="border: 1px solid #000;">'.$pck.'</th><th style="border: 1px solid #000;">'.$percpercrew.' x '.$recentshort.' = '.$shortpercrew.'</th></tr>';
												}
											}
										}
									}else{
										$recentcrewcnt += floatval($count);
										$recenthrs += floatval($Hours);
										$reccrewkey = array_merge($reccrewkey, array($Crew=>$Hours));
										foreach($reccrewkey as $pck => $pckhr){
											if($recenthrs>0){
												$percpercrew = (floatval($pckhr)/floatval($recenthrs))*100;
												$shortpercrew = (floatval($recentshort)*floatval($percpercrew))/100;
												if($shortpercrew > 0){
													if (array_key_exists($pck, $sumcrewval)) {
														$currval = $sumcrewval[$pck];
														$newval = floatval($currval)+floatval($shortpercrew);
														$sumcrewval = array_merge($sumcrewval, array($pck=>$newval));
														$totalShortSummary += $shortpercrew;
														//$sumcrewval = array_merge($sumcrewval, array($pck.'-'.$res.'-'.$recentday.'-duplicate' => $recenthrs.'--'.$percpercrew.' x '.$recentshort.' = '.$shortpercrew));
													}else{
														$sumcrewval = array_merge($sumcrewval, array($pck=>$shortpercrew));
														$totalShortSummary += $shortpercrew;
													}
													//$sumcrewval = array_merge($sumcrewval, array($pck=>$shortpercrew));
													//echo '<tr><th style="border: 1px solid #000;">'.$pck.'</th><th style="border: 1px solid #000;">'.$percpercrew.' x '.$recentshort.' = '.$shortpercrew.'</th></tr>';
												}
											}
										}
									}
								}else{
									if($recentday!=$ICDate){
										foreach($reccrewkey as $pck => $pckhr){
											if($recenthrs>0){
												$percpercrew = (floatval($pckhr)/floatval($recenthrs))*100;
												$shortpercrew = (floatval($recentshort)*floatval($percpercrew))/100;
												if($shortpercrew > 0){
													if (array_key_exists($pck, $sumcrewval)) {
														$currval = $sumcrewval[$pck];
														$newval = floatval($currval)+floatval($shortpercrew);
														$sumcrewval = array_merge($sumcrewval, array($pck=>$newval));
														$totalShortSummary += $shortpercrew;
														//$sumcrewval = array_merge($sumcrewval, array($pck.'-'.$res.'-'.$recentday.'-duplicate' => $recenthrs.'--'.$percpercrew.' x '.$recentshort.' = '.$shortpercrew));
													}else{
														$sumcrewval = array_merge($sumcrewval, array($pck=>$shortpercrew));
														$totalShortSummary += $shortpercrew;
													}
													//$sumcrewval = array_merge($sumcrewval, array($pck=>$shortpercrew));
													//echo '<tr><th style="border: 1px solid #000;">'.$pck.'</th><th style="border: 1px solid #000;">'.$percpercrew.' x '.$recentshort.' = '.$shortpercrew.'</th></tr>';
												}
											}
										}
										$prevday = $recentday;
										$prevshort = $recentshort;
										$prevcrewcnt = $recentcrewcnt;
										$prevhrs = $recenthrs;
										$recentcrewcnt = 0;
										$recenthrs = 0;
										$recentday = $ICDate;
										$recentshort = max(floatval($TotalShort),0);
										$recentcrewcnt += floatval($count);
										$recenthrs += floatval($Hours);
										unset($reccrewkey);
										$reccrewkey = array();
										//$crewarr = str_replace(' ', '', $Crew);
										$reccrewkey = array_merge($reccrewkey, array($Crew=>$Hours));
									}else{
										//echo '<tr><th>'.$recentday.'</th><th>count='.$recentcrewcnt.'--hours:'.$recenthrs.'</th></tr>';
										$recentcrewcnt += floatval($count);
										$recenthrs += floatval($Hours);
										//$crewarr = str_replace(' ', '', $Crew);
										$reccrewkey = array_merge($reccrewkey, array($Crew=>$Hours));
										//echo '<tr><th>'.$recentday.'</th><th>count='.$recentcrewcnt.'--hours:'.$recenthrs.'</th></tr>';
									}
								}
								$res++;
							}
							ksort($sumcrewval);
							foreach($sumcrewval as $sck => $sckamt){
								echo '<tr><td style="border: 1px solid #000; text-align:left">'.strtoupper($sck).'</td><td style="border: 1px solid #000; text-align:right">'. number_format($sckamt,2) .'</td></tr>';
							}
						}
					?>
					<tr><th style="width:50%; border: 1px solid #000; border-collapse: collapse;">TOTAL SHORTAGES</th><th  style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;" id="summarytotal"><?php echo number_format($totalShortSummary,2); ?></th></tr>
				</table>
				<table style="width:60%; margin:70px 20%; border: none; border-collapse: collapse;">
					<tr style="text-align: left"><td>Prepared by:</td><td></td><td>Checked by:</td></tr>
					<tr style="height: 50px"></tr>
					<tr><th><input type="text" value="Ma. Caressa Estimada" style="text-align: center"></th><th></th><th><input type="text" value="Ajie C. Tudeja" style="text-align: center"></th></tr>
					<tr><th>Acct. Officer</th><th></th><th>Bus. Unit Accountant</th></tr>
					<tr><th></th><td>Received by:</td><th></th></tr>
					<tr  style="height: 50px"></tr>
					<tr><th><input type="text" value="Abigail Joy. A. Gonzales" style="text-align: center"></th><th><input type="text" value="Ethel Leigh Chin" style="text-align: center"></th><th><input type="text" value="Gellyn Ann Casiano" style="text-align: center"></th></tr>
					<tr><th>Asst. Payroll Officer</th><th>Opt. Director</th><th>HR Manager</th></tr>
				</table>
			</div>
    </div>
</div>



<?php /*require('footer.php');*/ $_SESSION['dailysalesreport'] = $export; ?>
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
function FilterReport(dFrom,dTo){
	//alert(dFrom);
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
	<?php /*if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){?>
	<?php }else{ */?>
    	<input class="exemptPrint" type="text" value="From" style="width:35px; margin-left:5px; border:none; background-color:transparent;" readonly>
    	<input class="exemptPrint" type="date" id="dtpFrom" name="dtpFrom" value="<?php echo (isset($_GET['dfrom']))?$_GET['dfrom']:'';?>" >
    	<input class="exemptPrint" type="text" value="To" style="width:20px; margin-left:5px; border:none; background-color:transparent;" readonly>
    	<input class="exemptPrint" type="date" id="dtpTo" name="dtpTo" value="<?php echo (isset($_GET['dto']))?$_GET['dto']:'';?>" >
    	<input class="exemptPrint" type="button" value="Go" onClick="FilterReport(dtpFrom.value,dtpTo.value)">
    	
    	<!--<input type="text" style="border:none; background-color:transparent; float:right; margin-top:5px; margin-right:5px; text-align:right; width:300px;" value="<?php echo (isset($_GET['dfrom']))?ShowDateFilter():PMixPeriod();?>" readonly>-->
	<?php //} ?>
</div>
<div style = "overflow-x:auto;">
<div class="rptlistview" id="dsrlistview" style="width:2500px; padding-right: 10px">
	<?php if($_SESSION['BusinessUnit']!="Waffletime Inc.,"){?>
	<div class="rptcolumn">
   		
   			<!--<div class="rptcolumnheader" style="width:10%;">Branch</div>
			<div class="rptcolumnheader" style="width:20%;">Date</div>
			<div class="rptcolumnheader" style="width:9%; text-align:right;">Net Sales</div>-->
			<div class="rptcolumnheader" style="width:10%;">ID</div>
			<div class="rptcolumnheader" style="width:20%;">Date</div>
			<div class="rptcolumnheader" style="width:9%; text-align:right;">Net Sales</div>
    </div>
        <?php } ?>
    	
    	<?php
			//echo $_SESSION['area'];
			if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			?><div class="column" id="dsrcolumn" style="height: 60px;padding-left: 0px;"><?PHP
						if(isset($_GET['filter'])){
							$prevdate = "'".$_GET['dfrom']."'";
							$nextdate = "'".$_GET['dto']."'";
							$dateset = explode('-', $prevdate);
							$curryear = $dateset[0];
						}else{
							$currdate = ExecuteReader("Select ICDate as `result` From inventorycontrol Where `unBranch`=".$_SESSION['bid']." AND `unInventoryControl`=".$_SESSION['did']);
							$dateset = explode('-', $currdate);
							$lastday = date('t',strtotime($currdate));
							$curryear = $dateset[0];
							$currmonth = $dateset[1];
							$prevmonth = (int)$currmonth-1;
							$nextmonth = (int)$currmonth+1;
							$prevdate = "'".$curryear."-".$currmonth."-1'";
							$nextdate = "'".$curryear."-".$currmonth."-".$lastday."'";
						}
						//echo $prevdate.'-----'.$nextdate;
						$export=array();
						$col = array();
						$row = array();
						$col[] = 'DATE';
						$fieldss = array();
						$fieldss[] = '$Date';
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						$stmthead = $mysqli->stmt_init();
						$queryheader="SELECT BName
								FROM salesdata
								INNER JOIN branch ON branch.`unBranch` = salesdata.`unBranch` 
								INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
								WHERE  `ICDate` 
								BETWEEN  ".$prevdate."
								AND  ".$nextdate."
								AND branch.`unArea`=?
								GROUP BY BName
								ORDER BY ICDate ASC";
						$pivotstmt =' ';
						if($stmthead->prepare($queryheader)){
							$stmthead->bind_param('i',$_SESSION['area']);
							$stmthead->execute();
							$stmthead->store_result(); 
							$stmthead->bind_result($BName);
							$SHrowcount = $stmthead->num_rows;
							$counter = 0;
							//echo '<tr><th style="width:100px; margin:0;"><h2><select><option>'.$curryear.'</option></select></h2></th>';
							echo "<div class='columnheader' style='text-align:center; width:70px'></div>";
							while($stmthead->fetch()){
								if (++$counter == $SHrowcount) {
									$pivotstmt=$pivotstmt."ifnull(sum(case when BName = '".$BName."' then SDTotalSales end),0.0000) '".str_replace(' ', '', $BName)."' ";
								} else {
									$pivotstmt=$pivotstmt."ifnull(sum(case when BName = '".$BName."' then SDTotalSales end),0.0000) '".str_replace(' ', '', $BName)."', ";
								}
								//echo '<th style="width:100px; margin:0;">'.$BName.'</th>';
								echo "<div class='rptcolumnheader' style='text-align:center; width:72px; margin:1px'>".$BName."</div>";
								$fieldss[] = str_replace(' ', '', $BName);
								$col[] = $BName;
								//$fieldss[] = $BName;
							}
							$col[] = 'TOTAL';
							$export[]=$col;
							//echo $pivotstmt;
							//echo '<th>TOTAL</th></tr>';
							echo '<div class="rptcolumnheader" style="text-align:center; width:70px">TOTAL</div></div><div class="rptrow"><table>';
						}
						$query="select ICDate Date,
								  ".$pivotstmt."
								from salesdata 
								INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
								INNER JOIN branch ON salesdata.unBranch = branch.unBranch
								WHERE  `ICDate` 
								BETWEEN  ".$prevdate."
								AND  ".$nextdate."
								AND branch.`unArea`=? 
								GROUP BY ICDate
								ORDER BY ICDate ASC";
						if($stmt->prepare($query)){
							$stmt->bind_param('i',$_SESSION['area']);
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
							$totalSalesPerMo = array();
							foreach ($fieldss as $j) {
								$totalSalesPerMo[$j] = 0;
							}
							while ($stmt->fetch()) {
								$row = array();
								$m = date_parse_from_format("Y-m-d", $Date);
								$monthObj   = DateTime::createFromFormat('!m', $m["month"]);
								$monthName = $monthObj->format('F');
								$results[$i] = array();
								$totalSalesPerBr = 0;
								foreach($fields as $k => $v):
									$results[$i][$k] = $v;
									if($k=='Date'){
										if($currMonth!=$monthName){
											echo '<tr><th  style="width:70px; text-align: center">'.strtoupper($monthName).'</th></tr>';
											$currMonth=$monthName;
											unset($row);
											$row = array();
											$row[] = $monthName;
											$export[]=$row;
										}
										echo '<tr><th  style="width:70px; text-align: center">'.date('d', strtotime($Date)).'</th>';
										unset($row);
										$row = array();
										$row[] = date('d', strtotime($Date));
									}else{echo '<td style="width:70px; text-align: center">'.number_format($v,2).'</td>'; $totalSalesPerBr += $v; $totalSalesPerMo[$k] += $v; $row[] = number_format($v,2);}
								endforeach;
								echo '<th>'.number_format($totalSalesPerBr,2).'</th>';
								$row[] = number_format($totalSalesPerBr,2);
								$export[]=$row;
								unset($row);
								$TotalSales += $totalSalesPerBr;
								$i++;
								echo '</tr>';
								if(date("Y-m-t", strtotime($Date)) == $Date ){ //|| idate('d', $Date)
											echo '<tr><th></th>';
											$row = array(' ');
											foreach($fieldss as $l):
												if($l!='$Date'){echo '<td  style="width:70px; text-align: center"><b>'.number_format($totalSalesPerMo[$l],2).'</b></td>'; $row[] = number_format($totalSalesPerMo[$l],2);}
											$totalSalesPerMo[$l]=0;
											endforeach;
											$export[]=$row;
											unset($row);
											echo '</tr>';
										}
							}
						}
						foreach($fieldss as $l):
								$l=str_replace('$', '', $l);
								if($l=='Date'){
									//$totalSalesPerMo[$totalPerMo] = 0;
									 echo '<th><b></b></th>';
									 //$CSVTotal[] = 'DATE';
								}else { 
									echo '<th><b>'.number_format($totalSalesPerMo[$l],2).'</b></th>'; 
									//$CSVTotal[] = number_format($totalSalesPerMo[$l],2);
									//if($totalSalesPerMo[$l]>0){$summaryamt[] = '<th>'.number_format($totalShortPerCrew[$l],2).'</th><th>_________________________</th></tr>';}
								}
								endforeach;
						unset($row);
						$row = array('TOTAL','',number_format($TotalSales,2));
						$export[]=$row;
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
             <div class="rptlistviewitem" style="background-color:#FFF; border-top:2px solid #000; width: 1500px">
                    <div class="rptlistviewsubitem" style="font-weight:bold; width:74px; position: relative; display: inline-table" >GRAND TOTAL</div>
                    <?php 
				 	$totalSpan = 0;
                    foreach($fieldss as $l):
						//if($l!='$Date'){echo '<div  style="width:50px; height:14px text-align: center; margin:1px;position: relative; display: inline-table"></div>';}
				 	 $totalSpan += 67.15;
					endforeach;
				 	?>
                    <div class="rptlistviewsubitem" style="width:70px;position: relative; display: inline-table; margin-left: <?php echo $totalSpan; ?>px"><input readonly class="listviewsubitem" type="text" style="font-weight:bold; border:none; background-color:transparent; " value="<?php echo number_format($TotalSales,2); ?>" ></div>
            </div>
			
            
    </div>
</div>
</div>



<?php /*require('footer.php');*/ $_SESSION['dailysalesreport'] = $export; ?>
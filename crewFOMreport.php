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
<script src="js/manualinventory.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#export').remove();
		
		$('#monthyear').change(function() {
		  //alert( $('option:selected', this).attr('dptto') );
			var dpttoym = $('option:selected', this).attr('dptto');
			var dptfromym = $('option:selected', this).attr('dptfrom');
			FilterReport(dptfromym,dpttoym)
		})
		
		for(i=0;i<200;i++){
			if($("#fomreppoints-" + i).length > 0) {
				repcrewinc = parseFloat($("#fomreppoints-" + i).val())
				totalPercent = 0;
				totalInc = 0;
				for(j=0;j<10;j++){
					if($("#fomrepcrewhr-" + i + "-" + j).length > 0) {
						perchr = parseFloat(parseFloat($("#fomrepcrewhr-" + i + "-" + j).val())/parseFloat($("#fomrepcrewtotalhr-" + i).val()))*100;
						$("#fomrepcrewperc-" + i + "-" + j).val(perchr.toFixed(2)+'%');
						perinc = parseFloat(repcrewinc)*parseFloat(parseFloat(perchr)/100);
						$("#fomrepcrewinc-" + i + "-" + j).val(perinc.toFixed(2));
						totalInc += parseFloat(perinc);
						totalPercent = parseFloat(totalPercent) + parseFloat(perchr);
					}
				}
				$("#fomrepcrewtotalperc-" + i).val(totalPercent.toFixed(2)+'%');
				$("#fomrepcrewtotalinc-" + i).val(totalInc.toFixed(2));
				//alert($("#fomreppoints-" + i).val());
			}
		}
		$('.fomrptsumcrewtr').remove();
		$($('.fomrptrow .fomrptccrew').get().reverse()).each(function(){
			repcstr = $(this).text().toUpperCase();
			if(repcstr!=''){
				if ($(".fomrptsumrow .fomrptsumcrew")[0]){
					same = 0;
					$('.fomrptsumrow .fomrptsumcrew').each(function(){
						repsumname = $(this).text().toUpperCase();
						if(repcstr == repsumname){
							same++;
						}
						/*$('#incentivesrptsumtab tr:nth-child(2)').after('<tr class="rptsumrow rptsumcrewtr"><td class="rptsumcrew" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repcstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');*/
					});
					if(same == 0){
						$('#incentivesrptsumtab tr:nth-child(2)').after('<tr class="fomrptsumrow fomrptsumcrewtr fomrptsumcrewtrsort"><td class="fomrptsumcrew rptsumcrew" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repcstr.toUpperCase()+'</td><td class="fomrptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
					}
				}else{
					$('#incentivesrptsumtab tr:nth-child(2)').after('<tr class="fomrptsumrow fomrptsumcrewtr fomrptsumcrewtrsort"><td class="fomrptsumcrew rptsumcrew" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repcstr.toUpperCase()+'</td><td class="fomrptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
				}
			}
		});
		$('.fomrptrow .fomrptamt').each(function(){
		  	amt = $(this).val();
			str = $(this).parent().siblings('.fomrptcrew').text().toUpperCase();
			if(str == null || str == ''){
				str = $(this).parent().siblings('.fomrptcrew').find('input').val().toUpperCase();
			}
			if(amt == null || amt == ''){
				amt = 0.00;
			}
			//alert(amt);
			$('.fomrptsumrow .fomrptsumamt').each(function(){
				sumrowamt = $(this).text();
				//alert(sumrowamt);
				sumstr = $(this).siblings('.fomrptsumcrew').text().toUpperCase();
				//alert(sumstr);
				if(sumstr==str){
					sumamt = parseFloat(amt) + parseFloat(sumrowamt);
					$(this).text(addCommas(sumamt.toFixed(2)));
					//summarytotal += parseFloat(sumamt);
				}
			});
		});
		fomsummarytotal = 0;
		$('.fomrptsumrow .fomrptsumamt').each(function(){
			fomsumrowamtfin = $(this).text();
			fomsumrowamtfin=fomsumrowamtfin.replace(/\,/g,'');
			fomsummarytotal = parseFloat(fomsummarytotal)+Math.max(0, parseFloat(fomsumrowamtfin));
			//alert(summarytotal); 
		});
		$("#fomsummarytotal").text(addCommas(Math.round(fomsummarytotal.toFixed(2))));
		$('.fomrptsumrow').each(function(){
			sumcheckamt = $(this).children('.fomrptsumamt').text();
			if(sumcheckamt != "0.00"){
				//$(this).remove();
				//alert(sumcheckamt);
			}else{
				$(this).remove();
			}
		});
		sortTable('fomrptsumcrewtrsort');
	});
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
	<form name="frmincentivesrpt" id="frmincentivesrpt" action="include/crewincentivesreport.fnc.php" method="post">
		<input type="hidden" id="sessURL" name="sessURL" value="<?php echo basename($_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" id="sessArea" name="sessArea" value="<?php echo $_SESSION['area']; ?>">
	<?php /*if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){ }else{  */?>
   		<input type="submit" class="toolbarbutton" title="Save" name="btnIRSave" id="btnIRSave" style="background-image:url(img/icon/save.png); margin-left:5px; margin-right:10px; background-repeat:no-repeat" onClick="this.form.submit();" value="">
    	<!--<input class="exemptPrint" type="text" value="From" style="width:35px; margin-left:5px; border:none; background-color:transparent;" readonly>-->
    	<select id="monthyear"  name="monthyear">
    		<?php 
				if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
							$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
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
							$currmonthF = $nextMonthName.', '.$nextYearName;
							$currBID = ExecuteReader("Select unInventoryControl as `result` From inventorycontrol WHERE DATE_FORMAT( ICDate,  '%Y%m' ) = DATE_FORMAT( ".$prevdate.",  '%Y%m' )");
						}else{
							$currdate = ExecuteReader("Select ICDate as `result` From inventorycontrol Where `unInventoryControl`=".$_SESSION['did']);
							$dateset = explode('-', $currdate);
							$lastday = date('t',strtotime($currdate));
							$curryear = $dateset[0];
							$currmonth = $dateset[1];
							/*$prevmonth = (int)$currmonth-1;
							$nextmonth = (int)$currmonth+1;*/
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
							$currmonthF = $nextMonthName.', '.$nextYearName;
							//echo $prevMonthName.' '.$prevDateName.', '.$prevYearName.' TO '.$nextMonthName.' '.$nextDateName.', '.$nextYearName;
						}
						$stmtym = $mysqli->stmt_init();
						if($stmtym->prepare("SELECT  `ICDate` 
							FROM  `inventorycontrol` 
							WHERE  `Status` =1
							GROUP BY DATE_FORMAT( ICDate,  '%Y%m' )")){
								$stmtym->execute();
								$stmtym->bind_result($ym);
								while ($stmtym->fetch()) {
									//echo "<option value'' >".$ym."</option>";
									$ymYear = date('Y',strtotime($ym));
									$ymMonth = date('F',strtotime($ym));
									$ymdateset = explode('-', $ym);
									$ymlastday = date('t',strtotime($ym));
									$ymnumyear = $ymdateset[0];
									$ymnummonth = $ymdateset[1];
									$ymDptFrom = $ymnumyear."-".$ymnummonth."-01"; 
									$ymDptTo = $ymnumyear."-".$ymnummonth."-".$ymlastday;
									$ymName = $ymMonth.', '.$ymYear;
									?>
										<option value='<?php echo $ym; ?>' dptto='<?php echo $ymDptTo; ?>' dptfrom='<?php echo $ymDptFrom; ?>' <?php if($ymName==$currmonthF){ echo "selected"; } ?>><?php echo $ymName; ?></option>
									<?php
								}
						}
				}
			?>
    	</select>
    	<input class="exemptPrint" type="date" id="dtpFrom" style="display: none" name="dtpFrom" value="<?php echo (isset($_GET['dfrom']))?$_GET['dfrom']:'';?>" >
    	<!--<input class="exemptPrint" type="text" value="To" style="width:20px; margin-left:5px; border:none; background-color:transparent;" readonly>-->
    	<input class="exemptPrint" type="date" id="dtpTo" name="dtpTo"  style="display: none" value="<?php echo (isset($_GET['dto']))?$_GET['dto']:'';?>" >
    	<!--<input class="exemptPrint" type="button" value="Go" onClick="FilterReport(dtpFrom.value,dtpTo.value)">
    	<input type="text" style="border:none; background-color:transparent; float:right; margin-top:5px; margin-right:5px; text-align:right; width:300px;" value="<?php echo (isset($_GET['dfrom']))?ShowDateFilter():PMixPeriod();?>" readonly>-->
	<?php  //} ?>
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
			if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			?><table><?PHP
						$export=array();
						$row = array('BRANCH','ID','DATE','NET SALES');
						$export[]=$row;
						$stmt = $mysqli->stmt_init();
						$stmtinc = $mysqli->stmt_init();
						$query="SELECT SUM(  `IDSoldQuantity` ), BName AS Branch, inventorycontrol.unBranch
						FROM  `inventorydata` 
						INNER JOIN  `inventorycontrol` ON  `inventorydata`.`unInventoryControl` =  `inventorycontrol`.`unInventoryControl` 
						INNER JOIN  `branch` ON  `inventorycontrol`.`unBranch` =  `branch`.`unBranch` 
						INNER JOIN  `productitem` ON  `inventorydata`.`unProductItem` =  `productitem`.`unProductItem` 
						INNER JOIN  `productgroup` ON  `productitem`.`unProductGroup` =  `productgroup`.`unProductGroup` 
						WHERE  `branch`.`unArea` =?
						AND  `productgroup`.`unProductType` =1
						AND  `productgroup`.`PGName` LIKE  'fillings of the month'
						AND  `inventorycontrol`.`ICDate` BETWEEN ".$prevdate." AND ".$nextdate."
						GROUP BY BName";
						//echo $query;
						$bpm=array();
						$i=0;
						$FOMInc = 0;
						if($stmt->prepare($query)){
							$stmt->bind_param('i',$_SESSION['area']);
							$stmt->execute();
							$stmt->bind_result($NetSales,$Branch,$unBranch);
							$stmt->store_result();
							while ($stmt->fetch()) {
								$bpm[]=$unBranch;
								/*if(intval($NetSales) > 5){
									$FOMInc = intval($NetSales);
									echo number_format($FOMInc,2,'.',',');
								}*/
								
								if(intval($NetSales) > 11){
									$FOMInc = intval($NetSales);
								}else if(intval($NetSales) > 5 && intval($NetSales) < 12){
									$FOMInc = 6;
								}else if(intval($NetSales) < 6){
									$FOMInc = 0;
								}
								echo '<tr><th><h1 style="text-align: left; margin-left:10px">'.$Branch.'</h1></th></tr>
								<input type="hidden" id="fomrepbranch-'.$i.'" name="repbranch-'.$i.'" value="'.$unBranch.'">';
									echo '<tr><th style="width:20px"><b>FOM Sold</b></th>
									<th style="width:12%"><b>INCENTIVES</b></th>
									<th style="width:12%"><b></b></th>
									<th style="width:12%"><b></b></th>
									<th style="width:12%"><b></b></th>
									<th style="width:12%"><b></b></th></tr>'; 
									echo '<tr><td><input placeholder="0.00" id="fomrepnetsales-'.$i.'" readonly style="text-align:center;" value="'.number_format($NetSales,2,'.',',').'" pseudo="'.$Netsales.'"></td>
									<td><input value="'.number_format($FOMInc,2,'.',',').'" id="fomreppoints-'.$i.'" readonly style="text-align:center;  background:#EEE"></td></tr>';
								echo '<tr><th style="padding-top:30px;">CREW</th><th style="padding-top:30px;">HOURS DUTY</th><th style="padding-top:30px;">%</th><th style="padding-top:30px;">INCENTIVES</th></tr>';
								$j=0;
								$os=0;
								$OSString = '<tr><th style="padding-top:30px;">OUTLET SUPERVISOR</th></tr>';
								$totalHours=0;
								$totalOSHours=0;
								$stmtemp = $mysqli->stmt_init();
								if($stmtemp->prepare("SELECT  SCCode1, SCCode2, CONCAT( ELastName,  ', ', EFirstName ) AS Employee, SUM( SCHours ) AS Hours
								FROM  `salesdata`
								INNER JOIN branch ON salesdata.unBranch = branch.unBranch
								INNER JOIN salescrew ON salesdata.unInventoryControl = salescrew.unInventoryControl
								INNER JOIN employee ON salescrew.unEmployee = employee.unEmployee
								INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
								WHERE branch.unArea =? AND SCCode1 !='T' AND SCCode1!='F' AND salesdata.unBranch=? AND ICDate BETWEEN ".$prevdate." AND ".$nextdate." 
								GROUP BY Employee ORDER BY ELastName")){
									$stmtemp->bind_param('ii',$_SESSION['area'],$unBranch);
									$stmtemp->execute();
									$stmtemp->bind_result($SCCode1,$SCCode2,$Employee,$Hours);
									while ($stmtemp->fetch()) {
										if($SCCode1!='SP'||$SCCode1!='T'||$SCCode1!='F'){
												echo '<tr class="fomrptrow"><th class="fomrptcrew fomrptccrew" style="text-align:left; padding-left:90px;"><input type="hidden" style="display:none" name="repcrewname-'.$i.'-'.$j.'" id="fomrepcrewname-'.$i.'-'.$j.'" value="'.$Employee.'"><b>'.$Employee.'</b></th>
												<td><input placeholder="0.00" id="fomrepcrewhr-'.$i.'-'.$j.'" name="repcrewhr-'.$i.'-'.$j.'" name="repcrewhr-'.$i.'-'.$j.'" readonly style="text-align:center; background:#EEE" value="'.$Hours.'"></td>
												<td><input placeholder="%" id="fomrepcrewperc-'.$i.'-'.$j.'" readonly style="text-align:center; background:#EEE" value=""></td>
												<td><input class="fomrptamt" placeholder="0.00" id="fomrepcrewinc-'.$i.'-'.$j.'" readonly style="text-align:center; background:#EEE" value=""></td>
												</tr>';
												$totalHours+=$Hours;
												$j++;
										}
									}
								}
								echo '<tr><th>TOTAL</th>
									<td><input placeholder="0.00" id="fomrepcrewtotalhr-'.$i.'" readonly style="text-align:center; background:#EEE" value="'.$totalHours.'"></td>
									<td><input placeholder="%" id="fomrepcrewtotalperc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
									<td><input placeholder="0.00" id="fomrepcrewtotalinc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
									</tr>';
								$i++;
							}
							
						}
						$a=0;
						?>
						<input type="hidden" id="ICMonth" name="ICMonth" value="<?php echo $prevdate;?>">
						<?php 
						$row = array('TOTAL','',number_format($TotalSales,2));
						$export[]=$row;
				?></table> </form>
					 <div class="rptsum" id="fomincentivesrptsum" style=" width:100%; padding:50px 0; position: relative; display: inline-block; margin:50px 0; border-top: #000 thick dashed;">
						 <h2>WAFFLETIME INC.</br>SUMMARY of FOM INCENTIVES</br><?php echo $prevdateF.' - '.$nextdateF; ?></h2>
						 <table style="width:60%; margin:0 20%; border: 1px solid #000; border-collapse: collapse;" id="incentivesrptsumtab">
						 	<tr><th style="width:50%; border: 1px solid #000; border-collapse: collapse;">Name</th><th  style="width:50%; border: 1px solid #000; border-collapse: collapse;">Amount</th></tr>
						 	<tr class="fomrptsumcrewtrsort"><th style="width:50%; border: 1px solid #000; border-collapse: collapse;" colspan="2"  >CREW</th><tr>
							<tr><th style="width:50%; border: 1px solid #000; border-collapse: collapse;">TOTAL FOM INCENTIVES</th><th  style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;" id="fomsummarytotal">0.00</th></tr>
						 </table>
						 <table style="width:60%; margin:70px 20%; border: none; border-collapse: collapse;">
						 	<tr style="text-align: left"><td>Prepared by:</td><td></td><td>Checked by:</td></tr>
						 	<tr style="height: 50px"></tr>
						 	<tr><th><input type="text" value="Ma. Caressa Estimada" style="text-align: center"></th><th></th><th><input type="text" value="Ajie C. Tudeja" style="text-align: center"></th></tr>
						 	<tr><th>Acct. Officer</th><th></th><th>Bus. Unit Accountant</th></tr>
						 	<tr><th></th><td>Received by:</td><th></th></tr>
						 	<tr  style="height: 50px"></tr>
						 	<tr><th><input type="text" value="Abigail Joy Gonzales" style="text-align: center"></th><th><input type="text" value="Ethel Leigh Chin" style="text-align: center"></th><th><input type="text" value="Gellyn Ann Casiano" style="text-align: center"></th></tr>
						 	<tr><th>Asst. Payroll Officer</th><th>Opt. Director</th><th>HR Manager</th></tr>
						 </table>
				</div> 
				<?PHP
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
             <!--<div class="rptlistviewitem" style="background-color:#FFF; border-top:2px solid #000; width: 100%">
                    <div class="rptlistviewsubitem" style="font-weight:bold; width:30%; position: relative; display: inline-grid" >GRAND TOTAL</div>
                    <?php
				 		
				 	?>
                    <div class="rptlistviewsubitem" style="width:10%;"><input readonly class="listviewsubitem" type="text" style="font-weight:bold; border:none; background-color:transparent; float: right; position: absolute; right:8%" value="<?php echo number_format($TotalSales,2); ?>" ></div>
            </div>-->
           
            
    </div>
</div>



<?php /*require('footer.php');*/ $_SESSION['dailysalesreport'] = $export; ?>
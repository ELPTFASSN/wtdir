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
						$query="SELECT salesdata.unBranch, BName AS Branch, SUM(  `SDTotalSales` ) AS NetSales
						FROM  `salesdata`
						INNER JOIN branch ON salesdata.unBranch = branch.unBranch
						INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
						WHERE branch.unArea =?
						AND ICDate BETWEEN ".$prevdate." AND ".$nextdate."
						GROUP BY BName";
						//echo $query;
						$bpm=array();
						$i=0;
						if($stmt->prepare($query)){
							$stmt->bind_param('i',$_SESSION['area']);
							$stmt->execute();
							$stmt->bind_result($unBranch,$Branch,$NetSales);
							$stmt->store_result();
							while ($stmt->fetch()) {
								$bpm[]=$unBranch;
								echo '<tr><th><h1 style="text-align: left; margin-left:10px">'.$Branch.'</h1></th></tr>
								<input type="hidden" id="repbranch-'.$i.'" name="repbranch-'.$i.'" value="'.$unBranch.'">';
								if($_SESSION['area']==2){
									echo '<tr><th style="width:20px"><b>NET SALES</b></th>
									<th style="width:12%"><b>QUOTA</b></th>
									<th style="width:12%"><b>INCENTIVES</b></th>
									<th style="width:12%"><b>CREW (85%)</b></th>
									<th style="width:12%"><b>OUTLET SUPERVIOR (12%)</b></th>
									<th style="width:12%"><b>SENIOR SUPERVIOR (2%)</b></th>
									<th style="width:12%"><b>OPERATIONS MANAGER (1%)</b></th></tr>';
									echo '<tr><td><input placeholder="0.00" id="repnetsales-'.$i.'" readonly style="text-align:center;" value="'.number_format($NetSales,2,'.',',').'" pseudo="'.$NetSales.'"></td>
									<td><input placeholder="0.00" class="repquota" name="repquota-'.$i.'" id="repquota-'.$i.'" style="text-align:center"></td>
									<td><input value="0.00" id="reppoints-'.$i.'" readonly style="text-align:center;  background:#EEE"></td>
									<td><input value="0.00" id="repcrew-'.$i.'" readonly style="text-align:center;  background:#EEE" data-id="85"></td>
									<td><input value="0.00" id="repos-'.$i.'" readonly style="text-align:center; background:#EEE" data-id="12"></td>
									<td><input value="0.00" id="repss-'.$i.'" readonly style="text-align:center; background:#EEE" data-id="2"></td>
									<td><input value="0.00" id="repom-'.$i.'" readonly style="text-align:center; background:#EEE" data-id="1"></td></tr>';
								}else{
									echo '<tr><th style="width:20px"><b>NET SALES</b></th>
									<th style="width:8%"><b>QUOTA</b></th>
									<th style="width:8%"><b>INCENTIVES</b></th>
									<th style="width:8%"><b>CREW (85%)</b></th>
									<th style="width:8%"><b>OUTLET SUPERVIOR (10%)</b></th>
									<th style="width:8%"><b>OPERATIONS/AREA MANAGER (5%)</b></th></tr>';
									echo '<tr><td><input placeholder="0.00" id="repnetsales-'.$i.'" readonly style="text-align:center;" value="'.number_format($NetSales,2,'.',',').'" pseudo="'.$NetSales.'"></td>
									<td><input placeholder="0.00" class="repquota" id="repquota-'.$i.'" name="repquota-'.$i.'" style="text-align:center"></td>
									<td><input value="0.00" id="reppoints-'.$i.'" readonly style="text-align:center; background:#EEE"></td>
									<td><input value="0.00" id="repcrew-'.$i.'" readonly style="text-align:center; background:#EEE" data-id="85"></td>
									<td><input value="0.00" id="repos-'.$i.'" readonly style="text-align:center; background:#EEE" data-id="10"></td>
									<td><input value="0.00" id="repom-'.$i.'" readonly style="text-align:center; background:#EEE" data-id="5"></td>
									</tr>';
								}
								echo '<tr><th style="padding-top:30px;">CREW</th><th style="padding-top:30px;">HOURS DUTY</th><th style="padding-top:30px;">%</th><th style="padding-top:30px;">INCENTIVES</th></tr>';
								$j=0;
								$os=0;
								$OSString = '<tr><th style="padding-top:30px;">OUTLET SUPERVISOR</th></tr>';
								$totalHours=0;
								$totalOSHours=0;
								$stmtemp = $mysqli->stmt_init();
								if($stmtemp->prepare("SELECT idEmployee, SCCode1, SCCode2, CONCAT( ELastName,  ', ', EFirstName ) AS Employee, SUM( SCHours ) AS Hours
								FROM  `salesdata`
								INNER JOIN branch ON salesdata.unBranch = branch.unBranch
								INNER JOIN salescrew ON salesdata.unInventoryControl = salescrew.unInventoryControl
								INNER JOIN employee ON salescrew.unEmployee = employee.unEmployee
								INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
								WHERE branch.unArea =? AND SCCode1 !='T' AND SCCode1!='F' AND salesdata.unBranch=? AND ICDate BETWEEN ".$prevdate." AND ".$nextdate."
								GROUP BY Employee ORDER BY ELastName")){
									$stmtemp->bind_param('ii',$_SESSION['area'],$unBranch);
									$stmtemp->execute();
									$stmtemp->bind_result($idEmployee, $SCCode1,$SCCode2,$Employee,$Hours);
									while ($stmtemp->fetch()) {
										if($SCCode1!='SP' && $SCCode1!='T' && $SCCode1!='F'){
												echo '<tr class="rptrow"><th class="rptcrew rptccrew" style="text-align:left; padding-left:90px;"><input type="hidden" style="display:none" name="repcrewid-'.$i.'-'.$j.'" id="repcrewid-'.$i.'-'.$j.'" value="'.$idEmployee.'"><input type="hidden" style="display:none" name="repcrewname-'.$i.'-'.$j.'" id="repcrewname-'.$i.'-'.$j.'" value="'.$Employee.'"><b>'.$Employee.'</b></th>
												<td><input placeholder="0.00" id="repcrewhr-'.$i.'-'.$j.'" name="repcrewhr-'.$i.'-'.$j.'" name="repcrewhr-'.$i.'-'.$j.'" style="text-align:center;" value="'.$Hours.'"></td>
												<td><input placeholder="%" id="repcrewperc-'.$i.'-'.$j.'" readonly style="text-align:center; background:#EEE" value=""></td>
												<td><input class="rptamt" placeholder="0.00" id="repcrewinc-'.$i.'-'.$j.'" readonly style="text-align:center; background:#EEE" value=""></td>
												</tr>';
												$totalHours+=$Hours;
												$j++;
										}else if(($SCCode1=='SP')){
											$OSString = $OSString.'<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="hidden" style="display:none" name="reposid-'.$i.'-'.$j.'" id="reposid-'.$i.'-'.$j.'" value="'.$idEmployee.'"><input type="hidden" style="display:none" id="reposname-'.$i.'-'.$os.'" name="reposname-'.$i.'-'.$os.'" class="reposname  reposnewname-'.$i.'" value="'.$Employee.'"><b>'.$Employee.'</b></th>
											<td><input placeholder="0.00" id="reposhr-'.$i.'-'.$os.'" name="reposhr-'.$i.'-'.$os.'" class="reposhr" readonly style="text-align:center; background:#EEE" value="'.$Hours.'"></td>
											<td><input placeholder="%" id="reposperc-'.$i.'-'.$os.'" readonly style="text-align:center; background:#EEE" value=""></td>
											<td><input class="rptamt" placeholder="0.00" id="reposinc-'.$i.'-'.$os.'" readonly style="text-align:center; background:#EEE" value=""></td>
											</tr>';
											$totalOSHours+=$Hours;
											$os++;
										}
									}
//									$os1 = $os + 1;
//									$os2 = $os1 + 1;
//									$os3 = $os2 + 1;
//									$OSString = $OSString.'<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" id="reposname-'.$i.'-'.$os.'" name="reposnewname-'.$i.'-'.$os.'" class="reposname reposnewname-'.$i.'" value=""><input type="hidden" id="reposid-'.$i.'-'.$os.'" name="reposid-'.$i.'-'.$os.'" value="0"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-'.$os.'" name="reposnewhr-'.$i.'-'.$os.'" class="reposhr" style="text-align:center;" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-'.$os.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="reposinc-'.$i.'-'.$os.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" id="reposname-'.$i.'-'.$os1.'" name="reposnewname-'.$i.'-'.$os1.'" class="reposname reposnewname-'.$i.'" value=""><input type="hidden" id="reposid-'.$i.'-'.$os1.'" name="reposid-'.$i.'-'.$os1.'" value="0"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-'.$os1.'" name="reposnewhr-'.$i.'-'.$os1.'" class="reposhr" style="text-align:center;" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-'.$os1.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="reposinc-'.$i.'-'.$os1.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" id="reposname-'.$i.'-'.$os2.'"  name="reposnewname-'.$i.'-'.$os2.'" class="reposname reposnewname-'.$i.'" value=""><input type="hidden" id="reposid-'.$i.'-'.$os2.'" name="reposid-'.$i.'-'.$os2.'" value="0"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-'.$os2.'" name="reposnewhr-'.$i.'-'.$os2.'" class="reposhr" style="text-align:center;" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-'.$os2.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="reposinc-'.$i.'-'.$os2.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th>TOTAL</th>
//										<td><input placeholder="0.00" id="repostotalhr-'.$i.'" readonly style="text-align:center; background:#EEE" value="'.$totalOSHours.'"></td>
//										<td><input placeholder="%" id="repostotalperc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="repostotalinc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>';
//									$j=0;
//									echo '<tr><th>TOTAL</th>
//									<td><input placeholder="0.00" id="repcrewtotalhr-'.$i.'" readonly style="text-align:center; background:#EEE" value="'.$totalHours.'"></td>
//									<td><input placeholder="%" id="repcrewtotalperc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//									<td><input placeholder="0.00" id="repcrewtotalinc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//									</tr>'.$OSString;
								}

//								if($_SESSION['area']==2){
//									echo '<tr><th style="padding-top:30px;">SENIOR SUPERVISOR</th></tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="repssname-'.$i.'-0"  name="repssname-'.$i.'-0" class="repssname"><input type="hidden" id="repssid-'.$i.'-0" name="repssid-'.$i.'-0" value="0"></th>
//										<td><input placeholder="0.00" id="repsshr-'.$i.'-0"  name="repsshr-'.$i.'-0" class="repsshr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="repssperc-'.$i.'-0" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="repssinc-'.$i.'-0" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="repssname-'.$i.'-1"  name="repssname-'.$i.'-1" class="repssname"><input type="hidden" id="repssid-'.$i.'-1" name="repssid-'.$i.'-1" value="0"></th>
//										<td><input placeholder="0.00" id="repsshr-'.$i.'-1"  name="repsshr-'.$i.'-1" class="repsshr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="repssperc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="repssinc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="repssname-'.$i.'-2"  name="repssname-'.$i.'-2" class="repssname"><input type="hidden" id="repssid-'.$i.'-2" name="repssid-'.$i.'-2" value="0"></th>
//										<td><input placeholder="0.00" id="repsshr-'.$i.'-2"  name="repsshr-'.$i.'-2" class="repsshr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="repssperc-'.$i.'-2" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="repssinc-'.$i.'-2" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th>TOTAL</th>
//										<td><input placeholder="0.00" id="repsstotalhr-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="%" id="repsstotalperc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="repsstotalinc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>';
//									/*echo '<tr><th style="padding-top:30px;">OUTLET SUPERVISOR</th></tr>
//										<tr><th style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="reposname-'.$i.'-1" class="reposname"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-1" class="reposhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="reposinc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="reposname-'.$i.'-2" class="reposname"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-2" class="reposhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-2" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="reposinc-'.$i.'-2" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="reposname-'.$i.'-3" class="reposname"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-3" class="reposhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-3" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="reposinc-'.$i.'-3" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th>TOTAL</th>
//										<td><input placeholder="0.00" id="repostotalhr-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="%" id="repostotalperc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="repostotalinc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>';*/
//									echo '<tr><th style="padding-top:30px;">OPERATIONS/AREA MANAGER</th></tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="repomname-'.$i.'-0" name="repomname-'.$i.'-0" class="repomname"><input type="hidden" id="repomid-'.$i.'-0" name="repomid-'.$i.'-0" value="0"></th>
//										<td><input placeholder="0.00" id="repomhr-'.$i.'-0" name="repomhr-'.$i.'-0" class="repomhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="repomperc-'.$i.'-0" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="repominc-'.$i.'-0" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="repomname-'.$i.'-1" name="repomname-'.$i.'-1" class="repomname"><input type="hidden" id="repomid-'.$i.'-1" name="repomid-'.$i.'-1" value="0"></th>
//										<td><input placeholder="0.00" id="repomhr-'.$i.'-1" name="repomhr-'.$i.'-1" class="repomhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="repomperc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="repominc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th>TOTAL</th>
//										<td><input placeholder="0.00" id="repomtotalhr-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="%" id="repomtotalperc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="repomtotalinc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>';
//								}else{
//									/*echo '<tr><th style="padding-top:30px;">OUTLET SUPERVISOR</th></tr>
//										<tr><th style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="reposname-'.$i.'-1" class="reposname"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-1" class="reposhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="reposinc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="reposname-'.$i.'-2" class="reposname"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-2" class="reposhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-2" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="reposinc-'.$i.'-2" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="reposname-'.$i.'-3" class="reposname"></th>
//										<td><input placeholder="0.00" id="reposhr-'.$i.'-3" class="reposhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="reposperc-'.$i.'-3" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="reposinc-'.$i.'-3" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th>TOTAL</th>
//										<td><input placeholder="0.00" id="repostotalhr-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="%" id="repostotalperc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="repostotalinc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>';*/
//									echo '<tr><th style="padding-top:30px;">OPERATIONS/AREA MANAGER</th></tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="repomname-'.$i.'-0" name="repomname-'.$i.'-0" class="repomname"><input type="hidden" id="repomid-'.$i.'-0" name="repomid-'.$i.'-0" value="0"></th>
//										<td><input placeholder="0.00" id="repomhr-'.$i.'-0" name="repomhr-'.$i.'-0" class="repomhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="repomperc-'.$i.'-0" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="repominc-'.$i.'-0" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr class="rptrow"><th class="rptcrew" style="text-align:left; padding-left:90px;"><input type="text" style="font-weight:bold" id="repomname-'.$i.'-1" name="repomname-'.$i.'-1" class="repomname"><input type="hidden" id="repomid-'.$i.'-1" name="repomid-'.$i.'-1" value="0"></th>
//										<td><input placeholder="0.00" id="repomhr-'.$i.'-1" name="repomhr-'.$i.'-1" class="repomhr" style="text-align:center" value=""></td>
//										<td><input placeholder="%" id="repomperc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input class="rptamt" placeholder="0.00" id="repominc-'.$i.'-1" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>
//										<tr><th>TOTAL</th>
//										<td><input placeholder="0.00" id="repomtotalhr-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="%" id="repomtotalperc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										<td><input placeholder="0.00" id="repomtotalinc-'.$i.'" readonly style="text-align:center; background:#EEE" value=""></td>
//										</tr>';
//								}
								$i++;

							}
						}
						$a=0;
						foreach ($bpm as $b) {
							$unIC=0;
							$sizebpm = sizeof($bpm);
							///echo "SELECT `unIncentivesControl` FROM `incentivescontrol` WHERE DATE_FORMAT( ICMonth,  '%Y%m' ) = DATE_FORMAT( ".$prevdate.",  '%Y%m' ) AND unBranch=".$b." AND unArea=".$_SESSION['area']."<br>";
							/*$ifIRExists = ExecuteReader("SELECT `unIncentivesControl` FROM `incentivescontrol` WHERE DATE_FORMAT( ICMonth,  '%Y%m' ) = DATE_FORMAT( ".$prevdate.",  '%Y%m' ) AND unBranch=".$b." AND unArea=".$_SESSION['area']);*/
							$stmtbpm = $mysqli->stmt_init();
							if($stmtbpm->prepare("SELECT `unIncentivesControl`,`ICQuota` FROM `incentivescontrol` WHERE DATE_FORMAT( ICMonth,  '%Y%m' ) = DATE_FORMAT( ".$prevdate.",  '%Y%m' ) AND unBranch=".$b." AND unArea=".$_SESSION['area'])){
								$stmtbpm->execute();
								$stmtbpm->bind_result($unIncentivesControl,$ICQuota);
								$stmtbpm->fetch();
								if(!isset($unIncentivesControl)){
									$unIC=0;
								}else{
									$unIC=$unIncentivesControl;
								};
							} ?>
								<script type="text/javascript">
									$(document).ready(function(){
										$("#repquota-<?php echo $a; ?>").val('<?php echo $ICQuota; ?>');
										//alert(<?php echo "$prevdate"; ?>);
										processIncentives();
									});
								</script>
								<input type="hidden" id="hasRecord-<?php echo $a;?>" name="hasRecord-<?php echo $a;?>" value="<?php echo $unIC;?>">
							<?php
							$stmtbpm->close();
							$unIC=0;
							$stmtcpm = $mysqli->stmt_init();
							if($stmtcpm->prepare("SELECT  `idIncentivesData` , `IDEmployee` ,  `IDSeq` ,  `IDDes` ,  `IDHours` FROM  `incentivesdata` WHERE  `unIncentivesControl` =".$unIncentivesControl." ORDER BY `IDDes` ASC")){
								$stmtcpm->execute();
								$stmtcpm->bind_result($idIncentivesData,$IDEmployee,$IDSeq,$IDDes,$IDHours);
								while ($stmtcpm->fetch()) {
									if($IDDes=='OS'){
										?>
											<script type="text/javascript">
												$(document).ready(function(){
													var numos = parseInt($(".reposnewname-<?php echo $a; ?>").length)-4;
													var seq = Math.max(0,numos)+parseInt(<?php echo $IDSeq; ?>);  //alert(numos);
													$("#reposname-<?php echo $a; ?>-"+seq).val('<?php echo $IDEmployee; ?>');
													$("#reposhr-<?php echo $a; ?>-"+seq).val('<?php echo $IDHours; ?>');
													$("#reposid-<?php echo $a; ?>-"+seq).val('<?php echo $idIncentivesData; ?>');
													processIncentives();
												});
											</script>
										<?php
									}else if($IDDes=='SS'){
										?>
											<script type="text/javascript">
												$(document).ready(function(){
													$("#repssname-<?php echo $a; ?>-"+<?php echo $IDSeq; ?>).val('<?php echo $IDEmployee; ?>');
													$("#repsshr-<?php echo $a; ?>-"+<?php echo $IDSeq; ?>).val('<?php echo $IDHours; ?>');
													$("#repssid-<?php echo $a; ?>-"+<?php echo $IDSeq; ?>).val('<?php echo $idIncentivesData; ?>');
													processIncentives();
												});
											</script>
										<?php
									}else if($IDDes=='OM'){
										?>
											<script type="text/javascript">
												$(document).ready(function(){
													$("#repomname-<?php echo $a; ?>-"+<?php echo $IDSeq; ?>).val('<?php echo $IDEmployee; ?>');
													$("#repomhr-<?php echo $a; ?>-"+<?php echo $IDSeq; ?>).val('<?php echo $IDHours; ?>');
													$("#repomid-<?php echo $a; ?>-"+<?php echo $IDSeq; ?>).val('<?php echo $idIncentivesData; ?>');
													processIncentives();
												});
											</script>
										<?php
									}
								}
							}
							$stmtcpm->close();
							$a++;
						}
						?>
						<input type="hidden" id="ICMonth" name="ICMonth" value="<?php echo $prevdate;?>">
						<?php
						$row = array('TOTAL','',number_format($TotalSales,2));
						$export[]=$row;
				?></table> </form>
					 <div class="rptsum" id="incentivesrptsum" style=" width:100%; padding:50px 0; position: relative; display: inline-block; margin:50px 0; border-top: #000 thick dashed;">
						 <h2>WAFFLETIME INC.</br>SUMMARY of INCENTIVES</br><?php echo $prevdateF.' - '.$nextdateF; ?></h2>
						 <table style="width:60%; margin:0 20%; border: 1px solid #000; border-collapse: collapse;" id="incentivesrptsumtab">
						 	<tr><th style="width:50%; border: 1px solid #000; border-collapse: collapse;">Name</th><th  style="width:50%; border: 1px solid #000; border-collapse: collapse;">Amount</th></tr>
						 	<tr class="rptsumcrewtrsort"><th style="width:50%; border: 1px solid #000; border-collapse: collapse;" colspan="2"  >CREW</th><tr>
						 	<!--<?php
								/*$stmtsum = $mysqli->stmt_init();
								$SUP = '';
								if($stmtsum->prepare("SELECT  SCCode1, CONCAT( EFirstName,  ' ', ELastName ) AS Employee
								FROM  `salesdata`
								INNER JOIN branch ON salesdata.unBranch = branch.unBranch
								INNER JOIN salescrew ON salesdata.unInventoryControl = salescrew.unInventoryControl
								INNER JOIN employee ON salescrew.unEmployee = employee.unEmployee
								INNER JOIN inventorycontrol ON salesdata.unInventoryControl = inventorycontrol.unInventoryControl
								WHERE branch.unArea =? AND ICDate BETWEEN ".$prevdate." AND ".$nextdate."
								GROUP BY Employee")){
									$stmtsum->bind_param('i',$_SESSION['area']);
									$stmtsum->execute();
									$stmtsum->bind_result($SCCode1,$Employee);
									while ($stmtsum->fetch()) {
										if($SCCode1!='SP'){
											echo '<tr class="rptsumrow"><td class="rptsumcrew" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'.$Employee.'</td>
											<td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>';
										}else{
											$SUP = $SUP.'<tr class="rptsumrow"><td class="rptsumcrew" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'.$Employee.'</td>
											<td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>';
										}
									}
									echo '<tr><th style="width:50%; border: 1px solid #000; border-collapse: collapse;" colspan="2" >SUPERVIOSR/OPERATION MANAGER/AREA MANAGER</th><tr>'.$SUP;
								}*/
							?>-->
							<tr><th style="width:50%; border: 1px solid #000; border-collapse: collapse;" colspan="2"  class="rptsumspec" >SUPERVIOSR/OPERATION MANAGER/AREA MANAGER</th><tr>
							<tr><th style="width:50%; border: 1px solid #000; border-collapse: collapse;">TOTAL INCENTIVES</th><th  style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;" id="summarytotal">0.00</th></tr>
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

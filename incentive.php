<?php 
	require 'reportsheader.php'; 
	
	list($year,$month) = explode('-',$_GET['ddate']);
	$iYear = intval($year);
	$iMonth = intval($month);
	
	$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select ifnull(SQ.QQuota,b.BQuota) BQQuota,ifnull(SQ.QQuotaInterval,b.BQuotaInterval) BQQuotaInterval,ifnull(SQ.QQuotaPointAmount,b.BQuotaPointAmount) BQQuotaPointAmount,b.unBranch unBranch From branch b 
						left Join 
						(Select  unBranch,QQuota,QQuotaInterval,QQuotaPointAmount from Quota Group by QQuota order by Count(QQuota) Desc Limit 1) SQ 
						On SQ.unBranch = b.unBranch
						Where b.unBranch = ?")){
		$stmt->bind_param('i',$_GET['bid']);	
		$stmt->execute();
		$stmt->bind_result($BQuota,$BQuotaInterval,$BQuotaPointAmount,$unBranch);
		$stmt->fetch();
		$stmt->close();
	}
	$mysqli->close();
	
	/* ------ Functions ------ */
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
	function GetQuota($unEmployee,$year,$month){
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select Sum(IEQuotaAmount) as `Share` From inventoryemployee 
							Inner Join employee On inventoryemployee.unEmployee = employee.unEmployee
							Inner Join inventorycontrol On inventoryemployee.unInventoryControl = inventorycontrol.unInventoryControl
							Where Year(ICDate) = ? and Month(ICDate) = ? and unBranch = ? and inventoryemployee.unEmployee = ?")){
			$stmt->bind_param('iiii',$year,$month,$_GET['bid'],$unEmployee);	
			$stmt->execute();
			$stmt->bind_result($Share);
			$stmt->fetch();
			$stmt->close();
		}
		$mysqli->close();
		return $Share;
	}
?>
<script src="js/incentive.js"></script>
<script type="text/javascript">
function FilterReport(dDate){
	if(dDate == ''){
		msgbox('Month and Year cannot contain a null value. Select date.','','');
		return false;
	}
	redirect('<?php echo $_SERVER['PHP_SELF'].'?&bid='.$_GET['bid'].'&did='.$_GET['did'].'&type=1'; ?>&filter=1&ddate='+dDate);
}
</script>
<form method="post">
<div id="toolbar" style="width:100%; margin:auto;">
	<input class="exemptPrint" type="text" value="Month and Year" style="width:90px; margin-left:5px; border:none; background-color:transparent;" readonly><input class="exemptPrint" type="month" id="dtpDate" value="<?php echo (isset($_GET['ddate']))?$_GET['ddate']:'';?>" >
    <input class="exemptPrint" type="button" value="Generate Quota" onClick="SaveIncentives(<?php echo $_GET['bid']; ?>,'<?php echo $_GET['ddate'].'-1'; ?>',txtQuota.value,txtQuotaInterval.value,txtQuotaPointAmount.value,document.URL); FilterReport(dtpDate.value)">
    <input id="exemptPrint" class="toolbarbutton" type="button" style="float:right; background-image:url(img/icon/save.png);" title="Save" onClick="SaveIncentives(<?php echo $_GET['bid']; ?>,'<?php echo $_GET['ddate'].'-1'; ?>',txtQuota.value,txtQuotaInterval.value,txtQuotaPointAmount.value,document.URL)">
</div>
<!--- Good for new rows -->
<div class="rptlistview" style="width:100%;">
	<div class="rptcolumn">
    	<div class="rptcolumnheader" style="width:50%;">Description</div>
        <div class="rptcolumnheader" style="width:10%; text-align:right;">Amount</div>
    </div>
    <div class="rptrow">
    	<div class="rptlistviewitem" style="background-color:#FFF;">
            <div class="rptlistviewsubitem" style="width:49%;"><input readonly style="width:400px; border:none; background-color:transparent;" type="text" value="Monthly Quota"></div>
            <div class="rptlistviewsubitem" style="width:10%;"><input autocomplete="off" name="txtQuota" id="txtQuota" style="width:79px; border-bottom:thin solid #999; background-color:transparent; text-align:right;" type="text" value="<?php echo $BQuota; ?>"></div>
        </div>
        <div class="rptlistviewitem" style="background-color:#EEE;">
            <div class="rptlistviewsubitem" style="width:49%;"><input readonly style="width:400px; border:none; background-color:transparent;" type="text" value="Interval"></div>
            <div class="rptlistviewsubitem" style="width:10%;"><input autocomplete="off" name="txtQuotaInterval" id="txtQuotaInterval" style="width:79px; border-bottom:thin solid #999; background-color:transparent; text-align:right;" type="text" value="<?php echo $BQuotaInterval; ?>"></div>                   
        </div>
        <div class="rptlistviewitem" style="background-color:#FFF;">
            <div class="rptlistviewsubitem" style="width:49%;"><input readonly style="width:400px; border:none; background-color:transparent;" type="text" value="Point Amount"></div>
            <div class="rptlistviewsubitem" style="width:10%;"><input autocomplete="off" name="txtQuotaPointAmount" id="txtQuotaPointAmount" style="width:79px; border-bottom:thin solid #999; background-color:transparent; text-align:right;" type="text" value="<?php echo $BQuotaPointAmount; ?>"></div>                  
        </div>
    </div>
</div>
</form>

<div id="lvSalesList" class="rptlistview" style="width:100%;">
	<div class="rptcolumn">
    	<div class="rptcolumnheader" style="width:168px;">DIR</div>
    	<div class="rptcolumnheader" style="width:144px; text-align:right;">Total Sales</div>
    	<div class="rptcolumnheader" style="width:144px; text-align:right;">Deposited Amount</div>
    	<div class="rptcolumnheader" style="width:144px; text-align:right;">Quota Points</div>
    	<div class="rptcolumnheader" style="width:144px; text-align:right;">Quota Amount</div>
    </div>
    <div class="rptrow">
    	<input type="hidden" id="hdnSelected" value="0">
    	<?php
		$TTotalSales = 0;
		$TDeposited = 0;
		$TQuotaPoint = 0;
		$TQuotaAmount = 0;
		$i=0;
		$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unQuota,QTotalSales,QCashDeposit,QQuotaPoint,QQuotaTotalAmount,QDate,Concat(MonthName(QDate),' ',DayofMonth(QDate),', ',Year(QDate)) as `ICDate`
							From Quota as Q Where Year(QDate)=? and Month(QDate)=? and unBranch=? 
							Order by QDate Asc")){
			$stmt->bind_param('iii',$iYear,$iMonth,$_GET['bid']);
			$stmt->execute();
			$stmt->bind_result($unQuota,$QTotalSales,$QCashDeposit,$QQuotaPoint,$QQuotaTotalAmount,$QDate,$ICDate);
			while($stmt->fetch()){
				$TTotalSales += $QTotalSales;
				$TDeposited += $QCashDeposit;
				$TQuotaPoint += $QQuotaPoint;
				$TQuotaAmount += $QQuotaTotalAmount;
				?>
                <div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;" id="lv-<?php echo $unQuota; ?>">
                	<div class="rptlistviewsubitem" style="min-width:10px; font-family:Arial, Helvetica, sans-serif; cursor:pointer;" title="Click to view." onClick="ShowEmployeeList(<?php echo $unQuota; ?>,<?php echo $_GET['bid']; ?>,'<?php echo $QDate;?>',<?php echo $QQuotaTotalAmount;?>)" id="shwList-<?php echo $unQuota; ?>">►</div>
                	<div class="rptlistviewsubitem" style="width:150px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit;" value="<?php echo $ICDate; ?>"></div>
                	<div class="rptlistviewsubitem" style="width:140px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; text-align:right;" value="<?php echo $QTotalSales; ?>"></div>
                	<div class="rptlistviewsubitem" style="width:140px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; text-align:right;" value="<?php echo $QCashDeposit; ?>"></div>
                	<div class="rptlistviewsubitem" style="width:140px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; text-align:right;" value="<?php echo $QQuotaPoint; ?>"></div>
                	<div class="rptlistviewsubitem" style="width:140px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; text-align:right;" value="<?php echo $QQuotaTotalAmount; ?>"></div>
                    <div class="rptlistviewsubitem" style="min-width:10px; margin-left:5px; cursor:pointer;" id="exemptPrint" onClick="EditQuota('<?php echo $ICDate; ?>',<?php echo $unQuota; ?>)">'▼'</div>
                </div>
				<?php
				$i++;
			}
			$stmt->close();
		}
		$mysqli->close();
		?>
        <div class="rptlistviewitem" style="background-color:#FFF; border-top:solid thin #999;">
        	<div class="rptlistviewsubitem" style="min-width:12px; font-family:Arial, Helvetica, sans-serif;"></div>
            <div class="rptlistviewsubitem" style="width:150px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; font-weight:bold;" value="Total"></div>
            <div class="rptlistviewsubitem" style="width:140px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; font-weight:bold; text-align:right;" value="<?php echo number_format($TTotalSales,4); ?>"></div>
            <div class="rptlistviewsubitem" style="width:140px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; font-weight:bold; text-align:right;" value="<?php echo number_format($TDeposited,4); ?>"></div>
            <div class="rptlistviewsubitem" style="width:140px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; font-weight:bold; text-align:right;" value="<?php echo number_format($TQuotaPoint,4); ?>"></div>
            <div class="rptlistviewsubitem" style="width:140px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; font-weight:bold; text-align:right;" value="<?php echo number_format($TQuotaAmount,4); ?>"></div>
        </div>
    </div>
</div>

<div class="rptgroup" style="width:809px;" id="exemptPrint"></div>

<div class="rptlistview" style="width:100%;">
	<div class="rptcolumn">
    	<div class="rptcolumnheader" style="width:376px;">Employee</div>
        <div class="rptcolumnheader" style="width:10%; text-align:right;">Amount</div>
    </div>
    <div class="rptrow">
    	<?php
			$query = '';
			$i=0;
			$ETotalQuota = 0;
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();

			if(isset($_GET['filter'])){
				$query = "Select Distinct Concat(ELastName,', ',EFirstName,' ',Left(EMiddleName,1),'.') as FullName,inventoryemployee.unEmployee From inventoryemployee 
							Inner Join employee On inventoryemployee.unEmployee = employee.unEmployee
							Inner Join inventorycontrol On inventoryemployee.unInventoryControl = inventorycontrol.unInventoryControl
							Where Year(ICDate) = ? and Month(ICDate) = ? and unBranch = ? Order by ELastName Asc, EFirstName Asc";
			}
			
			if($stmt->prepare($query)){
				if(isset($_GET['filter'])){
					$stmt->bind_param('iii',$iYear,$iMonth,$_GET['bid']);
					$stmt->execute();
					$stmt->bind_result($FullName,$unEmployee);
					while($stmt->fetch()){
						$EQuota = GetQuota($unEmployee,$iYear,$iMonth);
						$ETotalQuota += $EQuota;
						?>
						<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
							<div class="rptlistviewsubitem" style="width:372px;"><input readonly style="width:inherit; border:none; background-color:transparent;" type="text" value="<?php echo $FullName; ?>"></div>
							<div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:79px; border:none; background-color:transparent; text-align:right;" type="text" value="<?php echo number_format($EQuota,4); ?>"></div>
						</div>
						<?php
						$i++;
					}
					?>
					<div class="rptlistviewitem" style="background-color:#FFF; border-top:solid thin #999;">
                        <div class="rptlistviewsubitem" style="width:372px;"><input readonly style="width:inherit; border:none; background-color:transparent; font-weight:bold;" type="text" value="Total"></div>
                        <div class="rptlistviewsubitem" style="width:10%;"><input readonly style="width:79px; border:none; background-color:transparent; text-align:right; font-weight:bold;" type="text" value="<?php echo number_format($ETotalQuota,4); ?>"></div>
                    </div>
					<?php
				}
			}
		?>
    </div>
</div>

<div class="popup" id="editquota" style="background-color:transparent;">
    <div class="popupcontainer">
        <form>
            <div class="popuptitle" id="editquotatitle" align="center"></div>
            <hr>
            <div class="popupitem">
            	<div class="popupitemlabel">Monthly Quota</div><input type="text" name="txtMonthlyQuota" id="txtMonthlyQuota" autocomplete="off" onKeyPress="return disableEnterKey(event)" style="text-align:right;">
            </div>
            <div class="popupitem">
            	<div class="popupitemlabel">Interval</div><input type="text" name="txtInterval" id="txtInterval" autocomplete="off" onKeyPress="return disableEnterKey(event)" style="text-align:right;">
            </div>
            <div class="popupitem">
            	<div class="popupitemlabel">Point Amount</div><input type="text" name="txtPointAmount" id="txtPointAmount" autocomplete="off" onKeyPress="return disableEnterKey(event)" style="text-align:right;">
            </div>
            <hr>
            <div align="center">
                <input type="hidden" name="hdnidQuota" id="hdnidQuota" value="">
                <input class="buttons" style="width:90px;" type="button" value="Save" title="Save" onClick="SaveDailyQuota(hdnidQuota.value,txtMonthlyQuota.value,txtInterval.value,txtPointAmount.value,document.URL)" >
                <input class="buttons" style="width:90px;" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" >
            </div>
        </form>        
    </div>
</div>

<?php require 'footer.php'; ?>
    
    <?php 
	require 'header.php';
	//include 'include/function.inc.php';
	
	$mysqli = new MySQLi($server,$username,$password,$database);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unSales,SBeginningBalance,STotalSales,SCashDeposit,SPettyCash,SDiscount,SGiftCertificate,SCreditCard,SLOA,SEndingBalance,SCashCount,SShortage From sales Where `Status` = 1 and unInventoryControl = ?")){
		$stmt->bind_param('i',$_GET['did']);
		$stmt->execute();
		$stmt->bind_result($unSales,$SBeginningBalance,$STotalSales,$SCashDeposit,$SPettyCash,$SDiscount,$SGiftCertificate,$SCreditCard,$SLOA,$SEndingBalance,$SCashCount,$SShortage);
		$stmt->fetch();
		$stmt->close();
	}

if (isset($_POST['btnCreateSale'])){
	$query="INSERT INTO salescontrol (unSalesControl,unBranch,unEmployeeOpen,unEmployeeClose,SCTimeStart,SCTimeEnd,SCQuota,SCQuotaInterval,SCQuotaPoint,SCQuotaTotalAmount) SELECT ifnull(max(unSalesControl),0)+1,'".$_POST['cmbBranch']."','".$_POST['cmbEOpen']."','".$_POST['cmbEClose']."','".$_POST['timestart']."','".$_POST['timeend']."','".$_POST['scquotatxt']."','".$_POST['scquotaintervaltxt']."','".$_POST['scquotapointtxt']."','".$_POST['scquotatotalamount']."' FROM salescontrol";
	ExecuteNonQuery($query);
	echo $_POST['cmbBranch'].$_POST['cmbEOpen'].$_POST['cmbEClose'].$_POST['timestart'].$_POST['timeend'].$_POST['scquotatxt'].$_POST['scquotaintervaltxt'].$_POST['scquotapointtxt'].$_POST['scquotatotalamount'];
}
	
?>

<script src="js/sale.js"></script>
<script>
$(document).ready(function(e) {
	LoadInventoryEmployee(<?php echo $_GET['did']; ?>); 
	
	$('#frmSale').submit(function(e){
		var tempVal = $('#txtShortage').val();
		var col = $('#txtShortage').css('color');
		if(col=='rgb(0, 0, 0)'){
		}else{
			var newVal = tempVal * -1;
			$('#txtShortage').val(newVal.toFixed(2));
		}
    	return true;
	});
});

</script>

<style>    
	.headbox{
		width:423px;
		height:auto;
		background-color:#FFF;
		float:left;
		color:#333;
		padding:5px;
	}
	.headboxlistitem{
		width:100%;
		float:left;
		min-height:20px;
		padding-top:5px;
		padding-bottom:5px;
	}
	.headboxlistsubitem{
		float:left;
		height:inherit;
		cursor:default;
	}
	.salesitem{
		font-family:calibri;
		width:290px; 
		text-align:right;
	}
</style>



<form action="include/sales.inc.php" method="post" name="frmSale" id="frmSale">
	<input type="hidden" id="idSale" name="idSales" value="<?php echo $unSales; ?>">
    <input type="hidden" id="idInventoryControl" name="idInventoryControl" value="<?php echo $_GET['did']; ?>">
<div id="toolbar">
	<?php
	if($ICLock==0){
	?>
	    <input type="submit" name="btnSaveSale" value="" title="Save" style="background-image:url(img/icon/save.png); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    <?php
		}
	?>
</div>

<div class="headbox" style="border-left:thin solid #999; width:500px;">
	<div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="font-weight:bold; width:15%;">Legend:</div>
    	<div class="headboxlistsubitem" style="width:85%;"> 
        	<div class="listbox" style="border:none;">
            	<div class="listboxitem" style="background-color:#FFF; cursor:default;">S - Supervisor</div>
            	<div class="listboxitem" style="background-color:#FFF; cursor:default;">C - Cashier</div>
                <div class="listboxitem" style="background-color:#FFF; cursor:default;">SC - Service Crew</div>
                <div class="listboxitem" style="background-color:#FFF; cursor:default;">CSC - Cashier and Service Crew</div>
            </div>
        </div>
    </div>
    <div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:inherit;">
        	<div class="listview" style="height:299px;" id="lvCrewOnDuty">
            	<div class="column" id="colCrewOnDuty">
                	<div class="columnheader" style="width:196px;">Full Name</div>
                    <div class="columnheader" style="width:70px;">Assignment</div>
                    <div class="columnheader" style="width:51px; text-align:right;">% Cash</div>
                    <div class="columnheader" style="width:51px; text-align:right;">% Inv.</div>
                    <div class="columnheader" style="width:51px; text-align:right;">% Quota</div>
                </div>
                <div class="row" id="rowCrewOnDuty" style="overflow:hidden;">
                	<div class="listviewitem" style="border-bottom:thin solid #999;">
                    	<div class="listviewsubitem" style="width:196px;">
                        	<input autocomplete="off" type="text" id="txtEmployee" placeholder="Enter name to search" onKeyPress="return disableEnterKey(event)" value="" style="position:relative;top:0px;left:0px;width:191px; z-index:0;">
                            <input type="hidden" id="hdnEmployee" value="0">
                        </div>
                        <div class="listviewsubitem" style="width:70px; margin-top:2px;">
                        	<select style="width:inherit; height:21px;" id="cmbRole" onKeyPress="return disableEnterKey(event)">
                            	<option><none></option>
                                <option value="S">S</option>
                            	<option value="C">C</option>
                                <option value="SC">SC</option>
                                <option value="CSC">CSC</option>
                            </select>
                        </div>
                        <div class="listviewsubitem" style="width:51px; text-align:right;">
                        	<input autocomplete="off" onKeyPress="return disableEnterKey(event)" type="text" id="txtCash" placeholder="0.00" value="100.00" style="width:46px; text-align:right;">
                        </div>
                        <div class="listviewsubitem" style="width:51px; text-align:right;">
                        	<input autocomplete="off" onKeyPress="return disableEnterKey(event)" type="text" id="txtInv" placeholder="0.00" value="100.00" style="width:46px; text-align:right;">
                        </div>
                        <div class="listviewsubitem" style="width:51px; text-align:right;">
                        	<input autocomplete="off" onKeyPress="return disableEnterKey(event)" type="text" id="txtQuota" placeholder="0.00" value="100.00" style="width:46px; text-align:right;">
                        </div>
                        <div class="listviewsubitem" style="min-width:20px;">
                        	<input type="button" class="button16" id="btnAddData" title="Add" onClick="addElement(txtEmployee.value,hdnEmployee.value,'cmbRole',txtCash.value,txtInv.value,txtQuota.value)" style="background-image:url(img/icon/add.png); padding:0px; border:none; width:16px; height:16px; margin-top:5px; background-color:transparent; cursor:pointer;">
                        	<input type="hidden" id="hdnCount" name="hdnCount">
                        </div>
                    </div>
                    <div class="listbox" id="lstresult"  style="position:fixed;width:245px;max-height:240px;display:none;"></div>
                    <div id="crewonduty" style="overflow:auto; overflow-x:hidden; position:relative; height:150px; width:500px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="headbox" style="height:417px; width:345px; ">
	<div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="font-weight:bold; font-family:calibri; font-size:14px;">Cash Count</div>
    </div>
    <div class="headboxlistitem">
    	<div class="rptlistview">
        	<div class="rptcolumn">
            	<div class="rptcolumnheader" style="width:100px;">Denomination</div>
                <div class="rptcolumnheader" style="width:100px; text-align:right;">Quantity</div>
                <div class="rptcolumnheader" style="width:100px; text-align:right;">Amount</div>
            </div>
            <div class="rptrow">
            	<?php 
					$i=1;
					$mysqli = new MySQLi($server,$username,$password,$database);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("Select unDenomination,DValue,
									(Select DCQuantity From denominationcount Where unDenomination=denomination.unDenomination and `Status` = 1 and unInventoryControl=?) as `Quantity`,
									ifNull((Select DCAmount From denominationcount Where unDenomination=denomination.unDenomination and `Status` = 1 and unInventoryControl=?),0.00) as `Amount`,
									ifNull((Select unDenominationCount From denominationcount Where unDenomination=denomination.unDenomination and `Status` = 1 and unInventoryControl=?),0) as `unDenominationCount`
									From denomination 
									Where `Status` = 1 Order by DValue Desc")){
						$stmt->bind_param('iii',$_GET['did'],$_GET['did'],$_GET['did']);
						$stmt->execute();
						$stmt->bind_result($unDenomination,$DValue,$Quantity,$Amount,$unDenominationCount);
						while($stmt->fetch()){
							?>
                            <div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                                <div class="rptlistviewsubitem" style="width:96px;">
                                	<input readonly onKeyPress="return disableEnterKey(event)" type="text" id="txtDenomination-<?php echo $i; ?>" value="<?php echo $DValue; ?>" style="width:80px; text-align:right;">
                                    <input type="hidden" name="hdnDenomination-<?php echo $i; ?>" value="<?php echo $unDenomination; ?>">
									<input type="hidden" name="hdnDenominationCount-<?php echo $i?>" value="<?php echo $unDenominationCount; ?>">
                                </div>
                                <div class="rptlistviewsubitem" style="width:96px;">
                                	<input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="txtQuantity" type="text" name="txtQty-<?php echo $i; ?>" id="txtQty-<?php echo $i; ?>" value="<?php echo $Quantity; ?>" style="text-align:right; border-bottom:thin solid #999; width:inherit;" >
                                </div>
                                <div class="rptlistviewsubitem" style="width:96px;">
                                	<input readonly onKeyPress="return disableEnterKey(event)" type="text" name="txtAmount-<?php echo $i; ?>" id="txtAmount-<?php echo $i; ?>" style="text-align:right; border:none; width:inherit;" value="<?php echo $Amount; ?>">
                                </div>
                            </div>
							<?php
							$i++;
						}
					}
					?>
					<input type="hidden" id="hdnDCount" name="hdnDCount" value="<?php echo $i-1; ?>">
					<?php
				?>
            </div>
        </div>
    </div>
</div>

<div class="headbox" style="border-right:thin solid #999;">  
	<div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;">Beginning Balance</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtBeginningBalance" name="txtBeginningBalance" value="<?php echo $SBeginningBalance; ?>"></div>
    </div>
    <div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;">Total Sales</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtTotalSales" name="txtTotalSales" value="<?php echo $STotalSales; ?>" readonly></div>
    </div>
    
    <div class="headboxlistitem">
		<div class="headboxlistsubitem" style="border-bottom:thin solid #999; width:inherit; font-weight:bold;">Less</div>
    </div>
	<div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;">Cash Deposit</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtCashDeposit" name="txtCashDeposit" value="<?php echo $SCashDeposit; ?>" required></div>
    </div>
    <div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;">Petty Cash Fund</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtPettyCash" name="txtPettyCash" value="<?php echo $SPettyCash; ?>" required readonly></div>
    </div>
    <div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;">Discount</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtDiscount" name="txtDiscount" value="<?php echo $SDiscount; ?>" required></div>
    </div>
    <div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;">Gift Certificate</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtGC" name="txtGC" value="<?php echo $SGiftCertificate; ?>" required readonly></div>
    </div>
    <div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;">Credit Card</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtCC" name="txtCC" value="<?php echo $SCreditCard; ?>" required readonly></div>
    </div>
    <div class="headboxlistitem" style="border-bottom:thin solid #999;">
    	<div class="headboxlistsubitem" style="width:30%;">LOA</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtLOA" name="txtLOA" value="<?php echo $SLOA; ?>" required readonly></div>
    </div>
    
    <div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;">End Balance</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtEndingBalance" name="txtEndingBalance" value="<?php echo $SEndingBalance; ?>" required readonly></div>
    </div>
	<div class="headboxlistitem" style="border-bottom:thin solid #999;">
    	<div class="headboxlistsubitem" style="width:30%;">Cash Count</div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtCashCount" name="txtCashCount" value="<?php echo $SCashCount; ?>" required readonly></div>
    </div>
    <div class="headboxlistitem">
    	<div class="headboxlistsubitem" style="width:30%;" id="shortover"><?php echo ($SShortage < 0)?'Shortage':'Overage';?></div>
        <div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" class="salesitem" onKeyPress="return disableEnterKey(event)" type="text" id="txtShortage" name="txtShortage" value="<?php echo number_format(($SShortage < 0)?$SShortage * -1:$SShortage,4); ?>" required readonly style="color:#<?php echo ($SShortage < 0)?'F00':'000';?>"></div>
    </div>
</div>

</form>


<?php require 'footer.php'; ?>
<?php
	include 'header.php';
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("SELECT unArea,unBranch,unInventoryControl,unDiscountType,unEmployeePrepared,unEmployeeReceived,DCReference,DCInvoice,DCPax,DCDate,DCTotal,DCNetOfVat,DCDiscount,DCNetPrice 
						FROM discountcontrol Where unDiscountControl=?")){
		$stmt->bind_param('i',$_GET['id']);
		$stmt->execute();
		$stmt->bind_result($unArea,$unBranch,$unInventoryControl,$unDiscountType,$unEmployeePrepared,$unEmployeeReceived,$DCReference,$DCInvoice,$DCPax,$DCDate,$DCTotal,$DCNetOfVat,$DCDiscount,$DCNetPrice);
		$stmt->fetch();
		$stmt->close();
	}
?>
<script src="js/discount.js"></script>
<script type="text/javascript">
function deletecustomer(i){ 
		if(confirm('Remove [ ' + document.getElementById('txtcard-'+i).getAttribute('value') + ' ] Are you sure?') ){
			var d = document.getElementById('rowcustomer')
			var olddiv = document.getElementById('lvitem-'+i)		
			d.removeChild(olddiv);
		}
}

function deleteitem(i){ 
		if(confirm('Remove [ ' + document.getElementById('txtitemname-'+i).getAttribute('value') + ' ] Are you sure?') ){
			var d = document.getElementById('rowitemdata')
			var olddiv = document.getElementById('lvitemdata-'+i)		
			d.removeChild(olddiv);
			sumtotal();
		}
}
</script>
<style type="text/css">
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
	height:20px;
	padding-top:5px;
	padding-bottom:5px;
}
.headboxlistsubitem{
	float:left;
	height:inherit;
}

</style>
<form name="frmcreatediscount" id="frmcreatediscount" method="post" action="include/discount.inc.php?&id=<?php echo $_GET['id']; ?>" >
<div id="toolbar">
<?php
	if($unInventoryControl==0){	
?>
	<input type="submit" class="toolbarbutton" title="Save" name="btnupdatediscount" value="" onclick="" style="background-image:url(img/icon/save.png);background-repeat:no-repeat;background-position:center;">
<?php
	}else{
?>
	<script type="text/javascript">msgbox('This entry cannot be updated because it is currently mapped to an Inventory Sheet. Remove it from the sheet first.<br><br><a href="discount.php?&bid=<?php echo $unBranch; ?>&did=<?php echo $unInventoryControl; ?>&type=1" target="_blank"><u>Go to Inventory Sheet</u></a>','','')</script>
<?php
	}
?>
</div>

<div class="headbox" style="border-left:solid thin #999;">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Branch</div>
        <div class="headboxlistsubitem" style="width:60%;">
            <select name="cmbbranch" id="cmbbranch" style="width:200px;" required>
            <option value=""><none></option>
            <?php
				$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Select unBranch,BName from branch where `Status`=1 and unArea=? Order by BName")){
					$stmt->bind_param('i',$_SESSION['area']);
					$stmt->execute();
					$stmt->bind_result($uunBranch,$BName);
					while($stmt->fetch()){
			?>
		                <option value="<?php echo $uunBranch; ?>" <?php echo ($uunBranch==$unBranch)? 'Selected':''; ?>><?php echo $BName; ?></option>
            <?php
					}
					$stmt->close();
				}
			?>
            </select>
        </div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Discount</div>
        <div class="headboxlistsubitem" style="width:60%;">
            <select name="cmbtype" id="cmbtype" style="width:200px;">
            <?php
				$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Select unDiscountType,DTName from discounttype where `Status`=1 Order by DTName")){
					$stmt->execute();
					$stmt->bind_result($uunDiscountType,$DTName);
					while($stmt->fetch()){
			?>
		                <option value="<?php echo $uunDiscountType; ?>" <?php echo ($uunDiscountType==$unDiscountType)? 'Selected':''; ?>><?php echo $DTName; ?></option>
            <?php
					}
					$stmt->close();
				}
			?>
            </select>
        </div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Date</div>
        <div class="headboxlistsubitem" style="width:60%;">
			<input type="date" name="dtdate" id="dtdate" style="width:200px;" onKeyPress="return disableEnterKey(event)" value="<?php echo $DCDate; ?>">
        </div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Pax</div>
        <div class="headboxlistsubitem" style="width:60%;">
			<input type="number" name="txtpax" id="txtpax" value="<?php echo $DCPax; ?>" style="width:200px;" min="1" onKeyPress="return disableEnterKey(event)">
        </div>
    </div>
</div>

<div class="headbox" style="width:422px;">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Reference</div>
        <div class="headboxlistsubitem" style="width:60%;">
			<input type="text" name="txtreference" id="txtreference" value="<?php echo $DCReference; ?>" style="width:200px;" required onKeyPress="return disableEnterKey(event)">
        </div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Invoice</div>
        <div class="headboxlistsubitem" style="width:60%;">
			<input type="text" name="txtinvoice" id="txtinvoice" value="<?php echo $DCInvoice; ?>" style="width:200px;" onKeyPress="return disableEnterKey(event)">
        </div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Prepared by</div>
        <div class="headboxlistsubitem" style="width:60%;">
            <select name="cmbpreparedby" id="cmbpreparedby" style="width:200px;">
	            <option value="0"></option>
            <?php
				$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Select employee.unEmployee,concat(Upper(ELastName),', ',EFirstName, ' ',Substr(EMiddleName,1,1),'. ',ifnull(EAlias,'')) as `EFullName` From employee inner Join employeearea on employee.unEmployee=employeearea.unEmployee Where employee.`Status`=1 and unArea=? Order by EFullName")){
					$stmt->bind_param('i',$_SESSION['area']);
					$stmt->execute();
					$stmt->bind_result($uunEmployee,$EFullName);
					while($stmt->fetch()){
			?>
		                <option value="<?php echo $uunEmployee; ?>" <?php echo ($uunEmployee==$unEmployeePrepared)? 'Selected':''; ?>><?php echo $EFullName; ?></option>
            <?php
					}
					$stmt->close();
				}
			?>
            </select>
        </div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Received by</div>
        <div class="headboxlistsubitem" style="width:60%;">
            <select name="cmbreceivedby" id="cmbreceivedby" style="width:200px;">
	            <option value="0"></option>
            <?php
				$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Select employee.unEmployee,concat(Upper(ELastName),', ',EFirstName, ' ',Substr(EMiddleName,1,1),'. ',ifnull(EAlias,'')) as `EFullName` From employee inner Join employeearea on employee.unEmployee=employeearea.unEmployee Where employee.`Status`=1 and unArea=? Order by EFullName")){
					$stmt->bind_param('i',$_SESSION['area']);
					$stmt->execute();
					$stmt->bind_result($uunEmployee,$EFullName);
					while($stmt->fetch()){
			?>
		                <option value="<?php echo $uunEmployee; ?>" <?php echo ($uunEmployee==$unEmployeeReceived)? 'Selected':''; ?>><?php echo $EFullName; ?></option>
            <?php
					}
					$stmt->close();
				}
			?>
            </select>
        </div>
    </div>
</div>

<div class="headbox" style="border-right:solid thin #999;">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Total</div>
        <div class="headboxlistsubitem" id="divtotal" style="width:60%;text-align:right;font-weight:bold;"><?php echo $DCTotal; ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Net of Vat</div>
        <div class="headboxlistsubitem" id="divnetofvat" style="width:60%;text-align:right;font-weight:bold;"><?php echo $DCNetOfVat; ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Discount</div>
        <div class="headboxlistsubitem" id="divdiscount" style="width:60%;text-align:right;font-weight:bold;"><?php echo $DCDiscount; ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Net Price</div>
        <div class="headboxlistsubitem" id="divnetsales" style="width:60%;text-align:right;font-weight:bold;"><?php echo $DCNetPrice; ?></div>
    </div>

</div>

<div class="listview" id="lvdiscount">
	<div class="column" id="colcustomer">
    	<div class="columnheader" style="width:150px;text-align:left;">ID Number</div>
    	<div class="columnheader" style="width:150px;text-align:left;">LastName</div>
    	<div class="columnheader" style="width:150px;text-align:left;">First Name</div>
    	<div class="columnheader" style="width:150px;text-align:left;">Middle Name</div>
    	<div class="columnheader" style="width:150px;text-align:left;">Alias</div>
    	<div class="columnheader" style="width:150px;text-align:left;">Action</div>
    </div>
    <div class="row" id="rowcustomer">
    	<div class="listviewitem" id="lvitem-0">
            <input type="hidden" id="hdncount" name="hdncount" value="<?php echo ExecuteReader("Select Count(unDiscountCustomer) as `result` From discountcustomer where `Status`=1 and unDiscountControl=".$_GET['id']) ?>">
    
            <div class="listviewsubitem" style="width:150px;">
                <input type="search" id="txtsearchcard" value="" style="position:relative;top:0px;left:0px;width:100%;" onKeyPress="return disableEnterKey(event)">
            </div>
    
            <div class="listviewsubitem" style="width:150px;">
                <input type="text" id="txtsearchlastname"  style="position:relative;top:0px;left:0px;width:100%;text-transform:uppercase;" onKeyPress="return disableEnterKey(event)">
            </div>
    
            <div class="listviewsubitem" style="width:150px;">
                <input type="text" id="txtsearchfirstname" style="text-transform:capitalize;" onKeyPress="return disableEnterKey(event)">
            </div>
    
            <div class="listviewsubitem" style="width:150px;">
                <input type="text" id="txtsearchmiddlename" style="text-transform:capitalize;" onKeyPress="return disableEnterKey(event)">
            </div>
    
            <div class="listviewsubitem" style="width:150px;">
                <input type="text" id="txtsearchalias" style="text-transform:capitalize;" onKeyPress="return disableEnterKey(event)">
            </div>
            <div class="listviewsubitem" style="width:150px;text-align:right;">
                <div title="Add as new customer entry" onClick="addcustomer(0,0,cmbtype.value,txtsearchcard.value,txtsearchlastname.value,txtsearchfirstname.value,txtsearchmiddlename.value,txtsearchalias.value)" class="button16" style="background-image:url(img/icon/add.png);padding-top:10px;"></div>
            </div>
        </div>
		<div class="listbox" id="lstresult" style="position:fixed;width:300px;max-height:240px;display:none;"></div>
        <?php
			$i=0;
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			// this structure isn't capable of retrieving different discount types yet.
			if($stmt->prepare("SELECT unDiscountCustomer,CCNumber,discountcustomer.unCustomer,CLastName,CFirstName,CMiddleName,CAlias,unCustomerCard 
								FROM discountcustomer 
								Inner Join customer on discountcustomer.unCustomer=customer.unCustomer 
								Inner Join customercard on discountcustomer.unCustomer=customercard.unCustomer 
								WHERE unDiscountControl=? and unDiscountType=? and discountcustomer.`Status`=1")){
				$stmt->bind_param('ii',$_GET['id'],$unDiscountType);
				$stmt->execute();
				$stmt->bind_result($unDiscountCustomer,$CCNumber,$unCustomer,$CLastName,$CFirstName,$CMiddleName,$CAlias,$unCustomerCard);
				while($stmt->fetch()){
					$i++;
		?>
                    <div class="listviewitem" id="lvitem-<?php echo $i; ?>">
                        <div class="listviewsubitem" style="width: 150px;"><input id="txtcard-<?php echo $i; ?>" value="<?php echo $CCNumber; ?>" readonly style="width: 150px; background-color: transparent; border: none;"></div>
                        <div class="listviewsubitem" style="width: 150px;"><input style="text-transform: uppercase; width: 150px; background-color: transparent; border: none;" value="<?php echo $CLastName; ?>" readonly></div>
                        <div class="listviewsubitem" style="width: 150px;"><input style="text-transform: capitalize; width: 150px; background-color: transparent; border: none;" value="<?php echo $CFirstName; ?>" readonly></div>
                        <div class="listviewsubitem" style="width: 150px;"><input style="text-transform: capitalize; width: 150px; background-color: transparent; border: none;" value="<?php echo $CMiddleName; ?>" readonly></div>
                        <div class="listviewsubitem" style="width: 150px;"><input style="text-transform: capitalize; width: 150px; background-color: transparent; border: none;" value="<?php echo $CAlias; ?>" readonly></div>
                        <div class="button16" style="width: 150px;">
                            <div class="button16" style="background-image: url(http://119.93.224.26/admin/img/icon/delete.png); padding-top: 5px; padding-left: 0px;" onClick="deletecustomer(<?php echo $i; ?>)"></div>
                        </div>
                        <input type="hidden" name="hdnidcard-<?php echo $i; ?>" value="<?php echo $unCustomerCard; ?>">
                        <input type="hidden" name="hdnidcustomer-<?php echo $i; ?>" value="<?php echo $unCustomer; ?>">
                        <input type="hidden" name="hdnidcardtype-<?php echo $i; ?>" value="<?php echo $unDiscountType; ?>">
                    </div>
		<?php
				}
				$stmt->close();
			}
		?>

    </div>
</div>

<div class="listview" id="lvitemdata">
	<div class="column" id="colitemdata">
    	<div class="columnheader" style="width:300px;text-align:left;">Item</div>
        <div class="columnheader" style="width:150px;text-align:right;">Quantity</div>
        <div class="columnheader" style="width:150px;text-align:right;">Price</div>
        <div class="columnheader" style="width:150px;text-align:right;">Total</div>
    	<div class="columnheader" style="width:150px;text-align:left;">Action</div>
    </div>
    <div class="row" id="rowitemdata">
    	<div class="listviewitem" id="lvitemdata-0">
		    <input type="hidden" id="hdnitemcount" name="hdnitemcount" value="<?php echo ExecuteReader("SELECT count(idDiscountData) as `result` FROM discountdata WHERE `Status`=1 and idDiscountControl=".$_GET['id']) ?>">
        	
            <div class="listviewsubitem" style="width:300px;">
				<input type="search" id="txtsearchitem" value="" style="position:relative;top:0px;left:0px;width:100%;" onKeyPress="return disableEnterKey(event)">
                <input type="hidden" id="hdnitemadd" value="0">
            </div>
        	
            <div class="listviewsubitem" style="width:150px;">
				<input type="text" id="txtquantity" style="text-align:right" onKeyPress="return disableEnterKey(event)">
            </div>
            
        	<div class="listviewsubitem" style="width:150px;">
				<input type="text" id="txtprice" style="text-align:right" value="0.00" onKeyPress="return disableEnterKey(event)">
            </div>
            
        	<div class="listviewsubitem" style="width:150px;">
				<input type="text" id="txttotal" style="text-align:right" value="0.00" readonly onKeyPress="return disableEnterKey(event)">            
            </div>
        	
            <div class="listviewsubitem" style="width:150px;">
				<input type="button" class="button16" id="btnadditem" title="Add" onMouseUp="additem(hdnitemadd.value,txtsearchitem.value,txtquantity.value,txtprice.value,txttotal.value)" style="background-image:url(img/icon/add.png); margin-left:10px; border:none; width:16px; height:16px; margin-top:5px; background-color:transparent; cursor:pointer;" onKeyPress="return disableEnterKey(event)">
            </div>
        </div>
        <?php
			$i=0;
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("SELECT unDiscountData,discountdata.unProductItem,PIName,DDQuantity,DDPrice,(DDQuantity*DDPrice) as DDTotal 
								FROM discountdata 
								Inner Join productitem On discountdata.unProductItem=productitem.unProductItem 
								WHERE productitem.`Status`=1 and unDiscountControl=?")){
				$stmt->bind_param('i',$_GET['id']);
				$stmt->execute();
				$stmt->bind_result($unDiscountData,$unProductItem,$PIName,$DDQuantity,$DDPrice,$DDTotal);
				while($stmt->fetch()){
					$i++;
		?>
                    <div class="listviewitem" id="lvitemdata-<?php echo $i; ?>">
                        <div class="listviewsubitem" style="width: 300px;"><input id="txtitemname-<?php echo $i; ?>"value="<?php echo $PIName; ?>" readonly style="width: 300px; background-color: transparent; border: none;"></div>
                        <div class="listviewsubitem" style="width: 150px;"><input value="<?php echo $DDQuantity; ?>" name="txtitemquantity-<?php echo $i; ?>" readonly style="width: 150px; background-color: transparent; border: none; text-align: right;"></div>
                        <div class="listviewsubitem" style="width: 150px;"><input value="<?php echo $DDPrice; ?>" name="txtitemprice-<?php echo $i; ?>" readonly style="width: 150px; background-color: transparent; border: none; text-align: right;"></div>
                        <div class="listviewsubitem" style="width: 150px;"><input id="txtitemtotal-<?php echo $i; ?>" value="<?php echo $DDTotal; ?>" readonly style="width: 150px; background-color: transparent; border: none; text-align: right;"></div>
                        <div class="listviewsubitem" style="width: 150px;">
                            <div class="button16" style="background-image: url(img/icon/delete.png); padding-top: 5px; padding-left: 0px;" onClick="deleteitem(<?php echo $i; ?>)"></div>
                        </div><input type="hidden" name="hdnitemid-<?php echo $i; ?>" value="<?php echo $unProductItem; ?>">
                    </div>
 		<?php
				}
				$stmt->close();
			}
		?>
       
    </div>
</div>
</form>
<?php
	include 'footer.php';
?>
<?php
	include 'header.php';
?>
<script src="js/discount.js"></script>
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
<form name="frmcreatediscount" id="frmcreatediscount" method="post" action="include/discount.inc.php" >
<div id="toolbar">
<input type="submit" class="toolbarbutton" title="Save" name="btnadddiscount" value="" onclick="" style="background-image:url(img/icon/save.png);background-repeat:no-repeat;background-position:center;">
</div>
<div style="float:left;width:100%;background-color:#FFF;height:auto;">
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
					$stmt->bind_result($unBranch,$BName);
					while($stmt->fetch()){
			?>
		                <option value="<?php echo $unBranch; ?>" <?php echo($unBranch==$_GET['bid'])? 'Selected':''; ?>><?php echo $BName; ?></option>
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
					$stmt->bind_result($unDiscountType,$DTName);
					while($stmt->fetch()){
			?>
		                <option value="<?php echo $unDiscountType; ?>"><?php echo $DTName; ?></option>
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
			<input type="date" name="dtdate" id="dtdate" style="width:200px;" onKeyPress="return disableEnterKey(event)">
        </div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Pax</div>
        <div class="headboxlistsubitem" style="width:60%;">
			<input type="number" name="txtpax" id="txtpax" value="1" style="width:200px;" min="1" onKeyPress="return disableEnterKey(event)">
        </div>
    </div>
</div>

<div class="headbox" style="width:422px;">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Reference</div>
        <div class="headboxlistsubitem" style="width:60%;">
			<input type="text" name="txtreference" id="txtreference" value="" style="width:200px;" required onKeyPress="return disableEnterKey(event)">
        </div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Invoice</div>
        <div class="headboxlistsubitem" style="width:60%;">
			<input type="text" name="txtinvoice" id="txtinvoice" value="" style="width:200px;" onKeyPress="return disableEnterKey(event)">
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
					$stmt->bind_result($unEmployee,$EFullName);
					while($stmt->fetch()){
			?>
		                <option value="<?php echo $unEmployee; ?>"><?php echo $EFullName; ?></option>
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
					$stmt->bind_result($unEmployee,$EFullName);
					while($stmt->fetch()){
			?>
		                <option value="<?php echo $unEmployee; ?>"><?php echo $EFullName; ?></option>
            <?php
					}
					$stmt->close();
				}
			?>
            </select>
        </div>
    </div>
</div>

<div class="headbox" style="">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Total</div>
        <div class="headboxlistsubitem" id="divtotal" style="width:60%;text-align:right;font-weight:bold;">0.00</div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Net of Vat</div>
        <div class="headboxlistsubitem" id="divnetofvat" style="width:60%;text-align:right;font-weight:bold;">0.00</div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Discount</div>
        <div class="headboxlistsubitem" id="divdiscount" style="width:60%;text-align:right;font-weight:bold;">0.00</div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Net Sales</div>
        <div class="headboxlistsubitem" id="divnetsales" style="width:60%;text-align:right;font-weight:bold;">0.00</div>
    </div>

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
    <div class="row">
    	<div class="listviewitem" id="lvitem-0">
            <input type="hidden" id="hdncount" name="hdncount" value="0">
    
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
    </div>
    <div class="row" id="rowcustomer"></div>
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
		    <input type="hidden" id="hdnitemcount" name="hdnitemcount" value="0">
        	
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
    </div>
</div>
</form>
<?php
	include 'footer.php';
?>
<?php 
require 'header.php'; 
$idPCC = isset($_GET['id'])?$_GET['id']:0;
if($idPCC != 0){
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unEmployee,PCCReferenceNumber,PCCDate,PCCAmount From pettycashcontrol Where unPettyCashControl = ? and `Status` = 1")){
		$stmt->bind_param("i",$unPCC);
		$stmt->execute();
		$stmt->bind_result($PCCEmployee,$PCCReferenceNumber,$PCCDate,$PCCAmount);
		$stmt->fetch();
		$stmt->close();
	}
	$mysqli->close();
}
?>
<script src="js/pettycash.js"></script>
<script>
$(document).ready(function(e) {
    LoadPettyCashData(<?php echo $unPCC; ?>);
});
</script>

<form name="frmPettyCash" id="frmPettyCash" method="post" action="include/pettycash.inc.php">
	<input type="hidden" name="idPTC" value="<?php echo $unPCC; ?>">
    <div id="toolbar">
    	<input type="submit" name="btnsavepettycash" value="" title="Save" style="background-image:url(img/icon/save.png); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    </div>
    
    <div class="listview">
        <div class="column">
            <div class="columnheader" style="width:200px;">Prepared by</div>
            <div class="columnheader" style="width:200px;">Reference Number</div>
            <div class="columnheader" style="width:150px;">Date</div>
            <div class="columnheader" style="width:100px; text-align:right;">Amount</div>        
        </div>
        <div class="row">
            <div class="listviewitem">
                <div class="listviewsubitem" style="width:200px;">
                    <select name="cmbEmployee" style="width:inherit; height:21px; margin-top:2px;">
                        <?php 
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("Select employee.unEmployee,Concat(ELastName,', ',EFirstName,' ',Substring(EMiddleName,1,1),'.') as `FullName` From employeearea 
											Inner Join employee on employeearea.unEmployee = employee.unEmployee
											Where unArea = ? and employee.`Status` = 1 and employeearea.`Status` = 1 Order By ELastName Asc, EFirstName Asc")){
							$stmt->bind_param("i",$_SESSION['area']);
							$stmt->execute();
							$stmt->bind_result($unEmployee,$FullName);
							while($stmt->fetch()){
								?>
                                <option value="<?php echo $unEmployee; ?>" <?php echo ($unEmployee==$PCCEmployee)?'selected':''; ?>><?php echo $FullName; ?></option>
								<?php
							}
							$stmt->close();
						}
						$mysqli->close();
						?>
                    </select>
                </div>
                <div class="listviewsubitem" style="width:200px;">
                    <input required type="text" name="txtReferenceNumber" style="width:196px;" autocomplete="off" onKeyPress="return disableEnterKey(event)" value="<?php echo $PCCReferenceNumber; ?>">
                </div>
                <div class="listviewsubitem" style="width:150px;">
                    <input required type="date" name="dtpDate" style="width:145px; height:20px;" autocomplete="off" onKeyPress="return disableEnterKey(event)" value="<?php echo $PCCDate; ?>">
                </div>
                <div class="listviewsubitem" style="width:100px;">
                    <input readonly required type="text" name="txtTotalAmount" id="txtTotalAmount" placeholder="0.00" style="width:96px; text-align:right;" onKeyPress="return disableEnterKey(event)" value="<?php echo $PCCAmount; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="group" style="text-align:center; font-weight:bold;">. . .</div>
    <div class="listview">
        <div class="column">
            <div class="columnheader" style="width:500px;">Description</div>
            <div class="columnheader" style="width:100px; text-align:right;">Amount</div>        
        </div>
        <div class="row">
            <div class="listviewitem">
                <div class="listviewsubitem">
                    <input type="text" id="txtDescription" placeholder="Enter description and quantity. Description [Amount]" style="width:496px; text-transform:capitalize;" autocomplete="off" onKeyPress="return disableEnterKey(event)">
                </div>
                <div class="listviewsubitem">
                    <input type="text" id="txtAmount" placeholder="0.00" style="width:96px; text-align:right;" autocomplete="off" onKeyPress="return disableEnterKey(event)">
                </div>
                <div class="listviewsubitem">
                    <input type="button" class="button16" id="btnAddData" title="Add" onMouseUp="addElement(txtDescription.value,txtAmount.value)" style="background-image:url(img/icon/add.png); padding:0px; border:none; width:16px; height:16px; margin-top:5px; background-color:transparent; cursor:pointer;">
                    <input type="hidden" id="hdnCount" name="hdnCount">
                </div>
            </div>
            <div id="pettycashdata"></div>
        </div>
    </div>
</form>
<?php require 'footer.php'; ?>
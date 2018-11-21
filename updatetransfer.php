<?php 
	require 'header.php';
	$colBranch = new Collection;
	$mysqli  = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order By BName Asc")){
		$stmt->bind_param('i',$_SESSION['area']);
		$stmt->execute();
		$stmt->bind_result($unBranch,$BName);
		while($stmt->fetch()){
			$colBranch->Add($BName,$unBranch);
		}
		$stmt->close();
	}	

	$colEmployee = new Collection;
	$mysqli  = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();			
	if($stmt = $mysqli->prepare("Select employee.unEmployee,Concat(ELastName,', ',EFirstName,' ',Substring(EMiddleName,1,1),'.') as `FullName` From employeearea 
								Inner Join employee on employeearea.unEmployee = employee.unEmployee
								Where unArea = ? and employee.`Status` = 1 and employeearea.`Status` = 1 Order By ELastName Asc, EFirstName Asc")){
		$stmt->bind_param('i',$_SESSION['area']);
		$stmt->execute();
		$stmt->bind_result($unEmployee,$EFullName);
		while($stmt->fetch()){
			$colEmployee->Add($EFullName,$unEmployee);
		}
		$stmt->close();
	}

	$colProduct = new Collection;
	$mysqli  = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt = $mysqli->prepare("Select unProductItem,PIName From productitem
									Inner Join productgroup on productitem.unProductGroup = productgroup.unProductGroup
									Where productitem.`Status` = 1 Order by PIName Asc")){
		$stmt->execute();
		$stmt->bind_result($unProductItem,$PIName);
		while($stmt->fetch()){
			$colProduct->Add($PIName,$unProductItem);
		}
		$stmt->close();
	}
	
	$mysqli  = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unTransferControl,unEmployeeFrom,unEmployeeTo,unEmployeeDelivery,TCNumber,TCDate,TCReason,unBranchFrom,unBranchTo,unInventoryControlFrom,unInventoryControlTo From transfercontrol Where unTransferControl = ?")){
		$stmt->bind_param('i',$_GET['idTC']);
		$stmt->execute();
		$stmt->bind_result($unTransferControl,$unEmployeeFrom,$unEmployeeTo,$unEmployeeDelivery,$TCNumber,$TCDate,$TCReason,$unBranchFrom,$unBranchTo,$unInventoryControlFrom,$unInventoryControlTo);
		$stmt->fetch();
		$stmt->close();
	}

?>
    
<script type="text/javascript" src="js/transfer.js"></script>
<script>

function loadtransferdata(idTC){
	var xmlhttp;
	if(idTC==0){
		document.getElementById('transferdata').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('transferdata').innerHTML=xmlhttp.responseText;
			document.getElementById('hdnCount').value = $('#transferdata').children().length;
		}
	}
	xmlhttp.open('POST','ajax/transfer.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=LoadTransferData&idTC='+idTC);
}

function updatetransferdata(icount,ihdnPIVal,itxtQtyVal,ihdnUOMVal){
	document.getElementById('txt-'+icount+'-product').value = document.getElementById('editcmbproduct').options[document.getElementById('editcmbproduct').selectedIndex].text;
	document.getElementById('hdn-'+icount+'-product').value = ihdnPIVal;
	document.getElementById('txt-'+icount+'-qty').value = itxtQtyVal;
	//document.getElementById('txt-'+icount+'-unit').value = document.getElementById('editcmbunit').options[document.getElementById('editcmbunit').selectedIndex].text;
	document.getElementById('hdn-'+icount+'-unit').value = ihdnUOMVal;
	location.href='#close';
}

$(document).ready(function(e) {
	loadtransferdata(<?php echo $_GET['idTC']; ?>);
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
	}
	input, select, textarea{
		font-family:calibri;
	}
</style>

<form id="frmedittransfer" method="post" action="include/transfer.inc.php">
	<input type="hidden" name="idtransfercontrol" value="<?php echo $_GET['idTC']; ?>">
    <div id="toolbar">	
	<?php
    if($unInventoryControlFrom==0 && $unInventoryControlTo==0){	
    ?>
    	<input type="submit" name="btnupdatetransfer" value="" title="Save" style="background-image:url(img/icon/save.png); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    <?php
    }else{
    ?>
    	<script type="text/javascript">msgbox('This entry cannot be updated because it is currently mapped to an Inventory Sheet. Remove it from the sheet first.<br><br><a href="transfer.php?&bid=<?php echo $unBranchFrom; ?>&did=<?php echo ($unInventoryControlTo==0)?$unInventoryControlFrom:$unInventoryControlTo; ?>&type=1" target="_blank"><u>Go to Inventory Sheet</u></a>','','')</script>
    <?php
    }
    ?>
    </div>
 <div style="width:100%;height:auto;background-color:#FFF;float:left;">   
    <div class="headbox" style="height:192px; border-left:thin solid #999;">
    	<div class="headboxlistitem"><div class="headboxlistsubitem" style="font-weight:bold;">Details</div></div>
    	<div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">ITF Number</div>
        	<div class="headboxlistsubitem" style="width:70%;">
            	<input autocomplete="off" onKeyPress="return disableEnterKey(event)" type="text" name="txtitfno" style="width:196px;" required value="<?php echo $TCNumber; ?>">
			</div>
        </div>
        <div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">Date</div>
            <div class="headboxlistsubitem" style="width:70%;">
            	<input autocomplete="off" onKeyPress="return disableEnterKey(event)" type="date" name="dtpdate" style="width:196px;" value="<?php echo $TCDate; ?>" required >
            </div>
        </div>
        <div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">Delivered by</div>
			<div class="headboxlistsubitem" style="width:70%;">
            	<select name="cmbdelivered" id="cmbdelivered" style="width:200px;" >
                	<option value="0"><none></option>
                	<?php
					for($i=1;$i<=$colEmployee->Count() - 1;$i++){
						?>
                        <option value="<?php echo $colEmployee->GetKey($i); ?>" <?php echo ($unEmployeeDelivery==$colEmployee->GetKey($i))?'Selected':''; ?> ><?php echo $colEmployee->GetByIndex($i); ?></option>
						<?php
					}
					?>
                </select>
			</div>
        </div>
    </div>
    
    <div class="headbox">
    	<div class="headboxlistitem"><div class="headboxlistsubitem" style="width:100%; font-weight:bold;">From</div></div>
    	<div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">Crew</div>
        	<div class="headboxlistsubitem" style="width:70%;">
            	<select name="cmbfrom" id="cmbfrom" style="width:200px;" >
                	<option><none></option>
                	<?php
					for($i=1;$i<=$colEmployee->Count() - 1;$i++){
						?>
                        <option value="<?php echo $colEmployee->GetKey($i); ?>" <?php echo ($unEmployeeFrom==$colEmployee->GetKey($i))?'Selected':''; ?> ><?php echo $colEmployee->GetByIndex($i); ?></option>
						<?php
					}
					?>	
                </select>
            </div>
        </div>
        <div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">Branch</div>
            <div class="headboxlistsubitem" style="width:70%;">
            	<select name="cmbbranchfrom" id="cmbbranchfrom" style="width:200px;" required >
                	<?php
					for($i=1;$i<=$colBranch->Count() - 1;$i++){
						?>
                        <option value="<?php echo $colBranch->GetKey($i); ?>" <?php echo ($unBranchFrom==$colBranch->GetKey($i))?'Selected':''; ?> ><?php echo $colBranch->GetByIndex($i); ?></option>
						<?php
					}
					?>
                </select>
            </div>
        </div>    

    	<div class="headboxlistitem"><div class="headboxlistsubitem" style="width:100%; font-weight:bold;">To</div></div>
        <div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">Crew</div>
        	<div class="headboxlistsubitem" style="width:70%;">
            	<select name="cmbto" id="cmbto" style="width:200px;" >
                	<option value=""><none></option>
                	<?php
					for($i=1;$i<=$colEmployee->Count() - 1;$i++){
						?>
                        <option value="<?php echo $colEmployee->GetKey($i); ?>" <?php echo ($unEmployeeTo==$colEmployee->GetKey($i))?'Selected':''; ?> ><?php echo $colEmployee->GetByIndex($i); ?></option>
						<?php
					}
					?>
                </select>
            </div>
        </div>
        <div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">Branch</div>
            <div class="headboxlistsubitem" style="width:70%;">
            	<select name="cmbbranchto" id="cmbbranchto" style="width:200px;" required >
                	<?php
					for($i=1;$i<=$colBranch->Count() - 1;$i++){
						?>
                        <option value="<?php echo $colBranch->GetKey($i); ?>" <?php echo ($unBranchTo==$colBranch->GetKey($i))?'Selected':''; ?> ><?php echo $colBranch->GetByIndex($i); ?></option>
						<?php
					}
					?>
                </select>
            </div>
        </div>  
    </div>
    
    <div class="headbox" style="border-right:thin solid #999; width:422px; height:192px;">
    	<div class="headboxlistitem"><div class="headboxlistsubitem" style="font-weight:bold;">Reason For Stock Transfer (Please Select One)</div></div>
        <div style="padding-left:10px;" onClick="chktoggle('itfrd-1')"><input required id="itfrd-1" type="radio" name="rdreason" onClick="CheckReason(this)" value="Shortage of Supply" <?php echo (substr($TCReason,0,1)=='S')?'Checked':''; ?> > Shortage of Supply </div>
        <div style="padding-left:10px;" onClick="chktoggle('itfrd-2')"><input required id="itfrd-2" type="radio" name="rdreason" onClick="CheckReason(this)" value="Others" <?php echo (substr($TCReason,0,1)=='O')?'Checked':''; ?> > Others (Please Specify)<br>
        <textarea  <?php echo (substr($TCReason,0,1)!='O')?'readonly':''; ?> id="itftxt-2" name="itftxt-2" style="width:282px; resize:none; height:50px; margin-top:2px;" ><?php echo (substr($TCReason,0,1)=='O')?substr($TCReason,9):''; ?></textarea></div>
    </div> 
</div>
    <div class="group" style="text-align:center;">. . .</div>

	<div class="listview">
    	<div class="column">
        	<div class="columnheader" style="width:504px;">Description</div>
        	<div class="columnheader" style="width:60px; text-align:center;">Qty</div>
        	<div class="columnheader" style="width:76px;">Unit</div>
        </div>
        <div class="row">
        	<div class="listviewitem" style="height:30px;">
                <div class="listviewsubitem">
                    <input autocomplete="off" type="text" id="txtSearch" onKeyPress="return disableEnterKey(event)" value="" style="position:relative;top:0px;left:0px;width:500px;">
                    <input type="hidden" id="hdnSearchId" value="0">
                </div>
                <div class="listviewsubitem">
                    <input onKeyPress="return disableEnterKey(event)" id="txtQuantity" type="text" style="width:56px; text-align:center;">
                </div>
                <div class="listviewsubitem" style="height:26px; vertical-align:middle;">
                    <select id="cmbUnit" style="width:76px; height:21px; margin-top:2px;"></select>
                </div>
                <div class="listviewsubitem">
                    <input type="button" class="button16" id="btnAddData" title="Add" onMouseUp="addElement(hdnSearchId.value,txtSearch.value,txtQuantity.value,cmbUnit.value,'update')" style="background-image:url(img/icon/add.png); padding:0px; border:none; width:16px; height:16px; margin-top:5px; background-color:transparent; cursor:pointer;">
                    <input type="hidden" id="hdnCount" name="hdnCount">
                </div>
            </div>
            <div class="listbox" id="lstresult"  style="position:fixed;width:500px;max-height:240px;display:none; padding:bottom:50px;"></div>

        	<div id="transferdata" style="width:inherit; padding-bottom:120px;"></div>   
        </div>
    </div>
</form>

<div class="popup" id="edittransferdata">
	<div class="popupcontainer" style="width:400px;">
    <div id="etdcontainer" >
    </div>
    <div align="center" style="margin-top:10px;">
        <input type="button" class="buttons" title="Update" value="Update" onClick="updatetransferdata(icount.value,editcmbproduct.value,edittxtqty.value,editcmbunit.value)">
        <input type="button" class="buttons" title="Cancel" value="Cancel" onClick="location.href='#close'">
    </div>
    </div>
</div>

<?php require 'footer.php'; ?>
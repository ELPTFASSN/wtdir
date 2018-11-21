<?php 
	require('header.php');
	$i = 1;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select DCDate,DCDocNum,DCComments,unBranchFrom,unBranchTo,unInventoryControl From damagecontrol Where unDamageControl = ? and `Status` = 1")){
		$stmt->bind_param('i',$_GET['idDC']);
		$stmt->execute();
		$stmt->bind_result($DCDate,$DCDocNum,$DCComments,$unBranchFrom,$unBranchTo,$unInventoryControl);
		$stmt->fetch();
	}
?>
<script src="js/damage.js"></script>
<script>

function loaddamagedata(idDC){
	var xmlhttp;
	if(idDC==0){
		document.getElementById('damagedata').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			
			document.getElementById('damagedata').innerHTML=xmlhttp.responseText;
			document.getElementById('hdnCount').value = $('#damagedata').children().length;
		}
	}
	xmlhttp.open('POST','ajax/damage.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=LoadDamageData&idDC='+idDC);
}

function updatedamagedata(icount,ihdnPIVal,itxtQtyVal,ihdnUOMVal){
	document.getElementById('txt-'+icount+'-product').value = document.getElementById('editcmbproduct').options[document.getElementById('editcmbproduct').selectedIndex].text;
	document.getElementById('hdn-'+icount+'-product').value = ihdnPIVal;
	document.getElementById('txt-'+icount+'-qty').value = itxtQtyVal;
	document.getElementById('txt-'+icount+'-unit').value = document.getElementById('editcmbunit').options[document.getElementById('editcmbunit').selectedIndex].text;
	document.getElementById('hdn-'+icount+'-unit').value = ihdnUOMVal;
	location.href='#close';
}

$(document).ready(function(e) {
	loaddamagedata(<?php echo $_GET['idDC']; ?>);
});

</script>

<form action="include/damage.inc.php" method="post" name="frmupdatedamage">
	<input type="hidden" name="undamagecontrol" value="<?php echo $_GET['idDC']; ?>">
<div id="toolbar">
<?php
	if($unInventoryControl==0){	
?>
	<input type="submit" name="btnupdatedamage" value="" title="Save" style="background-image:url(img/icon/save.png); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
<?php
	}else{
?>
	<script type="text/javascript">msgbox('This entry cannot be updated because it is currently mapped to an Inventory Sheet. Remove it from the sheet first.<br><br><a href="damage.php?&bid=<?php echo $idBranchFrom; ?>&did=<?php echo $idInventoryControl; ?>&type=1" target="_blank"><u>Go to Inventory Sheet</u></a>','','')</script>
<?php
	}
?>
</div>

<div class="listview" id="lvDamageControl">
	<div class="column">
    	<div class="columnheader" style="width:196px">Doc Number</div>
        <div class="columnheader" style="width:146px; text-align:center;">Date</div>
        <div class="columnheader" style="width:196px;">Branch From</div>
        <div class="columnheader" style="width:196px;">Branch To</div>
        <div class="columnheader" style="width:531px;">Comments</div>
    </div>
    <div class="row">
    	<div class="listviewitem">
        	<div class="listviewsubitem" style="width:196px;"><input autocomplete="off" onKeyPress="return disableEnterKey(event)" name="txtDCDocNum" id="txtDCDocNum" type="text" style="width:150px;" value="<?php echo $DCDocNum; ?>" required></div>
        	<div class="listviewsubitem" style="width:146px;"><input onKeyPress="return disableEnterKey(event)" name="dtpDCDate" id="dtpDCDate" type="date" style="width:140px;" value="<?php echo $DCDate; ?>" required></div>
        	<div class="listviewsubitem" style="width:196px;">
                <select name="cmbDCBranchFrom" id="cmbDCBranchFrom" style="width:195px; height:24px; margin-top:2px;" required>
                    <option><none></option>
					<?php 
                    $colBranch = new Collection;
                    $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt = $mysqli->stmt_init();
                    if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? and BType=1 Order by BName Asc")){
                        $stmt->bind_param('i',$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
                        while($stmt->fetch()){
                            ?>
                            <option value="<?php echo $unBranch; ?>" <?php echo ($unBranchFrom==$unBranch)?'Selected':'';?> ><?php echo $BName; ?></option>
                            <?php
                        }
                        $stmt->close();
                    }	
                    ?>
				</select>
            </div>
            <div class="listviewsubitem" style="width:196px;">
                <select name="cmbDCBranchTo" id="cmbDCBranchTo" style="width:195px; height:24px; margin-top:2px;" required>
                    <option><none></option>
					<?php 
                    $colBranch = new Collection;
                    $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt = $mysqli->stmt_init();
                    if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? and BType=2 Order by BName Asc")){
                        $stmt->bind_param('i',$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
                        while($stmt->fetch()){
                            ?>
                            <option value="<?php echo $unBranch; ?>" <?php echo ($unBranchTo==$unBranch)?'Selected':'';?> ><?php echo $BName; ?></option>
                            <?php
                        }
                        $stmt->close();
                    }	
                    ?>
				</select>
            </div>
            <div class="listviewsubitem" style="width:531px;">
            	<textarea name="txtDCComment" id="txtDCComment" style="width:95%; resize:vertical; font-family:calibri;"><?php echo $DCComments; ?></textarea>
            </div>
        </div>
    </div>
</div>

<div class="group" style="text-align:center;">. . . .</div>

<div class="listview" id="lvDamageData">
    <div class="column">
        <div class="columnheader" style="width:504px; ">Description</div>
        <div class="columnheader" style="width:60px; text-align:center;">QTY</div>
        <div class="columnheader" style="width:76px; ">Unit</div>
    </div>
    <div class="row">
        <div class="listviewitem" style="height:30px;">
            <div class="listviewsubitem">
                <input autocomplete="off" type="text" id="txtSearch" onKeyPress="return disableEnterKey(event)" value="" style="position:relative;top:0px;left:0px;width:500px;">
                <input type="hidden" id="hdnSearchId" value="0">
            </div>
            <div class="listviewsubitem">
            	<input autocomplete="off" type="text" id="txtQuantity" onKeyPress="return disableEnterKey(event)" style="width:56px; text-align:center;">
            </div>
            <div class="listviewsubitem" style="height:26px; vertical-align:middle;">
                <select id="cmbUnit" style="width:76px; height:22px; margin-top:2px;"></select>
            </div>
            <div class="listviewsubitem">
                <input type="button" class="button16" id="btnAddData" title="Add" onMouseUp="addElement(hdnSearchId.value,txtSearch.value,txtQuantity.value,cmbUnit.value,'update')" style="background-image:url(img/icon/add.png); padding:0px; border:none; width:16px; height:16px; margin-top:5px; background-color:transparent; cursor:pointer;">
                <input type="hidden" id="hdnCount" name="hdnCount">
            </div>
        </div>
        <div class="listbox" id="lstresult"  style="position:fixed;width:500px;max-height:240px;display:none;">
        </div>

        <div id="damagedata" style="width:inherit;"></div>    
    </div>
</div>

</form>

<div class="popup" id="editdamagedata">
	<div class="popupcontainer" style="width:400px;">
    <div id="eddcontainer" >
    </div>
    <div align="center" style="margin-top:10px;">
        <input type="button" class="buttons" title="Update" value="Update" onClick="updatedamagedata(icount.value,editcmbproduct.value,edittxtqty.value,editcmbunit.value)">
        <input type="button" class="buttons" title="Cancel" value="Cancel" onClick="location.href='#close'">
    </div>
    </div>
</div>
<?php require('footer.php'); ?>
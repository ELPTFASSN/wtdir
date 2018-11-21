<?php 
	require('header.php'); 
	$branchun = (isset($_GET['bid']))?$_GET['bid']:0;
?>
<script src="js/damage.js"></script>

<form action="include/damage.inc.php" method="post" name="frmcreatedamage" id="frmcreatedamage">
<div id="toolbar">
	<input type="submit" name="btnsavedamage" value="" title="Save" style="background-image:url(img/icon/save.png); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
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
        	<div class="listviewsubitem" style="width:196px;"><input autocomplete="off" onKeyPress="return disableEnterKey(event)" name="txtDCDocNum" id="txtDCDocNum" type="text" style="width:150px;" required> <!--<input type="button" class="button16" style="float:none;" title="Search" onClick="FetchDRControl(txtDCDocNum.value)">--></div>
        	<div class="listviewsubitem" style="width:146px;"><input onKeyPress="return disableEnterKey(event)" name="dtpDCDate" id="dtpDCDate" type="date" style="width:140px;" required></div>
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
                            <option value="<?php echo $unBranch; ?>" <?php echo ($unBranch == $branchun)?'selected':'';?>><?php echo $BName; ?></option>
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
                            <option value="<?php echo $unBranch; ?>" selected><?php echo $BName; ?></option>
                            <?php
                        }
                        $stmt->close();
                    }	
                    ?>
				</select>
            </div>
            <div class="listviewsubitem" style="width:531px;">
            	<textarea name="txtDCComment" id="txtDCComment" style="width:95%; resize:vertical; font-family:calibri;"></textarea>
            </div>
        </div>
    </div>
</div>

<div class="group" style=" text-align:center;">. . . .</div>

<div class="listview" id="lvDamageData">
    <div class="column">
        <div class="columnheader" style="width:504px; ">Description</div>
        <div class="columnheader" style="width:60px; text-align:center;">QTY</div>
        <div class="columnheader" style="width:76px; ">Unit</div>
    </div>
    <div class="row">
        <div class="listviewitem" style="height:30px;">
            <div class="listviewsubitem">
                <input autocomplete="off" type="text" id="txtSearch" placeholder="Enter to search item" onKeyPress="return disableEnterKey(event)" value="" style="position:relative;top:0px;left:0px;width:500px;">
                <input type="hidden" id="hdnSearchId" value="0">
            </div>
            <div class="listviewsubitem">
            	<input autocomplete="off" onKeyPress="return disableEnterKey(event)" id="txtQuantity" type="text" style="width:56px; text-align:center;">
            </div>
            <div class="listviewsubitem" style="height:26px; vertical-align:middle;">
                <select id="cmbUnit" style="width:76px; height:21px; margin-top:2px;"></select>
            </div>
            <div class="listviewsubitem">
                <input type="button" class="button16" id="btnAddData" title="Add" onMouseUp="addElement(hdnSearchId.value,txtSearch.value,txtQuantity.value,cmbUnit.value,'create')" style="background-image:url(img/icon/add.png); padding:0px; border:none; width:16px; height:16px; margin-top:5px; background-color:transparent; cursor:pointer;">
                <input type="hidden" id="hdnCount" name="hdnCount">
                <input type="hidden" id="hdnFlag" value="0">
            </div>
        </div>
        <div class="listbox" id="lstresult"  style="position:fixed;width:500px;max-height:240px;display:none;">
        </div>

        <div id="damagedata" style="width:inherit;"></div>    
    </div>
</div>
</form>
<?php require('footer.php'); ?>
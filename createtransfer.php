<?php 
	require 'header.php';
	$colBranch = new Collection;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
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
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
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
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt = $mysqli->prepare("Select unProductItem,PIName From productitem
									Inner Join productgroup on productitem.unProductGroup = productgroup.unProductGroup
									Where productitem.`Status` = 1 Order by PIName Asc")){
		$stmt->execute();
		$stmt->bind_result($unProductItem,$PIName);
		while($stmt->fetch()){
			$colProduct->Add($PIName,$unProductItem);
		}
	}
    ?>
    
<script type="text/javascript" src="js/transfer.js"></script>
    
<style>    
	.headbox{
		width:28%;
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

<form id="frmcreatetransfer" method="post" action="include/transfer.inc.php">
    <div id="toolbar">
    	<input type="submit" name="btnsavetransfer" value="" title="Save" style="background-image:url(img/icon/save.png); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    </div>
    
    <div class="transfercontainer" style="background-color:FFF;">
    	<div class="headbox" style="height:192px;">
    	<div class="headboxlistitem"><div class="headboxlistsubitem" style="font-weight:bold;">Details</div></div>
    	<div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">ITF Number</div>
        	<div class="headboxlistsubitem" style="width:70%;"><input autocomplete="off" onKeyPress="return disableEnterKey(event)" type="text" name="txtitfno" id="txtitfno" style="width:196px;" required ><input type="button" onClick="FetchITFControl('FetchITFControl',txtitfno.value)"></div>
        </div>
        <div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">Date</div>
            <div class="headboxlistsubitem" style="width:70%;"><input onKeyPress="return disableEnterKey(event)" type="date" name="dtpdate" id="dtpdate" style="width:196px;" required ></div>
        </div>
        <div class="headboxlistitem">
        	<div class="headboxlistsubitem" style="width:30%;">Delivered by</div>
			<div class="headboxlistsubitem" style="width:70%;">
            	<select name="cmbdelivered" id="cmbdelivered" style="width:200px;" >
                	<option value="0"><none></option>
                	<?php
					for($i=1;$i<=$colEmployee->Count() - 1;$i++){
						?>
                        <option value="<?php echo $colEmployee->GetKey($i); ?>"><?php echo $colEmployee->GetByIndex($i); ?></option>
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
                        <option value="<?php echo $colEmployee->GetKey($i); ?>"><?php echo $colEmployee->GetByIndex($i); ?></option>
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
                        <option value="<?php echo $colBranch->GetKey($i); ?>"><?php echo $colBranch->GetByIndex($i); ?></option>
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
                        <option value="<?php echo $colEmployee->GetKey($i); ?>"><?php echo $colEmployee->GetByIndex($i); ?></option>
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
                        <option value="<?php echo $colBranch->GetKey($i); ?>"><?php echo $colBranch->GetByIndex($i); ?></option>
						<?php
					}
					?>
                </select>
            </div>
        </div>  
    </div>
    
    <div class="headbox" style="width:422px; height:192px;">
    	<div class="headboxlistitem"><div class="headboxlistsubitem" style="font-weight:bold;">Reason For Stock Transfer (Please Select One)</div></div>
                <div style="padding-left:10px;" onClick="chktoggle('itfrd-1')"><input required id="itfrd-1" type="radio" name="rdreason" onClick="CheckReason(this)" value="Shortage of Supply" > Shortage of Supply </div>
                <div style="padding-left:10px;" onClick="chktoggle('itfrd-2')"><input required id="itfrd-2" type="radio" name="rdreason" onClick="CheckReason(this)" value="Others" > Others (Please Specify)<br>
                <textarea id="itftxt-2" name="itftxt-2" style="width:282px; resize:none; height:50px; margin-top:2px;" ></textarea></div>
    </div>
    </div>
    <div class="group" style="text-align:center;">. . .</div>

	<div class="listview" style=" padding:bottom:50px;">
    	<div class="column">
        	<div class="columnheader" style="width:504px;">Description</div>
        	<div class="columnheader" style="width:60px; text-align:center;">Qty</div>
        	<div class="columnheader" style="width:76px;">Unit</div>
        </div>
        <div class="row">
        	<div class="listviewitem" style="height:30px;  padding:bottom:50px;">
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
                    <input type="hidden" id="hdnFlag">
                </div>
            </div>
            <div class="listbox" id="lstresult"  style="position:fixed;width:500px;max-height:240px;display:none; padding:bottom:50px;"></div>

        	<div id="transferdata" style="width:inherit; padding-bottom:120px;"></div>   
        </div>
    </div>
</form>

<?php require 'footer.php'; ?>
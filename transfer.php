<?php
require('header.php');

if(isset($_GET['del'])){
	$query = '';
	$mysqli  = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	/*if($stmt->prepare("Select unBranchFrom,unBranchTo From transfercontrol Where `Status` = 1 and unTransferControl = ?")){
		$stmt->bind_param('i',$_GET['itf']);
		$stmt->execute();
		$stmt->bind_result($unBranchFrom,$unBranchTo);
		$stmt->fetch();
		if($unBranchFrom == $_GET['bid']){
			$query = 'Update transfercontrol Set unInventoryControlFrom = 0 Where `Status` = 1 and unTransferControl = '.$_GET['itf'];
		}elseif($unBranchTo == $_GET['bid']){
			$query = 'Update transfercontrol Set unInventoryControlTo = 0 Where `Status` = 1 and unTransferControl = '.$_GET['itf'];
		}
		$stmt->close();
		ExecuteNonQuery($query);
		ExecuteNonQuery('Call MAPTransferIUpdate('.$_GET['did'].','.$_GET['itf'].')');
	}*/
	if($stmt->prepare('Call UnMAPTransferFrom(?,?)')){
			$stmt->bind_param('ii',$_GET['did'],$_GET['itf']);
			$stmt->execute();
			$stmt->close();
	}

	$stmt = $mysqli->stmt_init();
	if($stmt->prepare('Call UnMAPTransferTo(?,?)')){
			$stmt->bind_param('ii',$_GET['did'],$_GET['itf']);
			$stmt->execute();
			$stmt->close();
	}


	ExecuteNonQuery('Call MAPTransferIUpdate('.$_GET['did'].','.$_GET['itf'].')');
	//die('Call MAPTransferIUpdate('.$_GET['did'].','.$_GET['itf'].')');
	echo "<script>location.href='transfer.php?&bid=".$_GET['bid']."&did=".$_GET['did']."&type=".$_GET['type']."'</script>";

	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt1 = $mysqli->stmt_init();
		$stmt2 = $mysqli->stmt_init();
		$stmt3 = $mysqli->stmt_init();
		if($stmt2->prepare("Call FinalResultRawMat(?,?)")){
			$stmt2->bind_param('ii',$_GET['did'],$_GET['bid']);
			$stmt2->execute();
		}else{
			echo $stmt2->error();
			die();
		}
		if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			if($stmt3->prepare("Call FinalResultMix(?,?)")){
				$stmt3->bind_param('ii',$_GET['did'],$_GET['bid']);
				$stmt3->execute();
			}else{
				echo $stmt3->error();
				die();
			}
		}
		if($stmt1->prepare("Call FinalResultProduct(?,?)")){
			$stmt1->bind_param('ii',$_GET['did'],$_GET['bid']);
			$stmt1->execute();
		}else{
			echo $stmt1->error();
			die();
		}
}

?>
<style>
form input, select, div{
	font-family:calibri;
}
</style>
<script>
function loaditfdata(idITF,qid,idBFrom,idBTo,idICFrom,idICTo){
	//alert(idITF+' '+qid+' '+idBFrom+' '+idBTo+' '+idICFrom+' '+idICTo);
	var xmlhttp;
	if (idITF==0){
		document.getElementById('rowmapitf').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){

		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			//alert(xmlhttp.responseText);
			document.getElementById('rowmapitf').innerHTML=xmlhttp.responseText;
			if(idBFrom!=0){
				//Clear cmbbranchfrom
				loadbranch(idBFrom,idICFrom,0)
			}
			if(idBTo!=0){
				//Clear cmbbranchto
				loadbranch(idBTo,idICTo,1)
			}
			if(idBTo==0 & idBFrom==0){
				document.getElementById('cmbbranchfrom').selectedIndex = -1;
				document.getElementById('cmbbranchto').selectedIndex = -1;
				document.getElementById('cmbinventorysheetfrom').innerHTML = '';
				document.getElementById('cmbinventorysheetto').innerHTML = '';
			}
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid='+qid+'&tid='+idITF);

	$('#hdnSelected').attr('value',idITF);
}

function loaddir(bid,imov,did){//imov=0 - From : imov=1 - To
	var xmlhttp;
	var id = '';
	if(imov==0){
		id='cmbinventorysheetfrom';
	}else if(imov==1){
		id='cmbinventorysheetto';
	}

	document.getElementById(id).options.length=0;
	if(bid==0){
		document.getElementById(id).innerHTML = '';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById(id).innerHTML=xmlhttp.responseText;
			if(did==0){
				document.getElementById(id).selectedIndex = -1;
			}
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaddir&bid='+bid+'&imov='+imov+'&did='+did);
}

function loadbranch(bid,did,imov){
	var xmlhttp;
	var id = '';
	var id2 = ''
	if(imov==0){
		id='cmbbranchfrom';
		id2='cmbinventorysheetfrom';
	}else if(imov==1){
		id='cmbbranchto';
		id2='cmbinventorysheetto';
	}

	if(bid==0){
		document.getElementById(id).selectedIndex = -1;
		document.getElementById(id).innerHTML = '';
		return;
	}
	document.getElementById(id).options.length=0;
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById(id).innerHTML=xmlhttp.responseText;
			if(bid==0){
				document.getElementById(id2).innerHTML = '';
				document.getElementById(id).selectedIndex = -1;
			}else{
				loaddir(bid,imov,did);
			}
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadbranch&bid='+bid+'&imov='+imov+'&did='+did);
}

function showactions(idITF,stitle){
	document.getElementById('itfactiontitle').innerHTML = stitle;
	document.getElementById('hdnidITF').value = idITF;
	location.href='#itfaction';
}

function executeaction(action){
	var id = document.getElementById('hdnidITF').value
	var title = document.getElementById('itfactiontitle').innerHTML
	if(action == 'view'){
		var xmlhttp;
		if(id==0){
			document.getElementById('ViewITF').innerHTML = '';
			return;
		}
		if(window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
				document.getElementById('rowviewitf').innerHTML = xmlhttp.responseText;
				document.getElementById('ViewITFTitle').innerHTML = title;
				if(id==0){
					document.getElementById('rowviewitf').innerHTML = '';
				}else{
					location.href='#viewitfdata';
				}
			}
		}
		xmlhttp.open('POST','ajax/ajax.php',true);
		xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xmlhttp.send('qid=itfdata&tid='+id);

	}else if(action == 'delete'){
	<?php
	if($ICLock==0){
	?>
		msgbox('Are you sure you want to remove this ITF? This action cannot be undone.','transfer.php?&bid=<?php echo $_GET['bid'].'&did='.$_GET['did'].'&type='.$_GET['type']; ?>&del=1&itf='+id,'#itfaction');
    <?php
		}
	?>
	}else{
		return;
	}
}


$(document).ready(function(e) {
	$('#frmMapITF').submit(function(){
		if(document.getElementById('hdnSelected').value == ''){
			msgbox('Select an ITF.','','#mapitf');
			return false;
		}else if(document.getElementById('cmbbranchfrom').value == document.getElementById('cmbbranchto').value){
			msgbox('Complete all fields. ','','#mapitf');
			return false;
		}else if(document.getElementById('cmbinventorysheetfrom').value == '' & document.getElementById('cmbinventorysheetto').value == ''){
			msgbox('Complete all fields. ','','#mapitf');
			return false;
		}
	});

	var h = $('#lvtransfercontrol').height()-$('#coltransfercontrol').height();
    $('#rowtransfercontrol').height(h);

	//var h = $('#lvmapitf').height()-$('#colmapitf').height();
    //$('#rowmapitf').height(h);

	var h = $('#lvviewitf').height()-$('#colviewitf').height();
    $('#rowviewitf').height(h);

	loaddir(<?php echo $_GET['bid']; ?>,0,<?php echo $_GET['did']; ?>);
	loaddir(<?php echo $_GET['bid']; ?>,1,<?php echo $_GET['did']; ?>);
});

</script>

<div id="toolbar">
<form>
	<?php
	if($ICLock==0){
	?>
	    <input type="button" title="Map ITF" onClick="location.href='#mapitf'" style="background-image:url(img/icon/mapitf.jpg); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    <?php
		}
	?>
</form>
</div>

<div class="listview" id="lvtransfercontrol">

	<div class="column" id="coltransfercontrol">
	    <div class="columnheader" style="width:100px; text-align:left;">ITF Number</div>
    	<div class="columnheader" style="width:100px;">Date</div>
        <div class="columnheader" style="width:106px;">Branch From</div>
        <div class="columnheader" style="width:106px;">Branch To</div>
        <div class="columnheader" style="width:200px;">From</div>
    	<div class="columnheader" style="width:200px;">To</div>
    	<div class="columnheader" style="width:390px; text-align:left;">Reason</div>
    </div>

    <div class="row" style="height:200px;" id="rowtransfercontrol">
		<?php
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt = $mysqli->stmt_init();
        if($stmt->prepare("Select unTransferControl,TCNumber,TCReason,Concat(MonthName(TCDate) , ' ' , Day(TCDate) , ', ' ,Year(TCDate)) as `TCPeriod`
                            ,bfrom.BName,bto.BName,
                            ifNull((Select Concat(ELastName,', ',EFirstName,' ',Left(EMiddleName,1),'.') From employee Where unEmployee=unEmployeeFrom),'') as `EmployeeFrom`,
                            ifNull((Select Concat(ELastName,', ',EFirstName,' ',Left(EMiddleName,1),'.') From employee Where unEmployee=unEmployeeTo),'') as `EmployeeTo`
                            From transfercontrol
                            Inner Join branch as `bfrom` on transfercontrol.unBranchFrom = bfrom.unBranch
                            Inner Join branch as `bto` on transfercontrol.unBranchTo = bto.unBranch
                            Where unInventoryControlTo=? or unInventoryControlFrom=?")){
            $stmt->bind_param('ii',$_GET['did'],$_GET['did']);
            $stmt->execute();
            $stmt->bind_result($unTransferControl,$TCNumber,$TCReason,$TCPeriod,$BranchFrom,$BranchTo,$EmployeeFrom,$EmployeeTo);
            while($stmt->fetch()){
                ?>
                <div class="listviewitem" onClick="showactions(<?php echo $unTransferControl; ?>,'<?php echo $TCNumber.' - ['.$TCPeriod.']'; ?>')">
                    <div class="listviewsubitem" style="width:100px; text-align:left;" ><?php echo $TCNumber; ?></div>
                    <div class="listviewsubitem" style="width:100px;" ><?php echo $TCPeriod; ?></div>
                    <div class="listviewsubitem" style="width:106px;"><?php echo $BranchFrom; ?></div>
                    <div class="listviewsubitem" style="width:106px;"><?php echo $BranchTo; ?></div>
                    <div class="listviewsubitem" style="width:200px;" ><?php echo $EmployeeFrom; ?></div>
                    <div class="listviewsubitem" style="width:200px;" ><?php echo $EmployeeTo; ?></div>
                    <div class="listviewsubitem" style="width:390px; text-align:left;" ><?php echo $TCReason; ?></div>
                </div>
                <?php
            }
            $stmt->close();
        }
        ?>
	</div>
</div>

<div class="listview">

	<div class="column">
		<div class="columnheader" style="width:250px; text-align:left;">Item</div>
	    <div class="columnheader" style="width:100px;">Quantity</div>
    	<div class="columnheader" style="width:100px;">Unit</div>
    </div>

    <div class="row">
		<?php
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt = $mysqli->stmt_init();
        if($stmt->prepare("Select PIName,PUOMName,TDQuantity,unBranchFrom From transferdata
                            Inner Join productitem on transferdata.unProductItem = productitem.unProductItem
                            Inner Join productuom on transferdata.unProductUOM = productuom.unProductUOM
                            Inner Join transfercontrol on transferdata.unTransferControl = transfercontrol.unTransferControl
                            Where (transfercontrol.unInventoryControlFrom = ? or transfercontrol.unInventoryControlTo = ?) and transferdata.`Status` = 1 Order by PIName Asc")){
            $stmt->bind_param('ii',$_GET['did'],$_GET['did']);
            $stmt->execute();
            $stmt->bind_result($PIName,$PUOMName,$TDQuantity,$idBranchFrom);
            while($stmt->fetch()){
                ?>
                <div class="listviewitem" style="cursor:default;">
                    <div class="listviewsubitem" style="width:250px; text-align:left;" ><?php echo $PIName; ?></div>
                    <div class="listviewsubitem" style="color:#<?php echo (($unBranchFrom==$_GET['bid'])?'F00':'000')?>; width:100px;" ><?php $qty = ($unBranchFrom==$_GET['bid'])?($TDQuantity*-1.00):$TDQuantity; echo number_format((float)$qty, 2, '.', '');?></div>
                    <div class="listviewsubitem" style="width:100px;"><?php echo $PUOMName; ?></div>
                </div>
                <?php
            }
            $stmt->close();
        }
        ?>
    </div>
</div>

<div id="mapitf" class="popup">
	<?php
	$colBranch = new Collection;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? and BType=1 Order by BName Asc")){
		$stmt->bind_param('i',$_SESSION['area']);
		$stmt->execute();
		$stmt->bind_result($unBranch,$BName);
		while($stmt->fetch()){
			$colBranch->Add($BName,$unBranch);
		}
		$stmt->close();
	}
	?>
	<div class="popupcontainer" style="width:590px;">
    	<div class="popuptitle" align="center">Map ITF</div>
        <div class="listbox" style="width:200px; height:300px;">
            <div class="listboxitem" onClick="window.open('createtransfer.php')"><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;">Create New Transfer</div>
			<?php
            $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
            $stmt = $mysqli->stmt_init();
            if($stmt->prepare("Select unTransferControl,TCNumber,concat(MonthName(TCDate) , ' ' , Day(TCDate) , ', ' ,Year(TCDate)) as `TCDate`,unBranchFrom,unBranchTo,unInventoryControlFrom,unInventoryControlTo
								From transfercontrol
								Where `Status`=1
								and (unBranchFrom = ? or unBranchTo = ?) and (unInventoryControlFrom = 0
								or unInventoryControlTo = 0)Order by unTransferControl Desc")){

                $stmt->bind_param('ii',$_GET['bid'],$_GET['bid']);
				$stmt->execute();
                $stmt->bind_result($unTransferControl,$TCNumber,$TCDate,$unBranchFrom,$unBranchTo,$unInventoryControlFrom,$unInventoryControlTo);
                while($stmt->fetch()){
                    ?>
                    <div class="listboxitem" onClick="loaditfdata(<?php echo $unTransferControl; ?>,'itfdata',<?php echo $unBranchFrom; ?>,<?php echo $unBranchTo; ?>,<?php echo $unInventoryControlFrom; ?>,<?php echo $unInventoryControlTo; ?>)"><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;"><?php echo $TCNumber.' - ['.$TCDate.']'; ?></div>
                    <?php
                }
                $stmt->close();
            }
            ?>
        </div>
        <div style="position:absolute; left:230px; top:45px; width:380px; height:300px; background-color:#FFF;">
            <div class="listview" id="lvMAP">
                <div class="column" id="colMAP">
                	<div class="columnheader" style="width:241px; text-align:left;">Item</div>
                    <div class="columnheader" style="width:51px; text-align:center;">Qty</div>
                    <div class="columnheader" style="width:51px; text-align:center;">Unit</div>
                </div>
                <div class="row" id="rowmapitf" style="height:275px;"></div>
            </div>
        </div>
        <form id="frmMapITF" name="frmMapITF" method="post" action="include/transfer.inc.php">
            <div style="padding-top:10px; width:250px;">
                <div style="font-weight:bold;">Branch</div>
                <div style="padding-left:10px; width:240px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left; vertical-align:middle;">From:</div>
                    <select id="cmbbranchfrom" name="cmbbranchfrom" onChange="loaddir(cmbbranchfrom.value,0,0)" style="width:150px;" disabled  >
                        <?php
                        for($i=1;$i<=$colBranch->Count()-1;$i++){
                            ?>
                            <option value="<?php echo $colBranch->GetKey($i); ?>" <?php echo ($_GET['bid']==$colBranch->GetKey($i))?'Selected':''; ?>><?php echo $colBranch->GetByIndex($i); ?></option>
                            <?php
                        }
                        ?>
                    </select>

                </div>
                <div style="padding-left:10px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left;">To:</div>
                    <select id="cmbbranchto" name="cmbbranchto" onChange="loaddir(cmbbranchto.value,1,0)" style="width:150px;" disabled >
                        <?php
                        for($i=1;$i<=$colBranch->Count()-1;$i++){
                            ?>
                            <option value="<?php echo $colBranch->GetKey($i); ?>"><?php echo $colBranch->GetByIndex($i); ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div style="position:absolute; left:270px; top:345px; padding-top:10px; width:300px;">
                <div style="font-weight:bold;">Inventory Sheet</div>
                <div style="padding-left:10px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left; vertical-align:middle;">From:</div>
                    <select name="cmbinventorysheetfrom" id="cmbinventorysheetfrom" style="width:200px;">
                    </select>
                </div>
                <div style="padding-left:10px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left;">To:</div>
                    <select name="cmbinventorysheetto" id="cmbinventorysheetto" style="width:200px;" >
                    </select>
                </div>
            </div>
            <input type="hidden" name="hdnselected" id="hdnSelected" value="" >
            <div align="center" style="padding-top:10px;">
                <input name="btnSaveMapping" type="submit" value="Save" title="Save" class="buttons" >
                <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>
    </div>
</div>

<div class="popup" id="itfaction" style="background-color:transparent;">
    <div class="popupcontainer">
        <div class="popuptitle" id="itfactiontitle" align="center"></div>
        <hr>
        <div align="center">
        <form>
        	<input type="hidden" name="hdnidITF" id="hdnidITF" value="" >
	        <input class="buttons" style="width:90px;" type="button" value="View"  title="View" onClick="executeaction('view')" >
	        <input class="buttons" style="width:90px;" type="button" value="Unmap" title="Unmap" onClick="executeaction('delete')" >
	        <input class="buttons" style="width:90px;" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" >
        </form>
        </div>
    </div>
</div>

<div class="popup" id="viewitfdata">
	<div class="popupcontainer" id="viewitfdatacontainer" style="width:350px;">
        <div class="popuptitle" align="center" id="ViewITFTitle"></div>
        <div class="listview" style="width:350px; height:300px;" id="lvviewitf">
            <div class="column" id="colviewitf">
                <div class="columnheader" style="width:211px;">Item</div>
                <div class="columnheader" style="text-align:center; width:51px;">Qty</div>
                <div class="columnheader" style="text-align:center; width:51px;">Unit</div>
            </div>
            <div class="row" id="rowviewitf">
            </div>
        </div>
        <div align="center" style="padding-top:10px;">
            <input type="button" class="buttons" value="Close" title="Close" onClick="location.href='#close'" >
        </div>
    </div>
</div>

<?php require('footer.php'); ?>

<?php 
	require('header.php'); 
	
	if(isset($_GET['del'])){
		$query = 'Call UnMAPDelivery('.$_GET['did'].','.$_GET['idDC'].')';
		ExecuteNonQuery($query);
		//die($_GET['did']);
		//header('location:delivery.php?&bid='.$_GET['bid'].'&did='.$_GET['did'].'&type='.$_GET['type']);
		echo "<script>location.href='delivery.php?&bid=".$_GET['bid']."&did=".$_GET['did']."&type=".$_GET['type']."'</script>";
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
#frmMapDelivery div, input, select{
	font-family:calibri;
}
</style>
<script>
function loaddelivertdata(idDC,BFrom,BTo,idBranchTo){
	var xmlhttp;
	if (idDC==0){
		document.getElementById('deliverydata').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('deliverydata').innerHTML=xmlhttp.responseText;
			document.getElementById('hdnSelected').value = idDC;
			document.getElementById('txtBranchFrom').value = BFrom;
			document.getElementById('txtBranchTo').value = BTo;
			loaddir(idBranchTo,'<?php $_GET['did']; ?>');
		}
	}
	xmlhttp.open('POST','ajax/delivery.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=ViewDeliveryData&idDC='+idDC);
}
function loaddir(bid,did){//imov=0 - From : imov=1 - To
	var xmlhttp;
	
	document.getElementById('cmbDIRTo').options.length=0;	
	if(bid==0){
		document.getElementById('cmbDIRTo').innerHTML = '';	
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('cmbDIRTo').innerHTML=xmlhttp.responseText;
			if(did==0){
				document.getElementById('cmbDIRTo').selectedIndex = 0;
			}
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaddir&bid='+bid+'&did='+did);
}

function showactions(idDC,stitle){
	document.getElementById('DCActionTitle').innerHTML = stitle;
	document.getElementById('hdnidDC').value = idDC;
	location.href='#DCAction';
}
function executeaction(action){
	var id = document.getElementById('hdnidDC').value;
	var title = document.getElementById('DCActionTitle').innerHTML;
	var xmlhttp;

	if(id==0){return;}

	if(action == 'view'){
		if(id==0){
			document.getElementById('ViewDD').innerHTML = '';	
			return;
		}
		if(window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		}
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('ViewDD').innerHTML=xmlhttp.responseText;
				document.getElementById('DDTitle').innerHTML = title;
				
				location.href='#ViewDeliveryData';
			}
		}
		
		xmlhttp.open('POST','ajax/delivery.ajax.php',true);
		xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xmlhttp.send('qid=ViewDeliveryData&idDC='+id);
		
	}else if(action == 'delete'){
	<?php
	if($ICLock==0){
	?>
		msgbox('Remove delivery. Are you sure?','delivery.php?&bid=<?php echo $_GET['bid'].'&did='.$_GET['did'].'&type='.$_GET['type'].'&del=1'?>&idDC='+id,'#close');
    <?php
		}
	?>
	}else{ return; }
}

$(document).ready(function(e) {
    $('#frmMapDelivery').submit(function(){
		if($('#cmbDIRTo').attr('value') == 0 || $('#hdnSelected').attr('value') == ''){
			msgbox('Error: Select an Inventory Sheet.','','#mapdelivery');
			return false;
		}
    });
	
	var h = $('#lvdeliverycontrol').height()-$('#coldeliverycontrol').height();
    $('#rowdeliverycontrol').height(h);
	
	var h = $('#lvdeliverydata').height()-$('#coldeliverydata').height();
    $('#rowdeliverydata').height(h);
	
	var h = $('#lvMAP').height()-$('#colMAP').height();
    $('#deliverydata').height(h);
});

</script>

<div id="toolbar">
	<?php
	if($ICLock==0){
	?>
	    <input type="button" title="Map Delivery" onClick="location.href='#mapdelivery'" style="background-image:url(img/icon/mapitf.jpg); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    <?php
		}
	?>
</div>

<div class="listview" style="height:200px;" id="lvdeliverycontrol">
	<div class="column" id="coldeliverycontrol">
    	<div class="columnheader" style="width:150px; text-align:left;">Doc Number</div>
        <div class="columnheader" style="width:150px;">Date</div>
        <div class="columnheader" style="width:150px;">Sheet Number</div>
        <div class="columnheader" style="width:150px;">Branch To</div>
        <div class="columnheader" style="width:300px; text-align:left;">Comments</div>
    </div>
    <div class="row" id="rowdeliverycontrol">
    <?php  
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unDeliveryControl,DCDocNum,Concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCDate`,ifNull(bfrom.BName,'') as `BranchFrom`,ifNull(bto.BName,'') as `BranchTo`,DCComments,ifNull(ICNumber,'') as `ICNumber`
							From deliverycontrol
							Left Join branch as `bfrom` on deliverycontrol.unBranchFrom = bfrom.unBranch
							Left Join branch as `bto` on deliverycontrol.unBranchTo = bto.unBranch
							Left Join inventorycontrol on deliverycontrol.unInventoryControl = inventorycontrol.unInventoryControl
							Where deliverycontrol.unInventoryControl = ? Order by unDeliveryControl Desc")){
			$stmt->bind_param('i',$_GET['did']);
			$stmt->execute();
			$stmt->bind_result($unDeliveryControl,$DCDocNum,$DCDate,$BranchFrom,$BranchTo,$DCComments,$ICNumber);
			while($stmt->fetch()){
				?>
                <div class="listviewitem" style="cursor:default;" onClick="showactions(<?php echo $unDeliveryControl;?>,'<?php echo $DCDocNum.' - ['.$DCDate.']'; ?>')">
					<div class="listviewsubitem" style="width:150px; text-align:left;" ><?php echo $DCDocNum; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $DCDate; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $ICNumber; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $BranchTo; ?></div>
                    <div class="listviewsubitem" style="width:300px; text-align:left;" ><?php echo $DCComments; ?></div>
                </div>
				<?php
			}
		}
	?>
    </div>
</div>

<div class="listview" id="lvdeliverydata">
	<div class="column" id="coldeliverydata">
    	<div class="columnheader" style="width:250px;">Item</div>
        <div class="columnheader" style="width:150px; text-align:right;">Quantity</div>
        <div class="columnheader" style="width:150px; text-align:center;">Unit</div>
    </div>
    <div class="row" id="rowdeliverydata">
		<?php 
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select PIName,DDQuantity,PUOMName from deliverydata
                            Inner Join productitem on deliverydata.unProductItem = productitem.unProductItem
                            Inner Join productuom on deliverydata.unProductUOM = productuom.unProductUOM
                            Inner Join deliverycontrol on deliverydata.unDeliveryControl = deliverycontrol.unDeliveryControl
                            Where unInventoryControl = ? and deliverydata.`Status` = 1 Order by PIName Asc")){
			$stmt->bind_param('i',$_GET['did']);
			$stmt->execute();
            $stmt->bind_result($PIName,$DDQuantity,$PUOMName);
            while($stmt->fetch()){
                ?>
                <div class="listviewitem">
                	<div class="listviewsubitem" style="width:250px;"><?php echo $PIName;?></div>
                    <div class="listviewsubitem" style="width:150px; text-align:right;"><?php echo $DDQuantity; ?></div>
                    <div class="listviewsubitem" style="width:150px; text-align:center;"><?php echo $PUOMName; ?></div>
                </div>
                <?php
			}
			$stmt->close();
		}
        ?>
    </div>
</div>

<div id="mapdelivery" class="popup">
	<?php 
	$colBranch = new Collection;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName Asc")){
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
    	<div class="popuptitle" align="center">Map Delivery</div>
        <div class="listbox" style="width:200px; height:300px;">
            <div class="listboxitem" onClick="window.open('createdelivery.php?&bid=<?php echo $_GET['bid']; ?>')"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;">Create New Delivery</div>
			<?php
            $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
            $stmt = $mysqli->stmt_init();
            if($stmt->prepare("Select unDeliveryControl,DCDocNum,concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCDate`,bfrom.BName,bto.BName,unBranchTo From deliverycontrol
								Inner Join branch as `bfrom` on deliverycontrol.unBranchFrom = bfrom.unBranch
								Inner Join branch as `bto` on deliverycontrol.unBranchTo = bto.unBranch
								Where unBranchTo=? and deliverycontrol.`Status`=1 and unInventoryControl = 0 Order by unDeliveryControl Desc")){
				$stmt->bind_param('i',$_GET['bid']);
				$stmt->execute();
                $stmt->bind_result($unDeliveryControl,$DCDocNum,$DCDate,$BranchFrom,$BranchTo,$unBranchTo);
                while($stmt->fetch()){
                    ?>
                    <div class="listboxitem" onClick="loaddelivertdata(<?php echo $unDeliveryControl; ?>,'<?php echo $BranchFrom; ?>','<?php echo $BranchTo; ?>',<?php echo $unBranchTo; ?>)"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;"><?php echo $DCDocNum.' - ['.$DCDate.']'; ?></div>
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
                    <div class="columnheader" style="width:51px; text-align:right;">Qty</div>
                    <div class="columnheader" style="width:51px; text-align:center;">Unit</div>
                </div>
                <div class="row" id="deliverydata" style="height:275px;"></div>
            </div>
        </div>
        <form id="frmMapDelivery" name="frmMapDelivery" method="post" action="include/delivery.inc.php">
            <div style="padding-top:10px; width:250px;">
                <div style="font-weight:bold;">Branch</div>
                <div style="padding-left:10px; width:240px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left; vertical-align:middle;">From:</div>
                    <input type="text"  id="txtBranchFrom" style="width:150px;" readonly >
                </div>
                <div style="padding-left:10px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left;">To:</div>
                    <input type="text" id="txtBranchTo" style="width:150px;" readonly >
                </div>
            </div>
            
            <div style="position:absolute; left:270px; top:345px; padding-top:10px; width:270px;">
                <div style="font-weight:bold;">Inventory Sheet</div>
                <div style="padding-left:10px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left;">To:</div>
                    <select name="cmbDIRTo" id="cmbDIRTo" style="width:200px;" >
                    </select>
                </div>
            </div>
            <input type="hidden" name="hdnidDC" id="hdnSelected" value="" >
            <div align="center" style="padding-top:10px;">
                <input name="btnSaveMapping" type="submit" value="Save" title="Save" class="buttons" >
                <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>
    </div>
</div>

<div class="popup" id="DCAction" style="background-color:transparent;">
    <div class="popupcontainer">
        <div class="popuptitle" id="DCActionTitle" align="center"></div>
        <hr>
        <div align="center">
        <form>
        	<input type="hidden" name="hdnidDC" id="hdnidDC" value="0" >
	        <input class="buttons" style="width:90px;" type="button" value="View"  title="View" onClick="executeaction('view')" >
	        <input class="buttons" style="width:90px;" type="button" value="Unmap" title="Unmap" onClick="executeaction('delete')" >
	        <input class="buttons" style="width:90px;" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" >
        </form>
        </div>
    </div>
</div>

<div class="popup" id="ViewDeliveryData">
	<div class="popupcontainer" style="width:375px;">
    	<div class="popuptitle" id="DDTitle" align="center"></div>
        <div class="listview" style="width:378px; height:300px;">
            <div class="column">
                <div class="columnheader" style="width:241px; text-align:left;" >Item</div>
                <div class="columnheader" style="width:51px; text-align:right;" >Qty</div>
                <div class="columnheader" style="width:51px; text-align:center;" >Unit</div>
            </div>
            <div class="row" id="ViewDD" style="height:270px;"></div>
        </div> 
        <div align="center" style="margin-top:5px;"><input type="button" onClick="location.href='#close'" class="buttons" title="Close" value="Close" ></div>
    </div>
</div>

<?php require('footer.php'); ?>
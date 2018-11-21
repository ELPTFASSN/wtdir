<?php
	include 'header.php';
	
	if(isset($_GET['del'])){
		$query = 'Call UnMAPSales('.$_GET['did'].','.$_GET['idSD'].')';
		ExecuteNonQuery($query);
		echo "<script>location.href='sold.php?&bid=".$_GET['bid']."&did=".$_GET['did']."&type=".$_GET['type']."'</script>";
	}
?>

<style>
form, input, select, div{
	font-family:calibri;
}
</style>
<script src="js/sold.js"></script>
<script>

function loadshift(bid,scid,stateSC){
	if(stateSC==0){
		$('.listviewitem').css('background-color','transparent');
		$('.selectedSC').empty();
		$('#listviewitemSC-'+scid).css('background-color','#B7E3F0');
		$('#selectedSC-'+scid).append(' - Day Selected');	
		$.post('ajax/sold.ajax.php',
			{
				qid:'LoadShift',
				bid:bid,
				scid:scid,
			},
			function(data,status){
				obj = JSON.stringify(data);
				$('#soldshiftdata').empty();
				$('#soldshiftdata').append(data);
			});
	}else if(stateSC==1){
		$('.listviewitem').css('background-color','transparent');
		$('.selectedSC').empty();
		$(this).css('background-color','#B7E3F0');
		$('#soldshiftdata').empty();
		$('#selectedSC-'+scid).append(' - Day Still Open!');	
	}
}

function SEThdnunSD(bid,unsd,stateSD){
	$('.listviewitem').css('background-color','transparent');
	$('.selected').empty();
	if(stateSD==1){
		$('#hdnunBID').val(bid);
		$('#hdnunSD').val(unsd);
		$('#listviewitem-'+unsd).css('background-color','#B7E3F0');
		$('#selected-'+unsd).append(' - Map Shift');
	}else{
		$('#listviewitemSD-'+unsd).css('background-color','#BBB');
		$('#selectedSD-'+unsd).append(' - Shift Still Open!');
	}
	
}

function loaddir(bid,did){
	var xmlhttp;
	
	document.getElementById('cmbDIRFrom').options.length=0;	
	if(bid==0){
		document.getElementById('cmbDIRFrom').innerHTML = '';	
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('cmbDIRFrom').innerHTML=xmlhttp.responseText;
			if(did==0){
				document.getElementById('cmbDIRFrom').selectedIndex = 0;
			}
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaddir&bid='+bid+'&did='+did);
}

function showactions(idSD,stitle){
	document.getElementById('SDActionTitle').innerHTML = stitle;
	document.getElementById('hdnidSD').value = idSD;
	location.href='#SDAction';
}

function executeaction(action,bid,idID){
	var id = document.getElementById('hdnidSD').value;
	var title = document.getElementById('SDActionTitle').innerHTML;
	var xmlhttp;

	if(id==0){return;}

	if(action == 'view'){
		if(id==0){
			alert(id);
			document.getElementById('ViewSD').innerHTML = '';	
			return;
		}
		if(window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		}
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('ViewSD').innerHTML=xmlhttp.responseText;
				document.getElementById('SDTitle').innerHTML = title;
				
				location.href='#ViewSoldData';
			}
		}
		
		xmlhttp.open('POST','ajax/sold.ajax.php',true);
		xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xmlhttp.send('qid=ViewSoldData&idSD='+id+'&bid='+bid);
		
	}
	 else if(action == 'invoice'){
		if(id==0){
			alert(id);
			document.getElementById('ViewSD').innerHTML = '';	
			return;
		}
		if(window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		}
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('ViewSI').innerHTML=xmlhttp.responseText;
				document.getElementById('SITitle').innerHTML = title;
				
				location.href='#ViewSoldInvoice';
			}
		}
		
		xmlhttp.open('POST','ajax/sold.ajax.php',true);
		xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xmlhttp.send('qid=ViewSoldInvoice&idSD='+id+'&bid='+bid);
		
	}
	else if(action == 'invoicedata'){
		//document.getElementsByClassName('listviewitem').style.backgroundColor='white';
		elements = document.getElementsByClassName('listviewitem');
    		for (var i = 0; i < elements.length; i++) {
        	elements[i].style.backgroundColor="transparent";
    	}
		document.getElementById('listviewitem-'+idID).style.backgroundColor='#EEE';
		if(id==0){
			alert(id);
			document.getElementById('ViewSD').innerHTML = '';	
			return;
		}
		if(window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		}
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('ViewSID').innerHTML=xmlhttp.responseText;
				document.getElementById('SITitle').innerHTML = title;
				
				location.href='#ViewSoldInvoice';
			}
		}
		
		xmlhttp.open('POST','ajax/sold.ajax.php',true);
		xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xmlhttp.send('qid=ViewInvoiceData&idID='+idID+'&bid='+bid);
		
	}
	else if(action == 'delete'){
	<?php
	if($ICLock==0){
	?>
		msgbox('Remove this item. Are you sure?','sold.php?&bid=<?php echo $_GET['bid'].'&did='.$_GET['did'].'&type='.$_GET['type'].'&del=1'?>&idSD='+id,'#close');
    <?php
		}
	?>
	}else{ return; }
}

$(document).ready(function(e) {
    $('#frmMapSold').submit(function(){
		if($('#cmbDIRFrom').attr('value') == 0 || $('#hdnSelected').attr('value') == ''){
			msgbox('Error: Select an Inventory Sheet.','','#mapsold');
			return false;
		}
    });
	
	var h = $('#lvsold').height()-$('#colsold').height();
    $('#rowsold').height(h);
	
	var h = $('#lvsolddata').height()-$('#colsolddata').height();
    $('#rowsolddata').height(h);
	
	var h = $('#lvMAP').height()-$('#colMAP').height();
    $('#solddata').height(h);
});
</script>

<style>
input{
	font-family:calibri;
}
</style>

<div id="toolbar">
	<?php
	if($ICLock==0){
	?>
	    <input type="button" title="Map Sales" onClick="location.href='#mapsales'" style="background-image:url(img/icon/mapitf.jpg); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    <?php
		}
	?>
</div>

<div class="listview" style="height:400px;" id="lvsold">
	<div class="column" id="colsold">
    	<div class="columnheader" style="width:75px; text-align:left;">Date ID</div>
    	<div class="columnheader" style="width:75px; text-align:left;">Shift ID</div>
        <div class="columnheader" style="width:100px;">Date</div>
        <div class="columnheader" style="width:100px;">Time</div>
        <div class="columnheader" style="width:150px;">Cashier</div>
        <div class="columnheader" style="width:150px;">Beginning Balance</div>
        <div class="columnheader" style="width:150px;">Ending Balance</div>
        <div class="columnheader" style="width:150px;">Total Sales</div>
        <div class="columnheader" style="width:150px;">Discount</div>
        <div class="columnheader" style="width:150px;">Net Sales</div>
    </div>
    <div class="row" id="rowsold">
    <?php  
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("SELECT unSalesData,unSalesControl,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee,SDBalanceStart,SDBalanceEnd,SDTotalAmount,SDDiscount,SDNetSales
							FROM salesdata
							LEFT JOIN employee as `EO` on salesdata.unEmployee = EO.unEmployee
							WHERE unInventoryControl = ? AND unBranch = ? ORDER BY unSalesData,unSalesControl ASC")){
			$stmt->bind_param('ii',$_GET['did'],$_GET['bid']);
			$stmt->execute();
			$stmt->bind_result($unSalesData,$unSalesControl,$SDTimeStart,$unEmployee,$SDBalanceStart,$SDBalanceEnd,$SDTotalAmount,$SDDiscount,$SDNetSales);
			while($stmt->fetch()){
				$unSalesData1=sprintf('%06d', $unSalesData);
				$unSalesControl1=sprintf('%06d', $unSalesControl);
				?>
                <div class="listviewitem" style="cursor:default;" onClick="showactions(<?php echo $unSalesData;?>,'<?php echo $unSalesData.' - ['.$SDTimeStart.']'; ?>')">
               		<div class="listviewsubitem" style="width:75px; text-align:left;" ><?php echo $unSalesControl1; ?></div>
					<div class="listviewsubitem" style="width:75px; text-align:left;" ><?php echo $unSalesData1; ?></div>
                    <div class="listviewsubitem" style="width:100px;"><?php echo date('Y-m-d',strtotime($SDTimeStart)); ?></div>
                    <div class="listviewsubitem" style="width:100px;"><?php echo date('H:i:sa',strtotime($SDTimeStart)); ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $unEmployee; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $SDBalanceStart; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $SDBalanceEnd; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $SDTotalAmount; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $SDDiscount; ?></div>
                    <div class="listviewsubitem" style="width:150px;"><?php echo $SDNetSales; ?></div>
                </div>
				<?php
			}
		}
	?>
    </div>
</div>

<div id="mapsales" class="popup">
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
	<div class="popupcontainer" style="width:800px; top:-60px; height:530px;">
    	<div class="popuptitle" align="center">Map Sold</div>
        <div style=" left:230px; top:45px; width:800px; height:300px; background-color:#FFF;">
            <div class="listview" id="lvMAP">
                <div class="column" id="colMAP">
                    <div class="columnheader" style="width:100px; text-align:left;">Date ID</div>
        			<div class="columnheader" style="width:100px;">Date</div>
                    <div class="columnheader" style="width:150px;">Net Sales</div>
                    <div class="columnheader" style="width:150px;">Previous Reading</div>
                    <div class="columnheader" style="width:150px;">Current Reading</div>
                </div>
                <div class="row" id="solddata" style="height:275px;">
                	<!--<div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px;"><b>Create New Sales</b></div>-->
                	<?php  
						$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("SELECT unSalesControl,SCState,SCTimeStart,SCNetSales,SCReadingPrevious,SCReadingCurrent
											FROM salescontrol
											WHERE unInventoryControl = 0 AND unBranch=? ORDER BY unBranch DESC")){
							$stmt->bind_param('i',$_GET['bid']);
							$stmt->execute();
							$stmt->bind_result($unSalesControl,$SCState,$SCTimeStart,$SCNetSales,$SCReadingPrevious,$SCReadingCurrent);
							while($stmt->fetch()){
								$unSalesControl1=sprintf('%06d',$unSalesControl);
								?>
								<div class="listviewitem" style="cursor:default;" onClick="loadshift(<?php echo $_GET['bid']?>,<?php echo $unSalesControl; ?>,<?php if($SCState=='Close'){echo '0';}else{echo '1';};?>)">
									<div class="listviewsubitem" style="width:80px; text-align:left; background-image:url(img/icon/<?php if($SCState=='Close'){echo 'SCclosed';}else{echo 'SCopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesControl1; ?></div>
									<div class="listviewsubitem" style="width:100px;"><?php echo date('Y-m-d',strtotime($SCTimeStart)); ?></div>
									<div class="listviewsubitem" style="width:150px;"><?php echo $SCNetSales; ?></div>
									<div class="listviewsubitem" style="width:150px;"><?php echo $SCReadingPrevious; ?></div>
									<div class="listviewsubitem" style="width:150px;"><?php echo $SCReadingCurrent; ?></div>
                                    <div class="selectedSC" id="selectedSC-<?php echo $unSalesControl; ?>" style="padding-left:30px;padding-top:5px"></div>
								</div>
								<?php
							}
						}
					?>
                </div>
            </div>
        </div>
        <div style="position:relative; width:800px; height:150px; background-color:#FFF; margin-top:20px;">
            <div class="listview" id="lvMAP" style="min-height:150px;">
                <div class="column" id="colMAP">
                    <div class="columnheader" style="width:100px; text-align:left;">Shift ID</div>
        			<div class="columnheader" style="width:100px; text-align:left;">Time Start</div>
                    <div class="columnheader" style="width:150px; text-align:left;">Cashier</div>
                    <div class="columnheader" style="width:150px; text-align:left;">Net Sales</div>
                </div>
                <div class="row" style="height:130px;">
                	<!--<div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px;"><b>Create New Shift </b></div>-->
                	<div id="soldshiftdata" style="cursor:default;">
                    </div>
                </div>
            </div>
        </div>
        <form id="frmMapSold" name="frmMapSold" method="post" action="include/sold.inc.php" style="position:relative">
            <input type="hidden" name="hdnunIC" id="hdnunIC" value="<?php echo $_GET['did'];?>" >
            <input type="hidden" name="hdnunSD" id="hdnunSD" value="0" >
            <input type="hidden" name="hdnunBID" id="hdnunBID" value="0" >
                <div align="center" style="padding-top:10px; position:relative">
                    <input name="btnSaveMapping" type="submit" value="Map" title="Map" class="buttons" >
                    <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
                </div>
        </form>
    </div>
</div>

<div class="popup" id="SDAction" style="background-color:transparent;">
    <div class="popupcontainer" style="width:500px">
        <div class="popuptitle" id="SDActionTitle" align="center"></div>
        <hr>
        <div align="center">
        <form>
        	<input type="hidden" name="hdnidSD" id="hdnidSD" value="0" >
	        <input class="buttons" style="width:120px;" type="button" value="View Product Mix"  title="View Product Mix" onClick="executeaction('view',<?php echo $_GET['bid']; ?>)" >
            <input class="buttons" style="width:120px;" type="button" value="View Invoice"  title="View Invoice" onClick="executeaction('invoice',<?php echo $_GET['bid']; ?>)" >
	        <input class="buttons" style="width:90px;" type="button" value="Unmap" title="Unmap" onClick="executeaction('delete',<?php echo $_GET['bid']; ?>)" >
	        <input class="buttons" style="width:90px;" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" >
        </form>
        </div>
    </div>
</div>

<div class="popup" id="ViewSoldData">
	<div class="popupcontainer" style="width:375px;">
    	<div class="popuptitle" id="SDTitle" align="center"></div>
        <div class="listview" style="width:378px; height:300px;">
            <div class="column">
                <div class="columnheader" style="width:241px; text-align:left;" >Item</div>
                <div class="columnheader" style="width:51px; text-align:right;" >Qty</div>
                <div class="columnheader" style="width:51px; text-align:center;" >Unit</div>
            </div>
            <div class="row" id="ViewSD" style="height:270px;"></div>
        </div> 
        <div align="center" style="margin-top:5px;"><input type="button" onClick="location.href='#close'" class="buttons" title="Close" value="Close" ></div>
    </div>
</div>

<div class="popup" id="ViewSoldInvoice">
	<div class="popupcontainer" style="width:900px; top:-50px;">
    	<div class="popuptitle" id="SITitle" align="center"></div>
        <div class="listview" style="width:350px; height:500px; display:inline-table; position:relative">
            <div class="column">
                <div class="columnheader" style="width:30px; text-align:center;" >INV#</div>
                <div class="columnheader" style="width:90px; text-align:right;" >Net</div>
                <div class="columnheader" style="width:90px; text-align:right;" >Discount</div>
                <div class="columnheader" style="width:110px; text-align:right;" >Total</div>
            </div>
            <div class="row" id="ViewSI" style="height:500px;"></div>
        </div>
        <div class="listview" style="width:543px; height:500px;display:inline-table; position:relative;">
            <div class="column">
                <div class="columnheader" style="width:15px; text-align:center;" >Qty</div>
                <div class="columnheader" style="width:270px; text-align:center;" >Item</div>
                <div class="columnheader" style="width:100px; text-align:right;" >Price</div>
                <div class="columnheader" style="width:100px; text-align:right;" >Total</div>
            </div>
            <div class="row" id="ViewSID" style="height:500px;"></div>
        </div>
        <div align="center" style="margin-top:5px;"><input type="button" onClick="location.href='#close'" class="buttons" title="Close" value="Close" ></div>
    </div>
</div>

<?php
	include 'footer.php';
?>

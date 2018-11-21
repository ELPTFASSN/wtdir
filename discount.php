<?php
	include 'header.php';

	if(isset($_GET['del'])){
		$query = 'Update discountcontrol set unInventoryControl=0 where unDiscountControl='.$_GET['idDC'];
		ExecuteNonQuery($query);
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select Sum(DCDiscount) as DCDiscount From discountcontrol Where `Status`=1 and unInventoryControl=?")){
			$stmt->bind_param('i',$_GET['did']);
			$stmt->execute();
			$stmt->bind_result($DCDiscount);
			$stmt->fetch();
			$stmt->close();			
		}

		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update sales set SDiscount=?,SEndingBalance=(Select SBeginningBalance+STotalSales-SCashDeposit-SPettyCash-SDiscount) Where unInventoryControl=?")){
			$stmt->bind_param('di',$DCDiscount,$_GET['did']);
			$stmt->execute();
			$stmt->close();			
		}
		
		echo "<script>location.href='discount.php?&bid=".$_GET['bid']."&did=".$_GET['did']."&type=".$_GET['type']."'</script>";
	}
?>
<script>
function loaddiscountdata(idDC){
	var xmlhttp;
	if (idDC==0){
		document.getElementById('discountdata').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('discountdata').innerHTML=xmlhttp.responseText;
			document.getElementById('hdnSelected').value = idDC;
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaddiscountdata&idDC='+idDC);
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
				
				location.href='#ViewDiscountData';
			}
		}
		
		xmlhttp.open('POST','ajax/ajax.php',true);
		xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xmlhttp.send('qid=loaddiscountdata&idDC='+id);
		
	}else if(action == 'delete'){
	<?php
	if($ICLock==0){
	?>
		msgbox('Remove discount. Are you sure?','discount.php?&bid=<?php echo $_GET['bid'].'&did='.$_GET['did'].'&type='.$_GET['type'].'&del=1'?>&idDC='+id,'#close');
    <?php
		}
	?>
	}else{ return; }
}


$(document).ready(function() {
    $('#frmMapDiscount').submit(function(){
		if($('#hdnSelected').attr('value') == ''){
			msgbox('Error: Select a Discount.','','#mapdiscount');
			return false;
		}
    });

	var h = $('#lvdiscountcontrol').height()-$('#coldisocountcontrol').height();
   $('#rowdiscountcontrol').height(h);
	var h = $('#lvdiscountdata').height()-$('#coldiscountdata').height();
   $('#rowdiscountdata').height(h);
});
</script>

<div id="toolbar">
	<?php
	if($ICLock==0){
	?>
	    <input type="button" title="Map Discount" onClick="location.href='#mapdiscount'" style="background-image:url(img/icon/mapitf.jpg); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    <?php
		}
	?>
</div>
<div class="listview" id="lvdiscountcontrol" style="min-height:200px;">
	<div class="column" id="coldiscountcontrol">
    	<div class="columnheader" style="width:130px;text-align:left;">Type</div>
        <div class="columnheader" style="width:130px;text-align:center;">Date</div>
        <div class="columnheader" style="width:130px;text-align:right;">Pax</div>
        <div class="columnheader" style="width:130px;text-align:right;">Reference</div>
        <div class="columnheader" style="width:130px;text-align:right;">Invoice</div>
        <div class="columnheader" style="width:130px;text-align:right;">Total</div>
        <div class="columnheader" style="width:130px;text-align:right;">Net Of VAT</div>
        <div class="columnheader" style="width:130px;text-align:right;">Discount</div>
        <div class="columnheader" style="width:130px;text-align:right;">Net Price</div>
    </div>
    <div id="row" id="rowdiscountcontrol">
	<?php
		$Total=0;
		$NetOfVat=0;
		$Discount=0;
		$NetPrice=0;
		$myqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT unDiscountControl,DTName,DCDate,DCPax,DCReference,DCInvoice,DCTotal,DCNetOfVat,DCDiscount,DCNetPrice FROM discountcontrol INNER JOIN discounttype ON discountcontrol.unDiscountType=discounttype.unDiscountType WHERE discountcontrol.`Status`=1 and unInventoryControl=?")){
			$stmt->bind_param('i',$_GET['did']);
			$stmt->execute();
			$stmt->bind_result($unDiscountControl,$DTName,$DCDate,$DCPax,$DCReference,$DCInvoice,$DCTotal,$DCNetOfVat,$DCDiscount,$DCNetPrice);
			while($stmt->fetch()){
			?>
                <div class="listviewitem" style="cursor:default;" onClick="showactions(<?php echo $unDiscountControl;?>,'<?php echo $DTName.'  '.$DCReference; ?>')">
                    <div class="listviewsubitem" style="width:130px;text-align:left;"><?php echo $DTName; ?></div>
                    <div class="listviewsubitem" style="width:130px;text-align:center;"><?php echo $DCDate; ?></div>
                    <div class="listviewsubitem" style="width:130px;text-align:right;"><?php echo $DCPax; ?></div>
                    <div class="listviewsubitem" style="width:130px;text-align:right;"><?php echo $DCReference; ?></div>
                    <div class="listviewsubitem" style="width:130px;text-align:right;"><?php echo $DCInvoice; ?></div>
                    <div class="listviewsubitem" style="width:130px;text-align:right;"><?php echo $DCTotal; ?></div>
                    <div class="listviewsubitem" style="width:130px;text-align:right;"><?php echo $DCNetOfVat; ?></div>
                    <div class="listviewsubitem" style="width:130px;text-align:right;"><?php echo $DCDiscount; ?></div>
                    <div class="listviewsubitem" style="width:130px;text-align:right;"><?php echo $DCNetPrice; ?></div>
                </div>
			<?php
			$Total +=$DCTotal;
			$NetOfVat +=$DCNetOfVat;
			$Discount +=$DCDiscount;
			$NetPrice +=$DCNetPrice;
			}
			$stmt->close();
		}
	?>
    </div>
</div>

<div class="column" style="width:auto;height:21px;color:#333;padding-top:4px;">
	<div class="columnheader" id="DCTotal" style="width:805px;text-align:right;float:left;"><?php echo number_format((float)$Total, 4, '.', ''); ?></div>
	<div class="columnheader" id="DCNetOfVat" style="width:134px;text-align:right;float:left;"><?php echo number_format((float)$NetOfVat, 4, '.', ''); ?></div>
	<div class="columnheader" id="DCDiscount" style="width:134px;text-align:right;float:left;"><?php echo number_format((float)$Discount, 4, '.', ''); ?></div>
	<div class="columnheader" id="DCNetPrice" style="width:134px;text-align:right;float:left;color:#900;"><?php echo number_format((float)$NetPrice, 4, '.', ''); ?></div>
</div>

<div class="listview" id="lvdiscountdata">
	<div class="column" id="coldiscountdata">
    	<div class="columnheader" style="width:150px;text-align:left;">Item</div>
        <div class="columnheader" style="width:150px;text-align:right;">Quantity</div>
        <div class="columnheader" style="width:150px;text-align:right;">Price</div>
        <div class="columnheader" style="width:150px;text-align:right;">Total</div>
    </div>
    <div class="row" id="rowdiscountdata">
	<?php
		$mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Select PIName,DDQuantity,DDPrice,(DDQuantity*DDPrice) as DDTotal From discountdata Inner Join productitem on discountdata.unProductItem=productitem.unProductItem Inner Join discountcontrol on discountdata.unDiscountControl=discountcontrol.unDiscountControl Where discountdata.`Status`=1 and discountcontrol.unInventoryControl=?")){
			$stmt->bind_param('i',$_GET['did']);
			$stmt->execute();
			$stmt->bind_result($PIName,$DDQuantity,$DDPrice,$DDTotal);
			while($stmt->fetch()){
			?>
                <div class="listviewitem" style="cursor:default;" onClick="">
                    <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $PIName; ?></div>
                    <div class="listviewsubitem" style="width:150px;text-align:right;"><?php echo $DDQuantity; ?></div>
                    <div class="listviewsubitem" style="width:150px;text-align:right;"><?php echo $DDPrice; ?></div>
                    <div class="listviewsubitem" style="width:150px;text-align:right;"><?php echo $DDTotal; ?></div>
                </div>
            <?php
			}
			$stmt->close();
		}
	?>
    </div>
</div>
<div id="mapdiscount" class="popup">
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
    	<div class="popuptitle" align="center">MAP DISCOUNT</div>
        <div class="listbox" style="width:200px;height:300px;">
        	<div class="listboxitem" onClick="window.open('creatediscount.php?&bid=<?php echo $_GET['bid']; ?>')"><img src="img/icon/discount.png" width="16" height="16" style="padding-right:10px;">Create New Discount</div>
            <?php
            $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
            $stmt = $mysqli->stmt_init();
            if($stmt->prepare("Select unDiscountControl,DCReference,concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCDate` From discountcontrol Where `Status`=1 and unInventoryControl=0 and unArea=? and unBranch=? Order by unDiscountControl desc")){
				$stmt->bind_param('ii',$_SESSION['area'],$_GET['bid']);
				$stmt->execute();
                $stmt->bind_result($unDiscountControl,$DCReference,$DCDate);
                while($stmt->fetch()){
                    ?>
                    <div class="listboxitem" onClick="loaddiscountdata(<?php echo $unDiscountControl; ?>)"><img src="img/icon/discount.png" width="16" height="16" style="padding-right:10px;"><?php echo $DCReference.' - [ '.$DCDate.' ]'; ?></div>
                    <?php
                }
                $stmt->close();
            }
            ?>
        </div>
        <div class="listview" style="position:absolute; left:230px; top:45px; width:380px; height:300px;">
            <div class="column">
                <div class="columnheader" style="width:221px;text-align:left">Item</div>
                <div class="columnheader" style="width:50px;text-align:right">QTY</div>
                <div class="columnheader" style="width:70px;text-align:right">Price</div>
            </div>
            <div class="row" id="discountdata"></div>
        </div>
        <form id="frmMapDiscount" name="frmMapDiscount" method="post" action="include/discount.inc.php">
        	<input type="hidden" name="hdndid" value="<?php echo $_GET['did']; ?>" >
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

<div class="popup" id="ViewDiscountData">
	<div class="popupcontainer" style="width:380px;">
    	<div class="popuptitle" id="DDTitle" align="center"></div>
        <div class="listview" style="width:370px; height:300px;">
            <div class="column">
                <div class="columnheader" style="width:200px;text-align:left;">Item</div>
                <div class="columnheader" style="width:50px;text-align:right;">Quantity</div>
                <div class="columnheader" style="width:70px;text-align:right;">Price</div>
            </div>
            <div class="row" id="ViewDD"></div>
        </div> 
        <div align="center" style="margin-top:5px;"><input type="button" onClick="location.href='#close'" class="buttons" title="Close" value="Close" ></div>
    </div>
</div>

<?php
	include 'footer.php';
?>

<?php
include 'header.php';

if (isset($_POST['btnpaymenttypesave'])){
	$query="Update paymenttype set PTName='".$_POST['txtpaymenttype']."',PTFixedAmount=".$_POST['txtfixedamount']." where unPaymentType=".$_POST['ptid'];
	ExecuteNonQuery($query);
}

if (isset($_POST['btnpaymenttypeadd'])){
	$query="Insert Into paymenttype (PTName,unPaymentType,PTFixedAmount) SELECT '".$_POST['txtpaymenttype']."',ifnull(max(unPaymentType),0)+1,".$_POST['txtfixedamount']." FROM paymenttype";
	ExecuteNonQuery($query);
}
if (isset($_GET['del'])){
	$query="Update paymenttype set `Status`=0 where unPaymentType=".$_GET['paymenttype'];
	ExecuteNonQuery($query);
	header('location:paymenttype.php');
}

?>
<script>
function loadpaymenttypeinfo(unPaymentType){
	var xmlhttp;
	if (unPaymentType==0){
		document.getElementById('editpaymenttype').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editpaymenttype').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadpaymenttypeinfo&ptid='+unPaymentType);
}

$(document).ready(function() {
		var h = $('#lvpaymenttype').height()-$('#colpaymenttype').height();
       $('#rowpaymenttype').height(h);
});
$(document).scroll(function(){
	columnheader('colpaymenttype','lvpaymentype');
});
</script>
<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createpaymenttype'" style="background-image:url(img/icon/paymenttype.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvpaymenttype">

	<div class="column" id="colpaymenttype">
        <div class="columnheader" style="width:220px;text-align:left;">Payment Type</div>
    	<div class="columnheader" style="width:100px;text-align:left;">Fixed Amount</div>
    </div>  
	<div class="row" id="rowpaymenttype">
	<?php 
		$i=0;
        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("Select unPaymentType,PTName,PTFixedAmount,Status From paymenttype Where Status=1")){
        $stmt->execute();
        $stmt->bind_result($unPaymentType,$PTName,$PTFixedAmount,$Status);
        while($stmt->fetch()){
            ?>
            <div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:220px;text-align:left;"><?php echo $PTName;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $PTFixedAmount;?></div>
				<?php /*
                    if($Status==1){
                ?>               
                        <div class="listviewsubitem" style="width:100px;text-align:left;" title="Deactivate Payment Type" onclick="msgbox('Deactivate [ <strong><?php echo $PTName;?></strong> ], Are you sure?','paymenttype.php?&paymenttype=' + <?php echo $unPaymentType; ?> + '&del=1','')" style="cursor:pointer;">Deactivate</div>
                <?php
                    }else{
                ?>
                        <div class="listviewsubitem" style="width:100px;text-align:left;" title="Activate Payment Type" onclick="msgbox('Activate [ <strong><?php echo $PTName;?></strong> ], Are you sure?','paymenttype.php?&paymenttype=' + <?php echo $unPaymentType; ?> + '&del=1','')" style="cursor:pointer;">Activate</div>
                <?php
                    }*/
                ?>


                <div class="listviewsubitem" style="width:150px;text-align:left;">
                    <div title="Edit [ <?php echo $PTName;?> ]" class="button16" onclick="loadpaymenttypeinfo(<?php echo $unPaymentType; ?>)" style="background-image:url(img/icon/update.png);margin:auto;"></div>
                    <div title="Delete [ <?php echo $PTName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $PTName;?></strong> ], Are you sure?','paymenttype.php?&paymenttype=' + <?php echo $unPaymentType; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>
                </div>
            </div>
            <?php
			$i++;
            }
        $stmt->close();
        }
    ?>
    </div>
</div>

<div id="popupedit" class="popup">
	<div id="editpaymenttype" class="popupcontainer"></div>
</div>




<?php
include 'footer.php';
?>
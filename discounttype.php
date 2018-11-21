<?php include 'header.php';


if (isset($_POST['btndiscounttypesave'])){
	$query="Update discounttype set DTName='".$_POST['txtdiscounttype']."',DTPercent=".$_POST['txtpercent'].",DTAmount=".$_POST['txtamount'].",DTVatExempt=".$_POST['txtvatexempt']." where unDiscountType=".$_POST['dtid'];
	ExecuteNonQuery($query);
}

if (isset($_POST['btndiscounttypeadd'])){
	$query="Insert Into discounttype (DTName,unDiscountType,DTPercent,DTAmount,DTVatExempt) SELECT '".$_POST['txtdiscounttype']."',ifnull(max(unDiscountType),0)+1,".$_POST['txtpercent'].",".$_POST['txtamount'].",".$_POST['txtvatexempt']." FROM discounttype";
	ExecuteNonQuery($query);
}
if (isset($_GET['del'])){
	$query="Update discounttype set `Status`=0 where unDiscountType=".$_GET['discounttype'];
	ExecuteNonQuery($query);
	header('location:discounttype.php');
}

?>

<script>
function loaddiscounttypeinfo(unDiscountType){
	var xmlhttp;
	if (unDiscountType==0){
		document.getElementById('editdiscounttype').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editdiscounttype').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaddiscounttypeinfo&dtid='+unDiscountType);
}

$(document).ready(function() {
		var h = $('#lvdiscounttype').height()-$('#coldiscounttype').height();
       $('#rowdiscounttype').height(h);
});
$(document).scroll(function(){
	columnheader('coldiscounttype','lvdiscounttype');
});
</script>

<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#creatediscounttype'" style="background-image:url(img/icon/discounttype.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvdiscounttype">
	<div class="column" id="coldiscounttype">
        <div class="columnheader" style="width:220px;text-align:left;">Discount Type</div>
    	<div class="columnheader" style="width:100px;text-align:left;">Percent</div>
        <div class="columnheader" style="width:100px;text-align:left;">Amount</div>
        <div class="columnheader" style="width:100px;text-align:left;">Vat Exempt</div>
    </div>  
	<div class="row" id="rowdiscounttype">
	<?php 
		$i=0;
        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("Select unDiscountType,DTName,DTPercent,DTAmount,DTVatExempt,Status From discounttype Where Status=1")){
        $stmt->execute();
        $stmt->bind_result($unDiscountType,$DTName,$DTPercent,$DTAmount,$DTVatExempt,$Status);
        while($stmt->fetch()){
            ?>
            <div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:220px;text-align:left;"><?php echo $DTName;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $DTPercent;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $DTAmount;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $DTVatExempt;?></div>
				<?php
                   /* if($Status==1){
                ?>               
                        <div class="listviewsubitem" style="width:100px;text-align:left;" title="Deactivate Discount Type" onclick="msgbox('Deactivate [ <strong><?php echo $DTName;?></strong> ], Are you sure?','discounttype.php?&discounttype=' + <?php echo $unDiscountType; ?> + '&del=1','')" style="cursor:pointer;">Deactivate</div>
                <?php
                    }else{
                ?>
                        <div class="listviewsubitem" style="width:100px;text-align:left;" title="Activate Discount Type" onclick="msgbox('Activate [ <strong><?php echo $DTName;?></strong> ], Are you sure?','discounttype.php?&discounttype=' + <?php echo $unDiscountType; ?> + '&del=1','')" style="cursor:pointer;">Activate</div>
                <?php
                    }*/
                ?>


                <div class="listviewsubitem" style="width:150px;text-align:left;">
                    <div title="Edit [ <?php echo $DTName;?> ]" class="button16" onclick="loaddiscounttypeinfo(<?php echo $unDiscountType; ?>)" style="background-image:url(img/icon/update.png);margin:auto;"></div>
                    <div title="Delete [ <?php echo $DTName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $DTName;?></strong> ], Are you sure?','discounttype.php?&discounttype=' + <?php echo $unDiscountType; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>
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
	<div id="editdiscounttype" class="popupcontainer"></div>
</div>


<?php include 'footer.php';?>
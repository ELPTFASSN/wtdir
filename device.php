<?php include 'header.php';

if (isset($_POST['btndevicesave'])){
	$query="Update device set DName='".$_POST['txtname']."',unBranch=".$_POST['cbranch'].",DSerialNumber='".$_POST['txtserialnumber']."',DMacAddress='".$_POST['txtmacaddress']."' where unDevice=".$_POST['did'];
	ExecuteNonQuery($query);
}

if (isset($_POST['btndeviceadd'])){
	$query="Insert Into device (DName,unArea,unBranch,DSerialNumber,DMacAddress,unDevice) values ('".$_POST['txtname']."',".$_SESSION['area'].",".$_POST['cbranch'].",'".$_POST['txtserialnumber']."','".$_POST['txtmacaddress']."',".getMax('unDevice','device').")";
	ExecuteNonQuery($query);
}
if (isset($_GET['del'])){
	$query="Update device set `Status`=0 where unDevice=".$_GET['device'];
	ExecuteNonQuery($query);
	header('location:branch.php');
}

?>

<script>
function loaddeviceinfo(idDevice){
	var xmlhttp;
	if (idDevice==0){
		document.getElementById('editdevice').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editdevice').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaddeviceinfo&did='+idDevice);
}

$(document).ready(function() {
		var h = $('#lvdevice').height()-$('#coldevice').height();
       $('#rowdevice').height(h);
});
$(document).scroll(function(){
	columnheader('coldevice','lvdevice');
});
</script>

<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createdevice'" style="background-image:url(img/icon/device.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvdevice">

	<div class="column" id="coldevice">
        <div class="columnheader" style="width:220px;text-align:left;">Device Name</div>
    	<div class="columnheader" style="width:150px;text-align:left;">Branch</div>
        <div class="columnheader" style="width:100px;text-align:left;">Area</div>
        <div class="columnheader" style="width:150px;text-align:left;">Serial Number</div>
        <div class="columnheader" style="width:150px;text-align:left;">Mac Address</div>
        <div class="columnheader" style="width:100px;text-align:left;">Status</div>
        <div class="columnheader" style="width:100px;text-align:left;">Action</div>
    </div>
    <div class="row" id="rowdevice">
    	
    <?php
		//echo $_SESSION['area'];
		//die ($_SESSION['area']);
		$i=0;
       	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
        if($stmt->prepare("Select unDevice,DName,DSerialNumber,DMacAddress,AName,BName,device.Status From device left join branch On device.unBranch=branch.unBranch Inner Join area On device.unArea=area.unArea Where device.Status=1 and device.unArea=? Order by unDevice")){
		//if($stmt->prepare("Select idDevice,DName,DSerialNumber,DMacAddress,AName,device.Status From device Inner Join area On device.idArea=area.idArea Where device.Status=1 and device.idArea=? Order by idDevice")){
			$stmt->bind_param('i',$_SESSION['area']);
        	$stmt->execute(); 
        	$stmt->bind_result($unDevice,$DName,$DSerialNumber,$DMacAddress,$AName,$BName,$Status);
			//$stmt->bind_result($idDevice,$DName,$DSerialNumber,$DMacAddress,$AName,$Status);
        	while($stmt->fetch()){
				//die('----------------------'.$idDevice.$DName.$DSerialNumber.$DMacAddress.$AName.$Status);
				?>
           		<div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:220px;text-align:left;"><?php echo $DName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $BName;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $AName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $DSerialNumber;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $DMacAddress;?></div>
				<?php
                    if($Status==1){
                ?>               
                        <div class="listviewsubitem" style="width:100px;text-align:left;" title="Deactivate Device" onclick="msgbox('Deactivate [ <strong><?php echo $DName;?></strong> ], Are you sure?','device.php?&device=' + <?php echo $unDevice; ?> + '&del=1','')" style="cursor:pointer;">Deactivate</div>
                <?php
                    }else{
                ?>
                        <div class="listviewsubitem" style="width:100px;text-align:left;" title="Activate Device" onclick="msgbox('Activate [ <strong><?php echo $DName;?></strong> ], Are you sure?','device.php?&device=' + <?php echo $unDevice; ?> + '&del=1','')" style="cursor:pointer;">Activate</div>
                <?php
                    }
                ?>


                <div class="listviewsubitem" style="width:100px;text-align:left;"> 
                    <div title="Edit [ <?php echo $DName;?> ]" class="button16" onclick="loaddeviceinfo(<?php echo $unDevice; ?>)" style="background-image:url(img/icon/update.png);margin:auto;"></div>
                    <div title="Delete [ <?php echo $DName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $DName;?></strong> ], Are you sure?','device.php?&device=' + <?php echo $unDevice; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>
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
	<div id="editdevice" class="popupcontainer"></div>
</div>

<?php include 'footer.php' ?>
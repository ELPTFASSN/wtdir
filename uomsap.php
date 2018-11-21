<?php
	include 'header.php';
	
	if($_POST['btnuomsapadd']){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Insert Into sapuom (SUName,unproductuom,unSAPUOM) values (?,?,".getMax('unSAPUOM','sapuom').")")){
			$stmt->bind_param("si",$_POST['txtuomsap'],$_POST['cmbuom']);
			$stmt->execute();
			$stmt->close();
		}
		
	}

	if($_POST['btnuomsapedit']){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Update sapuom Set SUName=?,unproductuom=? Where unSAPUOM=?")){
			$stmt->bind_param("sii",$_POST['txtuomsap'],$_POST['cmbuom'],$_POST['iduomsap']);
			$stmt->execute();
			$stmt->close();
		}
		
	}

	if (isset($_GET['del'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Update sapuom Set `Status`=0 where unSAPUOM=?")){
			$stmt->bind_param("i",$_GET['iduomsap']);
			$stmt->execute();
			$stmt->close();
		}
		echo "<script>location.href='uomsap.php';</script>";
	}

?>
<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createuomsap'" style="background-image:url(img/icon/uom.png);background-repeat:no-repeat;background-position:center;" >
</div>
<script>
function loaduomsapinfo(iduomsap){
	var xmlhttp;
	if (iduomsap==0){
		document.getElementById('edituomsap').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('edituomsap').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaduomsapinfo&iduomsap='+iduomsap);
}
</script>

<div class="listview" id="lvuomsap">
	<div class="column" id="coluomsap">
        <div class="columnheader" style="width:150px;text-align:left;">SAP Unit</div>
        <div class="columnheader" style="width:150px;text-align:left;">Unit</div>
        <div class="columnheader">Action</div>
    </div>  
	<div class="row" id="rowuomsap">
	<?php 
		$i=0;
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("SELECT unSAPUOM,SUName,PUOMName FROM sapuom Inner Join productuom on sapuom.unProductUOM=productuom.unProductUOM Where sapuom.`Status`=1 Order by SUName Asc")){
			$stmt->execute();
			$stmt->bind_result($unSAPUOM,$SUName,$PUOMName);
			while($stmt->fetch()){
				?>
				<div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
					<div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $SUName;?></div>
					<div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $PUOMName;?></div>
					<div class="listviewsubitem">
						<div title="Edit [ <?php echo $SUName;?> ]" class="button16" onclick="loaduomsapinfo(<?php echo $unSAPUOM;?>)" style="background-image:url(img/icon/update.png);"></div>                
						<div title="Delete [ <?php echo $SUName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $SUName;?></strong> ], Are you sure?','uomsap.php?&iduomsap=' + <?php echo $unSAPUOM; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>                
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

<div id="createuomsap" class="popup">
    <div id="adduomsap" class="popupcontainer">
        <div class="popuptitle" align="center">Create SAP Unit of Measure</div>
        <form method="post" action="uomsap.php">
            <div class="popupitem">
                <div class="popupitemlabel">SAP Unit</div><input name="txtuomsap" type="text" style="width:195px;" required>
            </div>

            <div class="popupitem">
                <div class="popupitemlabel">Unit</div>
                <select name="cmbuom" style="width:195px;">
					<?php
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("Select unProductUOM,PUOMName from productuom Where `Status`=1")){
						$stmt->execute();
						$stmt->bind_result($unProductUOM,$PUOMName);
						while($stmt->fetch()){
						?>
						<option value="<?php echo $unProductUOM; ?>"><?php echo $PUOMName; ?></option>
						<?php
						}
						$stmt->close();
					}
                    ?>
                </select>
            </div>

            <div align="center">
                <input name="btnuomsapadd" type="submit" value="Add" title="Add SAP Unit of Measure" class="buttons" >
                <input name="btnuomaddsapcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>


<div id="popupedit" class="popup">
	<div id="edituomsap" class="popupcontainer"></div>
</div>

<?php
	include 'footer.php';
?>

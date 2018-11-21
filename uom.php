<?php
	include 'header.php';
	
	if(isset($_POST['btnuomadd'])){
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("Insert Into productuom (PUOMName,unProductUOM) Values (?,".getMax('unProductUOM','productuom').")")){
			$stmt->bind_param("s",$_POST['txtuom']);
			$stmt->execute();
			$stmt->close();
		}
	}
	
	if(isset($_POST['btnuomedit'])){
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("Update productuom Set PUOMName=? where unProductUOM=?")){
			$stmt->bind_param("si",$_POST['txtuom'],$_POST['iduom']);
			$stmt->execute();
			$stmt->close();
		}
	}
	
	if (isset($_GET['del'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Update productuom Set `Status`=0 where unProductUOM=?")){
			$stmt->bind_param("i",$_GET['iduom']);
			$stmt->execute();
			$stmt->close();
		}
		echo "<script>location.href='uom.php';</script>";
	}

?>
<script>
function loaduominfo(iduom){
	var xmlhttp;
	if (iduom==0){
		document.getElementById('edituom').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('edituom').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaduominfo&iduom='+iduom);
}
</script>

<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createuom'" style="background-image:url(img/icon/uom.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvuom">
	<div class="column" id="coluom">
        <div class="columnheader" style="width:150px;text-align:left;">Unit</div>
        <div class="columnheader">Action</div>
    </div>  
	<div class="row" id="rowuom">
	<?php 
		$i=0;
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("SELECT unProductUOM,PUOMName FROM productuom Where `Status`=1 Order by PUOMName Asc")){
			$stmt->execute();
			$stmt->bind_result($unProductUOM,$PUOMName);
			while($stmt->fetch()){
				?>
				<div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
					<div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $PUOMName;?></div>
					<div class="listviewsubitem">
						<div title="Edit [ <?php echo $PUOMName;?> ]" class="button16" onclick="loaduominfo(<?php echo $unProductUOM;?>)" style="background-image:url(img/icon/update.png);"></div>                
						<div title="Delete [ <?php echo $PUOMName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $PUOMName;?></strong> ], Are you sure?','uom.php?&iduom=' + <?php echo $unProductUOM; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>                
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

<div id="createuom" class="popup">
    <div id="adduom" class="popupcontainer">
        <div class="popuptitle" align="center">Create Unit of Measure</div>
        <form method="post" action="uom.php">
            <div class="popupitem">
                <div class="popupitemlabel">Unit</div><input name="txtuom" type="text" style="width:195px;" required>
            </div>

            <div align="center">
                <input name="btnuomadd" type="submit" value="Add" title="Add Unit of Measure" class="buttons" >
                <input name="btnuomaddcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>


<div id="popupedit" class="popup">
	<div id="edituom" class="popupcontainer"></div>
</div>

<?php
	include 'footer.php';
?>
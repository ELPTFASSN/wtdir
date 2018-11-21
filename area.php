<?php
include 'header.php';

if (isset($_POST['btnareasave'])){
	$query="Update area set AName='".$_POST['txtarea']."',ASAPSvr='".$_POST['txtserver']."',ASAPDB='".$_POST['txtdatabase']."',ASAPUsr='".$_POST['txtusername']."',ASAPpwd='".$_POST['txtpassword']."',ASAPDataSource='".$_POST['txtdatasource']."',unBranchCommi=".$_POST['cmbcommi']." where unArea=".$_POST['aid'];
	ExecuteNonQuery($query);
}

if (isset($_POST['btnareaadd'])){
	$query="Insert Into area (unBranchCommi,AName,ASAPSvr,ASAPDB,ASAPUsr,ASAPpwd,ASAPDataSource,unArea) values (".$_POST['cmbcommi'].",'".$_POST['txtarea']."','".$_POST['txtserver']."','".$_POST['txtdatabase']."','".$_POST['txtusername']."','".$_POST['txtpassword']."','".$_POST['txtdatasource']."',".getMax('unArea','area').")";
	ExecuteNonQuery($query);
}
if (isset($_GET['del'])){
	$query="Update area set `Status`=0 where unArea=".$_GET['aid'];
	ExecuteNonQuery($query);
	header('location:area.php');
}

?>
<script>
function loadareainfo(idArea){
	var xmlhttp;
	if (idArea==0){
		document.getElementById('editarea').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editarea').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadareainfo&aid='+idArea);
}

$(document).ready(function() {
		var h = $('#lvarea').height()-$('#colarea').height();
       $('#rowarea').height(h);
});
$(document).scroll(function(){
	columnheader('colarea','lvarea');
})
</script>
<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createarea'" style="background-image:url(img/icon/employeearea.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvarea">
	<div class="column" id="colarea">
        <div class="columnheader" style="width:150px;text-align:left;">Area</div>
        <div class="columnheader" style="width:200px;text-align:left;">Commi</div>
        <div class="columnheader" style="width:150px;text-align:left;">Server</div>
        <div class="columnheader" style="width:100px;text-align:left;">User</div>
        <div class="columnheader" style="width:100px;text-align:left;">Password</div>
        <div class="columnheader" style="width:200px;text-align:left;">Database</div>
        <div class="columnheader" style="width:200px;text-align:left;">Data Source</div>
        <div class="columnheader" style="width:200px;text-align:left;">Action</div>
    </div>  
	<div class="row" id="rowarea">
	<?php 
		$i=0;
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("SELECT area.unArea,AName,BName as Commi,ASAPSvr,ASAPUsr,ASAPpwd,ASAPDB,ASAPDataSource FROM area Inner Join branch on area.unbranchCommi=branch.unBranch Where area.`Status`=1 Order by AName Asc")){
        $stmt->execute();
        $stmt->bind_result($unArea,$AName,$Commi,$ASAPSvr,$ASAPUsr,$ASAPpwd,$ASAPDB,$ASAPDataSource);
        while($stmt->fetch()){
            ?>
            <div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $AName;?></div>
                <div class="listviewsubitem" style="width:200px;text-align:left;"><?php echo $Commi;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $ASAPSvr;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $ASAPUsr;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $ASAPpwd;?></div>
                <div class="listviewsubitem" style="width:200px;text-align:left;"><?php echo $ASAPDB;?></div>
                <div class="listviewsubitem" style="width:200px;text-align:left;"><?php echo $ASAPDataSource;?></div>
                <div class="listviewsubitem">
                    <div title="Edit [ <?php echo $AName;?> ]" class="button16" onclick="loadareainfo(<?php echo $unArea;?>)" style="background-image:url(img/icon/update.png);"></div>                
                    <div title="Delete [ <?php echo $AName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $AName;?></strong> ], Are you sure?','area.php?&aid=' + <?php echo $unArea; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>                
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

<div id="createarea" class="popup">
    <div id="addarea" class="popupcontainer">
        <div class="popuptitle" align="center">Create Area</div>
        <form method="post" action="area.php">
            <div class="popupitem">
                <div class="popupitemlabel">Area</div><input name="txtarea" type="text" style="width:195px;" required>
            </div>
            
            <div class="popupitem">
                <div class="popupitemlabel">Commi</div>
                <select name="cmbcommi" id="cmbcommi" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and BType=2")){
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unBranch; ?>"><?php echo $BName; ?></option>
                <?php
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>

            <div class="popupitem">
                <div class="popupitemlabel">Server</div><input name="txtserver" type="text" style="width:195px;" required>
            </div>

            <div class="popupitem">
                <div class="popupitemlabel">Username</div><input name="txtusername" type="text" style="width:195px;" required>
            </div>

            <div class="popupitem">
                <div class="popupitemlabel">Password</div><input name="txtpassword" type="text" style="width:195px;" required>
            </div>
      
            <div class="popupitem">
                <div class="popupitemlabel">Database</div><input name="txtdatabase" type="text" style="width:195px;" required>
            </div>

            <div class="popupitem">
                <div class="popupitemlabel">Data Source</div><input name="txtdatasource" type="text" style="width:195px;" required>
            </div>

            <div align="center">
                <input name="btnareaadd" type="submit" value="Add" title="Add Area" class="buttons" >
                <input name="btnareaaddcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="popupedit" class="popup">
	<div id="editarea" class="popupcontainer"></div>
</div>

<?php include 'footer.php'; ?>
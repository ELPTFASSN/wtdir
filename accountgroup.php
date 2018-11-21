<?php
	include 'header.php';

if (isset($_POST['btnagedit'])){
	$query="Update accountgroup set AGName='".$_POST['txtagname']."' where idAccountGroup=".$_POST['agid'];
	ExecuteNonQuery($query);
}

if (isset($_POST['btnagadd'])){
	$query="Insert Into accountgroup (unAccountGroup,AGName) values (".getMax('unAccountGroup','accountgroup').",'".$_POST['txtagname']."')";
	ExecuteNonQuery($query);
}
if (isset($_GET['del'])){
	$query="Update accountgroup set `Status`=0 where unAccountGroup=".$_GET['agid'];
	ExecuteNonQuery($query);
	header('location:accountgroup.php');
}
?>
<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createaccountgroup'" style="background-image:url(img/icon/user.png);background-repeat:no-repeat;background-position:center;" >
</div>
<script>
function loadaccountgroupinfo(idAccountGroup){
	var xmlhttp;
	if (idAccountGroup==0){
		document.getElementById('editaccountgroup').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editaccountgroup').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadaccountgroupinfo&agid='+idAccountGroup);
}
$(document).ready(function() {
		var h = $('#lvaccountgroup').height()-$('#colaccountgroup').height();
       $('#rowaccountgroup').height(h);
});
$(document).scroll(function(){
	columnheader('colaccountgroup','lvaccountgroup');
});
</script>

<div class="listview" id="lvaccountgroup">

	<div class="column" id="colaccountgroup">
        <div class="columnheader" style="width:150px;text-align:left;">Name</div>
        <div class="columnheader" style="width:150px;text-align:left;">Action</div>
    </div>  
	<div class="row" id="rowaccountgroup">
	<?php 
		$i=0;
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("Select unAccountGroup,AGName From accountgroup Where `Status`=1")){
        $stmt->execute();
        $stmt->bind_result($unAccountGroup,$AGName);
        while($stmt->fetch()){
            ?>
            <div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $AGName; ?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;">
                    <div title="Edit [ <?php echo $AGName; ?> ]" class="button16" onclick="loadaccountgroupinfo(<?php echo $unAccountGroup; ?>)" style="background-image:url(img/icon/update.png);"></div>                
                    <div title="Delete [ <?php echo $AGName; ?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $AGName;?></strong> ], Are you sure?','accountgroup.php?&agid=' + <?php echo $idAccountGroup; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>                
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

<div id="createaccountgroup" class="popup">
    <div class="popupcontainer">
        <div class="popuptitle" align="center">Create Group</div>
        <form method="post" action="accountgroup.php">
            <div class="popupitem">
                <div class="popupitemlabel">Group</div><input name="txtagname" type="text" style="width:195px;" required>
            </div>
            
            <div align="center">
                <input name="btnagadd" type="submit" value="Add" title="Add Group" class="buttons" >
                <input name="btnagcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>


<div id="popupedit" class="popup">
	<div id="editaccountgroup" class="popupcontainer"></div>
</div>

<?php
	include 'footer.php';
?>
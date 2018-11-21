<?php
	include 'header.php';

if (isset($_POST['btnegsave'])){
	$query="Update employeegroup set EGName='".$_POST['txtegname']."',EGLevel='".$_POST['cmblevel']."' where unEmployeeGroup=".$_POST['egid'];
	ExecuteNonQuery($query);
}

if (isset($_POST['btnegadd'])){
	$query="Insert Into employeegroup (unEmployeeGroup,EGName,EGLevel) SELECT ifnull(max(unEmployeeGroup),0)+1,'".$_POST['txtegname']."','".$_POST['cmblevel']."' FROM employeegroup";
	ExecuteNonQuery($query);
}
if (isset($_GET['del'])){
	$query="Update employeegroup set `Status`=0 where unEmployeeGroup=".$_GET['egid'];
	ExecuteNonQuery($query);
	header('location:employeegroup.php');
}

?>
<script>
function loademployeegroupinfo(idEmployeeGroup){
	var xmlhttp;
	if (idEmployeeGroup==0){
		document.getElementById('editemployeegroup').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editemployeegroup').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loademployeegroupinfo&egid='+idEmployeeGroup);
}
$(document).ready(function() {
		var h = $('#lvemployeegroup').height()-$('#colemployeegroup').height();
       $('#rowemployeegroup').height(h);
});
$(document).scroll(function(){
	columnheader('colemployeegroup','lvemployeegroup');
});
</script>
<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createemployeegroup'" style="background-image:url(img/icon/employee.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvemployeegroup">
	<div class="column" id="colemployeegroup">
        <div class="columnheader" style="width:150px;text-align:left;">Name</div>
        <div class="columnheader" style="width:150px;text-align:left;">Level</div>
        <div class="columnheader" style="width:150px;text-align:left;">Action</div>
    </div>  
	<div class="row" id="rowemployeegroup">
	<?php 
		$i=0;
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("Select unEmployeeGroup,EGName,EGLevel From employeegroup Where `Status`=1")){
        $stmt->execute();
        $stmt->bind_result($unEmployeeGroup,$EGName,$EGLevel);
        while($stmt->fetch()){
            ?>
            <div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $EGName; ?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $EGLevel; ?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;">
                    <div title="Edit [ <?php echo $EGName;?> ]" class="button16" onclick="loademployeegroupinfo(<?php echo $unEmployeeGroup; ?>)" style="background-image:url(img/icon/update.png);"></div>                
                    <div title="Delete [ <?php echo $EGName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $EGName;?></strong> ], Are you sure?','employeegroup.php?&egid=' + <?php echo $unEmployeeGroup; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>                    
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

<div id="createemployeegroup" class="popup">
    <div class="popupcontainer">
        <div class="popuptitle" align="center">Create Group</div>
        <form method="post" action="employeegroup.php">
            <div class="popupitem">
                <div class="popupitemlabel">Group</div><input name="txtegname" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Level</div>
                <select name="cmblevel" id="cmblevel" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select distinct EGLevel From employeegroup Where `Status`=1 Order by EGLevel")){
                        $stmt->execute();
                        $stmt->bind_result($level);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $level; ?>"><?php echo $level; ?></option>
                <?php
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>
            
            <div align="center">
                <input name="btnegadd" type="submit" value="Add" title="Add Group" class="buttons" >
                <input name="btnegcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>


<div id="popupedit" class="popup">
	<div id="editemployeegroup" class="popupcontainer"></div>
</div>

<?php
	include 'footer.php'
?>
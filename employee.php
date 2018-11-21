<?php
include 'header.php';
//echo (getMax('unEmployeeArea','employeearea'));

if (isset($_POST['btnemployesave'])){
	$query="Update employee set unEmployeeGroup=".$_POST['cmbgroup'].",ELastName='".$_POST['txtlastname']."',EFirstName='".$_POST['txtfirstname']."',EMiddleName='".$_POST['txtmiddlename']."',EAlias='".$_POST['txtalias']."',ENumber='".$_POST['txtemployeenumber']."',EUsername='".$_POST['txtemployeeusername']."',EPassword='".$_POST['txtemployeepassword']."' where unEmployee=".$_POST['eid'];
	$areacount=ExecuteReader("Select count(unArea) as `result` From area where `Status`=1");
	ExecuteNonQuery($query);
	
	ExecuteNonQuery("Update employeearea set `Status`=0 where unEmployee=".$_POST['eid']);
	
	for($i=1;$i<=$areacount;$i++){
		if(isset($_POST['chkearea'.$i])){
			if(ExecuteReader("Select count(unEmployeeArea) as `result` from employeearea where unArea=".$_POST['chkearea'.$i]." and unEmployee=".$_POST['eid'])>=1){
				ExecuteNonQuery("Update employeearea set `Status`=1 where unArea=".$_POST['chkearea'.$i]." and unEmployee=".$_POST['eid']);
			}else{
				ExecuteNonQuery("Insert into employeearea (unArea,unEmployee,unEmployeeArea) values (".$_POST['chkearea'.$i].",".$_POST['eid'].",".getMax('unEmployeeArea','employeearea').")");
			}
		}
	}
}

if (isset($_POST['btnemployeeadd'])){
	$query="Insert Into employee (unEmployeeGroup,unEmployee,ELastName,EFirstName,EMiddleName,EAlias,ENumber,EUsername,EPassword) SELECT ".$_POST['cmbgroup'].",ifnull(max(unEmployee),0)+1,'".$_POST['txtlastname']."','".$_POST['txtfirstname']."','".$_POST['txtmiddlename']."','".$_POST['txtalias']."','".$_POST['txtemployeenumber']."','".$_POST['txtemployeeusername']."','".$_POST['txtemployeepassword']."' FROM employee";
	//die($query);
	ExecuteNonQuery($query);
	$unEmployee=ExecuteReader("Select max(unEmployee) as `result` From employee");
	$areacount=ExecuteReader("Select count(unArea) as `result` From area where `Status`=1");
	$mysqli= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	for($i=1;$i<=$areacount;$i++){
		if(isset($_POST['chkearea'.$i])){
			if($stmt->prepare("Insert Into employeearea (unEmployee,unArea,unEmployeeArea) values (?,?,".getMax('unEmployeeArea','employeearea').")")){
				$stmt->bind_param('ii',$unEmployee,$_POST['chkearea'.$i]);
				$stmt->execute();
			}
		}
	}
	$stmt->close();
}
if (isset($_GET['del'])){
	$query="Update employee set `Status`=0 where unEmployee=".$_GET['eid'];
	ExecuteNonQuery($query);

	$query="Update employeearea set `Status`=0 where unEmployee=".$_GET['eid'];
	ExecuteNonQuery($query);
	header('location:employee.php');
}
?>
<script>
function loademployeeinfo(idEmployee){
	var xmlhttp;
	if (idEmployee==0){
		document.getElementById('editemployee').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editemployee').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loademployeeinfo&eid='+idEmployee);
}
$(document).ready(function() {
		var h = $('#lvemployee').height()-$('#colemployee').height();
       $('#rowemployee').height(h);
});
$(document).scroll(function(){
	columnheader('colemployee','lvemployee');
});
</script>
<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createemployee'" style="background-image:url(img/icon/employee.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvemployee">
	<div class="column" id="colemployee">
        <div class="columnheader" style="width:150px;text-align:left;">Last Name</div>
    	<div class="columnheader" style="width:150px;text-align:left;">First Name</div>
        <div class="columnheader" style="width:150px;text-align:left;">Middle Name</div>
        <div class="columnheader" style="width:150px;text-align:left;">Alias</div>
        <div class="columnheader" style="width:150px;text-align:left;">Employee No.</div>
        <div class="columnheader" style="width:150px;text-align:left;">Group</div>
        <div class="columnheader" style="width:150px;text-align:left;">Action</div>
    </div>  
	<div class="row" id="rowemployee">
	<?php 
		$i=0;
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("Select employee.unEmployee,ELastName,EFirstName,EMiddleName,EAlias,ENumber,EGName From employee inner join employeegroup On employee.unEmployeeGroup=employeegroup.unEmployeeGroup inner join employeearea on employee.unEmployee=employeearea.unEmployee Where employee.`Status`=1 and unArea=? and employeearea.`Status`=1 Order by ELastName Asc,EFirstName Asc,EMiddleName Asc")){
		$stmt->bind_param('i',$_SESSION['area']);	
        $stmt->execute();
        $stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName,$EAlias,$ENumber,$EGName);
        while($stmt->fetch()){
            ?>
            <div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $ELastName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $EFirstName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $EMiddleName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo (empty($EAlias))?'&nbsp;':$EAlias; ?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $ENumber;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $EGName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;">
                    <div title="Edit [ <?php echo strtoupper($ELastName).', '.$EFirstName.' '.strtoupper(substr($EMiddleName,0,1)).'. '.$EAlias; ?> ]" class="button16" onclick="loademployeeinfo(<?php echo $unEmployee; ?>)" style="background-image:url(img/icon/update.png);"></div>
                    <div title="Delete [ <?php echo strtoupper($ELastName).', '.$EFirstName.' '.strtoupper(substr($EMiddleName,0,1)).'. '.$EAlias; ?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo strtoupper($ELastName).', '.$EFirstName.' '.substr($EMiddleName,0,1).'. '.$EAlias ;?></strong> ], Are you sure?','employee.php?&eid=' + <?php echo $unEmployee; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>
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
	<div id="editemployee" class="popupcontainer" style="width:630px;"></div>
</div>
<?php
	include 'footer.php';
?>
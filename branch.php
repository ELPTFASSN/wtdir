<?php
include 'header.php';

if (isset($_POST['btnbranchsave'])){
	$untic=ExecuteReader("Select unTemplateItemControl as `result` From templateproductionbatch Where unTemplateProductionBatch=".$_POST['cmbbom']." AND Status=1");
	if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
		$query="Update branch set BName='".$_POST['txtbranch']."',unArea=".$_SESSION['area'].",unTemplateProductionBatch=".$_POST['cmbbom'].",unTemplateItemControl=".$_POST['cmbtemp'].",BDescription='".$_POST['txtdescription']."',BSAPCode='".$_POST['txtsapcode']."',BType=".$_POST['cmbbranchtype'].",BQuota=".$_POST['txtquota'].",BQuotaInterval=".$_POST['txtquotainterval'].",BQuotaPointAmount=".$_POST['txtquotapoint']." where unBranch=".$_POST['bid'];
	}else{
		$query="Update branch set BName='".$_POST['txtbranch']."',unArea=".$_SESSION['area'].",unTemplateProductionBatch=".$_POST['cmbbom'].",unTemplateItemControl=".$untic.",BDescription='".$_POST['txtdescription']."',BSAPCode='".$_POST['txtsapcode']."',BType=".$_POST['cmbbranchtype'].",BQuota=".$_POST['txtquota'].",BQuotaInterval=".$_POST['txtquotainterval'].",BQuotaPointAmount=".$_POST['txtquotapoint']." where unBranch=".$_POST['bid'];
	}
	ExecuteNonQuery($query);
}

if (isset($_POST['btnbranchadd'])){
	$untic=ExecuteReader("Select unTemplateItemControl as `result` From templateproductionbatch Where unTemplateProductionBatch=".$_POST['cmbbom']." AND Status=1");
	if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
		$query="Insert Into branch (BName,unArea,unTemplateProductionBatch,unTemplateItemControl,BDescription,BSAPCode,BType,BQuota,BQuotaInterval,BQuotaPointAmount,unBranch) values ('".$_POST['txtbranch']."',".$_SESSION['area'].",".$_POST['cmbbom'].",".$_POST['cmbtemp'].",'".$_POST['txtdescription']."','".$_POST['txtsapcode']."',".$_POST['cmbbranchtype'].",".$_POST['txtquota'].",".$_POST['txtquotainterval'].",".$_POST['txtquotapoint'].",".getMax('unBranch','branch').")";
	}else{
		$query="Insert Into branch (BName,unArea,unTemplateProductionBatch,unTemplateItemControl,BDescription,BSAPCode,BType,BQuota,BQuotaInterval,BQuotaPointAmount,unBranch) values ('".$_POST['txtbranch']."',".$_SESSION['area'].",".$_POST['cmbbom'].",".$untic.",'".$_POST['txtdescription']."','".$_POST['txtsapcode']."',".$_POST['cmbbranchtype'].",".$_POST['txtquota'].",".$_POST['txtquotainterval'].",".$_POST['txtquotapoint'].",".getMax('unBranch','branch').")";
	}
	ExecuteNonQuery($query);
}
if (isset($_GET['del'])){
	$query="Update branch set `Status`=0 where unBranch=".$_GET['branch'];
	ExecuteNonQuery($query);
	header('location:branch.php');
}
if (isset($_POST['btnchangearea'])){
	$query="Update branch set unArea=".$_POST['cmbarea']." Where unBranch=".$_POST['txtidbranch'];
	ExecuteNonQuery($query);
}
?>
<script>
function loadbranchinfo(idBranch){
	var xmlhttp;
	if (idBranch==0){
		document.getElementById('editbranch').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editbranch').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadbranchinfo&bid='+idBranch);
}

function changearea(idbranch,bname){
	document.getElementById('changeareatitle').innerHTML='CHANGE AREA OF '+ bname;
	$('#txtidbranch').val(idbranch);	
	location.href='#changearea';
}
$(document).ready(function() {
		var h = $('#lvbranch').height()-$('#colbranch').height();
       $('#rowbranch').height(h);
});
$(document).scroll(function(){
	columnheader('colbranch','lvbranch');
});
</script>
<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createbranch'" style="background-image:url(img/icon/branch.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvbranch">

	<div class="column" id="colbranch">
        <div class="columnheader" style="width:220px;text-align:left;">Branch</div>
    	<div class="columnheader" style="width:100px;text-align:left;">Area</div>
        <div class="columnheader" style="width:150px;text-align:left;">Template</div>
        <div class="columnheader" style="width:100px;text-align:left;">SAP Code</div>
        <div class="columnheader" style="width:100px;text-align:left;">Type</div>
        <div class="columnheader" style="width:300px;text-align:left;">Description</div>
        <div class="columnheader" style="width:100px;text-align:left;">Status</div>
        <div class="columnheader" style="width:150px;text-align:left;">Action</div>
    </div>  
	<div class="row" id="rowbranch" style="padding-bottom: 50px">
	<?php 
		$i=0;
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt=$mysqli->stmt_init();
        if($stmt->prepare("Select unBranch,BName,AName,TICName,BDescription,ifnull(BSAPCode,0),BType,branch.Status From branch left join templateitemcontrol On branch.unTemplateItemControl=templateitemcontrol.unTemplateItemControl Inner Join area On branch.unArea=area.unArea Where branch.Status=1 and branch.unArea=? Order by BName")){
		$stmt->bind_param('i',$_SESSION['area']);
        $stmt->execute();
        $stmt->bind_result($unBranch,$BName,$AName,$TICName,$BDescription,$BSAPCode,$BType,$Status);
        while($stmt->fetch()){
			if($BType==1){
				$BTypeName='Outlet';
			}elseif($BType==2){
				$BTypeName='Commi';
			}else{
				$BTypeName='Office';
			}
            ?>
            <div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:220px;text-align:left;"><?php echo $BName;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $AName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $TICName;?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo ($BSAPCode==0)?'&nbsp;':$BSAPCode; ?></div>
                <div class="listviewsubitem" style="width:100px;text-align:left;"><?php echo $BTypeName; ?></div>
                <div class="listviewsubitem" style="width:300px;text-align:left;"><?php echo '&nbsp'.$BDescription; ?></div>
				<?php
                    if($Status==1){
                ?>               
                        <div class="listviewsubitem" style="width:100px;text-align:left;" title="Deactivate Branch" onclick="msgbox('Deactivate [ <strong><?php echo $BName;?></strong> ], Are you sure?','branch.php?&branch=' + <?php echo $unBranch; ?> + '&del=1','')" style="cursor:pointer;">Deactivate</div>
                <?php
                    }else{
                ?>
                        <div class="listviewsubitem" style="width:100px;text-align:left;" title="Activate Branch" onclick="msgbox('Activate [ <strong><?php echo $BName;?></strong> ], Are you sure?','branch.php?&branch=' + <?php echo $unBranch; ?> + '&del=1','')" style="cursor:pointer;">Activate</div>
                <?php
                    }
                ?>


                <div class="listviewsubitem" style="width:150px;text-align:left;">
                    <div title="Edit [ <?php echo $BName;?> ]" class="button16" onclick="loadbranchinfo(<?php echo $unBranch; ?>)" style="background-image:url(img/icon/update.png);margin:auto;"></div>
                	<div title="Change Area of [ <?php echo $BName;?> ]" class="button16" onclick="changearea('<?php echo $unBranch; ?>','<?php echo $BName;?>')" style="background-image:url(img/icon/employeearea.png);margin:auto;"></div>
                    <div title="Delete [ <?php echo $BName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $BName;?></strong> ], Are you sure?','branch.php?&branch=' + <?php echo $unBranch; ?> + '&del=1','')" style="background-image:url(img/icon/delete.png);"></div>
                </div>
            </div>
            <?php
			$i++;
            }
        $stmt->close();
        }
    ?>
		<div class="listviewitem"></div>
    </div>
</div>

<div id="popupedit" class="popup">
	<div id="editbranch" class="popupcontainer"></div>
</div>

<div id="changearea" class="popup">
    <div class="popupcontainer" style="width:300px;">
        <div id="changeareatitle" class="popuptitle" align="center">Change Area</div>
        <form name="frmchangearea" method="post" action="branch.php">
            <div id="divoldpassword" class="popupitem">
                <div class="popupitemlabel">Area</div>
                <select name="cmbarea" style="width:200px;">
                <?php
				$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt=$mysqli->stmt_init();
				if($stmt->prepare("Select unArea,AName from area where `Status`=1 Order by AName")){
					$stmt->execute();
					$stmt->bind_result($unArea,$AName);
					while($stmt->fetch()){
				?>
                	<option value="<?php echo $unArea?>" <?php echo ($unArea==$_SESSION['area'])? 'Selected':''; ?>><?php echo $AName; ?></option>
                <?php
					}
					$stmt->close();
				}
				?>
                </select>
            </div>
            <input type="hidden" id="txtidbranch" name="txtidbranch" value="">
            <div align="center">
                <input name="btnchangearea" type="submit" value="Change" title="Change Area" class="buttons">
                <input name="btnchangeareacancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
		</form>
	</div>
</div>


<?php
include 'footer.php';
?>
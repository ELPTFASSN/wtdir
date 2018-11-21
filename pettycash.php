<?php 
require 'header.php'; 
if(isset($_GET['del'])){
	ExecuteNonQuery("Update pettycashcontrol Set unInventoryControl=0 Where unPettyCashControl=".$_GET['idPCC']);
	
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select Sum(PCCAmount) as `PCCAmount` From pettycashcontrol Where `Status`=1 and unInventoryControl=?")){
		$stmt->bind_param('i',$_GET['did']);
		$stmt->execute();
		$stmt->bind_result($PCCAmount);
		$stmt->fetch();
		$stmt->close();			
	}
	
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Update sales set SPettyCash=?,SEndingBalance=(SBeginningBalance+STotalSales-SCashDeposit-SPettyCash-SDiscount),SShortage=(SCashCount - SEndingBalance) Where unInventoryControl=?")){
		$stmt->bind_param('di',$PCCAmount,$_GET['did']);
		$stmt->execute();
		$stmt->close();			
	}
	
	//header('location:delivery.php?&bid='.$_GET['bid'].'&did='.$_GET['did'].'&type='.$_GET['type']);
	echo "<script>location.href='pettycash.php?&bid=".$_GET['bid']."&did=".$_GET['did']."&type=".$_GET['type']."'</script>";
}
?>
<script src="js/pettycash.js"></script>
<script>
$(document).ready(function(e) {
	var h = $('#lvpettycashcontrol').height()-$('#colpettycashcontrol').height();
    $('#rowpettycashcontrol').height(h);
	
	var h = $('#lvpettycashdata').height()-$('#colpettycashdata').height();
    $('#rowpettycashdata').height(h);
	
	var h = $('#lvviewdata').height()-$('#colviewdata').height();
    $('#rowviewdata').height(h);
});

function executeaction(action){
	var id = document.getElementById('hdnidPCC').value;
	var title = document.getElementById('actiontitle').innerHTML;
	var xmlhttp;

	if(id==0){return;}

	if(action == 'view'){
		if(id==0){
			document.getElementById('rowviewdata').innerHTML = '';	
			return;
		}
		if(window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		}
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4 && xmlhttp.status==200){
				if(xmlhttp.responseText!=''){
					document.getElementById('rowviewdata').innerHTML=xmlhttp.responseText;
					document.getElementById('viewtitle').innerHTML = title;
					location.href='#viewdata';
				}
			}
		}
		
		xmlhttp.open('POST','ajax/pettycash.ajax.php',true);
		xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xmlhttp.send('qid=DisplayPettyCashData&id='+id);
		
	}else if(action == 'delete'){
	<?php
	if($ICLock==0){
	?>
		msgbox('Remove delivery. Are you sure?','pettycash.php?&bid=<?php echo $_GET['bid'].'&did='.$_GET['did'].'&type='.$_GET['type'].'&del=1'?>&idPCC='+id,'#close');
    <?php
		}
	?>
	}else{ return; }
}
</script>

<div id="toolbar">
	<?php
    if($ICLock==0){
    ?>
        <input type="button" title="Map Petty Cash Fund" onClick="location.href='#mappettycash'" style="background-image:url(img/icon/mapitf.jpg); border:none; padding-top:4px; width:35px; height:27px; cursor:pointer;" >
    <?php
	}
    ?>
</div>

<div class="listview" id="lvpettycashcontrol" style="height:200px;">
	<div class="column" id="colpettycashcontrol">
        <div class="columnheader" style="width:200px;">Prepared by</div>
        <div class="columnheader" style="width:200px;">Reference Number</div>
        <div class="columnheader" style="width:150px;">Date</div>
        <div class="columnheader" style="width:100px; text-align:right;">Amount</div>    
    </div>
    <div class="row" id="rowpettycashcontrol">
    	<?php 
		$mysqli	= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unPettyCashControl,Concat(ELastName,', ',EFirstName,' ',Substring(EMiddleName,1,1),'.') as `FullName`,PCCReferenceNumber,Concat(MonthName(`PCCDate`), ' ',DayOfMonth(PCCDate),', ', Year(PCCDate)) as `PCDate`,PCCAmount From pettycashcontrol
						Inner Join employee On pettycashcontrol.unEmployee = employee.unEmployee
						Where pettycashcontrol.`Status` = 1 and unInventoryControl = ?")){
			$stmt->bind_param("i",$_GET['did']);
			$stmt->execute();
			$stmt->bind_result($unPettyCashControl,$FullName,$PCCReferenceNumber,$PCCDate,$PCCAmount);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" style="cursor:default;" onClick="showactions(<?php echo $unPettyCashControl;?>,'<?php echo $PCCReferenceNumber.' - ['.$PCCDate.']'; ?>')">
                    <div class="listviewsubitem" style="width:200px;">
                        <input readonly type="text" style="width:inherit; border:none; background-color:transparent;" value="<?php echo $FullName; ?>">
                    </div>
                    <div class="listviewsubitem" style="width:200px;">
                        <input readonly type="text" style="width:inherit; border:none; background-color:transparent;" value="<?php echo $PCCReferenceNumber; ?>">
                    </div>
                    <div class="listviewsubitem" style="width:150px;">
                        <input readonly type="text" style="width:inherit; border:none; background-color:transparent;" value="<?php echo $PCCDate; ?>">
                    </div>
                    <div class="listviewsubitem" style="width:100px;">
                        <input readonly type="text" style="width:inherit; border:none; background-color:transparent; text-align:right;" value="<?php echo $PCCAmount; ?>">
                    </div>
                </div>
				<?php
			}
			$stmt->close();
		}
		?>
    </div>
</div>
<div class="group" style="text-align:center; font-weight:bold;">. . .</div>
<div class="listview">
	<div class="column">
        <div class="columnheader" style="width:500px;">Description</div>
        <div class="columnheader" style="width:100px; text-align:right;">Amount</div>   
    </div>
    <div class="row">
    	<?php 
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select PCDDescription,PCDAmount From pettycashdata 
						Inner Join pettycashcontrol On pettycashdata.unPettyCashControl = pettycashcontrol.unPettyCashControl
						Where pettycashcontrol.`Status` = 1 and pettycashdata.`Status` = 1 and unInventoryControl = ? Order by PCDDescription Asc")){
			$stmt->bind_param("i",$_GET['did']);
			$stmt->execute();
			$stmt->bind_result($PCDDescription,$PCDAmount);
			while($stmt->fetch()){
				?>
				<div class="listviewitem">
                    <div class="listviewsubitem" style="width:500px;">
                        <input readonly type="text" style="width:inherit; border:none; background-color:transparent;" value="<?php echo $PCDDescription; ?>">
                    </div>
                    <div class="listviewsubitem" style="width:100px;">
                        <input readonly type="text" style="width:inherit; border:none; background-color:transparent; text-align:right;" value="<?php echo $PCDAmount; ?>">
                    </div>
                </div>
				<?php
			}
			$stmt->close();
		}
		?>
    </div>
</div>

<div class="popup" id="mappettycash">
	<div class="popupcontainer" style="width:590px;">
    	<div class="popuptitle" align="center">MAP Petty Cash</div>
        <div class="listbox" style="width:200px; height:300px;">
	        <div class="listboxitem" onClick="window.open('createpettycash.php')"><img src="img/icon/pettycash.png" width="16" height="16" style="padding-right:10px;">Create New Petty Cash Fund</div>
    	    <?php
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("Select unPettyCashControl,PCCReferenceNumber,Concat(MonthName(`PCCDate`), ' ',DayOfMonth(PCCDate),', ', Year(PCCDate)) as `PCDate` From pettycashcontrol
							Where pettycashcontrol.`Status` = 1 and unInventoryControl = 0")){
				$stmt->execute();
				$stmt->bind_result($unPettyCashControl,$PCCRefernceNumber,$PCDate);
				while($stmt->fetch()){
					?>
                    <div class="listboxitem" onClick="DisplayPettyCashData(<?php echo $unPettyCashControl; ?>)"><img src="img/icon/pettycash.png" width="16" height="16" style="padding-right:10px;"><?php echo $PCCRefernceNumber.' ['.$PCDate.']'; ?></div>
					<?php
				}
			}
			?>
        </div>
        <div class="listview" id="lvpettycashdata" style="height:300px; width:380px; position:absolute; right:20px; top:44px;">
        	<div class="column" id="colpettycashdata">
            	<div class="columnheader" style="width:250px;">Description</div>
                <div class="columnheader" style="width:50px;">Amount</div>
            </div>
            <div class="row" id="rowpettycashdata">
            </div>
        </div>
        <div align="center" style="margin-top:5px;">
        <form id="frmMapPettyCash" method="post" action="include/pettycash.inc.php">
        	<input type="hidden" name="hdnInventoryControl" value="<?php echo $_GET['did']; ?>">
        	<input type="hidden" id="hdnSaveMapping" name="hdnSaveMapping" value="0">
            <input name="btnSaveMapping" type="button" value="Save" title="Save" class="buttons" onClick="SaveMapping(hdnSaveMapping.value)">
            <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
		</form>
        </div>
    </div>
</div>

<div class="popup" id="action" style="background-color:transparent;">
    <div class="popupcontainer">
        <div class="popuptitle" id="actiontitle" align="center"></div>
        <hr>
        <div align="center">
        <form>
        	<input type="hidden" name="hdnidPCC" id="hdnidPCC" value="0" >
	        <input class="buttons" style="width:90px;" type="button" value="View"  title="View" onClick="executeaction('view')" >
	        <input class="buttons" style="width:90px;" type="button" value="Unmap" title="Unmap" onClick="executeaction('delete')" >
	        <input class="buttons" style="width:90px;" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" >
        </form>
        </div>
    </div>
</div>

<div class="popup" id="viewdata">
	<div class="popupcontainer" style="width:385px;">
        <div class="popuptitle" id="viewtitle" align="center"></div>
        <div class="listview" id="lvviewdata" style="height:300px; width:380px;">
            <div class="column" id="colviewdata">
                <div class="columnheader" style="width:295px;">Description</div>
                <div class="columnheader" style="width:50px;">Amount</div>
            </div>
            <div class="row" id="rowviewdata">
            </div>
        </div>
        <div align="center" style="margin-top:5px;"><input type="button" onClick="location.href='#close'" class="buttons" title="Close" value="Close" ></div>
    </div>
</div>

<?php require 'footer.php'; ?>
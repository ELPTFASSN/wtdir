<?php //require('header.php');
 include 'header.php'; ?>

 
<link rel="stylesheet" type="text/css" href="css/inventory.css">
<script src="js/inventory.js"></script>

<script type="text/javascript">
function msg(targ,selObj)
{
	var rep;
	var url = "<?php echo $_SERVER['REQUEST_URI']; ?>";
	url = url.split('?')[1];
	var type = url.replace('type='+<?php echo $_GET['type']; ?>,'type='+selObj.options[selObj.selectedIndex].value);
	eval(targ+".location='inventory.php?"+type+"'");
}

$(window).scroll(function() {

	<?php
		if($_GET['type']==1){
			echo "columnheader('colproduct','lvproduct');";
		}else{
			echo "columnheader('colrawmats','lvrawmats');";
		}
	?>

});

</script>
<form name="frminventorysheet" id="frminventorysheet" action="include/inventory.fnc.php" method="post">
<div id="toolbar">
<!--<input type="button" class="toolbarbutton" title="New" name="btnNew" onclick="location.href='#createinventorysheet'" style="background-image:url(img/icon/new.png);" >-->
<?php
	$bid = (isset($_GET['bid']))?$_GET['bid']:'';
	if($bid!='' && $ICLock==0){
		?>
        <button type="submit" class="toolbarbutton" title="Save" name="btnSave" style="background-image:url(img/icon/save.png);"></button>
		<input type="button" class="toolbarbutton" title="Edit Inventory Sheet" name="btnEdit" onclick="location.href='#<?php echo ($_GET['bid']!='')?'editinventorysheet':''; ?>'" style="background-image:url(img/icon/save35x27.png);" >
		<?php
	}
?>
<!--<input type="button" class="toolbarbutton" title="Map Delivery" onClick="location.href='#mapdelivery'" style="background-image:url(img/icon/mapitf.jpg); " >-->

<select name="cmbproducttype" onChange="msg('parent',this)" style="float:right;" >
	<?php 
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Select unProductType,PTName From producttype Where Status=1")){
		$stmt->execute();
		$stmt->bind_result($unProductType,$PTName);
		while($stmt->fetch()){
				?>
				<option value="<?php echo $unProductType; ?>" 
					<?php 
						$type = (isset($_GET['type']))?$_GET['type']:''; 
						echo ($type==$unProductType)?'Selected':''; 
					?> ><?php echo $PTName; ?></option>
                <?php
			}
		$stmt->close();
		}
	?>
</select>
</div>   
<?php
$OldPGName='';
if($type==ExecuteReader("Select unProductType as `result` From producttype Where PTName='Products'")){
	?>
	<div class="listview" id="lvproduct" style="color:#333;">
        <div class="column" id="colproduct">
            <div class="columnheader" style="width:160px; text-align:left;">Products</div>
            <div class="columnheader" style="width:131px; text-align:right;">Start Balance</div>
            <div class="columnheader" style="width:131px; text-align:right;">Transfer</div>
            <div class="columnheader" style="width:131px; text-align:right;">Damage / Return</div>
            <div class="columnheader" style="width:131px; text-align:right;">Sold</div>
            <div class="columnheader" style="width:131px; text-align:right;">Ending</div>
            <div class="columnheader" style="width:131px; text-align:right;">Adjustment</div>
            <div class="columnheader" style="width:131px; text-align:right;">Process In</div>
            <div class="columnheader" style="width:131px; text-align:right;">Amount</div>
        </div>
  		<div class="row" id="rowproduct" style="margin-bottom:50px;">
			<?php
            $i = 0;
            $mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
            $stmt=$mysqli->stmt_init();
            if($stmt->prepare("Select inventorydata.unInventoryData,inventorydata.unProductItem,PIName,TIDPrice,TIDCost,PGName,IDStart,IDTransfer,IDDamage,IDProcessIn,IDEndWhole,IDAdjustment,IDSoldQuantity,IDSoldAmount,PIPack
                                From inventorydata
                                Inner Join productitem ON inventorydata.unProductItem = productitem.unProductItem
                                Inner Join templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
                                Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
                                Where inventorydata.unInventoryControl = ? and unProductType = ? and templateitemdata.unTemplateItemControl=(Select unTemplateItemControl From branch Where unBranch=?)
                                Order By unProductType Asc , productgroup.PGPriority Asc, TIDPriority Asc;")){
                $stmt->bind_param('iii',$_GET['did'],$_GET['type'],$_GET['bid']);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($unInventoryData,$unProductItem,$PIName,$PITPrice,$PITCost,$PGName,$IDStart,$IDTransfer,$IDDamage,$IDProcessIn,$IDEndWhole,$IDAdjustment,$IDSoldQuantity,$IDSoldAmount,$PIPack);
                while ($stmt->fetch())
                {
                    if ($OldPGName!=$PGName){
                        $OldPGName=$PGName;
                        echo '<div class="group">'.$PGName.'</div>';
                    }
                    ?>
                    <div class="listviewitem" id="lvitem-<?php echo $i;?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                        <div class="inventorylistviewsubitem" style="width:141px; text-align:left; margin-left:5px;"><?php echo $PIName; ?><input type="hidden" id="hdnpack-<?php echo	$i; ?>" value="<?php echo $PIPack; ?>"></div>
                        <input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitem" type="text" id="txt-<?php echo $i.'-0'; ?>" name="txt-<?php echo $i.'-0'; ?>" value="<?php echo $IDStart; ?>" > <!-- Start Balance -->
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitem" type="text" id="txt-<?php echo $i.'-transfer'; ?>" name="txt-<?php echo $i.'-transfer'; ?>" value="<?php echo $IDTransfer; ?>" style="color:#<?php echo ($IDTransfer<0)?'F00':'8C8C8C'; ?>; width:131px; " > <!-- Tansfer -->        
						<input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitem" type="text" id="txt-<?php echo $i.'-damage'; ?>" name="txt-<?php echo $i.'-damage'; ?>" value="<?php echo $IDDamage; ?>" style="color:#8C8C8C;"> <!-- Damage/Return -->
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitem" type="text" id="txt-<?php echo $i.'-sold'; ?>" name="txt-<?php echo $i.'-sold'; ?>" value="<?php echo $IDSoldQuantity; ?>" style="color:#8C8C8C;"> <!-- Sold -->
                        <input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitem" type="text" id="txt-<?php echo $i.'-end'; ?>" name="txt-<?php echo $i.'-end'; ?>" value="<?php echo $IDEndWhole; ?>" > <!-- Ending -->
                        <!-- Readonly -->
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitem" type="text" id="txt-<?php echo $i.'-adj'; ?>" name="txt-<?php echo $i.'-adj'; ?>" value="<?php echo $IDAdjustment; ?>" style="color:#8C8C8C;" > <!-- Adjustment -->
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitem" type="text" id="txt-<?php echo $i.'-processin'; ?>" value="<?php echo $IDProcessIn; ?>" style="color:#<?php echo ($IDProcessIn<0)?'F00':'8C8C8C'; ?>;" > <!-- Process In -->
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitem" type="text" id="txt-<?php echo $i.'-amount'; ?>" value="<?php echo $IDSoldAmount; ?>" style="color:#8C8C8C;" > <!-- Amount -->   
                        <!-- Hidden -->
                        <input type="hidden" id="hdn-<?php echo $i.'-pip'; ?>" name="hdn-<?php echo $i.'-pip'; ?>" value="<?php echo $PITPrice.'-'.$unInventoryData.'-'.$unProductItem; ?>" >     
                    </div>
                    <?php
                    $i++;
                }
                $rowcount = $stmt->num_rows;
                $_SESSION['rowcount']=$rowcount;
                $_SESSION['bid']=$_GET['bid'];
                $_SESSION['did']=$_GET['did'];
                $_SESSION['type']=$_GET['type'];
                $stmt->close();
            }?>
		</div>
	</div>
<?php
}elseif($type==ExecuteReader("Select unProductType as `result` From producttype Where PTName='Rawmats'")){
	?>
    <div class="listview" id="lvrawmats" style="color:#333;">
    	<div class="column" id="colrawmats" style="height:44px;">
            <div class="columnheader" style="width:166px;">Products</div>
            <div class="columnheader" style="width:86px; text-align:right;">Starting</div>
            <div class="columnheader" style="width:86px; text-align:right;">Delivery</div>
            <div class="columnheader" style="width:86px; text-align:right;">Transfer</div>
            <div class="columnheader" style="width:86px; text-align:right;">Damage / Return</div>
            <div class="columnheader" style="width:76px; text-align:right;">End Whole</div>
            <div class="columnheader" style="width:96px; text-align:right;">End Fraction</div>
            <div class="columnheader" style="width:86px; text-align:right;">End Total</div>
            <div class="columnheader" style="width:86px; text-align:right;">Adjustment</div>
            <div class="columnheader" style="width:86px; text-align:right;">Process Out Usage</div>
            <div class="columnheader" style="width:86px; text-align:right;">Process Out Standard</div>
            <div class="columnheader" style="width:96px; text-align:right;">Variance Qty</div>
            <div class="columnheader" style="width:96px; text-align:right;">Variance Amt</div>
		</div>
        <div class="row" id="rowrawmats">
			<?php
            $i = 0;
            $mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
            $stmt=$mysqli->stmt_init();
            if($stmt->prepare("Select inventorydata.unInventoryData,inventorydata.unProductItem,PIName,IDCharge,PGName,IDStart,IDDelivery,IDTransfer,IDDamage,IDEndWhole,IDEndFraction,IDEndTotal,IDAdjustment,IDDIRUsage,IDProcessOut,IDVarianceQTY,IDVarianceAmount,
                                ifnull((Select PCRatio From productconversion Where PCSet='F' and productconversion.unProductItem=inventorydata.unProductItem ORDER BY PCSet, PCRatio DESC limit 1),0) as `PCRatio`,PIPack
                                From inventorydata
                                Inner Join productitem ON inventorydata.unProductItem = productitem.unProductItem
                                Inner Join templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
                                Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
                                Where inventorydata.unInventoryControl = ? and unProductType = ? and templateitemdata.unTemplateItemControl=(Select unTemplateItemControl From branch Where unBranch=?)
                                Order By unProductType Asc ,  productgroup.PGPriority Asc, TIDPriority Asc")){
				$stmt->bind_param('iii',$_GET['did'],$_GET['type'],$_GET['bid']);
				//die($_GET['did'].$_GET['type'].$_GET['bid']);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($unInventoryData,$unProductItem,$PIName,$IDCharge,$PGName,$IDStart,$IDDelivery,$IDTransfer,$IDDamage,$IDEndWhole,$IDEndFraction,$IDEndTotal,$IDAdjustment,$IDDIRUsage,$IDProcessOut,$IDVarianceQTY,$IDVarianceAmount,$PCRatio,$PIPack);
				while ($stmt->fetch()){
					if ($OldPGName!=$PGName){
						$OldPGName=$PGName;
						echo '<div class="group">'.$PGName.'</div>';
					}
					
					//echo $unInventoryData.$unProductItem.$PIName.$PITPrice.$PITCost.$PGName.$IDStart.$IDTransfer.$IDDamage.$IDProcessIn.$IDEndWhole.$IDAdjustment.$IDSoldQuantity.$IDSoldAmount.$PIPack.'</br>';
					?>
					<div class="listviewitem" id="lvitem-<?php echo $i;?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>; width:100%;">
						<div class="inventorylistviewitemtextrawmats" style="width:166px;" id="lvtext-<?php echo $i; ?>"><?php echo $PIName; ?><input type="hidden" id="hdnpack-<?php echo $i; ?>" value="<?php echo $PCRatio; ?>"></div>
						<input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-0'; ?>" name="txt-<?php echo $i.'-0'; ?>" value="<?php echo $IDStart; ?>" title="Press Ctrl + Enter to enter pack(s) and piece(s)" ><input type="hidden" id="hdnunProductItem<?php echo $i; ?>" value="<?php echo $unProductItem; ?>">
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-delivery'; ?>" name="txt-<?php echo $i.'-delivery'; ?>" value="<?php echo $IDDelivery; ?>" style="color:#8C8C8C;" >
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-transfer'; ?>" name="txt-<?php echo $i.'-transfer'; ?>" value="<?php echo $IDTransfer; ?>" style="color:#<?php echo ($IDTransfer<0)?'F00':'8C8C8C'; ?>" > 
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-damage'; ?>" name="txt-<?php echo $i.'-damage'; ?>" value="<?php echo $IDDamage; ?>" style="color:#8C8C8C;" >
                        <input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-1'; ?>" name="txt-<?php echo $i.'-1'; ?>" value="<?php echo $IDEndWhole; ?>" style="width:76px;">
                        <input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-2'; ?>" name="txt-<?php echo $i.'-2'; ?>" value="<?php echo $IDEndFraction; ?>" style="width:96px;">
                        <!--- Reade Only -->
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-endtotal'; ?>" name="txt-<?php echo $i.'-endtotal'; ?>" value="<?php echo $IDEndTotal; ?>" style="color:#<?php echo ($IDEndTotal<0)?'F00':'8C8C8C'; ?>;">
                        <input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-3'; ?>" name="txt-<?php echo $i.'-3'; ?>" value="<?php echo $IDAdjustment;?>" style="width:86px;">
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-dirusage'; ?>" value="<?php echo $IDDIRUsage; ?>" style="color:#<?php echo ($IDDIRUsage<0)?'F00':'8C8C8C'; ?>;">
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-processout'; ?>" value="<?php echo $IDProcessOut; ?>" style="color:#<?php echo ($IDProcessOut<0)?'F00':'8C8C8C'; ?>;">                      
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-varianceqty'; ?>" value="<?php echo $IDVarianceQTY; ?>" style="color:#<?php echo ($IDVarianceQTY<0)?'F00':'8C8C8C'; ?>; width:96px;"> 
                        <input autocomplete="off" readonly onKeyPress="return disableEnterKey(event)" class="inventorylistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-varianceamt'; ?>" value="<?php echo $IDVarianceAmount; ?>" style="color:#<?php echo ($IDVarianceAmount<0)?'F00':'8C8C8C'; ?>; width:96px;">					
                        <!--- Hidden -->
						<input type="hidden" id="hdn-<?php echo $i.'-cidrpp';?>" name="hdn-<?php echo $i.'-cidrpp';?>" value="<?php echo $IDCharge.'-'.$unInventoryData.'-'.$PCRatio.'-'.$IDProcessOut.'-'.$unProductItem; ?>" >
					</div>
					<?php
					$i++;
				}
					$rowcount = $stmt->num_rows;
					$_SESSION['rowcount']=$rowcount;
					$_SESSION['bid']=$_GET['bid'];
					$_SESSION['did']=$_GET['did'];
					$_SESSION['type']=$_GET['type'];
					$stmt->close();
            }?>
        </div>
	</div>
    <?php
}
?>

</form>

<div id="editinventorysheet" class="popup">
	<div class="popupcontainer">
    	<div class="popuptitle" align="center">Edit Inventory Sheet</div>
        <form action="include/inventory.fnc.php" method="post" >
        <div class="popupitem">
        	<div class="popupitemlabel" >Branch</div>
        	<select name="cmbBranch" style="width:200px;" required >
			<?php 
                $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt=$mysqli->stmt_init();
                if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName")){
					$stmt->bind_param("i",$_SESSION['area']);					
					$stmt->execute();
					$stmt->bind_result($unBranch,$BName);
					while($stmt->fetch()){
						?>
						<option value="<?php echo $unBranch; ?>" <?php echo ($bid==$unBranch)?'Selected':''; ?> ><?php echo $BName; ?></option>
						<?php
						}
					$stmt->close();
                }
            ?>
            </select>
		</div>
		<div class="popupitem"> <div class="popupitemlabel">Date</div><input name="dtpDate" type="date" style="width:195px; height:20px;" required value="<?php echo ExecuteReader('Select ICDate as `result` From inventorycontrol Where unInventoryControl='.$_SESSION['did']); ?>"> </div>
		<div class="popupitem"> <div class="popupitemlabel">Sheet Number</div><input name="txtSheetNumber" type="text" style="width:195px; height:20px;" required value="<?php echo ExecuteReader('Select ICNumber as `result` From inventorycontrol Where unInventoryControl='.$_SESSION['did']); ?>" > </div>
        <div class="popupitem"> <div class="popupitemlabel">Remarks</div><textarea name="txtRemark" style="max-width:292px; width:292px; height:100px; resize:none;" title="Remarks" ><?php echo ExecuteReader('Select ICRemarks as `result` From inventorycontrol Where unInventoryControl='.$_SESSION['did']); ?></textarea></div>
        <div align="center">
            <input name="btnEditInventorySheet" type="submit" value="Update" title="Update" class="buttons" >
            <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
		</div>
        </form>
    </div>
</div>

<div id="mapdelivery" class="popup">
	<?php 
	$colBranch = new Collection;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName Asc")){
		$stmt->bind_param('i',$_SESSION['area']);
		$stmt->execute();
		$stmt->bind_result($unBranch,$BName);
		while($stmt->fetch()){
			$colBranch->Add($BName,$unBranch);
		}
		$stmt->close();
	}		
	?>
	<div class="popupcontainer" style="width:590px;">
    	<div class="popuptitle" align="center">Map Sales</div>
        <div class="listbox" style="width:200px; height:300px;">
            <div class="listboxitem" onClick="window.open('createdelivery.php?&bid=<?php echo $_GET['bid']; ?>')"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;">Create New Delivery</div>
			<?php
            $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
            $stmt = $mysqli->stmt_init();
            if($stmt->prepare("Select unDeliveryControl,DCDocNum,concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCDate`,bfrom.BName,bto.BName,unBranchTo From deliverycontrol
								Inner Join branch as `bfrom` on deliverycontrol.unBranchFrom = bfrom.unBranch
								Inner Join branch as `bto` on deliverycontrol.unBranchTo = bto.unBranch
								Where unBranchTo=? and deliverycontrol.`Status`=1 and unInventoryControl = 0 Order by unDeliveryControl Desc")){
				$stmt->bind_param('i',$_GET['bid']);
				$stmt->execute();
                $stmt->bind_result($unDeliveryControl,$DCDocNum,$DCDate,$BranchFrom,$BranchTo,$unBranchTo);
                while($stmt->fetch()){
                    ?>
                    <div class="listboxitem" onClick="loaddelivertdata(<?php echo $unDeliveryControl; ?>,'<?php echo $BranchFrom; ?>','<?php echo $BranchTo; ?>',<?php echo $unBranchTo; ?>)"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;"><?php echo $DCDocNum.' - ['.$DCDate.']'; ?></div>
                    <?php
                }
                $stmt->close();
            }
            ?>
       	</div>
        <div style="position:absolute; left:230px; top:45px; width:380px; height:300px; background-color:#FFF;">
            <div class="listview" id="lvMAP">
                <div class="column" id="colMAP">
                    <div class="columnheader" style="width:150px; text-align:left;">Item</div>
                    <div class="columnheader" style="width:51px; text-align:right;">Qty</div>
                    <div class="columnheader" style="width:51px; text-align:center;">Unit</div>
                </div>
                <div class="row" id="deliverydata" style="height:275px;"></div>
            </div>
        </div>
        <form id="frmMapDelivery" name="frmMapDelivery" method="post" action="include/delivery.inc.php">
            <div style="padding-top:10px; width:250px;">
                <div style="font-weight:bold;">Branch</div>
                <div style="padding-left:10px; width:240px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left; vertical-align:middle;">From:</div>
                    <input type="text"  id="txtBranchFrom" style="width:150px;" readonly >
                </div>
                <div style="padding-left:10px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left;">To:</div>
                    <input type="text" id="txtBranchTo" style="width:150px;" readonly >
                </div>
            </div>
            
            <div style="position:absolute; left:270px; top:345px; padding-top:10px; width:270px;">
                <div style="font-weight:bold;">Inventory Sheet</div>
                <div style="padding-left:10px;">
                    <div style="padding-top:4px; width:50px; cursor:default; float:left;">To:</div>
                    <select name="cmbDIRTo" id="cmbDIRTo" style="width:200px;" >
                    </select>
                </div>
            </div>
            <div align="center" style="padding-top:10px;">
                <input name="btnSaveMapping" type="submit" value="Save" title="Save" class="buttons" >
                <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>
    </div>
</div>


<?php include 'footer.php'; ?>    
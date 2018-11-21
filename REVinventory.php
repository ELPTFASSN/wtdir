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
<input type="button" class="toolbarbutton" title="New" name="btnNew" onclick="location.href='#createinventorysheet'" style="background-image:url(img/icon/new.png);" >
<?php
	$bid = (isset($_GET['bid']))?$_GET['bid']:'';
	if($bid!='' && $ICLock==0){
		?>
        <button type="submit" class="toolbarbutton" title="Save" name="btnSave" style="background-image:url(img/icon/save.png);"></button>
		<input type="button" class="toolbarbutton" title="Edit Inventory Sheet" name="btnEdit" onclick="location.href='#<?php echo ($_GET['bid']!='')?'editinventorysheet':''; ?>'" style="background-image:url(img/icon/save35x27.png);" >
		<?php
	}
?>
</div>   
    <div class="listview" id="lvrawmats" style="color:#333;">
    	<div class="column" id="colrawmats" style="height:20px; padding-top:10px; padding-left:0px">
            <div class="columnheader" style="width:39%; text-align:center; border-right:solid thin #999; border-left:solid thin #CCC; padding-left:0; padding-right:0;">RAWMATS</div>
            <div class="columnheader" style="width:30%; text-align:center; border-right:solid thin #999; border-left:solid thin #CCC">PRODUCTS</div>
            <div class="columnheader" style="width:30%; text-align:center; border-left:solid thin #CCC">USAGE</div>
		</div>
        <div class="misrow" id="misinputrows">
            <div class="miscolumn" id="misrawmats">
            	<?php
					$OldPGName='';
					$i = 0;
					$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt=$mysqli->stmt_init();
						if($stmt->prepare("SELECT productitem.unProductItem, PIName, PGName, 
							IFNULL((SELECT IDEndWhole FROM inventorydata INNER JOIN inventorycontrol ON inventorydata.unInventoryControl=inventorycontrol.unInventoryControl WHERE inventorydata.unProductItem = productitem.unProductItem AND inventorydata.unInventoryControl=? AND inventorycontrol.unBranch=?),0) AS `EndWhole`, 
							IFNULL((SELECT IDEndFraction FROM inventorydata  INNER JOIN inventorycontrol ON inventorydata.unInventoryControl=inventorycontrol.unInventoryControl WHERE inventorydata.unProductItem = productitem.unProductItem AND inventorydata.unInventoryControl=? AND inventorycontrol.unBranch=?),0) AS `EndFraction`,
							IFNULL( (SELECT PUOMName
							FROM productconversion
INNER JOIN productuom ON productconversion.unProductUOM = productuom.unProductUOM
							WHERE PCSet =  'W'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PIWhole` , IFNULL( (
							SELECT PUOMName
							FROM productconversion
INNER JOIN productuom ON productconversion.unProductUOM = productuom.unProductUOM
							WHERE PCSet =  'F'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PIFraction` , IFNULL( (
							SELECT PCRatio
							FROM productconversion
							WHERE PCSet =  'F'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PCRatio` , PIPack
							FROM productitem
							INNER JOIN templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
							INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
							WHERE unProductType =2
							AND productitem.Status=1
							AND templateitemdata.unTemplateItemControl = ( 
							SELECT unTemplateItemControl
							FROM branch
							WHERE unBranch = ?) 
							ORDER BY unProductType ASC , productgroup.PGPriority ASC , TIDPriority ASC ")){
								$stmt->bind_param('iiiii',$_GET['did'],$_GET['bid'],$_GET['did'],$_GET['bid'],$_GET['bid']);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($unProductItem,$PIName,$PGName,$EndWhole,$EndFraction,$PIWhole,$PIFraction,$PCRatio,$PIPack);
								while ($stmt->fetch()){
									$i = $i+1;
									if ($OldPGName!=$PGName){
										$OldPGName=$PGName;
										echo '<div class="misgroup">'.$PGName.' <span class="misqty">QTY</span><span class="misqty">QTY</span></div>';
									}
									?>
									<div class="mislistviewitem" id="lvitem-<?php echo $i;?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
											<div class="mislistviewitemtextrawmats" id="lvtext-<?php echo $i; ?>"><?php echo $PIName; ?>
                                            </div>
											<?php if($PIWhole!='0'){ ?><div class="misqtylabel"><input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="mislistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-0'; ?>" name="txt-<?php echo $i.'-0'; ?>" value="<?php echo $EndWhole; ?>" title="Press Ctrl + Enter to enter pack(s) and piece(s)" width="100" >
                                            <?php echo $PIWhole; ?>(s)</div> <?php } ?>	
											<?php if($PIFraction!='0'){ ?><div class="misqtylabel"><input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="mislistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-1'; ?>" name="txt-<?php echo $i.'-1'; ?>" value="<?php echo $EndFraction; ?>" style="width:76px;" width="100" >
                                            <?php echo $PIFraction; ?>(s)</div> <?php } ?>	
											<!--- Hidden -->
											<input type="hidden" id="hdn-<?php echo $i.'-cidrpp';?>" name="hdn-<?php echo $i.'-cidrpp';?>" value="0.0000" >
                                            <input type="hidden" id="hdnpack-<?php echo $i; ?>" value="<?php echo $PCRatio; ?>">
                                            <input type="hidden" id="hdnunProductItem<?php echo $i; ?>" value="<?php echo $unProductItem; ?>">
										</div>
					<?php
								}
								$rowcount = $stmt->num_rows;
								$_SESSION['rowcount']=$rowcount;
								$_SESSION['bid']=$_GET['bid'];
								$_SESSION['did']=$_GET['did'];
								$_SESSION['type']=$_GET['type'];
								$stmt->close();
						} 
				?>
            </div>
            <div class="miscolumn" id="misproducts">
            	<?php
					$OldPGName='';
					$i = 0;
					$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt=$mysqli->stmt_init();
						if($stmt->prepare("SELECT productitem.unProductItem, PIName, PGName, 
							IFNULL((SELECT IDEndWhole FROM inventorydata INNER JOIN inventorycontrol ON inventorydata.unInventoryControl=inventorycontrol.unInventoryControl WHERE inventorydata.unProductItem = productitem.unProductItem AND inventorydata.unInventoryControl=? AND inventorycontrol.unBranch=?),0) AS `EndWhole`, 
							IFNULL((SELECT IDEndFraction FROM inventorydata  INNER JOIN inventorycontrol ON inventorydata.unInventoryControl=inventorycontrol.unInventoryControl WHERE inventorydata.unProductItem = productitem.unProductItem AND inventorydata.unInventoryControl=? AND inventorycontrol.unBranch=?),0) AS `EndFraction`,
							IFNULL( (SELECT PUOMName
							FROM productconversion
INNER JOIN productuom ON productconversion.unProductUOM = productuom.unProductUOM
							WHERE PCSet =  'W'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PIWhole` , IFNULL( (
							SELECT PUOMName
							FROM productconversion
INNER JOIN productuom ON productconversion.unProductUOM = productuom.unProductUOM
							WHERE PCSet =  'F'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PIFraction` , IFNULL( (
							SELECT PCRatio
							FROM productconversion
							WHERE PCSet =  'F'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PCRatio` , PIPack
							FROM productitem
							INNER JOIN templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
							INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
							WHERE unProductType =1
							AND productitem.Status=1
							AND templateitemdata.unTemplateItemControl = ( 
							SELECT unTemplateItemControl
							FROM branch
							WHERE unBranch = ?) 
							ORDER BY unProductType ASC , productgroup.PGPriority ASC , TIDPriority ASC ")){
								$stmt->bind_param('iiiii',$_GET['did'],$_GET['bid'],$_GET['did'],$_GET['bid'],$_GET['bid']);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($unProductItem,$PIName,$PGName,$EndWhole,$EndFraction,$PIWhole,$PIFraction,$PCRatio,$PIPack);
								while ($stmt->fetch()){
									$i = $i+1;
									if ($OldPGName!=$PGName){
										$OldPGName=$PGName;
										echo '<div class="misgroup">'.$PGName.' <span class="misqty">QTY</span></div>';
									}
									$EndWhole=number_format($EndWhole, 0, '', '');
									?>
									<div class="mislistviewitem" id="lvitem-<?php echo $i;?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
											<div class="mislistviewitemtextrawmats" id="lvtext-<?php echo $i; ?>"><?php echo $PIName; ?>
                                            </div>
											<?php if($PIWhole!='0'){ ?><div class="misqtylabel"><input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="mislistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-2'; ?>" name="txt-<?php echo $i.'-3'; ?>" value="<?php echo $EndWhole; ?>" style="width:76px;" width="100" min="0" step="1">
                                             <?php echo $PIWhole; ?>(s)</div>	 <?php } ?>		
											<!--- Hidden -->
											<input type="hidden" id="hdn-<?php echo $i.'-cidrpp';?>" name="hdn-<?php echo $i.'-cidrpp';?>" value="0.0000" >
                                            <input type="hidden" id="hdnpack-<?php echo $i; ?>" value="<?php echo $PCRatio; ?>">
                                            <input type="hidden" id="hdnunProductItem<?php echo $i; ?>" value="<?php echo $unProductItem; ?>">
										</div>
					<?php
								}
								$rowcount = $stmt->num_rows;
								$_SESSION['rowcount']=$rowcount;
								$_SESSION['bid']=$_GET['bid'];
								$_SESSION['did']=$_GET['did'];
								$_SESSION['type']=$_GET['type'];
								$stmt->close();
						}
				?>
            </div>
            <div class="miscolumn" id="misusage">
            	<?php
					$OldPGName='';
					$i = 0;
					$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt=$mysqli->stmt_init();
					if($stmt->prepare("Select inventorydata.unInventoryData,inventorydata.unProductItem,PIName,IDCharge,PGName,IDStart,IDDelivery,IDTransfer,IDDamage,IDEndWhole,IDEndFraction,IDEndTotal,IDAdjustment,IDDIRUsage,IDProcessOut,IDVarianceQTY,IDVarianceAmount,
										ifnull((Select PCRatio From productconversion Where PCSet='F' and productconversion.unProductItem=inventorydata.unProductItem ORDER BY PCSet, PCRatio DESC limit 1),0) as `PCRatio`,PIPack
										From inventorydata
										Inner Join productitem ON inventorydata.unProductItem = productitem.unProductItem
										Inner Join templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
										Inner Join productgroup ON productitem.unProductGroup = productgroup.unProductGroup
										Where inventorydata.unInventoryControl = ? and unProductType = 1 and templateitemdata.unTemplateItemControl=(Select unTemplateItemControl From branch Where unBranch=?)
										Order By unProductType Asc ,  productgroup.PGPriority Asc, TIDPriority Asc")){
						$stmt->bind_param('ii',$_GET['did'],$_GET['bid']);
						$stmt->execute();
						$stmt->store_result();
						$stmt->bind_result($unInventoryData,$unProductItem,$PIName,$IDCharge,$PGName,$IDStart,$IDDelivery,$IDTransfer,$IDDamage,$IDEndWhole,$IDEndFraction,$IDEndTotal,$IDAdjustment,$IDDIRUsage,$IDProcessOut,$IDVarianceQTY,$IDVarianceAmount,$PCRatio,$PIPack);
						while ($stmt->fetch()){
							if ($OldPGName!=$PGName){
								$OldPGName=$PGName;
								echo '<div class="misgroup">'.$PGName.' <span class="misqty">QTY</span></div>';
							}
							?>
							<div class="mislistviewitem" id="lvitem-<?php echo $i;?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
								<div class="mislistviewitemtextrawmats" id="lvtext-<?php echo $i; ?>"><?php echo $PIName; ?></div>
								<div class="misqtylabel"><input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="mislistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-3'; ?>" name="txt-<?php echo $i.'-5'; ?>" value="<?php echo $IDEndWhole; ?>" style="width:76px;" min="0" step="1">
                                pc(s)</div>				
								<!--- Hidden -->
                                <input type="hidden" id="hdnpack-<?php echo $i; ?>" value="<?php echo $PCRatio; ?>">
                                <input type="hidden" id="hdnunProductItem<?php echo $i; ?>" value="<?php echo $unProductItem; ?>">
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
					}else{
						if($stmt->prepare("SELECT productitem.unProductItem, PIName, PGName, IFNULL( (
							SELECT PUOMName
							FROM productconversion
INNER JOIN productuom ON productconversion.unProductUOM = productuom.unProductUOM
							WHERE PCSet =  'W'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PIWhole` , IFNULL( (
							SELECT PUOMName
							FROM productconversion
INNER JOIN productuom ON productconversion.unProductUOM = productuom.unProductUOM
							WHERE PCSet =  'F'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PIFraction` , IFNULL( (
							SELECT PCRatio
							FROM productconversion
							WHERE PCSet =  'F'
							AND productconversion.unProductItem = productitem.unProductItem
							ORDER BY PCSet, PCRatio DESC 
							LIMIT 1
							), 0 ) AS  `PCRatio` , PIPack
							FROM productitem
							INNER JOIN templateitemdata ON productitem.unProductItem = templateitemdata.unProductItem
							INNER JOIN productgroup ON productitem.unProductGroup = productgroup.unProductGroup
							WHERE unProductType =1
							AND productitem.Status=1
							AND templateitemdata.unTemplateItemControl = ( 
							SELECT unTemplateItemControl
							FROM branch
							WHERE unBranch = ?) 
							ORDER BY unProductType ASC , productgroup.PGPriority ASC , TIDPriority ASC ")){
								$stmt->bind_param('i',$_GET['bid']);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($unProductItem,$PIName,$PGName,$PIWhole,$PIFraction,$PCRatio,$PIPack);
								while ($stmt->fetch()){
									$i = $i+1;
									if ($OldPGName!=$PGName){
										$OldPGName=$PGName;
										echo '<div class="misgroup">'.$PGName.' <span class="misqty">QTY</span></div>';
									}
									?>
									<div class="mislistviewitem" id="lvitem-<?php echo $i;?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
											<div class="mislistviewitemtextrawmats" id="lvtext-<?php echo $i; ?>"><?php echo $PIName; ?>
                                            </div>
											<?php if($PIWhole!='0'){ ?><div class="misqtylabel"><input autocomplete="off" onKeyPress="return disableEnterKey(event)" class="mislistviewsubitemrawmats" type="text" id="txt-<?php echo $i.'-3'; ?>" name="txt-<?php echo $i.'-5'; ?>" value="0" style="width:76px;" width="100" min="0" step="1">
                                             <?php echo $PIWhole; ?>(s)</div>  <?php } ?>	
											<!--- Hidden -->
											<input type="hidden" id="hdn-<?php echo $i.'-cidrpp';?>" name="hdn-<?php echo $i.'-cidrpp';?>" value="0.0000" >
                                            <input type="hidden" id="hdnpack-<?php echo $i; ?>" value="<?php echo $PCRatio; ?>">
                                            <input type="hidden" id="hdnunProductItem<?php echo $i; ?>" value="<?php echo $unProductItem; ?>">
										</div>
					<?php
								}
								$rowcount = $stmt->num_rows;
								$_SESSION['rowcount']=$rowcount;
								$_SESSION['bid']=$_GET['bid'];
								$_SESSION['did']=$_GET['did'];
								$_SESSION['type']=$_GET['type'];
								$stmt->close();
						}
					} 
				?>
            </div>
            <div class="miscolumn" id="misinfo" style="width:40%; float:right; border:none; padding:10%; font-size:14px">
            	<!--<div class="group" style="font-size:10px; width:100%">INFO</div> -->
                <div class="misinfocol" id="miscashbo">
                	<div style="width:100%;"><b>CASH BREAKDOWN</b></div>
                    <div class="misdenogroup" style="padding-left:10%; width:45%"><b>BILLS</b></div>
                    <div class="misdenogroup"><b>COINS</b></div>
                    <div class="misdenocol">
                    	<div style="height:23px"><div class="misdenolabel">1000 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-1-4" value="0"/><input type="hidden" id="Bx-1" value="1000" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">500 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-2-4" value="0"/><input type="hidden" id="Bx-2" value="500" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">200 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-3-4" value="0"/><input type="hidden" id="Bx-3" value="200" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">100 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-4-4" value="0"/><input type="hidden" id="Bx-4" value="100" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">50 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-5-4" value="0"/><input type="hidden" id="Bx-5" value="50" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">20 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-6-4" value="0"/><input type="hidden" id="Bx-6" value="20" /></div></div>
                        <div style="height:23px"><div class="misdenolabel"><b>TOTAL BILLS</b> </div><div class="misdenoholder"><input class="misdenomination" readonly="readonly" type="text" id="txt-7-4" value="0.00"/></div></div>
                    </div>
                    <div class="misdenocol">
                    	<div style="height:23px"><div class="misdenolabel">10.00 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-1-5" value="0"/><input type="hidden" id="Cx-1" value="10" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">5.00 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-2-5" value="0"/><input type="hidden" id="Cx-2" value="5" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">1.00 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-3-5" value="0"/><input type="hidden" id="Cx-3" value="1" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">0.25 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-4-5" value="0"/><input type="hidden" id="Cx-4" value=".50" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">0.10 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-5-5" value="0"/><input type="hidden" id="Cx-5" value=".25" /></div></div>
                        <div style="height:23px"><div class="misdenolabel">0.05 X </div><div class="misdenoholder"><input class="misdenomination" type="text" id="txt-6-5" value="0"/><input type="hidden" id="Cx-6" value=".05" /></div></div>
                        <div style="height:23px"><div class="misdenolabel"><b>TOTAL COINS</b> </div><div class="misdenoholder"><input class="misdenomination" readonly="readonly" type="text" id="txt-7-5" value="0.00"/></div></div>
                    </div>
                    <div class="misdenocol" style="width:100%; display:inline-block; padding-top:20px">
                    	<div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">TOTAL BILLS & COINS</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;" readonly="readonly" type="text" id="txt-8-5" value="0.00"/></div></div>
                        <div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">TOTAL DEPOSIT</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;" type="text" id="txt-9-5" value="0.00"/></div></div>
                        <div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">CHANGE FUND</div><div class="misdenoholder"><input class="misdenomination" style="max-width:120px;" type="text" id="txt-10-5"  value="0.00"/></div></div>
                        <div style="height:23px"><div class="misdenolabel" style="width:150px; font-weight:bold">CASHIER</div><select name="cmbEOpen" id="cmbEOpen" style="width:200px; margin-left:5px;" required >
							<?php 
                                 $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                                 $stmt=$mysqli->stmt_init();
                                 if($stmt->prepare("SELECT employee.unEmployee,ELastName,EFirstName,EMiddleName,unArea FROM employee INNER JOIN employeearea ON employee.unEmployee=employeearea.unEmployee WHERE employee.Status=1 AND employeearea.Status=1 AND unArea=?")){
                                  //if($stmt->prepare("SELECT unEmployee,ELastName,EFirstName,EMiddleName FROM employee ")){
                                    $stmt->bind_param('i',$_SESSION['area']);
                                    $stmt->execute();
                                    $stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName,$unEA);
                                    //$stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName);
                                    while($stmt->fetch()){
                                        //if($idEA==$_SESSION['area']){
                            ?>
                                        
                                        <option value="<?php echo $unEmployee; ?>"<?php /*echo ($bid==$idBranch)?'Selected':'';*/ ?> ><?php echo $EFirstName." ".$EMiddleName." ".$ELastName; ?></option>
                            <?php
                                        //}
                                    }
                                    $stmt->close();
                                    }
                            ?>
                            </select></div>
                    </div>
                </div>          	
            </div>
        </div>
        <div class="row" id="rowrawmats">
        </div>
	</div>
    <?php
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
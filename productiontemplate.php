
<?php include 'header.php';?>

<script src="js/productiontemplate.js" type="text/javascript"></script>
<?php //if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){?>
<script type="text/javascript">
function msg(targ,selObj)
{
	var rep;
	var url = "<?php echo $_SERVER['REQUEST_URI']; ?>";
	url = url.split('?')[1];
	var type = url.replace('type='+<?php echo $_GET['type']; ?>,'type='+selObj.options[selObj.selectedIndex].value);
	eval(targ+".location='productiontemplate.php?"+type+"'");
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

<?php
//}

if(isset($_POST['btnproductdatasave']))
{
	// ----- set status of existing entries to false
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("UPDATE templateproductiondata SET `Status`=0 WHERE unTemplateProductionControl=?")){
		$stmt->bind_param("i",$_POST['bid']);
		$stmt->execute();
		$stmt->close();	
	}
	$mysqli->close();
	
	// ----- allocate/overwrite/re-use entries who's statuses were set to false
	$i=0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unTemplateProductionData From templateproductiondata Where unTemplateProductionControl=? Order by unTemplateProductionData")){
		$stmt->bind_param("i",$_POST['bid']);
		$stmt->execute();
		$stmt->bind_result($unTemplateProductionData);
		while($stmt->fetch()){
			for($j=$i+1;$j<=$_POST['hdncount'];$j++){
				if(isset($_POST['hdnproduct-'.$j])){
					$i=$j;
					break;
				}
			}
			$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt1 = $mysqli1->stmt_init();
			if($stmt1->prepare("UPDATE templateproductiondata SET unProductItem=?,unProductUOM=?,TPDCost=?,TPDQuantity=?,TPDAmount=?,TPDProcessType=?,`Status`=1 WHERE unTemplateProductionData=?")){
				$stmt1->bind_param("iddddii",$_POST['hdnproduct-'.$i],$_POST['hdnunit-'.$i],$_POST['txtcost-'.$i],$_POST['txtquantity-'.$i],$_POST['txtamount-'.$i],$_POST['hdnprocesstype-'.$i],$unTemplateProductionData);
				$stmt1->execute();
				$stmt1->close();
			}
			$mysqli1->close();
			if($i==$_POST['hdncount']){break;}
		}
		$stmt->close();		
	}
	$mysqli->close();
	
	// ----- insert excess entries when needed
	for($j=$i+1;$j<=$_POST['hdncount'];$j++){
		if(isset($_POST['hdnproduct-'.$j])){
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("INSERT INTO templateproductiondata(unTemplateProductionControl,unProductItem,unProductUOM,TPDCost,TPDQuantity,TPDAmount,TPDProcessType,unTemplateProductionData) VALUES (?,?,?,?,?,?,?,".getMax('unTemplateProductionData','templateproductiondata').")")){
				$stmt->bind_param("iiidddi",$_POST['bid'],$_POST['hdnproduct-'.$j],$_POST['hdnunit-'.$j],$_POST['txtcost-'.$j],$_POST['txtquantity-'.$j],$_POST['txtamount-'.$j],$_POST['hdnprocesstype-'.$j]);
				$stmt->execute();
				$stmt->close();
			}
			$mysqli->close();
		}
	}

	// ----- sums up TPDCost and update TPCCost
	$sumTPDCost = ExecuteReader("Select sum(TPDAmount) as `result` From templateproductionbatch
								Inner Join templateproductioncontrol 
									On templateproductioncontrol.unTemplateProductionBatch= templateproductionbatch.unTemplateProductionBatch
								Inner Join templateproductiondata 
									On templateproductiondata.unTemplateProductionControl  = templateproductioncontrol.unTemplateProductionControl 
								Inner Join productuom 
									On productuom.unProductUOM = templateproductiondata.unProductUOM
								Inner Join productitem 
									On productitem.unProductItem = templateproductiondata.unProductItem
								Where templateproductioncontrol.unTemplateProductionControl=".$_POST['bid']."
								And templateproductiondata.`Status`=1");

	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt =	$mysqli->stmt_init();
	if($stmt->prepare("UPDATE templateproductioncontrol SET TPCCost=? WHERE unTemplateProductionControl=?"))
	{
		$stmt->bind_param("di",$sumTPDCost,$_POST['bid']);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
}

// ----- add item to templateproductioncontrol
if(isset($_POST['btnitemadd']))
{	
	$isTPCExist = ExecuteReader("Select unTemplateProductionControl as `result` From templateproductioncontrol Where unTemplateProductionBatch=".$_GET['id']." And unProductItem=".$_POST['itemid']);
	if ($isTPCExist > 0) // templateproductioncontrol is existing
	{
		addexistingtpcontrol($isTPCExist);
	}
	else // templateproductioncontrol is not existing
	{
		$query = "Insert Into templateproductioncontrol(unProductItem, unTemplateProductionBatch, unProductUOM, TPCYield, TPCCost,unTemplateProductionControl) 
				  Values (".$_POST['itemid'].",".$_GET['id'].",".$_POST['itemuom'].",0,0,".getMax('unTemplateProductionControl','templateproductioncontrol').")";
		ExecuteNonQuery($query);
	}
}

// ----- update TemplateProductionControl's yield and cost
if(isset($_POST['btnproductioncontrol']))
{	
	$query = "Update templateproductioncontrol Set TPCYield=".$_POST['txtproductionyield'].",TPCCost=".$_POST['txtproductioncost']." Where unTemplateProductionControl=".$_POST['idtpc'];
	ExecuteNonQuery($query);
}

function addexistingtpcontrol($id)
{
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Update templateproductioncontrol Set Status=1 Where unTemplateProductionControl=?"))
	{
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
}

$colItem;
$colProductItemCount=0;
$colAddProductItem = new Collection();
$mysqli_productitem = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
$stmt_productitem = $mysqli_productitem->stmt_init();
if($stmt_productitem->prepare("Select unProductItem, productitem.unProductGroup, productitem.unProductUOM, productuom.PUOMName, PIName, PGName, productitem.Status   
								From productitem 
								Inner Join productgroup
								On productgroup.unProductGroup = productitem.unProductGroup
								Inner Join producttype
								On producttype.unProductType = productgroup.unProductType
								Inner Join productuom
								On productuom.unProductUOM = productitem.unProductUOM
								WHERE  productgroup.unProductType = ? And productitem.Status=1 ORDER BY PIName"))
{
	$stmt_productitem->bind_param('i',$_GET['type']);
	$stmt_productitem->execute();
	$stmt_productitem->bind_result($unProductItem, $unProductGroup, $unProductUOM, $PUOMName, $PIName, $PGName, $Status);
	while($stmt_productitem->fetch())
	{
		$oProductItem = new TemplateProductionData($unProductItem, $unProductGroup, $unProductUOM, $PIName, $PUOMName, $PGName, $Status); 
		$colAddProductItem->Add($oProductItem,$oProductItem->unProductItem);
	}
	$stmt_productitem->close();
}
$colProductItemCount=$colAddProductItem->Count();
$unTICi = 0;
$colProductionControl = new Collection();
$mysqli_productioncontrol = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
$stmt_productioncontrol = $mysqli_productioncontrol->stmt_init();
if($stmt_productioncontrol->prepare("Select unTemplateProductionControl, templateproductioncontrol.unProductItem, templateproductioncontrol.unProductUOM, 
									productitem.unProductGroup, productgroup.PGName, PIName, PUOMName, TPCYield, TPCCost, unTemplateItemControl, templateproductioncontrol.Status
									From templateproductioncontrol 
									Inner Join templateproductionbatch
									On templateproductionbatch.unTemplateProductionBatch = templateproductioncontrol.unTemplateProductionBatch
									Inner Join productitem
									On productitem.unProductItem = templateproductioncontrol.unProductitem
									Inner Join productuom
									On productuom.unProductUOM = templateproductioncontrol.unProductUOM
									Inner Join productgroup
									On productgroup.unProductGroup = productitem.unProductGroup
									Where templateproductioncontrol.Status=1
									And productgroup.unProductType =?
									And templateproductionbatch.unTemplateProductionBatch=?
									Order By productgroup.PGPriority Asc, productitem.PIName ASC"))
{
	$stmt_productioncontrol->bind_param("ii",$_GET['type'],$_GET['id']);
	$stmt_productioncontrol->execute(); 
	$stmt_productioncontrol->bind_result($unTemplateProductionControl, $unProductItem, $unProductUOM, $unProductGroup, $PGName, $PIName, $PUOMName, $TPCYield, $TPCCost, $uTIC, $Status);
	while($stmt_productioncontrol->fetch())
	{
		$unTICi = $uTIC;
		$oProductionControl = new TemplateProductionControl($unTemplateProductionControl, $unProductItem, $unProductUOM, $unProductGroup, $PGName, $PIName, $PUOMName, $TPCYield, $TPCCost, $uTIC, $Status);
		$colProductionControl->Add($oProductionControl,$oProductionControl->unTemplateProductionControl);
		//echo $TPCCost.'---';
	}
	$stmt_productioncontrol->close();
}

for($a=1;$a<=$colAddProductItem->Count();$a++)
{
	$item_productitem = $colAddProductItem->GetByIndex($a);

	for($c=1;$c<=$colProductionControl->Count();$c++)
	{
		$item_productioncontrol = $colProductionControl->GetByIndex($c);

		if($item_productioncontrol->unProductItem == $item_productitem->unProductItem)
		{			
			$colAddProductItem->Remove($item_productitem->unProductItem);
		}
	}
}

$OldPGName='';
 ?>


<script>

	$(window).scroll(function() {
        columnheader('colproductiontemplatecontrol','lvproductiontemplatecontrol');
    });
function loadproductiontemplateinfo(idItem,i,untic){
	var xmlhttp;
	if(idItem==0){
		document.getElementById('rowproductiondata').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('rowproductiondata').innerHTML=xmlhttp.responseText;
			document.getElementById('popmecomponent').innerHTML='[ ' + document.getElementById('divname-'+i).innerHTML + ' ] <?php if($_GET['type']==1){echo 'Components';}else{echo 'End Products';} ?>';
			location.href='#popupedit';	
		}
	}
	xmlhttp.open('POST','ajax/ajax.php', true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadproductiontemplate&bid='+idItem+'&sid=0&untic='+untic);
}

function loadtpcontrol(idControl, tpc, ptype){
	var xmlhttp;
	if(idControl==0){
		document.getElementById('editproductioncontrol').innerHTML='';
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editproductioncontrol').innerHTML=xmlhttp.responseText;
			location.href='#editcontrol';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadproductionitemcontroledit&bid='+idControl+'&tpc='+tpc+'&type='+ptype);
}

function itemrowclick(iditem, piname, iduom, uomname, cost, quantity, amount){
	SelectElement(iduom);
	document.getElementById('txtproductitemsearch').value = piname;
	document.getElementById('hdnidproductitem').value = iditem;
	document.getElementById('txtcost').value = cost;
	document.getElementById('txtquantity').value = quantity;
	document.getElementById('txtamount').value = amount;
	document.getElementById('btnproductdatacancel').title = 'Cancel';
	document.getElementById('btnproductdatacancel').value = 'Cancel';
	document.getElementById('sid').value = '1';	
}
function selecteditem(id, iduom, name){
	document.getElementById('itemid').value = id;
	document.getElementById('itemuom').value = iduom;
	document.getElementById('btnitemadd').value = "Add [ " + name + " ] ";
}
function close_clicked(){
	var e = document.getElementById('btnproductdatacancel').title;	
	if (e == 'Cancel'){
		SelectElement('1');
		document.getElementById('txtproductitemsearch').value = '';
		document.getElementById('hdnidproductitem').value = '';
		document.getElementById('txtcost').value = '';
		document.getElementById('txtquantity').value = '';
		document.getElementById('txtamount').value = '';
		document.getElementById('btnproductdatacancel').title = 'Close';
		document.getElementById('btnproductdatacancel').value = 'Close';
		document.getElementById('sid').value = '0';		
	}
	else{ 
		location.href='#'
	}
}
function SelectElement(valueToSelect){    
    var element = document.getElementById('cmbunit');
    element.value = valueToSelect;
}
function deleteitem(i){ 
		if(confirm('Remove [ ' + document.getElementById('txtname-'+i).getAttribute('value') + ' ] Are you sure?') ){
			var d = document.getElementById('rowproductiondata')
			var olddiv = document.getElementById('lvitem-'+i)		
			d.removeChild(olddiv);
		}
}
function deletetpc(i,idControl,ptype){
		if(confirm('Remove [ ' + document.getElementById('divname-'+i).innerHTML + ' ] Are you sure?') ){
			var xmlhttp;
			if(idControl==0){
				document.getElementById('editproductioncontrol').innerHTML='';
			}
			if(window.XMLHttpRequest){
				xmlhttp=new XMLHttpRequest();
			}
			xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4 && xmlhttp.status==200){
				location.href='<?php echo $_SERVER['PHP_SELF']."?&id=".$_GET['id']."&type=".$_GET['type']; ?>';
				}
			}
			xmlhttp.open('POST','ajax/ajax.php',true);
			xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
			xmlhttp.send('qid=deletetpcontrol&bid='+idControl+'&type='+ptype);
		}
}
function setCostOnChange(idTIC){
	var idPI = document.getElementById('hdnidproductitem').value;
	var idPUOM = document.getElementById('cmbunit').value;
	if (document.getElementById('cmbunit').value==''){
		document.getElementById('cmbunit').value=0;
	}
	$.post('ajax/ajax.php',
			{qid:'loadproductioncost',
			idTIC:idTIC,
			idPI:idPI,
			idPUOM:idPUOM},
			function(data){
				document.getElementById('txtcost').value = data;
				//alert(data);
			});			
}

function setCostOnFocus(idTIC){
	var idPI = document.getElementById('hdnidproductitem').value;
	var idPUOM = document.getElementById('cmbunit').value;
	if (document.getElementById('cmbunit').value==''){
		document.getElementById('cmbunit').value=0;
	}
	$.post('ajax/ajax.php',
			{qid:'loadproductioncost',
			idTIC:idTIC,
			idPI:idPI,
			idPUOM:idPUOM},
			function(data){
				document.getElementById('txtcost').value = data;
				//alert(data);
			});		
}

/*$(document).ready(function() {
	var h = $('#lvproductiontemplatecontrol').height()-$('#colproductiontemplatecontrol').height();
	$('#rowproductiontemplatecontrol').height(h);
	var h = $('#lvproductiondata').height()-$('#colproductiondata').height();
	$('#rowproductiondata').height(h);
});*/
</script>

<div id="toolbar">
	<input type="button" class="toolbarbutton" title="Add" name="btnadd" onclick="location.href='#additem'" style="background-image:url(img/icon/productitem.png);background-repeat:no-repeat;background-position:center;display:inline;" >
    <?php //if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){?>
    <select <?php if($_SESSION['BusinessUnit']!="Waffletime Inc.,"){ ?> style="display:none;"  <?php }?> name="cmbproducttype" id="cmbproducttype" onChange="msg('parent',this)" style="float:right;" >
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
    <?php //} ?>
</div>

<div class="listview" id="lvproductiontemplatecontrol">
	<div class="column" id="colproductiontemplatecontrol">
    	<div class="columnheader" style="width:360px;">Item</div>
        <div class="columnheader" style="width:100px;">Unit</div>
        <div class="columnheader" style="width:100px; text-align:right;">Yield</div>
        <div class="columnheader" style="width:180px; text-align:right;">Cost</div>
        <div class="columnheader" style="width:218px; margin-left:100px;">Action</div>
    </div>
    
    <div class="row" id="rowproductiontemplatecontrol">
    <?php 
	
		$h=0;
			for($a=1;$a<=$colProductionControl->Count()-1;$a++)
			{	
			$colItem_ProductionControl = $colProductionControl->GetByIndex($a);	
			
			if ($OldPGName!=$colItem_ProductionControl->PGName){
				$OldPGName=$colItem_ProductionControl->PGName;
				echo '<div class="group">'.$colItem_ProductionControl->PGName.'</div>';
			}
				//echo $colItem_ProductionControl->unTemplateProductionControl;
		//$unTIC = ExecuteReader("Select unTemplateItemControl as `result` From templateproductionbatch Where `unBranch`=".$_SESSION['bid']." AND `unTemplateProductionBatch=".$_SESSION['did']);

	?>
    		<div class="listviewitem" id="listview-<?php echo $h; ?>" style="background-color:#<?php echo ($h%2)?'EEE':'FFF'; ?>;">
        		<div class="listviewsubitem" id="divname-<?php echo $h; ?>"style="width:360px;"><?php echo $colItem_ProductionControl->PIName; ?></div>
               	<input type="hidden" value="<?php echo $unTICi;?>" id="unTICi">
                <div class="listviewsubitem" style="width:100px;"><?php echo $colItem_ProductionControl->PUOMName; ?></div>
                <div class="listviewsubitem" style="width:100px; text-align:right;"><?php echo $colItem_ProductionControl->TPCYield; ?></div>
                <div class="listviewsubitem" style="width:180px; text-align:right;"><?php echo $colItem_ProductionControl->TPCCost; ?></div>
                <div class="listviewsubitem" style="width:218px; margin-left:100px;">             	   
                   <div title="Edit [ <?php echo $colItem_ProductionControl->PIName;?> ]" class="button16" onclick="loadtpcontrol(<?php echo $_GET['id'].",".$colItem_ProductionControl->unTemplateProductionControl; ?>,<?php echo $_GET['type']; ?>)" style="background-image:url(img/icon/update.png);padding-left:10px;"></div>
				   <div title="Edit [ <?php echo $colItem_ProductionControl->PIName;?> ] Components" class="button16" onclick="loadproductiontemplateinfo(<?php echo $colItem_ProductionControl->unTemplateProductionControl.",".$h.",".$unTICi; ?>)" style="background-image:url(img/icon/production.png);padding-left:0px;"></div>
                   <div title="Delete [ <?php echo $colItem_ProductionControl->PIName;?> ]" class="button16" onClick="deletetpc(<?php echo $h.",".$colItem_ProductionControl->unTemplateProductionControl.",".$_GET['type'];?>)" style="background-image: url(img/icon/delete.png);padding-left:10px;"></div>
                </div>
        	</div>
    <?php
		$h++;
			}
	?>
    	<div class="listviewitem" id="lvitem-end"> 
   		</div>
    </div>
</div>



<div id="popupedit" class="popup">
	<div id="editproduction" class="popupcontainer" style="width:650px;margin:5% auto;">
    
    <div id="popmecomponent" class="popuptitle" align="center">Add Production Item Data</div>
		<div class="popupitem" style="width:650px;">
        	<div class="popitemlabel" style="display:inline-block;width:100px;">Item</div>
            <input type="search" id="txtproductitemsearch" value="" style="position:relative;top:0px;left:0px;width:440px;" required onKeyPress="return disableEnterKey(event)">
            <input type="hidden" id="hdnidproductitem" name="hdnidproductitem" value="0"> 
            <div class="listbox" id="lstresult" style="position:fixed;width:200px;max-height:240px;display:none;"> </div>
        </div>
        
        <div class="popupitem" style="width:650px;">     
			<div class="popitemlabel" style="display:inline-block;width:100px;">Unit</div>
			<select name="cmbunit" id="cmbunit" style="width:440px;"  onKeyPress="return disableEnterKey(event)" onchange="setCostOnChange()">
            	<!--<option value="1">pc</option>-->
			</select>
		</div>
		
		<div class="popupitem" style="width:650px;">
			<div class="popitemlabel" style="display:inline-block;width:100px;">Cost</div>
			<input name="txtcost" id="txtcost" type="text" style="width:435px;" value="" required  onKeyPress="return disableEnterKey(event)" onfocus="setCostOnFocus(<?php echo $unTICi;?>)">
		</div>
		
		<div class="popupitem" style="width:650px;">
			<div class="popitemlabel" style="display:inline-block;width:100px;">Quantity</div>
			<input name="txtquantity" id="txtquantity" type="text" style="width:435px;" value="" required  onKeyPress="return disableEnterKey(event)">
		</div>
		
		<div class="popupitem" style="width:650px;">
			<div class="popitemlabel" style="display:inline-block;width:100px;">Amount</div>
			<input name="txtamount" id="txtamount" type="text" style="width:435px;" value="" required  onKeyPress="return disableEnterKey(event)" readonly>
		</div>            

		<div class="popupitem" style="width:650px;">     
			<div class="popitemlabel" style="display:inline-block;width:100px;">Process type</div>
			<select name="cmbprocesstype" id="cmbprocesstype" style="width:440px;"  onKeyPress="return disableEnterKey(event)" >
            	<option value="0">Sales</option>
                <option value="1">Production</option>
                <option value="2">Shared Usage</option>
                <option value="3">End Product</option>                   
			</select>
		</div>
        
		<div class="popupitem" style="width:650px;">
			<div class="popitemlabel" style="display:inline-block;width:100px;"></div>
			<input type="button" id="btnadditem" value="Add to Item's Production Template" onClick="additem(hdnidproductitem.value,cmbunit.value,txtproductitemsearch.value,cmbunit.options[cmbunit.selectedIndex].innerHTML,txtcost.value,txtquantity.value,txtamount.value,
            																		cmbprocesstype.value,cmbprocesstype.options[cmbprocesstype.selectedIndex].innerHTML)">
		</div>            
	
    	<form action="productiontemplate.php?&id=<?php echo $_GET['id']; ?>&type=<?php echo $_GET['type']; ?>" method="post">                        
            <div class="popupitem">
                <div id="lvproductiondata" class="listview" style="width:650px;">
                   	<!--<input name="hdncount" id="hdncount" type="hidden" value="0" >-->
                    <div class="column" id="colproductiondata" style="height:30px;">
                       <div class="columnheader" style="width:200px;">Item</div>
                        <div class="columnheader" style="width:51px;">Unit</div>
                        <div class="columnheader" style="width:82px;text-align:right;">Cost</div>
                        <div class="columnheader" style="width:82px;text-align:right;">Qty</div>
                        <div class="columnheader" style="width:82px;text-align:right;">Amt</div>
                        <div class="columnheader" style="width:80px;text-align:right;">Process Type</div>
                    </div>
                    <div class="row" style="height:200px;" id="rowproductiondata">
                    </div>

                </div>
             </div> 
            
             <div align="right" style="width:550px;">
                    <input name="btnproductdatasave" id="btnproductdatasave" type="submit" value="Save" title="Save" class="buttons" >
                    <input name="btnproductdatacancel" id="btnproductdatacancel" type="button" value="Close" title="Close" onClick="close_clicked()" class="buttons" >
            </div>
		</form>
    
    </div>
</div>

<div id="additem" class="popup">
	<div id="addproductionitem" class="popupcontainer" style="width:500px; height:350px;">
    
    	<div id="popme" class="popuptitle" align="center" style="width:500px;">Add Item</div>
            <form method="post" action="productiontemplate.php?&id=<?php echo $_GET['id']; ?>&type=<?php echo $_GET['type']; ?>">
				<div class="popupitem">
                	<div class="listview" id="lvproductitem" style="width:500px; height:300px;">
                    	<div class="column" id="colproductitem" >
                        	<div class="columnheader" style="width:200px;">Item</div>
                            <div class="columnheader" style="width:130px;">Unit</div> 
                        </div>
                        <div class="row" style="height:500px;  height:300px; padding-bottom: 20px;" id="rowproductitem">
                        <?php
							$i=0;
							for ($a=1;$a<=$colProductItemCount-1;$a++)
							{				
								$colItem = $colAddProductItem->GetByIndex($a);
								if($colItem->unProductItem != '')
								{
						?>
                                    <div class="listviewitem" onclick="selecteditem(<?php echo $colItem->unProductItem.",".$colItem->unProductUOM.",'".$colItem->PIName."'"; ?>)" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;" id="lvitem-<?php echo $i; ?>">
                                        <div class="listviewsubitem" style="width:200px;"><?php echo $colItem->PIName;?></div>
                                        <div class="listviewsubitem" style="width:122px;"><?php echo $colItem->PUOMName;?></div>
                                    </div>
                        <?php
								$i++;
								}							
							}															
						?>    
                       		<div class="listviewitem" onclick="selecteditem()" style="background-color:#EEE;" id="lvitem-">
                            	<div class="listviewsubitem" style="width:200px;"></div>
                            	<div class="listviewsubitem" style="width:122px;"></div>
                            </div>                    	                           
                        </div>                       
                    </div>                                         
                </div>  
                
                <input name="itemid" id="itemid" type="hidden" value="" >
                <input name="itemuom" id="itemuom" type="hidden" value=""  >
				<div align="right">                
                    <input name="btnitemadd" id="btnitemadd" type="submit" value="Add" title="Add" onClick="" class="buttons" style="width:auto">
                    <input name="btnitemcancel" id="btnitemcancel" type="button" value="Close" title="Close" onClick="location.href='#'" class="buttons" >
                </div>                      
            </form>
        </div>
    </div>
</div>

<div id="editcontrol" class="popup">
	<div id="editproductioncontrol" class="popupcontainer" style="width:300px;"></div>
</div>

<?php include 'footer.php'; ?>
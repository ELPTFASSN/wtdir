<?php include 'header.php'; 

if(isset($_POST['btnitemupdate']))
{
	$query="Update productitem set unProductGroup='".$_POST['cmbproductgroup']."', unProductUOM='".$_POST['cmbproductitemuom']."', PIName='".$_POST['txtproductitem']."', PISAPCode='".$_POST['txtproductitemsap']."', PIPack='".$_POST['txtppp']."' WHERE unProductItem=".$_POST['bid'];
	ExecuteNonQuery($query);
	header('location:productitem.php?&type='.$_GET['type']);
}
if(isset($_POST['btnitemsave'])){
	$query="INSERT INTO productitem(unProductItem,unProductGroup, unProductUOM, PIName, PISAPCode, PIPack) 
			SELECT ifnull(max(unProductItem),0)+1,'".$_POST['cmbgroup']."','".$_POST['cmbuom']."','".$_POST['txtitem']."','".$_POST['txtsap']."','".$_POST['txtppp']."' FROM productitem";
	//die ('----beautiful---'.$_POST['cmbgroup']."','".$_POST['cmbuom']."','".$_POST['txtitem']."','".$_POST['txtsap']."','".$_POST['txtppp']."' FROM productitem");
	ExecuteNonQuery($query);
}
if (isset($_GET['del'])){
	$query="Update productitem set `Status`=0 where unProductItem=".$_GET['item'];
	ExecuteNonQuery($query);
	header('location:productitem.php?&type='.$_GET['type']);
}
if(isset($_POST['btnitemconversionsave']))
{
	// ----- set status of existing entries to false
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("UPDATE productconversion SET `Status`=0 WHERE unProductItem=?")){
		$stmt->bind_param("i",$_POST['bid']);
		$stmt->execute();
		$stmt->close();	
	}
	$mysqli->close();
	
	// ----- allocate/overwrite/re-use entries who's statuses were set to false
	$i=0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unProductConversion From productconversion Where unProductItem=? Order by unProductConversion")){
		$stmt->bind_param("i",$_POST['bid']);
		$stmt->execute();
		$stmt->bind_result($unProductConversion);
		while($stmt->fetch()){
			for($j=$i+1;$j<=$_POST['hdncount'];$j++){
				if(isset($_POST['hdnunit-'.$j])){
					$i=$j;
					break;
				}
			}
			$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt1 = $mysqli1->stmt_init();							    
			if($stmt1->prepare("UPDATE productconversion SET unProductItem=?,unProductUOM=?,PCRatio=?,PCSet=?,Status=1 WHERE unProductConversion=?")){
				$stmt1->bind_param("iidsi",$_POST['hdnproduct-'.$i],$_POST['hdnunit-'.$i],$_POST['txtpcratio-'.$i],$_POST['txtpcset-'.$i],$unProductConversion);
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
							  
			if($stmt->prepare("INSERT INTO productconversion(unProductItem, unProductUOM, PCRatio, PCSet,unProductConversion) VALUES (?,?,?,?,".getMax('unProductConversion','productconversion').")")){
				$stmt->bind_param("iids",$_POST['hdnproduct-'.$j],$_POST['hdnunit-'.$j],$_POST['txtpcratio-'.$j],$_POST['txtpcset-'.$j]);
				$stmt->execute();
				$stmt->close();
			}
			$mysqli->close();
		}
	}	
}
?>

<script src="js/productitem.js" type="text/javascript"></script>
<script>
function msg(targ,selObj)
{ 
	var rep;
	var url = "<?php echo $_SERVER['REQUEST_URI']; ?>";
	url = url.split('?')[1];
	var type = url.replace('type='+<?php echo $_GET['type']; ?>,'type='+selObj.options[selObj.selectedIndex].value);
	eval(targ+".location='productitem.php?"+type+"'");
}
function deleteitem(i,lvitem){ 
	if(confirm('Remove [ ' + document.getElementById('txtpcname-'+i).getAttribute('value') + ' ] Are you sure?'))
	{
		  $('#'+lvitem).remove();
	}
}
function loaditeminfo(idItem, idType, gname){
	var xmlhttp;
	if(idItem==0){
		document.getElementById('edititem').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('edititem').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';	
		}
	}
	xmlhttp.open('POST','ajax/ajax.php', true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadproductiteminfo&bid='+idItem+'&tid='+idType+'&gname='+gname);
}

function loadconversioninfo(idItem, idType, piname){
	var xmlhttp;
	if(idItem==0){
		document.getElementById('edituom').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('rowproductconversion').innerHTML=xmlhttp.responseText;
			document.getElementById('popmeuom').innerHTML = "Update [ " + piname + " ] Conversion";
			location.href='#popupedituom';
		}
	}
	xmlhttp.open('POST', 'ajax/ajax.php', true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadconversioninfo&bid='+idItem);
}

function itemrowclick(id, uom, ratio, set){
	SelectElement(id);
	document.getElementById('txtratio').value = ratio;
	document.getElementById('cmbset').value = set;
	document.getElementById('btnitemconversioncancel').title = 'Cancel';
	document.getElementById('btnitemconversioncancel').value = 'Cancel';
	document.getElementById('sid').value = '1';
}

function close_clicked()
{
	var e = document.getElementById('btnitemconversioncancel').title;	
	if (e == 'Cancel'){
		SelectElement('1');
		document.getElementById('txtratio').value = '';
		document.getElementById('cmbset').value = '';
		document.getElementById('btnitemconversioncancel').title = 'Close';
		document.getElementById('btnitemconversioncancel').value = 'Close';
		document.getElementById('sid').value = '0';		
	}
	else{ 
		location.href='#close'
	}
}
function SelectElement(valueToSelect)
{    
    var element = document.getElementById('cmbunit');
    element.value = valueToSelect;
}

$(document).ready(function() {
		var h = $('#lvproductitem').height()-$('#colproductitem').height();
       $('#rowproductitem').height(h);
		var h = $('#lvuom').height()-$('#colproductconversion').height();
       $('#rowproductconversion').height(h);
});
$(document).scroll(function(){
	columnheader('colproductitem','lvproductitem');
});
</script>

<div id="toolbar" class="wala">
	<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createitem'" style="background-image:url(img/icon/productitem.png);background-repeat:no-repeat;background-position:center;display:inline;" >
        <form action="productitem.php" method="post" style="display:inline;margin-left: 1170px;">
            <select name="producttype" onChange="msg('parent', this)" >
                <?php
                    $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt = $mysqli->stmt_init();
                        if($stmt->prepare("Select unProductType, PTName From producttype Where Status=1"))
                        {
                            $stmt->execute();
                            $stmt->bind_result($unProductType, $PTName); 
                            while($stmt->fetch())
                            {
                ?>
                                <option value="<?php echo $unProductType; ?>"
                                <?php 
                                    $type = (isset($_GET['type']))?$_GET['type']:'';
                                    echo ($type==$unProductType)?'Selected':'';
                                ?>> <?php echo $PTName; ?> </option>
                <?php
                            }
                            $stmt->close();
                        }
                ?>
            </select>
        </form>
</div>

<div class="listview" id="lvproductitem">
	<div class="column" id="colproductitem">
		<div class="columnheader" style="width:320px;">Item</div>
		<div class="columnheader" style="width:250px;">Unit</div>
        <div class="columnheader" style="width:250px;">Piece per Pack</div>		
		<div class="columnheader" style="width:150px;">SAP Code</div>
		<div class="columnheader" style="width:215px;">Action
			<!--<a name="top" href="#bottom" style="color:#333;"> Go to Bottom</a>-->
		</div>
	</div>
    
    <div class="row" id="rowproductitem">
<?php
	$i = 0;
	$mysqli2 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli2->stmt_init();
	
	if($stmt->prepare("Select unProductItem, productitem.unProductGroup, PIName, productgroup.PGName, productuom.PUOMName, PISAPCode, PIPack, productitem.Status 
							From productitem
							Inner Join productgroup 
							On productitem.unProductGroup = productgroup.unProductGroup 
							Inner Join producttype
							On productgroup.unProductType = producttype.unProductType 
							Inner Join productuom
							On productitem.unProductUOM = productuom.unProductUOM
							Where producttype.unProductType=? and productitem.Status=1 
							Order By producttype.unProductType ASC, productgroup.PGPriority ASC,PIName ASC"))
	{
		$stmt->bind_param("i", $_GET['type']);
		$stmt->execute();
		$stmt->bind_result($unProductItem, $unProductGroup, $PIName, $PGName, $PUOMName, $PISAPCode, $PIPack, $Status);
		$stmt->store_result();
		$numrows=$stmt->num_rows();
		//echo $numrows;
		while($stmt->fetch()) 
		{
			if ($OldPGName!=$PGName){
				$OldPGName=$PGName;
				echo '<div class="group" id="'.str_replace(' ','', $PGName).'" >'.$PGName.'</div>';
			}
	?>
		<div class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;" id="lvitem-<?php echo $i; ?>"> 
			<input type="hidden" name="txthidden-<?php echo $i;?>" id="txthidden-<?php echo $i;?>" value="<?php echo $unProductItem; ?>">
           
            <div class="listviewsubitem" style="text-align:left;width:320px;"><?php echo $PIName;?></div>
			<div class="listviewsubitem" style="width:250px;"><?php echo $PUOMName;?></div>
			<div class="listviewsubitem" style="width:250px;"><?php echo $PIPack;?></div>
			<div class="listviewsubitem"  style="width:150px;"><?php echo ($PISAPCode==0)?'':$PISAPCode;?></div>
            
			<div class="listviewsubitem" align="center"  style="width:215px;">
				<div title="Edit [ <?php echo $PIName;?> ]" class="button16" onclick="loaditeminfo(<?php echo $unProductItem.",".$_GET['type'].",'".str_replace(' ','', $PGName)."'";?>)" style="background-image:url(img/icon/update.png);margin:auto;"></div>
				<div title="Add Units for [ <?php echo $PIName;?> ]" class="button16" onclick="loadconversioninfo(<?php echo $unProductItem.','.$_GET['type'].','."'".$PIName."'";?>)" style="background-image:url(img/icon/uom.png);margin:auto;"></div>
				<div title="Delete [ <?php echo $PIName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $PIName;?></strong> ], Are you sure?','productitem.php?&type=' + <?php echo $_GET['type']; ?> + '&item=' + <?php echo $unProductItem;?> +'&del=1','')" style="background-image:url(img/icon/delete.png);"></div>
            </div>
		</div>
	<?php
			$i++;
		}
		$stmt->close();
	}
?>
	<div class="listviewitem" id="lvitem-end"> 
    </div>
	</div>
</div>

<div id="popupedit" class="popup">
	<div id="edititem" class="popupcontainer"></div>
</div>

<div id="popupedituom" class="popup">
	<div id="edituom" class="popupcontainer" style="width:320px;">
    
    <div id="popmeuom" class="popuptitle" align="center">Update <?php echo $_POST['piname']; ?> Conversion</div>
	<form action="productitem.php?&type=<?php echo $_GET['type']; ?>" method="post">
            <div class="popupitem">
                <div class="popupitemlabel">Unit</div>
                <select name="cmbunit" id="cmbunit" style="width:195px;">
                <?php
                        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt = $mysqli->stmt_init();
                        if($stmt = $mysqli->prepare("SELECT unProductUOM, PUOMName FROM productuom WHERE Status=1 Order by PUOMName"))
                        	{
                            $stmt->execute();
                            $stmt->bind_result($unProductUOM, $PUOMName);
							while($stmt->fetch())
								{
				?>
								<option value="<?php echo $unProductUOM; ?>"><?php echo $PUOMName; ?>
								</option>
				<?php									
								}
								$stmt->close();
							}
                ?>
                </select>
            </div>
            
            <div class="popupitem">
                <div class="popupitemlabel">Ratio</div>
                <input name="txtratio" id="txtratio" type="text" style="width:195px;" value="">
            </div>
            
            <div class="popupitem">
                <div class="popupitemlabel">Set</div>
                <Select id="cmbset" name="cmbset" style="width:195px;">
                	<option value=""><None></option>                
                	<option value="W">Whole</option>
                	<option value="F">Fraction</option>
                	<option value="X">Others</option>
                </Select>
            </div>
            
            <div class="popupitem" style="width:550px;">
				<div class="popitemlabel" style="display:inline-block;width:100px;"></div>
				<input type="button" id="btnadditem" value="Add to Item's Product Conversion" onClick="additem(bid.value,cmbunit.value,cmbunit.options[cmbunit.selectedIndex].innerHTML,txtratio.value,cmbset.options[cmbset.selectedIndex].value)">
			</div>   																					
                    
            <input type="hidden" name="tid" value="<?php echo $_GET['type']; ?>">
                        
            <div class="popupitem">
                <div id="lvuom" class="listview" style="width:320px;">
                    <div class="column" id="colproductconversion">
                        <div class="columnheader" style="width:100px">Unit</div>
                        <div class="columnheader" style="width:100px;text-align:right;">Ratio</div>
                        <div class="columnheader" style="width:50px;text-align:right;">Set</div>
                    </div>
                    <div class="row" style="height:220px;" id="rowproductconversion">
                    	
                    </div>
                </div>
       		 </div>

             <div align="center">
                <input name="btnitemconversionsave" id="btnitemconversionsave" type="submit" value="Save" title="Save" onClick="" class="buttons" >
                <input name="btnitemconversioncancel" id="btnitemconversioncancel" type="button" value="Close" title="Close" onClick="close_clicked()" class="buttons" >
            </div>
        </form>     
    
    </div>
</div>

<div id="createitem" class="popup">
	<div id="additem" class="popupcontainer">
    <?php
    	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt = $mysqli->stmt_init();
        if($stmt->prepare("Select unProductType, PTName
						   From producttype
						   Where unProductType=? And Status=1"))
        {
            $stmt->bind_param("i", $_GET['type']);
            $stmt->execute();
            $stmt->bind_result($unProductType, $PTName);
            while($stmt->fetch())
            {
            ?>            
                <div id="popme" class="popuptitle" align="center">Add <?php echo $PTName; ?> Item</div>
                <form method="post" action="productitem.php?&type=<?php echo $_GET['type']; ?>">
                    <div class="popupitem">
                        <div class="popupitemlabel">Item</div>
                        <input name="txtitem" type="text" style="width:195px;" required value="">
                    </div>
                    <div class="popupitem">
                        <div class="popupitemlabel">Group</div>
                        <select name="cmbgroup" id="cmbgroup" style="width:200px;">
                        <?php
                                $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                                $stmt = $mysqli->stmt_init();
                                if($stmt = $mysqli->prepare("SELECT unProductGroup, PGName, productgroup.Status
                                                               FROM productgroup
                                                               Inner Join producttype 
                                                               ON productgroup.unProductType = producttype.unProductType 
                                                               WHERE producttype.unProductType=? and productgroup.Status = 1 Order by PGName"))
                                    {
                                    $stmt->bind_param("i", $_GET['type']);
                                    $stmt->execute();
                                    $stmt->bind_result($unProductGroupSub, $PGName, $Status);
                                        while($stmt->fetch())
                                        {
                            ?>
                                    <option value="<?php echo $unProductGroupSub; ?>" <?php echo ($unProductGroupSub==$unProductGroupMain)?'Selected':''; ?>><?php echo $PGName; ?>
                                    </option>
                            <?php									
                                        }
                                        $stmt->close();
                                    }
                            ?>
                        </select>
                    </div>
                    <div class="popupitem">
                        <div class="popupitemlabel">Unit</div>
                        <select name="cmbuom" id="cmbuom" style="width:200px;">
                        <?php
                                $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                                $stmt = $mysqli->stmt_init();
                                if($stmt = $mysqli->prepare("SELECT unProductUOM, PUOMName FROM productuom WHERE Status=1"))
                                    {
                                    $stmt->execute();
                                    $stmt->bind_result($unProductUOMSub, $PUOMNameSub);
                                        while($stmt->fetch())
                                        {
                        ?>
                                    <option value="<?php echo $unProductUOMSub; ?>" <?php echo ($unProductUOMSub==$unProductUOMMain)?'Selected':''; ?>><?php echo $PUOMNameSub; ?>
                                    </option>
                        <?php									
                                        }
                                        $stmt->close();
                                    }
                        
                        ?>
                        </select>
                    </div>
                    <div class="popupitem">
                        <div class="popupitemlabel">SAP Code</div>
                        <input name="txtsap" type="text" style="width:195px;" value="">
                    </div>                
                    <div class="popupitem">
                        <div class="popupitemlabel">Piece per Pack</div>
                        <input name="txtppp" type="text" style="width:195px;" required value="1">
                    </div>
                    <div align="right">
                        <input name="btnitemsave" type="submit" value="Save" title="Save" onClick="" class="buttons" >
                        <input name="btnitemcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#'" class="buttons" >
                    </div>
                </form>
              
        <?php
            }
            $stmt->close();
        }
		?>
    </div>
</div>


<?php include 'footer.php'; ?>
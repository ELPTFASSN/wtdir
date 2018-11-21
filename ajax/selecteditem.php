<?php
	include '../include/var.inc.php';
	include '../include/savecollection.php';
	//
	//	NOT 
	//		IN
	//			USE
	//
	//
	//
	//
	$count = $_POST['currentcount'];
	$unProductTemplate = $_POST['ptid'];
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unProductItem,PIName, producttype.unProductType, producttype.PTName 
						From productitem 
						Inner Join productgroup
						On productgroup.unProductGroup = productitem.unProductGroup 
						Inner Join producttype
						On producttype.unProductType = productgroup.unProductType
						Where productitem.`Status`=1
						And unProductItem=?"))
	{
		$stmt->bind_param('i',$_POST['piid']);
		$stmt->execute();
		$stmt->bind_result($unProductItem,$PIName,$unProductType,$PTName);
		while($stmt->fetch())
		{
?>

<div class="row" id="rowproducttemplate">
	<div class="listviewitem maintitem" style="background-color:#<?php echo ($count%2)?'EEE':'FFF'; ?>;" id="lvitem-<?php echo $count; ?>">
		<input type="hidden" id="mhpitem-<?php echo $count; ?>" value="<?php echo $unProductItem;?>">				
        <input type="hidden" id="mhpitemtemplate-<?php echo $count; ?>" value="<?php echo $count; ?>">
        <input type="hidden" id="mhpitemtype-<?php echo $count; ?>" value="<?php echo $unProductType; ?>">
        <div class="listviewsubitem">
            <label id='mpitemname-<?php echo $count; ?>'  class='producttemplatesubitem' style='text-align:left;text-indent: 10px;'><?php echo $PIName; ?></label> 
        </div>
    
        <div class="listviewsubitem">
            <input type='text' value='0.00' onKeyPress="return isNumberKey(event, 0)" onBlur="lostfocus('textprice-<?php echo $count; ?>','<?php echo $count; ?>')" id='textprice-<?php echo $count; ?>' class='producttemplatesubitem'>
        </div>
                    
        <div class="listviewsubitem">
            <input type='text' value='0.00' onKeyPress="return isNumberKey(event, 0)" onBlur="lostfocus('textcost-<?php echo $count."',"; ?>')" id='textcost-<?php echo $count; ?>' class='producttemplatesubitem'>
        </div>
                    
        <div class="listviewsubitem">
            <input type='text' value='0' onKeyPress="return isNumberKey(event, 1)" id='textpriority-<?php echo $count; ?>' onBlur="checkpriority('textpriority-', '<?php echo $count.','.$unProductType;?>')" class='producttemplatesubitem'>
        </div>    
	</div>
</div>

<?php
	
		}
		$stmt->close();
	}
?>
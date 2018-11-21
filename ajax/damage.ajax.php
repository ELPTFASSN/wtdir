<?php 
include '../include/var.inc.php';
include '../include/class.inc.php';
session_start();

switch($_POST['qid']){
	case 'LoadDamageData':
		$i = 1;
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select idDamageData,damagedata.unProductItem,PIName,DDQuantity,damagedata.unProductUOM,
							(Select PUOMName From productuom Where unProductUOM = damagedata.unProductUOM) as `PUOMName`
							From damagedata
							Inner Join productitem on damagedata.unProductItem = productitem.unProductItem
							Where unDamageControl = ? and damagedata.`Status` = 1 Order by PIName Asc")){
			$stmt->bind_param('i',$_POST['idDC']);
			$stmt->execute();
			$stmt->bind_result($unDamageData,$unProductItem,$PIName,$DDQuantity,$unProductUOM,$PUOMName);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" style="border-bottom:#EEE thin solid;" id="<?php echo 'lv-'.$i;?>">
					<input type="hidden" name="<?php echo 'hdn-'.$i.'-iddamagedata'; ?>" value="<?php echo $unDamageData; ?>">
                    <div class="listviewsubitem">
                    	<input readonly type="text" value="<?php echo $PIName; ?>" id="<?php echo 'txt-'.$i.'-product'; ?>" style="border:none; width:502px; background-color:transparent; margin-left:2px;"> 
                        <input type="hidden" value="<?php echo $unProductItem; ?>" name="<?php echo 'hdn-'.$i.'-product'; ?>" id="<?php echo 'hdn-'.$i.'-product'; ?>" >
					</div>
					<div class="listviewsubitem">
                    	<input readonly type="text" value="<?php echo $DDQuantity; ?>" name="<?php echo 'txt-'.$i.'-qty'; ?>" id="<?php echo 'txt-'.$i.'-qty'; ?>" style="border:none; width:60px; background-color:transparent; text-align:center;" >
                    </div>
					
                    <div class="listviewsubitem">
                    	<input readonly type="text" value="<?php echo $PUOMName; ?>" id="<?php echo 'txt-'.$i.'-unit'; ?>" style="border:none; width:72px; background-color:transparent; text-align:center;"> 
                        <input type="hidden" value="<?php echo $unProductUOM; ?>" name="<?php echo 'hdn-'.$i.'-unit'; ?>" id="<?php echo 'hdn-'.$i.'-unit'; ?>">
                    </div>
					
                    <div class="listviewsubitem" style="min-width:20px;">
	                    <input type="button" title="Edit" onClick="editdamagedata(<?php echo $unProductItem; ?>,<?php echo $DDQuantity; ?>,<?php echo $i; ?>,<?php echo $unProductUOM;?>)" style="margin-left:5px; margin-top:6px; width:16px; height:16px; border:none; background-image:url(img/icon/edit.png); background-repeat:no-repeat; cursor:pointer; background-color:transparent;">
                    </div>
					
                    <div class="listviewsubitem">
						<input type="button" title="Remove" onClick="removeelement('<?php echo 'lv-'.$i; ?>')" style="margin-left:5px; margin-top:6px; width:16px; height:16px; padding:0px; border:none; background-image:url(img/icon/delete.png); background-repeat:no-repeat; cursor:pointer; background-color:transparent;">
                    </div>
					
				</div>
				<?php
				$i++;
			}
			$stmt->close();
		}
		break;
		
	case 'EditDamageData';
		?>
        <div class="popuptitle" align="center">Edit Data</div>
        <hr>
        <div class="listview">
            <div class="column">
                <div class="columnheader" style="width:250px;">Description</div>
                <div class="columnheader" style="width:60px; text-align:center;">Qty</div>
                <div class="columnheader" style="width:66px; text-align:center;">Unit</div>
            </div>
            <div class="row">
            	<div class="listviewitem">
                    <input type="hidden" value="<?php echo $_POST['icount']; ?>" id="icount" >
                    <div class="listviewsubitem">
                        <select id="editcmbproduct" style="width:254px; height:22px; margin-top:2px;" onChange="loaduom(editcmbproduct.value,'editcmbunit')" disabled>
                            <?php 
                            $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                            $stmt = $mysqli->stmt_init();
                            if($stmt->prepare("Select unProductItem,PIName From productitem
                                                Where productitem.`Status` = 1 Order by PIName Asc")){
                                $stmt->execute();
                                $stmt->bind_result($unProductItem,$PIName);
                                while($stmt->fetch()){
                                    ?>
                                    <option value="<?php echo $unProductItem; ?>" <?php echo ($unProductItem==$_POST['idPI'])?'Selected':'';?>><?php echo $PIName; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="listviewsubitem">
                    	<input onKeyPress="return disableEnterKey(event)" id="edittxtqty" type="text" style="width:56px; text-align:center;" value="<?php echo $_POST['qty']; ?>">
                    </div>
                    
                    <div class="listviewsubitem">
                        <select id="editcmbunit" style="width:60px; height:22px; margin-top:2px;" disabled></select>
                    </div>
                </div>
            </div>
    	</div>
        <?php
        break;

	case 'ViewDamageData':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select PIName,PUOMName,DDQuantity From damagedata
							Inner Join productitem on damagedata.unProductItem = productitem.unProductItem
							Inner Join productuom on damagedata.unProductUOM = productuom.unProductUOM
							Where unDamageControl = ?")){
			$stmt->bind_param('i',$_POST['idDC']);
			$stmt->execute();
			$stmt->bind_result($PIName,$PUOMName,$DDQuantity);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" style="padding-top:2px; padding-bottom:2px;">
					<div class="listviewsubitem" style="width:245px;"><?php echo $PIName; ?></div>
					<div class="listviewsubitem" style="width:51px; text-align:right;"><?php echo $DDQuantity; ?></div>
					<div class="listviewsubitem" style="width:51px; text-align:center;"><?php echo $PUOMName; ?></div>	
				</div>
				<?php
			}
		}
		break;
}
?>
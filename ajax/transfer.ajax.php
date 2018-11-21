<?php 
include '../include/var.inc.php';
include '../include/class.inc.php';
session_start();

switch($_POST['qid']){
	case 'SearchItem':
		$i=0;
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();	
		if($stmt->prepare("Select unProductItem,PIName From productitem  
							Where productitem.`Status`=1 and PIName Like ? 
							Order by PIName limit 10")){
			$likestring='%'.$_POST['search'].'%';
			$stmt->bind_param('s',$likestring);
			$stmt->execute();
			$stmt->bind_result($unProductItem,$PIName);
			while($stmt->fetch()){
			?>
				<div class="listboxitem" id="SearchItem-<?php echo $i; ?>" onClick="selectresult('<?php echo $PIName; ?>',<?php echo $unProductItem; ?>)" style="cursor:pointer;">
					<?php echo $PIName; ?>
				</div>
			<?php
				$i++;
			}
			$stmt->close();
		}
		break;

	case 'LoadTransferData':
		$i = 1;
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select idTransferData,transferdata.unProductItem,PIName,TDQuantity,transferdata.unProductUOM,
							(Select PUOMName From productuom Where unProductUOM = transferdata.unProductUOM) as `PUOMName`
							From transferdata
							Inner Join productitem on transferdata.unProductItem = productitem.unProductItem
							Where unTransferControl = ? and transferdata.`Status` = 1 Order by PIName Asc")){
			$stmt->bind_param('i',$_POST['idTC']);
			$stmt->execute();
			$stmt->bind_result($unDeliveryData,$unProductItem,$PIName,$DDQuantity,$unProductUOM,$PUOMName);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" style="border-bottom:#EEE thin solid;" id="<?php echo 'lv-'.$i;?>">
					<input type="hidden" name="<?php echo 'hdn-'.$i.'-idtransferdata'; ?>" value="<?php echo $unDeliveryData; ?>">
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
	                    <input type="button" title="Edit" onClick="edittransferdata(<?php echo $unProductItem; ?>,<?php echo $DDQuantity; ?>,<?php echo $i; ?>,<?php echo $unProductUOM;?>)" style="margin-left:5px; margin-top:6px; width:16px; height:16px; border:none; background-image:url(img/icon/edit.png); background-repeat:no-repeat; cursor:pointer; background-color:transparent;">
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
		
	case 'EditTransferData':
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
												Inner Join productgroup on productitem.unProductGroup = productgroup.unProductGroup
												Where productitem.`Status` = 1 Order by unProductType Asc, PIName Asc")){
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
	
	case 'CheckHasTemplate':
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select Count(idTemplateItemData) From templateitemdata
							Where unTemplateItemControl = (Select unTemplateItemControl From branch Where unBranch = ?)")){
			$stmt->bind_param('i',$_POST['bid']);
			$stmt->execute();
			$stmt->bind_result($iCount);
			$stmt->fetch();
			echo $iCount;
			$stmt->close();
		}
		break;
		
	case 'FetchITFControl':
		$Filler = '';
		$WhsCode = '';
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select ASAPUsr,ASAPpwd,ASAPDataSource From area Where `Status` = 1 and unArea = ?")){
			$stmt->bind_param('i',$_SESSION['area']);
			$stmt->execute();
			$stmt->bind_result($usr,$pwd,$datasource);
			$stmt->fetch();
			$stmt->close();
		}
		
		$conn = odbc_connect($datasource,$usr,$pwd);
		if($conn){
		$tsql = "Select Top 1 DocNum,OWTR.DocDate,Filler,Comments,OWTR.DocEntry,WhsCode From OWTR
					Inner Join WTR1 on OWTR.DocEntry = WTR1.DocEntry
					Where DocNum = '".$_POST['DocNum']."'";
			$stmt = odbc_exec($conn, $tsql);
			if($stmt == false){
				echo "Error";
			}
			while($row = odbc_fetch_array($stmt)){
				$Filler = $row['Filler'];
				$WhsCode = $row['WhsCode'];
				echo $row['DocNum'].'@';
				echo $row['DocDate'].'@';
				echo $Filler.'@';
				echo $row['Comments'].'@';
				echo $WhsCode.'@';
				echo $row['DocEntry'].'@';
			}
		}else{
				echo "Connection could not be established.\n";
				die( odbc_errormsg());
				odbc_close( $conn);
		}
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unBranch From branch Where BSAPCode=?")){
			$stmt->bind_param('s',$Filler);
			$stmt->execute();
			$stmt->bind_result($unBranchFrom);
			$stmt->fetch();
			echo $unBranchFrom.'@';
			$stmt->close();
		}
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select unBranch From branch Where BSAPCode=?")){
			$stmt->bind_param('s',$WhsCode);
			$stmt->execute();
			$stmt->bind_result($unBranchTo);
			$stmt->fetch();
			echo $unBranchTo.'@';
			$stmt->close();
		}
		break;
		
	case 'FetchITFData':
		function GetUOM($sapuom){
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("Select count(unProductUOM),ifNull(unProductUOM,0) as `unUOM`,ifNull(PUOMName,'Not Found') as `UOMName` From productuom Where unProductUOM = (Select ifNull(unProductUOM,0) From sapuom Where SUName = ?)")){
				$stmt->bind_param('s',$sapuom);
				$stmt->execute();
				$stmt->bind_result($unCount,$unProductUOM,$PUOMName);
				$stmt->fetch();
				$stmt->close();
				$ret = $unProductUOM.'@'.$PUOMName;
				return $ret;
			}
		}
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Select ASAPUsr,ASAPpwd,ASAPDataSource From area Where `Status` = 1 and unArea = ?")){
			$stmt->bind_param('i',$_SESSION['area']);
			$stmt->execute();
			$stmt->bind_result($usr,$pwd,$datasource);
			$stmt->fetch();
			$stmt->close();
		}
		
		$colItem = new Collection;
		$conn = odbc_connect($datasource,$usr,$pwd);
		if( $conn ){
			$tsql = "Select ItemCode,Dscription,Quantity,unitMsr From WTR1 where DocEntry = '".$_POST['DocEntry']."' Order by Dscription Asc, LineNum Asc";
			$stmt = odbc_exec( $conn, $tsql);
			if( $stmt == false ){
				 echo "Error";
			}
		
			while($row = odbc_fetch_array($stmt) ){
				$oSAPItem = new SAPProductItem($row['ItemCode'],$row['Dscription'],$row['Quantity'],$row['unitMsr']);
				$colItem->Add($oSAPItem,$oSAPItem->ItemCode);
			}
		}else{
			echo "Error";
			odbc_close( $conn);
		}
		
		$iFlag = 0;
        for($i=1;$i<=$colItem->Count() - 1;$i++){
			$clsSAPItem = $colItem->GetByIndex($i);
			$unProductItem = ExecuteReader("Select ifNull(unProductItem,0) as `result` From productitem Where PISAPCode='".$clsSAPItem->ItemCode."'",$server,$username,$password,$database);
			$iFlag = ($unProductItem==0)?$iFlag+1:$iFlag;
			?>
            <div class="listviewitem" style="border-bottom:thin solid #EEE;">
                <div class="listviewsubitem" style="width:500px;">
                	<input onKeyPress="return disableEnterKey(event)" readonly type="text" id="<?php echo 'txt-'.$i.'-product';?>" value="<?php echo $clsSAPItem->Description; echo ($unProductItem==0)?' - [Item not found]':'';?>" style="border:none; width:496px; background-color:transparent; color:#<?php echo ($unProductItem==0)?'F00':'000'; ?>;">
                   	<input type="hidden" name="<?php echo 'hdn-'.$i.'-product';?>" id="<?php echo 'hdn-'.$i.'-product';?>" value="<?php echo $unProductItem;?>">
                </div>
				<div class="listviewsubitem" style="width:60px;">
                	<input readonly autocomplete="off" onClick="$(this).select(); $(this).focus();" onKeyPress="return disableEnterKey(event)" onChange="(<?php echo $clsSAPItem->Quantity; ?> != $(this).val())?$(this).css('color','#F00'):$(this).css('color','#000');" class="clstxtQty" type="text" id="<?php echo 'txt-'.$i.'-qty';?>" name="<?php echo 'txt-'.$i.'-qty';?>" value="<?php echo number_format((float)$clsSAPItem->Quantity, 2, '.', ''); ?>" style="border:none; width:56px; background-color:transparent; text-align:right;">
                </div>
                <div class="listviewsubitem" style="width:80px;">
                	<?php 
						$retUom = GetUOM($clsSAPItem->Unit);
						list($un,$uom) = explode('@',$retUom);
					?>
                	<input onKeyPress="return disableEnterKey(event)" readonly type="text" value="<?php echo ($un==0)?$clsSAPItem->Unit:$uom; ?>" style="border:none; width:76px; background-color:transparent; text-align:center; color:#<?php echo ($un==0)?'F00':'000';?>;">
                    <input type="hidden" name="<?php echo 'hdn-'.$i.'-unit'; ?>" value="<?php echo $un;?>">
                </div>
            </div>
			<?php
			$iFlag = ($un==0)?$iFlag+1:$iFlag;
		}
		?>
        	<input type="hidden" id="hdnFlagTemp" value="<?php echo $iFlag; ?>">
		<?php
		break;
}
?>
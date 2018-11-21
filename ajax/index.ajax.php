<?php
session_start();

switch($_POST['qid']){
	case 'loadbranch':
		$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();

		if($_POST['typ']=='inventory'){
			$query="Select inventorycontrol.unBranch,BName 
								From inventorycontrol Inner Join branch on inventorycontrol.unBranch=branch.unBranch 
								Where inventorycontrol.`Status`=1 and branch.`status`=1 and unArea=? 
								Group By BName 
								Order by BName";
		}elseif($_POST['typ']=='delivery'){
			$query="Select deliverycontrol.unBranchTo,BName 
								From deliverycontrol Inner Join branch on deliverycontrol.unBranchTo=branch.unBranch 
								Where deliverycontrol.`Status`=1 and branch.`status`=1 and deliverycontrol.unArea=? 
								Group By BName 
								Order by BName";
		}elseif($_POST['typ']=='damage'){
			$query="Select damagecontrol.unBranchTo,BName 
								From damagecontrol Inner Join branch on damagecontrol.unBranchTo=branch.unBranch 
								Where damagecontrol.`Status`=1 and branch.`status`=1 and damagecontrol.unArea=? 
								Group By BName 
								Order by BName";
		}elseif($_POST['typ']=='transfer'){
			$query="Select BranchId,BranchName
								From
								(
								 Select BName as BranchName,unBranchFrom as BranchId from transfercontrol inner join branch on transfercontrol.unBranchFrom=branch.unBranch Where transfercontrol.unArea=?
								 Union
								 Select BName as BranchName,unBranchTo as BranchId from transfercontrol inner join branch on transfercontrol.unBranchTo=branch.unBranch
								) tablesource
								Order By BranchName";
		}
		
		if($stmt->prepare($query)){
			$stmt->bind_param('i',$_POST['aid']);
			$stmt->execute();
			$stmt->bind_result($unBranch,$BName);
			while($stmt->fetch()){
				?>
				<div class="listviewitem" onClick="loadperiod('<?php echo $_POST['typ']; ?>',<?php echo $unBranch; ?>,'<?php echo $BName; ?>')" style="cursor:pointer">
					<div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;"><?php echo $BName; ?></div>
				</div>
				<?php
				}
			$stmt->close();
		}
		break;
	
	case 'loadperiod':
		?>
			<div class="listviewitem" onClick="loadbranch('<?php echo $_POST['typ']; ?>',<?php echo $_SESSION['area']; ?>)" style="cursor:pointer">
				<div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;">..</div>
			</div>
		<?php	
		$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();

		if($_POST['typ']=='inventory'){
			$query="Select concat(MonthName(ICDate),' ',Year(ICDate)) as `ICPeriod`, Year(ICDate) as `ICYear`, MonthName(ICDate) as `ICMonth` 
					From inventorycontrol 
					Where Status=1 and unBranch=? 
					Group By ICPeriod 
					Order By Year(ICDate) Desc, Month(ICDate)";
		}elseif($_POST['typ']=='delivery'){
			$query="Select concat(MonthName(DCDate),' ',Year(DCDate)) as `DCPeriod`, Year(DCDate) as `DCYear`, MonthName(DCDate) as `DCMonth` 
					From deliverycontrol 
					Where Status=1 and unBranchTo=? 
					Group By DCPeriod 
					Order By Year(DCDate) Desc, Month(DCDate)";
		}elseif($_POST['typ']=='damage'){
			$query="Select concat(MonthName(DCDate),' ',Year(DCDate)) as `DCPeriod`, Year(DCDate) as `DCYear`, MonthName(DCDate) as `DCMonth` 
					From damagecontrol 
					Where Status=1 and unBranchTo=? 
					Group By DCPeriod 
					Order By Year(DCDate) Desc, Month(DCDate)";
		}elseif($_POST['typ']=='transfer'){
			$query="Select CPeriod,CYear,CMonth
					From
					(
					 Select concat(MonthName(TCDate), ' ', Year(TCDate)) as CPeriod, Year(TCDate) as CYear, MonthName(TCDate) as CMonth, Month(TCDate) as CMon, unBranchFrom as bFrom
						from transfercontrol
					 Union
					 Select concat(MonthName(TCDate), ' ', Year(TCDate)) as CPeriod, Year(TCDate) as CYear, MonthName(TCDate) as CMonth, Month(TCDate) as CMon, unBranchTo as bTo
						from transfercontrol
					) tablesource 
					Where bFrom = ?
					Order By CYear Desc,CMon";
		}

		if($stmt->prepare($query)){
			$stmt->bind_param('i',$_POST['bid']);
			$stmt->execute();
			$stmt->bind_result($CPeriod,$CYear,$CMonth);
			while($stmt->fetch()){
                if($_POST['typ']=='inventory'){
                	$link="loadinventory('".$_POST['typ']."',".$_POST['bid'].",'".$CYear."','".$CMonth."','".$CPeriod."','".$_POST['brn']."')";
                }elseif($_POST['typ']=='delivery'){
					$link="loaddelivery('".$_POST['typ']."',".$_POST['bid'].",'".$CYear."','".$CMonth."')";
				}elseif($_POST['typ']=='damage'){
					$link="loaddamage('".$_POST['typ']."',".$_POST['bid'].",'".$CYear."','".$CMonth."')";
				}elseif($_POST['typ']=='transfer'){
					$link="loadtransfer('".$_POST['typ']."',".$_POST['bid'].",'".$CYear."','".$CMonth."')";
				}
				?>
				<div class="listviewitem" onClick="<?php echo $link; ?>" style="cursor:pointer">
					<div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;"><?php echo $CPeriod; ?></div>
				</div>
				<?php
				}
			$stmt->close();
		}
		break;	
	
	case 'loadinventory':
			?>
				<div class="listviewitem" onClick="loadperiod('inventory',<?php echo $_POST['bid']; ?>,'<?php echo $_POST['brn'] ?>')" style="cursor:pointer">
					<div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;">..</div>
				</div>
            <?php	
			$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt=$mysqli->stmt_init();
			if($stmt->prepare("Select unInventoryControl,ICNumber,ICInventoryNumber,ICRemarks,concat(MonthName(ICDate) , ' ' , Day(ICDate) , ', ' ,Year(ICDate)) as `ICPeriod`,ICLock 
								From inventorycontrol 
								Where Status=1 and unBranch=? and Year(ICDate)=? and MonthName(ICDate)=? 
								Order By ICInventoryNumber Desc")){
			$stmt->bind_param('iss',$_POST['bid'],$_POST['yr'],$_POST['mon']);
			$stmt->execute();
			$stmt->bind_result($unInventoryControl,$ICNumber,$ICInventoryNumber,$ICRemarks,$ICPeriod,$ICLock);
			while($stmt->fetch()){
				?>
		        <div class="listviewitem" onClick="openinventory('inventory',<?php echo $_POST['bid']; ?>,<?php echo $unInventoryControl; ?>)" style="cursor:pointer;">
                    <div class="listviewsubitem" style="width:160px;" id="icinventorynumber"><img src="img/icon/<?php echo ($ICLock==1)? 'lockedblack.png':'nocheck.png'; ?>" width="16" height="16" style="padding-right:10px;"><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;"><?php echo substr('000000'.$ICInventoryNumber,-6); ?></div>                    
                    <div class="listviewsubitem" style="width:120px;" id="icnumber"><?php echo $ICNumber; ?></div>
                    <div class="listviewsubitem" style="width:180px;" id="icremarks"><?php echo ($ICRemarks==0)? '':$ICRemarks; ?></div>                    
                    <div class="listviewsubitem" style="width:120px;" id="icperiod"><?php echo $ICPeriod; ?></div>                    
				</div>
		
				<?php
				}
			$stmt->close();
			}
		break;	

	case 'loaddelivery':
			?>
				<div class="listviewitem" onClick="loadperiod('delivery',<?php echo $_POST['bid']; ?>)" style="cursor:pointer">
					<div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;">..</div>
				</div>
            <?php	
			$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt=$mysqli->stmt_init();
			if($stmt->prepare("Select unDeliveryControl,concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCPeriod`,DCDocNum,ICNumber 
								From deliverycontrol 
								Left Join inventorycontrol on deliverycontrol.unInventoryControl=inventorycontrol.unInventoryControl 
								Where deliverycontrol.`Status`=1 and unBranchTo=? and Year(DCDate)=? and MonthName(DCDate)=? 
								Order By DCDate Desc")){
			$stmt->bind_param('iss',$_POST['bid'],$_POST['yr'],$_POST['mon']);
			$stmt->execute();
			$stmt->bind_result($unDeliveryControl,$DCPeriod,$DCDocNum,$ICNumber);
			while($stmt->fetch()){
				?>
                <div class="listviewitem" onClick="opendelivery(<?php echo $unDeliveryControl; ?>)" style="cursor:pointer;">
                    <div class="listviewsubitem" style="width:140px;"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;"><?php echo $DCDocNum; ?></div>
                    <div class="listviewsubitem" style="width:140px;"><?php echo substr('000000'.$ICNumber,-6); ?></div>
                    <div class="listviewsubitem" style="width:140px;"><?php echo $DCPeriod; ?></div>
                </div>
				<?php
				}
			$stmt->close();
			}
		break;	
		
		case 'loaddamage':
			?>
				<div class="listviewitem" onClick="loadperiod('damage',<?php echo $_POST['bid']; ?>)" style="cursor:pointer">
					<div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;">..</div>
				</div>
            <?php	
			$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt=$mysqli->stmt_init();
			if($stmt->prepare("Select unDamageControl,concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCPeriod`,DCDocNum,ICNumber 
								From damagecontrol 
								Left Join inventorycontrol on damagecontrol.unInventoryControl=inventorycontrol.unInventoryControl 
								Where damagecontrol.`Status`=1 and unBranchTo=? and Year(DCDate)=? and MonthName(DCDate)=? 
								Order By DCDate Desc")){
			$stmt->bind_param('iss',$_POST['bid'],$_POST['yr'],$_POST['mon']);
			$stmt->execute();
			$stmt->bind_result($unDamageControl,$DCPeriod,$DCDocNum,$ICNumber);
			while($stmt->fetch()){
				?>
                <div class="listviewitem" onClick="opendamage(<?php echo $unDamageControl; ?>)" style="cursor:pointer;">
                    <div class="listviewsubitem" style="width:140px;"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;"><?php echo $DCDocNum; ?></div>
                    <div class="listviewsubitem" style="width:140px;"><?php echo substr('000000'.$ICNumber,-6); ?></div>
                    <div class="listviewsubitem" style="width:140px;"><?php echo $DCPeriod; ?></div>
                </div>
				<?php
				}
			$stmt->close();
			}
		break;	
	
	
	case 'loadtransfer':
		?>	
			<div class="listviewitem" onClick="loadperiod('transfer',<?php echo $_POST['bid']; ?>)" style="cursor:pointer">
				<div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;">..</div>
			</div>
		<?php
		$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Select unTransferControl,concat(MonthName(TCDate) , ' ' , Day(TCDate) , ', ' ,Year(TCDate)) as `TCPeriod`,TCNumber,branchfrom.BName,branchto.BName 
							From transfercontrol 
							Left Join branch as branchfrom on transfercontrol.unBranchFrom=branchfrom.unBranch 
							Left Join branch as branchto on transfercontrol.unBranchTo=branchto.unBranch 
							Where transfercontrol.`Status`=1 and (unBranchFrom=? or unBranchTo=?) And Year(TCDate)=? And MonthName(TCDate)=? 
							Order By Year(TCDate) Desc, Month(TCDate) Asc, TCDate Desc")){
			$stmt->bind_param('iiss',$_POST['bid'],$_POST['bid'],$_POST['yr'],$_POST['mon']);
			$stmt->execute();
			$stmt->bind_result($unTransferControl,$TCPeriod,$TCNumber,$BranchFrom,$BranchTo);
			while($stmt->fetch()){
		?>
			<div class="listviewitem" onClick="openitf(<?php echo $unTransferControl; ?>)" style="cursor:pointer;">
				<div class="listviewsubitem" style="width:140px;" id="tcnumber"><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;"><?php echo $TCNumber; ?></div>
				<div class="listviewsubitem" style="width:140px;" id="idbranchfrom" ><?php echo $BranchFrom; ?></div>                    
				<div class="listviewsubitem" style="width:140px;" id="idbranchto"><?php echo $BranchTo; ?></div>                    
				<div class="listviewsubitem" style="width:140px;" id="tcperiod"><?php echo $TCPeriod; ?></div>                    
			</div>
		<?php
			}
		$stmt->close();
		}
	break;
	
	case 'loadsalesday':
		$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt=$mysqli->stmt_init();
			if($stmt->prepare("Select unSalesControl,SCTimeStart From salescontrol where unBranch=? and SCState='Open'")){
				$stmt->bind_param('i',$_POST['bid']);
				$stmt->execute();
				$stmt->bind_result($unSalesControl,$SCTimeStart);
				while($stmt->fetch()){
					$unSalesControl1=sprintf('%06d', $unSalesControl);
				?>
					<option value="<?php echo $unSalesControl; ?>"><?php echo $unSalesControl1." - ".date('Y-m-d',strtotime($SCTimeStart)); ?></option>
                <?php
				}
				$stmt->close();
			}
	break;
	
	case 'loadquota':
		$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt=$mysqli->stmt_init();
			$result=array();
			if($stmt->prepare("Select BQuota,BQuotaInterval,BQuotaPointAmount From branch where unBranch=?")){
				$stmt->bind_param('i',$_POST['bid']);
				$stmt->execute();
				$stmt->bind_result($BQuota,$BQuotaInterval,$BQuotaPointAmount);
				$stmt->fetch();
				$result['BQuota']=$BQuota;
				$result['BQuotaInterval']=$BQuotaInterval;
				$result['BQuotaPointAmount']=$BQuotaPointAmount;
				$stmt->close();
			}
			echo json_encode($result);
	break;



}
?>
<?php
session_start();
switch($_POST['qid']){
case 'searchcard':
	$i=0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();	
	if($stmt->prepare("Select unCustomerCard,customercard.unCustomer,CCNumber,CLastName,CFirstName,CMiddleName,ifnull(CAlias,''),DTCode 
						from customercard 
						inner join customer on customercard.unCustomer=customer.unCustomer 
						inner join discounttype on customercard.unDiscountType = discounttype.unDiscountType
						where customercard.`status`=1 and CCNumber like ? and customercard.unDiscountType = ? 
						Order by CCNumber")){
		$likestring='%'.$_POST['query'].'%';
		$stmt->bind_param('si',$likestring,$_POST['type']);
		$stmt->execute();
		$stmt->bind_result($unCustomerCard,$unCustomer,$CCNumber,$CLastName,$CFirstName,$CMiddleName,$CAlias,$DTCode);
		while($stmt->fetch()){
		?>
			<div class="listboxitem" id="lvresult-<?php echo $i; ?>" onClick="addcustomer(<?php echo $unCustomerCard; ?>,<?php echo $unCustomer; ?>,$('#cmbtype').val(),'<?php echo $CCNumber; ?>','<?php echo $CLastName; ?>','<?php echo $CFirstName; ?>','<?php echo $CMiddleName; ?>','<?php echo $CAlias; ?>')" style="cursor:pointer;">
                <?php echo '<strong>'.$CCNumber.'</strong> [ '.strtoupper($CLastName).', '.$CFirstName.' '.substr($CMiddleName,0,1).'. '.$CAlias.' ] - '.$DTCode ; ?>
            </div>
        <?php
			$i++;
		}
		$stmt->close();
	}
	break;

case 'searchlastname':
	$i=0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();	
	if($stmt->prepare("Select unCustomer,CLastName,CFirstName,CMiddleName,CAlias From customer where `status`=1 and CLastName like ? Order by CLastName")){
		$likestring='%'.$_POST['query'].'%';
		$stmt->bind_param('s',$likestring);
		$stmt->execute();
		$stmt->bind_result($unCustomer,$CLastName,$CFirstName,$CMiddleName,$CAlias);
		while($stmt->fetch()){
		?>
            <div class="listboxitem" id="lvresult-<?php echo $i; ?>" onClick="addcustomer(0,<?php echo $unCustomer; ?>,$('#cmbtype').val(),$('#txtsearchcard').val(),'<?php echo $CLastName; ?>','<?php echo $CFirstName; ?>','<?php echo $CMiddleName; ?>','<?php echo $CAlias; ?>')" style="cursor:pointer;">
                <?php echo strtoupper($CLastName).', '.$CFirstName.' '.substr($CMiddleName,0,1).'. '.$CAlias; ?>
            </div>
        <?php
			$i++;
		}
		$stmt->close();
	}
	break;

case 'searchitem':
	$i=0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();	
	if($stmt->prepare("Select templateitemdata.unProductItem,PIName,TIDPrice from branch 
						inner join templateitemcontrol on templateitemcontrol.unTemplateItemControl=branch.unTemplateItemControl 
						inner join templateitemdata on templateitemcontrol.unTemplateItemControl=templateitemdata.unTemplateItemControl 
						inner join productitem on templateitemdata.unProductItem=productitem.unProductItem 
						inner join productgroup on productitem.unProductGroup=productgroup.unProductGroup 
						where unProductType=(Select unProductType from producttype where PTName='Products') and templateitemdata.`status`=1 and unBranch=? and PIName like ? 
						Order by PIName limit 10")){
		$likestring='%'.$_POST['query'].'%';
		$stmt->bind_param('is',$_POST['bid'],$likestring);
		$stmt->execute();
		$stmt->bind_result($unProductItem,$PIName,$TIDPrice);
		while($stmt->fetch()){
		?>
            <div class="listboxitem" id="lvresult-<?php echo $i; ?>" onClick="selectresult(<?php echo $unProductItem ; ?>,'<?php echo $PIName ; ?>',<?php echo $TIDPrice ; ?>)" style="cursor:pointer;">
                <?php echo $PIName.' @ '.$TIDPrice; ?>
            </div>
        <?php
			$i++;
		}
		$stmt->close();
	}
	break;

}

?>

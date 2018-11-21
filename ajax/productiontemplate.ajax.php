<?php
session_start();
switch($_POST['qid']){
case 'searchitem':
	$i=0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	if(isset($_POST['currproddata'])){	
		if($stmt->prepare("Select unProductItem,PIName 
						   From productitem 
						   Inner Join productgroup 
						   On productitem.unProductGroup=productgroup.unProductGroup 
						   Inner Join producttype
						   On producttype.unProductType = productgroup.unProductType
						   Where  productitem.`Status`=1 
						   And PIName Like ? 
						   And PIName NOT IN (".$_POST['currproddata'].")
						   Order By PIName Limit 10"
						   /*
						   productgroup.unProductType = ? 
						   And
						   Select unProductItem,PIName 
						   From productitem 
						   Inner Join productgroup 
						   On productitem.unProductGroup=productgroup.unProductGroup 
						   Inner Join producttype
						   On producttype.unProductType = productgroup.unProductType
						   Where PTName Like '%rawmats%' 
						   And productitem.`Status`=1 
						   And PIName Like ? 
						   Order By PIName Limit 10
						   */)){ 
			$likestring='%'.$_POST['query'].'%';
			$notlikestring= '('.$_POST['currproddata'].')';
			//$stmt->bind_param('is',$_POST['ptype'],$likestring);
			$stmt->bind_param('s',$likestring);
			$stmt->execute();
			$stmt->bind_result($unProductItem,$PIName);
			while($stmt->fetch()){
			?>
				<div class="listboxitem" id="lstitemresult-<?php echo $i; ?>" onClick="selectresult('<?php echo $PIName; ?>',<?php echo $unProductItem; ?>)" style="cursor:pointer;">
					<?php echo $PIName; ?>
				</div>
			<?php
				$i++;
			}
			$stmt->close();
		}
	}else{
		if($stmt->prepare("Select unProductItem,PIName 
						   From productitem 
						   Inner Join productgroup 
						   On productitem.unProductGroup=productgroup.unProductGroup 
						   Inner Join producttype
						   On producttype.unProductType = productgroup.unProductType
						   Where productitem.`Status`=1 
						   And PIName Like ? 
						   Order By PIName Limit 10"
						   /*
						    productgroup.unProductType = ? 
						   And
						   Select unProductItem,PIName 
						   From productitem 
						   Inner Join productgroup 
						   On productitem.unProductGroup=productgroup.unProductGroup 
						   Inner Join producttype
						   On producttype.unProductType = productgroup.unProductType
						   Where PTName Like '%rawmats%' 
						   And productitem.`Status`=1 
						   And PIName Like ? 
						   Order By PIName Limit 10
						   */)){ 
			$likestring='%'.$_POST['query'].'%';
			//$stmt->bind_param('is',$_POST['ptype'],$likestring);
			$stmt->bind_param('s',$likestring);
			$stmt->execute();
			$stmt->bind_result($unProductItem,$PIName);
			while($stmt->fetch()){
			?>
				<div class="listboxitem" id="lstitemresult-<?php echo $i; ?>" onClick="selectresult('<?php echo $PIName; ?>',<?php echo $unProductItem; ?>)" style="cursor:pointer;">
					<?php echo $PIName; ?>
				</div>
			<?php
				$i++;
			}
			$stmt->close();
		}
	}
break;

case 'loaduom':
	$i=0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();	
	if($stmt->prepare("Select productconversion.unProductUOM,PUOMName 
						from productconversion 
						Inner Join productuom on productconversion.unProductUOM=productuom.unProductUOM 
						Where unProductItem=? AND productconversion.Status=1")){
		$stmt->bind_param('i',$_POST['pid']);
		$stmt->execute();
		$stmt->bind_result($unProductUOM,$PUOMName);
		while($stmt->fetch()){
		?>
        	<option value="<?php echo $unProductUOM; ?>"><?php echo $PUOMName; ?></option>
        <?php
			$i++;
		}
		$stmt->close();
	}

break;

}
?>


<?php include 'class.inc.php';
	session_start();	
		$price = $_POST['postPrice'];
		$cost = $_POST['postCost'];
		$priority = $_POST['postPriority'];
		$unProductItem = $_POST['postidProductItem'];
		$unTemplateItemData = $_POST['postidTemplateItemData'];
		$colIndex = $_POST['postcolIndex'];
		
		$colMainProductItem = $_SESSION['colMainProductItem'];
		
		$colMainItem = $colMainProductItem->GetByIndex($colIndex);
		
		$colSideProductItemTransfer = $_SESSION['colSideProductItemPageLoad'];
		
	
			for($i=1;$i<=$colSideProductItemTransfer->Count()-1;$i++)
			{
				$colSideItemTransfer = $colSideProductItemTransfer->GetByIndex($i);
				if($colSideItemTransfer->idProductItem == $colMainItem->unProductItem) // ---------- hindi makita ang colMainItem kay 0 or no value siya
				{
					$colSideItemTransfer->PIPrice = $price;
					$colSideItemTransfer->PICost = $cost;
					$colSideItemTransfer->PIPriority = $priority;
					$colSideItemTransfer->unTemplateItemData = $unTemplateItemData;
				}
				elseif($colSideItemTransfer->unProductItem == '0' || $colMainItem->unProductItem == '')
				{
					$colSideItemTransfer->PIPrice = $price;
					$colSideItemTransfer->PICost = $cost;
					$colSideItemTransfer->PIPriority = $priority;
					$colSideItemTransfer->unTemplateItemData = $unTemplateItemData;
				}
			}
		
		$_SESSION['colSideItemsAfterTransfer'] = $colSideProductItemTransfer;
		$_SESSION['colMainProductItemAfterUpdate'] = $colMainProductItem;
		
?>
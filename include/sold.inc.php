<?php
	include 'var.inc.php';
	include 'class.inc.php';
	
	session_start();
	if ($_SESSION['Session'] == '') {header("location:../end.php");}
	
	if(isset($_POST['btnSaveMapping'])){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Call MAPSales(?,?,?)")){
			$stmt->bind_param('iii',$_POST['hdnunIC'],$_POST['hdnunSD'],$_POST['hdnunBID']);
			$stmt->execute();
			$stmt->close();
		}
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
	
	/* MAPSales BACKUP
	DROP PROCEDURE `MAPSales`//
CREATE DEFINER=`root`@`%` PROCEDURE `MAPSales`(
IN puninventorycontrol int(11),
IN punSalesData INT(11)
)
BEGIN
UPDATE invoicecontrol Set unInventoryControl = puninventorycontrol Where unSalesData = punSalesData and `Status` = 1;
UPDATE invoicedata Set unInventoryControl = puninventorycontrol Where unSalesData = punSalesData and `Status` = 1;
UPDATE salesdata Set unInventoryControl = puninventorycontrol Where unSalesData = punSalesData and `Status` = 1;

Update inventorydata Set IDSoldQuantity = 0 Where (IDSoldQuantity <> 0 And unInventoryControl = puninventorycontrol);

UPDATE inventorydata as Dest,
(
Select idata.unProductItem AS unItem,Sum(idata.IDQuantity) As QTY from invoicedata as idata Where (idata.unInventoryControl = punInventoryControl) and (idata.IDState = 'Paid') and (idata.`Status` = 1) Group by idata.unProductItem
) as Src
Set Dest.IDSoldQuantity = Src.QTY Where (Dest.unProductItem = Src.unItem) and (Dest.unInventoryControl = puninventorycontrol);


Update inventorydata Set IDProcessIn = (IDStart + IDTransfer - IDDamage - IDSoldQuantity - IDEndWhole) * -1,IDSoldAmount  = (IDSoldQuantity * IDCharge)
Where unInventoryControl = puninventorycontrol;

-- UPDATE inventorydata Set IDProcessIn = IDStart +  IDTransfer - IDDamage - IDEndTotal - IDSoldQuantity Where unInventoryControl = punInventoryControl;
-- 
Update inventorydata Set IDProcessOut = 0 Where (IDProcessOut <> 0 And unInventoryControl = puninventorycontrol);
Update inventorydata AS Dest,
(Select TPD.unProductItem as IItem,conv.unProductUOM as unProductUOM, Sum(conv.PCRatio * TPD.TPDQuantity * TP.IDProcessIn) as POut  from templateproductiondata TPD
Inner Join (Select TPC.unTemplateProductionControl as unTemplateProductionControl,ID.IDProcessIn as IDProcessIn From inventorydata ID Inner Join templateproductioncontrol TPC On ID.unProductItem = TPC.unProductItem Where ID.IDProcessIn <> 0 And ID.unInventoryControl = puninventorycontrol) TP
on TPD.unTemplateProductionControl = TP.unTemplateProductionControl 
Inner Join productconversion conv
on (conv.unProductItem = TPD.unProductItem and conv.unProductUOM = TPD.unProductUOM)
Where TPD.TPDProcessType = 1 And conv.`Status` = 1 
Group by TPD.unProductItem,conv.unProductUOM) AS Src
Set Dest.IDProcessOut = Dest.IDProcessOut + Src.POut Where Dest.unProductItem = Src.IItem And Dest.unInventoryControl = punInventoryControl;

-- SOLD Proccess
Update inventorydata AS Dest,
(Select TPD.unProductItem as IItem,conv.unProductUOM as unProductUOM, Sum(conv.PCRatio * TPD.TPDQuantity * TP.IDSoldQuantity) as POut  from templateproductiondata TPD
Inner Join (Select TPC.unTemplateProductionControl as unTemplateProductionControl,ID.IDSoldQuantity as IDSoldQuantity From inventorydata ID Inner Join templateproductioncontrol TPC On ID.unProductItem = TPC.unProductItem Where ID.IDSoldQuantity <> 0 And ID.unInventoryControl = punInventoryControl) TP
on TPD.unTemplateProductionControl = TP.unTemplateProductionControl
Inner Join productconversion conv
on (conv.unProductItem = TPD.unProductItem and conv.unProductUOM = TPD.unProductUOM)
Where TPD.TPDProcessType = 0 And conv.`Status` = 1 
Group by TPD.unProductItem,conv.unProductUOM) AS Src
Set Dest.IDProcessOut = Dest.IDProcessOut + Src.POut Where Dest.unProductItem = Src.IItem And Dest.unInventoryControl = punInventoryControl;

UPDATE inventorydata Set IDDIRUsage = IDStart +  IDDelivery + IDTransfer - IDDamage - IDEndTotal, IDVarianceQTY =  (IDStart +  IDDelivery + IDTransfer - IDDamage - IDEndTotal) - IDProcessOut, IDVarianceAmount = ((IDStart +  IDDelivery + IDTransfer - IDDamage - IDEndTotal) - IDProcessOut) * IDCharge Where unInventoryControl = puninventorycontrol;


END

	*/
?>
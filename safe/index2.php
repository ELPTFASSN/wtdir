<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Rice 'n More - Menu</title>

<link rel="stylesheet" type="text/css" href="css/index2.css">
<script src="js/jquery1.5.2.min.js"></script>
<script src="js/jquery-ui.min.js"></script>

<script type="text/javascript" >
$(document).ready(function(){
	$("#userbutton ul ").css({display: "none"}); // Opera Fix
	$("#userbutton").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
</script>
</head>

<div id="titlebar">
	Rice N' More Inc. Sales and Inventory System
</div>
<div id="maintab">
	
	<div id="homebutton">
    	Home
	</div>
    <div id="areabutton">
    	Iloilo
	</div>
    <div id="userbutton">
    	
        <ul>
        	<a href="#"><li style="text-align:left;">Profile</li></a>
        	<a href="#"><li style="text-align:left;">Settings</li></a>
        	<a href="#"><li style="text-align:left;">Send Feed Back</li></a>
        	<a href="end.php"><li style="text-align:left;">Sign Out</li></a>
        </ul>HERIDA, Raff Richie B.
	</div>
    <div id="maintab1">
    </div>
</div>

<div id="dashboardcontainer">
</div>

 <div id="dashboardcontainerleft">
        <div id="dashboardcontainerlefttitle">Inventory Panel</div>
        <div id="dashboardcontainerleftcontent">
			<?php
                //if(ExecuteReader("Select AGName as `result` from accountgroup where idAccountGroup=".$oAccountUser->idAccountGroup)=='Administrator'){
            ?>
                <div class="splashcontentpanelbutton" title="Manage Item and Rawmat masterlist" onClick="redirect('productitem.php?&type=1')"><img src="img/icon/productitem.png" style="padding-right:10px;">Item</div>
                <div class="splashcontentpanelbutton" title="Manage Template - A customized list of products which can be applied to Branches/Outlets" onClick="redirect('templateitemcontrol.php')"><img src="img/icon/producttemplate.png" style="padding-right:10px;">Template</div>
                <div class="splashcontentpanelbutton" title="Manage Production" onClick="redirect('templateproductionbatch.php')"><img src="img/icon/production.png" style="padding-right:10px;">Production</div>
                <div class="splashcontentpanelbutton" title="Manage User masterlist (These are the people who access this system)" onClick="redirect('accountuser.php')"><img src="img/icon/user.png" style="padding-right:10px;">User</div>
                <div class="splashcontentpanelbutton" title="Manage Area - A cluster of Branches that belong to a certain geographic entity" onClick="redirect('area.php')"><img src="img/icon/employeearea.png" style="padding-right:10px;">Area</div>
                <div class="splashcontentpanelbutton" title="Manage Unit of Measure conversion value and SAP code" onClick="redirect('uom.php')"><img src="img/icon/uom.png" style="padding-right:10px;">Unit of Measure</div>
            <?php
                //}
            ?>
		</div>
	</div>
    
 <div id="dashboardcontainerright">
        <div id="dashboardcontainerrighttitle">Tools</div>
        <div id="dashboardcontainerrightcontent">
			<?php
                //if(ExecuteReader("Select AGName as `result` from accountgroup where idAccountGroup=".$oAccountUser->idAccountGroup)=='Administrator'){
            ?>
                <div class="splashcontentmenu" title="Create a new Employee (such as Service Crews, Cashiers and Managers)" onClick="redirect('employee.php')"><img src="img/icon/employee.png" width="16" height="16" style="padding-right:10px;">Employee</div>
                <div class="splashcontentmenu" title="Create a new Branch/Outlet" onClick="redirect('branch.php')"><img src="img/icon/branch.png" width="16" height="16" style="padding-right:10px;">Branch</div>
                <div class="splashcontentmenu" title="Create a new Device" onClick="redirect('device.php')"><img src="img/icon/device.png" width="16" height="16" style="padding-right:10px;">Device</div>
            <?php
                //}
            ?>
		</div>
	</div>
    
<div id="dashboardcontainercenter">
    	<div id="splash">
        	<div id="splashtitle"></div>
            <div id="splashcontent">
            	
                <div id="splashcontentleft">
                    <h3>Open a recent Item</h3>
					<?php 
                        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("SELECT idAccountDocument,ADSource,ADType FROM accountdocument WHERE `Status`=1 and idAccountUser=? Order By TimeStamp Desc Limit 9")){
						$stmt->bind_param('i',$oAccountUser->idAccountUser);
                        $stmt->execute();
                        $stmt->bind_result($idAccountDocument,$ADSource,$ADType);
                        while($stmt->fetch()){

							$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
							$stmt1=$mysqli1->stmt_init();
							switch($ADType){
								Case 1:
									$squery="Select branch.BName,concat(' [ ',MonthName(ICDate) , ' ' , Day(ICDate) , ', ' ,Year(ICDate),' - ',ICNumber,' ]') as `DocumentName`,branch.idBranch,idInventoryControl,inventorycontrol.`TimeStamp`,ICInventoryNumber From inventorycontrol Inner Join branch on inventorycontrol.idBranch=branch.idBranch Where inventorycontrol.`Status`=1 and branch.idArea=? and idInventoryControl=?";
									break;
								Case 2:
									$squery="Select TCNumber,concat(' [ ',MonthName(TCDate) , ' ' , Day(TCDate) , ', ' ,Year(TCDate),' - ',TCNumber,' ]') as `DocumentName`,idTransferControl,idTransferControl,`TimeStamp`,1 From transfercontrol Where transfercontrol.`Status`=1 and idArea=? and idTransferControl=?";
									break;
								Case 3:
									$squery="Select branch.BName,concat(' [ ',MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate),' - ',DCDocNum,' ]') as `DocumentName`,idBranchTo,idDeliveryControl,deliverycontrol.`TimeStamp`,1 From deliverycontrol Inner Join branch on deliverycontrol.idBranchTo=branch.idBranch Where deliverycontrol.`Status`=1 and deliverycontrol.idArea=? and idDeliveryControl=?";
									break;
								Case 4:
									$squery="Select branch.BName,concat(' [ ',MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate),' - ',DCReference,' ]') as `DocumentName`,discountcontrol.idBranch,idDiscountControl,discountcontrol.`TimeStamp`,1 From discountcontrol Inner Join branch on discountcontrol.idBranch=branch.idBranch Where discountcontrol.`Status`=1 and discountcontrol.idArea=? and idDiscountControl=?";
									break;
								Case 5:
									$squery="Select branch.BName,concat(' [ ',MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate),' - ',DCDocNum,' ]') as `DocumentName`,damagecontrol.idBranchFrom,idDamageControl,damagecontrol.`TimeStamp`,1 From damagecontrol Inner Join branch on damagecontrol.idBranchFrom=branch.idBranch Where damagecontrol.`Status`=1 and damagecontrol.idArea=? and idDamageControl=?";
									break;
								Case 6:
									$squery="Select branch.BName,concat(' [ ',MonthName(ICDate) , ' ' , Day(ICDate) , ', ' ,Year(ICDate),' - ',ICNumber,' ]') as `DocumentName`,invoicecontrol.idBranch,idInvoiceControl,invoicecontrol.`TimeStamp`,1 From invoicecontrol Inner Join branch on invoicecontrol.idBranch=branch.idBranch Where invoicecontrol.`Status`=1 and invoicecontrol.idArea=? and idInvoiceControl=?";
									break;
							}
							if($stmt1->prepare($squery)){
							$stmt1->bind_param('ii',$_SESSION['area'],$ADSource);
							$stmt1->execute();
							$stmt1->bind_result($BName,$DocumentName,$idBranch,$idInventoryControl,$TimeStamp,$Reference);
							while($stmt1->fetch()){
								switch($ADType){
									Case 1:
									?>
										<div class="splashcontentrecent" onClick="openinventory('inventory',<?php echo $idBranch; ?>,<?php echo $idInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;"><?php echo 'INV '.$BName.' - '.substr('000000'.$Reference,-6).' '.$DocumentName; ?></div>
									<?php
										break;
									Case 2:
									?>
										<div class="splashcontentrecent" onClick="openitf(<?php echo $idInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;"><?php echo 'ITF '.$DocumentName; ?></div>
									<?php
										break;
									case 3:
									?>
                                    	<div class="splashcontentrecent" onClick="opendelivery(<?php echo $idInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;"><?php echo 'DR '.$DocumentName; ?></div>
                                    <?php
										break;
									case 4:
									?>
                                    	<div class="splashcontentrecent" onClick="opendiscount(<?php echo $idInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/discount.png" width="16" height="16" style="padding-right:10px;"><?php echo 'DSC '.$BName.' '.$DocumentName; ?></div>
                                    <?php
										break;
									case 5:
									?>
                                    	<div class="splashcontentrecent" onClick="opendamage(<?php echo $idInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/damagereturn.png" width="16" height="16" style="padding-right:10px;"><?php echo 'DMG '.$BName.' '.$DocumentName; ?></div>
                                    <?php
										break;
									case 6:
									?>
                                    	<div class="splashcontentrecent" onClick="openinvoice(<?php echo $idInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/invoice.png" width="16" height="16" style="padding-right:10px;"><?php echo 'INV '.$BName.' '.$DocumentName; ?></div>
                                    <?php
										break;
								}
							}
							$stmt1->close();
							}
                          }
                        $stmt->close();
                        }
                    ?>
                    <div class="splashcontentrecent" onClick="location.href='#showopenitem'" title="Open"><img src="img/icon/open.png" width="16" height="16" style="padding-right:10px;">Open...</div>
                </div>                
              <div id="splashcontentright">
              		<div id="splashcontentrighta">
                        <h3>Create New</h3>
                        <div class="splashcontentmenu" title="Create a new Inventory Sheet" onClick="location.href='#createinventorysheet'"><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;">Inventory Sheet</div>
                        <div class="splashcontentmenu" title="Create a new Delivery Form" onClick="redirect('createdelivery.php')"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;">Delivery</div>
                        <div class="splashcontentmenu" title="Create a new ITF" onClick="redirect('createtransfer.php')"><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;">Interbranch Transfer Form</div>
                        <div class="splashcontentmenu" title="Create a new Damage/Return Form" onClick="redirect('createdamage.php')"><img src="img/icon/damagereturn.png" width="16" height="16" style="padding-right:10px;">Damage Return</div>
                        <div class="splashcontentmenu" title="Create a new Invoice" onClick="location.href='#createinvoice'"><img src="img/icon/invoice.png" width="16" height="16" style="padding-right:10px;">Invoice</div>
                        <!--<div class="splashcontentmenu" title="Create a new Discount" onClick="redirect('creatediscount.php')"><img src="img/icon/discount.png" width="16" height="16" style="padding-right:10px;">Discount</div>
                        <div class="splashcontentmenu" title="Create a new Credit Card" onClick="redirect('createdamage.php')"><img src="img/icon/creditcard.png" width="16" height="16" style="padding-right:10px;">Credit Card</div>
                        <div class="splashcontentmenu" title="Create a new Gift Certificate" onClick="redirect('createdamage.php')"><img src="img/icon/giftcertificate.png" width="16" height="16" style="padding-right:10px;">Gift Certificate</div>
                        <div class="splashcontentmenu" title="Create a new Letter of Authorization" onClick="redirect('createdamage.php')"><img src="img/icon/letterofauthorization.png" width="16" height="16" style="padding-right:10px;">Letter of Authorization</div>-->
                        <div class="splashcontentmenu" title="Create a new Petty Cash Form" onClick="redirect('createpettycash.php')"><img src="img/icon/pettycash.png" width="16" height="16" style="padding-right:10px;">Petty Cash</div>
                    </div>
              		<!--<div id="splashcontentrightb">
                        <h3>Options</h3>
                        <div class="splashcontentmenu" title="Create a new Employee (such as Service Crews, Cashiers and Managers)" onClick="redirect('employee.php')"><img src="img/icon/employee.png" width="16" height="16" style="padding-right:10px;">Employee</div>
                        <div class="splashcontentmenu" title="Create a new Branch/Outlet" onClick="redirect('branch.php')"><img src="img/icon/branch.png" width="16" height="16" style="padding-right:10px;">Branch</div>
                        <div class="splashcontentmenu" title="Create a new Device" onClick="redirect('device.php')"><img src="img/icon/device.png" width="16" height="16" style="padding-right:10px;">Device</div>
                    </div>-->
              </div>
                
            </div>
            <div id="splashfooter">
            	<div id="splashfootercheckbox" onClick="chktoggle('chknewtab')">
	            	<input name="chknewtab" type="checkbox" id="chknewtab" value="newtab" checked="Checked">Open links in new tab
                </div>
                <div id="splashfooterip">Last Login at <?php echo ExecuteReader("Select AHIP as `result` From accounthistory where `status`=0 and idAccountUser=".$oAccountUser->idAccountUser." order by `TimeStamp` desc limit 1"); ?></div>
            </div>
        </div>
    </div>


<body>
</body>
</html>
<?php include 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="css/index.css">
<script src="js/index.js"></script>
<script type="text/javascript">
function opencontainer(type){
	$('.showopencontainer').css('display','none');
	if (type=='inventorysheet'){
		$('#containerinventorysheet').css('display','block');
		$('#showopentitle').html('INVENTORY SHEET');
	}else if(type=='delivery'){
		$('#containerdelivery').css('display','block');
		$('#showopentitle').html('DELIVERY');
	}else if (type=='itf'){
		$('#containeritf').css('display','block');
		$('#showopentitle').html('INTERBRANCH TRANSFER FORM');
	}else if (type=='discount'){
		$('#containerdiscount').css('display','block');
		$('#showopentitle').html('DISCOUNT');
	}else if (type=='invoice'){
		$('#containerinvoice').css('display','block');
		$('#showopentitle').html('INVOICE');
	}else if(type=='damage'){
		$('#containerdamage').css('display','block');
		$('#showopentitle').html('DAMAGE RETURN');
	}else if(type=='template'){
		$('#containertemplate').css('display','block');
		$('#showopentitle').html('TEMPLATE');
	}else if(type=='production'){
		$('#containerproduction').css('display','block');
		$('#showopentitle').html('PRODUCTION');		
	}else if(type=='pettycash'){
		$('#containerpettycash').css('display','block');
		$('#showopentitle').html('PETTY CASH');		
	}else if(type=='report'){
		$('#showopentitle').html('REPORT');
	}
}

function openitf(idITF){
	markdocument(<?php echo $oAccountUser->idAccountUser; ?>,2,idITF);
	redirect('updatetransfer.php?&idTC='+idITF);
}

function opendelivery(iddelivery){
	markdocument(<?php echo $oAccountUser->idAccountUser; ?>,3,iddelivery);
	redirect('updatedelivery.php?&idDC='+iddelivery);
}

function opendiscount(iddiscount){
	markdocument(<?php echo $oAccountUser->idAccountUser; ?>,4,iddiscount);
	redirect('updatediscount.php?&id='+iddiscount);
}

function opendamage(iddamage){
	markdocument(<?php echo $oAccountUser->idAccountUser; ?>,5,iddamage);
	redirect('updatedamage.php?&idDC='+iddamage);
}

function openinvoice(idinvoice){
	markdocument(<?php echo $oAccountUser->idAccountUser; ?>,6,idinvoice);
	redirect('invoice.php?&id='+idinvoice);
}

</script>

<div id="dashboardcontainer">

	<div id="dashboardcontainerleft">
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
              		<div id="splashcontentrightb">
                        <h3>Options</h3>
                        <div class="splashcontentmenu" title="Create a new Employee (such as Service Crews, Cashiers and Managers)" onClick="redirect('employee.php')"><img src="img/icon/employee.png" width="16" height="16" style="padding-right:10px;">Employee</div>
                        <div class="splashcontentmenu" title="Create a new Branch/Outlet" onClick="redirect('branch.php')"><img src="img/icon/branch.png" width="16" height="16" style="padding-right:10px;">Branch</div>
                        <div class="splashcontentmenu" title="Create a new Device" onClick="redirect('device.php')"><img src="img/icon/device.png" width="16" height="16" style="padding-right:10px;">Device</div>
                    </div>
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

	<div id="dashboardcontainerright">
        <div id="dashboardcontainerrighttitle">Inventory Panel</div>
        <div id="dashboardcontainerrightcontent">
			<?php
                if(ExecuteReader("Select AGName as `result` from accountgroup where idAccountGroup=".$oAccountUser->idAccountGroup)=='Administrator'){
            ?>
                <div class="splashcontentpanelbutton" title="Manage Item and Rawmat masterlist" onClick="redirect('productitem.php?&type=1')"><img src="img/icon/productitem.png" style="padding-right:10px;">Item</div>
                <div class="splashcontentpanelbutton" title="Manage Template - A customized list of products which can be applied to Branches/Outlets" onClick="redirect('templateitemcontrol.php')"><img src="img/icon/producttemplate.png" style="padding-right:10px;">Template</div>
                <div class="splashcontentpanelbutton" title="Manage Production" onClick="redirect('templateproductionbatch.php')"><img src="img/icon/production.png" style="padding-right:10px;">Production</div>
                <div class="splashcontentpanelbutton" title="Manage User masterlist (These are the people who access this system)" onClick="redirect('accountuser.php')"><img src="img/icon/user.png" style="padding-right:10px;">User</div>
                <div class="splashcontentpanelbutton" title="Manage Area - A cluster of Branches that belong to a certain geographic entity" onClick="redirect('area.php')"><img src="img/icon/employeearea.png" style="padding-right:10px;">Area</div>
                <div class="splashcontentpanelbutton" title="Manage Unit of Measure conversion value and SAP code" onClick="redirect('uom.php')"><img src="img/icon/uom.png" style="padding-right:10px;">Unit of Measure</div>
            <?php
                }
            ?>
		</div>
	</div>

</div>

<div id="showopenitem" class="showopen">
    <div id="showopenform">
    	<div id="showopenheader">
        	<div id="showopentitleicon"></div>
	    	<div id="showopentitle" style="color:#FFF;">INVENTORY SHEET</div>
        </div>
	    <div id="showopenpanel">
        	 <div class="showopenpanelbutton" onClick="opencontainer('inventorysheet')"><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;">Inventory Sheet</div>
        	 <div class="showopenpanelbutton" onClick="opencontainer('delivery')"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;">Delivery</div>
        	 <div class="showopenpanelbutton" onClick="opencontainer('itf')"><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;">Interbranch Transfer Form</div>
        	 <div class="showopenpanelbutton" onClick="opencontainer('damage')"><img src="img/icon/damagereturn.png" width="16" height="16" style="padding-right:10px;">Damage Return</div>
        	 <div class="showopenpanelbutton" onClick="opencontainer('invoice')"><img src="img/icon/invoice.png" width="16" height="16" style="padding-right:10px;">Invoice</div>
        	 <!--<div class="showopenpanelbutton" onClick="opencontainer('discount')"><img src="img/icon/discount.png" width="16" height="16" style="padding-right:10px;">Discount</div>
        	 <div class="showopenpanelbutton" onClick="opencontainer('creditcard')"><img src="img/icon/creditcard.png" width="16" height="16" style="padding-right:10px;">Credit Card</div>
        	 <div class="showopenpanelbutton" onClick="opencontainer('giftcertificate')"><img src="img/icon/giftcertificate.png" width="16" height="16" style="padding-right:10px;">Gift Certificate</div>
        	 <div class="showopenpanelbutton" onClick="opencontainer('letterofauthorization')"><img src="img/icon/letterofauthorization.png" width="16" height="16" style="padding-right:10px;">Letter of Authorization</div>-->
        	 <div class="showopenpanelbutton" onClick="opencontainer('pettycash')"><img src="img/icon/pettycash.png" width="16" height="16" style="padding-right:10px;">Petty Cash</div>
        	 <div class="showopenpanelbutton" onClick="opencontainer('report')"><img src="img/icon/report.png" width="16" height="16" style="padding-right:10px;">Report</div>
			<?php
                if(ExecuteReader("Select AGName as `result` from accountgroup where idAccountGroup=".$oAccountUser->idAccountGroup)=='Administrator'){
            ?>
                     <div class="showopenpanelbutton" onClick="opencontainer('template')"><img src="img/icon/producttemplate.png" width="16" height="16" style="padding-right:10px;">Template</div>
                     <div class="showopenpanelbutton" onClick="opencontainer('production')"><img src="img/icon/production.png" width="16" height="16" style="padding-right:10px;">Production</div>
            <?php
                }
            ?>
        </div>
        
        <!-- Inventory Sheet -->
        <div id="containerinventorysheet" class="showopencontainer">                     
            <div class="showopencontaineritem">
            	<div class="addressbar" id="barinventory">
                	<div class="addressbarhead"><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;"></div>
                	<div class="addressbarbutton">Branch</div>
                    <div class="divider"></div>
                </div>
                <div class="listview" id="lvinventory" style="position:absolute;top:25px;width:620px;height:365px;">            
                    <div class="column" id="colinventory">
	                    <div class="columnheader" style="width:160px;">Inventory Branch</div>
                    </div>
                    <div class="row" id="rowinventory">
					<?php 
                        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("Select inventorycontrol.idBranch,BName 
                                            From inventorycontrol Inner Join branch on inventorycontrol.idBranch=branch.idBranch 
                                            Where inventorycontrol.`Status`=1 and branch.`status`=1 and idArea=? 
                                            Group By BName 
                                            Order by BName")){
                        $stmt->bind_param('i',$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($idBranch,$BName);
                        while($stmt->fetch()){
                    ?>
                            <div class="listviewitem" onClick="loadperiod('inventory',<?php echo $idBranch; ?>,'<?php echo $BName; ?>')" style="cursor:pointer">
                                <div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;"><?php echo $BName; ?></div>
                            </div>
                    <?php
                            }
                        $stmt->close();
                        }
                    ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery -->
        <div id="containerdelivery" class="showopencontainer">
            <div class="showopencontaineritem">
                <div class="listview" id="lvdelivery" style="position:absolute;top:0px;width:620px;height:390px;">
                    <div class="column" id="coldelivery">
	                    <div class="columnheader" style="width:160px;">Delivery Branch</div>
                    </div>
                    <div class="row" id="rowdelivery" style="cursor:pointer;">
						<?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("Select deliverycontrol.idBranchTo,BName 
											From deliverycontrol Inner Join branch on deliverycontrol.idBranchTo=branch.idBranch 
											Where deliverycontrol.`Status`=1 and branch.`status`=1 and deliverycontrol.idArea=? 
											Group By BName 
											Order by BName")){
                            $stmt->bind_param('i',$_SESSION['area']);
                            $stmt->execute();
                            $stmt->bind_result($idBranch,$BName);
                            while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="loadperiod('delivery',<?php echo $idBranch; ?>)" style="cursor:pointer">
                                <div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;"><?php echo $BName; ?></div>
                            </div>
                        <?php
                            }
                        $stmt->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

		<!-- Transfer -->
        <div id="containeritf" class="showopencontainer">
            <div class="showopencontaineritem">
                <div class="listview" id="lvtransfer" style="position:absolute;top:0px;width:620px;height:390px;">
                    <div class="column" id="coltransfer">
	                    <div class="columnheader" style="width:160px;">Transfer Branch</div>
                    </div>
                    <div class="row" id="rowtransfer">
                    <?php
                    $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysqli->stmt_init();
                    if($stmt->prepare("Select BranchId,BranchName
										From
										(
										 Select BName as BranchName,idBranchFrom as BranchId from transfercontrol inner join branch on transfercontrol.idBranchFrom=branch.idBranch Where transfercontrol.idArea=?
										 Union
										 Select BName as BranchName,idBranchTo as BranchId from transfercontrol inner join branch on transfercontrol.idBranchTo=branch.idBranch
										) tablesource
										Order By BranchName")){
	                    $stmt->bind_param('i',$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($idBranch,$BName);
                        while($stmt->fetch()){
                    ?>
                            <div class="listviewitem" onClick="loadperiod('transfer',<?php echo $idBranch; ?>)" style="cursor:pointer">
                                <div class="listviewsubitem" style="width:160px;"><img src="img/icon/dir.png" width="16" height="16" style="padding-right:10px;"><?php echo $BName; ?></div>
                            </div>
                    <?php
                        }
                    $stmt->close();
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
        
		<!-- Discount -->
        <div id="containerdiscount" class="showopencontainer">
            <div class="showopencontaineritem">
                <div class="listview" id="divitf" style="position:absolute;top:0px;width:620px;height:390px;">
                    <div class="column" id="coldiscount">
                        <div class="columnheader" style="width:120px;">Reference</div>
                        <div class="columnheader" style="width:120px;">Sheet Number</div>
                        <div class="columnheader" style="width:100px;">Branch</div>
                        <div class="columnheader" style="width:100px;text-align:right;">Discount</div>
                        <div class="columnheader" style="width:120px;">Date</div>
                    </div>
                    <div class="row" id="rowdiscount">
						<?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("Select idDiscountControl,concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCPeriod`,DCReference,BName,ICNumber,DCDiscount From discountcontrol 
                                            Inner Join branch on discountcontrol.idBranch=branch.idBranch 
											Left Join inventorycontrol on discountcontrol.idInventoryControl=inventorycontrol.idInventoryControl 
											Where discountcontrol.`Status`=1 and discountcontrol.idArea=? Order By DCDate")){
                            $stmt->bind_param('i',$_SESSION['area']);
                            $stmt->execute();
                            $stmt->bind_result($idDiscountControl,$DCPeriod,$DCReference,$BName,$ICNumber,$DCDiscount);
                            while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="opendiscount(<?php echo $idDiscountControl; ?>)" style="cursor:pointer;">
                                <div class="listviewsubitem" style="width:120px;"><img src="img/icon/discount.png" width="16" height="16" style="padding-right:10px;"><?php echo $DCReference; ?></div>
                                <div class="listviewsubitem" style="width:120px;"><?php echo substr('000000'.$ICNumber,-6); ?></div>                    
                                <div class="listviewsubitem" style="width:100px;"><?php echo $BName; ?></div>                    
                                <div class="listviewsubitem" style="width:100px;text-align:right;"><?php echo $DCDiscount; ?></div>                    
                                <div class="listviewsubitem" style="width:120px;"><?php echo $DCPeriod ?></div>                    
                            </div>
                        <?php
                            }
                        $stmt->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

		<!-- Invoice -->
        <div id="containerinvoice" class="showopencontainer">
            <div class="showopencontaineritem">
                <div class="listview" id="divitf" style="position:absolute;top:0px;width:620px;height:390px;">
                    <div class="column" id="colinvoice">
                        <div class="columnheader" style="width:120px;">Invoice Number</div>
                        <div class="columnheader" style="width:120px;text-align:right;">Sheet Number</div>
                        <div class="columnheader" style="width:100px;text-align:right;">Total Sales</div>
                        <div class="columnheader" style="width:100px;">Branch</div>
                        <div class="columnheader" style="width:120px;">Date</div>
                    </div>
                    <div class="row" id="rowinvoice">
						<?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("SELECT idInvoiceControl,ICNumber,idInventoryControl,ICTotalSales,BName,ICDate 
											FROM invoicecontrol Inner Join branch on invoicecontrol.idBranch = branch.idBranch 
											WHERE invoicecontrol.idArea=? and invoicecontrol.`Status`=1 Order By ICDate")){
                            $stmt->bind_param('i',$_SESSION['area']);
                            $stmt->execute();
                            $stmt->bind_result($idInvoiceControl,$ICNumber,$idInventoryControl,$ICTotalSales,$BName,$ICDate);
                            while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="openinvoice(<?php echo $idInvoiceControl; ?>)" style="cursor:pointer;">
                                <div class="listviewsubitem" style="width:120px;"><img src="img/icon/invoice.png" width="16" height="16" style="padding-right:10px;"><?php echo substr('000000'.$ICNumber,-6); ?></div>
                                <div class="listviewsubitem" style="width:120px;text-align:right;"><?php echo substr('000000'.$idInventoryControl,-6); ?></div>                    
                                <div class="listviewsubitem" style="width:100px;text-align:right;"><?php echo $ICTotalSales; ?></div>                    
                                <div class="listviewsubitem" style="width:100px;"><?php echo $BName; ?></div>                    
                                <div class="listviewsubitem" style="width:120px;"><?php echo $ICDate ?></div>                    
                            </div>
                        <?php
                            }
                        $stmt->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="containerdamage" class="showopencontainer">
            <div class="showopencontaineritem">
                <div class="listview" id="divdamage" style="position:absolute;top:0px;width:620px;height:390px;">
                    <div class="column" id="coldamage">
                        <div class="columnheader" style="width:140px;">Doc Number</div>
                        <div class="columnheader" style="width:140px;">Sheet Number</div>
                        <div class="columnheader" style="width:140px;">Branch</div>
                        <div class="columnheader" style="width:140px;">Date</div>
                    </div>
                    <div class="row" id="rowdamage">
						<?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("Select idDamageControl,concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCPeriod`,DCDocNum,BName,ICNumber,DCComments 
											From damagecontrol 
                                            Inner Join branch on damagecontrol.idBranchFrom=branch.idBranch 
											Left Join inventorycontrol on damagecontrol.idInventoryControl=inventorycontrol.idInventoryControl 
											Where damagecontrol.`Status`=1 and damagecontrol.idArea=? Order By DCDate")){
                            $stmt->bind_param('i',$_SESSION['area']);
                            $stmt->execute();
                            $stmt->bind_result($idDamageControl,$DCPeriod,$DCDocNum,$BName,$ICNumber,$DCComments);
                            while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="opendamage(<?php echo $idDamageControl; ?>)" style="cursor:pointer;">
                                <div class="listviewsubitem" style="width:140px;"><img src="img/icon/discount.png" width="16" height="16" style="padding-right:10px;"><?php echo $DCDocNum; ?></div>
                                <div class="listviewsubitem" style="width:140px;"><?php echo substr('000000'.$ICNumber,-6); ?></div>                    
                                <div class="listviewsubitem" style="width:140px;"><?php echo $BName; ?></div>                    
                                <div class="listviewsubitem" style="width:140px;"><?php echo $DCPeriod ?></div>                    
                            </div>
                        <?php
                            }
                        $stmt->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="containertemplate" class="showopencontainer">
            <div class="showopencontaineritem">
                <div class="listview" id="divtemplate" style="position:absolute;top:0px;width:620px;height:390px;">
                    <div class="column" id="coltemplate">
                        <div class="columnheader" style="width:606px;">Template Name</div>
                    </div>
                    <div class="row" id="rowtemplate">
						<?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("Select idTemplateItemControl,TICName From templateitemcontrol where `Status`=1 Order by TICName")){
                            $stmt->execute();
                            $stmt->bind_result($idTemplateItemControl,$TICName);
                            while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="redirect('producttemplate.php?&id=<?php echo $idTemplateItemControl; ?>')" style="cursor:pointer;">
                                <div class="listviewsubitem" style="width:606px;"><img src="img/icon/producttemplate.png" width="16" height="16" style="padding-right:10px;"><?php echo $TICName; ?></div>
                            </div>
                        <?php
                            }
                        $stmt->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="containerproduction" class="showopencontainer">
            <div class="showopencontaineritem">
                <div class="listview" id="divproduction" style="position:absolute;top:0px;width:620px;height:390px;">
                    <div class="column" id="colproduction">
                        <div class="columnheader" style="width:606px;">Production Batch Name</div>
                    </div>
                    <div class="row" id="rowproduction">
						<?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("SELECT idTemplateProductionBatch, TPBName FROM templateproductionbatch WHERE Status=1 Order by TPBName")){
                            $stmt->execute();
                            $stmt->bind_result($idTemplateItemBatch,$TPBName);
                            while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="redirect('productiontemplate.php?&id=<?php echo $idTemplateItemBatch; ?>')" style="cursor:pointer;">
                                <div class="listviewsubitem" style="width:606px;"><img src="img/icon/production.png" width="16" height="16" style="padding-right:10px;"><?php echo $TPBName; ?></div>
                            </div>
                        <?php
                            }
                        $stmt->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="containerpettycash" class="showopencontainer">
            <div class="showopencontaineritem">
                <div class="listview" id="divpettycash" style="position:absolute;top:0px;width:620px;height:390px;">
                    <div class="column" id="colpettycash">
                        <div class="columnheader" style="width:140px;">Reference</div>
                        <div class="columnheader" style="width:140px;">Sheet Number</div>
                        <div class="columnheader" style="width:140px;">Amount</div>
                        <div class="columnheader" style="width:140px;">Date</div>
                    </div>
                    <div class="row" id="rowpettycash">
						<?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("SELECT idPettyCashControl,PCCReferenceNumber,ICNumber,PCCAmount,PCCDate FROM pettycashcontrol Left Join inventorycontrol On pettycashcontrol.idInventoryControl = inventorycontrol.idInventoryControl WHERE pettycashcontrol.`Status`=1 Order by By Year(PCCDate) Desc, Month(PCCDate) Asc")){
                            $stmt->execute();
                            $stmt->bind_result($idPettyCashControl,$PCCReferenceNumber,$ICNumber,$PCCAmount,$PCCDate);
                            while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="redirect('createpettycash.php?&id=<?php echo $idPettyCashControl; ?>')" style="cursor:pointer;">
                                <div class="listviewsubitem" style="width:140px;"><img src="img/icon/pettycash.png" width="16" height="16" style="padding-right:10px;"><?php echo $PCCReferenceNumber; ?></div>
                                <div class="listviewsubitem" style="width:140px;"><?php echo substr('000000'.$ICNumber,-6); ?></div>                    
                                <div class="listviewsubitem" style="width:140px;"><?php echo $PCCAmount; ?></div>                    
                                <div class="listviewsubitem" style="width:140px;"><?php echo $PCCDate; ?></div>                    
                            </div>
                        <?php
                            }
                        $stmt->close();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <input name="btncancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" style="position:absolute;right:10px;bottom:10px;" >
    </div>
</div>

<div class="popup" id="edititf">
	<div id="edititfcontainer" class="popupcontainer" style="width:980px; height:400px;">
    </div>
</div>

<?php include 'footer.php'; ?>
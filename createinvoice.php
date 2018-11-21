<?php
  include 'header.php';
?>
<script src="js/invoice.js"></script>
<style type="text/css">
.headbox{
  width:314px;
  height:auto;
  background-color:#FFF;
  float:left;
  color:#333;
  padding:5px;
}
.headboxlistitem{
  width:100%;
  float:left;
  height:20px;
  padding-top:5px;
  padding-bottom:5px;
}
.headboxlistsubitem{
  float:left;
  height:inherit;
}
 
</style>
<div id="toolbar">
<?php
	 if(isset($_GET['uninv'])){
		 $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		  $stmt = $mysqli->stmt_init();
		  if($stmt->prepare("UPDATE salescontrol SET SCState='Open' WHERE unBranch=? AND unSalesControl=?")){
			  $stmt->bind_param('ii',$_GET['bid'],$_GET['unsc']);
			  $stmt->execute();
			  $stmt->close();
		  }
		  $stmt = $mysqli->stmt_init();
		  if($stmt->prepare("UPDATE salesdata SET SDState='Open' WHERE unBranch=? AND unSalesControl=? AND unSalesData=?")){
			  $stmt->bind_param('iii',$_GET['bid'],$_GET['unsc'],$_GET['unsd']);
			  $stmt->execute();
			  $stmt->close();
		  }
	}
?>
<!--<input style="position:relative; display:inline-table; margin-left:10px;" class="buttons" style="width:120px;" type="button" value="View Invoices" title="View Invoices" onclick="location.href='#selectSDInvoice'">-->
<input type="button" class="toolbarbutton" value="<?php if(!isset($_GET['bid'])){echo 'Select Branch';}else{
  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
  $stmt = $mysqli->stmt_init();
  if($stmt->prepare("Select BName From branch Where `Status`=1 and unBranch=? Order by BName Asc")){
	  $stmt->bind_param('i',$_GET['bid']);
	  $stmt->execute();
	  $stmt->bind_result($BName);
	  $stmt->fetch();
	  echo $BName;
	  $stmt->close();
  }}?>" title="<?php if(!isset($_GET['bid'])){echo 'Select Branch';}else{echo'Change Branch';}?>" name="popupbranch" onClick="location.href='#popupbranch'" style="margin-left:20px; width:auto" >
<input type="button" class="toolbarbutton" value="<?php if(!isset($_GET['unsc'])){echo 'Select Sales Date & Shift';}else if(!isset($_GET['unsd'])|| $_GET['unsd']==0){echo 'Select Shift under ' ;
						  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						  $stmt = $mysqli->stmt_init();
						  if($stmt->prepare("SELECT SCTimeStart
										  FROM salescontrol
										  WHERE unBranch=? AND unSalesControl = ?")){
						  $stmt->bind_param('ii',$_GET['bid'],$_GET['unsc']);
						  $stmt->execute();
						  $stmt->bind_result($SCTimeStart);
						  $stmt->fetch();
						  echo date('M d,Y',strtotime($SCTimeStart));
						  $stmt->close();	}}else{
						  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						  $stmt = $mysqli->stmt_init();
						  if($stmt->prepare("SELECT unSalesData,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee
										  FROM salesdata
										  LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
										  WHERE unBranch=? AND unSalesData = ?")){
						  $stmt->bind_param('ii',$_GET['bid'],$_GET['unsd']);
						  $stmt->execute();
						  $stmt->bind_result($unSalesData,$SDTimeStart,$unEmployee);
						  $stmt->fetch();
						  $unSalesData1=sprintf('%06d', $unSalesData);
						  echo $unSalesData1.' - '.date('M d,Y',strtotime($SDTimeStart)).' - '.date('H:i:s A',strtotime($SDTimeStart)).' - [ '.$unEmployee.' ]';
						  $stmt->close();
						  }}?>" value="<?php if(!isset($_GET['unsd']) || $_GET['unsd']==0){echo 'Select Shift';}else{echo 'Change Shift';}?>" name="selectSDSC" onClick="location.href='#selectSCSD'" style="margin-left:20px; width:auto" >
<input style="position:relative; display:inline-table; margin-left:10px;" class="buttons" style="width:120px;" type="button" value="View Invoices" title="View Invoices" onclick="location.href='#selectSDInvoice'">
<input style="position:relative; display:inline-table; margin-left:10px;" class="buttons" style="width:120px;" type="button" value="Edit Invoices" title="Edit Invoice" onclick="location.href='#selectSDEditInvoice'">
<?php if(isset($_GET['unsc']) && $_GET['unsc'] >0){ ?>
<button title="Close Day" style="float:right;margin:2.5px; margin-left:2.5px; margin-right:10px;" onClick="location.href='#popupcloseday'" class="buttons"><img src="img/icon/SCclosed.png" height="20" style="margin-bottom:-5px;"> Close Day</button>
<?php if($_GET['unsd']>0 && isset($_GET['unsd']) ){?>
<button title="Close Shift" style="float:right;margin:2.5px;" onClick="location.href='#popupcloseshift'" class="buttons"><img src="img/icon/SDclosed.png" height="20" style="margin-bottom:-5px;"> Close Shift</button>
<?php } }?>                          
</div>
<?php //if(isset($_GET['unsd'])){ ?>
<form name="invoiceForm" onSubmit="return false;" id="invoiceForm" method="post" action="include/POS.inc.php">
<div style=" height:700px; background-color:#FFF; border:solid thin #999; padding:10px; margin-bottom:20px;">
  <div style="width:680px; position:relative; display:inline-table">
	  <div class="listview" id="lvinvoicedata" style="width:680px; border:none">
		  <div class="headboxlistitem" style="padding:5px; width:670px;">
				  <?php
				  $mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				  $stmt = $mysqli->stmt_init();
				  if($stmt->prepare("SELECT ifnull(MAX(unInvoiceControl),0)+1 FROM invoicecontrol WHERE unBranch =".$_GET['bid'])){
					  $stmt->execute();
					  $stmt->bind_result($maxICnum);
					  $stmt->fetch();
					  $stmt->close();
				  }
				  //echo $maxICnum;
				  ?>
				  <div class="headboxlistsubitem" style="width:40%;">Invoice Number</div>
				  <div class="headboxlistsubitem" id="divinvoicenumber" style="width:60%;text-align:right;font-weight:bold;color:<?php if(isset($_GET['uninv'])){echo 'red';}else{echo 'green';}?>"><?php if(isset($_GET['uninv'])){echo 'EDITING - '.sprintf('%06d', $_GET['uninv']);}else{echo sprintf('%06d', $maxICnum);} ?></div>
				  <input type="hidden" id="InvCtrlun" name="InvCtrlun" value="<?php if(isset($_GET['uninv'])){echo $_GET['uninv'];}else{echo $maxICnum;} ?>">
		  </div>
		  <div class="listviewsubitem" style="width:620px; margin-left:10px;">
			  <input autocomplete="off" type="text" id="txtSearch" placeholder="Enter to search item" onKeyPress="return disableEnterKey(event)" value="" style="position:relative;top:0px;left:0px;width:100%;">
			  <input type="hidden" id="hdnSearchId" value="0">
			  <input type="hidden" id="hdnSearchPrice" value="0">
			  <input type="hidden" id="hdnbid" value="<?php echo $_GET['bid']; ?>">
		  </div>
			  <input type="button" class="button16" id="btnAddData" title="Add" onMouseUp="additem(hdnSearchId.value,txtSearch.value,hdnSearchPrice.value)" style="background-image:url(img/icon/add.png); padding:0px; border:none; width:16px; height:16px; margin-top:5px; background-color:transparent; cursor:pointer; margin-left:10px;">
		  <div class="listbox" id="lstresult"  style="position:fixed;width:620px;max-height:240px;display:none;"></div>
	  </div>
	  <div class="listview" id="lvinvoicedata" style="width:680px;">
		  <div class="column" id="colinvoicedata">
			  <div class="columnheader" style="width:40px;text-align:left;">QTY</div>
			  <div class="columnheader" style="width:280px;text-align:left;">ITEM</div>
			  <div class="columnheader" style="width:120px;text-align:right;">UNIT PRICE</div>
			  <div class="columnheader" style="width:120px;text-align:right;">TOTAL</div>
		  </div>
		  
		  <div class="row" id="rowinvoicedata" style="height:500px; overflow-y:scroll; overflow-x:hidden;">
			  <div class="lvitem" id="lvitem-0">
				  <input type="hidden" id="hdnitemcount" name="hdnitemcount" value="0">
				  <input type="hidden" id="hdnitemadd" value="0">
			  </div>
              <?php if(isset($_GET['uninv'])){
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("SELECT IDQuantity,invoicedata.unProductItem,PIName,IDPrice,IDTotalAmount FROM invoicedata
										INNER JOIN productitem ON invoicedata.unProductItem = productitem.unProductItem
										WHERE invoicedata.Status=1 AND unInvoiceControl = ? AND unBranch = ? AND invoicedata.Status=1")){
						$stmt->bind_param('ii',$_GET['uninv'],$_GET['bid']);
						$stmt->execute();
						$stmt->bind_result($IDQuantity,$unProductItem,$PIName,$IDPrice,$IDTotalAmount);
						$i=0;
						while($stmt->fetch()){
							$i=$i+1;
							?>
							<div class="listviewitem" id="lvitemdata-<?php echo $i ?>">
								<div class="listviewsubitem" style="width:40px; text-align:center;"><input type="number" value="<?php echo floor($IDQuantity); ?>" name="txtitemquantity-<?php echo $i ?>" id="txtitemquantity-<?php echo $i ?>" style="width:100%; background-color:transparent; border:none; text-align:center; margin-left:-10px" onChange="updateItemEditINV(<?php echo $i ?>)" tabindex="<?php echo $i ?>"></div>
								<div class="listviewsubitem" style="width:280px; text-align:center;"><input type="text" readonly style="width:100%; background-color:transparent; border:none; text-align:left;" name="txtitemname-<?php echo $i ?>"  id="txtitemname-<?php echo $i ?>" value="<?php echo $PIName; ?>"></div>
								<div class="listviewsubitem" style="width:120px; text-align:right;"><input type="number" readonly style=" background-color:transparent; border:none; text-align:right;" name="txtitemprice-<?php echo $i ?>"  id="txtitemprice-<?php echo $i ?>" value="<?php echo $IDPrice; ?>"></div>
								<div class="listviewsubitem" style="width:120px; text-align:right;"><input type="number" readonly style=" background-color:transparent; border:none; text-align:right;" name="txtitemtotal-<?php echo $i ?>"  id="txtitemtotal-<?php echo $i ?>" value="<?php echo $IDTotalAmount; ?>"></div>
                                <div class="listviewsubitem" style="width:50px; margin-left:20px;">
                                	<div class="button16" style="background-image:url(img/icon/delete.png);padding-top:5px;padding-left:0px;" onClick="removeItemEditINV(<?php echo $i ?>)">
                                    </div>
                                </div>
                                <input type="hidden" name="hdnitemid-<?php echo $i ?>" value="<?php echo $unProductItem ?>">
							</div>
							<?php
						}
						?>
                        	<script>
								$(function(){
									$('#hdnitemcount').val(<?php echo $i ?>);
									clearsearchitemdata();
									getTotalSales();
								});
							</script>
                            <script>
								function removeItemEditINV(linenum){ 
									if(confirm('Remove [ ' + $('#txtitemname-'+ linenum).val() + ' ] Are you sure?') ){
										$('#rowinvoicedata #lvitemdata-'+ linenum).remove();
										getTotalSales();
									}
								}
								function updateItemEditINV(linenum){
									if($('#txtitemquantity-'+ linenum).val().replace(/\s/g, '') == '' || $('#txtitemquantity-'+ linenum).val() <= 0){
										$('#txtitemquantity-'+ linenum).val(1)
									}
									var changenum=parseFloat($('#txtitemquantity-'+ linenum).val());
									var change=changenum.toFixed(2);
									var totalItemPrice=change * $('#txtitemprice-'+ linenum).val();
									$('#txtitemtotal-'+linenum).val(parseFloat(totalItemPrice).toFixed(2));
									getTotalSales();
								}
							</script>
						<?php
					}
				}?>
		  </div>
	  </div>
	  <div style="width:670px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px;">
		  TOTAL
		  <input type="text" name="totalAmount" id="totalAmount" value="0.00" style="float:right; text-align:right; color: #333; font-weight:bold; font-size:14px; border:none; padding:0px;" readonly>
	  </div>
  </div>
  <div style="width:580px; height:600px; position:relative; display:inline-table; float:right;">
	  <div style="width:500px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px; margin-top:50px;margin-left:auto;margin-right:auto;">
		  PAX
		  <input type="number" min="1" name="Pax" id="Pax" value="1" style="float:right; text-align:right; color: #333; font-weight:bold; font-size:14px; padding:0px; width:50px;">
	  </div>
	  <div class="listview" id="lvdiscountdata" style="width:500px; height:150px; padding:0px; margin-left:auto; margin-right:auto; margin-top:10px;">
		  <div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px; width:inherit"><div style="width:inherit; height:20px;" id="addDiscount" onClick="openDiscountPane();location.href='#popupdiscount'"><b>Add Discount</b></div></div>
		  <div class="row" id="rowdiscount" style=" width:500px; height:115px; overflow-y:scroll; overflow-x:hidden;">
          	  <?php if(isset($_GET['uninv'])){
				  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				  $stmt = $mysqli->stmt_init();
				  if($stmt->prepare("SELECT  discountcontrol.unDiscountType,DTPercent, DTName, DTVatExempt, DCTotalAmount, DCReference
						FROM  `discountcontrol` 
						INNER JOIN  `discounttype` ON discounttype.unDiscountType = discountcontrol.unDiscountType
						WHERE  `unBranch` =?
						AND  `unSalesControl` =?
						AND  `unSalesData` =?
						AND  `unInvoiceControl` =? AND discountcontrol.Status=1")){
						$stmt->bind_param('iiii',$_GET['bid'],$_GET['unsc'],$_GET['unsd'],$_GET['uninv']);
						$stmt->execute();
						$stmt->bind_result($unDiscountType,$DTPercent, $DTName, $DTVatExempt, $DCTotalAmount, $DCReference);
						$i=0;
						while($stmt->fetch()){
							$i=$i+1;
							?>
                            	<div class="listviewitem" id="lvdiscountdata-<?php echo $i ?>">
                                	<?php // echo $DTPercent.' - '.$DTName.' - '.$DCTotalAmount.' - '.$DCReference; ?>
                                    <div class="listviewsubitem" style="width:40px;">
                                    	<input type="text" value="<?php echo floor($DTPercent); ?>%" id="txtdiscountpercent-<?php echo $i ?>" name="txtdiscountpercent-<?php echo $i ?>" style="width:100%; background-color:transparent; border:none; text-align:center; margin-left:-10px" readonly>
                                    </div>
                                    <div class="listviewsubitem"  style="width:250px;">                                    	
                                    	<input type="text" value="<?php echo $DTName; ?>" id="txtdiscountname-<?php echo $i ?>" name="txtdiscountname-<?php echo $i ?>" style="width:100%; background-color:transparent; border:none; text-align:left;"  readonly>
                                    </div>
                                    <div class="listviewsubitem"  style="width:100px;">
                                    	<input type="number" value="<?php echo number_format((float)$DCTotalAmount, 2, '.', ''); ?>"  id="txtdiscountamount-<?php echo $i ?>" name="txtdiscountamount-<?php echo $i ?>"  style="width:100%; background-color:transparent; border:none; text-align:right;" readonly>
                                    </div>
                                    <input type="hidden" value="<?php echo $unDiscountType ?>" name="hdndiscountid-<?php echo $i ?>" id="hdndiscountid-<?php echo $i ?>">
                                    <input type="hidden" value="<?php echo $DTPercent/100; ?>" name="hdndiscountdeduct-<?php echo $i ?>" id="hdndiscountdeduct-<?php echo $i ?>">
                                    <input type="hidden" value="<?php echo $DTVatExempt ?>" name="hdndiscountvatexempt-<?php echo $i ?>" id="hdndiscountvatexempt-<?php echo $i ?>">
                                    <input type="hidden" name="hdndiscountvatexamount-<?php echo $i ?>" id="hdndiscountvatexamount-<?php echo $i ?>">
                                    <input type="hidden" name="hdndiscountvatexindp-<?php echo $i ?>" id="hdndiscountvatexindp-<?php echo $i ?>">
                                    <input type="hidden" name="hdndiscountvatnet-<?php echo $i ?>" id="hdndiscountvatnet-<?php echo $i ?>">
                                    <input type="hidden" name="hdndiscountvatamount-<?php echo $i ?>" id="hdndiscountvatamount-<?php echo $i ?>">
                                    <input type="hidden" name="hdndiscountvatex-<?php echo $i ?>" id="hdndiscountvatex-<?php echo $i ?>">
                                    <input type="hidden" name="hdndiscountvats-<?php echo $i ?>" id="hdndiscountvats-<?php echo $i ?>">
                                    <input type="hidden" name="hdndiscountdue-<?php echo $i ?>" id="hdndiscountdue-<?php echo $i ?>">
                                    <input type="hidden" value="<?php echo $DCReference ?>" name="hdndiscountreference-<?php echo $i ?>" id="hdndiscountreference-<?php echo $i ?>">
                                    <div class="listviewsubitem">
                                    	<div class="listviewsubitem"  style="width:25px; margin-left:10px">
                                        	<div class="button16"  style="background-image:url(img/icon/delete.png);padding-top:2.5px;padding-left:0px;"  onClick="removeDCountEditINV(<?php echo $i ?>)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
						}
						?>
                        <script>
						$(function(){
							$('#hdndiscountcount').val(<?php echo $i ?>);
							$('#hdndiscountadd').val(parseInt($('#hdndiscountadd').val())+1);
							//getDiscountAmount();
							//getTotalDiscount();
							getTotalSales();
						});
						function removeDCountEditINV(linenum){ 
									if(confirm('Remove [ ' + $('#txtdiscountname-'+ linenum).val() + ' ] Are you sure?') ){
										$('#rowdiscount #lvdiscountdata-'+ linenum).remove();
										//$('#hdndiscountcount').val(linenum-1);
										//getDiscountAmount();
										//getTotalDiscount();
										getTotalSales();
									}
								}
						</script>
                        <?php					
				  }
              }?>
			  <div class="lvdiscount" id="lvdiscount-0">
				  <input type="hidden" id="hdndiscountcount" name="hdndiscountcount" value="0">
				  <input type="hidden" id="hdndiscountadd" value="0">
			  </div>
		  </div>
	  </div>
	  <div style="width:500px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px;margin-left:auto;margin-right:auto;">
			  TOTAL DISCOUNT
			  <input type="text" name="TDiscount" id="TDiscount" value="0.00" style="float:right; text-align:right; border:none; color: #333; font-weight:bold; font-size:14px; padding:0px; width:50px;" readonly>
	  </div>
	  <div style="width:500px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px;margin-left:auto;margin-right:auto;">
			  TOTAL DUE
			  <input type="text" name="TDue" id="TDue" value="0.00" style="float:right; text-align:right; border:none; color: #333; font-weight:bold; font-size:14px; padding:0px; width:50px;" readonly>
	  </div>
	  <div style="width:480px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px; padding-left:30px; margin-left:auto;margin-right:auto;">
			  VAT Sales
			  <input type="text" name="VATS" id="VATS" value="0.00" style="float:right; text-align:right; margin-right: 20px; border:none; color: #333; font-weight:bold; font-size:14px; padding:0px; width:50px;" readonly>
	  </div>
	  <div style="width:480px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px; padding-left:30px; margin-left:auto;margin-right:auto;">
			  VATEx Sales
			  <input type="text" name="VATex" id="VATex" value="0.00" style="float:right; text-align:right; margin-right: 20px; border:none; color: #333; font-weight:bold; font-size:14px; padding:0px; width:50px;" readonly>
	  </div>
	  <div style="width:480px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px; padding-left:30px; margin-left:auto;margin-right:auto;">
			  Tax Amount
			  <input type="text" name="TaxAmount" id="TaxAmount" value="0.00" style="float:right; text-align:right; margin-right: 20px; border:none; color: #333; font-weight:bold; font-size:14px; padding:0px; width:50px;" readonly>
			  <input type="hidden" name="NetVAT" id="NetVAT" value="0.00">
			  <input type="hidden" name="VATExAmount" id="VATExAmount" value="0.00">
			  <input type="hidden" name="VATExIndP" id="VATExIndP" value="0.00">
	  </div>
	  <div class="listview" id="lvinvoicedata" style="width:500px; height:150px; padding:0px; margin-left:auto; margin-right:auto; margin-top:10px;">
		  <div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px; width:inherit"><div style="width:inherit; height:20px;" onClick="openPaymentPane();location.href='#popuppayment'"><b>Choose Payment</b></div></div>
		  <div class="row" id="rowpayment" style=" width:500px; height:115px; overflow-y:scroll; overflow-x:hidden;">
          	<?php if(isset($_GET['uninv'])){
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				    $stmt = $mysqli->stmt_init();
				    if($stmt->prepare("SELECT payment.unPaymentType,PTPriority,PTReference,PTName, PAmount
							FROM  `payment`
							INNER JOIN `paymenttype`
							ON paymenttype.unPaymentType=payment.unPaymentType
							WHERE  `unBranch` =?
							AND  `unSalesControl` =?
							AND  `unSalesData` =?
							AND  `unInvoiceControl` =? AND payment.Status=1")){
						$stmt->bind_param('iiii',$_GET['bid'],$_GET['unsc'],$_GET['unsd'],$_GET['uninv']);
						$stmt->execute();
						$stmt->bind_result($unPaymentType,$PTPriority,$PTReference,$PTName,$PAmount);
						$i=0;
						while($stmt->fetch()){
						$i=$i+1;
						?>
                          <div class="listviewitem" id="lvpaymentdata-<?php echo $i ?>">
                          	<div class="listviewsubitem" style="width:300px;">
                            	<input name="txtpaymentname-<?php echo $i ?>" id="txtpaymentname-<?php echo $i ?>" style="width:100%; background-color:transparent; border:none;" value="<?php echo $PTName ?>" readonly>
                            </div>
                            <div class="listviewsubitem" style="width:100px;">
                            	<input name="txtpaymentamount-<?php echo $i ?>" id="txtpaymentamount-<?php echo $i ?>" style="width:100%; background-color:transparent; border:none; text-align:right" value="<?php echo $PAmount ?>" type="number" onChange="updatePaymentEditINV(<?php echo $i ?>)">
                            </div>
                            <input type="hidden" value="<?php echo $unPaymentType ?>" name="hdnpaymentid-<?php echo $i ?>" id="hdnpaymentid-<?php echo $i ?>">
                            <input type="hidden" value="<?php if($PTName=='Cash'){echo 1;}?>" name="hdnpaymentcash-<?php echo $i ?>" id="hdnpaymentcash-<?php echo $i ?>">
                            <input type="hidden" value="<?php echo $PTPriority ?>" name="hdnpaymentpriority-<?php echo $i ?>" id="hdnpaymentpriority-<?php echo $i ?>">
                            <input type="hidden" value="<?php echo $PTReference ?>" name="hdnpaymentreference-<?php echo $i ?>" id="hdnpaymentreference-<?php echo $i ?>">
                            <div class="listviewsubitem" style="width:25px; margin-left:10px">
                            	<div class="button16" style="background-image:url(img/icon/delete.png);padding-top:2.5px;padding-left:0px;" onClick="removePaymentEditINV(<?php echo $i ?>)"></div>
                            </div>
                          </div>
                        <?php
						}
						?>
                        <script>
							$(function(){
								$('#hdnpaymentcount').val(<?php echo $i ?>);
								$('#hdnpaymentadd').val(parseInt($('#hdndiscountadd').val())+1);
									
								getTotalPayment();
							});
							function removePaymentEditINV(linenum){ 
								if(confirm('Remove [ ' + $('#txtpaymentname-'+linenum).val() + ' ] Are you sure?') ){
									$('#hdndiscountadd').val(parseInt($('#hdnpaymentadd').val())-1)	
									$('#rowpayment #lvpaymentdata-'+linenum).remove();
									getTotalPayment();
								}
							}
							
							function updatePaymentEditINV(linenum){
								if($('#txtpaymentamount-'+linenum).val().replace(/\s/g, '') == '' || $('#txtpaymentamount-'+linenum).val() <= 0){
									$('#txtpaymentamount-'+linenum).val(1)
								}
								getTotalPayment();
							}
						</script>
                        <?php
					}
			} ?>
			  <div class="lvdiscount" id="lvdiscount-0">
				  <input type="hidden" id="hdnpaymentcount" name="hdnpaymentcount" value="0">
				  <input type="hidden" id="hdnpaymentadd" value="0">
			  </div>
		  </div>
	  </div>
	  <div style="width:500px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px;margin-left:auto;margin-right:auto;">
			  TOTAL PAYMENT
			  <input type="text" name="TPaid" id="TPaid" value="0.00" style="float:right; text-align:right; border:none; color: #333; font-weight:bold; font-size:14px; padding:0px; width:50px;" readonly>
	  </div>
	  <div style="width:500px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px;margin-left:auto;margin-right:auto;">
			  FORFEIT/CHANGE
			  <input type="text" name="Change" id="Change" value="0.00" style="float:right; text-align:right; border:none; color: #333; font-weight:bold; font-size:14px; padding:0px; width:50px;" readonly>
	  </div>
	  <div style="width:500px; height:auto; color: #333; font-weight:bold; font-size:12px; padding:10px;margin-left:auto;margin-right:auto;">
			  <input type="hidden" id="TPCash" name="TPCash" value="0.00">
			  <input type="hidden" id="TPOthers" name="TPOthers" value="0.00">
			  <input type="hidden" id="branch" name="branch" value="<?php echo $_GET['bid']; ?>">
			  <input type="hidden" id="salesdata" name="salesdata" value="<?php echo $_GET['unsd']; ?>">
			  <input type="hidden" id="salescontrol" name="salescontrol" value="<?php 
				  /*$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				  $stmt = $mysqli->stmt_init();
				  if($stmt->prepare("SELECT unSalesControl FROM salesdata WHERE unSalesData=?")){
					  $stmt->bind_param('i',$_GET['unsd']);
					  $stmt->execute();
					  $stmt->bind_result($SC);
					  $stmt->fetch();
					  echo $SC; 
					  $stmt->close();
				  }*/
				  
				  echo $_GET['unsc'];
				  ?>">
			  <input type="hidden" id="employee" name="employee" value="<?php 
				  $mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				  $stmt = $mysqli->stmt_init();
				  if($stmt->prepare("SELECT unEmployee FROM salesdata WHERE unSalesData=?")){
					  $stmt->bind_param('i',$_GET['unsd']);
					  $stmt->execute();
					  $stmt->bind_result($E);
					  $stmt->fetch();
					  echo $E; 
					  $stmt->close();
				  }
				  ?>">
               <?php 
			   $settleStmt='';
			   if(isset($_GET['uninv'])){ $settleStmt=  'Save changes in this transaction. Are you sure?'; }else{ $settleStmt = 'Settle this transaction. Are you sure?'; } ?>
              <input type="hidden" name="isExist" id="isExist" value="<?php if(isset($_GET['uninv'])){echo 1;}else{echo 0;}?>">
			  <button title="Settle Transaction" style="float:right" onClick="msgbox('<?php echo $settleStmt; ?>
              ','settlethis','#close')" class="buttons" style="padding:10px;" ><?php if(isset($_GET['uninv'])){echo 'Save Changes';}else{echo 'Settle';}?></button>
			  <button title="Void Transaction" style="float:right" onClick="msgbox('Void this transaction. Are you sure?','voidthis','#close')" class="buttons" style="padding:10px;" > Void All</button>
</form>
	  </div>
  </div>
</div>

<?php //} ?> 

<div id="selectSCSD" class="popup">
  <?php 
  $colBranch = new Collection;
  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
  $stmt = $mysqli->stmt_init();
  if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName Asc")){
	  $stmt->bind_param('i',$_SESSION['area']);
	  $stmt->execute();
	  $stmt->bind_result($unBranch,$BName);
	  while($stmt->fetch()){
		  $colBranch->Add($BName,$unBranch);
	  }
	  $stmt->close();
  }		
  ?>
  <div class="popupcontainer" style="width:800px; top:-60px; height:530px;">
	  <div class="popuptitle" align="center">Select Shift</div>
	  <div style=" left:230px; top:45px; width:800px; height:300px; background-color:#FFF;">
		  <div class="listview" id="lvMAP">
			  <div class="column" id="colMAP">
				  <div class="columnheader" style="width:100px; text-align:left;">Date ID</div>
				  <div class="columnheader" style="width:100px;">Date</div>
				  <div class="columnheader" style="width:150px;">Net Sales</div>
				  <div class="columnheader" style="width:150px;">Previous Reading</div>
				  <div class="columnheader" style="width:150px;">Current Reading</div>
			  </div>
			  <div class="row" id="salesdata" style="height:275px;">
				  <div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px;"><div style="background-image:url(img/icon/SCadd.png); width:auto; height:20px; background-size:contain; position:relative; background-repeat:no-repeat; padding-left:25px;" onClick="location.href='#createsales'"><b>Create New Sales </b></div></div>
				  <?php  
					  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					  $stmt = $mysqli->stmt_init();
					  if($stmt->prepare("SELECT unSalesControl,SCState,SCTimeStart,SCNetSales,SCReadingPrevious,SCReadingCurrent
										  FROM salescontrol
										  WHERE unBranch=? ORDER BY unBranch DESC")){
						  $stmt->bind_param('i',$_GET['bid']);
						  $stmt->execute();
						  $stmt->bind_result($unSalesControl,$SCState,$SCTimeStart,$SCNetSales,$SCReadingPrevious,$SCReadingCurrent);
						  while($stmt->fetch()){
							  $unSalesControl1=sprintf('%06d',$unSalesControl);
							  ?>
							  <div class="listviewitem" style="cursor:default; background-color:transparent" onClick="loadshift(<?php echo $_GET['bid']?>,<?php echo $unSalesControl; ?>,<?php if($SCState=='Close'){echo '0';}else{echo '1';};?>)">
								  <div class="listviewsubitem" style="width:80px; text-align:left; background-image:url(img/icon/<?php if($SCState=='Close'){echo 'SCclosed';}else{echo 'SCopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesControl1; ?></div>
								  <div class="listviewsubitem" style="width:100px;"><?php echo date('Y-m-d',strtotime($SCTimeStart)); ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SCNetSales; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SCReadingPrevious; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SCReadingCurrent; ?></div>
								  <div class="selectedSC" id="selectedSC-<?php echo $unSalesControl; ?>" style="padding-left:30px;padding-top:5px;color:<?php if($SCState=='Close'){echo 'red';}else{echo 'green';};?>;"><?php if($unSalesControl==$_GET['unsc']){echo 'Day Selected';}; ?></div>
							  </div>
							  <?php
						  }
					  }
				  ?>
			  </div>
		  </div>
	  </div>
	  <div style="position:relative; width:800px; height:150px; background-color:#FFF; margin-top:20px;">
		  <div class="listview" id="lvMAP" style="min-height:150px;">
			  <div class="column" id="colMAP">
				  <div class="columnheader" style="width:100px; text-align:left;">Shift ID</div>
				  <div class="columnheader" style="width:100px;">Time Start</div>
				  <div class="columnheader" style="width:150px;">Cashier</div>
				  <div class="columnheader" style="width:150px;">Net Sales</div>
			  </div>
			  <div class="row" style="height:130px;">
				  <div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px;"><div style="background-image:url(img/icon/SDadd.png); width:auto; height:20px; background-size:contain; position:relative; background-repeat:no-repeat; padding-left:25px;" onClick="location.href='#createshift'"><b>Create New Shift </b></div></div>
				  <div id="shiftdata" style="cursor:default;">
				  <?php
					  if(isset($_GET['unsc'])){
					  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					  $stmt = $mysqli->stmt_init();
					  if($stmt->prepare("SELECT unSalesData,SDState,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee,SDNetSales
										  FROM salesdata
										  LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
										  WHERE unBranch=? AND unSalesControl = ? ORDER BY unBranch DESC")){
						  $stmt->bind_param('ii',$_GET['bid'],$_GET['unsc']);
						  $stmt->execute();
						  $stmt->bind_result($unSalesData,$SDState,$SDTimeStart,$unEmployee,$SDNetSales);
						  
						  while($stmt->fetch()){
							  $unSalesData1=sprintf('%06d', $unSalesData);
							  ?>
							  <div class="listviewitem" id="listviewitemSD-<?php echo $unSalesData; ?>" style="cursor:default;"  onClick="SEThdnunSD(<?php echo $unSalesData; ?>,<?php if($SDState=='Close'){echo '0';}else{echo '1';};?>)">
								  <div class="listviewsubitem" style="width:80px; text-align:left;background-image:url(img/icon/<?php if($SDState=='Close'){echo 'SDclosed';}else{echo 'SDopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesData1; ?></div>
								  <div class="listviewsubitem" style="width:100px;"><?php echo date('H:i:sa',strtotime($SDTimeStart)); ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $unEmployee; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SDNetSales; ?></div>
								  <div class="selectedSD" id="selectedSD-<?php echo $unSalesData; ?>" style="padding-left:30px;padding-top:5px;color:<?php if($SCState=='Close'){echo 'red';}else{echo 'green';};?>;"></div>
							  </div>
							  <?php
						  }
						  $stmt->close();
					  }
					  }
				  ?>
				  </div>
			  </div>
		  </div>
	  </div>
		  <div align="center" style="padding-top:10px; position:relative">
			  <input type="hidden" id="hdnunSC" name="hdnunSC" value="<?php if(isset($_GET['unsc'])){echo $_GET['unsc'];}else{echo '0';}?>">
			  <input type="hidden" id="hdnunSD" name="hdnunSD" value="0">
			  <input name="btnSelectShift" id="btnSelectShift" type="button" value="Open Shift" title="Open Shift" onClick="location.href='createinvoice.php?&bid=<?php echo $_GET['bid']; ?>&unsd='+ document.getElementById('hdnunSD').value+'&unsc='+ document.getElementById('hdnunSC').value;" class="buttons" disabled/>
			  <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
		  </div>
  </div>
</div>

<div id="popupdiscount" class="popup">
  <div class="popupcontainer" style="height:240px;">
	  <div class="popuptitle" align="center">ADD DISCOUNT</div>
	  <div class="popupitem">
	  
		  <div class="listview" style="height:150px;">
			  <div class="column">
				  <div class="columnheader">Discount</div>
			  </div>
			  <div class="row">
			  <?php
				  $mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				  $stmt=$mysqli->stmt_init();
				  if($stmt->prepare("Select unDiscountType,DTName,DTPercent,DTVatExempt 
									  from discounttype 
									  where `Status` = 1"
									  )){
					  $stmt->execute();
					  $stmt->bind_result($unDiscountType,$DTName,$DTPercent,$DTVatExempt);
						  
					  while($stmt->fetch()){
					  ?>
						  <div class="listviewitem" style="cursor:pointer; padding-left:0px" onClick="selectDiscount('<?php echo $unDiscountType; ?>','<?php echo $DTName; ?>','<?php echo $DTPercent; ?>','<?php echo $DTPercent/100; ?>','<?php echo $DTVatExempt; ?>')">
							  <div id="listviewDC-<?php echo $unDiscountType; ?>" class="listviewDC" style="width:inherit;height:inherit; padding:2.5px 10px;">
								  <div class="listviewsubitem"><?php echo $DTName; ?></div>
								  <div class="listviewsubitem" style="margin-left:15px;">-<?php echo number_format($DTPercent); ?>%</div>
								  <div class="listviewsubitem" style=" width:100px; text-align:center; height:10px;"><?php if($DTVatExempt==1){ echo ' ***VAT Inclusive';} ?></div>
								  <div class="discountSelected" id="discountSelected-<?php echo $unDiscountType; ?>" style="width:16px;height:16px; padding:0px; background-repeat:no-repeat;background-size:contain;position:relative; display:inline-table;"></div>
							  </div>
						  </div>
					  <?php
					  }
					  $stmt->close();
				  }
			  ?>                   
			  </div>
		  </div>
		  <form id="addDForm" style="width:inherit" onsubmit="return false;">
			  <input type="text" placeholder="Reference Number..." value="" id="refNum" name="refNum" style="width:296px; margin-top:10px;" required>
		  <div align="center" style="padding-top:10px;">
			  <input type="hidden" value="" class="buttons" id="DCselect">
			  <input type="submit" value="Add" title="Add" class="buttons" id="addDCount">
			  <input type="reset" value="Close" title="Close" onClick="closeDiscountPane();location.href='#close'" class="buttons" >
		  </div>
		  </form>
	  </div>
  </div>
</div>

<div id="popuppayment" class="popup">
  <div class="popupcontainer" style="height:240px;">
	  <div class="popuptitle" align="center">CHOOSE PAYMENT</div>
	  <div class="popupitem">
	  
		  <div class="listview" style="height:150px;">
			  <div class="column">
				  <div class="columnheader">PAYMENT</div>
			  </div>
			  <div class="row">
			  <?php
				  $mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				  $stmt=$mysqli->stmt_init();
				  if($stmt->prepare("Select unPaymentType,PTName,PTFixedAmount,PTReference,PTPriority
									  from paymenttype 
									  where `Status` = 1"
									  )){
					  $stmt->execute();
					  $stmt->bind_result($unPaymentType,$PTName,$PTFixedAmount,$PTReference,$PTPriority);
					  while($stmt->fetch()){
					  ?>
						  <div class="listviewitem" style="cursor:pointer; padding-left:0px" onClick="selectPayment('<?php echo $unPaymentType."','".$PTName."','".$PTFixedAmount."','".$PTReference."','".$PTPriority?>','<?php if($PTName=='Cash'){echo 1;}?>')">
							  <div id="listviewP-<?php echo $unPaymentType; ?>" class="listviewP" style="width:inherit;height:inherit; padding:2.5px 10px;">
								  <div class="listviewsubitem"><?php echo $PTName; ?></div>
								  <div class="paymentSelected" id="paymentSelected-<?php echo $unPaymentType; ?>" style="width:16px;height:16px; padding:0px; background-repeat:no-repeat;background-size:contain;position:relative; display:inline-table;"></div>
							  </div>
						  </div>
					  <?php
					  }
					  $stmt->close();
				  }
			  ?>                   
			  </div>
		  </div>
			  <form id="addPForm" style="width:inherit" onsubmit="return false;">
			  <input type="text" placeholder="Reference Number..." value="" id="PrefNum" name="PrefNum" style="width:296px; display:none; margin-top:10px;" >
		  <div align="center" style="padding-top:10px;">
			  <input type="hidden" value="" class="buttons" id="Pselect">
			  <input type="submit" value="Add" title="Add" class="buttons" id="addPCount">
			  <input type="button" value="Close" title="Close" onClick="closePaymentPane();location.href='#close'" class="buttons" >
		  </div>
		  </form>
	  </div>
  </div>
</div>

<div id="popupbranch" class="popup">
  <div class="popupcontainer" style="height:400px;">
	  <div class="popuptitle" align="center">SELECT BRANCH</div>
	  <div class="popupitem">
	  
		  <div class="listview" style="height:350px;">
			  <div class="column">
				  <div class="columnheader">Branch</div>
			  </div>
			  <div class="row">
			  <?php
				  $mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				  $stmt=$mysqli->stmt_init();
				  if($stmt->prepare("Select branch.unBranch,BName 
									  from branch 
									  where branch.`Status` = 1 and Btype=1 and unArea=? 
									  Order by BName")){
					  $stmt->bind_param('i',$_SESSION['area']);
					  $stmt->execute();
					  $stmt->bind_result($unBranch,$BName);
					  while($stmt->fetch()){
					  ?>
						  <div class="listviewitem" onClick="location.href='createinvoice.php?&bid=<?php echo $unBranch; ?>#selectSCSD'" style="cursor:pointer;">
							  <div class="listviewsubitem"><?php echo $BName; ?></div>
						  </div>
					  <?php
					  }
					  $stmt->close();
				  }
			  ?>                   
			  </div>
		  </div>

		  <div align="center" style="padding-top:10px;">
			  <input type="button" value="Close" title="Close" onClick="location.href='#close'" class="buttons" >
		  </div>
	  </div>
  </div>
</div>

<div id="popupcloseshift" class="popup">
  <div class="popupcontainer" style="height:auto;">
	  <div class="popuptitle" align="center">CLOSE SHIFT</div>
	  <div class="popupitem">
		  <div class="popupitem">
			  <div class="popupitemlabel">Time End</div>
			  <form style="width:inherit" onsubmit="return false;">
				<input type="datetime-local" name="SDtimeend" style="width:170px" required>
			  </div>
			  <div class="popupitemlabel">Ending Balance</div>
				<input type="text" style="width:195px;" id="sdbalanceend" name="sdbalanceend" style="text-align:right" placeholder="0.00">
			</div>
		  <div align="center" style="padding-top:10px;">
			  <input type="submit" value="Close Shift" title="Close Shift" class="buttons" id="closeshift" onClick="if(SDtimeend.value!=''){msgbox('Close Shift. Are you sure?','closeshift['+SDtimeend.value+']['+sdbalanceend.value+']','#popupcloseshift')}">
			  <input type="button" value="Cancel" title="Cancel" onClick="reset();location.href='#close'" class="buttons" >
		  </div>
		  </form>
		  <!--createinvoice.php?&bid=<?php echo $_GET['bid'];?>&idsd=<?php echo $_GET['unsd'];?>&closeshift='+SDtimeend.value+'#selectSCSD-->
	  </div>
  </div>
</div>

<div id="popupcloseday" class="popup">
  <div class="popupcontainer" style="height:auto;">
	  <div class="popuptitle" align="center">CLOSE Day</div>
	  <div class="popupitem">
		  <div class="popupitem">
			  <div class="popupitemlabel">Time End</div>
			  <form style="width:inherit" onsubmit="return false;">
				<input type="datetime-local" name="SCtimeend" id="SCtimeend" style="width:170px" required>
				<div class="popupitemlabel">Employee Close</div>
				<select name="cmbEClose" id="cmbEClose" style="width:200px;" required >
				  <?php 
					   $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					   $stmt1 = $mysqli->stmt_init();
						  if($stmt1->prepare("SELECT unEmployee
										  FROM salesdata
										  WHERE unBranch=? AND unSalesData = ?")){
						  $stmt1->bind_param('ii',$_GET['bid'],$_GET['unsd']);
						  $stmt1->execute();
						  $stmt1->bind_result($unEmpOpen);
						  $stmt1->fetch();
						  $stmt1->close();
						  }
					   $stmt=$mysqli->stmt_init();
					   if($stmt->prepare("SELECT employee.unEmployee,ELastName,EFirstName,EMiddleName,unArea FROM employee INNER JOIN employeearea ON employee.unEmployee=employeearea.unEmployee WHERE employee.Status=1 AND employeearea.Status=1 AND unArea=?")){
						  $stmt->bind_param('i',$_SESSION['area']);
						  $stmt->execute();
						  $stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName,$unEA);
						  while($stmt->fetch()){
				  ?>
							  <option value="<?php echo $unEmployee; ?>"<?php echo ($unEmployee==$unEmpOpen)?'Selected':''; ?> ><?php echo $EFirstName." ".$EMiddleName." ".$ELastName; ?></option>
				  <?php
						  }
						  $stmt->close();
						  }
				  ?>
				  </select>
			  </div>
		  <div align="center" style="padding-top:10px;">
		  <input type="hidden" id="hdnunSDOpen" name="hdnunSDOpen" value="<?php
				  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				  $stmt = $mysqli->stmt_init();
				  if($stmt->prepare("SELECT unSalesData
						  FROM salesdata
						  WHERE unBranch=? AND unSalesControl = ? AND SDState = 'Open'")){
					  $stmt->bind_param('ii',$_GET['bid'],$_GET['unsc']);
					  $stmt->execute();
					  $stmt->store_result();
					  //$stmt->fetch();
					  if($stmt->num_rows()!=0){echo '1';}else{echo '0';};
					  $stmt->close();
				  }
			  ?>">
			  <input type="submit" value="Close Day" title="Close Day" class="buttons" id="closeday" onClick="if(SCtimeend.value!=''){msgbox('Close Day. Are you sure?','closeday['+SCtimeend.value+']['+cmbEClose.value+']['+hdnunSDOpen.value+']','#popupcloseday')}">
			  <input type="button" value="Cancel" title="Cancel" onClick="reset();location.href='#close'" class="buttons" >
		  </div>
		  </form>
		  <!--createinvoice.php?&bid=<?php echo $_GET['bid'];?>&idsc=<?php echo $_GET['unsd'];?>&emp='+cmbEClose.value+'&closeday='+SCtimeend.value+'#selectSCSD-->
	  </div>
  </div>
</div>

<div class="popup" id="ViewSoldInvoice">
  <div class="popupcontainer" style="width:900px; top:-50px;">
	  <div class="popuptitle" id="SITitle" align="center">Invoices under <?php 
	  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	  $stmt = $mysqli->stmt_init();
	  if($stmt->prepare("Select BName From branch Where `Status`=1 and unBranch=? Order by BName Asc")){
		  $stmt->bind_param('i',$_GET['bid']);
		  $stmt->execute();
		  $stmt->bind_result($BName);
		  $stmt->fetch();
		  echo $BName.' - ';
		  $stmt->close();
	  }
	  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						  $stmt = $mysqli->stmt_init();
						  if($stmt->prepare("SELECT unSalesData,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee
										  FROM salesdata
										  LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
										  WHERE unBranch=? AND unSalesData = ?")){
						  $stmt->bind_param('ii',$_GET['bid'],$_GET['unsd']);
						  $stmt->execute();
						  $stmt->bind_result($unSalesData,$SDTimeStart,$unEmployee);
						  $stmt->fetch();
						  $unSalesData1=sprintf('%06d', $unSalesData);
						  echo $unSalesData1.' - '.date('M d,Y',strtotime($SDTimeStart)).' - '.date('h:i:s A',strtotime($SDTimeStart)).' - [ '.$unEmployee.' ]';
						  $stmt->close();
						  }?></div>
	  <div class="listview" style="width:350px; height:500px; display:inline-table; position:relative">
		  <div class="column">
			  <div class="columnheader" style="width:30px; text-align:center;" >INV#</div>
			  <div class="columnheader" style="width:90px; text-align:right;" >Net</div>
			  <div class="columnheader" style="width:90px; text-align:right;" >Discount</div>
			  <div class="columnheader" style="width:110px; text-align:right;" >Total</div>
		  </div>
		  <div class="row" id="ViewSI" style="height:500px;"></div>
	  </div>
	  <div class="listview" style="width:543px; height:500px;display:inline-table; position:relative;">
		  <div class="column">
			  <div class="columnheader" style="width:15px; text-align:center;" >Qty</div>
			  <div class="columnheader" style="width:270px; text-align:center;" >Item</div>
			  <div class="columnheader" style="width:100px; text-align:right;" >Price</div>
			  <div class="columnheader" style="width:100px; text-align:right;" >Total</div>
		  </div>
		  <div class="row" id="ViewSID" style="height:500px;"></div>
	  </div>
	  <div align="center" style="margin-top:5px;"><input type="button" onClick="location.href='#close'" class="buttons" title="Close" value="Close" ></div>
  </div>
</div>

<div id="selectSDInvoice" class="popup">
  <?php 
  $colBranch = new Collection;
  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
  $stmt = $mysqli->stmt_init();
  if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName Asc")){
	  $stmt->bind_param('i',$_SESSION['area']);
	  $stmt->execute();
	  $stmt->bind_result($unBranch,$BName);
	  while($stmt->fetch()){
		  $colBranch->Add($BName,$unBranch);
	  }
	  $stmt->close();
  }		
  ?>
  <div class="popupcontainer" style="width:800px; top:-60px; height:530px;">
	  <div class="popuptitle" align="center">View Invoices</div>
	  <div style=" left:230px; top:45px; width:800px; height:300px; background-color:#FFF;">
		  <div class="listview" id="lvMAP">
			  <div class="column" id="colMAP">
				  <div class="columnheader" style="width:100px; text-align:left;">Date ID</div>
				  <div class="columnheader" style="width:100px;">Date</div>
				  <div class="columnheader" style="width:150px;">Net Sales</div>
				  <div class="columnheader" style="width:150px;">Previous Reading</div>
				  <div class="columnheader" style="width:150px;">Current Reading</div>
			  </div>
			  <div class="row" id="salesdata" style="height:275px;">
				  <!--<div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px;"><div style="background-image:url(img/icon/SCadd.png); width:auto; height:20px; background-size:contain; position:relative; background-repeat:no-repeat; padding-left:25px;" onClick="location.href='#createsales'"><b>Create New Sales </b></div></div>-->
				  <?php  
					  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					  $stmt = $mysqli->stmt_init();
					  if($stmt->prepare("SELECT unSalesControl,SCState,SCTimeStart,SCNetSales,SCReadingPrevious,SCReadingCurrent
										  FROM salescontrol
										  WHERE unBranch=? ORDER BY unBranch DESC")){
						  $stmt->bind_param('i',$_GET['bid']);
						  $stmt->execute();
						  $stmt->bind_result($unSalesControl,$SCState,$SCTimeStart,$SCNetSales,$SCReadingPrevious,$SCReadingCurrent);
						  while($stmt->fetch()){
							  $unSalesControl1=sprintf('%06d',$unSalesControl);
							  ?>
							  <div class="listviewitem" style="cursor:default; background-color:transparent" onClick="loadshiftINV(<?php echo $_GET['bid']?>,<?php echo $unSalesControl; ?>,1)">
								  <div class="listviewsubitem" style="width:80px; text-align:left; background-image:url(img/icon/<?php if($SCState=='Close'){echo 'SCclosed';}else{echo 'SCopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesControl1; ?></div>
								  <div class="listviewsubitem" style="width:100px;"><?php echo date('Y-m-d',strtotime($SCTimeStart)); ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SCNetSales; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SCReadingPrevious; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SCReadingCurrent; ?></div>
								  <div class="selectedSCINV" id="selectedSCINV-<?php echo $unSalesControl; ?>" style="padding-left:30px;padding-top:5px;color:<?php if($SCState=='Close'){echo 'red';}else{echo 'green';};?>;"><?php if($unSalesControl==$_GET['unsc']){echo 'Day Selected';}; ?></div>
							  </div>
							  <?php
						  }
					  }
				  ?>
			  </div>
		  </div>
	  </div>
	  <div style="position:relative; width:800px; height:150px; background-color:#FFF; margin-top:20px;">
		  <div class="listview" id="lvMAP" style="min-height:150px;">
			  <div class="column" id="colMAP">
				  <div class="columnheader" style="width:100px; text-align:left;">Shift ID</div>
				  <div class="columnheader" style="width:100px;">Time Start</div>
				  <div class="columnheader" style="width:150px;">Cashier</div>
				  <div class="columnheader" style="width:150px;">Net Sales</div>
			  </div>
			  <div class="row" style="height:130px;">
				  <!--<div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px;"><div style="background-image:url(img/icon/SDadd.png); width:auto; height:20px; background-size:contain; position:relative; background-repeat:no-repeat; padding-left:25px;" onClick="location.href='#createshift'"><b>Create New Shift </b></div></div>-->
				  <div id="shiftdataINV" style="cursor:default;">
				  <?php
					  if(isset($_GET['unsc'])){
					  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					  $stmt = $mysqli->stmt_init();
					  if($stmt->prepare("SELECT unSalesData,SDState,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee,SDNetSales
										  FROM salesdata
										  LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
										  WHERE unBranch=? AND unSalesControl = ? ORDER BY unBranch DESC")){
						  $stmt->bind_param('ii',$_GET['bid'],$_GET['unsc']);
						  $stmt->execute();
						  $stmt->bind_result($unSalesData,$SDState,$SDTimeStart,$unEmployee,$SDNetSales);
						  
						  while($stmt->fetch()){
							  $unSalesData1=sprintf('%06d', $unSalesData);
							  ?>
							  <div class="listviewitem" id="listviewitemSDINV-<?php echo $unSalesData; ?>" style="cursor:default;"  onClick="SEThdnunSDINV(<?php echo $unSalesData; ?>,<?php if($SDState=='Close'){echo '0';}else{echo '1';};?>)">
								  <div class="listviewsubitem" style="width:80px; text-align:left;background-image:url(img/icon/<?php if($SDState=='Close'){echo 'SDclosed';}else{echo 'SDopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesData1; ?></div>
								  <div class="listviewsubitem" style="width:100px;"><?php echo date('H:i:sa',strtotime($SDTimeStart)); ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $unEmployee; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SDNetSales; ?></div>
								  <div class="selectedSDINV" id="selectedSDINV-<?php echo $unSalesData; ?>" style="padding-left:30px;padding-top:5px;color:<?php if($SCState=='Close'){echo 'red';}else{echo 'green';};?>;"></div>
							  </div>
							  <?php
						  }
						  $stmt->close();
					  }
					  }
				  ?>
				  </div>
			  </div>
		  </div>
	  </div>
		  <div align="center" style="padding-top:10px; position:relative">
			  <input type="hidden" id="hdnunSCINV" name="hdnunSCINV" value="<?php if(isset($_GET['unsc'])){echo $_GET['unsc'];}else{echo '0';}?>">
			  <input type="hidden" id="hdnunSDINV" name="hdnunSDINV" value="0">
			  <input type="hidden" id="hdnunBIDINV" name="hdnunBIDINV" value="<?php echo $_GET['bid']?>">
			  <input name="btnSelectShiftINV" id="btnSelectShiftINV" type="button" value="View Invoices" title="Open Shift" class="buttons" disabled/>
			  <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
		  </div>
  </div>
</div>

<div id="selectSDEditInvoice" class="popup">
  <?php 
  $colBranch = new Collection;
  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
  $stmt = $mysqli->stmt_init();
  if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName Asc")){
	  $stmt->bind_param('i',$_SESSION['area']);
	  $stmt->execute();
	  $stmt->bind_result($unBranch,$BName);
	  while($stmt->fetch()){
		  $colBranch->Add($BName,$unBranch);
	  }
	  $stmt->close();
  }		
  ?>
  <div class="popupcontainer" style="width:800px; top:-60px; height:550px;">
	  <div class="popuptitle" align="center">Edit Invoice</div>
	  <div style=" left:230px; top:45px; width:800px; height:150px; background-color:#FFF;">
		  <div class="listview" id="lvMAP" style="height:150px">
			  <div class="column" id="colMAP">
				  <div class="columnheader" style="width:100px; text-align:left;">Date ID</div>
				  <div class="columnheader" style="width:150px;">Date</div>
				  <div class="columnheader" style="width:100px;">Net Sales</div>
				  <div class="columnheader" style="width:150px;">Previous Reading</div>
				  <div class="columnheader" style="width:150px;">Current Reading</div>
			  </div>
			  <div class="row" id="salesdata" style="height:125px">
				  <!--<div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px;"><div style="background-image:url(img/icon/SCadd.png); width:auto; height:20px; background-size:contain; position:relative; background-repeat:no-repeat; padding-left:25px;" onClick="location.href='#createsales'"><b>Create New Sales </b></div></div>-->
				  <?php  
					  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					  $stmt = $mysqli->stmt_init();
					  if($stmt->prepare("SELECT unInventoryControl,unSalesControl,SCState,SCTimeStart,SCNetSales,SCReadingPrevious,SCReadingCurrent
										  FROM salescontrol
										  WHERE unBranch=? AND Status=1 ORDER BY unBranch DESC")){
						  $stmt->bind_param('i',$_GET['bid']);
						  $stmt->execute();
						  $stmt->bind_result($unInventoryControl,$unSalesControl,$SCState,$SCTimeStart,$SCNetSales,$SCReadingPrevious,$SCReadingCurrent);
						  while($stmt->fetch()){
							  $unSalesControl1=sprintf('%06d',$unSalesControl);
							  ?>
							  <div class="listviewitem" style="cursor:default; background-color:transparent" onClick="loadshiftINVEdit(<?php echo $_GET['bid']?>,<?php echo $unSalesControl; ?>,<?php echo $unInventoryControl; ?>,1)">
								  <div class="listviewsubitem" style="width:80px; text-align:left; background-image:url(img/icon/<?php if($SCState=='Close'){echo 'SCclosed';}else{echo 'SCopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesControl1; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><input class="editDateSC" id="editDateSC-<?php echo $_GET['bid']?>-<?php echo $unSalesControl; ?>" type="date" readonly="readonly" value="<?php echo date('Y-m-d',strtotime($SCTimeStart)); ?>" ></div>
								  <!--<div class="listviewsubitem" style="width:100px;"><?php echo date('Y-m-d',strtotime($SCTimeStart)); ?></div>-->
								  <div class="listviewsubitem" style="width:100px;"><?php echo $SCNetSales; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SCReadingPrevious; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SCReadingCurrent; ?></div>
								  <div class="selectedSCINVEdit" id="selectedSCINVEdit-<?php echo $unSalesControl; ?>" style="padding-left:30px;padding-top:5px;color:<?php if($unInventoryControl==0){echo 'green';}else{echo 'red';};?>;"><?php if($unSalesControl==$_GET['unsc']){if($unInventoryControl==0){echo 'Day Selected';}else{echo 'Mapped!';}}; ?></div>
							  </div>
							  <?php
						  }
					  }
				  ?>
			  </div>
		  </div>
	  </div>
	  <div style="position:relative; width:800px; height:150px; background-color:#FFF; margin-top:20px;">
		  <div class="listview" id="lvMAP" style="min-height:150px;">
			  <div class="column" id="colMAP">
				  <div class="columnheader" style="width:100px; text-align:left;">Shift ID</div>
				  <div class="columnheader" style="width:100px;">Time Start</div>
				  <div class="columnheader" style="width:150px;">Cashier</div>
				  <div class="columnheader" style="width:150px;">Net Sales</div>
			  </div>
			  <div class="row" style="height:130px;">
				  <!--<div class="group" style="cursor:pointer; padding-left:20px;padding-top:10px;padding-bottom:-10px;"><div style="background-image:url(img/icon/SDadd.png); width:auto; height:20px; background-size:contain; position:relative; background-repeat:no-repeat; padding-left:25px;" onClick="location.href='#createshift'"><b>Create New Shift </b></div></div>-->
				  <div id="shiftdataINVEdit" style="cursor:default;">
				  <?php
					  if(isset($_GET['unsc'])){
					  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					  $stmt = $mysqli->stmt_init();
					  if($stmt->prepare("SELECT unInventoryControl,unSalesData,SDState,SDTimeStart,CONCAT_WS(' ',EO.EFirstName,EO.EMiddleName,EO.ELastName) as unEmployee,SDNetSales
										  FROM salesdata
										  LEFT JOIN employee AS `EO` ON salesdata.unEmployee = EO.unEmployee
										  WHERE unBranch=? AND unSalesControl = ? AND salesdata.Status=1 ORDER BY unBranch DESC")){
						  $stmt->bind_param('ii',$_GET['bid'],$_GET['unsc']);
						  $stmt->execute();
						  $stmt->bind_result($unInventoryControl,$unSalesData,$SDState,$SDTimeStart,$unEmployee,$SDNetSales);
						  
						  while($stmt->fetch()){
							  $unSalesData1=sprintf('%06d', $unSalesData);
							  ?>
							  <div class="listviewitem" id="listviewitemSDINVEdit-<?php echo $unSalesData; ?>" style="cursor:default;"  onClick="SEThdnunSDINVEdit(<?php echo $unSalesData; ?>,<?php echo $_GET['bid'];?>,<?php echo $unInventoryControl; ?>)">
                              		<input id="SDIDINVEdit-<?php echo $unSalesData; ?>" value="<?php echo $unSalesData; ?>" hidden="hidden">
								  <div class="listviewsubitem" id="listviewsubitemSDIDINVEdit-<?php echo $unSalesData; ?>" style="width:80px; text-align:left;background-image:url(img/icon/<?php if($SDState=='Close'){echo 'SDclosed';}else{echo 'SDopen';};?>.png);background-size:contain; position:relative; background-repeat:no-repeat; padding-left:18px;" ><?php echo $unSalesData1; ?></div>
								  <div class="listviewsubitem" style="width:100px;"><?php echo date('H:i:sa',strtotime($SDTimeStart)); ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $unEmployee; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $SDNetSales; ?></div>
								  <div class="selectedSDINVEdit" id="selectedSDINVEdit-<?php echo $unSalesData; ?>" style="padding-left:30px;padding-top:5px; color:<?php if($unInventoryControl==0){echo 'green';}else{echo 'red';};?>;"><?php if($unSalesData==$_GET['unsd']){if($unInventoryControl==0){echo 'Day Selected';}else{echo 'Mapped!';}}?></div>
							  </div>
							  <?php
						  }
						  $stmt->close();
					  }
					  }
				  ?>
				  </div>
			  </div>
		  </div>
	  </div>
	  <div style="position:relative; width:800px; height:150px; background-color:#FFF; margin-top:20px;">
		  <div class="listview" id="lvMAP" style="min-height:150px;">
			  <div class="column" id="colMAP">
				  <div class="columnheader" style="width:100px; text-align:left;">Inv #</div>
				  <div class="columnheader" style="width:100px;">NET</div>
				  <div class="columnheader" style="width:150px;">DISCOUNT</div>
				  <div class="columnheader" style="width:150px;">TOTAL</div>
			  </div>
			  <div class="row"  id="INVEdit" style="height:130px;">
				  <?php
					  if(isset($_GET['unsd'])){
					  $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					  $stmt = $mysqli->stmt_init();
					  if($stmt->prepare("SELECT unInvoiceControl,ICNetSales,ICDiscount,ICTotalAmount FROM invoicecontrol
										  WHERE Status=1 AND invoicecontrol.unSalesData = ? AND invoicecontrol.unBranch = ?")){
						  $stmt->bind_param('ii',$_GET['unsd'],$_GET['bid']);
						  $stmt->execute();
						  $stmt->bind_result($unInvoiceControl,$ICNetSales,$ICDiscount,$ICTotalAmount);
						  while($stmt->fetch()){
							  ?>
							  <div class="listviewitem" id="listviewitemINVEdit-<?php echo $unInvoiceControl; ?>" onClick="SEThdnunINVEdit('<?php echo $unInvoiceControl; ?>','<?php echo $_POST['bid'] ?>')">
								  <div class="listviewsubitem" style="width:100px;"><?php echo $unInvoiceControl; ?></div>
								  <div class="listviewsubitem" style="width:100px;"><?php echo $ICNetSales; ?></div>
								<div class="listviewsubitem" style="width:150px;"><?php echo $ICDiscount; ?></div>
								  <div class="listviewsubitem" style="width:150px;"><?php echo $ICTotalAmount; ?></div>
								  <div class="selectedINVEdit" id="selectedINVEdit-<?php echo $unInvoiceControl; ?>" style="padding-left:30px;padding-top:5px; color:green;"></div>	
							  </div>
							  <?php
						  }$stmt->close();
					  }
					  }
					  ?>
			  </div>
		  </div>
	  </div>
		  <div align="center" style="padding-top:10px; position:relative">
			  <input type="hidden" id="hdnunSCINVEdit" name="hdnunSCINVEdit" value="<?php if(isset($_GET['unsc'])){echo $_GET['unsc'];}else{echo '0';}?>">
			<input type="hidden" id="hdnunSDINVEdit" name="hdnunSDINVEdit" value="<?php if(isset($_GET['unsd'])){echo $_GET['unsd'];}else{echo '0';}?>">
			  <input type="hidden" id="hdnunINVEdit" name="hdnunINVEdit" value="0">
			  <input type="hidden" id="hdnunBIDINVEdit" name="hdnunBIDINVEdit" value="<?php echo $_GET['bid']?>">
			  <input name="btnSelectINVEdit" id="btnSelectINVEdit" type="button" value="Edit Invoice" title="Edit Invoice" class="buttons" disabled/>
			  <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
		  </div>
  </div>
</div>

<div id="INVEditSaved" class="popup">
 <div class="popupcontainer" style="width:200px; top:-60px; height:60px;" align="center">
    <div class="popuptitle" align="center">Transaction Saved!</div>
    <input type="button" value="Close" title="Close" onClick="location.href='#close'" class="buttons" >
 </div>
</div>


<?php
  include 'footer.php';
?>
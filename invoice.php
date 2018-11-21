<?php
	include 'header.php';
	session_start();
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("SELECT invoicecontrol.unBranch,BName,unInventoryControl,ICNumber,ICDate,ICPax,ICTotalSales,ICTotalDiscount,ICVatable,ICNetOfVaT,ICTaxAmount,ICVatExempt,ICVatExSales,ICVatExAmount,ICNetSales,ICCard,ICGC,ICLOA,ICCash,ICPaidAmount,ICChange
						FROM invoicecontrol
						inner join branch on branch.unBranch=invoicecontrol.unBranch Where unInvoiceControl=?")){
		$stmt->bind_param("i",$_GET['id']);
		$stmt->execute();
		$stmt->bind_result($unBranch,$BName,$unInventoryControl,$ICNumber,$ICDate,$ICPax,$ICTotalSales,$ICTotalDiscount,$ICVatable,$ICNetOfVaT,$ICTaxAmount,$ICVatExempt,$ICVatExSales,$ICVatExAmount,$ICNetSales,$ICCard,$ICGC,$ICLOA,$ICCash,$ICPaidAmount,$ICChange);
		$stmt->fetch();
		$stmt->close();
	}else{
		die($stmt->error);
	}
?>
<script src="js/invoice.js"></script>

<script type="text/javascript">
function getcash(idInvoiceControl){
		$.post('ajax/invoice.ajax.php',
		{
			qid:'getcash',
			idic:idInvoiceControl
		},
		function(data,status){
			$('#txtpopupcash').val(data);
			location.href="#popupcash"
		});
}
function deletecreditcard(idInvoiceControl,idCardTransaction){
		$.post('ajax/invoice.ajax.php',
		{
			qid:'delcreditcard',
			idic:idInvoiceControl,
			idct:idCardTransaction
		},
		function(data,status){
			location.href=data
		});
}
function getgiftcertificate(idInvoiceControl,idGiftCertificateTransaction){
		$.post('ajax/invoice.ajax.php',
		{
			qid:'delgc',
			idic:idInvoiceControl,
			idgct:idGiftCertificateTransaction
		},
		function(data,status){
			location.href=data
		});
}
function deleteitem(idInvoiceControl,idInvoiceData){
		$.post('ajax/invoice.ajax.php',
		{
			qid:'delitem',
			idic:idInvoiceControl,
			idid:idInvoiceData
		},
		function(data,status){
			location.href=data
		});
}
</script>
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
<input type="button" class="toolbarbutton" title="Save" name="btnsave" onclick="lockcontrols(true)" style="background-image:url(img/icon/save.png);background-repeat:no-repeat;background-position:center;" >

<input type="button" class="toolbarbutton" title="Letter of Authorization" name="btnletterofauthorization" onclick="location.href='#popuploa'" style="background-image:url(img/icon/letterofauthorization.png);background-repeat:no-repeat;background-position:center;float:right;" >

<input type="button" class="toolbarbutton" title="Gift Certificate" name="btngiftcertificate" onclick="location.href='#popupgc'" style="background-image:url(img/icon/giftcertificate.png);background-repeat:no-repeat;background-position:center;float:right;" >

<input type="button" class="toolbarbutton" title="Credit Card" name="btncreditcard" onclick="location.href='#popupcreditcard'" style="background-image:url(img/icon/creditcard.png);background-repeat:no-repeat;background-position:center;float:right;" >
 
<input type="button" class="toolbarbutton" title="Cash" name="btncash" onclick="getcash(<?php echo $_GET['id']; ?>)" style="background-image:url(img/icon/pettycash.png);background-repeat:no-repeat;background-position:center;float:right;" >

<input type="button" class="toolbarbutton" title="Discount" name="btndiscount" onclick="location.href='#popupdiscount'" style="background-image:url(img/icon/discount.png);background-repeat:no-repeat;background-position:center;float:right;" >

</div>

<div class="headbox" style="border-right:solid thin #999;">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Control Number</div>
        <div class="headboxlistsubitem" id="divinvoicenumber" style="width:60%;text-align:right;font-weight:bold;"><?php echo $_GET['id']; ?></div>
    </div>
    
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Branch</div>
        <div class="headboxlistsubitem" id="divbranch" style="width:60%;text-align:right;font-weight:bold;"><?php echo $BName; ?></div>
    </div>
    
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Inv Control Number</div>
        <div class="headboxlistsubitem" id="divinvoicenumber" style="width:60%;text-align:right;font-weight:bold;"><?php echo $unInventoryControl; ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Invoice Number</div>
        <div class="headboxlistsubitem" id="divinvoicenumber" style="width:60%;text-align:right;font-weight:bold;"><?php echo $ICNumber; ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Date</div>
        <div class="headboxlistsubitem" id="divdate" style="width:60%;text-align:right;font-weight:bold;"><?php echo $ICDate; ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Pax</div>
        <div class="headboxlistsubitem" id="divpax" style="width:60%;text-align:right;font-weight:bold;"><?php echo $ICPax; ?></div>
    </div>
</div>

<div class="headbox" style="border-right:solid thin #999;">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">VATable</div>
        <div class="headboxlistsubitem" id="divvatable" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICVatable); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Net Of VAT</div>
        <div class="headboxlistsubitem" id="divnonvatable" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICNetOfVaT); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Tax Amount</div>
        <div class="headboxlistsubitem" id="divvatsales" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICTaxAmount); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">VAT Exempt</div>
        <div class="headboxlistsubitem" id="divvatamount" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICVatExempt); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">VAT Ex Sales</div>
        <div class="headboxlistsubitem" id="divvatamount" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICVatExSales); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">VAT Ex Amount</div>
        <div class="headboxlistsubitem" id="divvatamount" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICVatExAmount); ?></div>
    </div>
</div>

<div class="headbox" style="border-right:solid thin #999;">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Total Sales</div>
        <div class="headboxlistsubitem" id="divtotal" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',($ICTotalSales)); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Discount</div>
        <div class="headboxlistsubitem" id="divdiscount" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',($ICTotalDiscount)); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Net Sales</div>
        <div class="headboxlistsubitem" id="divnetsales" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',($ICNetSales)); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Paid Amount</div>
        <div class="headboxlistsubitem" id="divdue" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',($ICPaidAmount)); ?></div>
    </div>
 
 	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Change</div>
        <div class="headboxlistsubitem" id="divnetsales" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',($ICChange)); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">GC Forfeit</div>
        <div class="headboxlistsubitem" id="divnetsales" style="width:60%;text-align:right;font-weight:bold;">0.00</div>
    </div>       
</div>

<div class="headbox" style="border-right:solid thin #999;">
	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Cash</div>
        <div class="headboxlistsubitem" id="divcash" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICCash); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Credit Card</div>
        <div class="headboxlistsubitem" id="divcreditcard" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICCard); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Gift Certificate</div>
        <div class="headboxlistsubitem" id="divgc" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICGC); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;">Letter Of Authorization</div>
        <div class="headboxlistsubitem" id="divloa" style="width:60%;text-align:right;font-weight:bold;"><?php echo money_format('%i',$ICLOA); ?></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;"></div>
        <div class="headboxlistsubitem" id="divloa" style="width:60%;text-align:right;font-weight:bold;"></div>
    </div>

	<div class="headboxlistitem">
        <div class="headboxlistsubitem" style="width:40%;"></div>
        <div class="headboxlistsubitem" id="divloa" style="width:60%;text-align:right;font-weight:bold;"></div>
    </div>
</div>

<div class="listview" id="lvinvoicedata">
	<div class="column" id="colinvoicedata">
    	<div class="columnheader" style="width:150px;text-align:left;">QUANTITY</div>
    	<div class="columnheader" style="width:400px;text-align:left;">ITEM</div>
    	<div class="columnheader" style="width:150px;text-align:right;">UNIT PRICE</div>
    	<div class="columnheader" style="width:150px;text-align:right;">TOTAL</div>
    </div>
    
    <div class="row" id="rowinvoicedata">
    	<div class="lvitem" id="lvitem-0">
			<form method="post" action="include/invoice.fnc.php">
                <div class="listviewsubitem" style="width:150px;text-align:left;">
                    <input type="text" name="txtquantity" id="txtquantity" style="position:relative;top:0px;left:0px;width:100%;" onKeyPress="return disableEnterKey(event)">
                </div>
                <div class="listviewsubitem" style="width:400px;">
                    <input type="text" name="txtproductitemsearch" id="txtproductitemsearch" style="position:relative;top:0px;left:0px;width:100%;" onKeyPress="return disableEnterKey(event)">
                </div>
                <div class="listviewsubitem" style="width:150px;text-align:right;">
                    <input type="text" name="txtunitprice" id="txtunitprice" style="position:relative;top:0px;left:0px;width:100%;text-align:right;" onKeyPress="return disableEnterKey(event)" readonly>
                </div>
                <div class="listviewsubitem" style="width:150px;text-align:right;">
                    <input type="text" name="txttotal" id="txttotal" style="position:relative;top:0px;left:0px;width:100%;text-align:right;" onKeyPress="return disableEnterKey(event)" readonly>
                </div>

                <input type="hidden" name="hdntxtic" value="<?php echo $_GET['id']; ?>" />
                <input type="hidden" name="hdnidproductitem" id="hdnidproductitem" value="0">
                <div class="listviewsubitem" style="width:150px;">
                    <input type="submit" class="button16" name="btnadditem" id="btnadditem" title="Add" style="background-image:url(img/icon/add.png);margin-left:10px;border:none;width:16px;height:16px;margin-top:5px;background-color:transparent;cursor:pointer;">
                </div>
            </form>
        </div>

		<?php
			$i=1;
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("SELECT unInvoiceData,invoicedata.unProductItem,PIName,IDQuantity,IDUnitPrice,IDTotalAmount 
							FROM invoicedata 
							INNER JOIN productitem on invoicedata.unProductItem=productitem.unProductItem
							WHERE invoicedata.`Status`=1 And unInvoiceControl=?")){
				$stmt->bind_param("i",$_GET['id']);
				$stmt->execute();
				$stmt->bind_result($unInvoiceData,$unProductItem,$PIName,$IDQuantity,$IDUnitPrice,$IDTotalAmount);
				while($stmt->fetch()){
		?>
                <div class="listviewitem" id="lvitemdata-<?php echo $i; ?>">
                    <div class="listviewsubitem" style="width: 140px;"><input value="<?php echo money_format('%i',$IDQuantity); ?>" name="txtitemquantity-<?php echo $i; ?>" readonly style="width: 100%; background-color: transparent; border: none; text-align: right;"></div>
                    <div class="listviewsubitem" style="width: 400px;"><input value="<?php echo $PIName; ?>" id="txtitemname-<?php echo $i; ?>" readonly style="width: 100%; background-color: transparent; border: none;"></div>
                    <div class="listviewsubitem" style="width: 150px;"><input value="<?php echo money_format('%i',$IDUnitPrice); ?>" name="txtitemprice-<?php echo $i; ?>" readonly style="width: 100%; background-color: transparent; border: none; text-align: right;"></div>
                    <div class="listviewsubitem" style="width: 150px;"><input value="<?php echo money_format('%i',$IDTotalAmount); ?>" id="txtitemtotal-<?php echo $i; ?>" readonly style="width: 100%; background-color: transparent; border: none; text-align: right;"></div>
                    <div class="listviewsubitem" style="width: 150px;"><div class="button16" style="background-image: url(http://10.1.1.3/rnmdir/img/icon/delete.png); padding-top: 5px; padding-left: 0px;" onclick="deleteitem(<?php echo $i; ?>,<?php echo $idInvoiceData; ?>)"></div></div>
                    <input type="hidden" name="hdnitemid-<?php echo $i; ?>" value="0">
                </div>
        <?php
					$i++;
				}
				$stmt->close();
			}else{
				die($stmt->error);
			}
		?>
    </div>

    <div class="listbox" id="lstresult" style="position:fixed;width:300px;max-height:240px;display:none;z-index:99999;">
    </div>
</div>

<div id="popupdiscount" class="popup">
    <div id="getdiscount" class="popupcontainer" style="width:630px;">
        <div class="popuptitle" align="center">Discount</div>
        <form method="post" action="include/invoice.fnc.php">
            <div class="popupitem">
                <div class="popupitemlabel">Card Type</div>
                    <select name="cmbpopuptype" style="width:200px;text-align:right">
                <?php
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("Select unCard,CName from card Where `Status`=1")){
						$stmt->execute();
						$stmt->bind_result($unCard,$CName);
						while($stmt->fetch()){
				?>
                            <option value="<?php echo $unCard; ?>"><?php echo $CName; ?></option>
                <?		}
						$stmt->close();
					}else{
						die($stmt->error);
					}
                ?>
                    </select>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Terminal</div>
                    <select name="cmbpopupterminal" style="width:200px;text-align:right">
                <?php
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("Select unCardTerminal,CTName from cardterminal Where `Status`=1")){
						$stmt->execute();
						$stmt->bind_result($unCardTerminal,$CTName);
						while($stmt->fetch()){
				?>
                            <option value="<?php echo $unCardTerminal; ?>"><?php echo $CTName; ?></option>
                <?		}
						$stmt->close();
					}else{
						die($stmt->error);
					}
                ?>
                    </select>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Number</div><input name="txtpopupnumber" id="txtpopupnumber" type="text" style="width:195px;text-align:right" required>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Customer</div><input name="txtpopupcustomer" id="txtpopupcustomer" type="text" style="width:195px;text-align:right" required>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Trace</div><input name="txtpopuptrace" id="txtpopuptrace" type="text" style="width:195px;text-align:right" required>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Approval</div><input name="txtpopupapproval" id="txtpopupapproval" type="text" style="width:195px;text-align:right" required>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Amount</div><input name="txtpopupamount" id="txtpopupamount" type="text" style="width:195px;text-align:right" required>
            </div>
            <div class="listview" id="lvarea" style="position:absolute;top:50px;left:350px;height:225 px;width:300px;">
                <div class="column" id="colarea">
                    <div class="columnheader" style="width:75px;">Card</div>
                    <div class="columnheader" style="width:60px;text-align:right;">Number</div>
                    <div class="columnheader" style="width:75px;text-align:right;">Amount</div>
                    <div class="columnheader" style="width:60px;text-align:right;">Remove</div>
	            </div>
                <div class="row" id="rowarea">
                <?php
					$i=0;
					$mysql=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt=$mysql->stmt_init();
					if($stmt->prepare("Select unCardTransaction,CName,CTNumber,CTAmount From cardtransaction 
										Inner Join card on cardtransaction.unCard=card.unCard 
										Where cardtransaction.`Status`=1 and unInvoiceControl=?")){
						$stmt->bind_param("i",$_GET['id']);
						$stmt->execute();
						$stmt->bind_result($unCardTransaction,$CName,$CTNumber,$CTAmount);
						while($stmt->fetch()){
							$i++;
                ?>
                            <div class="listviewitem" style="cursor:pointer">
                            	<div class="listviewsubitem" style="width:75px;"><?php echo $CName; ?></div>
                            	<div class="listviewsubitem" style="width:60px;text-align:right;"><?php echo $CTNumber; ?></div>
                            	<div class="listviewsubitem" style="width:75px;text-align:right;"><?php echo $CTAmount; ?></div>
                                <div class="listviewsubitem" style="width:60px;text-align:right;"><img src="img/icon/delete.png" onclick="deletecreditcard(<?php echo $_GET['id']; ?>,<?php echo $unCardTransaction; ?>)" /></div>
							</div>
				<?php
						}
						$stmt->close();
					}
				?>
                </div>
            </div>

			<input type="hidden" name="hdntxtic" value="<?php echo $_GET['id']; ?>" />
            <div align="center">
                <input name="btnsavecreditcard" type="submit" value="Add Credit Card" title="Add Credit Card" class="buttons" >
                <input name="btncancelcreditcard" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="popupcash" class="popup">
    <div id="getcash" class="popupcontainer">
        <div class="popuptitle" align="center">Cash Payment</div>
        <form method="post" action="include/invoice.fnc.php">
            <div class="popupitem">
                <div class="popupitemlabel">Enter Amount</div><input name="txtpopupcash" id="txtpopupcash" type="text" style="width:195px;text-align:right" required>
            </div>
			<input type="hidden" name="hdntxtic" value="<?php echo $_GET['id']; ?>" />
            <div align="center">
                <input name="btnsavecash" type="submit" value="Save" title="Save Cash" class="buttons" >
                <input name="btncancelcash" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="popupcreditcard" class="popup">
    <div id="getcreditcard" class="popupcontainer" style="width:630px;">
        <div class="popuptitle" align="center">Credit Card Payment</div>
        <form method="post" action="include/invoice.fnc.php">
            <div class="popupitem">
                <div class="popupitemlabel">Card Type</div>
                    <select name="cmbpopuptype" style="width:200px;text-align:right">
                <?php
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("Select unCard,CName from card Where `Status`=1")){
						$stmt->execute();
						$stmt->bind_result($unCard,$CName);
						while($stmt->fetch()){
				?>
                            <option value="<?php echo $unCard; ?>"><?php echo $CName; ?></option>
                <?		}
						$stmt->close();
					}else{
						die($stmt->error);
					}
                ?>
                    </select>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Terminal</div>
                    <select name="cmbpopupterminal" style="width:200px;text-align:right">
                <?php
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("Select unCardTerminal,CTName from cardterminal Where `Status`=1")){
						$stmt->execute();
						$stmt->bind_result($unCardTerminal,$CTName);
						while($stmt->fetch()){
				?>
                            <option value="<?php echo $unCardTerminal; ?>"><?php echo $CTName; ?></option>
                <?		}
						$stmt->close();
					}else{
						die($stmt->error);
					}
                ?>
                    </select>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Number</div><input name="txtpopupnumber" id="txtpopupnumber" type="text" style="width:195px;text-align:right" required>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Customer</div><input name="txtpopupcustomer" id="txtpopupcustomer" type="text" style="width:195px;text-align:right" required>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Trace</div><input name="txtpopuptrace" id="txtpopuptrace" type="text" style="width:195px;text-align:right" required>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Approval</div><input name="txtpopupapproval" id="txtpopupapproval" type="text" style="width:195px;text-align:right" required>
			</div>
            <div class="popupitem">                
                <div class="popupitemlabel">Amount</div><input name="txtpopupamount" id="txtpopupamount" type="text" style="width:195px;text-align:right" required>
            </div>
            <div class="listview" id="lvarea" style="position:absolute;top:50px;left:350px;height:225 px;width:300px;">
                <div class="column" id="colarea">
                    <div class="columnheader" style="width:75px;">Card</div>
                    <div class="columnheader" style="width:60px;text-align:right;">Number</div>
                    <div class="columnheader" style="width:75px;text-align:right;">Amount</div>
                    <div class="columnheader" style="width:60px;text-align:right;">Remove</div>
	            </div>
                <div class="row" id="rowarea">
                <?php
					$i=0;
					$mysql=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt=$mysql->stmt_init();
					if($stmt->prepare("Select unCardTransaction,CName,CTNumber,CTAmount From cardtransaction 
										Inner Join card on cardtransaction.unCard=card.unCard 
										Where cardtransaction.`Status`=1 and unInvoiceControl=?")){
						$stmt->bind_param("i",$_GET['id']);
						$stmt->execute();
						$stmt->bind_result($unCardTransaction,$CName,$CTNumber,$CTAmount);
						while($stmt->fetch()){
							$i++;
                ?>
                            <div class="listviewitem" style="cursor:pointer">
                            	<div class="listviewsubitem" style="width:75px;"><?php echo $CName; ?></div>
                            	<div class="listviewsubitem" style="width:60px;text-align:right;"><?php echo $CTNumber; ?></div>
                            	<div class="listviewsubitem" style="width:75px;text-align:right;"><?php echo $CTAmount; ?></div>
                                <div class="listviewsubitem" style="width:60px;text-align:right;"><img src="img/icon/delete.png" onclick="deletecreditcard(<?php echo $_GET['id']; ?>,<?php echo $unCardTransaction; ?>)" /></div>
							</div>
				<?php
						}
						$stmt->close();
					}
				?>
                </div>
            </div>

			<input type="hidden" name="hdntxtic" value="<?php echo $_GET['id']; ?>" />
            <div align="center">
                <input name="btnsavecreditcard" type="submit" value="Add Credit Card" title="Add Credit Card" class="buttons" >
                <input name="btncancelcreditcard" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="popupgc" class="popup">
    <div id="getgc" class="popupcontainer" style="width:630px;height:200px;">
        <div class="popuptitle" align="center">Gift Certificate Payment</div>
        <form method="post" action="include/invoice.fnc.php">
                <div class="popupitemlabel">Certificate Type</div>
 			<div class="popupitem">
                    <select name="cmbpopupcardtype" style="width:200px;text-align:right">
                <?php
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("SELECT unGiftCertificate,GCName FROM giftcertificate WHERE `Status`=1")){
						$stmt->execute();
						$stmt->bind_result($unGiftCertificate,$GCName);
						while($stmt->fetch()){
				?>
                            <option value="<?php echo $unGiftCertificate; ?>"><?php echo $GCName; ?></option>
                <?		}
						$stmt->close();
					}else{
						die($stmt->error);
					}
                ?>
                    </select>
			</div>
			<div class="popupitem">
                <div class="popupitemlabel">Reference No.</div><input name="txtpopupreference" id="txtpopupreference" type="text" style="width:195px;text-align:right" required>
            </div>
            <div class="listview" id="lvarea" style="position:absolute;top:50px;left:350px;height:225 px;width:300px;">
                <div class="column" id="colarea">
                    <div class="columnheader" style="width:75px;">Card</div>
                    <div class="columnheader" style="width:60px;text-align:right;">Number</div>
                    <div class="columnheader" style="width:75px;text-align:right;">Amount</div>
                    <div class="columnheader" style="width:60px;text-align:right;">Remove</div>
	            </div>
                <div class="row" id="rowarea">
                <?php
					$i=0;
					$mysql=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt=$mysql->stmt_init();
					if($stmt->prepare("SELECT unGiftCertificateTransaction,GCName,GCTReferenceNumber,GCTAmount 
									FROM giftcertificatetransaction 
									INNER JOIN giftcertificate on giftcertificatetransaction.unGiftCertificate=giftcertificate.unGiftCertificate
									WHERE giftcertificatetransaction.`Status`=1 and `unInvoiceControl`=?")){
						$stmt->bind_param("i",$_GET['id']);
						$stmt->execute();
						$stmt->bind_result($unGiftCertificateTransaction,$GCName,$GCTReferenceNumber,$GCTAmount);
						while($stmt->fetch()){
							$i++;
                ?>
                            <div class="listviewitem" style="cursor:pointer">
                            	<div class="listviewsubitem" style="width:75px;"><?php echo $GCName; ?></div>
                            	<div class="listviewsubitem" style="width:60px;text-align:right;"><?php echo $GCTReferenceNumber; ?></div>
                            	<div class="listviewsubitem" style="width:75px;text-align:right;"><?php echo $GCTAmount; ?></div>
                                <div class="listviewsubitem" style="width:60px;text-align:right;"><img src="img/icon/delete.png" onclick="getgiftcertificate(<?php echo $_GET['id']; ?>,<?php echo $unGiftCertificateTransaction; ?>)" /></div>
							</div>
				<?php
						}
						$stmt->close();
					}
				?>
                </div>
            </div>

			<input type="hidden" name="hdntxtic" value="<?php echo $_GET['id']; ?>" />
            <br /><br /><br /><br /><br />
            <div align="center">
                <input name="btnsavegc" type="submit" value="Add GC" title="Add Gift Certificate" class="buttons" >
                <input name="btncancelgc" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="popuploa" class="popup">
    <div id="getloa" class="popupcontainer">
        <div class="popuptitle" align="center">Letter of Authorization Payment</div>
        <?php
			$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("Select unLetterOfAuthorizationTransaction,LOATCompanyName,LOATReferenceNumber,LOATAmount From letterofauthorizationtransaction Where unInvoiceControl=?")){
				$stmt->bind_param("i",$_GET['id']);
				$stmt->execute();
				$stmt->bind_result($unLetterOfAuthorizationTransaction,$LOATCompanyName,$LOATReferenceNumber,$LOATAmount);
				$stmt->fetch();
				$stmt->close();
			}else{
				die($stmt->error);
			}
        ?>
        <form method="post" action="include/invoice.fnc.php">
            <div class="popupitem">
                <div class="popupitemlabel">Company</div><input name="txtpopuploacompany" id="txtpopuploacompany" type="text" style="width:195px;text-align:right" value="<?php echo $LOATCompanyName; ?>" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Reference</div><input name="txtpopuploareference" id="txtpopuploareference" type="text" style="width:195px;text-align:right" value="<?php echo $LOATReferenceNumber; ?>" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Enter Amount</div><input name="txtpopuploaamount" id="txtpopuploaamount" type="text" style="width:195px;text-align:right" value="<?php echo $LOATAmount; ?>" required>
            </div>
			<input type="hidden" name="hdntxtic" value="<?php echo $_GET['id']; ?>" />
            <div align="center">
                <input name="btnsaveloa" type="submit" value="Save" title="Save Letter of Authorization" class="buttons" >
                <input name="btncancelloa" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>


<?php
	include 'footer.php';
?>
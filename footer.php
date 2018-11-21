<script type="text/javascript">

function CheckHasTemplate(idBranch){
	var xmlhttp;
	
	if(idBranch==0){
		return 0;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText==0){
				msgbox('Cannot create inventory sheet. The template of this branch has no items.','#','');
			}else{
				document.getElementById('hdnCreateInventory').value = 'Create';
				document.getElementById('frmnewinventorysheet').submit();
			}
		}
	}
	
	xmlhttp.open('POST','ajax/transfer.ajax.php',false);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=CheckHasTemplate&bid='+idBranch);
}

function enabledisableComboBox(i)
{
	if (i==0){
		document.getElementById('cmbCopyFrom').disabled	= true;
	}else{
		document.getElementById('cmbCopyFrom').disabled	= false;
	}
}

$(document).ready(function() {
	var h = $('#lvaddemployeearea').height()-$('#coladdemployeearea').height();
	$('#rowaddemployeearea').height(h);
});


</script>

<?php 

	if(isset($_POST['btnproducttemplatedelete']))
	{
		$iddelete = $_POST['iddelete'];
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if ($stmt->prepare("UPDATE templateitemcontrol SET `Status`=0 WHERE unTemplateItemControl=?"))
		{
			$stmt->bind_param("i", $undelete);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}
	
	
?>

<div id="createinventorysheet" class="popup">
	<div class="popupcontainer">
    	<div class="popuptitle" align="center">Create New Inventory Sheet</div>
        <form name="frmnewinventorysheet" id="frmnewinventorysheet" action="include/inventory.fnc.php" method="post" >
        <div class="popupitem">
        	<div class="popupitemlabel">Branch</div>
        	<select name="cmbBranch" id="cmbCITFBranch" style="width:200px;" required >
			<?php 
                $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt=$mysqli->stmt_init();
                if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName")){
				$stmt->bind_param('i',$_SESSION['area']);
                $stmt->execute();
                $stmt->bind_result($unBranch,$BName);
                while($stmt->fetch()){
					?>
					<option value="<?php echo $unBranch; ?>" <?php echo ($bid==$unBranch)?'Selected':''; ?> ><?php echo $BName; ?></option>
					<?php
                    }
                $stmt->close();
                }
            ?>
            </select>
		</div>
        <div class="popupitem"> <div class="popupitemlabel">Date</div><input name="dtpDate" type="date" style="width:195px; height:20px;" required > </div>
        <div class="popupitem"> <div class="popupitemlabel">Sheet Number</div><input name="txtSheetNumber" type="text" style="width:195px; height:20px;" required > </div>
		<div class="popupitem"> <div class="popupitemlabel">Remarks</div><textarea name="txtRemark" style="max-width:292px; width:292px; height:80px; resize:none;" title="Remarks" ></textarea> </div>
        <div align="center">
        	<input type="hidden" name="hdnCreateInventory" id="hdnCreateInventory" value="" >
            <input name="btnAddInventorySheet" id="btnAddInventorySheet" type="button" value="Create" title="Create" class="buttons" onClick="CheckHasTemplate(cmbCITFBranch.value)" >
            <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
		</div>
        </form>
    </div>
</div>

<div id="createinvoice" class="popup">
	<div class="popupcontainer">
    	<div class="popuptitle" align="center">Create New Invoice</div>
        <form name="frmnewinvoice" id="frmnewinvoice" action="include/invoice.fnc.php" method="post" >
        <div class="popupitem">
        	<div class="popupitemlabel">Branch</div>
        	<select name="cmbBranch" id="cmbBranch" style="width:200px;" required >
			<?php 
                $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt=$mysqli->stmt_init();
                if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName")){
				$stmt->bind_param('i',$_SESSION['area']);
                $stmt->execute();
                $stmt->bind_result($unBranch,$BName);
                while($stmt->fetch()){
					?>
					<option value="<?php echo $unBranch; ?>" <?php echo ($bid==$unBranch)?'Selected':''; ?> ><?php echo $BName; ?></option>
					<?php
                    }
                $stmt->close();
                }
            ?>
            </select>
		</div>
        <div class="popupitem"> <div class="popupitemlabel">Invoice</div><input type="number" name="txtinvoice" id="txtinvoice" value="1" style="width:195px;height:20px;" min="1" required></div>
        <div class="popupitem"> <div class="popupitemlabel">Date</div><input name="dtpDate" type="date" style="width:195px; height:20px;" required></div>
        <div class="popupitem"> <div class="popupitemlabel">Pax</div><input type="number" name="txtpax" id="txtpax" value="1" style="width:195px;height:20px;" min="1" required></div>
        <div align="center">
        	<input type="hidden" name="hdnCreateInvoice" id="hdnCreateInvoice" value="" >
            <input name="btnCreateInvoice" id="btnCreateInvoice" type="submit" value="Create" title="Create" class="buttons">
            <input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
		</div>
        </form>
    </div>
</div>

<div id="createbranch" class="popup">
    <div id="addbranch" class="popupcontainer">
        <div class="popuptitle" align="center">Create Branch</div>
        <form method="post" action="branch.php">
            <div class="popupitem">
                <div class="popupitemlabel">Branch</div><input name="txtbranch" type="text" style="width:195px;" required>
            </div>
               <?php
					if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
						$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    	$stmt=$mysql->stmt_init();
						?>
				<div class="popupitem">
						<div class="popupitemlabel">Template</div>
						<select name="cmbtemp" id="cmbtemp" style="width:200px;" >
							<?php
								$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
								$stmt=$mysql->stmt_init();
								if($stmt->prepare("SELECT  `unTemplateItemControl` , TICName
									FROM templateitemcontrol
									WHERE templateitemcontrol.`Status` =1
									AND templateitemcontrol.`unArea` =1
									ORDER BY  `TICName` ")){
									$stmt->bind_param("i",$_SESSION['area']);
									$stmt->execute();
									$stmt->bind_result($unTemplateItemControl,$TICName);
									while($stmt->fetch()){
				?>
                    <option value="<?php echo $unTemplateItemControl; ?>"><?php echo $TICName; ?></option>
                <?php
									}
								}
						 $stmt->close();
							?>
						</select>
					</div>
						<?php
					}
				?>
            <div class="popupitem">
                <div class="popupitemlabel">BOM</div>
                <select name="cmbbom" id="cmbbom" style="width:200px;" >
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select templateproductionbatch.`unTemplateItemControl`,TICName,`unTemplateProductionBatch`,TPBName From templateproductionbatch INNER JOIN templateitemcontrol ON  templateitemcontrol.`unTemplateItemControl` = templateproductionbatch.`unTemplateItemControl` Where templateproductionbatch.`Status`=1 AND templateproductionbatch.`unArea`=? Order by `TPBName`")){
						$stmt->bind_param("i",$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($unTemplateItemControl,$TICName,$unTemplateProductionBatch,$TPBName);
                        while($stmt->fetch()){
							if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
                ?>
                    <option value="<?php echo $unTemplateProductionBatch; ?>"><?php echo $TPBName; ?></option>
                <?php
							}else{
				 ?>
                    <option value="<?php echo $unTemplateProductionBatch; ?>"><?php echo $TPBName; ?>[ Template: <?php echo $TICName; ?>]</option>
                <?php				
							}
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>
            <!--<div class="popupitem">
                <div class="popupitemlabel">Template</div>
                <select name="cmbtemplate" id="cmbtemplate" style="width:200px;" disabled>
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unTemplateItemControl,TICName From templateitemcontrol Where `Status`=1 And unArea=? Order by TICName")){
						$stmt->bind_param("i",$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($unTemplateItemControl,$TICName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unTemplateItemControl; ?>"><?php echo $TICName; ?></option>
                <?php
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>-->
            <div class="popupitem">
                <div class="popupitemlabel">SAP Code</div><input name="txtsapcode" type="text" style="width:195px;">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Type</div>
                <select name="cmbbranchtype" style="width:200px;">
                	<option value="1">Outlet</option>
                	<option value="2">Commi</option>
                	<option value="3">Office</option>
                </select>
            </div>
            <div class="popupitem" style="height:20px; border-bottom:thin solid #999;">
	            <div class="popupitemlabel">Quota:</div>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Amount</div><input autocomplete="off" name="txtquota" type="text" style="width:195px; text-align:right;" placeholder="0.0000" value="0">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Interval</div><input autocomplete="off" name="txtquotainterval" type="text" style="width:195px; text-align:right;" placeholder="0.0000" value="0">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Amount/Point</div><input autocomplete="off" name="txtquotapoint" type="text" style="width:195px; text-align:right;" placeholder="0.0000" value="0">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Description</div>
                <textarea name="txtdescription" style="max-width:292px; width:292px; height:80px; resize:none;" title="Remarks" ></textarea>
            </div>
            <div align="center">
                <input name="btnbranchadd" type="submit" value="Add" title="Add Branch" onClick="" class="buttons" >
                <input name="btnbranchaddcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>


<div id="createemployee" class="popup">
    <div class="popupcontainer" style="width:630px;">
        <div class="popuptitle" align="center">Create Employee</div>
        <form method="post" action="employee.php">
            <div class="popupitem">
                <div class="popupitemlabel">Last Name</div><input name="txtlastname" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">First Name</div><input name="txtfirstname" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Middle Name</div><input name="txtmiddlename" type="text" style="width:195px;">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Alias</div><input name="txtalias" type="text" style="width:195px;">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Employee No.</div><input name="txtemployeenumber" type="text" style="width:195px;">
            </div>
            

            <div class="popupitem">
                <div class="popupitemlabel">Group</div>
                <select name="cmbgroup" id="cmbgroup" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unEmployeeGroup,EGName From employeegroup Where `Status`=1 Order by EGName")){
                        $stmt->execute();
                        $stmt->bind_result($unEmployeeGroup,$GName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unEmployeeGroup; ?>"><?php echo $GName; ?></option>
                <?php
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>
            
            <div class="popupitem">
                <div class="popupitemlabel">Username</div><input name="txtemployeeusername" type="text" style="width:195px;">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Password</div><input name="txtemployeepassword" type="text" style="width:195px;">
            </div>
            
            <div class="listview" id="lvaddemployeearea" style="position:absolute;top:50px;left:350px;height:200px;width:300px;">
                <div class="column" id="coladdemployeearea">
                    <div class="columnheader">Area</div>
	            </div>
                <div class="row" id="rowaddemployeearea">
                <?php
					$i=0;
					$mysql=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt=$mysql->stmt_init();
					if($stmt->prepare("Select unArea,AName From area Where `Status`=1 Order by AName Asc")){
						$stmt->execute();
						$stmt->bind_result($unArea,$AName);
						while($stmt->fetch()){
							$i++;
                ?>
                            <div class="listviewitem"  onClick="chktoggle('chkearea<?php echo $i; ?>')">
                            	<div class="listviewsubitem"><input type="checkbox" id="chkearea<?php echo $i; ?>" name="chkearea<?php echo $i; ?>" value="<?php echo $unArea; ?>" ><?php echo $AName; ?></div>
							</div>
				<?php
						}
						$stmt->close();
					}
				?>
                </div>
            </div>
            <br>
            <br>
            <div align="center">
                <input name="btnemployeeadd" type="submit" value="Add" title="Add Employee" class="buttons" >
                <input name="btnemployeecancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="createdevice" class="popup">
    <div id="adddevice" class="popupcontainer">
        <div class="popuptitle" align="center">Create Device</div>
        <form method="post" action="device.php">
        	<div class="popupitem">
                <div class="popupitemlabel">Device Name</div><input name="txtname" type="text" style="width:195px;">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Branch</div>
                <select name="cbranch" id="cbranch" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 AND unArea=".$_SESSION['area']." Order by BName")){
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unBranch; ?>"><?php echo $BName; ?></option>
                <?php
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>
          	<div class="popupitem">
                <div class="popupitemlabel">Serial Number</div><input name="txtserialnumber" type="text" style="width:195px;">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Mac Address</div><input name="txtmacaddress" type="text" style="width:195px;">
            </div>
            <div align="center">
                <input name="btndeviceadd" type="submit" value="Add" title="Add Device" onClick="" class="buttons" >
                <input name="btndeviceaddcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="createpaymenttype" class="popup">
    <div id="addpaymenttype" class="popupcontainer">
        <div class="popuptitle" align="center">Create Payment Type</div>
        <form method="post" action="paymenttype.php">
            <div class="popupitem">
                <div class="popupitemlabel">Payment Type</div><input name="txtpaymenttype" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Fixed Amount</div><input autocomplete="off" name="txtfixedamount" type="text" style="width:195px; text-align:right;" placeholder="0.0000" value="0">
            </div>
            <div align="center">
                <input name="btnpaymenttypeadd" type="submit" value="Add" title="Add Payment Type" onClick="" class="buttons" >
                <input name="btnpaymenttypeaddcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="creatediscounttype" class="popup">
    <div id="adddiscounttype" class="popupcontainer">
        <div class="popuptitle" align="center">Create Discount Type</div>
        <form method="post" action="discounttype.php">
            <div class="popupitem">
                <div class="popupitemlabel">Discount Type</div><input name="txtdiscounttype" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Percent</div><input autocomplete="off" name="txtpercent" type="text" style="width:195px; text-align:right;" placeholder="0.0000" value="0">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Amount</div><input autocomplete="off" name="txtamount" type="text" style="width:195px; text-align:right;" placeholder="0.0000" value="0">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Vat Exempt</div><!--<input autocomplete="off" name="txtvatexempt" type="text" style="width:195px; text-align:right;" placeholder="0.0000" value="0">--><input type="radio" name="txtvatexempt" value="1">Yes
<input type="radio" name="txtvatexempt" value="0">No
                
            </div>
            <div align="center">
                <input name="btndiscounttypeadd" type="submit" value="Add" title="Add Discount Type" onClick="" class="buttons" >
                <input name="btndiscounttypeaddcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="createsales" class="popup">
	<div id="addsales" class="popupcontainer">
    	<div class="popuptitle" align="center">Create New Sales</div>
    		<form name="newsale" id="newsale" method="post" action="include/POS.inc.php" >
        		<div class="popupitem">
        			<div class="popupitemlabel">Branch</div>
        			<select name="cmbBranch" id="cmbBranch" style="width:200px;" onChange="loadquota(this.value)" required >
					<?php 
               			 $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                		 $stmt=$mysqli->stmt_init();
               			 if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and Btype=1 and unArea=? Order by BName")){
						 	$stmt->bind_param('i',$_SESSION['area']);
                		 	$stmt->execute();
                		 	$stmt->bind_result($unBranch,$BName);
                			while($stmt->fetch()){
					?>
					<option value="<?php echo $unBranch; ?>" <?php if($unBranch==$_GET['bid']){echo 'selected';} ?>><?php echo $BName; ?></option>
					<?php
                    		}
               				$stmt->close();
                			}
           			?>
            		</select>
				</div>
                <div class="popupitem">
        			<div class="popupitemlabel">Employee Open</div>
        			<select name="cmbEOpen" id="cmbEOpen" style="width:200px;" required >
					<?php 
               			 $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                		 $stmt=$mysqli->stmt_init();
               			 if($stmt->prepare("SELECT employee.unEmployee,ELastName,EFirstName,EMiddleName,unArea FROM employee INNER JOIN employeearea ON employee.unEmployee=employeearea.unEmployee WHERE employee.Status=1 AND employeearea.Status=1 AND unArea=?")){
						  //if($stmt->prepare("SELECT unEmployee,ELastName,EFirstName,EMiddleName FROM employee ")){
						 	$stmt->bind_param('i',$_SESSION['area']);
                		 	$stmt->execute();
                		 	$stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName,$unEA);
							//$stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName);
                			while($stmt->fetch()){
								//if($idEA==$_SESSION['area']){
					?>
                    			
								<option value="<?php echo $unEmployee; ?>"<?php /*echo ($bid==$idBranch)?'Selected':'';*/ ?> ><?php echo $EFirstName." ".$EMiddleName." ".$ELastName; ?></option>
					<?php
								//}
							}
               				$stmt->close();
                			}
           			?>
            		</select>
				</div>
              <div class="popupitem">
               	<div class="popupitemlabel">Time Start</div>
               	  <input type="datetime-local" name="SCtimestart" style="width:170px" required>
              </div>
              <?php $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                	$stmt=$mysqli->stmt_init();
					if($stmt->prepare("Select BQuota,BQuotaInterval,BQuotaPointAmount From branch where unBranch=?")){
					$stmt->bind_param('i',$_GET['bid']);
					$stmt->execute();
					$stmt->bind_result($BQuota,$BQuotaInterval,$BQuotaPointAmount);
					$stmt->fetch();
					$stmt->close();
				} ?>
              <div class="popupitem">
              	<div class="popupitemlabel">Sales Quota</div>
               	  <input type="number" style="width:195px;" id="scquota" name="scquota" style="text-align:right" placeholder="0.00" value="<?php echo $BQuota; ?>">
              </div>
              <div class="popupitem">
              	<div class="popupitemlabel">Quota Interval</div>
               	  <input type="number" style="width:195px;" id="scquotaint" name="scquotaint" style="text-align:right" placeholder="0.00" value="<?php echo $BQuotaInterval; ?>">
              </div>
              <div class="popupitem">
              	<div class="popupitemlabel">Quota Point Amount</div>
               	  <input type="number" style="width:195px;" id="scquotap" name="scquotap" style="text-align:right" placeholder="0.00" value="<?php echo $BQuotaPointAmount; ?>">
              </div>
        			<br/><div align="center" style="width:350px;">
            			<input name="btnCreateSale" id="btnCreateSale" type="submit" value="Create" title="Create" class="buttons">
            			<input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='<?php switch($_SERVER['PHP_SELF']){case $_SESSION['ParentPath'].'createinvoice.php':echo '#selectSCSD';break;case $_SESSION['ParentPath'].'index.php':echo '#close';break;}?>'" class="buttons" >
					</div>
        </form>
    </div>
</div>

<div id="createshift" class="popup">
	<div id="addsales" class="popupcontainer">
    	<div class="popuptitle" align="center">Create New Shift</div>
    		<form name="newsale" id="newsale" action="include/POS.inc.php" method="post" >
        		<div class="popupitem">
        			<div class="popupitemlabel">Branch</div>
        			<select name="cmbBranch" id="cmbBranch" style="width:200px;" onChange="loadsalesday(this.value)" required >
					<?php 
               			 $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                		 $stmt=$mysqli->stmt_init();
               			 if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName")){
						 	$stmt->bind_param('i',$_SESSION['area']);
                		 	$stmt->execute();
                		 	$stmt->bind_result($unBranch,$BName);
                			while($stmt->fetch()){
					?>
					<option value="<?php echo $unBranch; ?>" <?php if($unBranch==$_GET['bid']){echo 'selected';} ?>><?php echo $BName; ?></option>
					<?php
                    		}
               				$stmt->close();
                			}
           			?>
            		</select>
				</div>
                <div class="popupitem">
        			<div class="popupitemlabel">Sales Day</div>
        			<select name="cmbSalesDay" id="cmbSalesDay" style="width:200px;" required >
					<?php 
               			 $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						 $stmt=$mysqli->stmt_init();
						 if($stmt->prepare("Select unSalesControl,SCTimeStart From salescontrol where unBranch=? and SCState='Open' ORDER BY TimeStamp DESC")){
							$stmt->bind_param('i',$_GET['bid']);
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
           			?>
            		</select>
				</div>
                <div class="popupitem">
        			<div class="popupitemlabel">Employee Open</div>
        			<select name="cmbSEOpen" id="cmbSEOpen" style="width:200px;" required >
					<?php 
               			 $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                		 $stmt=$mysqli->stmt_init();
               			 if($stmt->prepare("SELECT employee.unEmployee,ELastName,EFirstName,EMiddleName,unArea FROM employee INNER JOIN employeearea ON employee.unEmployee=employeearea.unEmployee WHERE employee.Status=1 AND employeearea.Status=1 AND unArea=?")){
						  //if($stmt->prepare("SELECT unEmployee,ELastName,EFirstName,EMiddleName FROM employee ")){
						 	$stmt->bind_param('i',$_SESSION['area']);
                		 	$stmt->execute();
                		 	$stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName,$unEA);
							//$stmt->bind_result($unEmployee,$ELastName,$EFirstName,$EMiddleName);
                			while($stmt->fetch()){
								//if($idEA==$_SESSION['area']){
					?>
                    			
								<option value="<?php echo $unEmployee; ?>"<?php /*echo ($bid==$idBranch)?'Selected':'';*/ ?> ><?php echo $EFirstName." ".$EMiddleName." ".$ELastName; ?></option>
					<?php
								//}
							}
               				$stmt->close();
                			}
           			?>
            		</select>
				</div>
              <div class="popupitem">
               	<div class="popupitemlabel">Time Start</div>
                <input type="datetime-local" name="SDtimestart" style="width:170px" value="2014-11-02T06:00:00
                <?php 
               			/* $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						 $stmt=$mysqli->stmt_init();
						 if($stmt->prepare("Select 
						 SCTimeStart From salescontrol where unBranch=? and SCState='Open'")){
							$stmt->bind_param('i',$_GET['bid']);
							$stmt->execute();
							$stmt->bind_result(
							$SCTimeStart);
							echo $SCTimeStart;	
							$stmt->close();
						 }*/
           			?>
                    " step="1" autocomplete="on" required><?php //echo $SCTimeStart;?>
                    <!--<input type="datetime-local" name="SDtimestart" style="width:170px" step="1" autocomplete="on" value="
                    <?php 
               			/*$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						 $stmt=$mysqli->stmt_init();
						 if($stmt->prepare("Select 
						 SCTimeStart From salescontrol where unBranch=? and SCState='Open'")){
							$stmt->bind_param('i',$_GET['bid']);
							$stmt->execute();
							$stmt->bind_result(
							$SCTimeStart);
							echo date('m/d/y',$SCTimeStart);	
							$stmt->close();
						 }*/
           			?>
                    " required>-->
              </div>
              <div class="popupitem">
               	<div class="popupitemlabel">Starting Balance</div>
               	  <input type="text" style="width:195px;" id="sdbalancestart" name="sdbalancestart" style="text-align:right" placeholder="0.00">
              </div>
        			<br/><div align="center" style="width:350px;">
        				<input type="hidden" name="hdnCreateSale" id="hdnCreateSale" value="" >
            			<input name="btnCreateShift" id="btnCreateShift" type="submit" value="Create" title="Create" class="buttons">
            			<input name="btnCancel" type="button" value="Cancel" title="Cancel" onClick="location.href='<?php switch($_SERVER['PHP_SELF']){case $_SESSION['ParentPath'].'createinvoice.php':echo '#selectSCSD';break;case $_SESSION['ParentPath'].'index.php':echo '#close';break;}?>'" class="buttons" >
					</div>
        </form>
    </div>
</div>

<div id="showimport" class="popup">
    <div id="showimportform" class="popupcontainer">
        <div class="popuptitle" align="center"><img src="img/icon/import.png" width="16" height="16" style="padding-right:10px;" />Import Sales</div>
        <form action="import.php" method="post" enctype="multipart/form-data">
            <div class="popupitem">
	            Import sales from removable device. For security reasons, select one file at a time.<br /><br /> 
            </div>
            <div class="popupitem">
                <input type="file" name="flimport" id="flimport"/>
            </div>
            <div class="popupitem" style="padding-top:20px;">
                <input name="btnimport" type="submit" value="Open" title="Open" class="buttons" style="position:absolute;right:130px;bottom:10px;" >
                <input name="btncancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" style="position:absolute;right:10px;bottom:10px;" >
            </div>
        </form>
    </div>
</div>

<!-- Add/Load/Create Template -->

<!-- Add Production -->

<div id="documentfooter">
	<div class="documentfooterwrapper">
        <div class="documentfooterlabel"><img src="img/icon/employeearea.png" width="16" height="16" /></div>
        <div class="documentfootervalue"><?php echo ExecuteReader('Select AName as `result` from area where unArea='.$_SESSION['area']); ?></div>
	</div>
	<div class="documentfooterwrapper">
        <div class="documentfooterlabel"><img src="img/icon/user.png" width="16" height="16" /></div>
        <div class="documentfootervalue"><?php echo $oAccountUser->getFullName(); ?></div>
	</div>

</div>

</body>
</html>
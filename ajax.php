<?php
include '../include/var.inc.php';
include '../include/class.inc.php';
session_start();

switch($_POST['qid']){
case 'openinventory':
	$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	if($stmt->prepare("Select unInventoryControl,ICNumber,ICInventoryNumber,ICRemarks,concat(MonthName(ICDate) , ' ' , Day(ICDate) , ', ' ,Year(ICDate)) as `ICPeriod` 
						From inventorycontrol 
						Where Status=1 and unBranch=? 
						Order By ICInventoryNumber Desc")){
	$stmt->bind_param('i',$_POST['bun']);
	$stmt->execute();
	$stmt->bind_result($unInventoryControl,$ICNumber,$ICInventoryNumber,$ICRemarks,$ICPeriod);
	while($stmt->fetch()){
		?>
        <div class="listviewitem" onClick="openinventory('inventory',cmbbranch.value,<?php echo $unInventoryControl; ?>)" style="cursor:pointer;">
            <div class="listviewsubitem" style="width:120px;" id="icnumber"><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;"><?php echo $ICNumber; ?></div>
            <div class="listviewsubitem" style="width:120px;" id="icinventorynumber"><?php echo substr('000000'.$ICInventoryNumber,-6); ?></div>                    
            <div class="listviewsubitem" style="width:220px;" id="icremarks"><?php echo ($ICRemarks==0)? '':$ICRemarks; ?></div>                    
            <div class="listviewsubitem" style="width:120px;" id="icperiod"><?php echo $ICPeriod ?></div>                    
        </div>

		<?php
		}
	$stmt->close();
	}
	break;
	
case 'markdocument':
	$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$res=$mysqli->query("Select unAccountDocument as Result From accountdocument Where `Status`=1 and unAccountUser=".$_POST['uid']." and ADSource=".$_POST['did']." and ADType=".$_POST['type']);
	$row = $res->fetch_assoc();
	$mysqli->close(); 
	
	if ($row['Result']>0){
		$query="Update accountdocument Set ADCount=ADCount+1 Where `Status`=1 and unAccountUser=".$_POST['uid']." and ADSource=".$_POST['did']." and ADType=".$_POST['type'];
	}else{
		$query="Insert Into accountdocument (unAccountUser,ADSource,ADType,unAccountDocument) Values (".$_POST['uid'].",".$_POST['did'].",".$_POST['type'].",".getMax("unAccountDocument","accountdocument").")";		
	}
	ExecuteNonQuery($query);
	break;
	
case 'loadbranchinfo':
		$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysql->stmt_init();
		if($stmt->prepare("SELECT unArea,unTemplateItemControl,BName,BDescription,BSAPCode,BType,BQuota,BQuotaInterval,BQuotaPointAmount 
							FROM branch  
							WHERE `Status`=1 And unBranch=?")){
			$stmt->bind_param('i',$_POST['bid']);
			$stmt->execute();
			$stmt->bind_result($unAreaBr,$unTemplateItemControlBr,$BName,$BDescription,$BSAPCode,$BType,$BQuota,$BQuotaInterval,$BQuotaPointAmount);
			$stmt->fetch();
			$stmt->close();
		}
	?>		
		<div class="popuptitle" align="center">Edit <?php echo $BName;?></div>
        <form method="post" action="branch.php">
        	<div class="popupitem">
	            <div class="popupitemlabel">Branch</div><input name="txtbranch" type="text" style="width:195px;" required value ="<?php echo $BName; ?>">
            </div>
            <div class="popupitem">
    	        <div class="popupitemlabel">Template</div>
                <select name="cmbtemplate" id="cmbtemplate" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unTemplateItemControl,TICName 
										From templateitemcontrol 
										Where Status=1 Order by TICName")){
                        $stmt->execute();
                        $stmt->bind_result($unTemplateItemControl,$TICName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unTemplateItemControl; ?>" <?php echo ($unTemplateItemControl==$unTemplateItemControlBr)?'Selected':''; ?>><?php echo $TICName; ?></option>
                <?php
                        }
						$stmt->close();
                    }
                ?>
                </select>

            </div>
            <div class="popupitem">
                <div class="popupitemlabel">SAP Code</div><input name="txtsapcode" type="text" style="width:195px;" value="<?php echo $BSAPCode; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Type</div>
                <select name="cmbbranchtype" style="width:200px;">
                	<option value="1" <?php echo ($BType==1)?'Selected':''; ?>>Outlet</option>
                	<option value="2" <?php echo ($BType==2)?'Selected':''; ?>>Commi</option>
                    <option value="3" <?php echo ($BType==3)?'Selected':''; ?>>Office</option>
                </select>
            </div>
            <div class="popupitem" style="height:20px; border-bottom:thin solid #999;">
	            <div class="popupitemlabel">Quota:</div>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Amount</div><input autocomplete="off" name="txtquota" type="text" style="width:195px; text-align:right;" value="<?php echo $BQuota; ?>" placeholder="0.0000">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Interval</div><input autocomplete="off" name="txtquotainterval" type="text" style="width:195px; text-align:right;" value="<?php echo $BQuotaInterval; ?>" placeholder="0.0000">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Amount/Point</div><input autocomplete="off" name="txtquotapoint" type="text" style="width:195px; text-align:right;" value="<?php echo $BQuotaPointAmount; ?>" placeholder="0.0000">
            </div>
            <div class="popupitem">
        	    <div class="popupitemlabel">Description</div>
                <textarea name="txtdescription" style="max-width:292px; width:292px; height:80px; resize:none;" title="Remarks" ><?php echo $BDescription; ?></textarea>
            </div>
            	<input type="hidden" name="bid" value="<?php echo $_POST['bid']; ?>">
            <div align="center">
                <input name="btnbranchsave" type="submit" value="Save" title="Save Changes for [ <?php echo $BName;?> ]" onClick="" class="buttons" >
                <input name="btnbranchsavecancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    <?php
	break;
	
case 'loadpaymenttypeinfo':
		$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysql->stmt_init();
		if($stmt->prepare("SELECT unPaymentType,PTName,PTFixedAmount FROM paymenttype  
							WHERE `Status`=1 And unPaymentType=?")){
			$stmt->bind_param('i',$_POST['ptid']);
			$stmt->execute();
			$stmt->bind_result($unPaymentType,$PTName,$PTFixedAmount);
			$stmt->fetch();
			$stmt->close();
		}
	?>		
		<div class="popuptitle" align="center">Edit <?php echo $PTName;?></div>
        <form method="post" action="paymenttype.php">
        	<div class="popupitem">
	            <div class="popupitemlabel">Payment Type</div><input name="txtpaymenttype" type="text" style="width:195px;" required value ="<?php echo $PTName; ?>">
            </div>
          
            <div class="popupitem">
                <div class="popupitemlabel">Fixed Amount</div><input autocomplete="off" name="txtfixedamount" type="text" style="width:195px; text-align:right;" value="<?php echo $PTFixedAmount; ?>" placeholder="0.0000">
            </div>
            <input type="hidden" name="ptid" value="<?php echo $_POST['ptid']; ?>">
            <div align="center">
                <input name="btnpaymenttypesave" type="submit" value="Save" title="Save Changes for [ <?php echo $PTName;?> ]" onClick="" class="buttons" >
                <input name="btnpaymenttypesavecancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    <?php
	break;
	
case 'loaddiscounttypeinfo':
		$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysql->stmt_init();
		if($stmt->prepare("SELECT unDiscountType,DTName,DTPercent,DTAmount,DTVatExempt FROM discounttype  
							WHERE `Status`=1 And unDiscountType=?")){
			$stmt->bind_param('i',$_POST['dtid']);
			$stmt->execute();
			$stmt->bind_result($unDiscountType,$DTName,$DTPercent,$DTAmount,$DTVatExempt);
			$stmt->fetch();
			$stmt->close();
		}
	?>		
		<div class="popuptitle" align="center">Edit <?php echo $DTName;?></div>
        <form method="post" action="discounttype.php">
        	<div class="popupitem">
	            <div class="popupitemlabel">Discount Type</div><input name="txtdiscounttype" type="text" style="width:195px;" required value ="<?php echo $DTName; ?>">
            </div>
          
            <div class="popupitem">
                <div class="popupitemlabel">Percent</div><input autocomplete="off" name="txtpercent" type="text" style="width:195px; text-align:right;" value="<?php echo $DTPercent; ?>" placeholder="0.0000">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Amount</div><input autocomplete="off" name="txtamount" type="text" style="width:195px; text-align:right;" value="<?php echo $DTAmount; ?>" placeholder="0.0000">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Vat Exempt</div><!--<input autocomplete="off" name="txtvatexempt" type="text" style="width:195px; text-align:right;" value="<?php echo $DTVatExempt; ?>" placeholder="0.0000">--><input type="radio" name="txtvatexempt" value="1" <?php if($DTVatExempt==1){echo 'checked';}?>>Yes
<input type="radio" name="txtvatexempt" value="0" <?php if($DTVatExempt==0){echo 'checked';}?>>No
            </div>
            <input type="hidden" name="dtid" value="<?php echo $_POST['dtid']; ?>">
            <div align="center">
                <input name="btndiscounttypesave" type="submit" value="Save" title="Save Changes for [ <?php echo $DTName;?> ]" onClick="" class="buttons" >
                <input name="btndiscounttypesavecancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    <?php
	break;
	
case 'loaddeviceinfo':
		$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysql->stmt_init();
		if($stmt->prepare("SELECT unBranch,DName,DMacAddress,DSerialNumber 
							FROM device  
							WHERE `Status`=1 And unDevice=?")){
			$stmt->bind_param('i',$_POST['did']);
			$stmt->execute();
			$stmt->bind_result($unBranchr,$DName,$DMacAddress,$DSerialNumber);
			$stmt->fetch();
			$stmt->close();
		}
	?>		
		<div class="popuptitle" align="center">Edit <?php echo $BName;?></div>
        <form method="post" action="device.php">
        	<div class="popupitem">
	            <div class="popupitemlabel">Device Name</div><input name="txtname" type="text" style="width:195px;" required value ="<?php echo $DName; ?>">
            </div>
            <div class="popupitem">
    	        <div class="popupitemlabel">Branch</div>
                <select name="cbranch" id="cbranch" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unBranch,BName 
										From branch 
										Where Status=1 Order by BName")){
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unBranch; ?>" <?php echo ($unBranch==$unBranchr)?'Selected':''; ?>><?php echo $BName; ?></option>
                <?php
                        }
						$stmt->close();
                    }
                ?>
                </select>

            </div>
           <div class="popupitem">
	            <div class="popupitemlabel">Serial Number</div><input name="txtserialnumber" type="text" style="width:195px;" required value ="<?php echo $DSerialNumber; ?>">
            </div>
            <div class="popupitem">
	            <div class="popupitemlabel">Mac Address</div><input name="txtmacaddress" type="text" style="width:195px;" required value ="<?php echo $DMacAddress; ?>">
            </div>
            	<input type="hidden" name="did" value="<?php echo $_POST['did']; ?>">
            <div align="center">
                <input name="btndevicesave" type="submit" value="Save" title="Save Changes for [ <?php echo $DName;?> ]" onClick="" class="buttons" >
                <input name="btndevicesavecancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    <?php
	break;
	
case 'loademployeeinfo':
		$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysql->stmt_init();
		if($stmt->prepare("Select unEmployeeGroup,ELastName,EFirstName,EMiddleName,EAlias,ENumber,EUsername,EPassword From employee Where `Status`=1 and unEmployee=?")){
			$stmt->bind_param('i',$_POST['eid']);
			$stmt->execute();
			$stmt->bind_result($unEmployeeGroupE,$ELastName,$EFirstName,$EMiddleName,$EAlias,$ENumber,$EUsername,$EPassword);
			$stmt->fetch();
			$stmt->close();
		}
	?>
            <div class="popuptitle" align="center">Edit <?php echo strtoupper($ELastName).', '.$EFirstName.' '.strtoupper(substr($EMiddleName,0,1)).'. '.$EAlias; ?></div>
            <form method="post" action="employee.php">
                <div class="popupitem">
                    <div class="popupitemlabel">Last Name</div><input name="txtlastname" type="text" style="width:195px;" required value="<?php echo $ELastName; ?>">
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">First Name</div><input name="txtfirstname" type="text" style="width:195px;" required value="<?php echo $EFirstName; ?>">
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">Middle Name</div><input name="txtmiddlename" type="text" style="width:195px;" required value="<?php echo $EMiddleName; ?>">
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">Alias</div><input name="txtalias" type="text" style="width:195px;" value="<?php echo $EAlias; ?>">
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">Employee No.</div><input name="txtemployeenumber" type="text" style="width:195px;" value="<?php echo $ENumber; ?>">
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
                        <option value="<?php echo $unEmployeeGroup; ?>" <?php echo ($unEmployeeGroup==$unEmployeeGroupE)?'Selected':''; ?>><?php echo $GName; ?></option>
                    <?php
                            }
                            $stmt->close();
                        }
                    ?>
                    </select>
                </div>
                
               <div class="popupitem">
                    <div class="popupitemlabel">Username</div><input name="txtemployeeusername" type="text" style="width:195px;" value="<?php echo $EUsername; ?>">
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">Password</div><input name="txtemployeepassword" type="text" style="width:195px;" value="<?php echo $EPassword; ?>">
                </div>
                
                <div class="listview" id="lveditemployeearea" style="position:absolute;top:50px;left:350px;width:300px;">
                    <div class="column" id="coleditemployeearea">
                        <div class="columnheader">Area</div>
                    </div>
                    <div class="row" id="roweditemployeearea">
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
                                <div class="listviewitem" onClick="chktoggle('chkearea<?php echo $i; ?>')">
                                    <div class="listviewsubitem"><input type="checkbox" id="chkearea<?php echo $i; ?>" name="chkearea<?php echo $i; ?>" value="<?php echo $unArea; ?>" <?php echo (ExecuteReader("Select count(unemployeearea) as `result` from employeearea where `Status`=1 and unEmployee=".$_POST['eid']." and unArea=".$unArea)==1)? 'checked="Checked"':''; ?>><?php echo $AName; ?></div>
                                </div>
                    <?php
                            }
                            $stmt->close();
                        }
                    ?>
                    </div>
                </div>
            	<input type="hidden" name="eid" value="<?php echo $_POST['eid']; ?>">                
                <br>
				<br>
                <div align="center">
                    <input name="btnemployesave" type="submit" value="Save" title="Save Changes for [ <?php echo strtoupper($ELastName).', '.$EFirstName.' '.strtoupper(substr($EMiddleName,0,1)).'. '.$EAlias; ?> ]" onClick="" class="buttons" >
                    <input name="btnemployeecancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
                </div>
            </form>    
    <?php
	break;
	
case 'itfdata':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select TDQuantity,PUOMName,PIName From transferdata
						Inner Join productuom on transferdata.unProductUOM=productuom.unProductUOM
						Inner Join productitem on transferdata.unProductItem=productitem.unProductItem
						Where unTransferControl = ? and transferdata.`Status` = 1")){
		$stmt->bind_param('i',$_POST['tid']);
		$stmt->execute();
		$stmt->bind_result($TDQuantity,$PUOMName,$PIName);
		while($stmt->fetch()){
			?>
			<div class="listviewitem" style="padding-top:2px; padding-bottom:2px;">
            	<div class="listviewsubitem" style="width:245px;"><?php echo $PIName; ?></div>
				<div class="listviewsubitem" style="width:51px; text-align:center;"><?php echo $TDQuantity; ?></div>
				<div class="listviewsubitem" style="width:51px; text-align:center;"><?php echo $PUOMName; ?></div>
			</div>
			<?php
		}
		$stmt->close();
	}
	break;
	
case 'LoadInventoryEmployee':
	$i=1;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unInventoryEmployee,inventoryemployee.unEmployee,Concat(ELastName,', ',EFirstName,' ',Substring(EMiddleName,1,1),'.') as `FullName`,IEAssignment,IECashPercent,IEInventoryPercent,IEQuotaPercent 
						From inventoryemployee
						Inner Join employee on inventoryemployee.unEmployee = employee.unEmployee
						Where unInventoryControl = ? and inventoryemployee.`Status` = 1 and employee.`Status` = 1
						Order by ELastName Asc, EFirstName Asc ")){
		$stmt->bind_param('i',$_POST['idIC']);
		$stmt->execute();
		$stmt->bind_result($unInventoryEmployee,$unEmployee,$FullName,$IEAssignment,$IECashPercent,$IEInventoryPercent,$IEQuotaPercent);
		while($stmt->fetch()){
			?>
			<div class="listviewitem" id="lvItem-<?php echo $i; ?>">
            	<div class="listviewsubitem" style="width:196px;">
                	<input value="<?php echo $FullName; ?>" readonly type="text" id="<?php echo 'txt-'.$i.'-name'; ?>" style="width:inherit; border:none; background-color:transparent;">
                    <input value="<?php echo $unEmployee; ?>" type="hidden" name="<?php echo 'hdn-'.$i.'-name'; ?>">
                </div>
				<div class="listviewsubitem" style="width:70px;">
                	<input value="<?php echo $IEAssignment; ?>" readonly type="text" name="<?php echo 'txt-'.$i.'-role'; ?>" style="width:inherit; border:none; background-color:transparent;">
                </div>
                <div class="listviewsubitem" style="width:51px;">
                	<input value="<?php echo $IECashPercent; ?>" readonly type="text" name="<?php echo 'txt-'.$i.'-cash'; ?>" style="width:inherit; border:none; background-color:transparent; text-align:right;">
                </div>
                <div class="listviewsubitem" style="width:51px;">
                	<input value="<?php echo $IEInventoryPercent; ?>" readonly type="text" name="<?php echo 'txt-'.$i.'-inventory'; ?>" style="width:inherit; border:none; background-color:transparent; text-align:right;">
                </div>
				<div class="listviewsubitem" style="width:51px;">
                	<input value="<?php echo $IEQuotaPercent; ?>" readonly type="text" name="<?php echo 'txt-'.$i.'-quota'; ?>" style="width:inherit; border:none; background-color:transparent; text-align:right;">
                </div>
                <div class="listviewsubitem" style="min-width:20px;">
                	<input type="button" title="Remove" onClick="removeelement('<?php echo 'lvItem-'.$i; ?>')" style="border:none; background-color:transparent; width:16px; height:16px; margin-top:3px; background-image:url(img/icon/delete.png);">
                </div>
                <input type="hidden" value="<?php echo $unInventoryEmployee; ?>" name="<?php echo 'hdn-'.$i.'-idIE';?>">
			</div>
			<?php
			$i++;
		}
		$stmt->close();
	}
	break;
	
case 'LoadPettyCashData':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select PCDDescription,PCDAmount From pettycashdata Where `Status` = 1 and unPettyCashControl = ? Order by PCDDescription Asc")){
		$stmt->bind_param("i",$_POST['id']);
		$stmt->execute();
		$stmt->bind_result($PCDDescription,$PCDAmount);
		while($stmt->fetch()){
			echo $PCDDescription.'©'.$PCDAmount.'®';
		}
		$stmt->close();
	}
	$mysqli->close();
	break;
	
case 'loaddir':
	?>
		<option value="0"><none></option>
	<?php
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unInventoryControl,concat(ICNumber,' [', MonthName(ICDate) , ' ' , Day(ICDate) , ', ' ,Year(ICDate),']') as `ICPeriod` 
						From inventorycontrol 
						Where `Status`=1 and unBranch = ? 
						Order by unInventoryControl Desc")){
		$stmt->bind_param('i',$_POST['bid']);
		$stmt->execute();
		$stmt->bind_result($unInventoryControl,$ICPeriod);
		while($stmt->fetch()){
			?>
				<option value="<?php echo $unInventoryControl; ?>" <?php echo ($unInventoryControl==$_POST['did'])?'Selected':'';?> ><?php echo $ICPeriod; ?></option>
			<?php
		}
		$stmt->close();
	}
	break;
case 'loadbranch':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and unArea=? Order by BName")){
		$stmt->bind_param('i',$_SESSION['area']);
		$stmt->execute();
		$stmt->bind_result($unBranch,$BName);
		while($stmt->fetch()){
			?>
			<option value="<?php echo $unBranch; ?>" <?php echo ($_POST['bid']==$unBranch)? 'Selected':''; ?>><?php echo $BName; ?></option>
            <?php
		}
		$stmt->close();
	}	
	break;

case 'loademployeegroupinfo':
		$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysql->stmt_init();
		if($stmt->prepare("Select EGName,EGLevel FROM employeegroup WHERE `Status`=1 And unEmployeeGroup=?")){
			$stmt->bind_param('i',$_POST['egid']);
			$stmt->execute();
			$stmt->bind_result($EGName,$levelE);
			$stmt->fetch();
			$stmt->close();
		}
	?>
        <div class="popuptitle" align="center">Edit <?php echo $EGName;?></div>
        <form method="post" action="employeegroup.php">
            <div class="popupitem">
                <div class="popupitemlabel">Group</div><input name="txtegname" type="text" style="width:195px;" value="<?php echo $EGName ;?>" required>
            </div>
             <div class="popupitem">
                    <div class="popupitemlabel">Level</div>
                    <select name="cmblevel" id="cmblevel" style="width:200px;">
                    <?php
                        $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysql->stmt_init();
                        if($stmt->prepare("Select distinct EGLevel From employeegroup Where `Status`=1")){
                            $stmt->execute();
                            $stmt->bind_result($level);
                            while($stmt->fetch()){
                    ?>
                        <option value="<?php echo $level; ?>" <?php echo ($level==$levelE)?'Selected':''; ?>><?php echo $level; ?></option>
                    <?php
                            }
                            $stmt->close();
                        }
                    ?>
                    </select>
                </div>
             <input type="hidden" name="egid" value="<?php echo $_POST['egid']; ?>" >
            <div align="center">
                <input name="btnegsave" type="submit" value="Save" title="Save Changes for [ <?php echo $EGName;?> ]" class="buttons" >
                <input name="btnegcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
	<?php
	break;

case 'loadareainfo':
		$mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysql->stmt_init();
		if($stmt->prepare("SELECT unBranchCommi,AName,ASAPSvr,ASAPDB,ASAPUsr,ASAPpwd,ASAPDataSource FROM area Where unArea=?")){
			$stmt->bind_param('i',$_POST['aid']);
			$stmt->execute();
			$stmt->bind_result($unBranchCommi,$AName,$ASAPSvr,$ASAPDB,$ASAPUsr,$ASAPpwd,$ASAPDataSource);
			$stmt->fetch();
			$stmt->close();
		}
	?>
        <div class="popuptitle" align="center">Edit <?php echo $AName;?></div>
        <form method="post" action="area.php">
            <div class="popupitem">
                <div class="popupitemlabel">Area</div><input name="txtarea" type="text" style="width:195px;" required value="<?php echo $AName; ?>">
            </div>
            
            <div class="popupitem">
                <div class="popupitemlabel">Commi</div>
                <select name="cmbcommi" id="cmbcommi" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unBranch,BName From branch Where `Status`=1 and BType=2 Order By BName")){
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unBranch; ?>" <?php echo ($unBranch==$unBranchCommi)?'Selected':'';?>><?php echo $BName; ?></option>
                <?php
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Server</div><input name="txtserver" type="text" style="width:195px;" required value="<?php echo $ASAPSvr; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Username</div><input name="txtusername" type="text" style="width:195px;" required value="<?php echo $ASAPUsr; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Password</div><input name="txtpassword" type="text" style="width:195px;" required value="<?php echo $ASAPpwd; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Database</div><input name="txtdatabase" type="text" style="width:195px;" required value="<?php echo $ASAPDB; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Data Source</div><input name="txtdatasource" type="text" style="width:195px;" required value="<?php echo $ASAPDataSource; ?>">
            </div>
            <input type="hidden" name="aid" value="<?php echo $_POST['aid']; ?>">
            <div align="center">
                <input name="btnareasave" type="submit" value="Save" title="Save Changes for [ <?php echo $AName;?> ]" class="buttons" >
                <input name="btnareaaddcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
	<?php
	break;

case 'loadaccountuserinfo';
	$mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	if($stmt->prepare("SELECT unAccountUser,unAccountGroup,AULastName,AUFirstName,AUMiddleName,AUUserName,AUEMail FROM accountuser WHERE unAccountUser=?")){
		$stmt->bind_param('i',$_POST['auid']);
		$stmt->execute();
		$stmt->bind_result($unAccountUser,$unAccountGroupAU,$AULastName,$AUFirstName,$AUMiddleName,$AUUserName,$AUEMail);
		$stmt->fetch();
		$stmt->close();
	}	
	?>
        <div class="popuptitle" align="center">Edit <?php echo $AUUserName;?></div>
       
        <form method="post" action="accountuser.php">
            <div class="popupitem">  
                <div class="popupitemlabel">Last Name</div><input name="txtlastname" type="text" style="width:195px;" required value="<?php echo $AULastName; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">First Name</div><input name="txtfirstname" type="text" style="width:195px;" required value="<?php echo $AUFirstName; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Middle Name</div><input name="txtmiddlename" type="text" style="width:195px;" required value="<?php echo $AUMiddleName; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">EMail</div><input name="txtemail" type="email" style="width:195px;" required value="<?php echo $AUEMail; ?>">
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Group</div>
                <select name="cmbgroup" id="cmbgroup" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unAccountGroup,AGName From accountgroup Where `Status`=1 Order by AGName")){
                        $stmt->execute();
                        $stmt->bind_result($unAccountGroup,$AGName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unAccountGroup; ?>" <?php echo ($unAccountGroup==$unAccountGroupAU)? 'Selected':''; ?>><?php echo $AGName; ?></option>
                <?php
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>

            <div class="listview" id="lveditemployeearea" style="position:absolute;top:50px;left:350px;width:300px;">
                <div class="column" id="coleditemployeearea">
                    <div class="columnheader">Area</div>
                </div> 
                <div class="row" id="roweditemployeearea">
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
							<div class="classlistviewitem" onClick="chktoggle('chkeauarea<?php echo $i; ?>')"> 
                                <input type="checkbox" id="chkeauarea<?php echo $i; ?>" name="chkeauarea<?php echo $i; ?>" value="<?php echo $unArea; ?>" <?php echo (ExecuteReader("Select count(unAccountUserArea) as `result` from accountuserarea where `Status`=1 and unAccountUser=".$_POST['auid']." and unArea=".$unArea)==1)?'checked="Checked"':''; ?> ><?php echo $AName; ?>
                            </div>
				<?php
						}
						$stmt->close();
					}
				?>
                </div>
            </div>
            <br>
           	<input type="hidden" name="auid" value="<?php echo $_POST['auid']; ?>">                
            <div align="center">
                <input name="btnaccountuseredit" type="submit" value="Save" title="Save Changes for [ <?php echo $AUUserName;?> ]" onClick="" class="buttons" >
                <input name="btnaccountusercancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>  
    <?php
	break;
	
case 'loadaccountgroupinfo':
	$mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	if($stmt->prepare("Select AGName from accountgroup where unAccountGroup=?")){
		$stmt->bind_param('i',$_POST['agid']);
		$stmt->execute();
		$stmt->bind_result($AGName);
		$stmt->fetch();
		$stmt->close();
	}
	?>
        <div class="popuptitle" align="center">Edit <?php echo $AGName;?></div>
        <form method="post" action="accountgroup.php">
            <div class="popupitem">
                <div class="popupitemlabel">Group</div><input name="txtagname" type="text" style="width:195px;" required value="<?php echo $AGName; ?>">
            </div>
            <input type="hidden" name="agid" value="<?php echo $_POST['agid'];?>">
            <div align="center">
                <input name="btnagedit" type="submit" value="Save" title="Save Changes for [ <?php echo $AGName;?> ]" class="buttons" >
                <input name="btnagcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    

    <?php
	break;
	
case 'loaduom':
    $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
    $stmt = $mysqli->stmt_init();
    if($stmt = $mysqli->prepare("Select productconversion.unProductUOM as `unProductUOM`,PUOMName From productconversion
                                Inner Join productuom on productconversion.unProductUOM = productuom.unProductUOM
                                Where productconversion.`Status` = 1 and productuom.`Status` = 1 and unProductItem = ?")){
        $stmt->bind_param('i',$_POST['pid']);
		$stmt->execute();
        $stmt->bind_result($idProductUOM,$PUOMName);
        while($stmt->fetch()){
            ?>
            <option value="<?php echo $idProductUOM; ?>"><?php echo $PUOMName; ?></option>
            <?php
        }
        $stmt->close();
    }
	break;

case 'SearchEmployee':
	$i=0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select employee.idEmployee,Concat(ELastName,', ',EFirstName,' ',Substring(EMundleName,1,1),'.') as `FullName` From employeearea 
						Inner Join employee on employeearea.unEmployee = employee.unEmployee
						Where unArea = ? and employee.`Status` = 1 and employeearea.`Status` = 1 and (ELastName Like ? or EFirstName Like ?)
						Order by ELastName Asc, EFirstName Asc Limit 10")){
		$likestring='%'.$_POST['search'].'%';
		$stmt->bind_param('iss',$_SESSION['area'],$likestring,$likestring);
		$stmt->execute();
		$stmt->bind_result($unEmployee,$FullName);
		while($stmt->fetch()){			
			?>
            <div class="listboxitem" id="SearchItem-<?php echo $i; ?>" onClick="selectresult('<?php echo $FullName; ?>',<?php echo $unEmployee; ?>)" style="cursor:pointer;">
					<?php echo $FullName; ?>
			</div>
			<?php
			$i++;
		}
		$stmt->close();
	}
	break;

case 'viewdeliverydata':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select PIName,PUOMName,DDQuantity From deliverydata
						Inner Join productitem on deliverydata.unProductItem = productitem.unProductItem
						Inner Join productuom on deliverydata.unProductUOM = productuom.unProductUOM
						Where unDeliveryControl = ?")){
		$stmt->bind_param('i',$_POST['idDC']);
		$stmt->execute();
		$stmt->bind_result($PIName,$PUOMName,$DDQuantity);
		while($stmt->fetch()){
			?>
			<div class="listviewitem" style="padding-top:2px; padding-bottom:2px;">
            	<div class="listviewsubitem" style="width:245px;"><?php echo $PIName; ?></div>
            	<div class="listviewsubitem" style="width:50px; text-align:right;"><?php echo $DDQuantity; ?></div>
				<div class="listviewsubitem" style="width:70px; text-align:center;"><?php echo $PUOMName; ?></div>	
            </div>
			<?php
		}
	}
	break;
	
case 'loaddiscountdata':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select PIName,DDQuantity,DDPrice From discountdata 
						Inner Join productitem on discountdata.unProductItem=productitem.unProductItem 
						Where discountdata.`Status`=1 and unDiscountControl=?")){
		$stmt->bind_param('i',$_POST['idDC']);
		$stmt->execute();
		$stmt->bind_result($PIName,$DDQuantity,$DDPrice);
		while($stmt->fetch()){
			?>
			<div class="listviewitem">
            	<div class="listviewsubitem" style="width:220px;text-align:left;"><?php echo $PIName; ?></div>
            	<div class="listviewsubitem" style="width:50px;text-align:right;"><?php echo $DDQuantity; ?></div>
				<div class="listviewsubitem" style="width:70px;text-align:right;"><?php echo $DDPrice; ?></div>	
            </div>
			<?php
		}
	}
	break;
	
case 'loadproductiteminfo':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select unProductItem, productitem.unProductGroup, productitem.unProductUOM, PIName, productuom.PUOMName, PISAPCode, PIPack, productitem.Status 
						From productitem
						Inner Join productgroup 
						On productitem.unProductGroup = productgroup.unProductGroup 
						Inner Join producttype
						On productgroup.unProductType = producttype.unProductType 
						Inner Join productuom
						On productitem.unProductUOM = productuom.unProductUOM
						Where producttype.unProductType=? and unProductItem=? and productitem.Status=1"))
	{
		$stmt->bind_param("ii", $_POST['tid'], $_POST['bid']);
		$stmt->execute();
		$stmt->bind_result($unProductItemMain, $unProductGroupMain, $unProductUOMMain, $PIName, $PUOMNameMain, $PISAPCode, $PIPack, $Status);
		while($stmt->fetch())
		{
		?>
        	<div id="popme" class="popuptitle" align="center">Edit <?php echo $PIName; ?></div>
            <form method="post" action="productitem.php?&type=<?php echo $_POST['tid'];?>#<?php echo $_POST['gname']; ?>">
                <div class="popupitem">
                    <div class="popupitemlabel">Item</div>
                    <input name="txtproductitem" id="txtproductitem" type="text" style="width:195px;text-transform:capitalize;" required value="<?php echo $PIName; ?>" tabindex="7">
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">Group</div>
                    <select name="cmbproductgroup" id="cmbproductgroup" style="width:200px;" tabindex="8">
                    <?php
                            $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                            $stmt = $mysqli->stmt_init();
                            if($stmt = $mysqli->prepare("SELECT unProductGroup, PGName, productgroup.Status
                                                           FROM productgroup
                                                           Inner Join producttype 
                                                           ON productgroup.unProductType = producttype.unProductType 
                                                           WHERE producttype.unProductType=? and productgroup.Status = 1 Order By PGName"))
                            	{
                                $stmt->bind_param("i", $_POST['tid']);
                                $stmt->execute();
                                $stmt->bind_result($unProductGroupSub, $PGName, $Status);
									while($stmt->fetch())
									{
						?>
								<option value="<?php echo $unProductGroupSub; ?>" <?php echo ($unProductGroupSub==$unProductGroupMain)?'Selected':''; ?>><?php echo $PGName; ?>
								</option>
						<?php									
									}
									$stmt->close();
								}
                        ?>
                    </select>
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">Unit</div>
               		<select name="cmbproductitemuom" id="cmbproductitemuom" style="width:200px;" tabindex="9">
                    <?php
                            $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                            $stmt = $mysqli->stmt_init();
                            if($stmt = $mysqli->prepare("SELECT unProductUOM, PUOMName FROM productuom WHERE Status=1 Order by PUOMName"))
                            	{
                                $stmt->execute();
                                $stmt->bind_result($unProductUOMSub, $PUOMNameSub);
									while($stmt->fetch())
									{
						?>
								<option value="<?php echo $unProductUOMSub; ?>" <?php echo ($unProductUOMSub==$unProductUOMMain)?'Selected':''; ?>><?php echo $PUOMNameSub; ?>
								</option>
						<?php									
									}
									$stmt->close();
								}
                        ?>
                    </select>
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">SAP Code</div>
                    <input name="txtproductitemsap" id="txtproductitemsap" type="text" style="width:195px;" required value="<?php echo $PISAPCode; ?>" tabindex="10">
                </div>
                <div class="popupitem">
                    <div class="popupitemlabel">Piece per Pack</div>
                    <input name="txtppp" id="txtppp" type="text" style="width:195px;" required value="<?php echo $PIPack; ?>" tabindex="10">
                </div>
                <input type="hidden" name="bid" value="<?php echo $_POST['bid']; ?>">
                <div align="center">
                    <input name="btnitemupdate" type="submit" value="Save" title="Save Changes for [ <?php echo $PIName;?> ]" onClick="" class="buttons" tabindex="11">
                    <input name="btnitemcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" tabindex="12">
                </div>
            </form>
          
		<?php
		}
		$stmt->close();
	}
break;

case 'loadconversioninfo':
?>	
    <?php
    	$i=0;
        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt = $mysqli->stmt_init();
        if($stmt->prepare("Select unProductConversion, productconversion.unProductUOM, productuom.PUOMName, PCRatio, PCSet 
        				   From productconversion 
                           Inner Join productuom
                           On productconversion.unProductUOM = productuom.unProductUOM 
                           Where unProductItem=? and productconversion.Status=1"))
		{
        $stmt->bind_param("i", $_POST['bid']);
        $stmt->execute();
        $stmt->bind_result($unProductConversion, $unProductUOM, $PUOMName, $PCRatio, $PCSet);
        	while($stmt->fetch())
			{
			$i++;
	?> 
    	
    <!--ondblclick="itemrowclick(<?php echo "'".$unProductUOM."','".$PUOMName."','".$PCRatio."','".$PCSet."'"; ?>)" -->    
            <div class="listviewitem"  id="lvitemuom-<?php echo $i; ?>">
                <div class="listviewsubitem" style="width: 100px;"><input type="input" name="txtpcname-<?php echo $i; ?>" id="txtpcname-<?php echo $i; ?>" readonly style="border: none; background-color: transparent; width: inherit;" value="<?php echo $PUOMName; ?>"></div>
                <div class="listviewsubitem" style="width: 100px;"><input type="input" name="txtpcratio-<?php echo $i; ?>" id="txtpcratio-<?php echo $i; ?>" readonly style="border: none; background-color: transparent; width: inherit; text-align: right;" value="<?php echo $PCRatio; ?>"></div>
                <div class="listviewsubitem" style="width: 50px;"><input type="input" name="txtpcset-<?php echo $i; ?>" id="txtpcset-<?php echo $i; ?>" readonly style="border: none; background-color: transparent; width: inherit; text-align: right;" value="<?php echo $PCSet; ?>"></div>                                                        
                <input type="hidden" name="hdnproduct-<?php echo $i; ?>" value="<?php echo $_POST['bid']; ?>" style="width: inherit;">
                <input type="hidden" name="hdnunit-<?php echo $i; ?>" value="<?php echo $unProductUOM; ?>" style="width: inherit;">
                <div class="button16" style="background-image: url(img/icon/delete.png); padding-top: 5px; padding-left: 0px;" onClick="deleteitem('<?php echo $i; ?>','lvitemuom-<?php echo $i; ?>');"></div>
            </div>
	<?php
    		
			}
        $stmt->close();
		}
	?>
    	<input type="hidden" id="hdncount" name="hdncount" value="<?php echo $i; ?>">
        <input type="hidden" name="bid" value="<?php echo $_POST['bid']; ?>">
<?php
break;

case 'loadproductiontemplate':
?> 
    <input type="hidden" name="bid" id="bid" value="<?php echo $_POST['bid']; ?>">
    <input type="hidden" name="sid" id="sid" value="">
<?php
		$i=0;
		$mysqli2 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt2 = $mysqli2->stmt_init();
		if($stmt2->prepare("Select templateproductionbatch.unTemplateProductionBatch, templateproductioncontrol.unTemplateProductionControl, 
							unTemplateProductionData, templateproductiondata.unProductItem, templateproductiondata.unProductUOM,TPDCost,
							TPDQuantity,TPDAmount, TPDProcessType,productitem.PIName, productuom.PUOMName
							From templateproductionbatch
							Inner Join templateproductioncontrol 
								On templateproductioncontrol.unTemplateProductionBatch= templateproductionbatch.unTemplateProductionBatch
							Inner Join templateproductiondata 
								On templateproductiondata.unTemplateProductionControl  = templateproductioncontrol.unTemplateProductionControl 
							Inner Join productuom 
								On productuom.unProductUOM = templateproductiondata.unProductUOM
							Inner Join productitem 
								On productitem.unProductItem = templateproductiondata.unProductItem
							Where templateproductioncontrol.unTemplateProductionControl=? 
							And templateproductiondata.`Status`=1")){
			$stmt2->bind_param("i", $_POST['bid']);
			$stmt2->execute();
			$stmt2->bind_result($unTemplateProductionBatch,$unTemplateProductionControl, $unTemplateProductionData, $unProductItem, $unProductUOM, $TPDCost, $TPDQuantity, $TPDAmount, $TPDProcessType, $PIName, $PUOMName);
			while($stmt2->fetch()){
				$i++;
?>
            <div class="listviewitem" id="lvitem-<?php echo $i; ?>">
                <div class="listviewsubitem" style="width: 200px;"><input type="input" id="txtname-<?php echo $i; ?>" readonly style="border: none; background-color: transparent; width: inherit;" value="<?php echo $PIName; ?>"></div>
                <div class="listviewsubitem" style="width: 51px;"><input type="input" readonly style="border: none; background-color: transparent; width: inherit;" value="<?php echo $PUOMName; ?>"></div>
                <div class="listviewsubitem" style="width: 82px;"><input type="input" name="txtcost-<?php echo $i; ?>" readonly style="border: none; background-color: transparent; width: inherit; text-align: right;" value="<?php echo number_format($TPDCost,10,'.','');  ?>"></div>
                <div class="listviewsubitem" style="width: 82px;"><input type="input" name="txtquantity-<?php echo $i; ?>" readonly style="border: none; background-color: transparent; width: inherit; text-align: right;" value="<?php echo number_format($TPDQuantity,10,'.',''); ?>"></div>
                <div class="listviewsubitem" style="width: 82px;"><input type="input" name="txtamount-<?php echo $i; ?>" readonly style="border: none; background-color: transparent; width: inherit; text-align: right;" value="<?php echo number_format($TPDAmount,10,'.',''); ?>"></div>
                <div class="listviewsubitem" style="width: 80px;"><input type="input" name="txtprocesstype-<?php echo $i; ?>" readonly style="border: none; background-color: transparent; width: inherit; text-align: right;" value="<?php echo ($TPDProcessType==0)?'Sales':'Production'; ?>"></div>
                <input type="hidden" name="hdnproduct-<?php echo $i; ?>" value="<?php echo $unProductItem; ?>" style="width: inherit;">
                <input type="hidden" name="hdnunit-<?php echo $i; ?>" value="<?php echo $unProductUOM; ?>" style="width: inherit;">
                <input type="hidden" name="hdnprocesstype-<?php echo $i; ?>" value="<?php echo $TPDProcessType; ?>" style="width: inherit;">
                <div class="button16" style="background-image: url(img/icon/delete.png); padding-top: 5px; padding-left: 0px;" onClick="deleteitem(<?php echo $i; ?>)"></div>
            </div>
<?php
			}
			$stmt2->close();
		}
?>
	<input type="hidden" id="hdncount" name="hdncount" value="<?php echo $i; ?>">
<?php
break;

case 'editproductgroup':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select productgroup.unProductType,shortagetype.unShortageType,PGName,PGPriority,PTName,STName 
						From productgroup 
						Left Join producttype on productgroup.unProductType=producttype.unProductType 
						Left Join shortagetype on productgroup.unShortageType=shortagetype.unShortageType 
						where unProductGroup=?")){
		$stmt->bind_param('i',$_POST['bid']);
		$stmt->execute();
		$stmt->bind_result($unProductType,$unShortageType,$PGName,$PGPriority,$PTName,$STName);
		$stmt->fetch();
		$stmt->close();
	}

?>
	<div id="popme" class="popuptitle" align="center">Edit [ <?php echo $_POST['pgn']; ?> ] Group</div>
        <form method="post" action="productgroup.php?&type=<?php echo $_POST['it']; ?>">
        <div class="popupitem">
            <div class="popupitemlabel">Group</div>
            <input name="txtgroupupdate" type="text" style="width:195px;" required value="<?php echo $PGName; ?>">
        </div>
            
            <div class="popupitem">
                <div class="popupitemlabel">Type</div>
                <select name="idptypeupdate" id="idptypeupdate" style="width:200px;">
                <?php 
                    $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt = $mysqli->stmt_init();
                        if($stmt->prepare("Select unProductType, PTName From producttype Where Status=1")){
                            $stmt->execute();
                            $stmt->bind_result($unProductTypeT,$PTNameT);
                            while($stmt->fetch()){
                ?>
                            <option value="<?php echo $unProductTypeT; ?>" <?php echo ($unProductTypeT==$unProductType)?'selected':''; ?>> <?php echo $PTNameT; ?></option>
                <?php
                            }
                            $stmt->close();
                        }
                ?>
                </select>
            </div>

            <div class="popupitem">
                <div class="popupitemlabel">Shortage Type</div>
                <select name="idshortagetype" id="idshortagetype" style="width:200px;">
                <?php 
                    $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt = $mysqli->stmt_init();
                        if($stmt->prepare("Select unShortageType,STName From shortagetype Where Status=1")){
                            $stmt->execute();
                            $stmt->bind_result($unShortageTypeS,$STNameS);
                            while($stmt->fetch()){
                ?>
                            <option value="<?php echo $unShortageTypeS; ?>" <?php echo ($unShortageTypeS==$unShortageType)? 'Selected':''; ?>> <?php echo $STNameS; ?></option>
                <?php
                            }
                            $stmt->close();
                        }
                ?>
                </select>
            </div>

            <div class="popupitem">
                <div class="popupitemlabel">Priority</div>
                <input name="txtpriority" type="text" style="width:195px;" required value="<?php echo $PGPriority; ?>">
            </div>

                <input type="hidden" name="bid" value="<?php echo $_POST['bid']; ?>" >
            <div align="right">
                <input name="btngroupupdate" type="submit" value="Save" title="Save" onClick="" class="buttons" >
                <input name="btngroupcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#'" class="buttons" >
            </div>
        </form>
<?php 
break;

case 'producttemplatesave':
$idTemplateItemData = $_POST['postidTemplateItemData'];
$unProductItem = $_POST['postidProductItem'];
$price = $_POST['postPrice'];
$cost = $_POST['postCost'];
$priority = $_POST['postPriority'];

	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare('UPDATE templateitemdata SET TIDPrice=?,TIDCost=?,TIDPriority=?,`Status`=1 WHERE idTemplateItemData=?'))
	{
		$stmt->bind_param("dddi",$price,$cost,$priority,$idTemplateItemData);
		$stmt->execute();
		$stmt->close();
	}
	
break;

case 'accountlogs':
	$timezone = 'Asia/Manila';
	if(function_exists('date_default_timezone_set')) { date_default_timezone_set($timezone);}
	$ALTimeStamp = date('Y-m-d H:i:s');
	
	$query="Insert Into accountlog (unAccountUser, ALPage, ALDescription, ALTimeStamp) Values (".
			$_POST['iau'].",'".$_POST['pg']."','".$_POST['dsc']."', TIMESTAMP '".$ALTimeStamp ."')";		
	ExecuteNonQuery($query);

	break;

case 'loaduominfo':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select PUOMName from productuom Where unProductUOM=?")){
		$stmt->bind_param("i",$_POST['iduom']);
		$stmt->execute();
		$stmt->bind_result($PUOMName);
		$stmt->fetch();
		$stmt->close();
	}
?>
        <div class="popuptitle" align="center">Edit Unit of Measure</div>
        <form method="post" action="uom.php">
            <div class="popupitem">
                <div class="popupitemlabel">Unit</div><input name="txtuom" type="text" style="width:195px;" required value="<?php echo $PUOMName; ?>">
            </div>
            <input type="hidden" name="iduom" value="<?php echo $_POST['iduom']; ?>">
            <div align="center">
                <input name="btnuomedit" type="submit" value="Save" title="Update Unit of Measure" class="buttons" >
                <input name="btnuomeditcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
<?
	break;

case 'loaduomsapinfo':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select SUName,unProductUOM from sapuom Where unSAPUOM=?")){
		$stmt->bind_param("i",$_POST['iduomsap']);
		$stmt->execute();
		$stmt->bind_result($SUName,$unProductUOMSAP);
		$stmt->fetch();
		$stmt->close();
	}
?>	
    <div class="popuptitle" align="center">Edit SAP Unit of Measure</div>
    <form method="post" action="uomsap.php">
        <div class="popupitem">
            <div class="popupitemlabel">SAP Unit</div><input name="txtuomsap" type="text" style="width:195px;" value="<?php echo $SUName; ?>" required>
        </div>

        <div class="popupitem">
            <div class="popupitemlabel">Unit</div>
            <select name="cmbuom" style="width:195px;">
                <?php
                $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt = $mysqli->stmt_init();
                if($stmt->prepare("Select unProductUOM,PUOMName from productuom Where `Status`=1")){
                    $stmt->execute();
                    $stmt->bind_result($unProductUOM,$PUOMName);
                    while($stmt->fetch()){
                    ?>
                    <option value="<?php echo $unProductUOM; ?>"<?php echo ($unProductUOM==$unProductUOMSAP)? 'Selected':''; ?>><?php echo $PUOMName; ?></option>
                    <?php
                    }
                    $stmt->close();
                }
                ?>
            </select>
        </div>
        <input type="hidden" name="iduomsap" value="<?php echo $_POST['iduomsap']; ?>">
        <div align="center">
            <input name="btnuomsapedit" type="submit" value="Save" title="Update SAP Unit of Measure" class="buttons" >
            <input name="btnuomaddsapcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
        </div>
    </form>    
<?php
	break;

case 'savefieldvalue':
	if($_POST['field']=='TIDPrice'){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update templateitemdata set TIDPrice=? Where idTemplateItemData=?")){
			$stmt->bind_param("di",$_POST['value'],$_POST['idTID']);
			$stmt->execute();
			$stmt->close();
		}
	}elseif($_POST['field']=='TIDCost'){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update templateitemdata set TIDCost=? Where idTemplateItemData=?")){
			$stmt->bind_param("di",$_POST['value'],$_POST['idTID']);
			$stmt->execute();
			$stmt->close();
		}
	}elseif($_POST['field']=='TIDPriority'){
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("Update templateitemdata set TIDPriority=? Where idTemplateItemData=?")){
			$stmt->bind_param("ii",$_POST['value'],$_POST['idTID']);
			$stmt->execute();
			$stmt->close();
		}
	}
	
	
	break;
	
case 'loadproductioncost':

		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();

		if($stmt->prepare("SELECT (productconversion.PCRatio * templateitemdata.TIDCost) As CostValue
					   	   FROM productitem
					   	   INNER JOIN productconversion
					   	   ON productconversion.unProductItem = productitem.unProductItem
					   	   INNER JOIN templateitemdata
					   	   ON templateitemdata.unProductItem = productitem.unProductItem
					   	   Where templateitemdata.unTemplateItemControl=?
	   					   And productitem.unProductItem=?
			   			   And productconversion.unProductUOM=?
						   And productconversion.Status=1")){
			$stmt->bind_param("iii",$_POST['idTIC'],$_POST['idPI'],$_POST['idPUOM']);
			$stmt->execute();
			$stmt->bind_result($val);
			while($stmt->fetch()){
				echo $val;
			}
			$stmt->close();
		}

break;

case 'loadproductionitemcontroledit':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	
	if($stmt->prepare("Select unTemplateProductionControl, PIName, TPCYield, TPCCost 
					   From templateproductioncontrol 
					   Inner Join productitem
					   On productitem.unProductItem=templateproductioncontrol.unProductItem
					   Where unTemplateProductionControl=?"))
	{
		$stmt->bind_param("i",$_POST['tpc']);
		$stmt->execute();
		$stmt->bind_result($unTemplateProductionControl, $PIName, $TPCYield, $TPCCost);
		while($stmt->fetch())
		{
?>
	<div id="popme" class="popuptitle" align="center">[ <?php echo $PIName; ?> ] Production Data</div>
		<form method="post" action="productiontemplate.php?&id=<?php echo $_POST['bid']; ?>">                                     
			<input type="hidden" id="idtpc" name="idtpc" value="<?php echo $unTemplateProductionControl; ?>">            
			<div class="popupitem" style="width:300px;">
				<div class="popitemlabel" style="display:inline-block;width:90px;">Yield</div>
				<input name="txtproductionyield" id="txtproductionyield" type="text" style="width:200px; text-align:right;" value="<?php echo $TPCYield; ?>" required  onKeyPress="return disableEnterKey(event)" >
			</div>
					
			<div class="popupitem" style="width:300px;">
				<div class="popitemlabel" style="display:inline-block;width:90px;">Cost</div>
				<input name="txtproductioncost" id="txtproductioncost" type="text" style="width:200px; text-align:right;" value="<?php echo $TPCCost; ?>" required  onKeyPress="return disableEnterKey(event)">
			</div>
                
			<div align="right">                
            	<input name="btnproductioncontrol" id="btnproductioncontrol" type="submit" value="Save" title="Save" onClick="" class="buttons" style="width:auto">
                <input name="btnitemcancel" id="btnitemcancel" type="button" value="Close" title="Close" onClick="location.href='#'" class="buttons" >
            </div>                      
		</form>
	</div>

<?
		}
		$stmt->close();
	}
	$mysqli->close();
break;

case 'deletetpcontrol':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Update templateproductioncontrol Set Status=0 Where unTemplateProductionControl=?"))
	{
		$stmt->bind_param("i",$_POST['bid']);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
break;

case 'edittpb':
?>
<div class="popuptitle" align="center">
	<div class="popuptitle" id="editproductiontitle">Edit [ <?php echo $_POST['pname'];?> ] Production</div>
</div>
        
<form action="templateproductionbatch.php" method="post">    
	<input type="hidden" name="hdnidtpb" id="hdnidtpb" value="<?php echo $_POST['tpb']; ?>">
    <div class="popupitem">
        <div class="popupitemlabel" style="width:100px;">Production Name</div>
        <input type="text" name="txttpbname" value="<?php echo $_POST['pname']; ?>" style="width:180px;" required>
    </div>

    <div class="popupitem">
        <div class="popupitemlabel"  style="width:100px;">Template</div>
            <select name="cmbptname" id="cmbptname" style="width:180px;">
            <?php
            $mysqli = new MySQLi ($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
            $stmt = $mysqli->stmt_init();
            if ($stmt->prepare("Select unTemplateItemControl,TICName From templateitemcontrol Where `Status` = 1"))
            {
            $stmt->execute();
            $stmt->bind_result($unTemplateItemControl, $TICName);
				while($stmt->fetch())
				{
				?>
					<option value="<?php echo $unTemplateItemControl; ?>" <?php echo ($unTemplateItemControl==$_POST['tic'])?'selected':'';?> > <?php echo $TICName; ?></option>
				<?php
				}
            $stmt->close();
            }
            ?>
            </select>            
	</div>
        
    <div align="center">
        <input type="submit" name="btnproductionedit" class="buttons" value="Save" >
        <input name="btnitemcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#'" class="buttons" >
	</div>
</form>     

<?php

break;

case 'edittic':
?>

<div class="popuptitle" align="center">
	<div class="popuptitle" id="edittemplatetitle">Edit [ <?php echo $_POST['tname']; ?> ] Template</div>
</div>

<form action="templateitemcontrol.php" method="post">    
	<input type="hidden" name="hdnidtic" id="hdnidtic" value="<?php echo $_POST['tic']; ?>">
    <div class="popupitem">
        <div class="popupitemlabel" style="width:100px;">Template Name</div>
        <input type="text" name="txttpbname" value="<?php echo $_POST['tname']; ?>" style="width:180px;" required>
    </div>
        
    <div align="center">
        <input type="submit" name="btntemplateedit" class="buttons" value="Save" >
        <input name="btnitemcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#'" class="buttons" >
	</div>
</form>     

<?php

break;

case 'ShowEmployeeList':
	?>
	<div class="rptlistview">
    	<div class="rptcolumn">
        	<div class="rptcolumnheader" style="width:204px;">Employee</div>
        	<div class="rptcolumnheader" style="width:154px; text-align:right;">% Quota</div>
        	<div class="rptcolumnheader" style="width:154px; text-align:right;">Quota Amount</div>                        
        </div>
        <div class="rptrow">
        	<?php
			$i=0;
			$TotalAmount = 0;
			$mysqli = new MySQLi ($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
            $stmt = $mysqli->stmt_init();
			if($stmt->prepare("Select Distinct Concat(ELastName,', ',EFirstName,' ',Left(EMiddleName,1),'.') as FullName,
								(
									Select Sum(IEQuotaPercent) From inventoryemployee
									Inner Join employee On inventoryemployee.unEmployee = employee.unEmployee
									Inner Join inventorycontrol On inventoryemployee.unInventoryControl = inventorycontrol.unInventoryControl
									Where ICDate = IC.ICDate and unBranch = IC.unBranch and inventoryemployee.unEmployee = IE.unEmployee
								) as `QuotaPercent`,
								(
									Select Sum(IEQuotaAmount) From inventoryemployee
									Inner Join employee On inventoryemployee.unEmployee = employee.unEmployee
									Inner Join inventorycontrol On inventoryemployee.unInventoryControl = inventorycontrol.unInventoryControl
									Where ICDate = IC.ICDate and unBranch = IC.unBranch and inventoryemployee.unEmployee = IE.unEmployee
								) as `QuotaAmount`
								From inventoryemployee as IE
								Inner Join employee On IE.unEmployee = employee.unEmployee
								Inner Join inventorycontrol as IC On IE.unInventoryControl = IC.unInventoryControl
								Where ICDate = ? and unBranch = ?")){
				$stmt->bind_param('si',$_POST['qdate'],$_POST['bid']);
				$stmt->execute();
				$stmt->bind_result($FullName,$QuotaPercent,$QuotaAmount);
				while($stmt->fetch()){
					$TotalAmount += $QuotaAmount;
					?>
					<div class="rptlistviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                    	<div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit;" value="<?php echo $FullName;?>"></div>
                    	<div class="rptlistviewsubitem" style="width:150px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; text-align:right;" value="<?php echo $QuotaPercent; ?>"></div>
                    	<div class="rptlistviewsubitem" style="width:150px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; text-align:right;" value="<?php echo $QuotaAmount; ?>"></div>                        
                    </div>
					<?php
					$i++;
				}
				?>
                <div class="rptlistviewitem" style="background-color:#FFF; border-top:solid thin #999;">
	                <div class="rptlistviewsubitem" style="width:200px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; font-weight:bold;" value="Total"></div>
                    <div class="rptlistviewsubitem" style="width:304px;"><input readonly type="text" style="background-color:transparent; border:none; width:inherit; text-align:right; font-weight:bold; color:#<?php echo ($TotalAmount!=$_POST['qquota'])?'F00':'000';?>;" value="<?php echo number_format($TotalAmount,4); ?>"></div>                        
                </div>
				<?php
			}
			?>
        </div>
    </div>
	<?php
	break;

case 'EditQuota':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select QQuota,QQuotaInterval,QQuotaPointAmount From Quota Where `Status` = 1 and idQuota = ?")){
		$stmt->bind_param('i',$_POST['unQuota']);
		$stmt->execute();
		$stmt->bind_result($SQuota,$SQuotaPoint,$SQuotaTotalAmount);
		$stmt->fetch();
		$stmt->close();
		echo $SQuota.'©'.$SQuotaPoint.'©'.$SQuotaTotalAmount;
	}
	$mysqli->close();
	break;

case 'SaveDailyQuota':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Call UpdateDailyQuota(?,?,?,?)")){
		$stmt->bind_param('iddd',$_POST['idquota'],$_POST['quota'],$_POST['quotainterval'],$_POST['quotapoint']);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
	break;

case 'SaveIncentives':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Call SummarizeQuota(?,?,?,?,?)")){
		$stmt->bind_param('dddis',$_POST['quota'],$_POST['quotainterval'],$_POST['quotapointamount'],$_POST['idbranch'],$_POST['fdate']);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
	break;
	

}

?>

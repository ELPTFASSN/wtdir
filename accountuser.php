<?php
	include 'header.php';
	//echo (getMax('unAccountUser','accountuser'));

if (isset($_POST['btnaccountuseredit'])){
	$query="Update accountuser set unAccountGroup=".$_POST['cmbgroup'].",AULastName='".$_POST['txtlastname']."',AUFirstName='".$_POST['txtfirstname']."',AUMiddleName='".$_POST['txtmiddlename']."',AUEMail='".$_POST['txtemail']."',AUSession='' where unAccountUser=".$_POST['auid'];
	$areacount=ExecuteReader("Select count(unArea) as `result` From area where `Status`=1");
	ExecuteNonQuery($query);
	
	ExecuteNonQuery("Update accountuserarea set `Status`=0 where unAccountUser=".$_POST['auid']);
	
	for($i=1;$i<=$areacount;$i++){
		if(isset($_POST['chkeauarea'.$i])){
			if(ExecuteReader("Select count(unAccountUserArea) as `result` from accountuserarea where unArea=".$_POST['chkeauarea'.$i]." and unAccountUser=".$_POST['auid'])>=1){
				ExecuteNonQuery("Update accountuserarea set `Status`=1 where unArea=".$_POST['chkeauarea'.$i]." and unAccountUser=".$_POST['auid']);
			}else{
				ExecuteNonQuery("Insert into accountuserarea (unArea,unAccountUser,unAccountUserArea) values (".$_POST['chkeauarea'.$i].",".$_POST['auid'].",".getMax('unAccountUserArea','accountuserarea').")");
			}
		}
	}
}

if (isset($_POST['btnaccountuseradd'])){
		$query="Insert Into accountuser (unAccountGroup,AULastName,AUFirstName,AUMiddleName,AUUserName,AUEMail,AUPassword,unAccountUser) values (".$_POST['cmbgroup'].",'".$_POST['txtlastname']."','".$_POST['txtfirstname']."','".$_POST['txtmiddlename']."','".$_POST['txtusername']."','".$_POST['txtemail']."','".CreatePassword($_POST['txtusername'],$_POST['txtpassword1'])."',".getMax('unAccountUser','accountuser').")";
		ExecuteNonQuery($query);
		$unAccountUser=ExecuteReader("Select max(unAccountUser) as `result` From accountuser");
		$areacount=ExecuteReader("Select count(unArea) as `result` From area where `Status`=1");
		$mysqli= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		//$maxunAccountUserArea=(getMax('unAccountUserArea','accountuserarea'));
		//die ($maxunAccountUserArea);
		for($i=1;$i<=$areacount;$i++){
			if(isset($_POST['chkcauarea'.$i])){
				if($stmt->prepare("Insert Into accountuserarea (unAccountUser,unArea,unAccountUserArea) values (?,?,".getMax('unAccountUserArea','accountuserarea').")")){
					$stmt->bind_param('ii',$unAccountUser,$_POST['chkcauarea'.$i]);
					$stmt->execute();
				}
			}
		}
		$stmt->close();
}

if(isset($_POST['btnchangepassword'])){
	$mysqli= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	if($stmt->prepare("Select AUPassword,AUUserName,AGName from accountuser inner join accountgroup on accountuser.unAccountGroup=accountgroup.unAccountGroup where unAccountUser=?")){
		$stmt->bind_param('i',$_POST['txtaccountuserid']);
		$stmt->execute();
		$stmt->bind_result($AUPassword,$AUUserName,$AGName);		
		if($stmt->fetch()){
			if($AGName=='Administrator' && $AUPassword==CreatePassword($AUUserName,$_POST['txtcpassword1'])){;
				$newpassword=CreatePassword($AUUserName,$_POST['txtcpassword2']);
				$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt1=$mysqli1->stmt_init();
				if($stmt1->prepare("Update accountuser set AUPassword=?,AUSession='' where unAccountUser=?")){
					$stmt1->bind_param('si',$newpassword,$_POST['txtaccountuserid']);
					$stmt1->execute();
					$stmt1->close();
					echo "<script>msgbox('Administrator Password has been changed.','','')</script>";
				}
			}elseif($AGName=='Administrator' && $AUPassword!=CreatePassword($AUUserName,$_POST['txtcpassword1'])){
				echo "<script>msgbox('Old Password is incorrect.','','')</script>";
			}elseif($AGName!='Administrator'){
				$newpassword=CreatePassword($AUUserName,$_POST['txtcpassword2']);
				$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt1=$mysqli1->stmt_init();
				if($stmt1->prepare("Update accountuser set AUPassword=?,AUSession='' where unAccountUser=?")){
					$stmt1->bind_param('si',$newpassword,$_POST['txtaccountuserid']);
					$stmt1->execute();
					$stmt1->close();
					echo "<script>msgbox('Password Changed.','','')</script>";
				}
			}

		}
		$stmt->close();
	}
}

if (isset($_GET['del'])){
	$query="Update accountuser set `Status`=0,AUSession='' where unAccountUser=".$_GET['userid'];
	ExecuteNonQuery($query);
	header('location:accountuser.php');
}

if (isset($_GET['add'])){
	$query="Update accountuser set `Status`=1 where unAccountUser=".$_GET['userid'];
	ExecuteNonQuery($query);
	header('location:accountuser.php');
}


?>
<script>
function loadaccountuserinfo(idAccountUser){
	var xmlhttp;
	if (idAccountUser==0){
		document.getElementById('editaccountuser').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('editaccountuser').innerHTML=xmlhttp.responseText;
			location.href='#popupedit';
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loadaccountuserinfo&auid='+idAccountUser);
}
function changepassword(username,accountuserid,accountgroup){
	document.getElementById('changepasswordtitle').innerHTML='PASSWORD FOR '+ username;
	if(accountgroup!='Administrator'){
		$('#divoldpassword').remove();
	}else{
		$('#txtcpassword1').val('');
	}
	$('#txtcpassword2').val('');
	$('#txtcpassword3').val('');	
	$('#txtaccountuserid').val(accountuserid);
		
	location.href='#changepassword';
}

$(document).ready(function() {
	$('#frmaddaccountuser').submit(function() {
		if($('#txtpassword1').val()!=$('#txtpassword2').val()){
			msgbox('New Password doesnt match','','#createaccountuser');			
			return false;
		}
    });
	$('#frmchangepassword').submit(function(){
		if($('#txtcpassword2').val()!=$('#txtcpassword3').val()){
			msgbox('New Password doesnt match','');
			return false;
		}
	});

	var h = $('#lvaccountuser').height()-$('#colaccountuser').height();
   $('#rowaccountuser').height(h);
	var h = $('#lvarea').height()-$('#colarea').height();
   $('#rowarea').height(h);

});
$(document).scroll(function(){
	columnheader('colaccountuser','lvaccountuser');
});
</script>
<div id="toolbar">
<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#createaccountuser'" style="background-image:url(img/icon/user.png);background-repeat:no-repeat;background-position:center;" >
</div>

<div class="listview" id="lvaccountuser">
	<div class="column" id="colaccountuser">
    	<div class="columnheader" style="width:150px;text-align:left;">Last Name</div>
        <div class="columnheader" style="width:150px;text-align:left;">First Name</div>
        <div class="columnheader" style="width:150px;text-align:left;">Middle Name</div>
        <div class="columnheader" style="width:150px;text-align:left;">Username</div>
        <div class="columnheader" style="width:200px;text-align:left;">EMail</div>
        <div class="columnheader" style="width:150px;text-align:left;">Account Group</div>
        <div class="columnheader" style="width:100px;text-align:left;">Status</div>
        <div class="columnheader" style="width:150px;text-align:left;">Action</div>
    </div>  
    <div class="row" id="rowaccountuser">
    <?php
		$i=0;
		$mysql = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysql->stmt_init();
		if($stmt->prepare("Select unAccountUser,AULastName,AUFirstName,AUMiddleName,AUUsername,AUEMail,AGName,accountuser.`status` from accountuser inner join accountgroup on accountuser.unAccountGroup=accountgroup.unAccountGroup Order by AULastName")){
			$stmt->execute();
			$stmt->bind_result($unAccountUser,$AULastName,$AUFirstName,$AUMiddleName,$AUUsername,$AUEMail,$AGName,$Status);
			while($stmt->fetch()){
	?>
            <div id="lvitem-<?php echo $i;?>" class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;">
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $AULastName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $AUFirstName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $AUMiddleName;?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $AUUsername; ?></div>
                <div class="listviewsubitem" style="width:200px;text-align:left;"><?php echo ($AUEMail=='')? '&nbsp;':$AUEMail; ?></div>
                <div class="listviewsubitem" style="width:150px;text-align:left;"><?php echo $AGName; ?></div>
				<?php
                    if($Status==1){
                ?>               
                        <div class="listviewsubitem" style="width:100px;text-align:left;cursor:pointer;" title="[ <?php echo $AUUsername;?> ] is Active. Click to Deactivate" onclick="msgbox('Deactivate [ <strong><?php echo $AUUsername;?></strong> ], Are you sure?','accountuser.php?&userid=' + <?php echo $unAccountUser; ?> + '&del=1','')" style="cursor:pointer;">Deactivate</div>
                <?php
                    }else{
                ?>
                        <div class="listviewsubitem" style="width:100px;text-align:left;cursor:pointer;" title="[ <?php echo $AUUsername;?> ] is inactive. Click to Activate" onclick="msgbox('Activate [ <strong><?php echo $AUUsername;?></strong> ], Are you sure?','accountuser.php?&userid=' + <?php echo $unAccountUser; ?> + '&add=1','')" style="cursor:pointer;">Activate</div>
                <?php
                    }
                ?>
                <div class="listviewsubitem" style="width:150px;text-align:left;">
                	<div title="Edit [ <?php echo $AUUsername;?> ]" class="button16" onclick="loadaccountuserinfo(<?php echo $unAccountUser; ?>)" style="background-image:url(img/icon/update.png);"></div>
                	<div title="Change Password for [ <?php echo $AUUsername;?> ]" class="button16" onclick="changepassword('<?php echo $AUUsername; ?>','<?php echo $unAccountUser; ?>','<?php echo $AGName; ?>')" style="background-image:url(img/icon/password.png);"></div>
                </div>
            </div>
	<?php
				$i++;
			}
			$stmt->close();
		}
	?>
    </div>
</div>

<div id="createaccountuser" class="popup">
    <div class="popupcontainer" style="width:630px;">
        <div class="popuptitle" align="center">Create User</div>
        <form id="frmaddaccountuser" method="post" action="accountuser.php">
            <div class="popupitem">
                <div class="popupitemlabel">Username</div><input name="txtusername" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Last Name</div><input name="txtlastname" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">First Name</div><input name="txtfirstname" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Middle Name</div><input name="txtmiddlename" type="text" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">EMail</div><input name="txtemail" type="email" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Enter Password</div><input name="txtpassword1" id="txtpassword1" type="password" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Verify Password</div><input name="txtpassword2" id="txtpassword2" type="password" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Group</div>
                <select name="cmbgroup" id="cmbgroup" style="width:200px;">
                <?php
                    $mysql= new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysql->stmt_init();
                    if($stmt->prepare("Select unAccountGroup,AGName From accountgroup Where `Status`=1")){
                        $stmt->execute();
                        $stmt->bind_result($unAccountGroup,$AGName);
                        while($stmt->fetch()){
                ?>
                    <option value="<?php echo $unAccountGroup; ?>"><?php echo $AGName; ?></option>
                <?php
                        }
                        $stmt->close();
                    }
                ?>
                </select>
            </div>
            <div class="listview" id="lvarea" style="position:absolute;top:50px;left:350px;height:270px;width:300px;">
                <div class="column" id="colarea">
                    <div class="columnheader">Area</div>
	            </div>
                <div class="row" id="rowarea">
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
                            <div class="listviewitem" onClick="chktoggle('chkcauarea<?php echo $i; ?>')" style="cursor:pointer">
                            	<div class="listviewsubitem"><input type="checkbox" id="chkcauarea<?php echo $i; ?>" name="chkcauarea<?php echo $i; ?>" value="<?php echo $unArea; ?>" ><?php echo $AName; ?></div>
							</div>
				<?php
						}
						$stmt->close();
					}
				?>
                </div>
            </div>
			<br> 
            <div align="center">
                <input name="btnaccountuseradd" type="submit" value="Add" title="Add User" onClick="" class="buttons" >
                <input name="btnaccountusercancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
        </form>    
    </div>
</div>

<div id="popupedit" class="popup">
    <div id="editaccountuser" class="popupcontainer" style="width:630px;"></div>
</div>

<div id="changepassword" class="popup">
    <div class="popupcontainer" style="width:300px;">
        <div id="changepasswordtitle" class="popuptitle" align="center">Change Password</div>
        <form id="frmchangepassword" method="post" action="accountuser.php">
            <div id="divoldpassword" class="popupitem">
                <div class="popupitemlabel">Old Password</div><input id="txtcpassword1" name="txtcpassword1" type="password" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">New Password</div><input id="txtcpassword2" name="txtcpassword2" type="password" style="width:195px;" required>
            </div>
            <div class="popupitem">
                <div class="popupitemlabel">Verify Password</div><input id="txtcpassword3" name="txtcpassword3" type="password" style="width:195px;" required>
            </div>
            	<input type="hidden" id="txtaccountuserid" name="txtaccountuserid" value="">
            <div align="center">
                <input name="btnchangepassword" type="submit" value="Change" title="Change Password" class="buttons" >
                <input name="btnchangepasswordcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#close'" class="buttons" >
            </div>
		</form>
	</div>
</div>

<?php
	include 'footer.php';
?>
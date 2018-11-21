  <?php
	include 'include/var.inc.php';
	include 'include/class.inc.php';
	
	session_start();
	if ($_SESSION['Session'] == '') {header("location:end.php");}
	
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where idAccountUser='.$oAccountUser->idAccountUser);
	
	if ($_SESSION['Session'] != $sessionid) {header("location:end.php");}
	
	if(isset($_GET['action'])){
		$_SESSION['area']=$_GET['aid'];
		if ($_SERVER['PHP_SELF']=='/rnmdir/inventory.php' || $_SERVER['PHP_SELF']=='/rnmdir/delivery.php' || $_SERVER['PHP_SELF']=='/rnmdir/transfer.php' || $_SERVER['PHP_SELF']=='/rnmdir/discount.php' || $_SERVER['PHP_SELF']=='/rnmdir/productmix.php' || $_SERVER['PHP_SELF']=='/rnmdir/damage.php'){
			header('location:index.php');
		}else{
			header('location:'.$_SERVER['PHP_SELF']);			
		}
	}
	if(!isset($_SESSION['area'])){
		echo "<script>location.href='#popuparea'</script>";
	}
?>
<!doctype html>
<html><head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/class.css">
    <link rel="stylesheet" type="text/css" href="css/listview.css">
    <link rel="SHORTCUT ICON" href="img/favicon.ico" type="image/x-icon">
    <script src="js/jquery1.5.2.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <title>Waffletime - Menu</title>
</head>

<script type="text/javascript" >
function mainmenu(){
	$(" #nav ul ").css({display: "none"}); // Opera Fix
	$(" #nav li").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
}

$(document).ready(function(){					
	mainmenu();
    $('.listviewitem').mouseover(function() {
        $(this).css('background-color','#B7E3F0');
    });
    $('.listviewitem').mouseout(function() {
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);
		if(row%2){
			$(this).css('background-color','#EEE');
		}else{
			$(this).css('background-color','#FFF');
		};
    });
});

function msgbox(content,ok,cancel){
	document.getElementById('msgboxcontent').innerHTML=content;
	if(ok!=''){
		$('#msgboxform').append('<a href="' + ok + '" title="Ok" class="msgboxclose" style="right:150px;">Ok</a>');
	}
	if(cancel!=''){
		$('#msgboxform').append('<a href="' + cancel + '" title="Close" class="msgboxclose">Cancel</a>');
	}else{
		$('#msgboxform').append('<a href="#" title="Close" class="msgboxclose">Close</a>');
	}
	
	location.href='#showmessagebox';
}

function chktoggle(id){
	if($('#'+id).attr('checked')){
		$('#'+id).removeAttr('checked');
	}else{
		$('#'+id).attr('checked','checked');
	}
}

function markdocument(iduser,itype,did){
	$.post('ajax/ajax.php',
	{
		qid:'markdocument',
		uid:iduser,
		did:did,
		type:itype
	});
}

function openinventory(tab,bid,did){
	<?php
		if (isset($_GET['type'])){
			echo 'type='.$_GET['type'].';';
		}else{
			echo 'type=1;';
		}
	?>
	if(tab=='' || bid =='' || did==''){
		msgbox('Please select a sheet','');
		return;
	}	
	markdocument(<?php echo $oAccountUser->idAccountUser; ?>,1,did);
	if (tab=='sheet'){
		location.href='<?php echo $_SERVER['PHP_SELF']; ?>?&bid='+ bid +'&did='+did+'&type='+type;
	}else{
		redirect(tab+'.php?&bid='+ bid +'&did='+did+'&type='+type);	
	}
}

function redirect(url){
	if($('#chknewtab').attr('checked')){
		window.open(url,'_blank');
		setTimeout(function(){location.href='index.php';},2000);
	}else{
		location.href=url;
	}
}

function disableEnterKey(e){
	 var key;      
	 if(window.event)
		  key = window.event.keyCode; //IE
	 else
		  key = e.which; //firefox      																							  
	 return (key != 13);
}

function loaduom(idPI,idComboBox){
	if(idPI==0){
		$('#'+idComboBox).html('');
		return;
	}
	
	$.post('ajax/ajax.php',
	{
		qid:'loaduom',
		pid:idPI
	},
	function(data,status){
		$('#'+idComboBox).html(data);
		$('#'+idComboBox).val(0);
	});
}

function getOffset(el){
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop - el.scrollTop;
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
}

function columnheader(idColumn,idListView)
	{
		var checkItem = $('#colnewheader').length;			
		if (checkItem < 1)
		{
			var fixedDiv = document.createElement('div');
			fixedDiv.setAttribute('class', 'column');
			fixedDiv.setAttribute('id','colnewheader');
			fixedDiv.setAttribute('align', 'center');
			fixedDiv.setAttribute('name', 'colnewheader');
			fixedDiv.style.paddingLeft = '0';
			fixedDiv.style.position = 'fixed';
			//fixedDiv.style.top = '0';
			//fixedDiv.style.backgroundColor = 'rgb(228,228,228)';
			fixedDiv.style.backgroundColor = '#FFF';
			fixedDiv.style.zIndex = '999';
			fixedDiv.style.width = '1298px';
			fixedDiv.style['boxShadow'] = '0px 0px 5px #300';
			fixedDiv.style.borderBottom = 'thin solid #300';
			fixedDiv.style.top = '-50px';
			
			document.getElementById(idListView).appendChild(fixedDiv);
				
			var foo1 = jQuery('#'+idColumn);
			var foo2 = jQuery('#colnewheader');

			foo1.clone().appendTo(foo2);		
		}

		if(document.body.scrollTop > 140)
		{
			$('#colnewheader').animate({top:'0px'}, 'slow','linear');	
		}
		else // if ($(document).scrollTop() <= 139)
		{
		//$('#colnewheader').animate({top:'-50px'}, 'slow','linear',function(){$('#colnewheader').remove();})
		$('#colnewheader').remove();
		}
	}
		
function sal(value,page,desc){
	$.post('ajax/ajax.php',
	{
		qid:'accountlogs',
		iau:value,
		pg:page,
		dsc:desc
	});
}


</script>

<body style="min-height:700px;">

<div id="popuparea" class="popup">
    <div class="popupcontainer">
        <div class="popuptitle" align="center">SELECT AREA</div>
        <div class="popupitem">
        
            <div class="listview">
                <div class="column">
                    <div class="columnheader">Area</div>
                </div>
                <div class="row">
                <?php
                    $mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysqli->stmt_init();
                    if($stmt->prepare("Select accountuserarea.idArea,AName 
                                        from accountuserarea 
                                        inner join area on accountuserarea.idArea=area.idArea 
                                        where accountuserarea.`status` = 1 and area.`Status`=1 and idAccountUser=? 
                                        Order by AName")){
                        $stmt->bind_param('i',$oAccountUser->idAccountUser);
                        $stmt->execute();
                        $stmt->bind_result($idArea,$AName);
                        while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>?&action=setarea&aid=<?php echo $idArea; ?>'" style="cursor:pointer;">
                                <div class="listviewsubitem"><?php echo $AName; ?></div>
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

<div id="showmessagebox" class="msgbox">
    <div id="msgboxform" style="text-align:center;color:#FFF;font-weight:bold;font-size:14px;letter-spacing:2px;">
    	MESSAGE
        <br>
	    <div id="msgboxcontent" class="msgboxcontent"></div>        
    </div>
</div>

<div id="titlebar">
	Rice N' More Inc. Sales and Inventory System
	<?php
/*        $did = (isset($_GET['did']))?$_GET['did']:'';
        $bid = (isset($_GET['bid']))?$_GET['did']:'';
        $mid = (isset($_GET['mid']))?$_GET['mid']:'';
        $mad = (isset($_GET['mad']))?$_GET['mad']:'';
        echo GetInventoryControlData($did,$bid,$mid,$mad,$server,$username,$password,$database); */
    ?>
</div>

<div id="maintab">
    <ul id="nav" style="padding-left:10px;">
        <li><a href="index.php"><img src="img/home.png"> Home</a></li>
        <li><a href="#popuparea"><img src="img/area.png"> <?php echo ExecuteReader("Select AName as `result` From area where idArea=".$_SESSION['area']); ?></a>
            <!--<ul>
                <?php
                    $mysqli= new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysqli->stmt_init();
                    if($stmt->prepare("Select accountuserarea.idArea,AName from accountuserarea inner join area on accountuserarea.idArea=area.idArea where accountuserarea.`status` = 1 and area.`Status`=1 and idAccountUser=? Order by AName")){
                        $stmt->bind_param('i',$oAccountUser->idAccountUser);
                        $stmt->execute();
                        $stmt->bind_result($idArea,$AName);
                        while($stmt->fetch()){
                ?>
                        <li><a href="<?php $_SERVER['PHP_SELF'] ?>?&action=setarea&aid=<?php echo $idArea; ?>"><?php echo $AName; ?></a></li>
                <?php
                        }
                        $stmt->close();
                    }
    
                ?>
            </ul>-->
        </li>
    
        <?php 
		if ($_SERVER['PHP_SELF']=='/rnmdir/inventory.php' || $_SERVER['PHP_SELF']=='/rnmdir/delivery.php' || $_SERVER['PHP_SELF']=='/rnmdir/transfer.php' || 
			$_SERVER['PHP_SELF']=='/rnmdir/discount.php' || $_SERVER['PHP_SELF']=='/rnmdir/damage.php' || $_SERVER['PHP_SELF']=='/rnmdir/sale.php' || 
			$_SERVER['PHP_SELF']=='/rnmdir/pettycash.php' || $_SERVER['PHP_SELF']=='/rnmdir/giftcertificate.php'){
        ?>
        <li><a href="#"><img src="img/branch.png"> <?php echo ExecuteReader("Select `BName` as result From branch Where idBranch=".$_GET['bid']); ?></a>
            <ul>
                <?php
                    $mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysqli->stmt_init();
                    if($stmt->prepare("SELECT idBranch,BName FROM branch WHERE Status=1 And idArea=? Order By BName ASC")){
                        $stmt->bind_param('i',$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($idBranch,$BName);
                        while ($stmt->fetch())
                        {
                            ?>
                            <li>
                                <a href="#"><?php echo $BName; ?></a>
                                
                                <ul>
                                    <?php
                                        $mysqli1=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                                        $stmt1=$mysqli1->stmt_init();
                                        if($stmt1->prepare("SELECT concat(MonthName(ICDate) , ' ' ,Year(ICDate)) as `ICPeriod`,Min(ICDate) as `ICMinDate`, Max(ICDate) as `ICMaxDate` FROM inventorycontrol WHERE Status=1 and idBranch=? Group By `ICPeriod` Order By Year(ICDate) Desc, Month(ICDate) Asc")){
                                            $stmt1->bind_param('i',$idBranch);
                                            $stmt1->execute();
                                            $stmt1->bind_result($ICPeriod,$ICMinDate,$ICMaxDate);
                                            while ($stmt1->fetch())
                                            {
                                                ?>
                                                <li>
                                                    <a href="#"><?php echo $ICPeriod; ?></a>
                                                    <!-- inventory.php?&bid=<?php //echo $idBranch.'&mid='.$ICMinDate.'&mad='.$ICMaxDate.'&type=1'; ?> -->
                                                    <ul>
                                                    <?php
                                                        $mysqli2=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                                                        $stmt2=$mysqli2->stmt_init();
                                                        if($stmt2->prepare("SELECT DAYOFMONTH(ICDate) as `ICDay`,idInventoryControl,ICNumber From inventorycontrol Where (ICDate Between ? and ?) and idBranch=? Order BY ICDate Asc")){
                                                            $stmt2->bind_param('ssi',$ICMinDate,$ICMaxDate,$idBranch);
                                                            $stmt2->execute();
                                                            $stmt2->bind_result($ICDay,$idInventoryControl,$ICNumber);
                                                            while ($stmt2->fetch())
                                                            {
                                                                ?>
                                                                <li><a href="inventory.php?&bid=<?php echo $idBranch.'&did='.$idInventoryControl.'&type=1'; ?>"><?php echo $ICDay.' - '.$ICNumber; ?></a></li>
                                                                <?php
                                                            }
                                                        }
                                                        $stmt2->close();	
                                                    ?>
                                                    </ul>
                                                </li>
                                                <?php
                                            }
                                        }
                                        $stmt1->close();
                                    ?>
                                </ul>
                                
                            </li>
                            <?php
                        }
                        $stmt->close();				
                    }
                ?>
            </ul>
        </li>
        <?php } ?>
    
        
        <li style="position:absolute; right:11px; min-width:200px;text-align:right;">
            <a href="#"><img src="img/user.png"> <?php echo $oAccountUser->getFullName(); ?></a>
            <ul>
                <li style="text-align:left;"><a href="#">Profile</a></li>
                <li style="text-align:left;"><a href="#">Settings</a></li>
                <li style="text-align:left;"><a href="#">Send Feed Back</a></li>
                <li style="text-align:left;"><a href="end.php">Sign Out</a></li>
            </ul>
        </li>
        
    </ul>
</div>

<div id="maintab1">
	<?php
		switch($_SERVER['PHP_SELF']){
			case '/rnmdir/inventory.php':
			case '/rnmdir/delivery.php':
			case '/rnmdir/transfer.php':
			case '/rnmdir/discount.php':
			case '/rnmdir/damage.php':
			case '/rnmdir/pettycash.php':
			case '/rnmdir/sale.php':
				echo 'Inventory Sheet';
				break;
			
			case '/rnmdir/employee.php':
			case '/rnmdir/employeegroup.php':
				echo 'Employee';
				break;
	
			case '/rnmdir/branch.php':
				echo 'Branch';
				break;
				
			case '/rnmdir/device.php':
				echo 'Device';
				break;
	
			case '/rnmdir/productitem.php':
			case '/rnmdir/productgroup.php':
				echo 'Item';
				break;
				
			case '/rnmdir/producttemplate.php':
				echo 'Template [ '.ExecuteReader("Select TICName as `result` From templateitemcontrol Where idTemplateItemControl=".$_GET['id']).' ]';
				break;

			case '/rnmdir/productiontemplate.php':
				echo 'Production [ '.ExecuteReader("Select TPBName as `result` From templateproductionbatch Where idTemplateProductionBatch=".$_GET['id']).' ]';
				break;

			case '/rnmdir/accountuser.php':
			case '/rnmdir/accountgroup.php':
				echo 'User';
				break;

			case '/rnmdir/area.php':
				echo 'Area';
				break;

			case '/rnmdir/creatediscount.php':
			case '/rnmdir/updatediscount.php':
				echo 'Discount';
				break;

			case '/rnmdir/createdamage.php':
			case '/rnmdir/updatedamage.php':
				echo 'Damage';
				break;

			case '/rnmdir/createdelivery.php':
			case '/rnmdir/updatedelivery.php':
				echo 'Delivery';
				break;

			case '/rnmdir/createtransfer.php':
			case '/rnmdir/updatetransfer.php':
				echo 'Transfer';
				break;

			case '/rnmdir/uom.php':
			case '/rnmdir/uomsap.php':
				echo 'Unit of Measure';
				break;

		}
	?>
</div>

<?php
	$bid=(isset($_GET['bid'])=='')?'':$_GET['bid'];
	$did=(isset($_GET['did'])=='')?'':$_GET['did'];
	$type=(isset($_GET['type'])=='')?'':$_GET['type'];
	
	if($_SERVER['PHP_SELF']=='/rnmdir/inventory.php' || $_SERVER['PHP_SELF']=='/rnmdir/delivery.php' || $_SERVER['PHP_SELF']=='/rnmdir/transfer.php' || 
		$_SERVER['PHP_SELF']=='/rnmdir/discount.php' || $_SERVER['PHP_SELF']=='/rnmdir/damage.php' || $_SERVER['PHP_SELF']=='/rnmdir/sale.php' || 
		$_SERVER['PHP_SELF']=='/rnmdir/pettycash.php' || $_SERVER['PHP_SELF']=='/rnmdir/giftcertificate.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
            	<?php
					if(isset($_GET['lock'])){
						if($_GET['lock']==0 && ExecuteReader("Select AGName as `result` from accountgroup where idAccountGroup=".$oAccountUser->idAccountGroup)=='Administrator'){
							ExecuteNonQuery("Update inventorycontrol Set ICLock=0 Where idInventoryControl=".$_GET['did']);
						}elseif($_GET['lock']==1){
							ExecuteNonQuery("Update inventorycontrol Set ICLock=1 Where idInventoryControl=".$_GET['did']);
						}
					}
					$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("Select ICLock from inventorycontrol Where idInventoryControl=?")){
						$stmt->bind_param('i',$_GET['did']);
						$stmt->execute();
						$stmt->bind_result($ICLock);
						$stmt->fetch();
						$stmt->close();
					}
					if ($ICLock==1){
							if(ExecuteReader("Select AGName as `result` from accountgroup where idAccountGroup=".$oAccountUser->idAccountGroup)=='Administrator'){
					?>
				                <li><img src="img/icon/locked.png" width="16" height="16" title="This sheet is locked. Click to unlock." onClick="msgbox('Unlocking the sheet, Are you sure?','<?php echo $_SERVER['PHP_SELF'].'?&bid='.$bid.'&did='.$did.'&type='.$type.'&lock=0'; ?>','')" style="cursor:pointer;"></li>
					<?php
                        	}else{
					?>
				                <li><img src="img/icon/locked.png" width="16" height="16" title="This sheet is locked. To unlock, ask your Administrator." onClick="msgbox('You do not have enough previlege to unlock this sheet. Contact your Administrator','','')" style="cursor:pointer;"></li>
                    <?php
							}
					}else{
					?>
		                <li><img src="img/icon/unlocked.png" width="16" height="16" title="This sheet is not locked. To Prevent accidental changes, click me to lock." onClick="msgbox('You are about to lock this sheet. Remember that unlocking requires administrative control. Are you sure?','<?php echo $_SERVER['PHP_SELF'].'?&bid='.$bid.'&did='.$did.'&type='.$type.'&lock=1'; ?>','')" style="cursor:pointer;"></li>
					<?php
					}
				?>
                <li><a href="inventory.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/inventory.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Inventory</a></li>
                <li><a href="delivery.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/delivery.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Delivery</a></li>
                <li><a href="transfer.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/transfer.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Transfer</a></li>
                <li><a href="damage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/damage.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Damage</a></li>
                <li><a href="discount.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/discount.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Discount</a></li>
                <li><a href="giftcertificate.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/giftcertificate.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Gift Certificate</a></li>
                <li><a href="pettycash.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/pettycash.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Petty Cash</a></li>
                <li><a href="sale.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/sale.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Sales</a></li>
            </ul>
            
            <ul id="nav" style="z-index:1">
                <li style="padding-left:10px;cursor:pointer;">Reports ▼ 
                	<ul>
                    	<li><a href="productmix.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Product Mix</a></li>
                    	<li><a href="#">Sales</a></li>
                    	<li><a href="wtishortage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Shortages</a></li>
                    	<li><a href="incentive.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Incentives</a></li>
                        <li><a href="cos.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">COS</a></li>
                    	<li><a href="#">Discounts</a></li>
                    	<li><a href="#">Petty Cash Movement</a></li>
                    	<li><a href="reportdelivery.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Delivery</a></li>
                		<li><a href="reportdamage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Damages</a></li>
                        <li><a href="reportitf.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type.'&ttype=1'; ?>">Transfers</a></li>
                    </ul>
                </li>
            </ul>
            
            <ul id="nav" style="position:absolute;right:10px;z-index:0">
               <li>
               		<?php
						$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("Select BName,Concat(MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate),' - ',ICNumber) as `ICPeriod`, ICInventoryNumber from inventorycontrol Inner Join branch On inventorycontrol.idBranch=branch.idBranch Where idInventoryControl=?")){
							$stmt->bind_param('i',$_GET['did']);
							$stmt->execute();
							$stmt->bind_result($BName,$ICPeriod,$ICInventoryNumber);
							$stmt->fetch();
							$stmt->close();
						}
					?>
                    <a href="#" style="background-color:#333;"><?php echo $BName; ?> - <?php echo substr('000000'.$ICInventoryNumber,-6); ?> [ <?php echo $ICPeriod; ?> ]</a>
                    <ul style="right:0px;">
                    <?php
						$mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt=$mysqli->stmt_init();
						if($stmt->prepare("Select idInventoryControl, Concat('[ ',MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate),' - ',ICNumber,' ]') as `ICPeriod`, ICInventoryNumber, ICLock from inventorycontrol Where idBranch=? Order by Year(ICDate) Desc, Month(ICDate) Desc, ICDate Desc")){
							$stmt->bind_param('i',$_GET['bid']);
							$stmt->execute();
							$stmt->bind_result($idInventoryControl,$ICPeriod,$ICInventoryNumber,$ICLockSheet);
							while($stmt->fetch()){
								if($idInventoryControl!=$_GET['did']){
							?>
                                <li style="cursor:pointer;"><a onClick="openinventory('sheet',<?php echo $_GET['bid']; ?>,<?php echo $idInventoryControl; ?>)"><img src="img/icon/<?php echo ($ICLockSheet==1)? 'locked.png':'nocheck.png'; ?>" width="16" height="16" style="padding-right:10px;"><?php echo substr('000000'.$ICInventoryNumber,-6).' '.$ICPeriod; ?></a></li>
                            <?php
								}
							}
							$stmt->close();
						}
					?>
                    </ul>
                </li>
            </ul>

    	</div>
    	<?php
	}elseif($_SERVER['PHP_SELF']=='/rnmdir/employee.php' || $_SERVER['PHP_SELF']=='/rnmdir/employeegroup.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="employee.php" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/employee.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Employee</a></li>
                <li><a href="employeegroup.php" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/employeegroup.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Group</a></li>
            </ul>
    	</div>
        <?php
	}elseif($_SERVER['PHP_SELF']=='/rnmdir/accountuser.php' || $_SERVER['PHP_SELF']=='/rnmdir/accountgroup.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="accountuser.php" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/accountuser.php')?'style="background-color:#FFF; color:#000;"':''; ?>>User</a></li>
                <li><a href="accountgroup.php" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/accountgroup.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Group</a></li>
            </ul>
    	</div>
        <?php
	}elseif($_SERVER['PHP_SELF']=='/rnmdir/productitem.php' || $_SERVER['PHP_SELF']=='/rnmdir/productgroup.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="productitem.php?&type=<?php echo $_GET['type']; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/productitem.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Item</a></li>
                <li><a href="productgroup.php?&type=<?php echo $_GET['type']; ?>" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/productgroup.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Group</a></li>
            </ul>
    	</div>
        <?php
	}elseif($_SERVER['PHP_SELF']=='/rnmdir/uom.php' || $_SERVER['PHP_SELF']=='/rnmdir/uomsap.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="uom.php" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/uom.php')?'style="background-color:#FFF; color:#000;"':''; ?>>UOM</a></li>
                <li><a href="uomsap.php" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/uomsap.php')?'style="background-color:#FFF; color:#000;"':''; ?>>SAP UOM</a></li>
            </ul>
    	</div>
        <?php
	}elseif($_SERVER['PHP_SELF']=='/rnmdir/templateproductionbatch.php' || $_SERVER['PHP_SELF']=='/rnmdir/templateitemcontrol.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="templateitemcontrol.php" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/templateitemcontrol.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Template</a></li>
                <li><a href="templateproductionbatch.php" <?php echo ($_SERVER['PHP_SELF']=='/rnmdir/templateproductionbatch.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Production</a></li>
            </ul>
    	</div>
        <?php
	}

?>

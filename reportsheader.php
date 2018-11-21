<?php 
	include 'include/var.inc.php';
	include 'include/class.inc.php';
	
	session_start();
	if ($_SESSION['Session'] == '') {header("location:end.php");}
	
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	
	$bid=(isset($_GET['bid'])=='')?'':$_GET['bid'];
	$did=(isset($_GET['did'])=='')?'':$_GET['did'];
	$type=(isset($_GET['type'])=='')?'':$_GET['type'];
	
	if ($_SESSION['Session'] != $sessionid) {header("location:end.php");}
	
	if(isset($_GET['action'])){
		$_SESSION['area']=$_GET['aid'];
		if ($_SERVER['PHP_SELF']=='/admin/inventory.php' || $_SERVER['PHP_SELF']=='/admin/delivery.php' || $_SERVER['PHP_SELF']=='/admin/transfer.php' || $_SERVER['PHP_SELF']=='/admin/discount.php' || $_SERVER['PHP_SELF']=='/admin/productmix.php' || $_SERVER['PHP_SELF']=='/admin/damage.php'){
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
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="css/class.css">
<link rel="stylesheet" type="text/css" href="css/listview.css">
<link rel="SHORTCUT ICON" href="img/<?php echo $_SESSION['BULogo']; ?>.ico" type="image/x-icon">
<script src="js/jquery1.5.2.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<title><?php echo $_SESSION['BusinessUnit']; ?> - Report</title>
</head>
<script type="text/javascript">
$(document).ready(function(){
	$("#userbutton ul ").css({display: "none"}); // Opera Fix
	$("#userbutton, a").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
	$(window).scroll(function() {
		if ($(this).scrollTop() >= 140) { // this refers to window
			fixedColumn('dsrcolumn','dsrlistview');
		}else{
			//$('#colnewheader').animate({top:'-50px'}, 'slow','linear',function(){})
			$('#colnewheader').remove();
		}
		if ($(this).scrollLeft() >0) {
			//alert($(this).scrollLeft());
			scrollL = $(this).scrollLeft();
			margL = $('body').css('margin-left');
			movL = parseFloat(margL)-parseFloat(scrollL);
			$('#colnewheader').css('left',movL);
		}
	});
});

function markdocument(idUser,iType,did){
	var xmlhttp;

	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			return;
		}
	}

	xmlhttp.open('POST','ajax/ajax.php',false);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=markdocument&uid='+idUser+'&did='+did+'&type='+iType);
}

function openinventory(tab,bid,did){
	if(tab=='' || bid =='' || did==''){
		msgbox('Please select a sheet','');
		return;
	}	
	markdocument(<?php echo $oAccountUser->unAccountUser; ?>,1,did);
	if (tab=='sheet'){
		<?php 
			if($_SERVER['PHP_SELF']=='/admin/reportitf.php'){
				?>
				location.href='<?php echo $_SERVER['PHP_SELF']; ?>?&bid='+ bid +'&did='+did+'&type=1&ttype='+<?php echo $_GET['ttype']; ?>;
				<?php
			}else{
				?>
				location.href='<?php echo $_SERVER['PHP_SELF']; ?>?&bid='+ bid +'&did='+did+'&type=1';
				<?php
			}
		?>
	}else{
		<?php 
			if($_SERVER['PHP_SELF']=='/admin/reportitf.php'){
				?>
				redirect(tab+'.php?&bid='+ bid +'&did='+did+'&type=1&ttype=1');	
				<?php
			}else{
				?>
				redirect(tab+'.php?&bid='+ bid +'&did='+did+'&type=1');	
				<?php
			}
		?>
	}

}

function redirect(url){
	location.href=url;
}
function disableEnterKey(e){
	 var key;      
	 if(window.event)
		  key = window.event.keyCode; //IE
	 else
		  key = e.which; //firefox      																							  
	 return (key != 13);
}

function msgbox(content,ok,cancel){
	document.getElementById('msgboxcontent').innerHTML=content;
	if(ok!=''){
		$('#msgboxform').append('<a href="' + ok + '" id="msgboxok" title="Ok" class="msgboxclose" style="right:100px;">Ok</a>');
	}
	if(cancel!=''){
		$('#msgboxform').append('<a href="' + cancel + '" id="msgboxclose" title="Close" class="msgboxclose">Cancel</a>');
	}else{
		$('#msgboxform').append('<a href="#" id="msgboxclose" title="Close" class="msgboxclose">Close</a>');
	}
	
	location.href='#showmessagebox';
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
			fixedDiv.setAttribute('class', 'rptcolumn');
			fixedDiv.setAttribute('id','colnewheader');
			fixedDiv.setAttribute('align', 'center');
			fixedDiv.setAttribute('name', 'colnewheader');
			fixedDiv.style.paddingLeft = '0';
			fixedDiv.style.position = 'fixed';
			//fixedDiv.style.top = '0';
			//fixedDiv.style.backgroundColor = 'rgb(228,228,228)';
			fixedDiv.style.backgroundColor = '#FFF';
			fixedDiv.style.zIndex = '999';
			fixedDiv.style.width = '811px';
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
		else if ($(document).scrollTop() <= 139)
		{
		//$('#colnewheader').animate({top:'-50px'}, 'slow','linear',function(){$('#colnewheader').remove();})
		$('#colnewheader').remove();
		}
	}
	
function fixedColumn(idColumn,idListView)
	{
		//alert('SCROLLED');
		var checkItem = $('#colnewheader').length;			
		if (checkItem < 1)
		{
			var fixedDiv = document.createElement('div');
			fixedDiv.setAttribute('class', 'rptcolumn');
			fixedDiv.setAttribute('id','colnewheader');
			fixedDiv.setAttribute('align', 'center');
			fixedDiv.setAttribute('name', 'colnewheader');
			fixedDiv.style.paddingLeft = '0';
			fixedDiv.style.position = 'fixed';
			//fixedDiv.style.top = '0';
			//fixedDiv.style.backgroundColor = 'rgb(228,228,228)';
			fixedDiv.style.backgroundColor = '#FFF';
			fixedDiv.style.zIndex = '999';
			fixedDiv.style.width = '1500px';
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
		else if ($(document).scrollTop() <= 139)
		{
		$('#colnewheader').animate({top:'-50px'}, 'slow','linear',function(){$('#colnewheader').remove();})
		//$('#colnewheader').remove();
		}
}
</script>

<style type="text/css" media="print">
.exemptPrint{
	display:none;
}
#exemptPrint{
	display:none;
}
	#colnewheader{
		transition: 3s;
	}
</style>
<body style="width:11in; font-family:calibri;">

<div id="showmessagebox" class="msgbox">
    <div id="msgboxform" style="text-align:center;color:#900;font-weight:bold;font-size:14px;font-stretch:expanded;">
    	MESSAGE<br>
	    <div id="msgboxcontent" class="msgboxcontent"></div>        
    </div>
</div>

<div id="titlebar" style="width:inherit; padding-left:0px; background-color:<?php echo $_SESSION['color']; ?>">
	 <a href="index.php"><img src="img/<?php echo $_SESSION['BULogo']; ?>.png" style="margin-top:-10px;margin-bottom:-10px; background-color:<?php echo $_SESSION['color']; ?>" height="35"></a>
	<?php echo $_SESSION['BusinessUnit']; ?> Sales and Inventory System - [	<?php
		switch($_SERVER['PHP_SELF']){
			case $_SESSION['ParentPath'].'productmix.php':
				echo 'Product Mix Report';
				break;
			case $_SESSION['ParentPath'].'dailysalesreport.php':
				echo 'Sales Report';
				break;
			case $_SESSION['ParentPath'].'wtishortage.php':
				echo 'Shortage Report';
				break;
			case $_SESSION['ParentPath'].'cos.php':
				echo 'Cost of Sales';
				break;
			case $_SESSION['ParentPath'].'crewincentivesreport.php':
				echo ' Crew Incentives Report';
				break;
			case $_SESSION['ParentPath'].'crewFOMreport.php':
				echo ' FOM Incentives Report';
				break;
			case $_SESSION['ParentPath'].'reportitf.php':
				echo 'Transfers';
				break;
			case $_SESSION['ParentPath'].'crewshortagesreport.php':
				echo 'Crew Shortages Report';
				break;
		}
	?>]
</div>

<div id="maintab" style="width:inherit;">
	   <div id="userbutton" class="headbtn"><img src="img/user.png">  
            <a href="#"><?php echo $oAccountUser->getFullName(); ?></a> 
            <ul style="visibility:hidden">
            <a href="index.php"><li style="text-align:left;" id="home">Home</li></a>
            <a href="#"><li style="text-align:left;" id="profile">Profile</li></a>
        	<a href="#"><li style="text-align:left;" id="settings">Settings</li></a>
        	<a href="#"><li style="text-align:left;" id="feedback">Send Feed Back</li></a>
        	<a href="end.php"><li style="text-align:left;" id="signout">Sign Out</li></a>
            </ul>
        </div>
</div>

<div id="maintab2" style="width:inherit; padding-top:5px;">
    <!--<ul id="nav" style="z-index:1">
        <li style="padding-left:10px;cursor:pointer;">Reports ▼ 
            <ul>
                <li><a href="productmix.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Product Mix</a></li>
                <!--<li><a href="#">Sales</a></li>-->
                <!--<li><a href="wtishortage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Shortages</a></li>
                <!--<li><a href="incentive.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Incentives</a></li>
                <li><a href="cos.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">COS</a></li>
                <li><a href="#">Discounts</a></li>
                <li><a href="#">Petty Cash Movement</a></li>
                <li><a href="reportdelivery.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Delivery</a></li>
                <li><a href="reportdamage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Damages</a></li>
                <li><a href="reportitf.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type.'&ttype=1'; ?>">Transfers</a></li>-->
            <!--</ul>
        </li>
    </ul>-->
    <ul id="nav">
    	<li style="padding-left:10px;cursor:pointer;" onClick="window.print()" title="Print Report">Print (X)
        </li>
    </ul>
    <ul id="nav">
    	<li style="padding-left:10px;cursor:pointer;" title="Export Report" id="export"><a href="
        <?php switch($_SERVER['PHP_SELF']){
			case $_SESSION['ParentPath'].'productmix.php':
				echo 'productmixcsv';
				break;
			case $_SESSION['ParentPath'].'dailysalesreport.php':
				echo 'dailysalesreportcsv';
				break;
			case $_SESSION['ParentPath'].'wtishortage.php':
				echo 'shortagecsv';
				break;
			case $_SESSION['ParentPath'].'cos.php':
				echo 'costofsalescsv';
				break;
			case $_SESSION['ParentPath'].'incentive.php':
				echo 'incentivecsv';
				break;
			case $_SESSION['ParentPath'].'reportitf.php':
				echo 'transfercsv';
				break;
			case $_SESSION['ParentPath'].'crewshortagesreport.php':
				echo 'crewshortagesreportcsv';
				break;
		}?>.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; if(isset($_GET['filter'])){echo '&filter=1&dfrom='.$_GET['dfrom'].'&dto='.$_GET['dto'];} ?>" style="text-decoration:none; color:#FFF;" >Export <?php
		switch($_SERVER['PHP_SELF']){
			case $_SESSION['ParentPath'].'productmix.php':
				echo 'Product Mix Report';
				break;
			case $_SESSION['ParentPath'].'dailysalesreport.php':
				echo 'Sales Report';
				break;
			case $_SESSION['ParentPath'].'wtishortage.php':
				echo 'Shortage Report';
				break;
			case $_SESSION['ParentPath'].'cos.php':
				echo 'Cost of Sales';
				break;
			case $_SESSION['ParentPath'].'incentive.php':
				echo 'Incentives';
				break;
			case $_SESSION['ParentPath'].'reportitf.php':
				echo 'Transfers';
				break;
			case $_SESSION['ParentPath'].'crewshortagesreport.php':
				echo 'Crew Shortages Report';
				break;
		}
		
	?>></a>
        </li>
    </ul>
    <ul id="nav" style="position:absolute;right:10px;z-index:2">
       <li>
            <?php
                $mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt = $mysqli->stmt_init();
                if($stmt->prepare("Select BName from inventorycontrol Inner Join branch On inventorycontrol.unBranch=branch.unBranch Where unInventoryControl=?")){
                    $stmt->bind_param('i',$_GET['did']);
                    $stmt->execute();
                    $stmt->bind_result($BName);
                    $stmt->fetch();
                    $stmt->close();
                }
            ?>
            <?php if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){}else{ ?> 
            <a href="#" style="background-color:#BBB;#999;"><?php echo $BName; ?></a> <?php } ?>
            <ul style="right:0px; z-index:2">
			<?php
                $mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt=$mysqli->stmt_init();
                if($stmt->prepare("Select unInventoryControl, Concat('[ ',MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate),' - ',ICNumber,' ]') as `ICPeriod` from inventorycontrol Where unBranch=? Order by Year(ICDate) Desc, Month(ICDate) Desc, ICDate Desc")){
                    $stmt->bind_param('i',$_GET['bid']);
                    $stmt->execute();
                    $stmt->bind_result($unInventoryControl,$ICPeriod);
                    while($stmt->fetch()){
                        if($unInventoryControl!=$_GET['did']){
                    ?>
                        <li style="cursor:pointer;"><a onClick="openinventory('sheet',<?php echo $_GET['bid']; ?>,<?php echo $unInventoryControl; ?>)"><?php echo $ICPeriod; ?></a></li>
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
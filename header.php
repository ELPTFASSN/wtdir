  <?php
	include 'include/var.inc.php';
	include 'include/class.inc.php';

	session_start();
	if ($_SESSION['Session'] == '') {header("location:end.php");}

	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);
	if ($_SESSION['Session'] != $sessionid) {header("location:end.php");}

	if(isset($_GET['action'])){
		$_SESSION['area']=$_GET['aid'];
		if ($_SERVER['PHP_SELF']== $_SESSION['ParentPath'].'manualinventory.php' || $_SESSION['ParentPath'].'inventory.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'delivery.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'transfer.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'discount.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'productmix.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'damage.php'){
			header('location:index.php');
		}else{
			header('location:'.$_SERVER['PHP_SELF']);
		}
	}
	if(!isset($_SESSION['area'])){
		echo "<script>location.href='#popuparea'</script>";
	}

	//echo $_SESSION['BusinessUnit'];
?>

<!doctype html>
<html><head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/class.css">
    <link rel="stylesheet" type="text/css" href="css/listview.css">
    <link rel="SHORTCUT ICON" href="img/<?php echo $_SESSION['BULogo']; ?>.ico" type="image/x-icon">
    <!--<script src="js/jquery-1.11.3.js"></script>
	<script src="js/jquery-migrate-1.2.1.js"></script>-->
    <script src="js/jquery1.5.2.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <title><?php echo $_SESSION['BusinessUnit']; ?> - Menu</title>
</head>

<script type="text/javascript" >
/*$(document).ready(function(e) {
    //$('.msgbox').css('background','<?php echo $_SESSION['color']; ?>')
});*/

function mainmenu(){
	$(" #nav ul ").css({display: "none"}); // Opera Fix
	$(" #nav li").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
}

	$(document).ready(function(){
	$("#userbutton ul ").css({display: "none"}); // Opera Fix
	$("#userbutton, a").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#branchbutton ul ").css({display: "none"}); // Opera Fix
	$("#branchbutton, a").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#branchbutton ul li ul").css({display: "none"}); // Opera Fix
	$("#branchbutton ul li").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#menufile ul").css({display: "none"}); // Opera Fix
	$("#menufile, a").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#menuoption ul").css({display: "none"}); // Opera Fix
	$("#menuoption, a").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#menuoption ul li ul").css({display: "none"}); // Opera Fix
	$("#menuoption ul li").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "inline-table"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#menuview ul").css({display: "none"}); // Opera Fix
	$("#menuview, a").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#menufile ul li ul").css({display: "none"}); // Opera Fix
	$("#menufile ul li").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "inline-table"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#menuedit ul").css({display: "none"}); // Opera Fix
	$("#menuedit, a").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});
	$(document).ready(function(){
	$("#menutools ul").css({display: "none"}); // Opera Fix
	$("#menutools, a").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(200);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
	});
});


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
		if(ok=='settlethis'){
			$('#msgboxform').append('<a title="Ok" name="settle" onClick="submitinvoiceForm(<?php echo $_GET['unsd'];?>,<?php echo $_GET['unsc'];?>);" class="msgboxclose" style="right:150px;">Ok</a>');
		}else if(ok=='voidthis'){
			$('#msgboxform').append('<a title="Ok" name="void" onClick="resetinvoiceForm();" href="#close" class="msgboxclose" style="right:150px;">Ok</a>');
		}else if(ok.indexOf("closeshift") >= 0){
			$('#msgboxform').append('<a title="Ok" name="closeshift" id="okcloseshift" href="#close" class="msgboxclose" style="right:150px;">Ok</a>');
			//var timeend=ok.replace('closeshift','');
			var matches = [];
			ok.replace(/\[(.+?)\]/g, function(_, m){matches.push(m)});
				var timeend = matches[0];
				var balend = matches[1];
			$('#okcloseshift').attr("onClick","closeshift('<?php echo $_GET['bid'];?>','<?php echo $_GET['unsd'];?>','"+timeend+"','"+balend+"','<?php echo $_GET['unsc'];?>');");
		}else if(ok.indexOf("closeday") >= 0){
			$('#msgboxform').append('<a title="Ok" name="closeday" id="okcloseday" href="#close" class="msgboxclose" style="right:150px;">Ok</a>');
			//var timeend=ok.replace('closeday','');
			//var matches = ok.match(/\[(.*?)\]/);
			//if (matches) {
			//var regExp = /\(([^)]+)\)/;
			//var res = regExp.exec(ok);
			var matches = [];
			ok.replace(/\[(.+?)\]/g, function(_, m){matches.push(m)});
				var timeend = matches[0];
				var cmb = matches[1];
				var stillopen = matches[2];
			//}
			//alert(ok);
			$('#okcloseday').attr("onClick","closeday('<?php echo $_GET['bid'];?>','<?php echo $_GET['unsc'];?>','"+timeend+"','"+cmb+"','<?php echo ($_GET['unsd'])?'1':'0';?>','"+stillopen+"');");
		}else{
			$('#msgboxform').append('<a href="' + ok + '" title="Ok" class="msgboxclose" style="right:150px;">Ok</a>');
		}
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
			echo 'type=2;';
		}
		if($_SESSION['BusinessUnit']=="Waffletime Inc.,"){
			echo 'tabtoopen="manualinventory";';
		}else{
			echo 'tabtoopen=tab;';
		}
	?>
	if(tab=='' || bid =='' || did==''){
		msgbox('Please select a sheet','');
		return;
	}
	markdocument(<?php echo $oAccountUser->unAccountUser; ?>,1,did);
	if (tab=='sheet'){
		location.href='<?php echo $_SERVER['PHP_SELF']; ?>?&bid='+ bid +'&did='+did+'&type='+type;
	}else{
		redirect(tabtoopen+'.php?&bid='+ bid +'&did='+did+'&type='+type);
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
	//alert(idPI);
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
			fixedDiv.style.width = '1293px';
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

function loadquota(unBranch){
$.post('ajax/index.ajax.php',
		{
			qid:'loadquota',
			bid:unBranch,
		},
		function(data,status){
			obj = JSON.parse(data);
			//alert(obj.BQuota);
			$('#scquota').val(0.00);
			$('#scquota').val(obj.BQuota);
			$('#scquotaint').val(0.00);
			$('#scquotaint').val(obj.BQuotaInterval);
			$('#scquotap').val(0.00);
			$('#scquotap').val(obj.BQuotaPointAmount);
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
                    if($stmt->prepare("Select accountuserarea.unArea,AName
                                        from accountuserarea
                                        inner join area on accountuserarea.unArea=area.unArea
                                        where accountuserarea.`Status` = 1 and area.`Status`=1 and unAccountUser=?
                                        Order by AName")){
                        $stmt->bind_param('i',$oAccountUser->unAccountUser);
                        $stmt->execute();
                        $stmt->bind_result($unArea,$AName);
                        while($stmt->fetch()){
                        ?>
                            <div class="listviewitem" onClick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>?&action=setarea&aid=<?php echo $unArea; ?>'" style="cursor:pointer;">
                                <div class="listviewsubitem"><?php echo $AName; ?></div>
                            </div>
                        <?php
                        }
                        $stmt->close();
                    }
                ?>
                </div>
            </div>
			<div class="popupitem" style = "padding-top: 20px">
			<div class="listview">
                <div class="column">
                    <div class="columnheader">NOTICE</div>
                </div>
                <div class="row" style = "margin-top: 5px">
					<div class="listviewitem" style = "margin-bottom: 5px; width: auto;">
						<div class="listviewsubitem"><b>LAMP</b>(currently used Linux web server solution stack package) which consists of Apache, MYSQL, and PHP are all being considered for an upgrade. With this, we believe it could
							fix the woes of website's slow response time and crashes. Please wait patiently while we're trying our best to sort out and prepare the server for the upgrade. </div>
					</div>
					<div class="listviewitem" style = "margin-bottom: 10px; width: auto;">
						<div class="listviewsubitem">For the meantime, if you have any concerns and suggestions, you can send us an e-mail via [ Send Feedback ] found on upper left corner of the screen.</div>
					</div>
					<div class="listviewitem" style = "margin-bottom: 5px; width: auto;" title = "Cheers!">
						<div class="listviewsubitem">-- MIS Department</div>
					</div>
					<div class="listviewitem" style = "margin-bottom: 5px; width: auto;">
						<div class="listviewsubitem">Stay tuned on [ Version Info ] for updates!</div>
					</div>
                </div>
            </div>
			</div>
            <div align="center" style="padding-top:10px;">
                <input type="button" value="Close" title="Close" onClick="location.href='#close'" class="buttons" >
            </div>
        </div>
    </div>
</div>
<div id="popupversion" class="popup">
    <div class="popupcontainer" style="width:600px">
        <div class="popuptitle" align="center">VERSION INFO</div>
        <div class="popupitem" style = "width: 100%">
            <div class="listview"  style="width:600px; height: 300px; min-height: 300px; max-height: 500px;">
                <div class="column">
                    <div class="columnheader">LOGS</div>
                </div>
                <div class="row">
<!--
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170212 - </div>
               			<div class="listviewsubitem">[on ALL Reports] Default values of fields for personnel changed.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170212 - </div>
               			<div class="listviewsubitem">[on Shortage Report] Adjustments from Cash Breakdown now reflect on the report.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170212 - </div>
               			<div class="listviewsubitem">[on Cash Breakdown] Adjustment fields now functioning.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170209 - </div>
               			<div class="listviewsubitem">[on Cash Breakdown] Adjustment fields for Variances added. Functions to follow.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170208 - </div>
               			<div class="listviewsubitem">[on Inventory Sheet] Field for Adjustment removed.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170208 - </div>
               			<div class="listviewsubitem">[on Inventory Sheet] Field for Adjustment provided. Function still on-progress.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170207 - </div>
               			<div class="listviewsubitem">[on Crew Incentives Report] Segregation of crews & supervisors fixed.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170207 - </div>
               			<div class="listviewsubitem">[on Cash Breakdown] Total Deposit and Deposit Variance hidden to avoid confusion.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170207 - </div>
               			<div class="listviewsubitem">[on Cash Breakdown] Cash denomination hidden to avoid confusion.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170207 - </div>
               			<div class="listviewsubitem">[on Inventory Sheet] W & F of Starting & Ending Rawmats now based on main field's entry.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170123 - </div>
               			<div class="listviewsubitem">[on Shortage Report] Changes to formula regarding distribrution of shortages to trainees reverted.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170119 - </div>
               			<div class="listviewsubitem">[on Cash & Crew] Cash Breakdown disabled, Total Deposit field enabled for quicker encoding.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170118 - </div>
               			<div class="listviewsubitem">[on FOM Incentives Report] All issues resolved.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170116 - </div>
               			<div class="listviewsubitem">[on FOM Incentives Report] Report added.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170110 - </div>
               			<div class="listviewsubitem">[on Shortage Report] Company-hired trainees are excluded from charging of Mix Shortage.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170105 - </div>
               			<div class="listviewsubitem">[on Incentives Report] Trainees and Franchise Trainees excluded in distribution of incentives.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171222 - </div>
               			<div class="listviewsubitem">[on Inventory Sheet - Mix] Formula for variance now fully based into pcs.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171221 - </div>
               			<div class="listviewsubitem">[on Shortage Report] Franchise trainees are charged.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171212 - </div>
               			<div class="listviewsubitem">[on Cash Breakdown] Field for shortage qty, charge, and amt for each Other Supplies added.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171211 - </div>
               			<div class="listviewsubitem">Issues on process out, variance qty & amt in Inventory Sheet (mix) fixed.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171123 - </div>
               			<div class="listviewsubitem">Employees with zero incentives excluded from Incentives Report.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171122 - </div>
               			<div class="listviewsubitem">Process Out in Inventory Sheet (rawmats) changed format into by-piece.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171117 - </div>
               			<div class="listviewsubitem">Additional columns for whole and fraction of Starting and Ending Balances in Inventory Sheet (rawmats).</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171110 - </div>
               			<div class="listviewsubitem">Actual values displays on whole and fraction fields in popup boxes for Inventory Sheet (Rawmats).</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20171001 - </div>
               			<div class="listviewsubitem">Negative values are excluded in summation of crew's total incentives in Incentives Report Summary.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170928 - </div>
               			<div class="listviewsubitem">Issues on entering whole and fraction Ending/Strating of items in Inventory sheets resolved.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170927 - </div>
               			<div class="listviewsubitem">Editing of Repositories now enabled for "Accounting" users (UOM and Items only).</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170927 - </div>
               			<div class="listviewsubitem">"Area" replaced "Options" menu in toolbar for easy access of areas.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170924 - </div>
               			<div class="listviewsubitem">Template and BOM independent combo box in adding and editing of Branches granted.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170921 - </div>
               			<div class="listviewsubitem">Damage Sheets arranged by Branch & Date when opening from Dashboard.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170921 - </div>
               			<div class="listviewsubitem">Stored Procedures and its functions in WTI database sorted.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170921 - </div>
               			<div class="listviewsubitem">Cascading of updates from an inventory sheet to next (Starting, Variance, Sold, etc.) fixed.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170921 - </div>
               			<div class="listviewsubitem">Formula for variances of Mix and Other Supplies in Cash & Crew fixed.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170920 - </div>
               			<div class="listviewsubitem">Formula for Total Monthly Incentive in Incentives Report Summary fixed.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170919 - </div>
               			<div class="listviewsubitem">Redundant crews in Incentives Report Summary controlled.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170919 - </div>
               			<div class="listviewsubitem">Sorting of names in Incentives Report Summary reformulated.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170919 - </div>
               			<div class="listviewsubitem">Validation of Sales Quota in Incentives Report disabled as per request.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170918 - </div>
               			<div class="listviewsubitem">Redirection after updating Inventory Details fixed.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170918 - </div>
               			<div class="listviewsubitem">Sequence of product groups in BOM accdg. to priority.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170914 - </div>
               			<div class="listviewsubitem">Deleting of Employee in DTR - Cash & Crew enabled.</div>
               		</div>
               		<div class="listviewitem">
               			<div class="listviewsubitem">20170914 - </div>
               			<div class="listviewsubitem">2nd dropdown for Codes in DTR - Cash & Crew disabled.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170914 - </div>
               			<div class="listviewsubitem">Issues on saving Incentives Report resolved.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170913 - </div>
               			<div class="listviewsubitem">Fields for additional supervisors in Incentives Report added.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170912 - </div>
               			<div class="listviewsubitem">Issues on Total Variance in Cash & Crew page resolved.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170908 - </div>
               			<div class="listviewsubitem">Incentives Report quota can now be saved for future reference.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170908 - </div>
               			<div class="listviewsubitem">Date range changed into monthly basis in Incentives Report.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170907 - </div>
               			<div class="listviewsubitem">Employees ordered by their respective last names in Incentives Report.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170906 - </div>
               			<div class="listviewsubitem">Issues on editing productions in BOM fixed (loading of price per item as set in BOM's template).</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170905 - </div>
               			<div class="listviewsubitem">Employees ordered by their respective last names in Shortages Report.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170905 - </div>
               			<div class="listviewsubitem">Sales formula updated. Negatives are considered zero in summary of Sales per Day.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170905 - </div>
               			<div class="listviewsubitem">Exporting for Shortages Report in .CSV file enabled.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170905 - </div>
               			<div class="listviewsubitem">Precentage formula fixed in Product Mix Report.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170904 - </div>
               			<div class="listviewsubitem">CSV file fixed in Product Mix Report.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170825 - </div>
               			<div class="listviewsubitem">Numerical format provided in Incentives Report Summary.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170808 - </div>
               			<div class="listviewsubitem">Numerical format provided in Shortage Report & Summary.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170807 - </div>
               			<div class="listviewsubitem">Date Range fixed in Sales Report</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170804 - </div>
               			<div class="listviewsubitem">Exporting for sales report in .CSV file enabled.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170804 - </div>
               			<div class="listviewsubitem">Automated summary of shortages and incentives provided.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170804 - </div>
               			<div class="listviewsubitem">Editing of UOM in delivery enabled.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170804 - </div>
               			<div class="listviewsubitem">Complete processing of sold qty and amt in IV Sheet automated.</div>
               		</div>
                	<div class="listviewitem">
               			<div class="listviewsubitem">20170804 - </div>
               			<div class="listviewsubitem">Processing of cash count and variance automated every map/unmap of deliveries/transfer.</div>
               		</div>
               		<div class="listviewitem">
               			<div class="listviewsubitem">20170804 - </div>
               			<div class="listviewsubitem">Distribution of shortages to crew factored in duty hours.</div>
               		</div>
-->
<!--
					<div class="listviewitem" style = "padding: 10px;">
               			<div class="listviewsubitem">- - - </div>
               			<div class="listviewsubitem">Transfer of Software Development Duties to Christian Ryan R. Macarse(macarse@coffeebreak.ph) - - -</div>
               		</div>
-->
					<div class="listviewitem">
               			<div class="listviewsubitem">20181105 - </div>
               			<div class="listviewsubitem">Added [ Send Feedback ] for user-to-developer assitance </div>
               		</div>
					<div class="listviewitem">
               			<div class="listviewsubitem">20181029 - </div>
               			<div class="listviewsubitem">GUI(Graphical User Interface) table overflow fix for Daily Sales Report </div>
               		</div>
					<div class="listviewitem">
               			<div class="listviewsubitem">20181025 - </div>
               			<div class="listviewsubitem">Removed hours of operation for Supervisors & Operations</div>
               		</div>
					<div class="listviewitem">
               			<div class="listviewsubitem">20181025 - </div>
               			<div class="listviewsubitem">Removed Personell on duty choices(C: Cashier, SC: Service Crew) </div>
               		</div>
					<div class="listviewitem">
               			<div class="listviewsubitem">20181024 - </div>
               			<div class="listviewsubitem">Added breakdown of total in product mix { Divided into 2 categories: Waffles and Beverages }</div>
               		</div>
					<div class="listviewitem">
               			<div class="listviewsubitem">20181024 - </div>
               			<div class="listviewsubitem">Disabled the editing of [Starting] in Inventory[manualinventory.php] on non-initial day of the month</div>
               		</div>
					<div class="listviewitem">
               			<div class="listviewsubitem">20181024 - </div>
               			<div class="listviewsubitem">Removed Inventory reading of Water, Sugar, & General Supplies</div>
               		</div>
					<div class="listviewitem">
               			<div class="listviewsubitem">20181024 - </div>
               			<div class="listviewsubitem">Change log starts.</div>
               		</div>
                <!--<?php
                    $mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt=$mysqli->stmt_init();
                    if($stmt->prepare("Select accountuserarea.unArea,AName
                                        from accountuserarea
                                        inner join area on accountuserarea.unArea=area.unArea
                                        where accountuserarea.`Status` = 1 and area.`Status`=1 and unAccountUser=?
                                        Order by AName")){
                        $stmt->bind_param('i',$oAccountUser->unAccountUser);
                        $stmt->execute();
                        $stmt->bind_result($unArea,$AName);
                        while($stmt->fetch()){
                        ?>

                            <div class="listviewitem" onClick="location.href='<?php echo $_SERVER['PHP_SELF']; ?>?&action=setarea&aid=<?php echo $unArea; ?>'" style="cursor:pointer;">
                                <div class="listviewsubitem"><?php echo $AName; ?></div>
                            </div>
                        <?php
                        }
                        $stmt->close();
                    }
                ?> -->
                </div>
            </div>

            <div align="center" style="padding-top:10px; padding-bottom:15px; margin:0 auto;">
                <input type="button" value="Close" title="Close" style = "float: right" onClick="location.href='#close'" class="buttons" >
            </div>
        </div>
    </div>
</div>

<div class="popup" id="popupsendfeedback">
		<div class="popupcontainer" style="width:600px">
			<div align="center" class="popuptitle">
				Send us a Feedback
			</div>
			<form action="include/sendfeedback.fnc.php" id="frmsendfeedback" method="post" name="frmsendfeedback">
				<div class="popupitem" style="width: 100%">
					<p><b>Kindly specify what concern do you have:</b></p><select name="FEEDBACKTYPE" required="">
						<option value = "" disabled selected>
							Please Select an Option
						</option>
						<option value = "Bug">
							Report a Bug
						</option>
						<option value = "Suggestion">
							Suggestions
						</option>
					</select>
					<p><b>Description:</b></p>
					<textarea name="FEEDBACKMESSAGE" rows="10" style="margin-bottom: 20px; width: 100%; resize: none" required></textarea>
					<div align="center" style="padding-top:10px; padding-bottom:15px; margin:0 auto;">
						<span style="float: right"><?php
						              if($oAccountUser->unAccountGroup == 1)
						              {
						              ?> <input class="buttons" name="btnSFGetList" title="This module is Visible ONLY for Administrators" type="button" value="Review Feedbacks"> <?php
						              }
						              ?> <input class="buttons" name="btnSFSend" title="Send Feedback" type="submit" value="Send Feedback"> <input class="buttons" onclick="location.href='#close'" title="Close" type="button" value="Close"></span>
					</div>
				</div>
			</form>
		</div>
	</div>

<div id="showmessagebox" class="msgbox">
    <div id="msgboxform" style="text-align:center;color:#FFF;font-weight:bold;font-size:14px;letter-spacing:2px; background-color:<?php echo $_SESSION['color']; ?>">
    	MESSAGE
        </br>
        </br>
	    <div id="msgboxcontent" class="msgboxcontent" style="margin-top:50px; background-color:<?php echo $_SESSION['color']; ?>"></div>
    </div>
</div>


<div id="titlebar" style=" background-color:<?php echo $_SESSION['color']; ?>"> <a href="index.php"><img src="img/<?php echo $_SESSION['BULogo']; ?>.png" style="margin-top:-10px;margin-bottom:-10px; background-color:<?php echo $_SESSION['color']; ?>" height="35"></a> <?php echo $_SESSION['BusinessUnit']; ?> Sales and Inventory System
	<?php
/*        $did = (isset($_GET['did']))?$_GET['did']:'';
        $bid = (isset($_GET['bid']))?$_GET['did']:'';
        $mid = (isset($_GET['mid']))?$_GET['mid']:'';
        $mad = (isset($_GET['mad']))?$_GET['mad']:'';
        echo GetInventoryControlData($did,$bid,$mid,$mad,$server,$username,$password,$database); */
    ?>
    <?php
		switch($_SERVER['PHP_SELF']){
			case $_SESSION['ParentPath'].'manualinventory.php':
			case $_SESSION['ParentPath'].'inventory.php':
			case $_SESSION['ParentPath'].'delivery.php':
			case $_SESSION['ParentPath'].'transfer.php':
			case $_SESSION['ParentPath'].'discount.php':
			case $_SESSION['ParentPath'].'damage.php':
			case $_SESSION['ParentPath'].'pettycash.php':
			case $_SESSION['ParentPath'].'sold.php':
				echo '- [Inventory Sheet]';
				break;

			case $_SESSION['ParentPath'].'employee.php':
			case $_SESSION['ParentPath'].'employeegroup.php':
				echo '- [Employee]';
				break;

			case $_SESSION['ParentPath'].'branch.php':
				echo '- [Branch]';
				break;

			case $_SESSION['ParentPath'].'device.php':
				echo '- [Device]';
				break;

			case $_SESSION['ParentPath'].'paymenttype.php':
				echo '- [Payment Type]';
				break;

			case $_SESSION['ParentPath'].'productitem.php':
			case $_SESSION['ParentPath'].'productgroup.php':
				echo '- [Item]';
				break;

			case $_SESSION['ParentPath'].'ptemplate.php':
				echo '- [Template [ '.ExecuteReader("Select TICName as `result` From templateitemcontrol Where unTemplateItemControl=".$_GET['id']).' ]]';
				break;

			case $_SESSION['ParentPath'].'productiontemplate.php':
				echo '- [Production [ '.ExecuteReader("Select TPBName as `result` From templateproductionbatch Where unTemplateProductionBatch=".$_GET['id']).' ]]';
				break;

			case $_SESSION['ParentPath'].'accountuser.php':
			case $_SESSION['ParentPath'].'accountgroup.php':
				echo '- [User]';
				break;

			case $_SESSION['ParentPath'].'area.php':
				echo '- [Area]';
				break;

			case $_SESSION['ParentPath'].'creatediscount.php':
			case $_SESSION['ParentPath'].'updatediscount.php':
				echo '- [Discount]';
				break;

			case $_SESSION['ParentPath'].'createdamage.php':
			case $_SESSION['ParentPath'].'updatedamage.php':
				echo '- [Damage]';
				break;

			case $_SESSION['ParentPath'].'createdelivery.php':
			case $_SESSION['ParentPath'].'updatedelivery.php':
				echo '- [Delivery]';
				break;

			case $_SESSION['ParentPath'].'createtransfer.php':
			case $_SESSION['ParentPath'].'updatetransfer.php':
				echo '- [Transfer]';
				break;

			case $_SESSION['ParentPath'].'createinvoice.php':
				echo '- [POS]';
				break;

			case $_SESSION['ParentPath'].'uom.php':
			case $_SESSION['ParentPath'].'uomsap.php':
				echo '- [Unit of Measure]';
				break;

		}
	?>
</div>

<div id="maintab">

        <li id="menufile">File
        	<ul>
            <a href="#"><li style="text-align:left;">New
            	<ul>
                <a href="#createinventorysheet" title="Create a new Inventory Form"><li ><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;">Inventory Sheet</li></a>
                <a href="#"><li ><div title="Create a new Delivery Form" onClick="redirect('createdelivery.php')"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;">Delivery</div></li></a>
                <a href="#"><li ><div title="Create a new Transfer Form" onClick="redirect('createtransfer.php')"><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;">Transfer</div></li></a>
                <a href="#"><li ><div title="Create a new Damage Form" onClick="redirect('createdamage.php')"><img src="img/icon/damagereturn.png" width="16" height="16" style="padding-right:10px;">Damage Return</div></li></a>
                <a href="#"><li ><div title="Create a new Petty Cash Form" onClick="redirect('createpettycash.php')"><img src="img/icon/pettycash.png" width="16" height="16" style="padding-right:10px;">Petty Cash</div></li></a>
                <a href="#createsales"><li ><img src="img/icon/sales.png" width="16" height="16" style="padding-right:10px;">Sales</li></a>
                </ul>
            </li></a>
        	<a href="#"><li style="text-align:left;">Open
            	<ul>
                <a href="#createinventorysheet" title="Open an Inventory Form" onClick="redirect('createdelivery.php')"><li><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;">Inventory Sheet
                <?php
                        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("Select inventorycontrol.unBranch,BName
                                            From inventorycontrol Inner Join branch on inventorycontrol.unBranch=branch.unBranch
                                            Where inventorycontrol.`Status`=1 and branch.`status`=1 and unArea=?
                                            Group By BName
                                            Order by BName")){
                        $stmt->bind_param('i',$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
						echo '<ul>';
                        while($stmt->fetch()){
                    ?>
                             <li><?php echo $BName;?>
                    <?php
                        }
						$stmt2=$mysqli->stmt_init();
						if($stmt2->prepare("Select concat(MonthName(ICDate),' ',Year(ICDate)) as `ICPeriod`, Year(ICDate) as `ICYear`, MonthName(ICDate) as `ICMonth`
							From inventorycontrol
							Where Status=1 and unBranch=?
							Group By ICPeriod
							Order By Year(ICDate) Desc, Month(ICDate)")){
						$stmt2->bind_param('i',$unBranch);
						$stmt2->execute();
						$stmt2->bind_result($CPeriod,$CYear,$CMonth);
						echo '<ul style="margin-left:50px;">';
						while($stmt2->fetch()){
						?>
                        		<li><?php echo $CPeriod; ?>
                        <?php
						}
						$stmt3=$mysqli->stmt_init();
						if($stmt3->prepare("Select unInventoryControl,ICNumber,ICInventoryNumber,ICRemarks
											From inventorycontrol
											Where Status=1 and unBranch=? and Year(ICDate)=? and MonthName(ICDate)=?
											Order By ICInventoryNumber Desc")){
						$stmt3->bind_param('iss',$unBranch,$CYear,$CMonth);
						$stmt3->execute();
						$stmt3->bind_result($unInventoryControl,$ICNumber,$ICInventoryNumber,$ICRemarks);
						echo '<ul style="margin-left:50px;">';
						while($stmt3->fetch()){
							?><li><div onClick="openinventory('inventory',<?php echo $unBranch; ?>,<?php echo $unInventoryControl; ?>)" style="cursor:pointer;">No.<?php echo substr('000000'.$ICInventoryNumber,-6);?> Sheet<?php echo $ICNumber?></div></li><?php
						}
						echo '</ul>';
						}
						echo '</li></ul>';
						}
						echo '</li></ul>';
						}
                    ?>
                </li></a>
                <a href="#"><li ><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;">Delivery
                <?php
                        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("Select deliverycontrol.unBranchTo,BName
								From deliverycontrol Inner Join branch on deliverycontrol.unBranchTo=branch.unBranch
								Where deliverycontrol.`Status`=1 and branch.`status`=1 and deliverycontrol.unArea=?
								Group By BName
								Order by BName")){
                        $stmt->bind_param('i',$_SESSION['area']);
                        $stmt->execute();
                        $stmt->bind_result($unBranch,$BName);
						echo '<ul>';
                        while($stmt->fetch()){
                    ?>
                             <li><?php echo $BName;?>
                    <?php
                        }
						$stmt2=$mysqli->stmt_init();
						if($stmt2->prepare("Select concat(MonthName(DCDate),' ',Year(DCDate)) as `DCPeriod`, Year(DCDate) as `DCYear`, MonthName(DCDate) as `DCMonth`
							From deliverycontrol
							Where Status=1 and unBranchTo=?
							Group By DCPeriod
							Order By Year(DCDate) Desc, Month(DCDate)")){
						$stmt2->bind_param('i',$unBranch);
						$stmt2->execute();
						$stmt2->bind_result($CPeriod,$CYear,$CMonth);
						echo '<ul style="margin-left:50px;">';
						while($stmt2->fetch()){
							echo '<li>'.$CPeriod;
						}
						$stmt3=$mysqli->stmt_init();
						if($stmt3->prepare("Select unDeliveryControl,DCDocNum,ICNumber
								From deliverycontrol
								Left Join inventorycontrol on deliverycontrol.unInventoryControl=inventorycontrol.unInventoryControl
								Where deliverycontrol.`Status`=1 and unBranchTo=? and Year(DCDate)=? and MonthName(DCDate)=?
								Order By DCDate Desc")){
						$stmt3->bind_param('iss',$unBranch,$CYear,$CMonth);
						$stmt3->execute();
						$stmt3->bind_result($unDeliveryControl,$DCDocNum,$ICNumber);
						echo '<ul style="margin-left:50px;">';
						while($stmt3->fetch()){
							?><li><div onClick="opendelivery(<?php echo $unDeliveryControl; ?>)" style="cursor:pointer;">No.<?php echo substr('000000'.$unDeliveryControl,-6);?> Doc<?php echo $DCDocNum?></div></li><?php
						}
						echo '</ul>';
						}
						echo '</li></ul>';
						}
						echo '</li></ul>';
						}
                    ?>
                </li></a>
                <a href="#"><li ><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;">Transfer
				<?php
                        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt2=$mysqli->stmt_init();
						if($stmt2->prepare("Select CPeriod,CYear,CMonth
					From
					(
					 Select concat(MonthName(TCDate), ' ', Year(TCDate)) as CPeriod, Year(TCDate) as CYear, MonthName(TCDate) as CMonth, Month(TCDate) as CMon, unBranchFrom as bFrom
						from transfercontrol
					 Union
					 Select concat(MonthName(TCDate), ' ', Year(TCDate)) as CPeriod, Year(TCDate) as CYear, MonthName(TCDate) as CMonth, Month(TCDate) as CMon, unBranchTo as bTo
						from transfercontrol
					) tablesource
					Group By CYear Desc,CMon")){
						$stmt2->execute();
						$stmt2->bind_result($CPeriod,$CYear,$CMonth);
						echo '<ul style="margin-left:50px;">';
						while($stmt2->fetch()){
							echo '<li>'.$CPeriod;
						}
						$stmt3=$mysqli->stmt_init();
						if($stmt3->prepare("Select unTransferControl,concat(MonthName(TCDate) , ' ' , Day(TCDate) , ', ' ,Year(TCDate)) as `TCPeriod`,TCNumber,branchfrom.BName,branchto.BName
							From transfercontrol
							Left Join branch as branchfrom on transfercontrol.unBranchFrom=branchfrom.unBranch
							Left Join branch as branchto on transfercontrol.unBranchTo=branchto.unBranch
							Where transfercontrol.`Status`=1 and (unBranchFrom=? or unBranchTo=?) And Year(TCDate)=? And MonthName(TCDate)=?
							Order By Year(TCDate) Desc, Month(TCDate) Asc, TCDate Desc")){
						$stmt3->bind_param('iiss',$unBranch,$unBranch,$CYear,$CMonth);
						$stmt3->execute();
						$stmt3->bind_result($unTransferControl,$TCPeriod,$TCNumber,$BranchFrom,$BranchTo);
						echo '<ul style="margin-left:50px;">';
						while($stmt3->fetch()){
							?><li><div onClick="openitf(<?php echo $unTransferControl; ?>)" style="cursor:pointer;">No.<?php echo substr('000000'.$unTransferControl,-6);?> ITF<?php echo $TCNumber; ?></div></li><?php
						}
						echo '</ul>';
						}
						echo '</li></ul>';
						}
                    ?>
                </li></a>
                <a href="#"><li ><img src="img/icon/damagereturn.png" width="16" height="16" style="padding-right:10px;">Damage Return
                <?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("Select unDamageControl,concat(MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate)) as `DCPeriod`,DCDocNum,BName,ICNumber,DCComments
											From damagecontrol
                                            Inner Join branch on damagecontrol.unBranchFrom=branch.unBranch
											Left Join inventorycontrol on damagecontrol.unInventoryControl=inventorycontrol.unInventoryControl
											Where damagecontrol.`Status`=1 and damagecontrol.unArea=? Order By DCDate")){
                            $stmt->bind_param('i',$_SESSION['area']);
                            $stmt->execute();
                            $stmt->bind_result($unDamageControl,$DCPeriod,$DCDocNum,$BName,$ICNumber,$DCComments);
							echo '<ul>';
                            while($stmt->fetch()){
                        ?>
                        	<li><div onClick="opendamage(<?php echo $unDamageControl; ?>)" style="cursor:pointer;">No.<?php echo substr('000000'.$unDamageControl,-6);?> Doc<?php echo substr('000000'.$ICNumber,-6).' '.$BName.' '.$DCPeriod; ?></div></li>
                        <?php
                            }
							echo '</ul>';
                        $stmt->close();
                        }
                ?>
                </li></a>
                <a href="#"><li ><img src="img/icon/pettycash.png" width="16" height="16" style="padding-right:10px;">Petty Cash
                <?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("SELECT unPettyCashControl,PCCReferenceNumber,ICNumber,PCCAmount,PCCDate FROM pettycashcontrol Left Join inventorycontrol On pettycashcontrol.unInventoryControl = inventorycontrol.unInventoryControl WHERE pettycashcontrol.`Status`=1 Order by By Year(PCCDate) Desc, Month(PCCDate) Asc")){
                            $stmt->execute();
                            $stmt->bind_result($unPettyCashControl,$PCCReferenceNumber,$ICNumber,$PCCAmount,$PCCDate);
							echo '<ul>';
                            while($stmt->fetch()){
                        ?>
                        	<li><div onClick="redirect('createpettycash.php?&id=<?php echo $unPettyCashControl; ?>')" style="cursor:pointer;">Reference No.<?php echo $PCCReferenceNumber;?> Doc<?php echo substr('000000'.$ICNumber,-6).' '.$PCCAmount.' '.$PCCDate; ?></div></li>
                        <?php
                            }
							echo '</ul>';
                        $stmt->close();
                        }
                ?>
                </li></a>
                <a href="#createsales" title="Open a Sales Form"><li ><img src="img/icon/sales.png" width="16" height="16" style="padding-right:10px;">Sales
				<?php
                        $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("SELECT unInvoiceControl,ICNumber,unInventoryControl,ICTotalSales,BName,ICDate
											FROM invoicecontrol Inner Join branch on invoicecontrol.unBranch = branch.unBranch
											WHERE invoicecontrol.unArea=? and invoicecontrol.`Status`=1 Order By ICDate")){
                            $stmt->bind_param('i',$_SESSION['area']);
                            $stmt->execute();
                            $stmt->bind_result($unInvoiceControl,$ICNumber,$unInventoryControl,$ICTotalSales,$BName,$ICDate);
                            while($stmt->fetch()){
                        ?>
                        	<li><div onClick="openinvoice(<?php echo $unInvoiceControl; ?>)" style="cursor:pointer;">Invoice No.<?php echo substr('000000'.$ICNumber,-6);?> Sheet<?php echo substr('000000'.$unInvoiceControl,-6).' '.$ICTotalSales.' '.$BName.' '.$ICDate; ?></div></li>
                        <?php
                            }
                        $stmt->close();
                        }
                ?>
                </li></a>
                </ul>
            </li></a>
        	<a href="#"><li style="text-align:left;">Recent
            	<ul>
                <!--<ul>
                <a href="#createinventorysheet"><li >Inventory Sheet</li></a>
                <a href="#"><li ><div title="Create a new Delivery Form" onClick="redirect('createdelivery.php')">Delivery</div></li></a>
                <a href="#"><li >Interbranch Transfer Form</li></a>
                <a href="#"><li >Damage Return</li></a>
                <a href="#createinvoice"><li >Invoice</li></a>
                <a href="#"><li >Petty Cash</li></a>
                </ul>
            </li></a>
        	<a href="#showopenitem"><li style="text-align:left;">Open</li></a>
        	<a href="#"><li style="text-align:left;">Recent
            	<ul>-->
            	<?php
                        $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                        $stmt=$mysqli->stmt_init();
                        if($stmt->prepare("SELECT unAccountDocument,ADSource,ADType FROM accountdocument WHERE `Status`=1 and unAccountUser=? Order By TimeStamp Desc Limit 9")){
						$stmt->bind_param('i',$oAccountUser->unAccountUser);
                        $stmt->execute();
                        $stmt->bind_result($unAccountDocument,$ADSource,$ADType);
                        while($stmt->fetch()){

							$mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
							$stmt1=$mysqli1->stmt_init();
							switch($ADType){
								Case 1:
									$squery="Select branch.BName,concat(' [ ',MonthName(ICDate) , ' ' , Day(ICDate) , ', ' ,Year(ICDate),' - ',ICNumber,' ]') as `DocumentName`,branch.unBranch,unInventoryControl,inventorycontrol.`TimeStamp`,ICInventoryNumber From inventorycontrol Inner Join branch on inventorycontrol.unBranch=branch.unBranch Where inventorycontrol.`Status`=1 and branch.unArea=? and unInventoryControl=?";
									break;
								Case 2:
									$squery="Select TCNumber,concat(' [ ',MonthName(TCDate) , ' ' , Day(TCDate) , ', ' ,Year(TCDate),' - ',TCNumber,' ]') as `DocumentName`,unTransferControl,unTransferControl,`TimeStamp`,1 From transfercontrol Where transfercontrol.`Status`=1 and unArea=? and unTransferControl=?";
									break;
								Case 3:
									$squery="Select branch.BName,concat(' [ ',MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate),' - ',DCDocNum,' ]') as `DocumentName`,unBranchTo,unDeliveryControl,deliverycontrol.`TimeStamp`,1 From deliverycontrol Inner Join branch on deliverycontrol.unBranchTo=branch.unBranch Where deliverycontrol.`Status`=1 and deliverycontrol.unArea=? and unDeliveryControl=?";
									break;
								Case 4:
									$squery="Select branch.BName,concat(' [ ',MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate),' - ',DCReference,' ]') as `DocumentName`,discountcontrol.unBranch,unDiscountControl,discountcontrol.`TimeStamp`,1 From discountcontrol Inner Join branch on discountcontrol.unBranch=branch.unBranch Where discountcontrol.`Status`=1 and discountcontrol.unArea=? and unDiscountControl=?";
									break;
								Case 5:
									$squery="Select branch.BName,concat(' [ ',MonthName(DCDate) , ' ' , Day(DCDate) , ', ' ,Year(DCDate),' - ',DCDocNum,' ]') as `DocumentName`,damagecontrol.unBranchFrom,unDamageControl,damagecontrol.`TimeStamp`,1 From damagecontrol Inner Join branch on damagecontrol.unBranchFrom=branch.unBranch Where damagecontrol.`Status`=1 and damagecontrol.unArea=? and unDamageControl=?";
									break;
								Case 6:
									$squery="Select branch.BName,concat(' [ ',MonthName(ICDate) , ' ' , Day(ICDate) , ', ' ,Year(ICDate),' - ',ICNumber,' ]') as `DocumentName`,invoicecontrol.unBranch,unInvoiceControl,invoicecontrol.`TimeStamp`,1 From invoicecontrol Inner Join branch on invoicecontrol.unBranch=branch.unBranch Where invoicecontrol.`Status`=1 and invoicecontrol.unArea=? and unInvoiceControl=?";
									break;
							}
							if($stmt1->prepare($squery)){
							$stmt1->bind_param('ii',$_SESSION['area'],$ADSource);
							$stmt1->execute();
							$stmt1->bind_result($BName,$DocumentName,$unBranch,$unInventoryControl,$TimeStamp,$Reference);
							while($stmt1->fetch()){
								switch($ADType){
									Case 1:
									?>
										<a href="#"><li><div onClick="openinventory('inventory',<?php echo $unBranch; ?>,<?php echo $unInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;"><?php echo 'INV '.$BName.' - '.substr('000000'.$Reference,-6).' '.$DocumentName; ?></div></li></a>
									<?php
										break;
									Case 2:
									?>
										<a href="#"><li><div onClick="openitf(<?php echo $unInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/itf.png" width="16" height="16" style="padding-right:10px;"><?php echo 'ITF '.$DocumentName; ?></div></li></a>
									<?php
										break;
									case 3:
									?>
                                    	<a href="#"><li><div onClick="opendelivery(<?php echo $unInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/delivery.png" width="16" height="16" style="padding-right:10px;"><?php echo 'DR '.$DocumentName; ?></div></li></a>
                                    <?php
										break;
									case 4:
									?>
                                    	<a href="#"><li><div onClick="opendiscount(<?php echo $unInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/discount.png" width="16" height="16" style="padding-right:10px;"><?php echo 'DSC '.$BName.' '.$DocumentName; ?></div></li></a>
                                    <?php
										break;
									case 5:
									?>
                                    	<a href="#"><li><div onClick="opendamage(<?php echo $unInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/damagereturn.png" width="16" height="16" style="padding-right:10px;"><?php echo 'DMG '.$BName.' '.$DocumentName; ?></div></li></a>
                                    <?php
										break;
									case 6:
									?>
                                    	<a href="#"><li><div onClick="openinvoice(<?php echo $unInventoryControl; ?>)" title="Last opened <?php echo $TimeStamp; ?>"><img src="img/icon/invoice.png" width="16" height="16" style="padding-right:10px;"><?php echo 'INV '.$BName.' '.$DocumentName; ?></div></li></a>
                                    <?php
										break;
								}
							}
							$stmt1->close();
							}
                          }
                        $stmt->close();
                        }?>
                    </ul>
            </li></a>
            <li><a href="#showimport">Import</a></li>
            </ul>
        </li>
        <li id="menuedit">Edit
        	<ul>
           	 <?php
                    if(ExecuteReader("Select AGName as `result` from accountgroup where unAccountGroup=".$oAccountUser->unAccountGroup)=='Administrator'){
              ?>
            	 <a href="#"><li ><div title="Manage User masterlist (These are the people who access this system)" onClick="redirect('accountuser.php')"><img src="img/icon/user.png" style="padding-right:10px;">User</div></li></a>
                 <a href="#"><li ><div title="Manage Area - A cluster of Branches that belong to a certain geographic entity" onClick="redirect('area.php')"><img src="img/icon/employeearea.png" style="padding-right:10px;">Area</div></li></a>
                 <a href="#"><li ><div title="Create a new Branch/Outlet" onClick="redirect('branch.php')"><img src="img/icon/branch.png" width="16" height="16" style="padding-right:10px;">Branch</div></li></a>
            	 <a href="#"><li ><div title="Create a new Employee (such as Service Crews, Cashiers and Managers)" onClick="redirect('employee.php')"><img src="img/icon/employee.png" width="16" height="16" style="padding-right:10px;">Employee</div></li></a>
                 <a href="#"><li ><div title="Create a new Device" onClick="redirect('device.php')"><img src="img/icon/device.png" width="16" height="16" style="padding-right:10px;">Device</div></li></a>
                 <a href="#"><li ><div title="Create a new Payment Type" onClick="redirect('paymenttype.php')"><img src="img/icon/paymenttype.png" width="16" height="16" style="padding-right:10px;">Payment Type</div></li></a>
                 <a href="#"><li ><div title="Create a new Discount Type" onClick="redirect('discounttype.php')"><img src="img/icon/discounttype.png" width="16" height="16" style="padding-right:10px;">Discount Type</div></li></a>
             <?php
                    }
             ?>
                 <a href="#"><li ><div title="Manage Unit of Measure conversion value and SAP code" onClick="redirect('uom.php')"><img src="img/icon/uom.png" style="padding-right:10px;">Unit Of Measure</div></li></a>
                 <a href="#"><li ><div title="Manage Item and Rawmat masterlist" onClick="redirect('productitem.php?&type=1')"><img src="img/icon/productitem.png" style="padding-right:10px;">Item</div></li></a>
                 <a href="#"><li title="Create a new Item Temple"><div><img src="img/icon/update.png" width="16" height="16" style="padding-right:10px;">Item Template
                 <ul>
                 <?php
                $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt=$mysqli->stmt_init();
                if($stmt->prepare("Select unTemplateItemControl,TICName From templateitemcontrol where `Status`=1 Order by TICName")){
                    $stmt->execute();
                    $stmt->bind_result($unTemplateItemControl,$TICName);
                    while($stmt->fetch()){
                ?>
                    <li><div title="Manage Template - A customized list of products which can be applied to Branches/Outlets" onClick="redirect('ptemplate.php?&id=<?php echo $unTemplateItemControl; ?>&type=1')"><img src="img/icon/producttemplate.png" style="padding-right:10px;"><?php echo $TICName; ?></div></li>
                <?php
                    }
                $stmt->close();
                }
                ?>
                 </ul></div></li><a href="#">
                 <a href="#"><li ><div title="Create a new Production Template"><img src="img/icon/update.png" width="16" height="16" style="padding-right:10px;">Production Template
                 <ul>
                 <?php
                $mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt=$mysqli->stmt_init();
                if($stmt->prepare("SELECT unTemplateProductionBatch, TPBName FROM templateproductionbatch WHERE Status=1 Order by TPBName")){
                    $stmt->execute();
                    $stmt->bind_result($unTemplateItemBatch,$TPBName);
                    while($stmt->fetch()){
                ?>
					<li><div title="Manage Production" onClick="redirect('productiontemplate.php?&id=<?php echo $unTemplateItemBatch; ?>')"><img src="img/icon/production.png" style="padding-right:10px;"><?php echo $TPBName; ?></div></li>
                <?php
                    }
                $stmt->close();
                }
                ?>
                 </ul></div></li></a>
            </ul>
        </li>
        <?php if($_SESSION['BusinessUnit']=='Waffletime Inc.,'){ ?>
        <!--<li id="menuview">Reports
        	<ul>
        	<!--<a href="#"><li >Reports</li></a>-->
            	<!--<li><a href="wtidiscmatrix.php" target="_blank">Discrepancy Matrix</a></li>
            	<li><a href="crewshortagesreport.php" target="_blank">Crew Shortages</a></li>
            	<li><a href="dailysalesreport.php" target="_blank">Sales Report</a></li>
            </ul>
        </li> -->
        <?php } ?>
			<li id="menutools"><a href="#popuparea"  style="font-size: 12px;color: #333;">Area</a> </li>
        <?php if($_SESSION['BusinessUnit']=='Waffletime Inc.,'){ ?>
		<li id="menutools"><a href="#popupversion" style="font-size: 12px;color: #333;">Version Info</a>
        	<!--<ul>
            	 <a href="#"><li ><div title="Create a new Employee (such as Service Crews, Cashiers and Managers)" onClick="redirect('employee.php')">Employee</div></li></a>
                 <a href="#"><li ><div title="Create a new Branch/Outlet" onClick="redirect('branch.php')">Branch</div></li></a>
                 <a href="#"><li ><div title="Create a new Device" onClick="redirect('device.php')">Device</div></li></a>
            </ul>-->
        </li>
        <?php } ?>
        <li >Help</li>
		<li id = "menutools" >
		  <a href="#popupsendfeedback" style="font-size: 12px;color: #333;">Send Feedback</a>
      </li>
		<strong></strong>
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
<!--
<div id="maintab1">
</div>
-->

<?php
	$bid=(isset($_GET['bid'])=='')?'':$_GET['bid'];
	$did=(isset($_GET['did'])=='')?'':$_GET['did'];
	$type=(isset($_GET['type'])=='')?'':$_GET['type'];

	if($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'manualinventory.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'cashbreakdown.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'inventory.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'delivery.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'transfer.php' ||
		$_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'discount.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'damage.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'sold.php' ||
		$_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'pettycash.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
            	<?php
					if(isset($_GET['lock'])){
						if($_GET['lock']==0 && ExecuteReader("Select AGName as `result` from accountgroup where unAccountGroup=".$oAccountUser->unAccountGroup)=='Administrator'){
							ExecuteNonQuery("Update inventorycontrol Set ICLock=0 Where unInventoryControl=".$_GET['did']);
						}elseif($_GET['lock']==1){
							ExecuteNonQuery("Update inventorycontrol Set ICLock=1 Where unInventoryControl=".$_GET['did']);
						}
					}
					$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
					if($stmt->prepare("Select ICLock from inventorycontrol Where unInventoryControl=?")){
						$stmt->bind_param('i',$_GET['did']);
						$stmt->execute();
						$stmt->bind_result($ICLock);
						$stmt->fetch();
						$stmt->close();
					}
					if ($ICLock==1){
							if(ExecuteReader("Select AGName as `result` from accountgroup where unAccountGroup=".$oAccountUser->unAccountGroup)=='Administrator'){
					?>
				                <li><img src="img/icon/lockedblack.png" width="16" height="16" title="This sheet is locked. Click to unlock." onClick="msgbox('Unlocking the sheet, Are you sure?','<?php echo $_SERVER['PHP_SELF'].'?&bid='.$bid.'&did='.$did.'&type='.$type.'&lock=0'; ?>','')" style="cursor:pointer;" title="This sheet is locked. Click to unlock." onClick="msgbox('Unlocking the sheet, Are you sure?','<?php echo $_SERVER['PHP_SELF'].'?&bid='.$bid.'&did='.$did.'&type='.$type.'&lock=0'; ?>','')" style="cursor:pointer;" ></li>
					<?php
                        	}else{
					?>
				                <li><img src="img/icon/lockedblack.png" width="16" height="16" title="This sheet is locked. To unlock, ask your Administrator." onClick="msgbox('You do not have enough previlege to unlock this sheet. Contact your Administrator','','')"style="cursor:pointer;" title="This sheet is locked. To unlock, ask your Administrator." onClick="msgbox('You do not have enough previlege to unlock this sheet. Contact your Administrator','','')"style="cursor:pointer;"></li>
                    <?php
							}
					}else{
					?>
		                <li><img src="img/icon/unlockedblack.png" width="16" height="16" title="This sheet is not locked. To Prevent accidental changes, click me to lock." onClick="msgbox('You are about to lock this sheet. Remember that unlocking requires administrative control. Are you sure?','<?php echo $_SERVER['PHP_SELF'].'?&bid='.$bid.'&did='.$did.'&type='.$type.'&lock=1'; ?>','')" style="cursor:pointer;"></li>
					<?php
					}
				?>
                <?php if($_SESSION['BusinessUnit']=='Waffletime Inc.,'){ ?>
                <li><a href="manualinventory.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'manualinventory.php')?'style="background-color:#FFF;background-image:none; color:#000;"':''; ?> style=""><img src="img/icon/inventory.png" width="16" height="16">Inventory</a></li>
                <li><a href="cashbreakdown.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'cashbreakdown.php')?'style="background-color:#FFF;background-image:none; color:#000;"':''; ?> style=""><img src="img/icon/pettycash.png" width="16" height="16">Cash & Crew</a></li>
                <?php }?>
                <?php if($_SESSION['BusinessUnit']!='Waffletime Inc.,'){ ?>
                <li><a href="inventory.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'inventory.php')?'style="background-color:#FFF;background-image:none; color:#000;"':''; ?> style=""><img src="img/icon/inventory.png" width="16" height="16">Inventory</a></li>
                <?php }?>
                <li><a href="delivery.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'delivery.php')?'style="background-color:#FFF;background-image:none; color:#000;"':''; ?> style=";"><img src="img/icon/delivery.png" width="16" height="16">Delivery</a></li>
                <li><a href="transfer.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'transfer.php')?'style="background-color:#FFF;background-image:none; color:#000;"':''; ?> style=""><img src="img/icon/itf.png" width="16" height="16">Transfer</a></li>
                <li><a href="damage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'damage.php')?'style="background-color:#FFF;background-image:none; color:#000;"':''; ?> style=""><img src="img/icon/damagereturn.png" width="16" height="16">Damage</a></li>
                <?php if($_SESSION['BusinessUnit']!='Waffletime Inc.,'){ ?>
                <li><a href="sold.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'sold.php')?'style="background-color:#FFF;background-image:none; color:#000;"':''; ?> style=""><img src="img/icon/sales.png" width="16" height="16">Sold</a></li>
                <?php }?>
                <!--<li><a href="discount.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'discount.php')?'style="background-color:#FFF; color:#000;"':''; ?> style="color:#333;"><img src="img/icon/discount.png" width="16" height="16">Discount</a></li>
                <!--<li><a href="pettycash.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'pettycash.php')?'style="background-color:#FFF; color:#000;"':''; ?>><img src="img/icon/paymenttype.png" width="16" height="16">Payment</a></li>
                <li><a href="giftcertificate.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'giftcertificate.php')?'style="background-color:#FFF; color:#000;"':''; ?>><img src="img/icon/giftcertificate.png" width="16" height="16">Gift Certificate</a></li>-->
                <li><a href="pettycash.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'pettycash.php')?'style="background-color:#FFF;background-image:none; color:#000;"':''; ?> style=""><img src="img/icon/pettycash.png" width="16" height="16">Petty Cash</a></li>
                <!--<li><a href="sale.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'sale.php')?'style="background-color:#FFF; color:#000;"':''; ?>><img src="img/icon/sales.png" width="16" height="16">Sales</a></li>-->
            </ul>

            <ul id="nav" style="z-index:1">
               <?php if($_SESSION['BusinessUnit']=='Waffletime Inc.,'){ ?>
                <li style="padding-left:10px;cursor:pointer; padding-top:2.5px;">Reports ▼
                	<ul>
                    	<li><a href="productmix.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Product Mix</a></li>
                    	<li><a href="dailysalesreport.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Sales Report</a></li>
                    	<li><a href="crewshortagesreport.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Crew Shortages</a></li>
                    	<li><a href="crewincentivesreport.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Crew Incentives</a></li>
                    	<li><a href="crewFOMreport.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">FOM Incentives</a></li>
                    	<!--<li><a href="wtishortage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Shortages</a></li>
                    	<!--<li><a href="incentive.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Incentives</a></li>
                        <li><a href="cos.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">COS</a></li>
                    	<li><a href="#">Discounts</a></li>
                    	<li><a href="#">Petty Cash Movement</a></li>
                    	<li><a href="reportdelivery.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Delivery</a></li>
                		<li><a href="reportdamage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Damages</a></li>
                        <li><a href="reportitf.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type.'&ttype=1'; ?>">Transfers</a></li>-->
                    </ul>
                </li>
                <?php }else{ ?>
				<li style="padding-left:10px;cursor:pointer; padding-top:2.5px;">Reports ▼
                	<ul>
                    	<li><a href="productmix.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Product Mix</a></li>
                    	<li><a href="wtishortage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>" target="_blank">Shortages</a></li>
                    	<!--<li><a href="incentive.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Incentives</a></li>
                        <li><a href="cos.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">COS</a></li>
                    	<li><a href="#">Discounts</a></li>
                    	<li><a href="#">Petty Cash Movement</a></li>
                    	<li><a href="reportdelivery.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Delivery</a></li>
                		<li><a href="reportdamage.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type; ?>">Damages</a></li>
                        <li><a href="reportitf.php<?php echo '?&bid='.$bid.'&did='.$did.'&type='.$type.'&ttype=1'; ?>">Transfers</a></li>-->
                    </ul>
                </li>
				<?php } ?>
            </ul>

            <ul id="nav" style="position:absolute;right:10px;z-index:0;padding-top:2.5px;z-index:2">
               <li>
               		<?php
						$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt = $mysqli->stmt_init();
						if($stmt->prepare("Select BName,Concat(MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate),' - ',ICNumber) as `ICPeriod`, EXTRACT(YEAR_MONTH FROM ICDate), ICInventoryNumber from inventorycontrol Inner Join branch On inventorycontrol.unBranch=branch.unBranch Where unInventoryControl=?")){
							$stmt->bind_param('i',$_GET['did']);
							$stmt->execute();
							$stmt->bind_result($BName,$ICPeriod,$ICDate,$ICInventoryNumber);
							$stmt->fetch();
							$stmt->close();
						}
					?>
                    <a href="#" style="background-image:url(../img/background_pattern.png);"><?php echo $BName; ?> - <?php echo substr('000000'.$ICInventoryNumber,-6); ?> [ <?php echo $ICPeriod; ?> ]</a>
                    <ul style="right:0px;z-index:2">
                    <?php
						$mysqli=new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
						$stmt=$mysqli->stmt_init();
						if($stmt->prepare("Select unInventoryControl, Concat('[ ',MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate),' - ',ICNumber,' ]') as `ICPeriod`, ICInventoryNumber, ICLock from inventorycontrol Where unBranch=? AND EXTRACT(YEAR_MONTH FROM ICDate) = ? Order by Year(ICDate) Desc, Month(ICDate) Desc, ICDate Desc")){
							$stmt->bind_param('is',$_GET['bid'],$ICDate);
							$stmt->execute();
							$stmt->bind_result($unInventoryControl,$ICPeriod,$ICInventoryNumber,$ICLockSheet);
							while($stmt->fetch()){
								if($unInventoryControl!=$_GET['did']){
							?>
                                <li style="cursor:pointer;"><a onClick="openinventory('sheet',<?php echo $_GET['bid']; ?>,<?php echo $unInventoryControl; ?>)"><img src="img/icon/<?php echo ($ICLockSheet==1)? 'locked.png':'nocheck.png'; ?>" width="16" height="16" style="padding-right:10px;"><?php echo substr('000000'.$ICInventoryNumber,-6).' '.$ICPeriod; ?></a></li>
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
	}elseif($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'employee.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'employeegroup.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="employee.php" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'employee.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Employee</a></li>
                <li><a href="employeegroup.php" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'employeegroup.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Group</a></li>
            </ul>
    	</div>
        <?php
	}elseif($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'accountuser.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'accountgroup.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="accountuser.php" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'accountuser.php')?'style="background-color:#FFF; color:#000;"':''; ?>>User</a></li>
                <li><a href="accountgroup.php" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'accountgroup.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Group</a></li>
            </ul>
    	</div>
        <?php
	}elseif($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'productitem.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'productgroup.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="productitem.php?&type=<?php echo $_GET['type']; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'productitem.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Item</a></li>
                <li><a href="productgroup.php?&type=<?php echo $_GET['type']; ?>" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'productgroup.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Group</a></li>
            </ul>
    	</div>
        <?php
	}elseif($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'uom.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'uomsap.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="uom.php" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'uom.php')?'style="background-color:#FFF; color:#000;"':''; ?>>UOM</a></li>
                <li><a href="uomsap.php" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'uomsap.php')?'style="background-color:#FFF; color:#000;"':''; ?>>SAP UOM</a></li>
            </ul>
    	</div>
        <?php
	}elseif($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'templateproductionbatch.php' || $_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'templateitemcontrol.php'){
		?>
        <div id="maintab2">
            <ul id="nav2">
                <li><a href="templateitemcontrol.php" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'templateitemcontrol.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Template</a></li>
                <li><a href="templateproductionbatch.php" <?php echo ($_SERVER['PHP_SELF']==$_SESSION['ParentPath'].'templateproductionbatch.php')?'style="background-color:#FFF; color:#000;"':''; ?>>Production</a></li>
            </ul>
    	</div>
        <?php
	}

?>

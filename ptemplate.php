<?php include 'header.php'; ?>

<link rel="stylesheet" type="text/css" href="css/producttemplate.css">
<link rel="stylesheet" type="text/css" href="css/listview.css">

<?php

if (isset($_GET['del'])){
	$query="Update templateitemdata set `Status`=0 where idTemplateItemData=".$_GET['TID'];
	ExecuteNonQuery($query);
	header('location:ptemplate.php?&id='.$_GET['id']);
}

	$clsSidePI;
	$clsMainPI; 
	$_SESSION['isUpdated']= 0;
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt = $mysqli->prepare("Select unProductItem,PIName, producttype.unProductType, producttype.PTName, productitem.unProductGroup, productgroup.PGName  
									From productitem 
									Inner Join productgroup
									On productgroup.unProductGroup = productitem.unProductGroup 
									Inner Join producttype
									On producttype.unProductType = productgroup.unProductType
									Where productgroup.unProductType = ? AND productitem.Status=1
									Order By PIName ASC")){
			$stmt->bind_param('i',$_GET['type']);
			$stmt->execute();
			$stmt->bind_result($unProductItem,$PIName,$unProductType,$PTName, $unProductGroup, $PGName);
			//$stmt->fetch();
			$stmt->store_result();
			$numrows=$stmt->num_rows();
			//echo $numrows;
			while($stmt->fetch())
			{
				//echo ($unProductItem.$PIName.'<br>'.$unProductType.'<br>'.$PTName.$unProductGroup.$PGName);
				$oProductItem = new ProductItemTemp($unProductItem, $unProductGroup, '', '', $PIName, '', '', '', '', '', false, $unProductType, $PTName, $PGName);
				$colSideProductItemPageLoad->Add($oProductItem,$oProductItem->unProductItem);			
			}
		$stmt->close();
		$mysqli->close();
		}
	$_SESSION['colSideProductItemPageLoad'] = $colSideProductItemPageLoad;
	function saveproducttemplate($unTemplateItemControl, $unProductItem, $TIDPrice, $TIDCost, $TIDPriority)
	{
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO templateitemdata(unTemplateItemControl, unProductItem, TIDPrice, TIDCost, TIDPriority) VALUES (?,?,?,?,?)"))
		{
			$stmt->bind_param("iiddi", $unTemplateItemControl, $unProductItem, $TIDPrice, $TIDCost, $TIDPriority);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}

	function updateproducttemplate($idTemplateItemData, $unTemplateItemControl, $unProductItem, $TIDPrice, $TIDCost, $TIDPriority)
	{
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('UPDATE templateitemdata 
						   SET unTemplateItemControl=?, unProductItem=?, TIDPrice=?, TIDCost=?, TIDPriority=? 
						   WHERE idTemplateItemData=?'))
		{
			$stmt->bind_param("iiddii", $unTemplateItemControl, $unProductItem, $TIDPrice, $TIDCost, $TIDPriority, $idTemplateItemData);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}
	
	function DeleteTemplateItemData($idTemplateItemData)
	{
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('UPDATE templateitemdata 
						   SET Status=0 
						   WHERE idTemplateItemData=?'))
		{			
			$stmt->bind_param("i", $idTemplateItemData);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}
	
	function InsertTemplateItemData($unTemplateItemControl, $unProductItem)
	{
		$TIDPrice =0;
		$TIDCost=0; 
		$TIDPriority=0;
		
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();		
		if($stmt->prepare("INSERT INTO templateitemdata(unTemplateItemControl, unProductItem, TIDPrice, TIDCost, TIDPriority) VALUES (?,?,?,?,?)"))
		{ 
			$stmt->bind_param("iiiii", $unTemplateItemControl, $unProductItem, $TIDPrice, $TIDCost, $TIDPriority);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}

	function UpdateTemplateItemData($idTemplateItemData, $unTemplateItemControl, $unProductItem)
	{
		$TIDPrice=0;
		$TIDCost=0;
		$TIDPriority=0;
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('UPDATE templateitemdata 
						   SET TIDPrice=?,TIDCost=?,TIDPriority=?,Status=1 
						   WHERE idTemplateItemData=?'))
		{
			$stmt->bind_param("iiii", $TIDPrice, $TIDCost, $TIDPriority, $idTemplateItemData);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}

	if(isset($_POST['labeltransferitem']))
	{
		$colSideProductItem = $_SESSION['colSideItemsAfterTransfer'] ;
		for($e=1;$e <= $colSideProductItem->Count() - 1;$e++)
		{
			$clsSidePI = $colSideProductItem->GetByIndex($e);
			
			if(isset($_POST['chkitem'.$e]))
			{
				$clsSidePI->CheckBool = true;
			}
			else
			{
				$clsSidePI->CheckBool = false;
			}
		}
		for($e=1;$e<=$colSideProductItem->Count() - 1;$e++)
		{
			$clsSidePI = $colSideProductItem->GetByIndex($e);
			if ($clsSidePI->CheckBool == true)
			{	
				$unTemplate = $_GET['id'];
				$querySelect = "Select idTemplateItemData as 'result' 
								From templateitemdata 
								Where unTemplateItemControl=".$unTemplate." And unProductItem=".$clsSidePI->unProductItem;
				$isItemExist = ExecuteReader($querySelect);
				
				if($isItemExist == 0)
				{
					InsertTemplateItemData($unTemplate, $clsSidePI->unProductItem);
				}
				else
				{
					$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
					$stmt = $mysqli->stmt_init();
									
					if ($stmt->prepare("Select idTemplateItemData, unTemplateItemControl, unProductItem 
									From templateitemdata 
									Where unTemplateItemControl=? And unProductItem=? And Status=0"))
						{
							$stmt->bind_param("ii", $unTemplate, $clsSidePI->unProductItem);
							$stmt->execute();
							$stmt->bind_result($idTemplateItemData, $unTemplateItemControl, $unProductItem );
							while($stmt->fetch())
							{			
									UpdateTemplateItemData($idTemplateItemData, $unTemplateItemControl, $unProductItem);
							}
							$stmt->close();
						}
				}
			}
			else
			{
				DeleteTemplateItemData($clsSidePI->idTemplateItemData);
			}			
		}	
	}

		$colSideProductItem = $_SESSION['colSideProductItemPageLoad'];
	
		$mysqli2 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt2 = $mysqli2->stmt_init();
		if($stmt2 = $mysqli2->prepare("Select idTemplateItemData, unTemplateItemControl, templateitemdata.unProductItem, 
									TIDPrice, TIDCost, TIDPriority, templateitemdata.Status, producttype.unProductType, 
									producttype.PTName, productitem.unProductGroup, productgroup.PGName 
									From templateitemdata
									Inner Join productitem
									On productitem.unProductItem = templateitemdata.unProductItem
									Inner Join productgroup
									On productgroup.unProductGroup = productitem.unProductGroup
									Inner Join producttype
									On producttype.unProductType = productgroup.unProductType
									WHERE templateitemdata.Status=1 AND productgroup.unProductType=? AND unTemplateItemControl=?
									Order By producttype.unProductType ASC, productgroup.PGPriority ASC, TIDPriority ASC, productitem.PIName ASC")){
			$stmt2->bind_param("ii",$_GET['type'],$_GET['id']);
			$stmt2->execute();
			$stmt2->bind_result($unTemplateItemData, $unTemplateItemControl, $unProductItem, $TIDPrice, $TIDCost, $TIDPriority, $Status, $unProductType, $PTName, $unProductGroup, $PGName);
			while($stmt2->fetch())
			{	
				//echo ($idTemplateItemData."<br>".$unTemplateItemControl."<br>".$unProductItem."<br>".$TIDPrice."<br>".$TIDCost."<br>".$TIDPriority."<br>".$Status."<br>".$unProductType."<br>".$PTName."<br>".$unProductGroup."<br>".$PGName);		
				$oTemplateItemData = new TemplateItemData($unTemplateItemControl, $unProductItem, $unProductGroup, $TIDPrice, $TIDCost, $TIDPriority, $Status, $unTemplateItemData,$unProductType, $PTName, $PGName);
				$colMainProductItem->Add($oTemplateItemData,$oTemplateItemData->unTemplateItemData);
			}
		$stmt2->close();
		$mysqli2->close();
		}	
		
		for($c=1;$c<=$colMainProductItem->Count() - 1;$c++)
    	{			
			$clsMainPI = $colMainProductItem->GetByIndex($c);

			for($d=1;$d<=$colSideProductItem->Count()-1;$d++)
			{				
				$clsSidePI = $colSideProductItem->GetByIndex($d);
				if($clsMainPI->unProductItem == $clsSidePI->unProductItem)
				{
					$clsSidePI->CheckBool = true;
					$colSideProductItem->PIPrice = $clsMainPI->TIDPrice;
					$colSideProductItem->PICost = $clsMainPI->TIDCost;
					$colSideProductItem->PIPriority = $clsMainPI->TIDPriority;
					$colSideProductItem->unTemplateItemData = $clsMainPI->unTemplateItemData;
					$colSideProductItem->unProductType = $clsMainPI->unProductType;
					$colSideProductItem->PTName = $clsMainPI->PTName;	
					$colSideProductItem->unProductGroup = $clsMainPI->unProductGroup;	
					$colSideProductItem->PGName = $clsMainPI->PGName;								
				}
			}
		}
		
		$_SESSION['colMainProductItem'] = $colMainProductItem;
		$_SESSION['colSideProductItem'] = $colSideProductItem;
		$_SESSION['colSideItemsAfterTransfer'] = $colSideProductItem;
	
$OldPGName='';
?>

<script>

$(document).ready(function(e) {
	$('#txtSearch').keyup(function(e) {
		if(e.keyCode==13){
			$(this).focus();
			return false;
		}else{
			filterstring(this.value);
		}
	});
	$('#txtSearch').focusout(function(e) {
	 	/*$('.rows').css('height','24px');
       	$('.listviewitem').fadeIn();*/
    });
});

function filterstring(string){
	$('.listviewitem').show();
	$('.rows').css('height','24px');
	var numrows = $('#numrows').val()+1;
	for (var i = 0; i < numrows ; i++) {
		var pitem = $('#spitemname-'+[i]).text();
		pitem = pitem.replace(/ /g, '');
		if(pitem.toLowerCase().indexOf(string) === -1){
			$('#lvitem-'+[i]).hide();
			$('#rowproducttemplate-'+[i]).css('height','0px');
		}
	}
	if (string==''){
		$('.listviewitem').show();
		$('.rows').css('height','24px');
	}
}


	function msg(targ,selObj)
	{ 
		var rep;
		var url = "<?php echo $_SERVER['REQUEST_URI']; ?>";
		url = url.split('?')[1];
		var type = url.replace('type='+<?php echo $_GET['type']; ?>,'type='+selObj.options[selObj.selectedIndex].value);
		eval(targ+".location='ptemplate.php?"+type+"'");
	}


	var callme = setTimeout(function(){updateitemlist(<?php echo ($colMainProductItem->Count()-1); ?>);}, 1000)
	//var intervalFunction = setInterval(function(){updateitemlist(<?php echo ($colMainProductItem->Count()-1); ?>);}, 5000);
		$(window).scroll(function() {
			columnheader('colproducttemplate', 'lvproducttemplate');
		});
	
	function chkitemtoggleall()
	{	
		for (var a = 1; a <= <?php echo $colSideProductItem->Count() - 1; ?>; a++)
		{		
			if (document.getElementById('chkParent').getAttribute('checked')=='Checked')
			{
				document.getElementById('chkitem' + a).removeAttribute('checked');
				document.getElementById('chkitem' + a).checked = false;
			}
			else
			{
				document.getElementById('chkitem' + a).setAttribute('checked','Checked');	
				document.getElementById('chkitem' + a).checked = true;		
			}
		}
	
		if (document.getElementById('chkParent').getAttribute('checked')=='Checked')
		{
			document.getElementById('chkParent').removeAttribute('checked');
			document.getElementById('chkitem' + a).checked = false;
		}
		else
		{
			document.getElementById('chkParent').setAttribute('checked', 'Checked');
			document.getElementById('chkitem' + a).checked = true;
		}
	}
	
	function chkitemtoggle(id)
	{
		if(document.getElementById('chkitem' + id).getAttribute('checked')=='Checked')
		{
			document.getElementById('chkitem' + id).removeAttribute('checked');
			document.getElementById('chkitem' + id).checked = false;
		}
		else
		{
			document.getElementById('chkitem' + id).setAttribute('checked','Checked');
			document.getElementById('chkitem' + id).checked = true;
		}
	}
	
	function isNumberKey(evt, choice)
	{
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (choice == 0)
		{
			if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
			return false;
		}
		else
		{
			if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
		}
		return true;
	}
	
	function lostfocus(element, number)
	{
		var e = document.getElementById(element);
		if (e.value == "" || e.value == '.')
		{
			e.value	 = '0.00';
			haspricecosterrors = 1;
			document.getElementById(element).focus();
		}	
		else
		{
			if (e.value.split(".").length - 1> 1)
			{
				e.style.backgroundColor = "rgb(255, 150, 145)";
				document.getElementById(element).focus();
			}
			else
			{				
				e.style.backgroundColor = "transparent";
				onBlurSaveItem(number);
				saveindividualitem(number);		
				location.href='producttemplate.php?&id=<?php echo $_GET['id']; ?>';	
			}
		}
	}
	
	function checkpriority(name, number, pTypeMain, pGroupMain, clicked, group)
	{
		var e = document.getElementById(name + number);
		var pTypeSub;
		var haserror = 0;
		var eCount = $('.listviewitem.maintitem').length;
		
		for (var i = 1; i <= eCount; i++)
		{
			pTypeSub = document.getElementById("mhpitemtype-" + i).value;
			pGroupSub = document.getElementById("mhpitemgroup-" + i).value;
			if (i != number)
			{ 	
				var e2 = document.getElementById(name + i);

				if (e.value == e2.value && pTypeMain == pTypeSub && pGroupMain == pGroupSub)
				{		
					haserror = 1;
					e.style.backgroundColor = "rgb(255, 150, 145)";		
					//e.focus();					
				}
				else
				{
					if(haserror > 0)
					{
						haserror = 1;
					}
					else
					{
						haserror = 0;
					}
				}
			}
		}
		
		if (haserror == 0)
		{
			e.style.backgroundColor = "transparent";
			onBlurSaveItem(number);
			saveindividualitem(number);
			if (clicked==1)
			{
				location.href='producttemplate.php?&id=<?php echo $_GET['id']; ?>';
			}
		}
	}
	
	function saveeachitems()
	{
		var itemtotalcount = document.getElementById('itemtotalcount').value;
		var errorcount = 0;
		var pTypeSub;
		var pTypeMain;	
		
		for (var a = 1; a <= itemtotalcount; a++)
		{			
			var e = document.getElementById('textpriority-' + a);			
			var haserror = 0;
			pTypeMain = document.getElementById("mhpitemtype-" + a).value;
			pGroupMain = document.getElementById("mhpitemgroup-" + a).value;
			for (var i = 1; i <= itemtotalcount; i++)
			{
			pTypeSub = document.getElementById("mhpitemtype-" + i).value;
			pGroupSub = document.getElementById("mhpitemgroup-" + i).value;
				if (i != a)
				{
					var e2 = document.getElementById('textpriority-' + i);
					if (e.value == e2.value && pTypeMain == pTypeSub && pGroupMain == pGroupSub)
					{		
						haserror = 1;
						e.style.backgroundColor = "rgb(255, 150, 145)";
						e.focus();
						errorcount++;
					}
					else
					{
						if(haserror > 0)
						{
							haserror = 1;
						}
						else
						{
							haserror = 0;
						}
					}
				}
			}
			
			if (haserror == 0)
			{
				e.style.backgroundColor = "transparent";
			}
		}
		
		if (errorcount == 0)
		{
			$('.progress-value').html('Saving 0 out of ' + itemtotalcount + ' items');
			
			for (var a = 1; a <= itemtotalcount; a++)
			{
				var idTemplateItemData = document.getElementById('mhpitemtemplate-' + a).value;
				var idProductItem = document.getElementById('mhpitem-' + a).value;
				var price = document.getElementById('textprice-' + a).value;
				var cost = document.getElementById('textcost-' + a).value;
				var priority = document.getElementById('textpriority-' + a).value;
				var choice = "producttemplatesave";
				$.post('ajax/ajax.php',
					{postidTemplateItemData:idTemplateItemData, 
				 	 postidProductItem:idProductItem, 
					 postPrice:price, 
					 postCost:cost, 
					 postPriority:priority,
			 		 qid:choice},
					function(data){
						
					});
			}
			
			 setTimeout(function(){msgbox('Template Save!', '', '');}, 1500);
		}
		else
		{
			msgbox('Input errors found!', '', '');
		}
	}
	
	function saveindividualitem(number)
	{
		var idpit = document.getElementById('mhpitemtemplate-' + number).value,
			idpi = document.getElementById('mhpitem-' + number).value,
			price = isEmpty(document.getElementById('textprice-' + number).value),
			cost = isEmpty(document.getElementById('textcost-' + number).value),
			priority = isEmpty(document.getElementById('textpriority-' + number).value);
			choice = "producttemplatesave";
			
		$.post('ajax/ajax.php',
			{postidTemplateItemData:idpit, 
			 postidProductItem:idpi, 
			 postPrice:price, 
			 postCost:cost, 
			 postPriority:priority,
			 qid:choice},
			function(data){
				//alert("data: " + data);
			});
	}
	
	function isEmpty(value)
	{
		if (value)
		{
			return value;
		}
		else
		{
			return	0;
		}
	}
	
	function updateitemlist(number)
	{
		for (var i = 1; i<=number; i++)
		{
			onBlurSaveItem(i)
		}
	}
	
	function onBlurSaveItem(number)
	{
		var itemtotalcount = document.getElementById('itemtotalcount').value;
		var idTemplateItemData = document.getElementById('mhpitemtemplate-' + number).value;
		var idTemplateItemControl = <?php echo $_GET['id']; ?>;
		var idProductItem = document.getElementById('mhpitem-' + number).value;
		var price = document.getElementById('textprice-' + number).value;
		var cost = document.getElementById('textcost-' + number).value;
		var priority = document.getElementById('textpriority-' + number).value;
		var colIndex = number;
		$.post('include/savecollection.php',
			{postidTemplateItemData:idTemplateItemData,  
			postidProductItem:idProductItem, 
			postPrice:price, 
			postCost:cost, 
			postPriority:priority,
			postcolIndex:colIndex},
			function(data){
				
			});
	}
	
function savefieldvalue(field,id,idTID){
	var xmlhttp;
	if (document.getElementById(id).value==''){
		document.getElementById(id).value=0;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');

	xmlhttp.send('qid=savefieldvalue&idTID='+idTID+'&field='+field+'&value='+document.getElementById(id).value);			

}	

</script>

<input type="hidden" id="newcolumnexist" value="0">

<div id="transitem" class="popup">
	<div class="popupcontainer" style="width:500px;">
    	<form name="form1" action="ptemplate.php?id=<?php echo $_GET['id']; ?>&type=<?php echo $_GET['type']; ?>" method="post">
    		<div style="margin-bottom:20px;width:500px;">
      			<div class="listviewcolumn" style="width:500px;">
        			<div class="productitemheader" style="width:500px;"><b>PRODUCT NAME</b>
                    	<div style="color:#333; padding-bottom:10px; float:right">
          				<input type="checkbox" id="chkParent" class="chk_boxes" name="chkParent" onclick="chkitemtoggleall()">
                     	Select All
                    	</div>
                    </div>       
      			</div>
				<input autocomplete="off" type="text" id="txtSearch" placeholder="Enter to search item" onKeyPress="return disableEnterKey(event)" value="" style="width:inherit;">
                <input type="hidden" value="<?php echo $numrows; ?>" id="numrows">
      			<div class="listview" style="height:300px; overflow:scroll;">
      				<?php 
						$row=0;
						for($a=1;$a<=$colSideProductItem->Count() - 1;$a++)
						{ 
						$clsSidePI = $colSideProductItem->GetByIndex($a);
					?>
        					<div class="rows" id="rowproducttemplate-<?php echo $a; ?>" style="height:24px;">
        					<div class="listviewitem" style="background-color:#<?php echo ($row%2)?'FFF':'EEE'; ?>;width:500px;" id="lvitem-<?php echo $a; ?>" onClick="chkitemtoggle(<?php echo $a; ?>)">          
                				<input type="hidden" id="shpitem-<?php echo $a; ?>" value="<?php echo $clsSidePI->unProductItem; ?>">
            	
                			<div class="listviewsubitem">
                    				<label name="spitemname-<?php echo $a; ?>" id="spitemname-<?php echo $a; ?>" class="inputbox"><?php if ($clsSidePI->unProductType == 2) {  echo "(Rawmats) " . $clsSidePI->PIName; } else { echo $clsSidePI->PIName; };  ?></label>
                    		</div>
                        	<div style="display:inline;position:inherit;float:right;width:20px;margin-right:30px;" align="center">
                            	<input class="chk_boxes1" name="chkitem<?php echo $a; ?>" type="checkbox" id="chkitem<?php echo $a; ?>" value="<?php echo $clsSidePI->unProductItem; ?>" <?php echo ($clsSidePI->CheckBool)?'checked=Checked':''; ?>>
                        	</div>                    
            	</div>
            </div>
        <?php
			$row++;
			}
		?> 
      </div>  
      
    </div>
    
    	<input name="labeltransferitem" id="btnTransItem" type="submit" value="Add" title="Add" class="buttons">
        <input name="btnCancel" type="button" value="Close" title="Close" onClick="location.href='#close'" class="buttons" >
  </form>
 
    </div>
</div>

<form action="producttemplate.php?id=<?php echo $_GET['id']; ?>" method="post">
    <div id="toolbar">
          <input type="button" class="toolbarbutton" title="Product Items" name="btntrans" onclick="location.href='#transitem'" style="background-image:url(img/icon/productitem.png);background-repeat:no-repeat;background-position:center;display:inline;" >
          <input type="button" class="toolbarbutton" title="Save" name="btnSave" onClick="location.reload()" style="background-image:url(img/icon/save.png);" value="" style="">
          <form action="productitem.php" method="post" style="display:block;">
            <select name="producttype" onChange="msg('parent', this)" style="margin-left:1100px;;" >
                <?php
                    $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt = $mysqli->stmt_init();
                        if($stmt->prepare("Select unProductType, PTName From producttype Where Status=1"))
                        {
                            $stmt->execute();
                            $stmt->bind_result($unProductType, $PTName); 
                            while($stmt->fetch())
                            {
                ?>
                                <option value="<?php echo $unProductType; ?>"
                                <?php 
                                    $type = (isset($_GET['type']))?$_GET['type']:'';
                                    echo ($type==$unProductType)?'Selected':'';
                                ?>> <?php echo $PTName; ?> </option>
                <?php
                            }
                            $stmt->close();
                        }
                ?>
            </select>
        </form>
    	  <span id="spansaving" style="color:#333;"> </span>
    </div>
    
	<div class="listview" id="lvproducttemplate">
      <div class="column" id="colproducttemplate" align="center">
        <div class="columnheader" style="width:318px;">Product Name</div>
        <div class="columnheader" style="width:200px;text-align:right;">Cost</div>
        <div class="columnheader" style="width:200px;text-align:right;">Price</div>
        <div class="columnheader" style="width:200px;text-align:right;">Priority</div>
        <div class="columnheader" style="width:200px;text-align:right;">Action</div>
        
      </div>
     
	 <div class="row" id="rowproducttemplate">
      <?php
            for($c=1;$c<=$colMainProductItem->Count() - 1;$c++)
            {
                $clsMainPI = $colMainProductItem->GetByIndex($c);
	
	if ($OldPGName!=$clsMainPI->PGName){
        $OldPGName=$clsMainPI->PGName;
	?>
    <div class="group" id="<?php echo str_replace(' ','', $clsMainPI->PGName); ?>"><?php echo $clsMainPI->PGName; ?>
    </div>
    <?php
	}
	
        ?>
            <div class="listviewitem maintitem" style="background-color:#<?php echo ($c%2)?'EEE':'FFF'; ?>;" id="lvitem-<?php echo $c; ?>">
              	
				<input type="hidden" id="mhpitem-<?php echo $c; ?>" value="<?php echo $clsMainPI->unProductItem;?>">				
                <input type="hidden" id="mhpitemtemplate-<?php echo $c; ?>" value="<?php echo $clsMainPI->unTemplateItemData; ?>">
                <input type="hidden" id="mhpitemtype-<?php echo $c; ?>" value="<?php echo $clsMainPI->unProductType; ?>">
                <input type="hidden" id="mhpitemgroup-<?php echo $c; ?>" value="<?php echo $clsMainPI->unProductGroup; ?>">
                
                    <?php $mysqli3 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                          $stmt3 = $mysqli3->stmt_init();
                          if ($stmt3 = $mysqli3->prepare("SELECT PIName FROM productitem WHERE unProductItem=?"))
                          {
                              $stmt3->bind_param("i", $clsMainPI->unProductItem);
                              $stmt3->execute();
                              $stmt3->bind_result($PIName);
                              while ($stmt3->fetch())
                              {
                    ?>
								  <div class="listviewsubitem">
                                  <label id="mpitemname-<?php echo $c; ?>" class="producttemplatesubitem" style="text-align:left;"><?php echo $PIName; ?></label>	
								  </div>
                    <?php
                              }
                          }
                    ?>  
				
                <div class="listviewsubitem">
                <!--<input type="text" value="<?php echo $clsMainPI->TIDCost; ?>" onKeyPress="return isNumberKey(event, 0)" onBlur="lostfocus('textcost-<?php echo $c; ?>',<?php echo $c."'"; ?>)" id="textcost-<?php echo $c; ?>" class="producttemplatesubitem">-->
                <input type="text" value="<?php echo $clsMainPI->TIDCost; ?>" onKeyPress="return isNumberKey(event, 0)" onBlur="savefieldvalue('TIDCost','textcost-<?php echo $c;?>','<?php echo $clsMainPI->unTemplateItemData; ?>')" id="textcost-<?php echo $c; ?>" class="producttemplatesubitem" style="width:200px;text-align:right;">

				</div>
                
				<div class="listviewsubitem">
                <!--<input type="text" value="<?php echo $clsMainPI->TIDPrice; ?>" onKeyPress="return isNumberKey(event, 0)" onBlur="lostfocus('textprice-<?php echo $c; ?>',<?php echo $c."'"; ?>)" id="textprice-<?php echo $c; ?>" class="producttemplatesubitem">-->
                <input type="text" value="<?php echo $clsMainPI->TIDPrice; ?>" onKeyPress="return isNumberKey(event, 0)" onBlur="savefieldvalue('TIDPrice','textprice-<?php echo $c;?>','<?php /*echo '2';*/ echo $clsMainPI->unTemplateItemData; ?>')" id="textprice-<?php echo $c; ?>" class="producttemplatesubitem" style="width:200px;text-align:right;">
				</div>							
				
				<div class="listviewsubitem">
                <!--<input type="text" value="<?php echo $clsMainPI->TIDPriority; ?>" onKeyPress="return isNumberKey(event, 1)" onBlur="checkpriority('textpriority-', <?php echo $c.','.$clsMainPI->unProductType.','.$clsMainPI->unProductGroup.',1,'.$clsMainPI->PGName;?>)" id="textpriority-<?php echo $c;?>" class="producttemplatesubitem">-->
				<input type="text" value="<?php echo $clsMainPI->TIDPriority; ?>" onKeyPress="return isNumberKey(event, 1)" onBlur="savefieldvalue('TIDPriority','textpriority-<?php echo $c;?>','<?php echo $clsMainPI->unTemplateItemData; ?>')" id="textpriority-<?php echo $c;?>" class="producttemplatesubitem" style="width:200px;text-align:right;">
				</div>
                
                <div class="listviewsubitem" align="right"  style="width:200px;">
                	<div title="Delete [ <?php echo $PIName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $PIName;?></strong> ], Are you sure?','ptemplate.php?&id=' + <?php echo $_GET['id']; ?> + '&type=' + <?php echo $_GET['type']; ?> + '&TID=' + <?php echo $clsMainPI->unTemplateItemData?> +'&del=1','')" style="background-image:url(img/icon/delete.png); margin-left:180px; width:20px;"></div>
                </div>
            </div>
      <?php
            }		
        ?>
       </div>
       <div class="listviewitem maintitem">
       </div>
	   
       <input type="hidden" name="itemtotalcount" id="itemtotalcount" value="<?php echo $colMainProductItem->Count()-1; ?>">
</form>

<?php include 'footer.php'; ?>
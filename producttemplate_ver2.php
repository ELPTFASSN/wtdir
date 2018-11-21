<?php include 'header.php';
//
//
//
//
//
// To be deleted
//
//
//
//
//
//
?>
<!-- Main Content -->

<link rel="stylesheet" type="text/css" href="css/producttemplate.css">
<link rel="stylesheet" type="text/css" href="css/listview.css">

<?php 
	$clsSidePI;
	$clsMainPI;
	$_SESSION['isUpdated']= 0;
	$mysqli = new MySQLi($server,$username,$password,$database);
		$stmt = $mysqli->stmt_init();
		if($stmt = $mysqli->prepare("Select unProductItem,PIName From productitem Where `Status`=1")){
			$stmt->execute();
			$stmt->bind_result($unProductItem,$PIName);
			while($stmt->fetch())
			{
				$oProductItem = new ProductItemTemp($unProductItem, '' , '', '', $PIName, '' , '', '', '', '', false);
				$colSideProductItemPageLoad->Add($oProductItem,$oProductItem->unProductItem);
			}
		$stmt->close();
		$mysqli->close();
		}
	$_SESSION['colSideProductItemPageLoad'] = $colSideProductItemPageLoad;
	
	function saveproducttemplate($unProductTemplate, $unProductItem, $PITPrice, $PITCost, $PITPriority, $server, $username, $password, $database)
	{
		$mysqli = new MySQLi($server ,$username, $password, $database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare("INSERT INTO productitemtemplate(unProductTemplate, unProductItem, PITPrice, PITCost, PITPriority) VALUES (?,?,?,?,?)"))
		{
			$stmt->bind_param("iiddi", $unProductTemplate, $unProductItem, $PITPrice, $PITCost, $PITPriority);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}

	function updateproducttemplate($unProductItemTemplate, $unProductTemplate, $unProductItem, $PITPrice, $PITCost, $PITPriority, $server, $username, $password, $database)
	{
		$mysqli = new MySQLi($server, $username, $password, $database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('UPDATE productitemtemplate SET unProductTemplate=?, unProductItem=?, PITPrice=?, PITCost=?, PITPriority=? WHERE unProductItemTemplate=?'))
		{
			$stmt->bind_param("iiddii", $unProductTemplate, $unProductItem, $PITPrice, $PITCost, $PITPriority, $unProductItemTemplate);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}
	
	function deleteproducttemplate($unProductItemTemplate, $server, $username, $password, $database)
	{
		$mysqli = new MySQLi($server, $username, $password, $database);
		$stmt = $mysqli->stmt_init();
		if($stmt->prepare('UPDATE productitemtemplate SET Status=0 WHERE unProductItemTemplate=?'))
		{			
			$stmt->bind_param("i", $unProductItemTemplate);
			$stmt->execute();
			$stmt->close();
		}
		$mysqli->close();
	}
	
	//if(isset($_POST['btnSave']))
//	{		
//		$idTemplate = $_GET['id'];
//		$colMain = $_SESSION['colMainProductItem'];
//		for($i=1;$i<= $colMain->Count()-1;$i++)
//		{	
//			$clsMainPIsave = $colMain->GetByIndex($i);
//			
//			$idItem = $clsMainPIsave->idProductItem;
//			$price = $clsMainPIsave->PITPrice;
//			$cost = $clsMainPIsave->PITCost;
//			$priority = $clsMainPIsave->PITPriority;
//			$idProductItemTemplate = $clsMainPIsave->idProductItemTemplate;
//
//			//echo "-------------------------------";
////			echo "-".$idItem.":idItem<<<,,";
////			echo "-".$price.":price<<<,,";
////			echo "-".$cost.":cost<<<,,";
////			echo "-".$price.":price<<<,,";
////			echo "-".$idProductItemTemplate.":idProductItemTemplate<<<,,,,,,,,,";
////			
//			if ($idTemplate > 0)
//			{
//				updateproducttemplate($idProductItemTemplate, $idTemplate, $idItem, $price, $cost, $priority, $server, $username, $password, $database);
//				echo 'UPDATE productitemtemplate SET idProductTemplate='.$idTemplate.', idProductItem='.$idItem.', PITPrice='.$price.', PITCost='.$cost.', PITPriority='.$priority.' WHERE idProductItemTemplate='.$idProductItemTemplate;
//				echo $idProductItemTemplate."==".$idTemplate."==".$idItem."==".$price."==".$cost."==".$priority."==".$server."==".$username."==".$password."==".$database;
//				echo '------------------------------update'.$idTemplate;
//			}
//			else
//			{
//				//saveproducttemplate($idTemplate, $idItem, $price, $cost, $priority, $server, $username, $password, $database);
//				echo $idTemplate."==".$idItem."==".$price."==".$cost."==".$priority."==".$server."==".$username."==".$password."==".$database;
//				echo '------------------------------save'.$idTemplate;
//			}
//		}
//	}

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
				$idPIT = 0;
				if ($clsSidePI->unProductItemTemplate == 0 || $clsSidePI->unProductItemTemplate == '')
				{
					$idPIT = 0;
				}
				else
				{
					$idPIT = $clsSidePI->unProductItemTemplate;
				}
				$oProductItemTemplate = new ProductItemTemplate($_GET['id'], $clsSidePI->unProductItem, $clsSidePI->PIPrice, $clsSidePI->PICost, $clsSidePI->PIPriority, 1, $unPIT);
				$colMainProductItem->Add($oProductItemTemplate,$oProductItemTemplate->unProductItemTemplate);
			}
			else
			{
				deleteproducttemplate($clsSidePI->unProductItemTemplate, $server, $username, $password, $database);
			}
		}
		$_SESSION['colMainProductItem'] = $colMainProductItem;
	}
	else
	{
		$colSideProductItem = $_SESSION['colSideProductItemPageLoad'];
	
		$mysqli2 = new MySQLi($server,$username,$password,$database);
		$stmt2 = $mysqli2->stmt_init();
		if($stmt2 = $mysqli2->prepare("SELECT unProductItemTemplate, unProductTemplate, productitemtemplate.unProductItem,
							   PITPrice, PITCost, PITPriority, productitemtemplate.Status
							   FROM productitemtemplate
							   INNER JOIN productitem
							   ON productitem.unProductItem = productitemtemplate.unProductItem
							   WHERE productitemtemplate.`Status`=1 AND unProductTemplate=?")){
			$stmt2->bind_param("i",$_GET['id']);
			$stmt2->execute();
			$stmt2->bind_result($unProductItemTemplate, $unProductTemplate, $unProductItem, $PITPrice, $PITCost, $PITPriority, $Status);
			while($stmt2->fetch())
			{			
				$oProductItemTemplate = new ProductItemTemplate($unProductTemplate, $unProductItem, $PITPrice, $PITCost, $PITPriority, $Status, $unProductItemTemplate);
				$colMainProductItem->Add($oProductItemTemplate,$oProductItemTemplate->unProductItemTemplate);
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
					$colSideProductItem->PIPrice = $clsMainPI->PITPrice;
					$colSideProductItem->PICost = $clsMainPI->PITCost;
					$colSideProductItem->PIPriority = $clsMainPI->PITPriority;
					$colSideProductItem->unProductItemTemplate = $clsMainPI->unProductItemTemplate;
				}
			}
		}
		
		$_SESSION['colMainProductItem'] = $colMainProductItem;
		$_SESSION['colSideProductItem'] = $colSideProductItem;
		$_SESSION['colSideItemsAfterTransfer'] = $colSideProductItem;
	}

?>

<script>
	//function myFunction{
	//setInterval(function(){ alert('hello')}, 3000);} // set interval --------------------------------waras
	var intervalFunction = setInterval(function(){updateitemlist(<?php echo ($colMainProductItem->Count()-1); ?>); alert('<?php echo ($colMainProductItem->Count()-1); ?>')}, 5000);
	
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
			}
		}
	}
	
	function checkpriority(name, number)
	{
		var e = document.getElementById(name + number);
		var haserror = 0;
		var eCount = $('.listviewitem.maintitem').length ;
		for (var i = 1; i <= eCount; i++)
		{
			if (i != number)
			{
				var e2 = document.getElementById(name + i);
				if (e.value == e2.value)
				{		
					haserror = 1;
					e.style.backgroundColor = "rgb(255, 150, 145)";
					e.focus();
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
		}
	}
	
	function saveeachitems()
	{
		var itemtotalcount = document.getElementById('itemtotalcount').value;
		var errorcount = 0;
		for (var a = 1; a <= itemtotalcount; a++)
		{			
			var e = document.getElementById('textpriority-' + a);
			
			var haserror = 0;
			
			for (var i = 1; i <= itemtotalcount; i++)
			{
				if (i != a)
				{
					var e2 = document.getElementById('textpriority-' + i);
					if (e.value == e2.value)
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
			for (var a = 1; a <= itemtotalcount; a++)
			{
				var unProductItemTemplate = document.getElementById('mhpitemtemplate-' + a).value;
				var unProductTemplate = <?php echo $_GET['id']; ?>;
				var unProductItem = document.getElementById('mhpitem-' + a).value;
				var price = document.getElementById('textprice-' + a).value;
				var cost = document.getElementById('textcost-' + a).value;
				var priority = document.getElementById('textpriority-' + a).value;
				$.post('ajax/producttemplatesave.php',
					{postidProductItemTemplate:unProductItemTemplate, postidProductTemplate:unProductTemplate, 
					postidProductItem:unProductItem, postPrice:price, postCost:cost, postPriority:priority},
					function(data){
						//alert(data);
					});
			}
		}
		else
		{
			alert("Input errors found");
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
		var unProductItemTemplate = document.getElementById('mhpitemtemplate-' + number).value;
		var unProductTemplate = <?php echo $_GET['id']; ?>;
		var unProductItem = document.getElementById('mhpitem-' + number).value;
		var price = document.getElementById('textprice-' + number).value;
		var cost = document.getElementById('textcost-' + number).value;
		var priority = document.getElementById('textpriority-' + number).value;
		var colIndex = number;
		$.post('include/savecollection.php',
			{postidProductItemTemplate:unProductItemTemplate, postidProductTemplate:unProductTemplate, 
			postidProductItem:unProductItem, postPrice:price, postCost:cost, postPriority:priority,
			postcolIndex:colIndex},
			function(data){
				//alert(data);
			});
	}
	
	function setitemtotalcount()
	{
		var ftmcount =  $('.listviewitem.maintitem').length;
		document.getElementById('itemtotalcount').value = ftmcount;
	}

	function retrievecheckeditems()
	{
		var itemcount = <?php echo $colSideProductItem->Count() - 1; ?>;
		var unProductTemplate = <?php echo $_GET['id']; ?>;
		var totalchecked = 0;
		for (var c = 1; c <= itemcount; c++)
		{
			if (document.getElementById('chkitem' + c).getAttribute('checked')=='Checked')
			{
				totalchecked += 1;				
			}
		}
		
		var currentcount = $('.listviewitem.maintitem').length + 1;
		var mainitems = $('.listviewitem.maintitem').length; // Total items in main container/items

		for (var a = 1; a <= itemcount ; a++) // Start - Loop through checked items
		{ 
			if (document.getElementById('chkitem' + a).getAttribute('checked')=='Checked') // Start - If side item is checked
			{
				var isexisting = 0;
				for (var b = 1; b <= mainitems; b++) // Start - Loop through main items
				{
					var mainitemname;
					var sideitemname = document.getElementById('shpitem-' + a).getAttribute('value'); // Get value from checked side items
					
					if ( $("#mhpitem-" + a).length > 0) // Start - If mhproductitem element is found or existing in main item
					{
						mainitemname = document.getElementById('mhpitem-' + b).getAttribute('value'); // Get value from main items
					}
					else
					{
						mainitemname = mainitems; // Set default value
					} // End - If mhproductitem element is found or existing in main item
					
					if (sideitemname == mainitemname) // Start - If sideitem is equal to mainitem / If checked sideitemname is existing in mainitemname
					{
						isexisting = 1;
					} // End - If sideitem is equal to mainitem / If checked sideitemname is existing in mainitemname
				} // End - Loop through main items
				
				if (isexisting == 0)
				{
					var unProductItem = document.getElementById('shpitem-' + a).getAttribute('value');
					var xmlhttp;
					if(window.XMLHttpRequest)
					{
						xmlhttp=new XMLHttpRequest();
					}
										
					xmlhttp.open('POST','ajax/selecteditem.php',false);
					xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');
					xmlhttp.send('piid=' + unProductItem + '&currentcount=' + currentcount + '&ptid=' + unProductTemplate);
					transferitems(currentcount);
					document.getElementById('formproductitem-' + currentcount).innerHTML=xmlhttp.responseText;					
					currentcount += 1;
                     
				}
				else
				{
					//alert('has value');
				}
			}	// End - If side item is checked
		} // End - Loop through checked items
		
		setitemtotalcount(); // Set total count of items
	}
	
	function transferitems(currentcount)
	{
		var divItem = document.createElement('div');
		var newHTML = "<form action='' method='post' class='formproductitem' id='formproductitem-" + currentcount +"'></form>";
		divItem.className = 'listviewitem maintitem';
		divItem.style.backgroundColor = (currentcount%2? '#EEE':'#FFF');
		divItem.innerHTML = newHTML;
		document.getElementById('templatecontainer').appendChild(divItem);
	}
</script>

<!-- Side Bar -->
<div id="productitemcontainer"> <img src="img/icon/productitemtext.png" alt="PRODUCT ITEMS" />
  <div id="productitemcontainer_inner">
  <form name="form1" action="producttemplate_ver2.php?id=<?php echo $_GET['id']; ?>" method="post">
    <div style="background-color:#FFF">
      <div class="listviewcolumn" style="width:300px;">
        <div class="productitemheader">PRODUCT NAME</div>       
        <div style="position:absolute;color:#333;right:30px;">
          <input type="checkbox" id="chkParent" class="chk_boxes" name="chkParent" onclick="chkitemtoggleall()">
          Select All </div>
      </div>
      <div class="productitemsubcontainer">
        <?php
			for($a=1;$a<=$colSideProductItem->Count() - 1;$a++)
			{
				$clsSidePI = $colSideProductItem->GetByIndex($a);
		?>
        	<div class="listviewitem" style="background-color:#<?php echo ($a%2)?'FFF':'EEE'; ?>" id="itemrow<?php echo $a; ?>" onClick="chkitemtoggle(<?php echo $a; ?>)">
               <!-- <form action="" method="post" class="formproductitem" id="itemForm<?php echo $a; ?>" name="itemForm<?php echo $a; ?>">-->
                	<input type="hidden" id="shpitem-<?php echo $a; ?>" value="<?php echo $clsSidePI->unProductItem; ?>">
                    <label name="spitemname-<?php echo $a; ?>" id="spitemname-<?php echo $a; ?>" class="inputbox"><?php echo $clsSidePI->PIName; ?></label>
                    
                    <div style="display:inline;position:inherit;float:right;width:20px;margin-right:30px;" align="center">
                    	<input class="chk_boxes1" name="chkitem<?php echo $a; ?>" type="checkbox" id="chkitem<?php echo $a; ?>" value="<?php echo $clsSidePI->unProductItem; ?>" <?php echo ($clsSidePI->CheckBool)?'checked=checked':''; ?>>
                    </div>
               <!-- </form>-->
            </div>
        <?php
			}
		?>        
      </div>
    </div>
    
    	<div style="position:relative; width:109px; height:auto; background:#CCC; margin-top:5px; float:right;" onClick=""><!--retrievecheckeditems()-->
			<!--<center>	
                <label name="labeltransferitem" id="labeltransferitem">Tranfer</label>-->               
               <input type="submit" value="Transfer" name="labeltransferitem" class="buttons">
                <!-- <img src="img/icon/transfer.png" align="top" style="margin-top:1px;"> 
            </center>--> 
        </div>
  </form>
  </div>
</div>

<!-- Main Content -->
<form action="producttemplate_ver2.php?id=<?php echo $_GET['id']; ?>" method="post">
    <div id="toolbar">
          <input type="button" class="toolbarbutton" title="Save" name="btnSave" onClick="saveeachitems()" style="background-image:url(img/icon/save.png);" value="">
          <input type="button" class="toolbarbutton" title="Update Items" name="btnUpdate" onClick="updateitemlist(<?php echo ($colMainProductItem->Count()-1); ?>)" style="background-image:url(img/icon/update.png);" value="">
    </div>
    <div id="templatecontainer" align="center">
      <div class="listviewcolumn">
        <div class="producttemplateheader">Product Name</div>
        <div class="producttemplateheader">Price</div>
        <div class="producttemplateheader">Cost</div>
        <div class="producttemplateheader">Priority</div>
      </div>
     
      <?php
            for($c=1;$c<=$colMainProductItem->Count() - 1;$c++)
            {
                $clsMainPI = $colMainProductItem->GetByIndex($c);
        ?>
            <div class="listviewitem maintitem" style="background-color:#<?php echo ($c%2)?'EEE':'FFF'; ?>;" id="lvitem-<?php echo $c; ?>">
              	<input type="hidden" id="mhpitem-<?php echo $c; ?>" value="<?php echo $clsMainPI->unProductItem;?>">
                <input type="hidden" id="mhpitemtemplate-<?php echo $c; ?>" value="<?php echo $clsMainPI->idProductItemTemplate; ?>">
                    <?php $mysqli3 = new MySQLi($server, $username, $password, $database);
                          $stmt3 = $mysqli3->stmt_init();
                          if ($stmt3 = $mysqli3->prepare("SELECT PIName FROM productitem WHERE unProductItem=?"))
                          {
                              $stmt3->bind_param("i", $clsMainPI->unProductItem);
                              $stmt3->execute();
                              $stmt3->bind_result($PIName);
                              while ($stmt3->fetch())
                              {
                    ?>
                                  <label id="mpitemname-<?php echo $c; ?>" class="producttemplatesubitem" style="text-align:left;text-indent: 10px;"><?php echo $PIName; ?></label>
                    <?php
                              }
                          }
                    ?>  
                <input type="text" value="<?php echo $clsMainPI->PITPrice ?>" onKeyPress="return isNumberKey(event, 0)" onBlur="lostfocus('textprice-<?php echo $c; ?>',<?php echo $c; ?>)" id="textprice-<?php echo $c; ?>" class="producttemplatesubitem">
                <input type="text" value="<?php echo $clsMainPI->PITCost; ?>" onKeyPress="return isNumberKey(event, 0)" onBlur="lostfocus('textcost-<?php echo $c; ?>',<?php echo $c; ?>)" id="textcost-<?php echo $c; ?>" class="producttemplatesubitem">
                <input type="text" value="<?php echo $clsMainPI->PITPriority; ?>" onKeyPress="return isNumberKey(event, 1)" onBlur="checkpriority('textpriority-', <?php echo $c;?>)" id="textpriority-<?php echo $c;?>" class="producttemplatesubitem">
                &nbsp;
            </div>
      <?php
            }		
        ?>
       
       <input type="hidden" name="itemtotalcount" id="itemtotalcount" value="<?php echo $colMainProductItem->Count()-1; ?>">
</form>

<?php include 'footer.php'; ?>
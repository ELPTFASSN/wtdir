<?php include 'header.php';
if (isset($_GET['del'])){
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if ($stmt->prepare("UPDATE productgroup SET Status=0 WHERE unProductGroup=?")){
		$stmt->bind_param("i", $_GET['del']);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
}

if (isset($_POST['btngroupupdate'])){
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if ($stmt->prepare("UPDATE productgroup SET unProductType=?, unShortageType=?, PGName=?, PGPriority=? WHERE unProductGroup=?")){
		$stmt->bind_param("iisii",$_POST['idptypeupdate'], $_POST['idshortagetype'], $_POST['txtgroupupdate'], $_POST['txtpriority'], $_POST['bid']);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
}

if (isset($_POST['btngroupsave'])){
	$unProductGroup = getMax("unProductGroup","productgroup");
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if ($stmt->prepare("INSERT INTO productgroup(unProductGroup,unProductType, unShortageType, PGName, PGPriority) Values (?,?,?,?,?)")){
		$stmt->bind_param("iiisi",$unProductGroup, $_POST['unptype'], $_POST['idshortagetype'], $_POST['txtgroup'], $_POST['txtpriority']);
		$stmt->execute();
		$stmt->close();
	}
	$mysqli->close();
}
?>
 
 
<script>
function loadeditpopup(id, name, type, priority){
	if(id==0){
		$('#editgroup').html('');
		return;
	}
	
	$.post('ajax/ajax.php',
	{
		qid:'editproductgroup',
		bid:id,
		pgn:name,
		it:type,
		priority:priority
	},
	function(data,status){
		$('#editgroup').html(data);
		location.href='#edit';
	});
}

$(document).ready(function(){
	var h = $('#lvproductiondata').height()-$('#colproductiondata').height();
	$('#rowproductiondata').height(h);
	$('frmAddGroup').submit(function(e) {
		if($('txtgroup').val().length==0){
			return false;
		}
	});
});

$(document).scroll(function(){
	columnheader('colproductioncontainer','productcontainer');
});
</script>
 
<link rel="stylesheet" type="text/css" href="css/listview.css">

<div id="toolbar">
	<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#addgroup'" style="background-image:url(img/icon/productgroup.png);background-repeat:no-repeat;background-position:center;display:inline;" >
</div>

<div class="listview" id="productcontainer">
    <div class="column" id="colproductioncontainer">
        <div class="columnheader" style="width:200px;">Item Group Name</div>
        <div class="columnheader" style="width:200px;">Item Type</div>
        <div class="columnheader" style="width:200px;">Shortage Type</div>
        <div class="columnheader" style="width:200px;">Priority</div>
        <div class="columnheader" style="width:200px"><a href="#bottom" style="color:#333">Action</a></div>
    </div>
    
    <div class="row" id="rowproductioncontainer">
            
    <?php 
        $i = 0;
        $mysqli1 = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
        $stmt1=$mysqli1->stmt_init();
            if($stmt1->prepare("Select unProductGroup, productgroup.unProductType, producttype.PTName, PGName, STName, PGPriority 
                                From productgroup 
                                Inner Join producttype ON productgroup.unProductType = producttype.unProductType 
                                Left Join shortagetype on productgroup.unShortageType = shortagetype.unShortageType 
                                Where productgroup.Status=1 
                                Order by PGPriority")){
                $stmt1->execute();
                $stmt1->bind_result($unProductGroup,$unProductType,$PTName,$PGName,$STName,$PGPriority);
                while($stmt1->fetch()){
    ?>
            
            <div class="listviewitem" id="lvitem-<?php echo $i; ?>" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>";>
            <form action="productgroup.php?&type=<?php echo $_GET['type']; ?>" method="post">
                <input type="hidden" name="hidden" id="hdnPGroup-<?php echo $i; ?>" value="<?php echo $unProductGroup; ?>">
                <input type="hidden" name="hidden" id="hdnPType-<?php echo $i; ?>" value="<?php echo $unProductType; ?>">
                <input type="hidden" name="hidden" id="hdnPPriority-<?php echo $i; ?>" value="<?php echo $PGPriority; ?>">
                <label name="txtgroup-<?php $i; ?>" id="txtgroup-<?php echo $i; ?>" style="text-align:left;width:200px;" class="listviewsubitem"> <?php echo $PGName; ?> </label>
                <label name="txttype-<?php echo $i; ?>" id="txttype-<?php echo $i; ?>" style="width:200px;" class="listviewsubitem"> <?php echo $PTName; ?> </label>
                <label name="txtshortagetype-<?php echo $i; ?>" id="txtshortagetype-<?php echo $i; ?>" style="width:200px;" class="listviewsubitem"> <?php echo $STName; ?> </label>
                <label name="txtpriority-<?php echo $i; ?>" id="txtpriority-<?php echo $i; ?>" style="width:200px;" class="listviewsubitem"> <?php echo $PGPriority; ?> </label>
                
                <div class="listviewsubitem" align="center"  style="width:200px;">
                    <input title="Edit [ <?php echo $PGName;?> ]" name="btnedit" style=" background-image:url(img/icon/update.png);background-position: 0px 0px;width:20px; height:20px;border:none;background-color: transparent;" value="" class="button16" onclick="loadeditpopup(<?php echo $unProductGroup.",'".$PGName."',".$unProductType.",".$PGPriority; ?>)">
                    <div title="Delete [ <?php echo $PGName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $PGName;?></strong> ], Are you sure?','productgroup.php?&type=<?php echo $_GET['type']; ?>&del=<?php echo $unProductGroup; ?>','')" style="background-image:url(img/icon/delete.png);"></div>
                </div>

            </form>
            </div>
                            
      <?php
                    $i++;
                }
                $stmt1->close();
            }
      ?>	
</div>

<div id="addgroup" class="popup">
	<div id="add" class="popupcontainer">
        <div id="popme" class="popuptitle" align="center">Add Product Group</div>
        <form id="frmAddGroup" method="post" action="productgroup.php?&type=<?php echo $_GET['type']; ?>" >
            <div class="popupitem">
                <div class="popupitemlabel">Group</div>
                <input name="txtgroup" type="text" style="width:195px;" required value="">
            </div>
            
            <div class="popupitem">
                <div class="popupitemlabel">Type</div>
                <select name="unptype" id="unptype" style="width:200px;">
                <?php 
                    $mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                    $stmt = $mysqli->stmt_init();
                        if($stmt->prepare("Select unProductType, PTName From producttype Where Status=1")){
                            $stmt->execute();
                            $stmt->bind_result($unProductType,$PTName);
                            while($stmt->fetch()){
                ?>
                            <option value="<?php echo $unProductType; ?>"> <?php echo $PTName; ?></option>
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
                            $stmt->bind_result($unShortageType,$STName);
                            while($stmt->fetch()){
                ?>
                            <option value="<?php echo $unShortageType; ?>"> <?php echo $STName; ?></option>
                <?php
                            }
                            $stmt->close();
                        }
                ?>
                </select>
            </div>

            <div class="popupitem">
                <div class="popupitemlabel">Priority</div>
                <input name="txtpriority" type="text" style="width:195px;" required value="0">
            </div>

            <div align="right">
                <input name="btngroupsave" type="submit" value="Save" title="Save" onClick="" class="buttons" >
                <input name="btngroupcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#'" class="buttons" >
            </div>
        </form>
    </div>
</div>

<div id="edit" class="popup">
	<div id="editgroup" class="popupcontainer" style="width:300px;"></div>
</div>

<?php include 'footer.php'; ?>
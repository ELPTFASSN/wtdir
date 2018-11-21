<?php include 'header.php';
//include 'include/function.inc.php';

// ----- insert templateproductionbatch
if(isset($_POST['btnproducttemplatesave']))
	{
		$radButton = $_POST['radTemplate'];
		$PTName = $_POST['tname'];

		if ($radButton=='Empty')
		{
				// Check if name has duplicate
			$isNameExist = ExecuteReader("Select count(TICName) as `result` From templateitemcontrol where `Status`=1 and TICName='".$PTName."'");
			if($isNameExist==0)
			{ 
			// Save product template
			$unProductTemplateNew=getMax('unTemplateItemControl','templateitemcontrol');
			//die($unProductTemplateNew);
			ExecuteNonQuery("Insert into templateitemcontrol(TICName,unTemplateItemControl,unArea) values ('".$PTName."',".$unProductTemplateNew.",".$_SESSION['area'].")");
			// Redirect to producttemplate.php page
			header('location:producttemplate.php?id='.$unProductTemplateNew);
			}
		}
		else
		{
			$unProductTemplateOld = $_POST['idSaveTemplate'];
				// Check if name has duplicate
			$isNameExist = ExecuteReader("Select count(TICName) as `result` From templateitemcontrol where `Status`=1 and TICName='".$PTName."'");
			if($isNameExist==0)
			{
				//die($unProductTemplateOld.$PTName);
				// Save product template
				//ExecuteNonQuery("Insert into templateitemcontrol(TICName) values (".$PTName.")");
				//$idProductTemplateNew = ExecuteReader("Select max(idTemplateItemControl) as `result` From templateitemcontrol Where `Status`=1");
				// Copy from selected item to new template
				$mysqli = new MySQLi ($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
				$stmt = $mysqli->stmt_init();
				if($stmt->prepare("Call DuplicateTemplate(?,?,?)"))
				{
					$stmt->bind_param("iis", $unProductTemplateOld, $_SESSION['area'], $PTName);
					$stmt->execute();
					$stmt->close();
				}
				$mysqli->close();
				header('location:producttemplate.php?id='.$unProductTemplateNew);
				// Redirect to producttemplate.php page
			}
			else
			{
				echo 'Template name already exist.';
			}
		}				
	}

// ----- delete templateproductionbatch
if(isset($_GET['del']))
{
	$query = "Update templateitemcontrol Set Status=0 Where unTemplateItemControl=".$_GET['del'];			
	ExecuteNonQuery($query);
}

// ------ edit templateproductionbatch
if(isset($_POST['btntemplateedit']))
{
	$query = "Update templateitemcontrol Set TICName='".$_POST['txttpbname']."' Where unTemplateItemControl=".$_POST['hdnidtic'];
	ExecuteNonQuery($query);
}

?>

<script>

function edittic(idtic,tname)
{
	$.post('ajax/ajax.php',
		{qid:'edittic',
		 tname:tname,
		 tic:idtic},
		 function(data)
		 {
			 location.href = '#edittemplate';			 
			 document.getElementById('editt').innerHTML = data;	 
		 }
		 );	
}

</script>

    <div id="toolbar">        
		<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#addtemplate'" style="background-image:url(img/icon/producttemplate.png);background-repeat:no-repeat;background-position:center;display:inline;">
    </div>
    
	<div class="listview" id="lvproducttemplate">
      <div class="column" id="colproducttemplate" align="center">
        <div class="columnheader" style="width:200px;">Template Name</div>
        <div class="columnheader" style="width:200px;">Action</div>
      </div>
     
	 <div class="row" id="rowproducttemplate">
		<?php
		$i=0;
			$mysqli = new MySQLi ($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("Select unTemplateItemControl, TICName 
								From templateitemcontrol 
								Where Status=1 And unArea=?")){
				$stmt->bind_param("i",$_SESSION['area']);
				$stmt->execute();
				$stmt->bind_result($unTemplateItemControl, $TICName);
			
				while($stmt->fetch())
				{
		?>
                <div class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;" id="lvitem-<?php echo $i; ?>"> 
                    <input type="hidden" name="txtidTPB-<?php echo $i;?>" id="txtidTIC-<?php echo $i;?>" value="<?php echo $idTemplateItemControl; ?>">                                       
                    <div class="listviewsubitem" style="text-align:left;width:200px;"><?php echo $TICName;?></div>                         
                    <div class="listviewsubitem" align="center"  style="width:215px;">
                        <div title="Edit [ <?php echo $TICName;?> ]" class="button16" onclick="edittic(<?php echo $unTemplateItemControl;?>,'<?php echo $TICName; ?>')" style="background-image:url(img/icon/update.png);margin:auto;"></div>
                        <div title="Delete [ <?php echo $TICName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $TICName;?></strong> ], Are you sure?','templateitemcontrol.php?&del=<?php echo $unTemplateItemControl;?>','')" style="background-image:url(img/icon/delete.png);"></div>
                    </div>
            	</div>
		<?php		
		$i++;
				}
				$stmt->close();
			}
		?>
     </div>
     
<div id="addtemplate" class="popup">
	<div id="addt" class="popupcontainer">
    	
        <div class="popuptitle" align="center">
        	<div class="popuptitle">Add new template</div>
        </div>
            
        <form action="#newtemplate" method="post">    
            <div class="popupitem">
                <div class="popupitemlabel">Template name:</div>
                <input type="text" name="tname" value="" style="width:195px;" required>
                <div>
                    
                    	<div style="padding-left: 94px;">
                            <div>
                                <input type="radio" name="radTemplate" id="radEmpty" value="Empty" checked="checked" onclick="enabledisableComboBox(0)"/>
                                <label for = "radEmpty">Empty Template</label><br />                                           
                            </div>
                            <div>
                                <input type="radio" name="radTemplate" id="radCopyFrom" value="Copy" onclick="enabledisableComboBox(1)" />
                                <label for = "radCopyFrom">Copy from </label>
                                <select name="idSaveTemplate" id="cmbCopyFrom" style="width:120px;" for = "radCopyFrom" disabled="disabled">
                                    <?php
                                        $mysqli = new MySQLi ($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                                        $stmt = $mysqli->stmt_init();
                                        if ($stmt->prepare("Select unTemplateItemControl,TICName From templateitemcontrol Where `Status` = 1 and unArea=?")){
											$stmt->bind_param("i",$_SESSION['area']);
                                            $stmt->execute();
                                            $stmt->bind_result($unTemplateItemControl, $TICName);
                                            
                                            while($stmt->fetch())
                                            {
                                    ?>
                                                <option value="<?php echo $unTemplateItemControl; ?>"> <?php echo $TICName; ?></option>
                                    <?php
                                            }
                                            $stmt->close();
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    
                </div>
                <div style="">
                
                </div>
            </div>
            
            <div id="dupli" class="duplicatepopup">
            	<div id="bla" class="duplicatecontainer">
                </div>
            </div>
            
            <div align="center">
                <input type="submit" name="btnproducttemplatesave" class="buttons" value="Save" >
                <input name="btnitemcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#'" class="buttons" >
            </div>
        </form>                
    </div>
</div>

<div id="edittemplate" class="popup">
	<div id="editt" class="popupcontainer">         
    </div>
</div>
     
<?php include 'footer.php';?>
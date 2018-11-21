<?php include 'header.php';

// ----- insert templateproductionbatch
if(isset($_POST['btnproductionsave']))
{
	$query = "Insert Into templateproductionbatch(unTemplateItemControl, TPBName,unTemplateProductionBatch,unArea) Values (".$_POST['productiontemplate'].",'".$_POST['productionname']."',".getMax('unTemplateProductionBatch','templateproductionbatch').",".$_SESSION['area'].")";
	ExecuteNonQuery($query);
}

// ----- delete templateproductionbatch
if(isset($_GET['del']))
{
	$query = "Update templateproductionbatch Set Status=0 Where unTemplateProductionBatch=".$_GET['del'];
	ExecuteNonQuery($query);
}

// ------ edit templateproductionbatch
if(isset($_POST['btnproductionedit']))
{
	$query = "Update templateproductionbatch Set unTemplateItemControl=".$_POST['cmbptname'].", TPBName='".$_POST['txttpbname']."' WHERE unTemplateProductionBatch=".$_POST['hdnidtpb'];
	ExecuteNonQuery($query);
}

?>

<script>

function edittpb(pname,idtpb,idtic,tname)
{
	$.post('ajax/ajax.php',
		{qid:'edittpb',
		 pname:pname,
		 tpb:idtpb,
		 tic:idtic},
		 function(data)
		 {
			 location.href = '#editproduction';
			 document.getElementById('editp').innerHTML = data;	 
		 }
		 );	
}

</script>

    <div id="toolbar">        
		<input type="button" class="toolbarbutton" title="New" name="btnnew" onclick="location.href='#addproduction'" style="background-image:url(img/icon/production.png);background-repeat:no-repeat;background-position:center;display:inline;">
    </div>
    
	<div class="listview" id="lvproducttemplate">
      <div class="column" id="colproducttemplate" align="center">
        <div class="columnheader" style="width:200px;">Production Name</div>
        <div class="columnheader" style="width:200px;">Template</div>
        <div class="columnheader" style="width:200px;">Action</div>
      </div>
     
	 <div class="row" id="rowproducttemplate">
		<?php
		$i=0;
			$mysqli = new MySQLi ($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
			$stmt = $mysqli->stmt_init();
			if($stmt->prepare("Select unTemplateProductionBatch, templateproductionbatch.unTemplateItemControl, TPBName, TICName 
								From templateproductionbatch
								Inner Join templateitemcontrol
								On templateitemcontrol.unTemplateItemControl = templateproductionbatch.unTemplateItemControl 
								Where templateproductionbatch.Status=1 And templateproductionbatch.unArea=?")){
				$stmt->bind_param("i",$_SESSION['area']);
				$stmt->execute();
				$stmt->bind_result($unTemplateProductionBatch, $unTemplateItemControl, $TPBName, $TICName);
			
				while($stmt->fetch())
				{
		?>
                <div class="listviewitem" style="background-color:#<?php echo ($i%2)?'EEE':'FFF'; ?>;" id="lvitem-<?php echo $i; ?>"> 
                    <input type="hidden" name="txtidTPB-<?php echo $i;?>" id="txtidTPB-<?php echo $i;?>" value="<?php echo $unTemplateProductionBatch; ?>">
                    <input type="hidden" name="txtidTIC-<?php echo $i;?>" id="txtidTIC-<?php echo $i;?>" value="<?php echo $unTemplateItemControl; ?>">
                                       
                    <div class="listviewsubitem" style="text-align:left;width:200px;"><?php echo $TPBName;?></div>
                    <div class="listviewsubitem" style="width:200px;"><?php echo $TICName;?></div>
                    
                    <div class="listviewsubitem" align="center"  style="width:215px;">
                        <div title="Edit [ <?php echo $TPBName;?> ]" class="button16" onclick="edittpb('<?php echo $TPBName;?>',<?php echo $unTemplateProductionBatch; ?>,<?php echo $unTemplateItemControl; ?>,'<?php echo $TICName; ?>')" style="background-image:url(img/icon/update.png);margin:auto;"></div>
                        <div title="Delete [ <?php echo $TPBName;?> ]" class="button16" onclick="msgbox('Delete [ <strong><?php echo $TPBName;?></strong> ], Are you sure?','templateproductionbatch.php?&del=<?php echo $unTemplateProductionBatch;?>','')" style="background-image:url(img/icon/delete.png);"></div>
                    </div>
            	</div>
		<?php		
		$i++;
				}
				$stmt->close();
			}
		?>
     </div>
     
<div id="addproduction" class="popup">
	<div id="addp" class="popupcontainer">
    	
        <div class="popuptitle" align="center">
        	<div class="popuptitle">Add new production</div>
        </div>
            
        <form action="templateproductionbatch.php" method="post">    
            <div class="popupitem">
                <div class="popupitemlabel" style="width:100px;">Production Name</div>
                <input type="text" name="productionname" value="" style="width:180px;" required>
            </div>

			<div class="popupitem">
            	<div class="popupitemlabel"  style="width:100px;">Template</div>
				<select name="productiontemplate" id="productiontemplate" style="width:180px;">
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

            <div align="center">
                <input type="submit" name="btnproductionsave" class="buttons" value="Save" >
                <input name="btnitemcancel" type="button" value="Cancel" title="Cancel" onClick="location.href='#'" class="buttons" >
            </div>
        </form>                
    </div>
</div>

<div id="editproduction" class="popup">
	<div id="editp" class="popupcontainer">                   
    </div>
</div>
     
<?php include 'footer.php';?>
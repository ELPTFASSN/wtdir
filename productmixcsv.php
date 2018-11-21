<?php
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=productmix.csv');
	
	include 'include/var.inc.php';
	include 'include/class.inc.php';
		
	session_start();
	if ($_SESSION['Session'] == '') {header("location:end.php");}
	
	$bid=(isset($_GET['bid'])=='')?'':$_GET['bid'];
	$did=(isset($_GET['did'])=='')?'':$_GET['did'];
	$type=(isset($_GET['type'])=='')?'':$_GET['type'];
	$dto=(isset($_GET['dto'])=='')?'':$_GET['dto'];
	$dfrom=(isset($_GET['dfrom'])=='')?'':$_GET['dfrom'];
	$dto = ltrim($dto, '0');
	$dfrom = ltrim($dfrom, '0');
	
	$export = $_SESSION['productmixreport'];
	
	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	
				$mysqli = New MySqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
                $stmt = $mysqli->stmt_init();
                if($stmt->prepare("Select BName from inventorycontrol Inner Join branch On inventorycontrol.unBranch=branch.unBranch Where unInventoryControl=?")){
                    $stmt->bind_param('i',$_GET['did']);
                    $stmt->execute();
                    $stmt->bind_result($BName);
                    $stmt->fetch();
                    $stmt->close();
                }
				
	
	// output the column headings
	fputcsv($output, array('PRODUCT MIX','','','',''));
	fputcsv($output, array($BName));
	if(isset($_GET['filter'])){
		$ICPeriodFrom='';
		$ICPeriodTo='';
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
       	$stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT Concat(MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate)) as `ICPeriod` FROM inventorycontrol WHERE unBranch=? AND unInventoryControl=? AND Status = 1")){
		$stmt->bind_param('ii',$bid,$dfrom);
        $stmt->execute();
        $stmt->bind_result($ICPeriod);
        while($stmt->fetch()){
			$ICPeriodFrom=$ICPeriod;
			}
        $stmt->close();
        }
		$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
       	$stmt=$mysqli->stmt_init();
		if($stmt->prepare("SELECT Concat(MonthName(`ICDate`), ' ',DayOfMonth(ICDate),', ', Year(ICDate)) as `ICPeriod` FROM inventorycontrol WHERE unBranch=? AND unInventoryControl=? AND Status = 1")){
		$stmt->bind_param('ii',$bid,$dto);
        $stmt->execute();
        $stmt->bind_result($ICPeriod);
        while($stmt->fetch()){
			$ICPeriodTo=$ICPeriod;
			}
        $stmt->close();
        }
		fputcsv($output, array($ICPeriodFrom.' TO '.$ICPeriodTo));
	}
	fputcsv($output, array('Products', 'Quantity', 'Unit Price', 'Amount', 'Percentage'));	
	
	// loop over the rows, outputting them
	foreach($export as $row => $line){
		fputcsv($output,$line);
	}

?>

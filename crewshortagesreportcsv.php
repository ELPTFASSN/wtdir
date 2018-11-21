<?php
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=crewshortagesreport.csv');
	
	include 'include/var.inc.php';
	include 'include/class.inc.php';
		
	session_start();
	if ($_SESSION['Session'] == '') {header("location:end.php");}
	
	$bid=(isset($_GET['bid'])=='')?'':$_GET['bid'];
	$did=(isset($_GET['did'])=='')?'':$_GET['did'];
	$type=(isset($_GET['type'])=='')?'':$_GET['type'];
	$dto=(isset($_GET['dto'])=='')?'':$_GET['dto'];
	$dfrom=(isset($_GET['dfrom'])=='')?'':$_GET['dfrom'];
	$dto = date_create($dto);
	$dto = date_format($dto,'F d, Y');
	$dfrom = date_create($dfrom);
	$dfrom = date_format($dfrom,'F d, Y');

	if(isset($_GET['filter'])){
		$prevdate = "'".$_GET['dfrom']."'";
		$nextdate = "'".$_GET['dto']."'";
		$prevdateS = $_GET['dfrom'];
		$nextdateS = $_GET['dto'];
		$dateset = explode('-', $prevdate);
		$curryear = $dateset[0];
		$prevMonthName = date('F',strtotime($prevdateS));
		$nextMonthName = date('F',strtotime($nextdateS));
		$prevDateName = date('d',strtotime($prevdateS));
		$nextDateName = date('d',strtotime($nextdateS));
		$prevYearName = date('Y',strtotime($prevdateS));
		$nextYearName = date('Y',strtotime($nextdateS));
		$prevdate = $prevMonthName.' '.$prevDateName.', '.$prevYearName;
		$nextdate = $nextMonthName.' '.$nextDateName.', '.$nextYearName;
	}else{
		$currdate = ExecuteReader("Select ICDate as `result` From inventorycontrol Where `unBranch`=".$_SESSION['bid']." AND `unInventoryControl`=".$_SESSION['did']);
		$lastday = date('t',strtotime($currdate));
		$dateset = explode('-', $currdate);
		$curryear = $dateset[0];
		$currmonth = $dateset[1];
		$currMonthName = date('F',strtotime($currdate));
		$prevmonth = (int)$currmonth-1;
		$nextmonth = (int)$currmonth+1;
		$prevdate = $currMonthName." 1, ".$curryear;
		$nextdate = $currMonthName." ".$lastday.", ".$curryear;
	}
	
	$export = $_SESSION['dailysalesreport'];
	
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
	fputcsv($output, array('SHORTAGE REPORT','',''));
	fputcsv($output, array($BName));
	fputcsv($output, array($prevdate.' TO '.$nextdate));
	//fputcsv($output, array('Products', 'Quantity', 'Unit Price', 'Amount', 'Percentage'));	
	
	// loop over the rows, outputting them
	foreach($export as $row => $line){
		fputcsv($output,$line);
	}

?>
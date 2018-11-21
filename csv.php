<?php
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=productmix.csv');
	
	include 'include/var.inc.php';
	include 'include/class.inc.php';
	
	session_start();
	if ($_SESSION['Session'] == '') {header("location:end.php");}
	
	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	
	// output the column headings
	fputcsv($output, array('Inventory Control', 'Unique Number', 'Status'));
	
	// fetch the data
	mysql_connect($_SESSION['localhost'],$_SESSION['username'],$_SESSION['password']);
	mysql_select_db($_SESSION['database']);
	$rows = mysql_query('SELECT idInventoryControl, unInventoryControl, unBranch FROM inventorycontrol');
	
	// loop over the rows, outputting them
	while ($row = mysql_fetch_assoc($rows)) fputcsv($output, $row);
?>
<!doctype html>
<html>
<head>
</head>
<body>
</body>
</html>
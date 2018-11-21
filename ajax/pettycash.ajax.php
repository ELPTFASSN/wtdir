<?php 
include '../include/var.inc.php';
include '../include/class.inc.php';
session_start();

switch($_POST['qid']){
case 'LoadPettyCashData':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select PCDDescription,PCDAmount From pettycashdata Where `Status` = 1 and unPettyCashControl = ? Order by PCDDescription Asc")){
		$stmt->bind_param("i",$_POST['id']);
		$stmt->execute();
		$stmt->bind_result($PCDDescription,$PCDAmount);
		while($stmt->fetch()){
			echo $PCDDescription.'©'.$PCDAmount.'®';
		}
		$stmt->close();
	}
	$mysqli->close();
	break;

case 'DisplayPettyCashData':
	$mysqli = new MySQLi($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("Select PCDDescription,PCDAmount From pettycashdata Where `Status` = 1 and unPettyCashControl = ? Order by PCDDescription Asc")){
		$stmt->bind_param("i",$_POST['id']);
		$stmt->execute();
		$stmt->bind_result($PCDDescription,$PCDAmount);
		while($stmt->fetch()){
			?>
			<div class="listviewitem">
                <div class="listviewsubitem" style="width:295px;"><?php echo $PCDDescription; ?></div>
                <div class="listviewsubitem" style="width:50px; text-align:right;"><?php echo $PCDAmount; ?></div>
            </div>
			<?php
		}
		$stmt->close();
	}
	$mysqli->close();
	break;
}
?>
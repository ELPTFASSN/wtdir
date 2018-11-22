<?php
	include 'var.inc.php';
	include 'class.inc.php';

	session_start();
	$oAccountUser=$_SESSION['oAccountUser'];
	$sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);

	if ($_SESSION['Session'] == '') {header("location:../end.php");}
	if ($_SESSION['Session'] != $sessionid) {header("location:../end.php");}


	if(isset($_POST['btnIRSave'])){
		$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		for($i=0;$i<30;$i++){
			$rb = "repbranch-{$i}";
			$rq = "repquota-{$i}";
			$hr = "hasRecord-{$i}";
			$repquota=0;
			$hasRec = 0;
			if(isset($_POST[$rb])){
				if($_POST[$rq]>0){
					$repquota=$_POST[$rq];
				};
				if($_POST[$hr] > 0){
					$stmt=$mysqli->stmt_init();
					if($stmt->prepare('UPDATE incentivescontrol SET ICQuota=? WHERE unIncentivesControl=? AND unBranch=? AND unArea=?')){
						$stmt->bind_param('diii',$repquota,$_POST[$hr],$_POST[$rb],$_POST['sessArea']);
						$stmt->execute();
					}
					$stmt->close();
					$hasRec = $_POST[$hr];
					// echo 'UPDATE incentivescontrol SET ICQuota='.$repquota.' WHERE unIncentivesControl='.$_POST[$hr].' AND unBranch='.$_POST[$rb].' AND unArea='.$_POST['sessArea'].'<br>';
				}else if($_POST[$hr] == 0){
					$maxIncCon = getMax('unIncentivesControl','incentivescontrol');

					$stmt=$mysqli->stmt_init();
					if($stmt->prepare('INSERT INTO incentivescontrol (unIncentivesControl,ICQuota,unBranch,unArea,ICMonth) values (?,?,?,?,'.$_POST['ICMonth'].')')){
						$stmt->bind_param('idii',$maxIncCon,$repquota,$_POST[$rb],$_POST['sessArea']);
						$stmt->execute();
					}
					$stmt->close();
					$hasRec = $maxIncCon;
					// echo 'INSERT INTO incentivescontrol (unIncentivesControl,ICQuota,unBranch,unArea,ICMonth) values ('.$maxIncCon.','.$repquota.','.$_POST[$rb].','.$_POST['sessArea'].','.$_POST['ICMonth'].')<br>';
				}
				// start macarse
				for($k= 0; $k<20; $k++){
					$rsci = "repsalescrewid-{$i}-{$k}";
					$rcnh = "repcrewhr-{$i}-{$k}";
					$rchours=0.00;
					if(isset($_POST[$rsci])){
							// echo 'result: ' . $_POST[$rsci] . ' == ' . $_POST[$rcnh] . '<br />';
							$stmt=$mysqli->stmt_init();
							if($stmt->prepare('UPDATE salescrew SET SCHours = ? WHERE idSalesCrew = ?')){
								$stmt->bind_param('ii', $_POST[$rcnh], $_POST[$rsci]);
								$stmt->execute();
							}
							$stmt->close();
					}
				}
				// end macarse

				for($k=0;$k<20;$k++){
					$ron = "reposnewname-{$i}-{$k}";
					$roh = "reposnewhr-{$i}-{$k}";
					$roi = "reposid-{$i}-{$k}";
					$rohours=0.00;
					// echo $k;
					$roid = getMax('idIncentivesData','incentivesdata')."";
					$maxIncDat = getMax('unIncentivesData','incentivesdata');
					if(isset($_POST[$ron])){
						if($_POST[$roh]>0){
							$rohours=$_POST[$roh];
						};
						if($_POST[$roi]>0){
							$roid=$_POST[$roi];
						};
						$stmt=$mysqli->stmt_init();
						if($stmt->prepare("INSERT INTO incentivesdata (idIncentivesData,unIncentivesControl,unIncentivesData,IDEmployee,IDHours,IDSeq,IDDes) values (".$roid.",".$hasRec.",".$maxIncDat.",'".$_POST[$ron]."',".$rohours.",".$k.",'OS') ON DUPLICATE KEY UPDATE IDEmployee='".$_POST[$ron]."', IDHours=".$rohours)){
							$stmt->execute();
						}
						$stmt->close();
					}
				}
				for($l=0;$l<5;$l++){
					$rsn = "repssname-{$i}-{$l}";
					$rsh = "repsshr-{$i}-{$l}";
					$rsi = "repssid-{$i}-{$k}";
					$rshours=0.00;
					$rsid = getMax('idIncentivesData','incentivesdata')."";
					$maxIncDat = getMax('unIncentivesData','incentivesdata');
					if(isset($_POST[$rsn])){
						if($_POST[$rsh]>0){
							$rshours=$_POST[$rsh];
						};
						if($_POST[$rsi]>0){
							$rsid=$_POST[$rsi];
						};
						$stmt=$mysqli->stmt_init();
						if($stmt->prepare("INSERT INTO incentivesdata (idIncentivesData,unIncentivesControl,unIncentivesData,IDEmployee,IDHours,IDSeq,IDDes) values (".$rsid.",".$hasRec.",".$maxIncDat.",'".$_POST[$rsn]."',".$rshours.",".$l.",'SS') ON DUPLICATE KEY UPDATE IDEmployee='".$_POST[$rsn]."', IDHours=".$rshours)){
							$stmt->execute();
						}
						$stmt->close();
					}
				}
				for($m=0;$m<5;$m++){
					$rmn = "repomname-{$i}-{$m}";
					$rmh = "repomhr-{$i}-{$m}";
					$rmi = "repomid-{$i}-{$m}";
					$rmhours=0.00;
					$rmid = getMax('idIncentivesData','incentivesdata');
					$maxIncDat = getMax('unIncentivesData','incentivesdata');
					if(isset($_POST[$rmn])){
						if($_POST[$rmh]>0){
							$rmhours=$_POST[$rmh];
						};
						if($_POST[$rmi]>0){
							$rmid=$_POST[$rmi];
						};
						$stmt=$mysqli->stmt_init();
						if($stmt->prepare("INSERT INTO incentivesdata (idIncentivesData,unIncentivesControl,unIncentivesData,IDEmployee,IDHours,IDSeq,IDDes) values (".$rmid.",".$hasRec.",".$maxIncDat.",'".$_POST[$rmn]."',".$rmhours.",".$m.",'OM') ON DUPLICATE KEY UPDATE IDEmployee='".$_POST[$rmn]."', IDHours=".$rmhours)){
							$stmt->execute();
							//die("success");
						}
						$stmt->close();
					}
					//die("INSERT INTO incentivesdata (idIncentivesData,unIncentivesControl,unIncentivesData,IDEmployee,IDHours,IDSeq,IDDes) values (".$rmid.",".$hasRec.",".$maxIncDat.",'".$_POST[$rmn]."',".$rmhours.",".$m.",'OM') ON DUPLICATE KEY UPDATE IDEmployee='".$_POST[$rmn]."', IDHours=".$rmhours);
				}
			}
		}
		//die();
		header('location:../'.$_POST['sessURL']);
	}

?>

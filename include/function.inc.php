<?php
	
function UpdateUserSession($unAccountUser,$iSession)
{
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	if($stmt->prepare('Update accountuser Set AUSession=?,TimeStamp=? Where unAccountUser=?')){
		$stmt->bind_param('ssi',$iSession,date('Y-m-d H:i:s'),$unAccountUser);
		$stmt->execute();
		$stmt->close();	
	}
}

function GetInventoryControlData($did,$bid,$Mid,$Mad)
{
	if (!empty($did)){
		$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Select concat(MonthName(ICDate) , ' ' , Day(ICDate) , ', ' ,Year(ICDate)) as `ICPeriod`,BName,ICNumber From inventorycontrol Inner Join branch on branch.unBranch=inventorycontrol.unBranch Where unInventoryControl=?")){
			$stmt->bind_param('i',$did);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($ICPeriod,$BName,$ICNumber);
			$stmt->fetch();
			$rowcount=$stmt->num_rows();
			$stmt->close();
			return ($rowcount==0)?'':$BName.' [ '.$ICPeriod.' - '.$ICNumber.' ]';
		}
	}
	else
	{		
		$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
		$stmt=$mysqli->stmt_init();
		if($stmt->prepare("Select concat(MonthName(ICDate) , ' ' ,Year(ICDate)) as `ICPeriod`,BName From inventorycontrol Inner Join branch on branch.unBranch=inventorycontrol.unBranch Where (ICDate Between ? and ?) and inventorycontrol.unBranch=? Limit 1")){
			$stmt->bind_param('ssi',$Mid,$Mad,$bid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($ICPeriod,$BName);
			$stmt->fetch();
			$rowcount=$stmt->num_rows();
			$stmt->close();
			return ($rowcount==0)?'':$BName.' ['.$ICPeriod.']';
		}
	}	
}

function CreateNewInventorySheet($unBranch,$unAccountUser,$Date,$DIRNumber,$Remarks)
{
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	if($stmt->prepare('Call CreateIV(?,?,?,?,?)')){
		$stmt->bind_param('iisis',$unBranch,$unAccountUser,$Date,$Remarks,$DIRNumber);
		$stmt->execute();
		$stmt->close();
	}
}

function GetMaxInventoryControlID($unAccountUser)
{
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt=$mysqli->stmt_init();
	if($stmt->prepare('Select ifNull(Max(unInventoryControl),0) as `MaxID` From inventorycontrol Where unAccountUser = ?')){
		$stmt->bind_param('i',$unAccountUser);
		$stmt->execute();
		$stmt->bind_result($MaxID);
		$stmt->fetch();
		$stmt->close();
		return $MaxID;
	}
}

function ExecuteReader($query)
{
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$res=$mysqli->query($query);
	$row = $res->fetch_assoc();
	$mysqli->close();
	return $row['result'];
}

function ExecuteNonQuery($query)
{
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$mysqli->query($query);
	$mysqli->close();
}

function TableHasRow($Table)
{
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$res=$mysqli->query('Select ifNull(Count(un'.$Table.'),0) as `RowCount` From '.strtolower($Table).' Where `Status` = 1');
	$row = $res->fetch_assoc();
	$mysqli->close(); 
	return ($row['RowCount']>0)?true:false;	
}
function CreatePassword($CPUser,$CPPass){
	$aucount = strlen($CPUser);
	$salt = substr(md5($CPUser),$aucount);
	$pwhash = md5($CPPass);
	$pwhashsount=strlen($pwhash);
	return substr($pwhash,$aucount).$salt.substr($pwhash,$aucount+1,$pwhashsount-$aucount);
}

function SaveSales($unInventoryControl,$SBeginningBalance,$STotalSales,$SCashDeposit,$SPettyCash,$SDiscount,$SGiftCertificate,$SCreditCard,$SLOA,$SEndingBalance,$SCashCount,$SShortage,$unSales=0,$unBranch=0){
	$sQuery = "Select SCashCount as `result` From sales 
				Where unInventoryControl = (Select unInventoryControl From inventorycontrol Where ICInventoryNumber = (Select Max(ICInventoryNumber) - 1 From inventorycontrol Where unBranch = ".$unBranch.") and unBranch = ".$unBranch.")";
				
	$BeginBalance = ExecuteReader($sQuery);
	
	ExecuteNonQuery("Insert Into accountlog (ALDescription) Values ('".$BeginBalance." - ".$unBranch."')");

	$BeginBalance = ($BeginBalance=='')?0:$BeginBalance;

	$mysqli = new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($unSales==0){
		if($stmt->prepare("Insert Into sales (unInventoryControl,SBeginningBalance,STotalSales,SCashDeposit,SPettyCash,SDiscount,SGiftCertificate,SCreditCard,SLOA,SEndingBalance,SCashCount,SShortage) Values (?,?,?,?,?,?,?,?,?,?,?,?)")){
			$stmt->bind_param('iddddddddddd',$unInventoryControl,floatval($BeginBalance),$STotalSales,$SCashDeposit,$SPettyCash,$SDiscount,$SGiftCertificate,$SCreditCard,$SLOA,$SEndingBalance,$SCashCount,$SShortage);
			$stmt->execute();
			$stmt->close();
		}	
	}else{
		if($stmt->prepare("Update sales Set unInventoryControl=?,SBeginningBalance=?,STotalSales=?,SCashDeposit=?,SPettyCash=?,SDiscount=?,SGiftCertificate=?,SCreditCard=?,SLOA=?,SEndingBalance=?,SCashCount=?,SShortage=? Where unSales = ?")){
			$stmt->bind_param('idddddddddddi',$unInventoryControl,$SBeginningBalance,$STotalSales,$SCashDeposit,$SPettyCash,$SDiscount,$SGiftCertificate,$SCreditCard,$SLOA,$SEndingBalance,$SCashCount,$SShortage,$unSales);
			$stmt->execute();
			$stmt->close();
		}
	}
}

function getMax($field,$table){
	$mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
	$stmt = $mysqli->stmt_init();
	if($stmt->prepare("SELECT ifnull(MAX(".$field."),0)+1 FROM ".$table)){
		$stmt->execute();
		$stmt->bind_result($max);
		$stmt->fetch();
		//$stmt->close();
	}
	return $max;
	}

	/*$CPUser='princess';
	$CPPass='1111';
	$aucount = strlen($CPUser);
	$salt = substr(md5($CPUser),$aucount);
	$pwhash = md5($CPPass);
	$pwhashsount=strlen($pwhash);
	echo substr($pwhash,$aucount).$salt.substr($pwhash,$aucount+1,$pwhashsount-$aucount);*/
?>
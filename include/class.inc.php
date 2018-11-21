<?php

//session_start();
class iMySQLi extends MySQLi
{
	function prepare ($query){
		$timezone = 'Asia/Manila';
		if(function_exists('date_default_timezone_set')) { date_default_timezone_set($timezone);}
		$ALTimeStamp = date('Y-m-d H:i:s');
		$oAccountUser=$_SESSION['oAccountUser'];
		$query2="Insert Into accountlog (unAccountUser, ALPage, ALDescription, ALTimeStamp) Values (".$oAccountUser->unAccountUser.",'".$_SERVER['PHP_SELF']."','".$query."', TIMESTAMP '".$ALTimeStamp ."')";			
		//ExecuteNonQuery($query2);
		
		return parent::prepare($query);
	}
	function savelog($description){
		$timezone = 'Asia/Manila';
		if(function_exists('date_default_timezone_set')) { date_default_timezone_set($timezone);}
		$ALTimeStamp = date('Y-m-d H:i:s');
		$oAccountUser=$_SESSION['oAccountUser'];
		$query2="Insert Into accountlog (unAccountUser, ALPage, ALDescription, ALTimeStamp) Values (".$oAccountUser->unAccountUser.",'".$_SERVER['PHP_SELF']."','".$description."', TIMESTAMP '".$ALTimeStamp ."')";			
		ExecuteNonQuery($query2);
	}
}

class AccountUser
{
	public $unAccountUser;
	public $unAccountGroup;
	public $AUUsername;
	public $AULastName;
	public $AUFirstName;
	public $AUMiddleName;
	public $AUPassword;
	public $AUSession;
	public $Status;
	
	function AccountUser($unAccountGroup,$AUUsername,$AULastName,$AUFirstName,$AUMiddleName,$AUPassword,$AUSession,$Status,$unAccountUser=0){
		$this->unAccountUser=$unAccountUser;
		$this->unAccountGroup=$unAccountGroup;
		$this->AUUsername=$AUUsername;
		$this->AULastName=$AULastName;
		$this->AUFirstName=$AUFirstName;
		$this->AUMiddleName=$AUMiddleName;
		$this->AUPassword=$AUPassword;
		$this->AUSession=$AUSession;
		$this->Status=$Status;
	}
	
	function getFullName(){
		return strtoupper($this->AULastName).', '.$this->AUFirstName.' '.substr($this->AUMiddleName,0,1).'.';
	}	
}

class TemplateItemData
{
	public $unTemplateItemData;
	public $unTemplateItemControl;
	public $unProductItem;
	public $unProductGroup;
	public $TIDPrice;
	public $TIDCost;
	public $TIDPriority;
	public $Status;
	public $unProductType;
	public $PTName;
	public $PGName;
	
	function TemplateItemData($unTemplateItemControl, $unProductItem, $unProductGroup, $TIDPrice, $TIDCost, $TIDPriority, 
								$Status, $unTemplateItemData=0, $unProductType, $PTName, $PGName)
	{
		$this->unTemplateItemControl=$unTemplateItemControl;
		$this->unProductItem=$unProductItem;
		$this->unProductGroup=$unProductGroup;
		$this->unTemplateItemData=$unTemplateItemData;
		$this->TIDPrice=$TIDPrice;
		$this->TIDCost=$TIDCost;
		$this->TIDPriority=$TIDPriority;
		$this->unProductType=$unProductType;
		$this->PTName=$PTName;
		$this->PGName=$PGName;
		$this->Status=$Status;
	}
	
	
}

class ProductItemTemp
{
	public $unProductItem;
	public $unProductGroup;
	public $unProductUOM;
	public $unTemplateItemData;
	public $PIName;
	public $PICost;
	public $PIPrice;
	public $PIPriority;
	public $PISAPCode;
	public $Status;
	public $CheckBool;
	public $unProductType;
	public $PTName;
	public $PGName;
	
	function ProductItemTemp($unProductItem, $unProductGroup, $unProductUOM, $unTemplateItemData, 
							 $PIName, $PICost, $PIPrice, $PIPriority, $PISAPCode, $Status, $CheckBool, 
							 $unProductType, $PTName, $PGName)
	{
		$this->unProductItem=$unProductItem;
		$this->unProductGroup=$unProductGroup;
		$this->unProductUOM=$unProductUOM;
		$this->unTemplateItemData=$unTemplateItemData;
		$this->PIName=$PIName;
		$this->PICost=$PICost;
		$this->PIPrice=$PIPrice;
		$this->PIPriority=$PIPriority;
		$this->PISAPCode=$PISAPCode;
		$this->Status=$Status;
		$this->CheckBool=$CheckBool;
		$this->unProductType=$unProductType;
		$this->PTName=$PTName;
		$this->PGName=$PGName;
	}
}

class TemplateProductionData
{
	public $unProductItem;
	public $unProductGroup;
	public $unProductUOM;
	public $PIName;
	public $PUOMName;
	public $PGName;
	public $Status;
	
	function TemplateProductionData($unProductItem, $unProductGroup, $unProductUOM, $PIName, $PUOMName, $PGName, $Status)
	{
		$this->unProductItem=$unProductItem;
		$this->unProductGroup=$unProductGroup;
		$this->unProductUOM=$unProductUOM;
		$this->PIName=$PIName;
		$this->PUOMName=$PUOMName;
		$this->PGName=$PGName;
		$this->Status=$Status;
	}
}

class ProductItem
{
	public $unProductItem;
	public $unProductGroup;
	public $unProductUOM;
	public $PIName;
	public $PISAPCode;
	public $Status;
	
	function ProductItem($unProductItem, $unProductGroup, $unProductUOM, $PIName, $PISAPCode, $Status)
	{
		$this->unProductItem=$unProductItem;
		$this->unProductGroup=$unProductGroup;
		$this->unProductUOM=$unProductUOM;
		$this->PIName=$PIName;
		$this->PISAPCode=$PISAPCode;
		$this->Status=$Status;
	}
}

class ProductGroup
{
	public $unProductGroup;
	public $unProductType;
	public $PGName;
	public $Status;
	
	function ProductGroup($unProductGroup, $unProductType, $PGName, $Status)
	{
		$this->unProductGroup=$unProductGroup;
		$this->unProductType=$unProductType;
		$this->PGName=$PGName;
		$this->Status=$Status;
	}
}

class ProductUOM
{
	public $unProductUOM;
	public $PUOMName;
	public $Status;
	
	function ProductUOM($unProductUOM, $PUOMName, $Status)
	{
		$this->unProductUOM=$unProductUOM;
		$this->PUOMName=$PUOMName;
		$this->Status=$Status;
	}
}

class TemplateProductionControl
{
	public $unTemplateProductionControl;
	public $unProductItem;
	public $unProductUOM;
	public $unProductGroup;
	public $PGName;
	public $PIName;
	public $PUOMName;
	public $TPCYield;
	public $TPCCost;
	public $Status;
	
	function TemplateProductionControl($unTemplateProductionControl, $unProductItem, $unProductUOM, $unProductGroup, $PGName, $PIName, $PUOMName, $TPCYield, $TPCCost, $Status){
		$this->unTemplateProductionControl=$unTemplateProductionControl;
		$this->unProductItem=$unProductItem;
		$this->unProductUOM=$unProductUOM;
		$this->unProductGroup=$unProductGroup;
		$this->PGName=$PGName;
		$this->PIName=$PIName;
		$this->PUOMName=$PUOMName;
		$this->TPCYield=$TPCYield;
		$this->TPCCost=$TPCCost;
		$this->Status=$Status;
	}

}

class TransferData
{
	public $unTransferData;
	public $unProductUOM;
	public $unProductItem;
	public $TDQuantity;
	
	function TransferData($unTransferData,$unProductUOM,$unProductItem,$TDQuantity){
		$this->unTransferData=$unTransferData;
		$this->unProductUOM=$unProductUOM;
		$this->unProductItem=$unProductItem;
		$this->TDQuantity=$TDQuantity;
	}
}

class SAPProductItem
{
	public $ItemCode;
	public $Description;
	public $Quantity;
	public $Unit;
	
	function SAPProductItem($ItemCode,$Description,$Quantity,$Unit){
		$this->ItemCode=$ItemCode;
		$this->Description=$Description;
		$this->Quantity=$Quantity;
		$this->Unit=$Unit;
	}
}

class Collection
{
	private $Item=array();
	private $Key=array();
	
	function Collection(){
		$this->Add('','');
	}
	
	public function Add($Object,$Key){
		array_push($this->Item,$Object);
		array_push($this->Key,$Key);
	}
	public function GetByKey($Key){
		$Index=array_search($Key,$this->Key);
		if ($Index!=false or !empty($Index)){
			return $this->Item[$Index];
		}
	}
	public function GetByIndex($Index){
		return $this->Item[$Index];
	}
	public function GetKey($Index){
		return $this->Key[$Index];
	}
	
	public function Remove($Key){
		$Index=array_search($Key,$this->Key);
		unset($this->Key[$Index]);
		unset($this->Item[$Index]);
	}
	public function Clear(){
		empty($this->Key);
		empty($this->Item);
		$this->Add('','');
	}
	public function Count(){
		return count($this->Item);
	}
	
}

	$colSideProductItem = new Collection;
	$colSideProductItemPageLoad = new Collection;
	$colSideProductItemTransfer = new Collection;
	$colMainProductItem = new Collection;
	$colAddProductItem = new Collection;
	$colProductionControl = new Collection;
	
?>

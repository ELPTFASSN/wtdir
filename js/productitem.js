// JavaScript Document

$(document).ready(function(e) {
	$('#txtproductitem').keyup(function(e) {
		if(e.keyCode==13){
			$('#cmbproductgroup').focus();
			return false;
		}
	});
	$('#cmbproductgroup').keyup(function(e) {
		if(e.keyCode==13){
			$('#cmbproductitemuom').focus();
			return false;
		}	
    });
	$('#cmbproductitemuom').keyup(function(e) {
		if(e.keyCode==13){
			$('#txtproductitemsap').focus();
			return false;
		}	
    });
	$('#txtproductitemsap').keyup(function(e) {
		if(e.keyCode==13){
			$('#txtppp').focus();
			return false;
		}	
    });
});

function clearentry(){
	$('#cmbunit').val();
	$('#txtratio').val('');
	$('#txtset').val('');
}

function additem(idproductitem,idproductuom,unitname,pcratio,pcset){
	var count = ($('#hdncount').val()-1)+2;
		
	if (idproductitem=='',idproductuom=='',unitname=='',pcratio=='',pcset==''){
		msgbox('Add failed, complete the fields','','');
		return false;
	}
	
	var lvitem = document.createElement('div');
	var lvsubitemunit = document.createElement('div');
	var lvsubitemratio = document.createElement('div');
	var lvsubitemset = document.createElement('div');
	
	var txtunit = document.createElement('input');
	var txtratio = document.createElement('input');
	var txtset = document.createElement('input');
	var txtidproductitem = document.createElement('input');
	var txtidproductuom = document.createElement('input');
	var btndelete = document.createElement('div');
	
	lvitem.setAttribute('class','listviewitem');
	lvitem.setAttribute('id','lvitem-'+count);
	
	lvsubitemunit.setAttribute('class','listviewsubitem');
	lvsubitemratio.setAttribute('class','listviewsubitem');
	lvsubitemset.setAttribute('class','listviewsubitem');
	
	lvsubitemunit.style.width='100px';
	lvsubitemratio.style.width='100px';
	lvsubitemset.style.width='50px';

	txtunit.type='input';
	txtunit.value=unitname;
	txtunit.readOnly=true;
	txtunit.style.border='none';
	txtunit.style.backgroundColor='transparent';
	txtunit.style.width='inherit';

	txtratio.type='input';
	txtratio.name='txtpcratio-'+count;
	txtratio.value=parseFloat(pcratio).toFixed(10);
	txtratio.readOnly=true;
	txtratio.style.border='none';
	txtratio.style.backgroundColor='transparent';
	txtratio.style.width='inherit';
	txtratio.style.textAlign='right';
	
	txtset.type='input';
	txtset.name='txtpcset-'+count;
	txtset.value=pcset
	txtset.readOnly=true;
	txtset.style.border='none';
	txtset.style.backgroundColor='transparent';
	txtset.style.width='inherit';
	txtset.style.textAlign='right';

	txtidproductitem.type='hidden';
	txtidproductitem.name='hdnproduct-'+count;
	txtidproductitem.value=idproductitem;
	txtidproductitem.style.width='inherit';
	
	txtidproductuom.type='hidden';
	txtidproductuom.name='hdnunit-'+count;
	txtidproductuom.value=idproductuom;
	txtidproductuom.style.width='inherit';
	
	btndelete.setAttribute('class','button16');
	btndelete.style.backgroundImage='url(img/icon/delete.png)';
	btndelete.style.paddingTop='5px';
	btndelete.style.paddingLeft='0px';
	btndelete.onclick=function(){ 
		if(confirm('Remove [ ' + unitname + ' ] Are you sure?') ){
			var d = document.getElementById('rowproductconversion')
			var olddiv = lvitem;		
			d.removeChild(olddiv);
		}
	}

	lvsubitemunit.appendChild(txtunit);
	lvsubitemratio.appendChild(txtratio);
	lvsubitemset.appendChild(txtset);

	lvitem.appendChild(lvsubitemunit);
	lvitem.appendChild(lvsubitemratio);
	lvitem.appendChild(lvsubitemset);

	lvitem.appendChild(txtidproductitem);
	lvitem.appendChild(txtidproductuom);

	lvitem.appendChild(btndelete);

	document.getElementById('rowproductconversion').appendChild(lvitem);
	$('#hdncount').val(count);
	clearentry();
}
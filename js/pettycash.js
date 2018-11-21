// JavaScript Document
$(document).ready(function(e) {
	$('#txtDescription').keyup(function(e){
		if(e.which==13){
			$('#txtAmount').focus();
		}
	});
	$('#txtAmount').keyup(function(e){
		if(e.which==13){
			$('#btnAddData').mouseup();
			 $('#txtDescription').select();
		}
	});
	
	$("#txtAmount").keydown(function(event) {
        // Allow: backspace, delete, tab, escape, and enter
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39) ||
			(event.keyCode == 190 || event.keyCode == 110)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });
});

function LoadPettyCashData(idPTC){
	var xmlhttp;
	if (idPTC==0){
		return;
	}

	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				var arr = xmlhttp.responseText.split('®');
				var iLen = arr.length - 2;
				for(i=0; i<=iLen; i++){
					var arritem = arr[i].split('©');
					addElement(arritem[0],arritem[1],'exist');
				}
			}
		}
	}
	xmlhttp.open('POST','ajax/pettycash.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=LoadPettyCashData&id='+idPTC);
}

function addElement(Description,Amount,optStat){
	Description = $.trim(Description);
	
	optStat = (typeof optStat == 'undefined') ? 'new' : optStat;
	
	if(Description=='' || Amount==0 || Amount==''){return;}
	
	var icount = (document.getElementById('hdnCount').value -1)+ 2;
	var ListViewItem = document.createElement('div');
	var divId = 'lvItem-'+icount;
	
	// Subitem Product
	var SubItemDescription = document.createElement('div');
	var txtDescription = document.createElement('input');
	
	txtDescription.readOnly = true;
	txtDescription.id = 'txtdescription-'+icount;
	txtDescription.name = 'txtdescription-'+icount;
	txtDescription.type = 'text';
	txtDescription.style.marginLeft = '2px';
	txtDescription.style.width = '494px';
	txtDescription.style.border = 'none';
	txtDescription.style.backgroundColor = 'transparent';
	txtDescription.value = Description;
	
	SubItemDescription.style.width = '500px';
	SubItemDescription.setAttribute('class','listviewsubitem');
	SubItemDescription.appendChild(txtDescription);
	
	// Subitem Quantity
	var SubItemAmount = document.createElement('div');
	var txtAmount = document.createElement('input');
	
	txtAmount.readOnly = true;
	txtAmount.id = 'txtamount-'+icount;
	txtAmount.name = 'txtamount-'+icount;
	txtAmount.type = 'text';
	txtAmount.style.textAlign = 'right';
	txtAmount.style.marginLeft = '2px';
	txtAmount.style.width = '94px';
	txtAmount.style.border = 'none';
	txtAmount.style.backgroundColor = 'transparent';
	txtAmount.value = parseFloat(Amount).toFixed(2);
	
	SubItemAmount.style.width = '100px';
	SubItemAmount.setAttribute('class','listviewsubitem');
	SubItemAmount.appendChild(txtAmount);
	
	// Subitem Button Remove
	var SubItemRemoveButton = document.createElement('div');
	var btnRemove = document.createElement('input');
	
	btnRemove.type = 'button';
	btnRemove.id = 'btnremove-'+icount;
	btnRemove.style.marginTop = '2px';
	btnRemove.style.width = '16px';
	btnRemove.style.height = '16px';
	btnRemove.style.padding = '0px';
	btnRemove.style.border = 'none';
	btnRemove.style.backgroundImage = 'url(img/icon/delete.png)';
	btnRemove.style.backgroundRepeat = 'no-repeat';
	btnRemove.style.backgroundColor = 'transparent';
	btnRemove.style.cursor = 'pointer';
	btnRemove.onclick = function(){
		removeelement(divId);
	};
	btnRemove.setAttribute('title','Remove');
	
	SubItemRemoveButton.setAttribute('class','listviewsubitem');
	SubItemRemoveButton.appendChild(btnRemove);
	
  	ListViewItem.setAttribute('id',divId);
	ListViewItem.setAttribute('class','listviewitem');
	ListViewItem.style.borderBottom = '#EEE thin solid';
	ListViewItem.appendChild(SubItemDescription);
	ListViewItem.appendChild(SubItemAmount);
	ListViewItem.appendChild(SubItemRemoveButton);

	document.getElementById('hdnCount').value = icount;
	document.getElementById('pettycashdata').appendChild(ListViewItem);
	document.getElementById('txtAmount').value = '';
	document.getElementById('txtDescription').value = '';
	document.getElementById('txtDescription').focus();
	if(optStat == 'new'){
		sumtotal();
	}
}

function removeelement(divId){
	var res = confirm("Remove ["+ document.getElementById('txtdescription-'+divId.split('-')[1]).value +"] from list. Are you sure?");
	if(res == true){
		var d = document.getElementById('pettycashdata');
		var olddiv = document.getElementById(divId);
		d.removeChild(olddiv);
		sumtotal();
	}
}

function sumtotal(){
	var totalamount = 0;
	var itemcount =($('#hdnCount').val()-1)+1;
	for(i=1;i<=itemcount;i++){
		if(document.getElementById('txtamount-'+i)!=undefined){
			totalamount += parseFloat($('#txtamount-'+i).val());
		}
	}
	
	$('#txtTotalAmount').val(totalamount.toFixed(2));	
}

function DisplayPettyCashData(idPCC){
	var xmlhttp;
	if (idPCC==0){
		$('#rowpettycashdata').html('');
		return;
	}

	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				$('#rowpettycashdata').html(xmlhttp.responseText);
				$('#hdnSaveMapping').val(idPCC);
			}
		}
	}
	xmlhttp.open('POST','ajax/pettycash.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=DisplayPettyCashData&id='+idPCC);
}

function SaveMapping(idPTC){
	if(idPTC==0){
		msgbox('Select','#mappettycash','');
		return false;
	}
	$('#frmMapPettyCash')
	$('#frmMapPettyCash').submit();
}

function showactions(idPCC,stitle){
	document.getElementById('actiontitle').innerHTML = stitle;
	document.getElementById('hdnidPCC').value = idPCC;
	location.href='#action';
}
// JavaScript Document
$(document).ready(function(e) {
	$('#txtSearch').keyup(function(e) {
		if(e.keyCode==13){
			$('.transelected').click();
			$('#txtQuantity').focus();
			if($(this).val() == ''){
				$('#cmbUnit').html('');
			}
			return false;
		}else if(e.which==38){
			listnum = $('.listboxitem').length;
			iddel = $('.transelected').attr('id');
			idnum = iddel.match(/\d+/);
			nextnum = Math.max(0, parseInt(idnum) - 1);
			$('#SearchItem-'+idnum).removeClass('transelected');
			$('#SearchItem-'+idnum).css('background-color','');
			$('#SearchItem-'+nextnum).addClass('transelected');
			$('#SearchItem-'+nextnum).css('background-color','#DFD');
			//alert(listnum);
		}else if(e.which==40){
			listnum = $('.listboxitem').length;
			iddel = $('.transelected').attr('id');
			idnum = iddel.match(/\d+/);
			if((parseInt(idnum)+1)<listnum){
				nextnum = parseInt(idnum) + 1;
			}else if((parseInt(idnum)+1)==listnum){
				nextnum = parseInt(idnum);
			}
			$('#SearchItem-'+idnum).removeClass('transelected');
			$('#SearchItem-'+idnum).css('background-color','');
			$('#SearchItem-'+nextnum).addClass('transelected');
			$('#SearchItem-'+nextnum).css('background-color','#DFD');
			//alert(listnum);
		}else{
			searchstring(this.value);
		}
	});
	
	$('#txtQuantity').keyup(function(e){
		if(e.which==13){
			if($('#cmbUnit').children().length>1){
				$('#cmbUnit').focus();	
			}else{
				$('#btnAddData').mouseup();
			}
			
		}
	});
	$('#cmbUnit').keyup(function(e){
		if(e.which==13){
			$('#btnAddData').mouseup();
		}
	});
	$("#txtQuantity").keydown(function(event) {
        // Allow: backspace, delete, tab, escape, and enter
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
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

function CheckReason(id)
{
	var i = $(id).attr('id').split('-')[1];
	parseInt(i);
	if(i==2){
		clear();
		document.getElementById("itftxt-" + i).removeAttribute('readonly');
		document.getElementById("itftxt-" + i).setAttribute('required','required');
	}else{
		clear();
	};
}

function clear(){
	document.getElementById("itftxt-2").setAttribute('readonly','readonly');
	document.getElementById("itftxt-2").removeAttribute('required');	
	document.getElementById("itftxt-2").value = '';
}

function searchstring(string){
	var xmlhttp;
	if (string==''){
		$('#lstresult').css('display','none');	
		$('#lstresult').html('');
		return;
	}
	//alert(string);
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				var top = getOffset(document.getElementById('txtSearch')).top + 25;
				var left = getOffset(document.getElementById('txtSearch')).left
				$('#lstresult').css('display','block');
				$('#lstresult').css('top',top);
				$('#lstresult').css('left',left);
				$('#lstresult').html(xmlhttp.responseText);
				$('#SearchItem-0').css('background-color','#DFD');
				$('#SearchItem-0').addClass('transelected');
			}else{
				$('#lstresult').css('display','none');
				$('#lstresult').html('');
				$('#cmbUnit').html('');
			}
		}
	}
	xmlhttp.open('POST','ajax/transfer.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=SearchItem&search='+string);
}

function selectresult(string,value){
	$('#txtSearch').val(string);
	$('#hdnSearchId').val(value);
	loaduom(value,'cmbUnit');
	searchstring('');
	$('#txtQuantity').focus();
}

function addElement(idProductItem,PIName,Quantity,idProductUOM,action){
	document.getElementById('txtSearch').focus();
	if(Quantity=='' || idProductUOM==0 || idProductItem==0){return;}
	//alert(document.getElementById('hdnCount').value);
	var icount = (document.getElementById('hdnCount').value -1)+ 2;
	//var icount = (document.getElementById('hdnCount').value)
	
	for(i=1;i<icount;i++){
		if(document.getElementById('hdn-'+i+'-product')!=undefined){
			if(document.getElementById('hdn-'+i+'-product').value==idProductItem){
				if(document.getElementById('hdn-'+i+'-unit').value==idProductUOM){
					msgbox('Failed to add entry, item already exists on the list.','','');
					return false;
				}
			}
		}
	}
	
	var ListViewItem = document.createElement('div');
	var divId = 'lvItem-'+icount;
	
	// Subitem Product
	var SubItemProduct = document.createElement('div');
	var txtProductItem = document.createElement('input');
	var hdnProductItemId = document.createElement('input');
	
	txtProductItem.readOnly = true;
	txtProductItem.id = 'txt-'+icount+'-product';
	txtProductItem.type = 'text';
	txtProductItem.style.marginLeft = '2px';
	txtProductItem.style.width = '500px';
	txtProductItem.style.border = 'none';
	txtProductItem.style.backgroundColor = 'transparent';
	txtProductItem.value = PIName;
	
	hdnProductItemId.id = 'hdn-'+icount+'-product';
	hdnProductItemId.name = 'hdn-'+icount+'-product';
	hdnProductItemId.type = 'hidden';
	hdnProductItemId.value = idProductItem;
	
	SubItemProduct.setAttribute('class','listviewsubitem');
	SubItemProduct.appendChild(txtProductItem);
	SubItemProduct.appendChild(hdnProductItemId);
	
	// Subitem Quantity
	var SubItemQuantity = document.createElement('div');
	var txtQuantity = document.createElement('input');
	
	txtQuantity.name = 'txt-'+icount+'-qty';
	txtQuantity.id = 'txt-'+icount+'-qty';
	txtQuantity.readOnly = true;
	txtQuantity.type = 'text';
	txtQuantity.style.marginLeft = '4px';
	txtQuantity.style.border = 'none';
	txtQuantity.style.width = '56px';
	txtQuantity.style.textAlign = 'Center';
	txtQuantity.style.backgroundColor = 'transparent';
	txtQuantity.value = Quantity;
	
	SubItemQuantity.setAttribute('class','listviewsubitem');
	SubItemQuantity.appendChild(txtQuantity);
	
	// Subitem UOM
	var SubItemUOM = document.createElement('div');
	var txtUOM = document.createElement('input');
	var hdnUOMId = document.createElement('input');
	
	txtUOM.setAttribute('class','listviewsubitem');
	txtUOM.readOnly = true;
	txtUOM.type = 'text';
	txtUOM.style.border = 'none';
	txtUOM.style.width = '72px';
	txtUOM.style.textAlign = 'center';
	txtUOM.style.backgroundColor = 'transparent';
	txtUOM.value = document.getElementById('cmbUnit').options[document.getElementById('cmbUnit').selectedIndex].text;
	
	hdnUOMId.id = 'hdn-'+icount+'-unit';
	hdnUOMId.name = 'hdn-'+icount+'-unit';
	hdnUOMId.type = 'hidden';
	hdnUOMId.value = idProductUOM;
	
	SubItemUOM.setAttribute('class','listviewsubitem');
	SubItemUOM.appendChild(txtUOM);
	SubItemUOM.appendChild(hdnUOMId);
	
	// Button
	var SubItemRemoveButton = document.createElement('div');
	var btnRemove = document.createElement('input');
	
	btnRemove.type = 'button';
	btnRemove.setAttribute('id','btn-'+icount+'-remove');
	btnRemove.style.marginTop = '6px';
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
	SubItemRemoveButton.style.marginLeft = '2px';
	SubItemRemoveButton.appendChild(btnRemove);
	
  	ListViewItem.setAttribute('id',divId);
	ListViewItem.setAttribute('class','listviewitem');
	ListViewItem.style.borderBottom = '#EEE thin solid';
	ListViewItem.appendChild(SubItemProduct);
	ListViewItem.appendChild(SubItemQuantity);
	ListViewItem.appendChild(SubItemUOM);
	
	if(action=='update'){
		var btnEdit = document.createElement('input');
		var SubItemEditButton = document.createElement('div');
		
		btnEdit.type = 'button';
		btnEdit.setAttribute('id','btn-'+icount+'-remove');
		btnEdit.style.marginTop = '6px';
		btnEdit.style.width = '16px';
		btnEdit.style.height = '16px';
		btnEdit.style.padding = '0px';
		btnEdit.style.border = 'none';
		btnEdit.style.backgroundImage = 'url(img/icon/edit.png)';
		btnEdit.style.backgroundRepeat = 'no-repeat';
		btnEdit.style.backgroundColor = 'transparent';
		btnEdit.style.cursor = 'pointer';
		btnEdit.onclick = function(){
			edittransferdata(idProductItem,Quantity,icount,idProductUOM);
		};
		btnEdit.setAttribute('title','Edit');
		
		SubItemEditButton.setAttribute('class','listviewsubitem');
		SubItemEditButton.style.marginLeft = '2px';
		SubItemEditButton.style.minWidth = '20px';
		SubItemEditButton.appendChild(btnEdit);
		
		var hdnTransferData = document.createElement('input');
		hdnTransferData.type = 'hidden';
		hdnTransferData.name = 'hdn-'+icount+'-idtransferdata';
		hdnTransferData.value = 0;
		
		ListViewItem.appendChild(hdnTransferData);
		ListViewItem.appendChild(SubItemEditButton);
	}
	ListViewItem.appendChild(SubItemRemoveButton);

	selectresult('','');
	document.getElementById('hdnCount').value = icount;
	document.getElementById('transferdata').appendChild(ListViewItem);
	document.getElementById('txtQuantity').value = '';
	document.getElementById('txtSearch').focus();
}

function removeelement(divId){
	var res = confirm("Remove ["+ document.getElementById('txt-'+divId.split('-')[1]+'-product').value +"] from list. Are you sure?");
	if(res == true){
		var d = document.getElementById('transferdata');
		var olddiv = document.getElementById(divId);
		d.removeChild(olddiv);
		document.getElementById('txtSearch').focus();
	}
}

function edittransferdata(idPI,qty,icount,idUOM){
	var xmlhttp;
	if(idPI==0){
		document.getElementById('etdcontainer').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('etdcontainer').innerHTML=xmlhttp.responseText;
			loaduom(idPI,'editcmbunit');
			document.getElementById('editcmbunit').value = idUOM;
			location.href='#edittransferdata';
		}
	}
	xmlhttp.open('POST','ajax/transfer.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=EditTransferData&idPI='+idPI+'&qty='+qty+'&icount='+icount);
}

function FetchITFControl(qid,docnum){
	if (docnum==''){return;}
	$.post('ajax/transfer.ajax.php',
	{
		qid:qid,
		DocNum:docnum
	},
	function(data,status){
		var myResponse = data;
		var myArray = myResponse.split('@');	
		var myDate = myArray[1].split(' ');

		$('#dtpdate').val(myDate[0]);
		$('#cmbbranchfrom').val(myArray[6]);
		$('#cmbbranchto').val(myArray[7]);
		FetchITFData(myArray[5]);
	});
}

function FetchITFData(docentry){
	if (docentry==''){return;}
	$.post('ajax/transfer.ajax.php',
	{
		qid:'FetchITFData',
		DocEntry:docentry
	},
	function(data,status){
		$('#transferdata').html(data);
		$('#hdnFlag').val($('#hdnFlagTemp').val());
		$('#hdnCount').val($('#transferdata').children().length());
	});
}
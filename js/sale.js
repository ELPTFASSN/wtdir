// JavaScript Document
$(document).ready(function(e) {
	$('#txtEmployee').keyup(function(e) {
		if(e.keyCode==13){
			$('#SearchItem-0').click();
			$('#cmbRole').focus();
			return false;
		}else{
			searchstring(this.value);
		}
	});
	
    var h = $('#lvCrewOnDuty').height()-$('#colCrewOnDuty').height();
    $('#rowCrewOnDuty').height(h);
	
	$('#cmbRole').keyup(function(e){
		if(e.which==13){
			$('#txtCash').select();
			$('#txtCash').focus();
		}
	});
	$('#txtCash').keyup(function(e) {
        if(e.which==13){
			$('#txtInv').select();
			$('#txtInv').focus();
		}
    });
	$('#txtInv').keyup(function(e) {
        if(e.which==13){
			$('#txtQuota').select();
			$('#txtQuota').focus();
		}
    });
	$('#txtQuota').keyup(function(e) {
        if(e.which==13){
			$('#btnAddData').click();
			$('#txtEmployee').focus();
		}
    });
	
	$('.salesitem').keyup(function(e) {
        if(e.keyCode==13){
			if($(this).attr('id') == 'txtCashDeposit'){
				$('#txtCashCount').select();
				$('#txtCashCount').focus();
			}	
			if($(this).attr('id') == 'txtCashCount'){
				$('#txtCashDeposit').select();
				$('#txtCashDeposit').focus();
			}			
		}
    });
	
	$('.salesitem').focusout(function(){
        var EndingBalance = 0;
		var Shortage = 0;
		
		var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
		if (!numberRegex.test($(this).val())){
			$(this).attr('value','0.0000');
		}else{
			var str = $(this).attr('value');
			str = str.replace(/\s/g,'');
			str = parseFloat(str).toFixed(4);
			$(this).attr('value',str);
		};
		
		EndingBalance = (parseFloat($('#txtBeginningBalance').val()) + parseFloat($('#txtTotalSales').val())) - (parseFloat($('#txtCashDeposit').val()) + parseFloat($('#txtPettyCash').val()) + parseFloat($('#txtDiscount').val()) + parseFloat($('#txtGC').val()) + parseFloat($('#txtCC').val()) + parseFloat($('#txtLOA').val()));
		Shortage = parseFloat($('#txtCashCount').val()) - EndingBalance;
		
		if(Shortage < 0){
			$('#shortover').html('Shortage');
			$('#txtShortage').css('color','#F00');
			Shortage = Shortage * -1;
		}else{
			$('#shortover').html('Overage');
			$('#txtShortage').css('color','#000');
		}
		
		$('#txtEndingBalance').val(EndingBalance.toFixed(4));
		$('#txtShortage').val(Shortage.toFixed(4));
    });
	
	$('.txtQuantity').keydown(function(e) {
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
	
	$('.txtQuantity').keyup(function(e) {
        var row=$(this).attr('id').split('-')[1];
		if(e.keyCode == 13){
			row ++;
			if(row<=$('#hdnDCount').val()){
				$('#txtQty-'+row).select();
			}else{
				$('#txtCashDeposit').select();
			}
		}
    });
	
	$('.txtQuantity').focusout(function(e) {
        var row=$(this).attr('id').split('-')[1];
		//if(e.keyCode == 13){
			var amount = $('#txtDenomination-'+row).val() * $(this).val();
			$('#txtAmount-'+row).val(amount.toFixed(2));

			var qty = $(this).val() * 1;
			if(qty != 0){
				$(this).val(qty.toFixed(2));
			}else{
				$(this).val('');
			}
			
			var cashcount = 0;
			for(i=1;i<=$('#hdnDCount').val() - 1;i++){
				cashcount += parseFloat($('#txtAmount-'+i).val());
			}
			
			$('#txtCashCount').val(cashcount.toFixed(2));
			
			var EndingBalance = 0;
			var Shortage = 0;
		
			EndingBalance = (parseFloat($('#txtBeginningBalance').val()) + parseFloat($('#txtTotalSales').val())) - (parseFloat($('#txtCashDeposit').val()) + parseFloat($('#txtPettyCash').val()) + parseFloat($('#txtDiscount').val()) + parseFloat($('#txtGC').val()) + parseFloat($('#txtCC').val()) + parseFloat($('#txtLOA').val()));
			Shortage = parseFloat($('#txtCashCount').val()) - EndingBalance;
			
			if(Shortage < 0){
				$('#shortover').html('Shortage');
				$('#txtShortage').css('color','#F00');
				Shortage = Shortage * -1;
			}else{
				$('#shortover').html('Overage');
				$('#txtShortage').css('color','#000');
			}
			
			$('#txtEndingBalance').val(EndingBalance.toFixed(2));
			$('#txtShortage').val(Shortage.toFixed(2));
		//}
    });
	
	$('.txtQuantity').keyup(function(e){
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);
		switch(e.which){
            case 38: 
				// up
				row --;
				$('#txtQty-'+ row).select(); 
				break;
			case 40: 
				// down
				row ++;
				if(row<=$('#hdnDCount').val()){
					$('#txtQty-'+row).select();
				}else{
					$('#txtCashDeposit').select();
				}
				break;
		}
	});
});

function LoadInventoryEmployee(idIC){
	var xmlhttp;
	if(idIC==0){
		document.getElementById('crewonduty').innerHTML='';
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('crewonduty').innerHTML=xmlhttp.responseText;
			document.getElementById('hdnCount').value = $('#crewonduty').children().length;
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=LoadInventoryEmployee&idIC='+idIC);
}

function addElement(EName,idEmployee,cmbAssignment,CashPercent,InventoryPercent,QuotaPercent){	
	var icount = (document.getElementById('hdnCount').value -1)+ 2;
	
	if(EName=='' || idEmployee==0 || document.getElementById(cmbAssignment).value==''){
		document.getElementById('txtEmployee').focus();
		return false;
	}
	
	
	for(i=1;i<icount;i++){
		if(document.getElementById('hdn-'+i+'-name')!=undefined){
			if(document.getElementById('hdn-'+i+'-name').value==idEmployee){
				msgbox('Failed to add entry, Employee already exists on the list.','','');
				return false;
			}
		}
	}
	
	var ListViewItem = document.createElement('div');
	var divId = 'lvItem-' + icount;
	
	ListViewItem.id = divId;
	ListViewItem.setAttribute('class','listviewitem');
	ListViewItem.style.zIndex = '-1';
	
	// Name
	var SubItemName = document.createElement('div');
	var txtName = document.createElement('input');
	var hdnName = document.createElement('input');
	
	SubItemName.setAttribute('class','listviewsubitem');
	SubItemName.style.width = '196px';
	SubItemName.style.zIndex = '-1';
	
	txtName.type = 'text';
	txtName.readOnly = true;
	txtName.id = 'txt-'+icount+'-name';
	txtName.value = EName;
	txtName.style.border = 'none';
	txtName.style.backgroundColor = 'transparent';
	txtName.style.width = 'inherit';
	txtName.style.zIndex = '-1';
	
	hdnName.type = 'hidden';
	hdnName.id = 'hdn-'+icount+'-name';
	hdnName.name = 'hdn-'+icount+'-name';
	hdnName.value = idEmployee;
	
	SubItemName.appendChild(txtName);
	SubItemName.appendChild(hdnName);
	
	//Assignment
	var SubItemAssignment = document.createElement('div');
	var txtAssignment = document.createElement('input');
	
	SubItemAssignment.setAttribute('class','listviewsubitem');
	SubItemAssignment.style.width = '70px';
	SubItemAssignment.style.zIndex = '-1';
	
	txtAssignment.type = 'text';
	txtAssignment.readOnly = true;
	txtAssignment.name = 'txt-'+icount+'-role';
	txtAssignment.id = 'txt-'+icount+'-role';
	txtAssignment.value = document.getElementById(cmbAssignment).value;
	txtAssignment.style.border = 'none';
	txtAssignment.style.backgroundColor = 'transparent';
	txtAssignment.style.width = 'inherit';
	txtAssignment.style.zIndex = '-1';

	SubItemAssignment.appendChild(txtAssignment);
	
	// % Cash
	var SubItemCash = document.createElement('div');
	var txtCash = document.createElement('input');
	var icash = parseInt(CashPercent);
	
	SubItemCash.setAttribute('class','listviewsubitem');
	SubItemCash.style.width = '51px';
	SubItemCash.style.zIndex = '-1';
	
	txtCash.type = 'text';
	txtCash.readOnly = true;
	txtCash.name = 'txt-'+icount+'-cash';
	txtCash.id = 'txt-'+icount+'-cash';
	txtCash.value = icash.toFixed(2);
	txtCash.style.border = 'none';
	txtCash.style.backgroundColor = 'transparent';
	txtCash.style.width = 'inherit';
	txtCash.style.textAlign = 'right';
	txtCash.style.zIndex = '-1';
	
	SubItemCash.appendChild(txtCash);
	
	// % Inventory
	var SubItemInventory = document.createElement('div');
	var txtInventory = document.createElement('input');
	var iinventory = parseInt(InventoryPercent);
	
	SubItemInventory.setAttribute('class','listviewsubitem');
	SubItemInventory.style.width = '51px';
	SubItemInventory.style.zIndex = '-1';
	
	txtInventory.type = 'text';
	txtInventory.readOnly = true;
	txtInventory.name = 'txt-'+icount+'-inventory';
	txtInventory.id = 'txt-'+icount+'-inventory';
	txtInventory.value = iinventory.toFixed(2);
	txtInventory.style.border = 'none';
	txtInventory.style.backgroundColor = 'transparent';
	txtInventory.style.width = 'inherit';
	txtInventory.style.textAlign = 'right';
	txtInventory.style.zIndex = '-1';
	
	SubItemInventory.appendChild(txtInventory);
	
	// % Quota
	var SubItemQuota = document.createElement('div');
	var txtQuota = document.createElement('input');
	var iquota= parseFloat(QuotaPercent);
	
	SubItemQuota.setAttribute('class','listviewsubitem');
	SubItemQuota.style.width = '51px';
	SubItemQuota.style.zIndex = '-1';
	
	txtQuota.type = 'text';
	txtQuota.readOnly = true;
	txtQuota.name = 'txt-'+icount+'-quota';
	txtQuota.id = 'txt-'+icount+'-quota';
	txtQuota.value = iquota.toFixed(2);
	txtQuota.style.border = 'none';
	txtQuota.style.backgroundColor = 'transparent';
	txtQuota.style.width = 'inherit';
	txtQuota.style.textAlign = 'right';
	txtQuota.style.zIndex = '-1';
	
	SubItemQuota.appendChild(txtQuota);
	
	//Remove Button
	var SubItemRemove = document.createElement('div');
	var btnRemove = document.createElement('input');
	
	SubItemRemove.setAttribute('class','listviewsubitem');
	SubItemRemove.style.minWidth = '20px';
	SubItemRemove.style.zIndex = '-1';
	
	btnRemove.type = 'button';
	btnRemove.setAttribute('title','Remove');
	btnRemove.style.border = 'none';
	btnRemove.style.width = '16px';
	btnRemove.style.height = '16px';
	btnRemove.style.marginTop = '3px';
	btnRemove.style.backgroundColor = 'transparent';
	btnRemove.style.backgroundImage = 'url(img/icon/delete.png)';
	btnRemove.onclick = function(){
		removeelement(divId)
	};
	btnRemove.style.zIndex = '-1';
	
	SubItemRemove.appendChild(btnRemove);
	
	var hdnInventoryEmployee = document.createElement('input');
	
	hdnInventoryEmployee.type = 'hidden';
	hdnInventoryEmployee.name = 'hdn-'+icount+'-idIE';
	hdnInventoryEmployee.value = 0;
	
	ListViewItem.appendChild(SubItemName);
	ListViewItem.appendChild(SubItemAssignment);
	ListViewItem.appendChild(SubItemCash);
	ListViewItem.appendChild(SubItemInventory);
	ListViewItem.appendChild(SubItemQuota);
	ListViewItem.appendChild(SubItemRemove);
	ListViewItem.appendChild(hdnInventoryEmployee);
	
	document.getElementById('hdnCount').value = icount;
	document.getElementById('crewonduty').appendChild(ListViewItem);
	document.getElementById('txtEmployee').value = '';
	document.getElementById('hdnEmployee').value = 0;
	document.getElementById('cmbRole').selectedIndex = 0;
	document.getElementById('txtCash').value = '100.00';
	document.getElementById('txtInv').value = '100.00';
	document.getElementById('txtQuota').value = '100.00';
	document.getElementById('txtEmployee').focus();
}

function removeelement(divId){
	var res = confirm("Remove "+ document.getElementById('txt-'+divId.split('-')[1]+'-name').value +" from list. Are you sure?");
	if(res == true){
		var d = document.getElementById('crewonduty');
		var olddiv = document.getElementById(divId);
		d.removeChild(olddiv);
	}
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
				var top = getOffset(document.getElementById('txtEmployee')).top + 25;
				var left = getOffset(document.getElementById('txtEmployee')).left
				$('#lstresult').css('z-index','1');
				$('#lstresult').css('display','block');
				$('#lstresult').css('top',top);
				$('#lstresult').css('left',left);
				$('#lstresult').html(xmlhttp.responseText);
				$('#SearchItem-0').css('background-color','#DFD');
			}else{
				$('#lstresult').css('display','none');
				$('#lstresult').html('');
			}
		}
	}
	xmlhttp.open('POST','ajax/ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=SearchEmployee&search='+string);
}

function selectresult(string,value){
	$('#txtEmployee').val(string);
	$('#hdnEmployee').val(value);
	searchstring('');
	$('#cmbRole').focus();
}
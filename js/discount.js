// JavaScript Document
$(document).ready(function(e) {
	$('#txtsearchcard').keyup(function(e) {
		if(e.keyCode==13){
			$('#lvresult-0').click();
			return false;
		}else if(e.keyCode!=9){
			searchcard(this.value);
		}
	});
	$('#txtsearchlastname').keyup(function(e) {
		if(e.keyCode==13){
			$('#lvresult-0').click();
			return false;
		}else if(e.keyCode!=9){
			searchlastname(this.value);
		}
	});
	$('#txtsearchitem').keyup(function(e){
		if(e.keyCode==13){
			$('#lvresult-0').click();
			return false;
		}else if(e.keyCode!=9){
			searchitem(this.value);
		}		
	});
	$('#txtquantity').keyup(function(e){
		if(e.keyCode==13){
			$('#btnadditem').mouseup();
			return false;
		}else{
			var total=$('#txtquantity').val()*$('#txtprice').val();
			$('#txttotal').val(total.toFixed(2));
		}		
	});
	$('#txtprice').keyup(function(e) {
		if(e.keyCode==13){
			$('#btnadditem').mouseup();
			return false;
		}else{
			var total=$('#txtquantity').val()*$('#txtprice').val();
			$('#txttotal').val(total.toFixed(2));
		}		
    });
	/* ----- Discount Type Change ----- */
	var lastSel = $("#cmbtype option:selected");
	$("#cmbtype").change(function(){
        if($('#hdncount').val()>0){
			 
			if(confirm('Changing Discount Type will clear the existing Customer entries. Are you sure?')==true){
				$('#rowcustomer').html('');
				$('#hdncount').val(0);
			}else{
				lastSel.attr("selected", true);
			}
		}
		
	});
	
	$("#cmbtype").click(function(){
		lastSel = $("#cmbtype option:selected");
	});

});

function searchcard(string){
	var xmlhttp;
	var type = $('#cmbtype').val();
	if (string==''){
		$('#lstresult').css('display','none');
		$('#lstresult').html('');
		clearsearchcustomer();
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				var top = getOffset(document.getElementById('txtsearchcard')).top + 25;
				var left = getOffset(document.getElementById('txtsearchcard')).left
				$('#lstresult').css('display','block');
				$('#lstresult').css('top',top);
				$('#lstresult').css('left',left);
				$('#lstresult').css('z-index','9999');
				$('#lstresult').html(xmlhttp.responseText);
				$('#lvresult-0').css('background-color','#DFD');				
			}else{
				$('#lstresult').css('display','none');
				$('#lstresult').html('');
			}
		}
	}
	xmlhttp.open('POST','ajax/discount.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=searchcard&query='+string+'&type='+type);
}

function searchlastname(string){
	var xmlhttp;
	if (string==''){
		$('#lstresult').css('display','none');
		$('#lstresult').html('');
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				var top = getOffset(document.getElementById('txtsearchlastname')).top + 25;
				var left = getOffset(document.getElementById('txtsearchlastname')).left
				$('#lstresult').css('display','block');
				$('#lstresult').css('top',top);
				$('#lstresult').css('left',left);
				$('#lstresult').css('z-index','9999');
				$('#lstresult').html(xmlhttp.responseText);
				$('#lvresult-0').css('background-color','#DFD');				
			}else{
				$('#lstresult').css('display','none');
			}
		}
	}
	xmlhttp.open('POST','ajax/discount.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=searchlastname&query='+string);
}

function clearsearchcustomer(){
	$('#txtsearchcard').val('');
	$('#txtsearchlastname').val('');
	$('#txtsearchlastname').val('');
	$('#txtsearchfirstname').val('');
	$('#txtsearchmiddlename').val('');
	$('#txtsearchalias').val('');

}
function addcustomer(idcard,idcustomer,idcardtype,card,last,first,middle,alias){
	var count = ($('#hdncount').val()-1)+2;
	
	if(card == ''){
		msgbox('Failed to add entry, Supply ID Number','','');
		return false;
	}
	if(last == '' || first == ''){
		msgbox('Failed to add entry, Supply Last Name and First Name','','');
		return false;
	}
	if ($('#rowcustomer').children().length-1 > $('#txtpax').val()){
		msgbox('Failed to add entry, Pax should not be less than the customer entries','','');
		searchcard('');
		return false;
	}
	
	for(i=1;i<count;i++){
		if(document.getElementById('txtcard-'+i)!=undefined){
			if($('#txtcard-'+i).val()==card){
				msgbox('Failed to add entry, ID Number already exists on the list','','');
				searchcard('');
				return false;
			}
		}
	}
	
	var listviewitem = document.createElement('div');
	var subitemcard = document.createElement('div');
	subitemlastname = document.createElement('div');	
	subitemfirstname = document.createElement('div');	
	subitemmiddlename = document.createElement('div');	
	subitemalias = document.createElement('div');	
	subitemaction = document.createElement('div');
	btndelete = document.createElement('div');
	var txtcard = document.createElement('input');
	txtlastname = document.createElement('input');
	txtfirstname = document.createElement('input');
	txtmiddlename = document.createElement('input');
	txtalias = document.createElement('input');
	var hdnidcard = document.createElement('input');
	hdnidcustomer = document.createElement('input');
	hdnidcardtype = document.createElement('input');
	
	if(idcustomer==0){
		txtlastname.setAttribute('name','txtlastname-'+count);
		txtfirstname.setAttribute('name','txtfirstname-'+count);
		txtmiddlename.setAttribute('name','txtmiddlename-'+count);
		txtalias.setAttribute('name','txtalias-'+count);
	}
	if(idcard==0){
		txtcard.setAttribute('name','txtcard-'+count);
	}

	listviewitem.setAttribute('class','listviewitem');
	listviewitem.setAttribute('id','lvitem-'+count);

	subitemcard.setAttribute('class','listviewsubitem');
	subitemcard.style.width='150px';
	txtcard.setAttribute('id','txtcard-'+count);
	txtcard.setAttribute('value',card);
	txtcard.readOnly=true;
	txtcard.style.width='150px';
	txtcard.style.backgroundColor='Transparent';
	txtcard.style.border='None';
	subitemcard.appendChild(txtcard);

	subitemlastname.setAttribute('class','listviewsubitem');	
	subitemlastname.style.width='150px';
	txtlastname.style.textTransform='Uppercase';
	txtlastname.setAttribute('value',last);
	txtlastname.readOnly=true;
	txtlastname.style.width='150px';
	txtlastname.style.backgroundColor='Transparent';
	txtlastname.style.border='None';
	subitemlastname.appendChild(txtlastname);

	subitemfirstname.setAttribute('class','listviewsubitem');
	subitemfirstname.style.width='150px';
	txtfirstname.style.textTransform='Capitalize';
	txtfirstname.setAttribute('value',first);
	txtfirstname.readOnly=true;
	txtfirstname.style.width='150px';
	txtfirstname.style.backgroundColor='Transparent';
	txtfirstname.style.border='None';
	subitemfirstname.appendChild(txtfirstname);

	subitemmiddlename.setAttribute('class','listviewsubitem');
	subitemmiddlename.style.width='150px';
	txtmiddlename.style.textTransform='Capitalize';
	txtmiddlename.setAttribute('value',middle);
	txtmiddlename.readOnly=true;
	txtmiddlename.style.width='150px';
	txtmiddlename.style.backgroundColor='Transparent';
	txtmiddlename.style.border='None';
	subitemmiddlename.appendChild(txtmiddlename);

	subitemalias.setAttribute('class','listviewsubitem');
	subitemalias.style.width='150px';
	txtalias.style.textTransform='Capitalize';
	txtalias.setAttribute('value',alias);
	txtalias.readOnly=true;
	txtalias.style.width='150px';
	txtalias.style.backgroundColor='Transparent';
	txtalias.style.border='None';
	subitemalias.appendChild(txtalias);

	subitemaction.setAttribute('class','button16');
	subitemaction.style.width='150px';
	btndelete.setAttribute('class','button16');
	btndelete.style.backgroundImage='url(img/icon/delete.png)';
	btndelete.style.paddingTop='5px';
	btndelete.style.paddingLeft='0px';
	btndelete.onclick=function(){ 
		if(confirm('Remove [ ' + txtcard.value + ' ] Are you sure?') ){
			var d = document.getElementById('rowcustomer')
			var olddiv = listviewitem;		
			d.removeChild(olddiv);
		}
	}
	subitemaction.appendChild(btndelete);
	
	hdnidcard.type='hidden';
	hdnidcard.name='hdnidcard-'+count;
	hdnidcard.value=idcard;

	hdnidcustomer.type='hidden';
	hdnidcustomer.name='hdnidcustomer-'+count;
	hdnidcustomer.value=idcustomer;

	hdnidcardtype.type='hidden';
	hdnidcardtype.name='hdnidcardtype-'+count;
	hdnidcardtype.value=idcardtype;

	listviewitem.appendChild(subitemcard);
	listviewitem.appendChild(subitemlastname);	
	listviewitem.appendChild(subitemfirstname);	
	listviewitem.appendChild(subitemmiddlename);	
	listviewitem.appendChild(subitemalias);		
	listviewitem.appendChild(subitemaction);	
	listviewitem.appendChild(hdnidcard);	
	listviewitem.appendChild(hdnidcustomer);		
	listviewitem.appendChild(hdnidcardtype);		
	
	document.getElementById('rowcustomer').appendChild(listviewitem);
	$('#hdncount').val(count);
	
	clearsearchcustomer();
	searchcard('');
}

/* ----- item ----- */

function searchitem(string){

	var xmlhttp;
	var bid = $('#cmbbranch').val();
	if (string==''){
		$('#lstresult').css('display','none');
		$('#lstresult').html('');
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				var top = getOffset(document.getElementById('txtsearchitem')).top + 25;
				var left = getOffset(document.getElementById('txtsearchitem')).left
				$('#lstresult').css('display','block');
				$('#lstresult').css('top',top);
				$('#lstresult').css('left',left);
				$('#lstresult').html(xmlhttp.responseText);
				$('#lvresult-0').css('background-color','#DFD');				
			}else{
				$('#lstresult').css('display','none');
			}
		}
	}
	xmlhttp.open('POST','ajax/discount.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=searchitem&query='+string+'&bid='+bid);
}
function selectresult(id,name,price){
	$('#hdnitemadd').val(id);
	$('#txtsearchitem').val(name);
	$('#txtprice').val(price.toFixed(2));
	
	$('#txtquantity').focus();
	searchitem('');
}
function clearsearchitemdata(){
	$('#txtsearchitem').val('');
	$('#hdnitemadd').val('');
	$('#txtquantity').val('');
	$('#txtprice').val('0.00');
	$('#txttotal').val('0.00');
}
function additem(id,name,quantity,price,total){
	var itemcount =($('#hdnitemcount').val()-1)+2;
	
	if(id == '' || name == '' || quantity  == '' || price == '' || total == '' ){
		return false;
	}
	
	listviewitem = document.createElement('div');
	subitemname = document.createElement('div');
	subitemquantity = document.createElement('div');
	subitemprice = document.createElement('div');
	subitemtotal = document.createElement('div');
	subitemaction = document.createElement('div');
	txtitemname = document.createElement('input');
	txtitemquantity = document.createElement('input');
	txtitemprice = document.createElement('input');
	txtitemtotal = document.createElement('input');
	btnitemdelete = document.createElement('div');
	hdnitemid = document.createElement('input');
	
	listviewitem.setAttribute('class','listviewitem');
	listviewitem.setAttribute('id','lvitemdata-'+itemcount);
	
	subitemname.setAttribute('class','listviewsubitem');
	subitemname.style.width='300px';
	txtitemname.setAttribute('value',name);
	txtitemname.setAttribute('id','txtitemname-'+itemcount);
	txtitemname.readOnly=true;
	txtitemname.style.width='300px';
	txtitemname.style.backgroundColor='Transparent';
	txtitemname.style.border='None';
	subitemname.appendChild(txtitemname);
	
	subitemquantity.setAttribute('class','listviewsubitem');
	subitemquantity.style.width='150px';
	txtitemquantity.setAttribute('value',quantity);
	txtitemquantity.setAttribute('name','txtitemquantity-'+itemcount);
	txtitemquantity.readOnly=true;
	txtitemquantity.style.width='150px';
	txtitemquantity.style.backgroundColor='Transparent';
	txtitemquantity.style.border='None';
	txtitemquantity.style.textAlign='right';
	subitemquantity.appendChild(txtitemquantity);

	subitemprice.setAttribute('class','listviewsubitem');
	subitemprice.style.width='150px';
	txtitemprice.setAttribute('value',price);
	txtitemprice.setAttribute('name','txtitemprice-'+itemcount);
	txtitemprice.readOnly=true;
	txtitemprice.style.width='150px';
	txtitemprice.style.backgroundColor='Transparent';
	txtitemprice.style.border='None';
	txtitemprice.style.textAlign='right';
	subitemprice.appendChild(txtitemprice);

	subitemtotal.setAttribute('class','listviewsubitem');
	subitemtotal.style.width='150px';
	txtitemtotal.setAttribute('id','txtitemtotal-'+itemcount);
	txtitemtotal.setAttribute('value',total);
	txtitemtotal.readOnly=true;
	txtitemtotal.style.width='150px';
	txtitemtotal.style.backgroundColor='Transparent';
	txtitemtotal.style.border='None';
	txtitemtotal.style.textAlign='right';
	subitemtotal.appendChild(txtitemtotal);
	
	subitemaction.setAttribute('class','listviewsubitem');
	subitemaction.style.width='150px';
	btnitemdelete.setAttribute('class','button16');
	btnitemdelete.style.backgroundImage='url(img/icon/delete.png)';
	btnitemdelete.style.paddingTop='5px';
	btnitemdelete.style.paddingLeft='0px';
	btnitemdelete.onclick=function(){ 
		if(confirm('Remove [ ' + txtitemname.value + ' ] Are you sure?') ){
			var d = document.getElementById('rowitemdata');
			var olddiv = listviewitem;	
			d.removeChild(olddiv);
			sumtotal();
		}
	}
	subitemaction.appendChild(btnitemdelete);

	hdnitemid.setAttribute('type','hidden');
	hdnitemid.setAttribute('name','hdnitemid-'+itemcount);
	hdnitemid.setAttribute('value',id);
	
	listviewitem.appendChild(subitemname);
	listviewitem.appendChild(subitemquantity);
	listviewitem.appendChild(subitemprice);
	listviewitem.appendChild(subitemtotal);
	listviewitem.appendChild(subitemaction);
	listviewitem.appendChild(hdnitemid);
	
	document.getElementById('rowitemdata').appendChild(listviewitem);
	$('#hdnitemcount').val(itemcount);
		
	clearsearchitemdata();
	searchitem('');
	$('#txtsearchitem').focus();
	
	sumtotal();
}

function sumtotal(){
	var productmix = 0;
	var netofvat = 0;
	var discount = 0;
	var netsales = 0;
	var itemcount =($('#hdnitemcount').val()-1)+1;
	for(i=1;i<=itemcount;i++){
		if(document.getElementById('txtitemtotal-'+i)!=undefined){
			productmix += parseInt($('#txtitemtotal-'+i).val());
		}
	}
	
	netofvat = productmix / 1.12;
	discount = netofvat * .2;
	netsales = netofvat - discount;
	
	$('#divtotal').html(productmix.toFixed(2));
	$('#divnetofvat').html(netofvat.toFixed(2));
	$('#divdiscount').html(discount.toFixed(2));	
	$('#divnetsales').html(netsales.toFixed(2));		
}

// JavaScript Document
$(document).ready(function(e) {
	$('#txtSearch').keyup(function(e) {
		var bid = $('#hdnbid').val();
		if(e.keyCode==13){
			$('#lvresult-0').click();
			return false;
		}else{
			$('#cmbbranch').val();
			searchstring(bid,this.value);
		}
	});
	$('.editDateSC').dblclick(function(e){
		$(this).removeAttr('readonly');
		//alert("$(this).val()");
	});
	$('.editDateSC').focusout(function(e){
		$(this).attr('readonly','readonly');
		var scts = $(this).val();
		var bid = $(this).attr('id').split('-')[1];
		var scid = $(this).attr('id').split('-')[2];
		//alert(bid + ' - ' + scid + ' - ' + scts);
		$.post('ajax/invoice.ajax.php',
			{
				qid:'saveDateSC',
				scts: scts,
				bid:bid, 
				scid:scid,
			},
			function(data,status){
				//alert(data);
			});
	});
	$('#Pax').change(function(e) {
		if($(this).val().replace(/\s/g, '') == '' || $(this).val() <= 0){
			$(this).val(1)
		}
		if($(this).val()>=$('#hdndiscountadd').val()){
			getDiscountAmount();
			getTotalDiscount();
		}else{
			if(confirm('The number of pax you input is below the number of discounts you added. Reset discount list?') ){
				var discountcount =($('#hdndiscountcount').val()-1)+1;
				for(i=1;i<=discountcount;i++){
					var dlistparent = document.getElementById('rowdiscount');
					var dlistchild = document.getElementById('lvdiscountdata-'+i);
					dlistparent.removeChild(dlistchild);
				}
				$('#hdndiscountadd').val(0);
			}else{
				paxbelow=parseFloat($('#hdndiscountadd').val())-parseFloat($(this).val());
				$(this).val(parseFloat($(this).val())+paxbelow);
			}
		}
	});
});


function loadsalesday(unBranch){
	$.post('ajax/index.ajax.php',
		{
			qid:'loadsalesday',
			bid:unBranch,
		},
		function(data,status){
			$('#cmbSalesDay').empty();
			$('#cmbSalesDay').append(data);
		});
	
}

function viewinvoice(idSD,bid){
	//var title = document.getElementById('SDActionTitle').innerHTML;
	var xmlhttp;
	
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById('ViewSI').innerHTML=xmlhttp.responseText;
			//document.getElementById('ViewSD').innerHTML='';
			//document.getElementById('SITitle').innerHTML = title;
				
			location.href='#ViewSoldInvoice';
		}
	}
		
	xmlhttp.open('POST','ajax/invoice.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=ViewSoldInvoice&idSD='+idSD+'&bid='+bid);
		
}

function viewinvoicedata(idID,bid){
	elements = document.getElementsByClassName('listviewitem');
    	for (var i = 0; i < elements.length; i++) {
        elements[i].style.backgroundColor="transparent";
    }
	document.getElementById('listviewitem-'+idID).style.backgroundColor='#EEE';
	
	if(window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		}
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('ViewSID').innerHTML=xmlhttp.responseText;
				//document.getElementById('SITitle').innerHTML = title;
				
				location.href='#ViewSoldInvoice';
			}
		}
		
		xmlhttp.open('POST','ajax/invoice.ajax.php',true);
		xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xmlhttp.send('qid=ViewInvoiceData&idID='+idID+'&bid='+bid);
}

function resetinvoiceForm(){
	$('#rowinvoicedata .listviewitem').remove();
	$('#rowdiscount .listviewitem').remove();
	$('#rowpayment .listviewitem').remove();
	$('#Pax').val(1);
	$('#invoiceForm')[0].reset();
	getTotalSales();
	getTotalPayment();
}

function submitinvoiceForm(unsd,unsc){
	if($('#TPaid').val()==0.00){
		alert('Complete transaction before settling.');
	}else if($('#totalAmount').val()==0.00){
		alert('Complete transaction before settling.');
	}else if(unsd==0||unsc==0){
		alert('Select day & shift frist before encoding a transaction.');
	}else{
		$('#invoiceForm')[0].submit();
		}
}

function closeshift(bid,unsd,endtime,balend,unsc){
	if($('#isExist').val()==0){
		if($('#TPaid').val()!=0.00){
			alert('Settle pending transaction!');
		}else if($('#totalAmount').val()!=0.00){
			alert('Settle pending transaction!');
		}else if($('#TDiscount').val()!=0.00){
			alert('Settle pending transaction!');
		}else{
			window.location = "include/POS.inc.php?&bid="+bid+"&closeshift="+endtime+"&bal="+balend+"&idsd="+unsd+"&unsc="+unsc;
		}
	}else{
		window.location = "include/POS.inc.php?&bid="+bid+"&closeshift="+endtime+"&bal="+balend+"&idsd="+unsd+"&unsc="+unsc;
	}
}

function closeday(bid,unsc,endtime,emp,thissdstatus,sdstatus){
	if(thissdstatus==1){
		alert('The shift you are operating on is still open!');
	}else if(sdstatus!=0){
		alert('There are still open shifts under this day!');
	}else{
		if($('#TPaid').val()!=0.00){
			alert('Settle pending transaction!');
		}else if($('#totalAmount').val()!=0.00){
			alert('Settle pending transaction!');
		}else if($('#TDiscount').val()!=0.00){
			alert('Settle pending transaction!');
		}else{
			window.location = "include/POS.inc.php?&emp="+emp+"&bid="+bid+"&closeday="+endtime+"&idsc="+unsc;
		}
	}
}
	
function loadshift(bid,scid,stateSC){
	if(stateSC==0){
		$('.listviewitem').css('background-color','transparent');
		$('.selectedSC').empty();
		$('#shiftdata').empty();
		$(this).css('background-color','#B7E3F0');
		$('#selectedSC-'+scid).append(' - Day Closed!');	
	}else{
		$('.listviewitem').css('background-color','transparent');
		$('.selectedSC').empty();
		$('#listviewitemSC-'+scid).css('background-color','#B7E3F0');
		$('#selectedSC-'+scid).append(' - Day Selected');
		$('#hdnunSC').val(scid);	
		$.post('ajax/invoice.ajax.php',
			{
				qid:'LoadShift',
				bid:bid,
				scid:scid,
			},
			function(data,status){
				obj = JSON.stringify(data);
				$('#shiftdata').empty();
				$('#shiftdata').append(data);
			});
	}
}

function SEThdnunSD(unsd,stateSD){
	$('.listviewitem').css('background-color','transparent');
	$('.selectedSD').empty();
	$('#btnSelectShift').attr('disabled',false);
	if(stateSD==1){
		$('#hdnunSD').val(unsd);
		$('#listviewitemSD-'+unsd).css('background-color','#B7E3F0');
		$('#selectedSD-'+unsd).append(' - Shift Selected');
		$('#btnSelectShift').attr('disabled',false);
	}else if(stateSD==0){
		$('#listviewitemSD-'+unsd).css('background-color','#BBB');
		$('#selectedSD-'+unsd).append(' - Shift Closed!');
	}
}

function loadshiftINV(bid,scid){
		//$('.listviewitem').css('background-color','transparent');
		$('.selectedSCINV').empty();
		$('#listviewitemSCINV-'+scid).css('background-color','#B7E3F0');
		$('#selectedSCINV-'+scid).append(' - Day Selected');
		$('#hdnunSCINV').val(scid);	
		$.post('ajax/invoice.ajax.php',
			{
				qid:'LoadShiftINV',
				bid:bid,
				scid:scid,
			},
			function(data,status){
				obj = JSON.stringify(data);
				$('#shiftdataINV').empty();
				$('#shiftdataINV').append(data);
			});
}

function loadshiftINVEdit(bid,scid,invtryid){
		//$('.listviewitem').css('background-color','transparent');
		$('.selectedSCINVEdit').empty();
		$('#listviewitemSCINVEdit-'+scid).css('background-color','#B7E3F0');
		if(invtryid==0){
			$('#selectedSCINVEdit-'+scid).append(' - Day Selected');
			$('#hdnunSCINVEdit').val(scid);	
			$.post('ajax/invoice.ajax.php',
			{
				qid:'LoadShiftINVEdit',
				bid:bid,
				scid:scid,
			},
			function(data,status){
				obj = JSON.stringify(data);
				$('#shiftdataINVEdit').empty();
				$('#shiftdataINVEdit').append(data);
			});
		}else{
			$('#selectedSCINVEdit-'+scid).append(' - Currently mapped!');
		}
		
}


function SEThdnunSDINV(unsd,stateSD){
	//$('.listviewitem').css('background-color','transparent');
	$('.selectedSDINV').empty();
	$('#ViewSID').empty();
	var bid = $('#hdnunBIDINV').val();
	//$('#btnSelectShiftINV').attr('disabled',false);
		$('#hdnunSDINV').val(unsd);
		$('#listviewitemSDINV-'+unsd).css('background-color','#B7E3F0');
		$('#selectedSDINV-'+unsd).append(' - Shift Selected');	
		$('#btnSelectShiftINV').attr('disabled',false);
		$('#btnSelectShiftINV').attr('onClick','viewinvoice('+unsd+','+bid+')');
	//viewinvoice(+ document.getElementById('hdnunSDINV').value+,
}

function SEThdnunSDINVEdit(unsd,bid,invtryid){
	//$('.listviewitem').css('background-color','transparent');
	$('.selectedSDINVEdit').empty();
	//$('#ViewSID').empty();
	var bid = $('#hdnunBIDINVEdit').val();
	//$('#btnSelectShiftINV').attr('disabled',false);
		$('#listviewitemSDINVEdit-'+unsd).css('background-color','#B7E3F0');
		//$('#selectedSDINVEdit-'+unsd).append(' - Shift Selected');
		if(invtryid==0){
			$('#selectedSDINVEdit-'+unsd).append(' - Shift Selected');
			$('#hdnunSDINVEdit').val(unsd);
			$.post('ajax/invoice.ajax.php',
			{
				qid:'ViewEditInvoice',
				bid:bid,
				unsd:unsd,
			},
			function(data,status){
				obj = JSON.stringify(data);
				$('#INVEdit').empty();
				$('#INVEdit').append(data);
			});
		}else{
			$('#selectedSDINVEdit-'+unsd).append(' - Currently mapped!');
			$('#INVEdit').empty();
		}
	//viewinvoice(+ document.getElementById('hdnunSDINV').value+,
	
}

function SEThdnunINVEdit(uninv,bid){
	$('.selectedINVEdit').empty();
	$('#selectedINVEdit-'+uninv).append(' - Invoice Selected');
	$('#hdnunINVEdit').val(uninv);
	$('#btnSelectINVEdit').attr('disabled',false);
	$('#btnSelectINVEdit').attr('onClick','location.href="?&bid='+$('#hdnunBIDINVEdit').val()+'&unsd='+$('#hdnunSDINVEdit').val()+'&unsc='+$('#hdnunSCINVEdit').val()+'&uninv='+$('#hdnunINVEdit').val()+'"');
}


function selectresult(string,value,price){
	$('#lstresult').css('display','none');
	$('#txtSearch').val(string);
	$('#hdnSearchId').val(value);
	$('#hdnSearchPrice').val(price);
	searchstring('');
}

function searchstring(bid,string){
	var xmlhttp;
	if (string==0){
		$('#lstresult').css('display','none');
		$('#lstresult').height('');
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				var top = getOffset(document.getElementById('txtSearch')).top + 23;
				var left = getOffset(document.getElementById('txtSearch')).left
				$('#lstresult').css('display','block');
				$('#lstresult').css('top',top);
				$('#lstresult').css('left',left);
				$('#lstresult').html(xmlhttp.responseText);
				$('#lvitem-0').css('background-color','#DFD');
			}else{
				$('#lstresult').css('display','none');
			}
		}
	}
	xmlhttp.open('POST','ajax/invoice.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=searchstring&bid='+bid+'&query='+string);
}

function clearsearchitemdata(){
	$('#txtSearch').val('');
	$('#lstresult').css('display','none');	
}

function additem(id,name,price){
	var itemcount =($('#hdnitemcount').val()-1)+2;
	var tabindex = itemcount+100;
	
	if(id == '' || name == '' || price == ''){
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
	subitemname.style.width='280px';
	txtitemname.setAttribute('value',name);
	txtitemname.setAttribute('id','txtitemname-'+itemcount);
	txtitemname.setAttribute('name','txtitemname-'+itemcount);
	txtitemname.readOnly=true;
	txtitemname.style.width='100%';
	txtitemname.style.backgroundColor='Transparent';
	txtitemname.style.border='None';
	subitemname.appendChild(txtitemname);
	
	subitemquantity.setAttribute('class','listviewsubitem');
	subitemquantity.style.width='40px';
	txtitemquantity.setAttribute('type','number');
	txtitemquantity.setAttribute('value',1);
	txtitemquantity.setAttribute('min',1);
	txtitemquantity.setAttribute('name','txtitemquantity-'+itemcount);
	txtitemquantity.style.width='100%';
	txtitemquantity.style.backgroundColor='Transparent';
	txtitemquantity.style.border='None';
	txtitemquantity.style.textAlign='center';
	txtitemquantity.style.marginLeft='-10px';
	txtitemquantity.setAttribute('tabindex',tabindex)
	subitemquantity.appendChild(txtitemquantity);

	subitemprice.setAttribute('class','listviewsubitem');
	subitemprice.style.width='120px';
	txtitemprice.setAttribute('value',price);
	txtitemprice.setAttribute('type','number');
	txtitemprice.setAttribute('name','txtitemprice-'+itemcount);
	txtitemprice.readOnly=true;
	txtitemprice.style.width='100%';
	txtitemprice.style.backgroundColor='Transparent';
	txtitemprice.style.border='None';
	txtitemprice.style.textAlign='right';
	subitemprice.appendChild(txtitemprice);

	subitemtotal.setAttribute('class','listviewsubitem');
	subitemtotal.style.width='120px';
	txtitemtotal.setAttribute('id','txtitemtotal-'+itemcount);
	txtitemtotal.setAttribute('name','txtitemtotal-'+itemcount);
	txtitemtotal.setAttribute('value',price);
	txtitemtotal.readOnly=true;
	txtitemtotal.style.width='100%';
	txtitemtotal.style.backgroundColor='Transparent';
	txtitemtotal.style.border='None';
	txtitemtotal.style.textAlign='right';
	subitemtotal.appendChild(txtitemtotal);
	txtitemquantity.onchange=function(){
		if($(this).val().replace(/\s/g, '') == '' || $(this).val() <= 0){
			$(this).val(1)
		}
		var changenum=parseFloat($(this).val());
		var change=changenum.toFixed(2);
		var totalItemPrice=change * price;
		$('#txtitemtotal-'+itemcount).val(parseFloat(totalItemPrice).toFixed(2));
		getTotalSales();
	}
	
	subitemaction.setAttribute('class','listviewsubitem');
	subitemaction.style.width='50px';
	subitemaction.style.marginLeft='20px';
	btnitemdelete.setAttribute('class','button16');
	btnitemdelete.style.backgroundImage='url(img/icon/delete.png)';
	btnitemdelete.style.paddingTop='5px';
	btnitemdelete.style.paddingLeft='0px';
	btnitemdelete.onclick=function(){ 
		if(confirm('Remove [ ' + $('#txtitemname-'+itemcount).val() + ' ] Are you sure?') ){
			var d = document.getElementById('rowinvoicedata');
			var olddiv = document.getElementById('lvitemdata-'+itemcount);	
			d.removeChild(olddiv);
			getTotalSales();
		}
	}
	subitemaction.appendChild(btnitemdelete);

	hdnitemid.setAttribute('type','hidden');
	hdnitemid.setAttribute('name','hdnitemid-'+itemcount);
	hdnitemid.setAttribute('value',id);
	
	listviewitem.appendChild(subitemquantity);
	listviewitem.appendChild(subitemname);
	listviewitem.appendChild(subitemprice);
	listviewitem.appendChild(subitemtotal);
	listviewitem.appendChild(subitemaction);
	listviewitem.appendChild(hdnitemid);
	
	document.getElementById('rowinvoicedata').appendChild(listviewitem);
	$('#hdnitemcount').val(itemcount);
		
	clearsearchitemdata();
	getTotalSales();
}

function selectDiscount(discountid,dname,percent,deduct,vatexempt){
	$('#refNum').removeAttr('value');
	$('.discountSelected').css('background-image','');
	$('.listviewDC').css('background-color','');
	$('#listviewDC-'+discountid).css('background-color','#B7E3F0');
	$('#discountSelected-'+discountid).css('background-image','url(img/icon/check.png)');
	$('#DCselect').val(1);
	$('#refNum').keyup(function(){
		var refNum = $('#refNum').val();
		if($('#DCselect').val() == 1){
			if( refNum.replace(/\s/g, '') !== ''){
			$('#addDCount').attr("onClick", "adddiscount('"+discountid+"','"+dname+"','"+percent+"','"+deduct+"','"+refNum+"','"+vatexempt+"');location.href='#close'");
			}else{$('#addDCount').attr("onClick","")}
		}
	});
}

function closeDiscountPane(){
	$('.discountSelected').css('background-image','');
	$('.listviewDC').css('background-color','');
	$('#refNum').removeAttr('value');
	$('#addDCount').removeAttr('onClick');
	$('#DCselect').val(0);
}

function openDiscountPane(){
	closeDiscountPane();
}

function adddiscount(discountid,dname,percent,deduct,reference,vatexempt){
	var discountcount =($('#hdndiscountcount').val()-1)+2;
	var percentFlat = parseFloat(percent).toFixed(0);
	
	if(discountid == '' || dname == '' || percent == '' || deduct == ''){
		return false;
	}
	if($('#Pax').val() > $('#hdndiscountadd').val()){
	listviewdiscount = document.createElement('div');
	subdiscountname = document.createElement('div');
	subdiscountpercent = document.createElement('div');
	subdiscountamount = document.createElement('div');
	subdiscountaction = document.createElement('div');
	txtdiscountname = document.createElement('input');
	txtdiscountpercent = document.createElement('input');
	txtdiscountamount = document.createElement('input');
	btndiscountdelete = document.createElement('div');
	hdndiscountid = document.createElement('input');
	hdndiscountdeduct = document.createElement('input');
	hdndiscountvatexempt = document.createElement('input');
	hdndiscountvatex = document.createElement('input');
	hdndiscountvatexindp = document.createElement('input');
	hdndiscountvatexamount = document.createElement('input');
	hdndiscountvatamount = document.createElement('input');
	hdndiscountvatnet = document.createElement('input');
	hdndiscountvats = document.createElement('input');
	hdndiscountdue = document.createElement('input');
	hdndiscountreference = document.createElement('input');
	
	listviewdiscount.setAttribute('class','listviewitem');
	listviewdiscount.setAttribute('id','lvdiscountdata-'+discountcount);
	
	subdiscountname.setAttribute('class','listviewsubitem');
	subdiscountname.style.width='250px';
	txtdiscountname.setAttribute('value',dname);
	txtdiscountname.setAttribute('id','txtdiscountname-'+discountcount);
	txtdiscountname.setAttribute('name','txtdiscountname-'+discountcount);
	txtdiscountname.readOnly=true;
	txtdiscountname.style.width='100%';
	txtdiscountname.style.backgroundColor='Transparent';
	txtdiscountname.style.border='None';
	subdiscountname.appendChild(txtdiscountname);
	
	subdiscountpercent.setAttribute('class','listviewsubitem');
	subdiscountpercent.style.width='40px';
	txtdiscountpercent.setAttribute('type','text');
	txtdiscountpercent.setAttribute('value',percentFlat+'%');
	txtdiscountpercent.setAttribute('name','txtdiscountpercent-'+discountcount);
	txtdiscountpercent.readOnly=true;
	txtdiscountpercent.style.width='100%';
	txtdiscountpercent.style.backgroundColor='Transparent';
	txtdiscountpercent.style.border='None';
	txtdiscountpercent.style.textAlign='center';
	txtdiscountpercent.style.marginLeft='-10px';
	subdiscountpercent.appendChild(txtdiscountpercent);

	subdiscountamount.setAttribute('class','listviewsubitem');
	subdiscountamount.style.width='100px';
	txtdiscountamount.setAttribute('type','number');
	txtdiscountamount.setAttribute('name','txtdiscountamount-'+discountcount);
	txtdiscountamount.setAttribute('id','txtdiscountamount-'+discountcount);
	txtdiscountamount.readOnly=true;
	txtdiscountamount.style.width='100%';
	txtdiscountamount.style.backgroundColor='Transparent';
	txtdiscountamount.style.border='None';
	txtdiscountamount.style.textAlign='right';
	subdiscountamount.appendChild(txtdiscountamount);

	subdiscountaction.setAttribute('class','listviewsubitem');
	subdiscountaction.style.width='25px';
	subdiscountaction.style.marginLeft='10px';
	btndiscountdelete.setAttribute('class','button16');
	btndiscountdelete.style.backgroundImage='url(img/icon/delete.png)';
	btndiscountdelete.style.paddingTop='5px';
	btndiscountdelete.style.paddingLeft='0px';
	btndiscountdelete.onclick=function(){ 
		if(confirm('Remove [ ' + $('#txtdiscountname-'+discountcount).val() + ' ] Are you sure?') ){
			var d = document.getElementById('rowdiscount');
			var olddiv = document.getElementById('lvdiscountdata-'+discountcount);
			$('#hdndiscountadd').val(parseInt($('#hdndiscountadd').val())-1)	
			d.removeChild(olddiv);
			getTotalDiscount();
		}
	}
	subdiscountaction.appendChild(btndiscountdelete);

	hdndiscountid.setAttribute('type','hidden');
	hdndiscountid.setAttribute('name','hdndiscountid-'+discountcount);
	hdndiscountid.setAttribute('value',discountid);
	
	hdndiscountdeduct.setAttribute('type','hidden');
	hdndiscountdeduct.setAttribute('name','hdndiscountdeduct-'+discountcount);
	hdndiscountdeduct.setAttribute('id','hdndiscountdeduct-'+discountcount);
	hdndiscountdeduct.setAttribute('value',deduct);
	
	hdndiscountvatexempt.setAttribute('type','hidden');
	hdndiscountvatexempt.setAttribute('name','hdndiscountvatexempt-'+discountcount);
	hdndiscountvatexempt.setAttribute('id','hdndiscountvatexempt-'+discountcount);
	hdndiscountvatexempt.setAttribute('value',vatexempt);
	
	hdndiscountvatexamount.setAttribute('type','hidden');
	hdndiscountvatexamount.setAttribute('name','hdndiscountvatexamount-'+discountcount);
	hdndiscountvatexamount.setAttribute('id','hdndiscountvatexamount-'+discountcount);
	
	hdndiscountvatexindp.setAttribute('type','hidden');
	hdndiscountvatexindp.setAttribute('name','hdndiscountvatexindp-'+discountcount);
	hdndiscountvatexindp.setAttribute('id','hdndiscountvatexindp-'+discountcount);
	
	hdndiscountvatnet.setAttribute('type','hidden');
	hdndiscountvatnet.setAttribute('name','hdndiscountvatnet-'+discountcount);
	hdndiscountvatnet.setAttribute('id','hdndiscountvatnet-'+discountcount);
	
	hdndiscountvatamount.setAttribute('type','hidden');
	hdndiscountvatamount.setAttribute('name','hdndiscountvatamount-'+discountcount);
	hdndiscountvatamount.setAttribute('id','hdndiscountvatamount-'+discountcount);
	
	hdndiscountvatex.setAttribute('type','hidden');
	hdndiscountvatex.setAttribute('name','hdndiscountvatex-'+discountcount);
	hdndiscountvatex.setAttribute('id','hdndiscountvatex-'+discountcount);
	
	hdndiscountvats.setAttribute('type','hidden');
	hdndiscountvats.setAttribute('name','hdndiscountvats-'+discountcount);
	hdndiscountvats.setAttribute('id','hdndiscountvats-'+discountcount);
	
	hdndiscountdue.setAttribute('type','hidden');
	hdndiscountdue.setAttribute('name','hdndiscountdue-'+discountcount);
	hdndiscountdue.setAttribute('id','hdndiscountdue-'+discountcount);
	
	hdndiscountreference.setAttribute('type','hidden');
	hdndiscountreference.setAttribute('name','hdndiscountreference-'+discountcount);
	hdndiscountreference.setAttribute('id','hdndiscountreference-'+discountcount);
	hdndiscountreference.setAttribute('value',reference);
	
	listviewdiscount.appendChild(subdiscountpercent);
	listviewdiscount.appendChild(subdiscountname);
	listviewdiscount.appendChild(subdiscountamount);
	listviewdiscount.appendChild(subdiscountaction);
	listviewdiscount.appendChild(hdndiscountid);
	listviewdiscount.appendChild(hdndiscountdeduct);
	listviewdiscount.appendChild(hdndiscountvatexempt);
	listviewdiscount.appendChild(hdndiscountvatnet);
	listviewdiscount.appendChild(hdndiscountvatex);
	listviewdiscount.appendChild(hdndiscountvatexamount);
	listviewdiscount.appendChild(hdndiscountvatamount);
	listviewdiscount.appendChild(hdndiscountvatexindp);
	listviewdiscount.appendChild(hdndiscountvats);
	listviewdiscount.appendChild(hdndiscountdue);
	listviewdiscount.appendChild(hdndiscountreference);
	
	document.getElementById('rowdiscount').appendChild(listviewdiscount);
	$('#hdndiscountcount').val(discountcount);
	$('#hdndiscountadd').val(parseInt($('#hdndiscountadd').val())+1);
	
	getDiscountAmount();
	getTotalDiscount();
	}else { alert("One discount per pax only."); }
}

function getTotalSales(){
	var totalsales = 0.00;
	var itemcount =($('#hdnitemcount').val()-1)+1;
	for(i=1;i<=itemcount;i++){
		if(document.getElementById('txtitemtotal-'+i)!=undefined){
			totalsales += parseFloat($('#txtitemtotal-'+i).val());
		}
	}
	$('#totalAmount').val(totalsales.toFixed(2));
	getDiscountAmount();
	getTotalDiscount();
	getTotalPayment();
}

function getTotalDiscount(){
	var totaldiscount = 0.00;
	var discountcount =($('#hdndiscountcount').val()-1)+1;
	for(i=1;i<=discountcount;i++){
		if(document.getElementById('txtdiscountamount-'+i)!=undefined){
			totaldiscount += parseFloat($('#txtdiscountamount-'+i).val());
		}
	}
	$('#TDiscount').val(totaldiscount.toFixed(2));
	if($('#TDiscount').val()==0.00){
		$('#VATS').val((parseFloat($('#totalAmount').val())).toFixed(2));
		$('#NetVAT').val((parseFloat($('#totalAmount').val()/1.12)).toFixed(2));
		$('#TaxAmount').val((parseFloat($('#totalAmount').val())-(parseFloat($('#totalAmount').val()/1.12))).toFixed(2));
		$('#VATex').val((0.00).toFixed(2));
		$('#TDue').val(parseFloat($('#totalAmount').val()).toFixed(2));
	}else{
		var totalvatnet = 0.00;
		var totalvatex = 0.00;
		var totalvatexindp = 0.00;
		var totalvatexamount = 0.00;
		var totalvats = 0.00;
		var totalvatamount = 0.00;
		var totaldue = 0.00;
		var VATcount =($('#hdndiscountcount').val()-1)+1;
		var indpayment = (parseFloat($('#totalAmount').val())/parseFloat($('#Pax').val()));
		var wodiscountpax = $('#Pax').val()-$('#hdndiscountadd').val();
		for(i=1;i<=VATcount;i++){
			if(document.getElementById('hdndiscountvatnet-'+i)!=undefined){
				totalvatnet += parseFloat($('#hdndiscountvatnet-'+i).val());
			}
			if(document.getElementById('hdndiscountvatex-'+i)!=undefined){
				totalvatex += parseFloat($('#hdndiscountvatex-'+i).val());
			}
			if(document.getElementById('hdndiscountvatexindp-'+i)!=undefined){
				totalvatexindp += parseFloat($('#hdndiscountvatexindp-'+i).val());
			}
			if(document.getElementById('hdndiscountvats-'+i)!=undefined){
				totalvats += parseFloat($('#hdndiscountvats-'+i).val());
			}
			if(document.getElementById('hdndiscountvatexamount-'+i)!=undefined){
				totalvatexamount += parseFloat($('#hdndiscountvatexamount-'+i).val());
			}
			if(document.getElementById('hdndiscountvatamount-'+i)!=undefined){
				totalvatamount += parseFloat($('#hdndiscountvatamount-'+i).val());
			}
			if(document.getElementById('hdndiscountdue-'+i)!=undefined){
				totaldue += parseFloat($('#hdndiscountdue-'+i).val());
			}
		}
		$('#VATS').val((totalvats+(indpayment*wodiscountpax)).toFixed(2));
		$('#VATex').val(totalvatex.toFixed(2));
		$('#VATExIndP').val(totalvatexindp.toFixed(2));
		$('#VATExAmount').val(totalvatexamount.toFixed(2));
		$('#TaxAmount').val((((indpayment-(indpayment/1.12))*wodiscountpax)+totalvatamount).toFixed(2));
		$('#NetVAT').val((((indpayment/1.12)*wodiscountpax)+totalvatnet).toFixed(2));
		$('#TDue').val((totaldue+(indpayment*wodiscountpax)).toFixed(2));
	}
	getTotalPayment();
}

function getDiscountAmount(){
	var discountamount = 0.00;
	var indpayment = 0.00;
	var vats = 0.00;
	var vatex = 0.00;
	var vatnet = 0.00;
	var taxamount = 0.00;
	var vatexamount = 0.00;
	var discountcount =($('#hdndiscountcount').val()-1)+1;
	for(i=1;i<=discountcount;i++){
		if(document.getElementById('hdndiscountdeduct-'+i)!=undefined){
			indpayment = (parseFloat($('#totalAmount').val())/parseFloat($('#Pax').val()));
			if($('#hdndiscountvatexempt-'+i).val()==1){
				vats = 0.00;
				vatex = parseFloat(indpayment)/1.12;
				vatexindp = parseFloat(indpayment);
				vatexamount = parseFloat(indpayment)-(parseFloat(indpayment)/1.12);
				vatnet = 0.00;
				taxamount = 0.00;
				discountamount = parseFloat(vatex)*parseFloat($('#hdndiscountdeduct-'+i).val());
				$('#txtdiscountamount-'+i).val(parseFloat(discountamount).toFixed(2));
				$('#hdndiscountvats-'+i).val(parseFloat(vats).toFixed(2));
				$('#hdndiscountvatex-'+i).val(parseFloat(vatex).toFixed(2));
				$('#hdndiscountvatexindp-'+i).val(parseFloat(vatexindp).toFixed(2));
				$('#hdndiscountvatexamount-'+i).val(parseFloat(vatexamount).toFixed(2));
				$('#hdndiscountvatamount-'+i).val(parseFloat(taxamount).toFixed(2));
				$('#hdndiscountvatnet-'+i).val(parseFloat(vatnet).toFixed(2));
				$('#hdndiscountdue-'+i).val(parseFloat(vatex).toFixed(2)-parseFloat(discountamount).toFixed(2));
			}else if($('#hdndiscountvatexempt-'+i).val()==0){
				discountamount = parseFloat(indpayment)*parseFloat($('#hdndiscountdeduct-'+i).val());
				vats = parseFloat(indpayment);
				vatex = 0.00;
				vatexamount = 0.00;
				vatexindp = 0.00;
				taxamount = parseFloat(vats)-(parseFloat(vats)/1.12);
				vatnet = parseFloat(vats)/1.12;
				$('#txtdiscountamount-'+i).val(parseFloat(discountamount).toFixed(2));
				$('#hdndiscountvats-'+i).val(parseFloat(vats).toFixed(2));
				$('#hdndiscountvatex-'+i).val(parseFloat(vatex).toFixed(2));
				$('#hdndiscountvatexindp-'+i).val(parseFloat(vatexindp).toFixed(2));
				$('#hdndiscountvatexamount-'+i).val(parseFloat(vatexamount).toFixed(2));
				$('#hdndiscountvatamount-'+i).val(parseFloat(taxamount).toFixed(2));
				$('#hdndiscountvatnet-'+i).val(parseFloat(vatnet).toFixed(2));
				$('#hdndiscountdue-'+i).val(parseFloat(vats).toFixed(2)-parseFloat(discountamount).toFixed(2));
			}
		}
	}
}

function selectPayment(unPaymentType,PTName,PTFixedAmount,PTReference,PTPriority,PTCash){
	$('#PrefNum').attr("required",false);
	$('#PrefNum').slideUp(100);
	$('#PrefNum').removeAttr('value');
	if( PTReference == 1){$('#PrefNum').attr("required",true);$('#PrefNum').slideDown(100);
		$('#PrefNum').keyup(function(){
			var refNum = $('#PrefNum').val();
			if($('#Pselect').val() == 1){
				if( refNum.replace(/\s/g, '') !== ''){
				$('#addPCount').attr("onClick", "addpayment('"+unPaymentType+"','"+PTName+"','"+PTFixedAmount+"','"+refNum+"','"+PTPriority+"','"+PTCash+"');location.href='#close'");
				}else{$('#addPCount').attr("onClick","")}
			}
		});
	}else{$('#addPCount').attr("onClick", "addpayment('"+unPaymentType+"','"+PTName+"','"+PTFixedAmount+"','"+refNum+"','"+PTPriority+"','"+PTCash+"');location.href='#close'");}
	$('.paymentSelected').css('background-image','');
	$('.listviewP').css('background-color','');
	$('#listviewP-'+unPaymentType).css('background-color','#B7E3F0');
	$('#paymentSelected-'+unPaymentType).css('background-image','url(img/icon/check.png)');
	$('#Pselect').val(1);
}

function closePaymentPane(){
	$('#PrefNum').slideUp(100);
	$('.paymentSelected').css('background-image','');
	$('.listviewP').css('background-color','');
	$('#PrefNum').removeAttr('value');
	$('#addPCount').removeAttr('onClick');
	$('#Pselect').val(0);
}

function openPaymentPane(){
	closePaymentPane();
}

function addpayment(unPaymentType,PTName,PTFixedAmount,PTReference,PTPriority,PTCash){
	var paymentcount =($('#hdnpaymentcount').val()-1)+2;
	
	if(unPaymentType == '' || PTName == '' || PTFixedAmount == '' || PTPriority == ''){
		return false;
	}
	listviewpayment = document.createElement('div');
	subpaymentname = document.createElement('div');
	subpaymentamount = document.createElement('div');
	subpaymentaction = document.createElement('div');
	txtpaymentname = document.createElement('input');
	txtpaymentamount = document.createElement('input');
	btnpaymentdelete = document.createElement('div');
	hdnpaymentid = document.createElement('input');
	hdnpaymentcash = document.createElement('input');
	hdnpaymentpriority = document.createElement('input');
	hdnpaymentreference = document.createElement('input');
	
	listviewpayment.setAttribute('class','listviewitem');
	listviewpayment.setAttribute('id','lvpaymentdata-'+paymentcount);
	
	subpaymentname.setAttribute('class','listviewsubitem');
	subpaymentname.style.width='300px';
	txtpaymentname.setAttribute('value',PTName);
	txtpaymentname.setAttribute('id','txtpaymentname-'+paymentcount);
	txtpaymentname.setAttribute('name','txtpaymentname-'+paymentcount);
	txtpaymentname.readOnly=true;
	txtpaymentname.style.width='100%';
	txtpaymentname.style.backgroundColor='Transparent';
	txtpaymentname.style.border='None';
	subpaymentname.appendChild(txtpaymentname);

	subpaymentamount.setAttribute('class','listviewsubitem');
	subpaymentamount.style.width='100px';
	txtpaymentamount.setAttribute('value',PTFixedAmount);
	txtpaymentamount.setAttribute('type','number');
	txtpaymentamount.setAttribute('min',1);
	if(PTFixedAmount!='0.00'){
		txtpaymentamount.readOnly=true;
	}
	txtpaymentamount.setAttribute('name','txtpaymentamount-'+paymentcount);
	txtpaymentamount.setAttribute('id','txtpaymentamount-'+paymentcount);
	txtpaymentamount.setAttribute('class','txtpaymentamount');
	txtpaymentamount.style.width='100%';
	txtpaymentamount.style.backgroundColor='Transparent';
	txtpaymentamount.style.border='None';
	txtpaymentamount.style.textAlign='right';
	subpaymentamount.appendChild(txtpaymentamount);
	txtpaymentamount.onchange=function(){
		if($(this).val().replace(/\s/g, '') == '' || $(this).val() <= 0){
			$(this).val(1)
		}
		getTotalPayment();
	}

	subpaymentaction.setAttribute('class','listviewsubitem');
	subpaymentaction.style.width='25px';
	subpaymentaction.style.marginLeft='10px';
	btnpaymentdelete.setAttribute('class','button16');
	btnpaymentdelete.style.backgroundImage='url(img/icon/delete.png)';
	btnpaymentdelete.style.paddingTop='5px';
	btnpaymentdelete.style.paddingLeft='0px';
	btnpaymentdelete.onclick=function(){ 
		if(confirm('Remove [ ' + $('#txtpaymentname-'+paymentcount).val() + ' ] Are you sure?') ){
			var d = document.getElementById('rowpayment');
			var olddiv = document.getElementById('lvpaymentdata-'+paymentcount);
			$('#hdndiscountadd').val(parseInt($('#hdnpaymentadd').val())-1)	
			d.removeChild(olddiv);
			getTotalPayment();
		}
	}
	subpaymentaction.appendChild(btnpaymentdelete);

	hdnpaymentid.setAttribute('type','hidden');
	hdnpaymentid.setAttribute('name','hdnpaymentid-'+paymentcount);
	hdnpaymentid.setAttribute('value',unPaymentType);
	
	hdnpaymentcash.setAttribute('type','hidden');
	hdnpaymentcash.setAttribute('name','hdnpaymentcash-'+paymentcount);
	hdnpaymentcash.setAttribute('id','hdnpaymentcash-'+paymentcount);
	hdnpaymentcash.setAttribute('value',PTCash);
	
	hdnpaymentpriority.setAttribute('type','hidden');
	hdnpaymentpriority.setAttribute('name','hdnpaymentpriority-'+paymentcount);
	hdnpaymentpriority.setAttribute('id','hdnpaymentpriority-'+paymentcount);
	hdnpaymentpriority.setAttribute('value',PTPriority);
	
	hdnpaymentreference.setAttribute('type','hidden');
	hdnpaymentreference.setAttribute('name','hdnpaymentreference-'+paymentcount);
	hdnpaymentreference.setAttribute('id','hdnpaymentreference-'+paymentcount);
	hdnpaymentreference.setAttribute('value',PTReference);
	
	listviewpayment.appendChild(subpaymentname);
	listviewpayment.appendChild(subpaymentamount);
	listviewpayment.appendChild(subpaymentaction);
	listviewpayment.appendChild(hdnpaymentid);
	listviewpayment.appendChild(hdnpaymentcash);
	listviewpayment.appendChild(hdnpaymentpriority);
	listviewpayment.appendChild(hdnpaymentreference);
	
	document.getElementById('rowpayment').appendChild(listviewpayment);
	$('#hdnpaymentcount').val(paymentcount);
	$('#hdnpaymentadd').val(parseInt($('#hdndiscountadd').val())+1);
		
	getTotalPayment();
}

function getTotalPayment(){
	var totalpayment = 0.00;
	var totalcash = 0.00;
	var paymentcount =($('#hdnpaymentcount').val()-1)+1;
	for(i=1;i<=paymentcount;i++){
		if(document.getElementById('txtpaymentamount-'+i)!=undefined){
			totalpayment += parseFloat($('#txtpaymentamount-'+i).val());
		}
	}
	for(i=1;i<=paymentcount;i++){
		if(document.getElementById('txtpaymentamount-'+i)!=undefined){
			PTCash = $('#hdnpaymentcash-'+i).val();
			if(PTCash==1){
				totalcash += parseFloat($('#txtpaymentamount-'+i).val());
				$('#hdnpaymentreference-'+i).val('');
			}
		}
	}
	$('#TPCash').val(totalcash.toFixed(2));
	$('#TPOthers').val((totalpayment-totalcash).toFixed(2));
	$('#TPaid').val(totalpayment.toFixed(2));
	$('#Change').val((parseFloat(totalpayment)-parseFloat($('#TDue').val())).toFixed(2))
}

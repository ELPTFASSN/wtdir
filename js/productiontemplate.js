// JavaScript Document
$(document).ready(function(e) {
	$('#txtproductitemsearch').keyup(function(e) {
		if(e.keyCode==13){
			$('.prodselected').click();
			return false;
		}else if(e.which==38){
			listnum = $('.listboxitem').length;
			iddel = $('.prodselected').attr('id');
			idnum = iddel.match(/\d+/);
			nextnum = Math.max(0, parseInt(idnum) - 1);
			$('#lstitemresult-'+idnum).removeClass('prodselected');
			$('#lstitemresult-'+idnum).css('background-color','');
			$('#lstitemresult-'+nextnum).addClass('prodselected');
			$('#lstitemresult-'+nextnum).css('background-color','#DFD');
			//alert(listnum);
		}else if(e.which==40){
			listnum = $('.listboxitem').length;
			iddel = $('.prodselected').attr('id');
			idnum = iddel.match(/\d+/);
			if((parseInt(idnum)+1)<listnum){
				nextnum = parseInt(idnum) + 1;
			}else if((parseInt(idnum)+1)==listnum){
				nextnum = parseInt(idnum);
			}
			$('#lstitemresult-'+idnum).removeClass('prodselected');
			$('#lstitemresult-'+idnum).css('background-color','');
			$('#lstitemresult-'+nextnum).addClass('prodselected');
			$('#lstitemresult-'+nextnum).css('background-color','#DFD');
			//alert(listnum);
		}else{
			searchstring(this.value,$('#cmbproducttype').val(),$('#unTICi').val());
			//alert($('#unTICi').val())
			//alert($('#cmbproducttype').val());
		}
		loaduom(this.value);
	});
	/*$('#txtproductitemsearch').blur(function(e) {
		alert(this.value)
	});*/
	$('#cmbunit').keyup(function(e) {
		if(e.keyCode==13){
			$('#txtcost').focus();
			return false;
		}
		
	});
	$('#txtcost').keyup(function(e) {
		if(e.keyCode==13){
			$('#txtquantity').focus();
			return false;
		}else{
			var total=$('#txtquantity').val()*$('#txtcost').val();
			$('#txtamount').val(total.toFixed(10));
		}		
    });
	$('#txtquantity').keyup(function(e){
		if(e.keyCode==13){			
			$('#cmbprocesstype').focus();			
			return false;
		}else{
			var total=$('#txtquantity').val()*$('#txtcost').val();
			$('#txtamount').val(total.toFixed(10));			
		}		
	});
	$('#cmbprocesstype').keyup(function(e){
		if(e.keyCode==13){
			$('#btnadditem').click();
			$('#txtproductitemsearch').focus();
			return false;	
		}else{
			var total=$('#txtquantity').val()*$('#txtcost').val();
			$('#txtamount').val(total.toFixed(10));			
		}	
	});

});

function searchstring(string,ptype,unTICi){
	if(ptype==1){
		var ptypesearch=2;
	}else{
		var ptypesearch=1;
	}
	//alert(ptypesearch);
	var xmlhttp;
	if (string==''){
		$('#lstresult').css('display','none');
		$('#lstresult').height('');
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	var currrowcount=parseFloat($('#lvproductiondata #hdncount').val())+1;
	//var currproddata = [];
	var currproddata = ""; 
	var i = 0;
	/*for (i = 1; i != currrowcount; i++){
		currproddata[i] = $('#lvproductiondata #txtname-'+i).val();;
		//currproddata.push(i)
		//alert($('#lvproductiondata #txtname-'+i).val());
	}*/
	if(currrowcount>1){
		for (i = 1; i != currrowcount; i++){
			if($('#lvproductiondata #txtname-'+i).attr('id')=='txtname-1'){
				var lvprodname= "'"+$('#lvproductiondata #txtname-'+i).val()+"'";
				currproddata += lvprodname.replace(/&/g , " ");
				//alert(i);
			}else{
				var lvprodname= ",'"+$('#lvproductiondata #txtname-'+i).val()+"'";
				currproddata += lvprodname.replace(/&/g , " ");
				//alert(i);
			}
		}
	}
	//alert(currproddata);
	//alert(currproddata.length)
	//alert(currrowcount);
	//alert(currproddata.join("\n"));
	//currproddata.replace(/&/g , " ");
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				var top = getOffset(document.getElementById('txtproductitemsearch')).top + 25;
				var left = getOffset(document.getElementById('txtproductitemsearch')).left;
				$('#lstresult').css('display','block');
				$('#lstresult').css('top',top);
				$('#lstresult').css('left',left);
				$('#lstresult').html(xmlhttp.responseText);
				$('#lstitemresult-0').css('background-color','#DFD');
				$('#lstitemresult-0').addClass('prodselected');
			}else{
				$('#lstresult').css('display','none');
			}
		}
	}
	xmlhttp.open('POST','ajax/productiontemplate.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	if(currrowcount>1){
		xmlhttp.send('qid=searchitem&query='+string+'&ptype='+ptypesearch+'&currproddata='+currproddata+'&unTICi='+unTICi);
	}else{
		xmlhttp.send('qid=searchitem&query='+string+'&ptype='+ptypesearch+'&unTICi='+unTICi);
	}
}

function selectresult(string,value){
	$('#txtproductitemsearch').val(string);
	$('#hdnidproductitem').val(value);
	loaduom(value,$('#unTICi').val());
	$('#cmbunit').focus();	
	searchstring('');
}

function loaduom(pid,unTICi){
	var xmlhttp;
	if (pid==''){
		return;
	}
	if(window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText!=''){
				$('#cmbunit').html(xmlhttp.responseText);
			}
		}
	}
	//alert(pid);
	xmlhttp.open('POST','ajax/productiontemplate.ajax.php',true);
	xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
	xmlhttp.send('qid=loaduom&pid='+pid);
}

function clearentry(){
	$('#txtproductitemsearch').val('');
	$('#cmbunit').val();
	$('#txtcost').val('');
	$('#txtquantity').val('');
	$('#txtamount').val('');
}

function additem(idproductitem,idproductuom,productname,unitname,cost,quantity,amount,idprocesstype, processtype){
//	document.writeln('idproductitem:'+idproductitem + ' idproductuom:'+ idproductuom + ' productname: ' + productname + 
//					 ' unitname:' + unitname + ' cost:' + cost + ' quantity:' + quantity + ' amount:' + amount + 
//					 ' idprocesstype:' + idprocesstype + ' processtype:' + processtype);
	var count = ($('#hdncount').val()-1)+2;
		
	if (idproductitem=='',idproductuom=='',productname=='',unitname=='',cost=='',quantity=='',amount=='',idprocesstype=='',processtype==''){
		msgbox('Add failed, complete the fields','','');
		return false;
	}
	
	var lvitem = document.createElement('div');
	var lvsubitemname = document.createElement('div');
	var lvsubitemunit = document.createElement('div');
	var lvsubitemcost = document.createElement('div');
	var lvsubitemquantity = document.createElement('div');
	var lvsubitemamount = document.createElement('div');
	var lvsubitemprocesstype = document.createElement('div');
	
	var txtname = document.createElement('input');
	var txtunit = document.createElement('input');
	var txtcost = document.createElement('input');
	var txtquantity = document.createElement('input');
	var txtamount = document.createElement('input');
	var txtidproductname = document.createElement('input');
	var txtidproductuom = document.createElement('input');	
	var txtidprocesstype = document.createElement('input');
	var txtprocesstype = document.createElement('input');
	var btndelete = document.createElement('div');
	
	lvitem.setAttribute('class','listviewitem');
	lvitem.setAttribute('id','lvitem-'+count);
	
	lvsubitemname.setAttribute('class','listviewsubitem');
	lvsubitemunit.setAttribute('class','listviewsubitem');
	lvsubitemcost.setAttribute('class','listviewsubitem');
	lvsubitemquantity.setAttribute('class','listviewsubitem');
	lvsubitemamount.setAttribute('class','listviewsubitem');
	lvsubitemprocesstype.setAttribute('class','listviewsubitem');
	
	lvsubitemname.style.width='200px';
	lvsubitemunit.style.width='51px';
	lvsubitemcost.style.width='82px';
	lvsubitemquantity.style.width='82px';
	lvsubitemamount.style.width='82px';
	lvsubitemprocesstype.style.width='80px';

	txtname.type='input';
	txtname.value=productname;
	txtname.readOnly=true;
	txtname.style.border='none';
	txtname.style.backgroundColor='transparent';
	txtname.style.width='inherit';
	txtname.setAttribute('id','txtname-'+count);

	txtunit.type='input';
	txtunit.value=unitname;
	txtunit.readOnly=true;
	txtunit.style.border='none';
	txtunit.style.backgroundColor='transparent';
	txtunit.style.width='inherit';

	txtcost.type='input';
	txtcost.name='txtcost-'+count;
	txtcost.value=parseFloat(cost).toFixed(10);
	txtcost.readOnly=true;
	txtcost.style.border='none';
	txtcost.style.backgroundColor='transparent';
	txtcost.style.width='inherit';
	txtcost.style.textAlign='right';
	
	txtquantity.type='input';
	txtquantity.name='txtquantity-'+count;
	txtquantity.value=parseFloat(quantity).toFixed(10);
	txtquantity.readOnly=true;
	txtquantity.style.border='none';
	txtquantity.style.backgroundColor='transparent';
	txtquantity.style.width='inherit';
	txtquantity.style.textAlign='right';

	txtamount.type='input';
	txtamount.name='txtamount-'+count;
	txtamount.value=parseFloat(amount).toFixed(10);
	txtamount.readOnly=true;
	txtamount.style.border='none';
	txtamount.style.backgroundColor='transparent';
	txtamount.style.width='inherit';
	txtamount.style.textAlign='right';
	
	txtprocesstype.type='input';
	txtprocesstype.name='txtprocesstype-'+count;
	txtprocesstype.value= processtype;//(idprocesstype===0?'Sales':'Production');
	txtprocesstype.readOnly=true;
	txtprocesstype.style.border='none';
	txtprocesstype.style.backgroundColor='transparent';
	txtprocesstype.style.width='inherit';
	txtprocesstype.style.textAlign='right';
		
	txtidproductname.type='hidden';
	txtidproductname.name='hdnproduct-'+count;
	txtidproductname.value=idproductitem;
	txtidproductname.style.width='inherit';
	
	txtidproductuom.type='hidden';
	txtidproductuom.name='hdnunit-'+count;
	txtidproductuom.value=idproductuom;
	txtidproductuom.style.width='inherit';
	
	txtidprocesstype.type='hidden';
	txtidprocesstype.name='hdnprocesstype-'+count;
	txtidprocesstype.value=idprocesstype;
	txtidprocesstype.style.width='inherit';
	
	btndelete.setAttribute('class','button16');
	btndelete.style.backgroundImage='url(img/icon/delete.png)';
	btndelete.style.paddingTop='5px';
	btndelete.style.paddingLeft='0px';
	btndelete.onclick=function(){ 
		if(confirm('Remove [ ' + txtname.value + ' ] Are you sure?') ){
			var d = document.getElementById('rowproductiondata')
			var olddiv = lvitem;		
			d.removeChild(olddiv);
		}
	}

	lvsubitemname.appendChild(txtname);
	lvsubitemunit.appendChild(txtunit);
	lvsubitemcost.appendChild(txtcost);
	lvsubitemquantity.appendChild(txtquantity);
	lvsubitemamount.appendChild(txtamount);
	lvsubitemprocesstype.appendChild(txtprocesstype);

	lvitem.appendChild(lvsubitemname);
	lvitem.appendChild(lvsubitemunit);
	lvitem.appendChild(lvsubitemcost);
	lvitem.appendChild(lvsubitemquantity);
	lvitem.appendChild(lvsubitemamount);
	lvitem.appendChild(lvsubitemprocesstype);

	lvitem.appendChild(txtidproductname);
	lvitem.appendChild(txtidproductuom);
	lvitem.appendChild(txtidprocesstype);

	lvitem.appendChild(btndelete);

	document.getElementById('rowproductiondata').appendChild(lvitem);
	$('#hdncount').val(count);
	clearentry();
}
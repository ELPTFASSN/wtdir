// JavaScript Document
function addlocation(type,value,level){
	if(value==''){
		$('#bar'+type).html('<div class="addressbarhead"><img src="img/icon/inventory.png" width="16" height="16" style="padding-right:10px;"></div><div class="addressbarbutton">Branch</div><div class="divider"></div>');
		return;
	}	

	for (i=2;i>=level;i--){
		$('#addr'+type+i).remove();
		$('#div'+type+i).remove();
	}

	$('#bar'+type).append('<div class="addressbarbutton" id="addr'+type+level+'">'+value+'</div><div id="div'+type+level+'" class="divider"></div>');
}
 
function loadbranch(type,idarea){
	$.post('ajax/index.ajax.php',
	{
		qid:'loadbranch',
		typ:type,
		aid:idarea
	},
	function(data,status){
		$('#col'+type).html('<div class="columnheader" style="width:160px;">'+type+' Branch</div>');
		$('#row'+type).html(data);
		addlocation(type,'',0);
	});
}

function loadperiod(type,idbranch,branchname){
	$.post('ajax/index.ajax.php',
	{
		qid:'loadperiod',
		typ:type,
		bid:idbranch,
		brn:branchname
	},
	function(data,status){
		$('#col'+type).html('<div class="columnheader" style="width:160px;">'+type+' Period</div>');
		$('#row'+type).html(data);
		//dellocation(type);
		addlocation(type,branchname,1);
	});
}

function loadinventory(type,idbranch,year,month,period,branchname){
	$.post('ajax/index.ajax.php',
	{
		qid:'loadinventory',
		typ:type,
		bid:idbranch,
		yr:year,
		mon:month,
		per:period,
		brn:branchname 
	},
	function(data,status){
		$('#colinventory').html('<div class="columnheader" style="width:160px;">Inv. Number</div><div class="columnheader" style="width:120px;">Sheet Number</div><div class="columnheader" style="width:180px;">Remarks</div><div class="columnheader" style="width:120px;">Date</div>');
		$('#rowinventory').html(data);
		addlocation(type,period,2);
	});
}

function loaddelivery(type,idbranch,year,month){
	$.post('ajax/index.ajax.php',
	{
		qid:'loaddelivery',
		typ:type,
		bid:idbranch,
		yr:year,
		mon:month
	},
	function(data,status){
		$('#coldelivery').html('<div class="columnheader" style="width:140px;">Doc Number</div><div class="columnheader" style="width:140px;">Sheet Number</div><div class="columnheader" style="width:140px;">Date</div>');
		$('#rowdelivery').html(data);
	});
}

function loaddamage(type,idbranch,year,month){
	$.post('ajax/index.ajax.php',
	{
		qid:'loaddamage',
		typ:type,
		bid:idbranch,
		yr:year,
		mon:month
	},
	function(data,status){
		$('#coldamage').html('<div class="columnheader" style="width:140px;">Doc Number</div><div class="columnheader" style="width:140px;">Sheet Number</div><div class="columnheader" style="width:140px;">Date</div>');
		$('#rowdamage').html(data);
	});
}


function loadtransfer(type,idbranch,year,month){
	$.post('ajax/index.ajax.php',
	{
		qid:'loadtransfer',
		typ:type,
		bid:idbranch,
		yr:year,
		mon:month
	},
	function(data,status){
		$('#coltransfer').html('<div class="columnheader" style="width:140px;">ITF Number</div><div class="columnheader" style="width:140px;">Branch From</div><div class="columnheader" style="width:140px;">Branch To</div><div class="columnheader" style="width:140px;">Date</div>');
		$('#rowtransfer').html(data);
	});
}

function loadsheet(idbranch,qid){
	if (idbranch==0){return;}
	$.post('ajax/ajax.php',
	{
		qid:qid,
		bid:idbranch
	},
	function(data,status){
		if(qid=='openinventory'){
			$('#rowinventory').html(data);
		}else if(qid=='openitf'){
			$('#rowtransfer').html(data);
		}
	});
}

$(document).ready(function(e) {
   $('#containerinventorysheet').css('display','block');

	var h = $('#lvinventory').height()-$('#colinventory').height();
   $('#rowinventory').height(h);

	var h = $('#lvdelivery').height()-$('#coldelivery').height();
   $('#rowdelivery').height(h);

	var h = $('#lvtransfer').height()-$('#coltransfer').height();
   $('#rowtransfer').height(h);
});

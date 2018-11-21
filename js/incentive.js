// JavaScript Document
$(document).ready(function(e) {
	$(window).scroll(function(e) {
        $('#maindiv').remove();
		$('#shwList-'+$('#hdnSelected').val()).html('►');
    });
});

function ShowEmployeeList(idQuota,idBranch,QDate,QQuotaTotalAmount){
	if($('#hdnSelected').val()==idQuota){
		if($('#shwList-'+idQuota).html()=='▼'){
			$('#shwList-'+idQuota).html('►');
			$('#maindiv').remove();
			return;	
		}
	}else{
		if($('#hdnSelected').val()!=0){
			$('#shwList-'+$('#hdnSelected').val()).html('►');
		}
	}
	
	$('#maindiv').remove();
	
	var maindiv = document.createElement('div');
	var top = getOffset(document.getElementById('shwList-'+idQuota)).top + 20;
	var left = getOffset(document.getElementById('shwList-'+idQuota)).left + $('#shwList-'+idQuota).width() + 5;
	
	maindiv.id = 'maindiv';
	maindiv.style.position = 'fixed';
	$(maindiv).css('top',top);
	$(maindiv).css('left',left);
	maindiv.style.width= '530px'
	maindiv.style.height = 'auto';
	maindiv.style.borderRadius = '5px';
	maindiv.style.backgroundColor = '#FFF';
	maindiv.style.backgroundImage = 'linear-gradient(rgb(255,255,255) 0%,rgb(238,238,238) 100%)';
	maindiv.style.border = 'thin solid #666';
	maindiv.style.boxShadow = '5px 5px 5px rgba(0,0,0,0.3)';
	maindiv.style.color = '#666';
		
	document.getElementById('lvSalesList').appendChild(maindiv);
	
	$.post('ajax/ajax.php',
	{
		qid:'ShowEmployeeList',
		bid:idBranch,
		qdate:QDate,
		qquota:QQuotaTotalAmount
	},function(data,status){
		$('#maindiv').html(data);
		$('#shwList-'+idQuota).html('▼');
		$('#hdnSelected').val(idQuota);
	});
}

function EditQuota(Title,idQuota){
	$('#editquotatitle').html(Title);
	$('#hdnidQuota').val(idQuota);
	location.href='#editquota';
	$.post('ajax/ajax.php',
	{
		qid:'EditQuota',
		idQuota:idQuota
	},function(data,status){
		$('#txtMonthlyQuota').val(data.split('©')[0]);
		$('#txtInterval').val(data.split('©')[1]);
		$('#txtPointAmount').val(data.split('©')[2]);
	});
}

function SaveDailyQuota(idQuota,quota,quotainterval,quotapoint,surl){
	$.post('ajax/ajax.php',
	{
		qid:'SaveDailyQuota',
		idquota:idQuota,
		quota:quota,
		quotainterval:quotainterval,
		quotapoint:quotapoint
	},function(data,status){
		window.location = surl.split('#')[0];
	});
}

function SaveIncentives(idBranch,FirstDate,Quota,QuotaInterval,QuotaPointAmount,surl){
	/*alert(idBranch + ' - ' + FirstDate + ' - ' + Quota + ' - ' + QuotaInterval + ' - ' + QuotaPointAmount);
	return false;*/
	$.post('ajax/ajax.php',
	{
		qid:'SaveIncentives',
		idbranch:idBranch,
		fdate:FirstDate,
		quota:Quota,
		quotainterval:QuotaInterval,
		quotapointamount:QuotaPointAmount
	},function(data,status){
		window.location = surl.split('#')[0];
		
	});
}
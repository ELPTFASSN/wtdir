// JavaScript Document
$(document).ready(function(e){
	 $('#frmMapSold').submit(function (e){
		 if($('#hdnunSD').val()==0){
			msgbox('Select shift to map','#close','');
			return false;
		}
	 });	
});
// JavaScript Document

$(document).ready(function(){
	
	$('.inventorylistviewsubitem').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		if (e.which==13){
			row ++;
			$('#txt-' + row + '-' + col).select();
		};
	});
	
	$('.inventorylistviewsubitem').keyup(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		switch(e.which){
			case 37: 
				// left
				$('#txt-' + row + '-0').select(); 
				break;
            case 38: 
				// up
				row --;
				$('#txt-' + row + '-' + col).select(); 
				break;
			case 39: 
				// right
				$('#txt-' + row + '-end' ).select();
				//$('#txt-' + row + '-adj' ).select(); 
				break;
			case 40: 
				// down
				row ++;
				$('#txt-' + row + '-' + col).select();
				break;
		}
	});
	
	$('.inventorylistviewsubitem').focus(function(){
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);		
		$('#lvitem-'+row).css('background-color','#B7E3F0');
	});
	
	$('.inventorylistviewsubitem').focusout(function(){
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);
		
		var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
		if (!numberRegex.test($(this).val())){
			$(this).attr('value','0.0000');
		}else{
			var str = $(this).attr('value');
			str = str.replace(/\s/g,'');
			str = parseFloat(str).toFixed(4);
			$(this).attr('value',str);
		};

		var sid='#txt-' + row + '-';
		var processin = 0;
		var amount = 0;
		var price = $('#hdn-' + row + '-pip').attr('value').split('-',1);
		parseFloat(processin);
		parseFloat(amount);
		
		processin = ((parseFloat($(sid + 0).val()) + parseFloat($(sid + 'transfer').val())) - parseFloat($(sid + 'damage').val()) - parseFloat($(sid + 'sold').val()) - parseFloat($(sid + 'end').val())) * -1;
		$('#txt-' + row + '-processin').attr('value',processin.toFixed(4));

		amount = parseFloat($(sid + 'sold').val()) * parseFloat(price);
		$('#txt-' + row + '-amount').attr('value',amount.toFixed(4));
		
		// Forecolor
		if(processin < 0){
			$('#txt-' + row + '-processin').css('color','#F00');
		}else{
			$('#txt-' + row + '-processin').css('color','#8C8C8C');
		};
		
		// Background Color
		if(row%2){
			$('#lvitem-'+row).css('background-color','#EEE');
		}else{
			$('#lvitem-'+row).css('background-color','#FFF');
		};
		
	});
	
	$('.inventorylistviewsubitem').click(function(){
		$(this).select();
	});
	
	// - - - - - - - - - - - - - - - Rawmats
	$('.inventorylistviewsubitemrawmats').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		if (e.which==13){
			row ++;
			$('#txt-' + row + '-' + col).select();
		};
	});
	
	$('.mislistviewsubitemrawmats').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		if (e.which==13){
			row ++;
			$('#txt-' + row + '-' + col).select();
		};
	});
	
	$('.misdenomination').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		if (e.which==13){
			row ++;
			$('#txt-' + row + '-' + col).select();
			event.preventDefault();
      		return false;
		};
		
	});
	
	
	
	$('.misdenomination').keyup(function(event){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		parseInt(col);
		switch(event.which){
			case 37: 
				// left
				col --; 
				$('#txt-' + row + '-' + col).select(); 
				break;
            case 38: 
				// up
				row --;
				$('#txt-' + row + '-' + col).select(); 
				break;
			case 39: 
				// right
				col ++;
				$('#txt-' + row + '-' + col).select(); 
				break;
			case 40: 
				// down
				row ++;
				$('#txt-' + row + '-' + col).select();
				break;
		}
		if(col == 4 || col == 5){
			if(row<=6){
				if(Math.floor($(this).val()) != $(this).val()){
					$(this).val(0);
				}
				totalCashBreakdown()
			}
		}
	});
	
	$('.codtxt').keyup(function(event){
		var row=$(this).attr('id').split('-')[2];
		var col=$(this).attr('id').split('-')[1];
		parseInt(row);
		parseInt(col);
		switch(event.which){
			case 37: 
				// left
				col --; 
				$('#cod-' + row + '-' + col).select(); 
				break;
            case 38: 
				// up
				row --;
				$('#cod-' + row + '-' + col).select(); 
				break;
			case 39: 
				// right
				col ++;
				$('#cod-' + row + '-' + col).select(); 
				break;
			case 40: 
				// down
				row ++;
				$('#cod-' + row + '-' + col).select();
				break;
		}
		//DTR(row);
	});
	
	/*$('.misdenomination').keyup(function(event){
		$('.misdenomination').val(function(index, value) {
			return value
			.replace(/\D/g, "")
			.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
			;
		  });
	});
	
	$('.misdenomination').focusout(function(event){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		parseInt(col);
		if(col == 4 || col == 5){
			if(row<=6){
				if($(this).val()==''){
					$(this).val(0);
				}
				totalCashBreakdown();
			}
		}
	});*/
	
	$('.misdenomination').focusout(function(event){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		parseInt(col);
		//if(col == 4 || col == 5){
			//if(row<=6){
				//if($(this).val()==''){
					//$(this).val(0);
				//}
				totalCashBreakdown();
			//}
		//}
	});
	
	$('.codhrs').focusout(function(event){
		var row=$(this).attr('id').split('-')[2];
		var col=$(this).attr('id').split('-')[1];
		parseInt(row);
		parseInt(col);
		//if(col == 4 || col == 5){
			//if(row<=6){
				//if($(this).val()==''){
					//$(this).val(0);
				//}
				DTR(row);
			//}
		//}
	});
	
	$('.mislistviewsubitemrawmats').keyup(function(event){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		parseInt(col);
		switch(event.which){
			case 37: 
				// left
				col --; 
				$('#txt-' + row + '-' + col).select(); 
				break;
            case 38: 
				// up
				row --;
				$('#txt-' + row + '-' + col).select(); 
				break;
			case 39: 
				// right
				col ++;
				$('#txt-' + row + '-' + col).select(); 
				break;
			case 40: 
				// down
				row ++;
				$('#txt-' + row + '-' + col).select();
				break;
		}
		if(col==2 || col == 3){
			if(Math.floor($(this).val()) != $(this).val()){
  				$(this).val(0);
			}
		}
		if(col == 0 || col == 1){
			if(event.keyCode == 13 && event.ctrlKey){
				var pck = Math.floor($(this).val())//parseInt($(this).val()) / parseInt($('#hdnpack-'+row).val());
				parseFloat(pck);
				var pc = (parseFloat($(this).val()) - pck) /  parseFloat($('#hdnpack-'+row).val());
				parseFloat(pc);
				var unPI=($('#hdnunProductItem'+row).val());
				$('#maindiv').remove();
				
				var maindiv = document.createElement('div');
				var top = getOffset(document.getElementById('txt-'+row+'-'+col)).top;
				var left = getOffset(document.getElementById('txt-'+row+'-'+col)).left + $('#txt-'+row+'-'+col).width() + 10;
								  
				maindiv.id = 'maindiv';
				maindiv.style.position = 'fixed';
				$(maindiv).css('top',top);
				$(maindiv).css('left',left);
				maindiv.style.width= '220px'
				maindiv.style.height = 'auto';
				maindiv.style.borderRadius = '5px';
				maindiv.style.backgroundColor = '#FFF';
				maindiv.style.backgroundImage = 'linear-gradient(rgb(255,255,255) 0%,rgb(238,238,238) 100%)';
				maindiv.style.border = 'thin solid #666';
				maindiv.style.boxShadow = '5px 5px 5px rgba(0,0,0,0.3)';
				maindiv.style.color = '#666';
							
				var divtitle = document.createElement('div');
				divtitle.className = 'popuptitle';
				divtitle.innerHTML = $('#lvtext-'+row).html();
				divtitle.align = 'center';
				divtitle.style.marginTop = '5px';
				divtitle.style.color = '#555';
				
				maindiv.appendChild(divtitle);
				
				var divfields = document.createElement('div');
				var divtotal = document.createElement('div');
				
				divfields.className = 'popupitem';
				divfields.style.width = 'inherit';
				divfields.style.marginBottom = '5px';
				
				divtotal.className = 'popupitem';
				divtotal.style.width = 'inherit';
				divtotal.style.marginBottom = '5px';
				divtotal.style.borderTop = 'thin solid #999';
				
				var labeltotal = document.createElement('div');
				var txttotal = document.createElement('input');
				
				labeltotal.className = 'popupitemlabel';
				labeltotal.innerHTML = 'TOTAL';
				labeltotal.style.width = '72px';
				labeltotal.style.marginLeft = '5px';
				labeltotal.style.color = '#555';
				labeltotal.style.fontWeight = 'bold';
				
				txttotal.type = 'text';
				txttotal.setAttribute('autocomplete','off');
				txttotal.setAttribute('placeholder','0.00');
				txttotal.style.width = '121px';
				txttotal.style.textAlign = 'right';
				txttotal.style.color = '#333';
				txttotal.style.background='transparent';
				txttotal.style.borderColor='transparent';
				txttotal.setAttribute('ReadOnly',true);
				//txttotal.setAttribute('property','disabled');
				
				$.post('ajax/sold.ajax.php',
				{
					qid:'LoadFraction',
					unPI:unPI,
				},
				function(data,status){
					var obj = JSON.parse(data);
					
					var hdnCount = document.createElement('input');
						
					hdnCount.type = 'hidden';
					hdnCount.id = 'hdnCount';
					hdnCount.value = obj.PCUnit.length;
					
					maindiv.appendChild(hdnCount);
						
					for (var i = 0; i < obj.PCUnit.length; i++) {
    					var PCUnit = obj.PCUnit[i];
						var PCSet = obj.PCSet[i];
						var PCRatio = obj.PCRatio[i];
							
						var label = document.createElement('div');
						var txt = document.createElement('input');
						var hdn = document.createElement('input');
						var hdnset = document.createElement('input');
							
						label.className = 'popupitemlabel';
						label.innerHTML = PCUnit+' ['+PCSet+']';
						label.style.width = '72px';
						label.style.marginLeft = '5px';
						label.style.color = '#666';
							
						hdn.type = 'hidden';
						hdn.value = PCRatio;
						hdn.id = 'hdn-'+i;
						hdn.name = 'hdn-'+i;
						
						hdnset.type = 'hidden';
						hdnset.value = PCSet;
						hdnset.id = 'hdnset-'+i;
						hdnset.name = 'hdnset-'+i;
						
						txt.type = 'text';
						txt.id = 'pctxt-'+i;
						txt.name = 'pctxt-'+i;
						txt.setAttribute('autocomplete','off');
						txt.setAttribute('placeholder','0.00');
						/*if(PCSet=='W'){
							txt.value = Math.floor(pck);
						}else{
							txt.value = pc;
						}*/
						txt.value = 0;
						txt.style.width = '121px';
						txt.style.textAlign = 'right';
						txt.onkeypress = function(event){
							var key;
							if(window.event){
								key = window.event.keyCode; //IE
							}else{
								key = e.which; //firefox      																							
							};
								return (key != 13);
							};
						txt.onkeydown = function(event){
							if(event.keyCode == 27){
								$('#maindiv').remove();
								$('#txt-'+row+'-'+col).focus();
								$('#txt-'+row+'-'+col).select();
							}
							if(col==0){
								if(event.keyCode == 13 || event.keyCode == 9 ){
									var index=parseInt($(this).attr('id').split('-')[1]);
									var maxIndex=parseInt($('#hdnCount').val());
									index++;
									total=0;
									whole=0;
									fraction=0;
									for(j=0;j<maxIndex;j++){
										if ($('#hdnset-'+j).val()=='W'){
											whole=(parseInt($('#pctxt-'+j).val())).toFixed(4);
										}else if(j==1){
											fraction=parseFloat($('#pctxt-'+j).val());
										}else{
											fraction=0;
											for(y=2;y<maxIndex;y++){
												fraction+=(parseFloat($('#hdn-'+y).val())/parseFloat($('#hdn-1').val()))*parseFloat($('#pctxt-'+y).val());
												//alert(fraction);
											}
											fraction=parseFloat(fraction)+parseFloat($('#pctxt-1').val());
										}
										total+=(parseInt($('#pctxt-'+j).val())*parseFloat($('#hdn-'+j).val()));
									}
									if(index == maxIndex){								
										$(this).select();
										txt.onkeydown = function(event){
											/*var subT=parseFloat(total).split('.');
											var endW=subT[0];
											var endF=subT[1];*/
											var endW=(Math.floor(total)).toFixed(4);
											var endF=(parseFloat(total)-Math.floor(total)).toFixed(4);
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val((Math.floor(whole)).toFixed(4));
												$('#txt-'+row+'-1').val(fraction.toFixed(4));
												$('#txt-'+row+'-'+col).focus();
												$('#maindiv').remove();
											}
										}
									}
									$('#pctxt-'+index).select();
									txttotal.value = total;
										
								}
							}
							else if(col==1){
								if(event.keyCode == 13 || event.keyCode == 9 ){
									var index=parseInt($(this).attr('id').split('-')[1]);
									var maxIndex=parseInt($('#hdnCount').val());
									index++;
									total=0;
									whole=0;
									fraction=0;
									for(j=0;j<maxIndex;j++){
										if ($('#hdnset-'+j).val()=='W'){
											whole=(parseInt($('#pctxt-'+j).val())).toFixed(4);
										}else if(j==1){
											fraction=parseFloat($('#pctxt-'+j).val());
										}else{
											fraction=0;
											for(y=2;y<maxIndex;y++){
												fraction+=(parseFloat($('#hdn-'+y).val())/parseFloat($('#hdn-1').val()))*parseFloat($('#pctxt-'+y).val());
												//alert(fraction);
											}
											fraction=parseFloat(fraction)+parseFloat($('#pctxt-1').val());
										}
										total+=(parseInt($('#pctxt-'+j).val())*parseFloat($('#hdn-'+j).val()));
									}
									if(index == maxIndex){								
										$(this).select();
										txt.onkeydown = function(event){
											/*var subT=parseFloat(total).split('.');
											var endW=subT[0];
											var endF=subT[1];*/
											var endW=(Math.floor(total)).toFixed(4);
											var endF=(parseFloat(total)-Math.floor(total)).toFixed(4);
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(fraction.toFixed(4));
												$('#txt-'+row+'-0').val((parseFloat(whole)).toFixed(4));
												$('#txt-'+row+'-'+col).focus();
												$('#maindiv').remove();
											}
										}
									}
									$('#pctxt-'+index).select();
									txttotal.value = total;
										
								}
							}
							// Allow: backspace, delete, tab, escape, and enter
							if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 27 || event.keyCode == 13 || 
								 // Allow: Ctrl+A
								(event.keyCode == 65 && event.ctrlKey === true) || 
								 // Allow: home, end, left, right
								(event.keyCode >= 35 && event.keyCode <= 39) ||
								(event.keyCode == 190 || event.keyCode == 110)) {
									 // let it happen, don't do anything
									 return;
							}else{
								// Ensure that it is a number and stop the keypress
								if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
									event.preventDefault(); 
								}   
							}
						}
							
						divfields.appendChild(label);
						divfields.appendChild(txt);
						divfields.appendChild(hdn);
						divfields.appendChild(hdnset);
					};
					divtotal.appendChild(labeltotal);
					divtotal.appendChild(txttotal);
					maindiv.appendChild(divfields);
					maindiv.appendChild(divtotal);
			
					document.getElementById('lvrawmats').appendChild(maindiv);				
					$('#pctxt-0').select();
				});
			}
		}
	});
	
	$('.inventorylistviewsubitemrawmats').keyup(function(event){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		parseInt(col);
		switch(event.which){
			case 37: 
				// left
				col --; 
				$('#txt-' + row + '-' + col).select(); 
				break;
            case 38: 
				// up
				row --;
				$('#txt-' + row + '-' + col).select(); 
				break;
			case 39: 
				// right
				col ++;
				$('#txt-' + row + '-' + col).select(); 
				break;
			case 40: 
				// down
				row ++;
				$('#txt-' + row + '-' + col).select();
				break;
		}
		if(col == 0 || col == 1 || col == 2){
			if(event.keyCode == 13 && event.ctrlKey){
				var pck = Math.floor($(this).val())//parseInt($(this).val()) / parseInt($('#hdnpack-'+row).val());
				parseFloat(pck);
				var pc = (parseFloat($(this).val()) - pck) /  parseFloat($('#hdnpack-'+row).val());
				parseFloat(pc);
				var unPI=($('#hdnunProductItem'+row).val());
				$('#maindiv').remove();
				
				var maindiv = document.createElement('div');
				var top = getOffset(document.getElementById('txt-'+row+'-'+col)).top;
				var left = getOffset(document.getElementById('txt-'+row+'-'+col)).left + $('#txt-'+row+'-'+col).width() + 10;
								  
				maindiv.id = 'maindiv';
				maindiv.style.position = 'fixed';
				$(maindiv).css('top',top);
				$(maindiv).css('left',left);
				maindiv.style.width= '220px'
				maindiv.style.height = 'auto';
				maindiv.style.borderRadius = '5px';
				maindiv.style.backgroundColor = '#FFF';
				maindiv.style.backgroundImage = 'linear-gradient(rgb(255,255,255) 0%,rgb(238,238,238) 100%)';
				maindiv.style.border = 'thin solid #666';
				maindiv.style.boxShadow = '5px 5px 5px rgba(0,0,0,0.3)';
				maindiv.style.color = '#666';
							
				var divtitle = document.createElement('div');
				divtitle.className = 'popuptitle';
				divtitle.innerHTML = $('#lvtext-'+row).html();
				divtitle.align = 'center';
				divtitle.style.marginTop = '5px';
				divtitle.style.color = '#555';
				
				maindiv.appendChild(divtitle);
				
				var divfields = document.createElement('div');
				var divtotal = document.createElement('div');
				
				divfields.className = 'popupitem';
				divfields.style.width = 'inherit';
				divfields.style.marginBottom = '5px';
				
				divtotal.className = 'popupitem';
				divtotal.style.width = 'inherit';
				divtotal.style.marginBottom = '5px';
				divtotal.style.borderTop = 'thin solid #999';
				
				var labeltotal = document.createElement('div');
				var txttotal = document.createElement('input');
				
				labeltotal.className = 'popupitemlabel';
				labeltotal.innerHTML = 'TOTAL';
				labeltotal.style.width = '72px';
				labeltotal.style.marginLeft = '5px';
				labeltotal.style.color = '#555';
				labeltotal.style.fontWeight = 'bold';
				
				txttotal.type = 'text';
				txttotal.setAttribute('autocomplete','off');
				txttotal.setAttribute('placeholder','0.00');
				txttotal.style.width = '121px';
				txttotal.style.textAlign = 'right';
				txttotal.style.color = '#333';
				txttotal.style.background='transparent';
				txttotal.style.borderColor='transparent';
				txttotal.setAttribute('ReadOnly',true);
				//txttotal.setAttribute('property','disabled');
				
				$.post('ajax/sold.ajax.php',
				{
					qid:'LoadFraction',
					unPI:unPI,
				},
				function(data,status){
					var obj = JSON.parse(data);
					
					var hdnCount = document.createElement('input');
						
					hdnCount.type = 'hidden';
					hdnCount.id = 'hdnCount';
					hdnCount.value = obj.PCUnit.length;
					
					maindiv.appendChild(hdnCount);
						
					for (var i = 0; i < obj.PCUnit.length; i++) {
    					var PCUnit = obj.PCUnit[i];
						var PCSet = obj.PCSet[i];
						var PCRatio = obj.PCRatio[i];
							
						var label = document.createElement('div');
						var txt = document.createElement('input');
						var hdn = document.createElement('input');
						var hdnset = document.createElement('input');
							
						label.className = 'popupitemlabel';
						label.innerHTML = PCUnit+' ['+PCSet+']';
						label.style.width = '72px';
						label.style.marginLeft = '5px';
						label.style.color = '#666';
							
						hdn.type = 'hidden';
						hdn.value = PCRatio;
						hdn.id = 'hdn-'+i;
						hdn.name = 'hdn-'+i;
						
						hdnset.type = 'hidden';
						hdnset.value = PCSet;
						hdnset.id = 'hdnset-'+i;
						hdnset.name = 'hdnset-'+i;
						
						txt.type = 'text';
						txt.id = 'pctxt-'+i;
						txt.name = 'pctxt-'+i;
						txt.setAttribute('autocomplete','off');
						txt.setAttribute('placeholder','0.00');
						/*if(PCSet=='W'){
							txt.value = Math.floor(pck);
						}else{
							txt.value = pc;
						}*/
						txt.value = 0;
						txt.style.width = '121px';
						txt.style.textAlign = 'right';
						txt.onkeypress = function(event){
							var key;
							if(window.event){
								key = window.event.keyCode; //IE
							}else{
								key = e.which; //firefox      																							
							};
								return (key != 13);
							};
						txt.onkeydown = function(event){
							if(event.keyCode == 27){
								$('#maindiv').remove();
								$('#txt-'+row+'-'+col).focus();
								$('#txt-'+row+'-'+col).select();
							}
							if(col==0){
								if(event.keyCode == 13 || event.keyCode == 9 ){
									var index=parseInt($(this).attr('id').split('-')[1]);
									var maxIndex=parseInt($('#hdnCount').val());
									index++;
									total=0;
									for(j=0;j<maxIndex;j++){
										total+=(parseInt($('#pctxt-'+j).val())*parseFloat($('#hdn-'+j).val()));
									}
									if(index == maxIndex){								
										$(this).select();
										txt.onkeydown = function(event){
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(total);
												$('#txt-'+row+'-'+col).focus();
												$('#maindiv').remove();
											}
										}
									}
									$('#pctxt-'+index).select();
									txttotal.value = total;
										
								}
							}
							else if(col==1){
								if(event.keyCode == 13 || event.keyCode == 9 ){
									var index=parseInt($(this).attr('id').split('-')[1]);
									var maxIndex=parseInt($('#hdnCount').val());
									index++;
									total=0;
									whole=0;
									fraction=0;
									for(j=0;j<maxIndex;j++){
										if ($('#hdnset-'+j).val()=='W'){
											whole=(parseInt($('#pctxt-'+j).val())).toFixed(4);
										}else if(j==1){
											fraction=parseFloat($('#pctxt-'+j).val());
										}else{
											fraction=0;
											for(y=2;y<maxIndex;y++){
												fraction+=(parseFloat($('#hdn-'+y).val())/parseFloat($('#hdn-1').val()))*parseFloat($('#pctxt-'+y).val());
												//alert(fraction);
											}
											fraction=parseFloat(fraction)+parseFloat($('#pctxt-1').val());
										}
										total+=(parseInt($('#pctxt-'+j).val())*parseFloat($('#hdn-'+j).val()));
									}
									if(index == maxIndex){								
										$(this).select();
										txt.onkeydown = function(event){
											/*var subT=parseFloat(total).split('.');
											var endW=subT[0];
											var endF=subT[1];*/
											var endW=(Math.floor(total)).toFixed(4);
											var endF=(parseFloat(total)-Math.floor(total)).toFixed(4);
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(whole);
												$('#txt-'+row+'-2').val(fraction.toFixed(4));
												$('#txt-'+row+'-'+col).focus();
												$('#maindiv').remove();
											}
										}
									}
									$('#pctxt-'+index).select();
									txttotal.value = total;
										
								}
							}
							else if(col==2){
								if(event.keyCode == 13 || event.keyCode == 9 ){
									var index=parseInt($(this).attr('id').split('-')[1]);
									var maxIndex=parseInt($('#hdnCount').val());
									index++;
									total=0;
									whole=0;
									fraction=0;
									for(j=0;j<maxIndex;j++){
										if ($('#hdnset-'+j).val()=='W'){
											whole=(parseInt($('#pctxt-'+j).val())).toFixed(4);
										}else if(j==1){
											fraction=parseFloat($('#pctxt-'+j).val());
										}else{
											fraction=0;
											for(y=2;y<maxIndex;y++){
												fraction+=(parseFloat($('#hdn-'+y).val())/parseFloat($('#hdn-1').val()))*parseFloat($('#pctxt-'+y).val());
												//alert(fraction);
											}
											fraction=parseFloat(fraction)+parseFloat($('#pctxt-1').val());
										}
										total+=(parseInt($('#pctxt-'+j).val())*parseFloat($('#hdn-'+j).val()));
									}
									if(index == maxIndex){								
										$(this).select();
										txt.onkeydown = function(event){
											/*var subT=parseFloat(total).split('.');
											var endW=subT[0];
											var endF=subT[1];*/
											var endW=(Math.floor(total)).toFixed(4);
											var endF=(parseFloat(total)-Math.floor(total)).toFixed(4);
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(fraction.toFixed(4));
												$('#txt-'+row+'-1').val(whole);
												$('#txt-'+row+'-'+col).focus();
												$('#maindiv').remove();
											}
										}
									}
									$('#pctxt-'+index).select();
									txttotal.value = total;
										
								}
							}
							// Allow: backspace, delete, tab, escape, and enter
							if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 27 || event.keyCode == 13 || 
								 // Allow: Ctrl+A
								(event.keyCode == 65 && event.ctrlKey === true) || 
								 // Allow: home, end, left, right
								(event.keyCode >= 35 && event.keyCode <= 39) ||
								(event.keyCode == 190 || event.keyCode == 110)) {
									 // let it happen, don't do anything
									 return;
							}else{
								// Ensure that it is a number and stop the keypress
								if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
									event.preventDefault(); 
								}   
							}
						}
							
						divfields.appendChild(label);
						divfields.appendChild(txt);
						divfields.appendChild(hdn);
						divfields.appendChild(hdnset);
					};
					divtotal.appendChild(labeltotal);
					divtotal.appendChild(txttotal);
					maindiv.appendChild(divfields);
					maindiv.appendChild(divtotal);
			
					document.getElementById('lvrawmats').appendChild(maindiv);				
					$('#pctxt-0').select();
				});
			}
		}
	});
	
	$('.inventorylistviewsubitemrawmats').focus(function(){
		$('#maindiv').remove();
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);
		$('#lvitem-'+row).css('background-color','#B7E3F0');
	});
	
	$('.inventorylistviewsubitemrawmats').focusout(function(){
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);
		
		var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
		if (!numberRegex.test($(this).val())){
			$(this).attr('value','0.0000');
		}else{
			var str = $(this).attr('value');
			str = str.replace(/\s/g,'');
			str = parseFloat(str).toFixed(4);
			$(this).attr('value',str);
		};
		
		var sid='#txt-' + row + '-';
		var endtotal = 0;
		var dirusage = 0;
		var adj = parseFloat($('#txt-' + row + '-3').val());
		var varianceqty = 0;
		var varianceamt = 0;
		var ratio = $('#hdn-' + row + '-cidrpp').attr('value').split('-')[2];
		var cost = $('#hdn-' + row + '-cidrpp').attr('value').split('-')[0];;
		parseFloat(endtotal);
		parseFloat(dirusage);
		parseFloat(varianceqty);
		parseFloat(varianceamt);
		
		endtotal = (parseFloat($(sid + 2).val()) * parseFloat(ratio)) + parseFloat($(sid + 1).val());
		$(sid + 'endtotal').attr('value',endtotal.toFixed(4)); //alert(adj);
		
		dirusage = parseFloat($(sid + 0).val()) + parseFloat($(sid + 'delivery').val()) + parseFloat($(sid + 'transfer').val()) - parseFloat($(sid + 'damage').val()) - endtotal;
		$(sid + 'dirusage').attr('value',dirusage.toFixed(4));
		
		varianceqty = dirusage + adj - parseFloat($(sid + 'processout').val());
		$(sid + 'varianceqty').attr('value',varianceqty.toFixed(4));
		
		varianceamt = parseFloat(cost) * varianceqty.toFixed(4);
		$(sid + 'varianceamt').attr('value',varianceamt.toFixed(4));
		
		//alert(varianceqty + ' ----- ' + cost + ' ----- ' + varianceamt);
		
		// Forecolor
		if(endtotal < 0){
			$('#txt-' + row + '-endtotal').css('color','#F00');
		}else{
			$('#txt-' + row + '-endtotal').css('color','#8C8C8C');
		};
		
		if(dirusage < 0){
			$('#txt-' + row + '-dirusage').css('color','#F00');
		}else{
			$('#txt-' + row + '-dirusage').css('color','#8C8C8C');
		};

		if(varianceqty < 0){
			$('#txt-' + row + '-varianceqty').css('color','#F00');
		}else{
			$('#txt-' + row + '-varianceqty').css('color','#8C8C8C');
		};

		if(varianceamt < 0){
			$('#txt-' + row + '-varianceamt').css('color','#F00');
		}else{
			$('#txt-' + row + '-varianceamt').css('color','#8C8C8C');
		};
		
		// Background Color
		if(row%2){
			$('#lvitem-'+row).css('background-color','#EEE');
		}else{
			$('#lvitem-'+row).css('background-color','#FFF');
		};
		
	});
	
	$('.inventorylistviewsubitemrawmats').click(function(){
		$(this).select();
	});
	
	$(window).scroll(function(e) {
        $('#maindiv').remove();
    });
});

function totalCashBreakdown(){
	var tBill=0.00;
	var tCoin=0.00;
	var tBnC=0.00;
	var tCash=0.00;
	var EndBal=0.00;
	var SpecDisc=0.00;
	var Var1=0.00;
	var Var2=0.00;
	var TotalVar=0.00;
	if($('#txt-14-5').val()){
		var GC=parseFloat($('#txt-14-5').val().replace(/,/g, ''));
	}else{
		var GC=0.00;
	}
	if($('#txt-15-5').val()){
		var SC=parseFloat($('#txt-15-5').val().replace(/,/g, ''));
	}else{
		var SC=0.00;
	}
	if($('#txt-16-5').val()){
		var Bulk=parseFloat($('#txt-16-5').val().replace(/,/g, ''));
	}else{
		var Bulk=0.00;
	}
	if($('#txt-10-5').val()){
		var TDep=parseFloat($('#txt-10-5').val().replace(/,/g, ''));
	}else{
		var TDep=0.00;
	}
	if($('#txt-19-5').val()){
		var CBank=parseFloat($('#txt-19-5').val().replace(/,/g, ''));
	}else{
		var CBank=0.00;
	}
	if($('#txt-8-5').val()){
		var BegBal=parseFloat($('#txt-8-5').val().replace(/,/g, ''));
	}else{
		var BegBal=0.00;
	}
	if($('#txt-9-5').val()){
		//var TSales=parseFloat($('#txt-9-5').val());
		var TSales=parseFloat($('#txt-9-5').val().replace(/,/g, ''));
	}else{
		var TSales=0.00;
	}
	if($('#txt-11-5').val()){
		var PCash=parseFloat($('#txt-11-5').val().replace(/,/g, ''));
	}else{
		var PCash=0.00;
	}
	for(i=1;i<7;i++){
		if($('#txt-'+i+'-4').val()==''){
			currBVal=0;
		}else{
			currBVal=parseFloat($('#txt-'+i+'-4').val());
		}
		if($('#txt-'+i+'-5').val()==''){
			currCVal=0;
		}else{
			currCVal=parseFloat($('#txt-'+i+'-5').val());
		}
		tBill += (parseFloat(currBVal)*parseFloat($('#Bx-'+i).val()));
		tCoin += (parseFloat(currCVal)*parseFloat($('#Cx-'+i).val()));
		totB = commaSeparateNumber(tBill.toFixed(2));
		totC = commaSeparateNumber(tCoin.toFixed(2));
		$('#txt-7-4').val(totB);
		$('#txt-7-5').val(totC);
	}
	SpecDisc = (( SC / 0.2)*0.32) + GC + Bulk ;
	//replace(/,/g, '')
	tBnC = parseFloat(tBill)+parseFloat(tCoin);
	totBnC = commaSeparateNumber(tBnC.toFixed(2));
	//$('#txt-10-5').val(totBnC);
	tCash = parseFloat(BegBal)+parseFloat(TSales)-parseFloat(PCash)-parseFloat(SpecDisc);
	//alert(tCash+'='+BegBal+'+'+TSales+'-'+PCash+'-'+SpecDisc);
	/*EndBal = parseFloat(BegBal)+parseFloat(TSales)-parseFloat(TDep)-parseFloat(PCash)-parseFloat(SpecDisc);
	Var1 = parseFloat(EndBal)-parseFloat(tBnC);*/
	//EndBal = parseFloat(tCash) - parseFloat(TDep);
	EndBal = parseFloat(tCash) - parseFloat(CBank);
	//Var1 = parseFloat(EndBal)- 500;
	Var2 = parseFloat(TDep)- parseFloat(CBank);
	Var1 = parseFloat($('#txt-13-5').val().replace(/,/g, ''))- parseFloat($('#txt-18-5').val().replace(/,/g, ''));
	//TotalVar = parseFloat($('#txt-21-5').val())+parseFloat($('#txt-24-5').val())+(parseFloat(tBnC)-(parseFloat(BegBal)+parseFloat(TSales)-parseFloat(TDep)-parseFloat(PCash)-parseFloat(SpecDisc))) + (parseFloat(TDep) - parseFloat(CBank)) ;
	TVar1=0;
	TVar2=0;
	TVar3=0;
	TVar4=0;
	if(parseFloat($('#txt-21-5').val())<=0){
		TVar1=0;
	}else{
		TVar1=parseFloat($('#txt-21-5').val().replace(/,/g, ''));
	}
	if(parseFloat($('#txt-24-5').val())<=0){
		TVar2=0;
	}else{
		TVar2=parseFloat($('#txt-24-5').val().replace(/,/g, ''));
	}
	if(parseFloat(Var1)<=0){
		TVar3=0;
	}else{
		TVar3=parseFloat(Var1);
	}
	if(parseFloat(Var2)<=0){
		TVar4=0;
	}else{
		TVar4=parseFloat(Var2);
	}
	TotalVar = parseFloat(TVar1)+(parseFloat(TVar3) + parseFloat(TVar4)) ;
	//TotalVar = parseFloat(TVar1)+parseFloat(TVar2)+(parseFloat(TVar3) + parseFloat(TVar4)) ;
	if(parseFloat(TotalVar)<=0){
		TotalVar=0;
	}
	cEndBal = commaSeparateNumber(EndBal.toFixed(2));
	ctCash = commaSeparateNumber(tCash.toFixed(2));
	//alert(ctCash);
	cSpecDisc = commaSeparateNumber(SpecDisc.toFixed(2));
	cVar1 = commaSeparateNumber(Var1.toFixed(2));
	cVar2 = commaSeparateNumber(Var2.toFixed(2));
	cTotalVar = commaSeparateNumber(TotalVar.toFixed(2));
	$('#txt-13-5').val(cEndBal);
	$('#txt-17-5').val(ctCash);
	$('#txt-12-5').val(cSpecDisc);
	/*$('#txt-18-5').val(Var1.toFixed(2));
	$('#txt-20-5').val(Var2.toFixed(2));*/
	$('#txt-25-5').val(cVar1);
	$('#txt-26-5').val(cVar2);
	$('#txt-27-5').val(cTotalVar);
	
	/** VARIANCE ADJUSTMENT **/
	var varPmix = $('#txt-21-5').val().replace(/,/g, '');
	var varWater = $('#txt-22-5').val().replace(/,/g, '');
	var varSugar = $('#txt-23-5').val().replace(/,/g, '');
	var varSupplies = $('#txt-24-5').val().replace(/,/g, '');
	var varCashFund =  $('#txt-25-5').val().replace(/,/g, '');;
	var adjPmix = $('#txt-adjpmix').val().replace(/,/g, '');
	var adjWater = $('#txt-adjwater').val().replace(/,/g, '');
	var adjSugar = $('#txt-adjsugar').val().replace(/,/g, '');
	var adjSupplies = $('#txt-adjsupplies').val().replace(/,/g, '');
	var adjCashFund = $('#txt-adjcashfund').val().replace(/,/g, '');
	var chargePmix = parseFloat(varPmix) - parseFloat(adjPmix) ;
	var chargeWater = parseFloat(varWater) - parseFloat(adjWater) ;
	var chargeSugar = parseFloat(varSugar) - parseFloat(adjSugar) ;
	var chargeSupplies = parseFloat(varSupplies) - parseFloat(adjSupplies) ;
	var chargeCashFund = parseFloat(varCashFund) - parseFloat(adjCashFund) ;
	$('#txt-chargepmix').val(commaSeparateNumber(Math.max(0,chargePmix).toFixed(2)));
	$('#txt-chargewater').val(commaSeparateNumber(Math.max(0,chargeWater).toFixed(2)));
	$('#txt-chargesugar').val(commaSeparateNumber(Math.max(0,chargeSugar).toFixed(2)));
	$('#txt-chargesupplies').val(commaSeparateNumber(Math.max(0,chargeSupplies).toFixed(2)));
	$('#txt-chargecashfund').val(commaSeparateNumber(Math.max(0,chargeCashFund).toFixed(2)));
}

function DTR(i){
	var diff1 = 0;
	var diff2 = 0;
	var diff = 0.00;
    s1 = $('#cod-3-'+i).val().split(':');
    e1 = $('#cod-4-'+i).val().split(':');
	s2 = $('#cod-5-'+i).val().split(':');
    e2 = $('#cod-6-'+i).val().split(':');
	

    min1 = e1[1]-s1[1];
    hour_carry1 = 0;
    if(min1 < 0){
        min1 += 60;
        hour_carry1 += 1;
    }
    hour1 = e1[0]-s1[0]-hour_carry1;
    diff1 = hour1 + "." + parseInt( min1 / .6 );
	
	min2 = e2[1]-s2[1];
    hour_carry2 = 0;
    if(min2 < 0){
        min2 += 60;
        hour_carry2 += 1;
    }
    hour2 = e2[0]-s2[0]-hour_carry2;
    diff2 = hour1 + "." + parseInt( min2 / .6 );
	diff = parseFloat(diff1)+parseFloat(diff2);
	$('#cod-7-'+i).val(diff.toFixed(2));
}

function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
  }
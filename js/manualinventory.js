// JavaScript Document

$(document).ready(function(){
	/*$(window).keydown(function(event){
			if(event.keyCode == 13) {
			  
			  return false;
			}
		  });*/
	
	$('.inventorylistviewsubitem').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		if (e.which==13){
			row ++;
			$('#txt-' + row + '-' + col).select();
		}
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
			//$(this).attr('value','0.0000');
			$(this).attr('value','0');
		}else{
			var str = $(this).attr('value');
			str = str.replace(/\s/g,'');
			//str = parseFloat(str).toFixed(4);
			str = Math.round(parseFloat(str));
			$(this).attr('value',str);
		}

		var sid='#txt-' + row + '-';
		//var processin = 0;
		var sold = 0;
		var amount = 0;
		var price = $('#hdn-' + row + '-pip').attr('value').split('-',1);
		//parseFloat(processin); 
		parseFloat(sold);
		parseFloat(amount); 
		
		/*processin = ((parseFloat($(sid + 0).val()) + parseFloat($(sid + 'transfer').val())) - parseFloat($(sid + 'damage').val()) - parseFloat($(sid + 'sold').val()) - parseFloat($(sid + 'end').val())) * -1;
		$('#txt-' + row + '-processin').attr('value',processin.toFixed(4));*/
		
		sold = ((parseFloat($(sid + 'processin').val()) + parseFloat($(sid + 0).val())) + parseFloat($(sid + 'delivery').val()) + parseFloat($(sid + 'transfer').val()) - parseFloat($(sid + 'damage').val()) - parseFloat($(sid + 'end').val()));
		//$('#txt-' + row + '-sold').attr('value',sold.toFixed(4));
		$('#txt-' + row + '-sold').attr('value',Math.round(sold));

		amount = parseFloat(sold) * parseFloat(price);
		$('#txt-' + row + '-amount').attr('value',amount.toFixed(4));
		//$('#txt-' + row + '-amount').attr('value',Math.round(amount));
		
		// Forecolor
		if(sold < 0){
			$('#txt-' + row + '-sold').css('color','#F00');
		}else{
			$('#txt-' + row + '-sold').css('color','#8C8C8C');
		}
		
		// Background Color
		if(row%2){
			$('#lvitem-'+row).css('background-color','#EEE');
		}else{
			$('#lvitem-'+row).css('background-color','#FFF');
		}
		
	});
	
	$('.inventorylistviewsubitem').click(function(){
		$(this).select();
	});
	
	// - - - - - - - - - - - - - - - Mix
	$('.inventorylistviewsubitemmix').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(row);
		if (e.which==13){
			row ++;
			$('#txt-' + row + '-' + col).select();
		}
	});
	
	$('.inventorylistviewsubitemmix').keyup(function(event){
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
		if(col == 6 || col == 2){
			if(event.keyCode == 13 && event.ctrlKey){
				var soe = $(this).data('soe');alert(soe);
				var pck = Math.floor($(this).val());//parseInt($(this).val()) / parseInt($('#hdnpack-'+row).val());
				parseFloat(pck);
				var pc = (parseFloat($(this).val()) - pck) /  parseFloat($('#hdnpack-'+row).val());
				parseFloat(pc);
				var unPI=($('#hdnunProductItem'+row).val());
				$('#maindiv').remove();
				
				var maindiv = document.createElement('div');
				var top = getOffset(document.getElementById('txt-'+row+'-'+col)).top;
				var left = getOffset(document.getElementById('txt-'+row+'-'+col)).left + $('#txt-'+row+'-'+col).width() + 10;
								  
				maindiv.id = 'maindiv';
				maindiv.style.position = 'absolute';
				$(maindiv).css('top',top);
				$(maindiv).css('left',left);
				maindiv.style.width= '220px';
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
						if(PCSet=='W'){
							txt.value = Math.floor(pck);
						}else{
							txt.value = pc;
						}
						txt.value = 0;
						txt.style.width = '121px';
						txt.style.textAlign = 'right';
						txt.onkeypress = function(event){
							var key;
							if(window.event){
								key = window.event.keyCode; //IE
							}else{
								key = e.which; //firefox      																							
							}
								return (key != 13);
							};
						txt.onkeydown = function(event){
							if(event.keyCode == 27){
								$('#maindiv').remove();
								$('#txt-'+row+'-'+col).focus();
								$('#txt-'+row+'-'+col).select();
							}
							if(col==2 || col==6){
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
									};
									$('#pctxt-'+index).select();
									txttotal.value = total;
										
								}
							}
							/*else if(col==0){
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
											//var subT=parseFloat(total).split('.');
											//var endW=subT[0];
											//var endF=subT[1];
											var endW=(Math.floor(total)).toFixed(4);
											var endF=(parseFloat(total)-Math.floor(total)).toFixed(4);
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(whole);
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
											//var subT=parseFloat(total).split('.');
											//var endW=subT[0];
											//var endF=subT[1];
											var endW=(Math.floor(total)).toFixed(4);
											var endF=(parseFloat(total)-Math.floor(total)).toFixed(4);
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(fraction.toFixed(4));
												$('#txt-'+row+'-0').val(whole);
												$('#txt-'+row+'-'+col).focus();
												$('#maindiv').remove();
											}
										}
									}
									$('#pctxt-'+index).select();
									txttotal.value = total;
										
								}
							}*/
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
						};
							
						divfields.appendChild(label);
						divfields.appendChild(txt);
						divfields.appendChild(hdn);
						divfields.appendChild(hdnset);
					}
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
	
	$('.inventorylistviewsubitemmix').focus(function(){
		$('#maindiv').remove();
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);
		$('#lvitem-'+row).css('background-color','#B7E3F0');
	});
	
	$('.inventorylistviewsubitemmix').focusout(function(){
		
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);
		
		var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
		if (!numberRegex.test($(this).val())){
			//$(this).attr('value','0.0000');
			$(this).attr('value','0');
		}else{
			var str = $(this).attr('value');
			str = str.replace(/\s/g,'');
			//str = parseFloat(str).toFixed(4);
			str = Math.round(parseFloat(str));
			$(this).attr('value',str);
		}
		
		var sid='#txt-' + row + '-';
		var endtotal = 0;
		var dirusage = 0;
		var processout = $(sid + 'processout').val();
		//var adj = parseFloat($('#txt-' + row + '-3').val());
		var varianceqty = 0;
		var varianceamt = 0;
		//var ratio = $('#hdn-' + row + '-cidrpp').attr('value').split('-')[2];
		//var cost = $('#hdn-' + row + '-cidrpp').attr('value').split('-')[0];;
		var cost = 10;
		parseFloat(endtotal);
		parseFloat(dirusage);
		parseFloat(processout);
		parseFloat(varianceqty);
		parseFloat(varianceamt);
		
		endtotal = parseFloat($(sid + 6).val());
		//endtotal = (parseFloat($(sid + 1).val()) * parseFloat(ratio)) + parseFloat($(sid + 0).val());
		//$(sid + 'endtotal').val(endtotal.toFixed(4)); //alert(adj);
		
		dirusage = (parseFloat($(sid + 2).val()) + parseFloat($(sid + 3).val()) + parseFloat($(sid + 4).val()) - parseFloat($(sid + 5).val()) - endtotal) * 8.5;
		//$(sid + 'dirusage').val(dirusage.toFixed(4));
		$(sid + 'dirusage').val((dirusage).toFixed(1));
		
		//alert(processout);
		
		varianceqty = parseFloat(dirusage) -parseFloat(processout);
		//$(sid + 'varianceqty').val(varianceqty.toFixed(2)*8.5);
		$(sid + 'varianceqty').val((varianceqty).toFixed(1));
		
		varianceamt = parseFloat(cost) * (varianceqty.toFixed(2));
		$(sid + 'varianceamt').val(varianceamt.toFixed(4));
		
		
		//alert(varianceqty + ' ----- ' + cost + ' ----- ' + varianceamt);
		
		// Forecolor
		if(endtotal < 0){
			$('#txt-' + row + '-endtotal').css('color','#F00');
		}else{
			$('#txt-' + row + '-endtotal').css('color','#8C8C8C');
		}
		
		if(dirusage < 0){
			$('#txt-' + row + '-dirusage').css('color','#F00');
		}else{
			$('#txt-' + row + '-dirusage').css('color','#8C8C8C');
		}

		if(varianceqty < 0){
			$('#txt-' + row + '-varianceqty').css('color','#F00');
		}else{
			$('#txt-' + row + '-varianceqty').css('color','#8C8C8C');
		}

		if(varianceamt < 0){
			$('#txt-' + row + '-varianceamt').css('color','#F00');
		}else{
			$('#txt-' + row + '-varianceamt').css('color','#8C8C8C');
		}
		
		// Background Color
		if(row%2){
			$('#lvitem-'+row).css('background-color','#EEE');
		}else{
			$('#lvitem-'+row).css('background-color','#FFF');
		}
		$(sid + 'processout').val(Math.round(processout));
	});
	
	$('.inventorylistviewsubitemmix').click(function(){
		$(this).select();
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
				totalCashBreakdown();
			}
		}
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
				totalCashBreakdown()
			}
		}
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
		if(col == 6 || col == 2){
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
				maindiv.style.position = 'absolute';
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
						if(PCSet=='W'){
							txt.value = pck;
						}else{
							txt.value = Math.round(pc);
						}
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
							if(col==2 || col == 6){
								if(event.keyCode == 13 || event.keyCode == 9 ){
									var index=parseInt($(this).attr('id').split('-')[1]);
									var maxIndex=parseInt($('#hdnCount').val());
									index++;
									total=0;
									for(j=0;j<maxIndex;j++){
										total+=(parseInt($('#pctxt-'+j).val())*parseFloat($('#hdn-'+j).val()));
									}
									var pcknew = Math.floor(total); parseFloat(pcknew);
									var pcnew = (parseFloat(total) - pck) /  parseFloat($('#hdnpack-'+row).val()); parseFloat(pcnew);
									if(index == maxIndex){								
										$(this).select();
										txt.onkeydown = function(event){
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(total);
												if(col==2){
													$('#sw-'+row).val(pcknew.toFixed(4));
												    $('#sf-'+row).val(pcnew.toFixed(4));
												}else{
													$('#ew-'+row).val(pcknew.toFixed(4));
												    $('#ef-'+row).val(pcnew.toFixed(4));
												}
												$('#txt-'+row+'-'+col).focus();
												$('#maindiv').remove();
											}
										}
									}
									$('#pctxt-'+index).select();
									txttotal.value = total;
								}
							}
							/*else if(col==0){
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
											//var subT=parseFloat(total).split('.');
											//var endW=subT[0];
											//var endF=subT[1];
											var endW=(Math.floor(total)).toFixed(4);
											var endF=(parseFloat(total)-Math.floor(total)).toFixed(4);
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(whole);
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
											//var subT=parseFloat(total).split('.');
											//var endW=subT[0];
											//var endF=subT[1];
											var endW=(Math.floor(total)).toFixed(4);
											var endF=(parseFloat(total)-Math.floor(total)).toFixed(4);
											if(event.keyCode == 13 || event.keyCode == 9 ){
												$('#txt-'+row+'-'+col).val(fraction.toFixed(4));
												$('#txt-'+row+'-0').val(whole);
												$('#txt-'+row+'-'+col).focus();
												$('#maindiv').remove();
											}
										}
									}
									$('#pctxt-'+index).select();
									txttotal.value = total;
										
								}
							}*/
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
		//var adj = parseFloat($('#txt-' + row + '-3').val());
		var varianceqty = 0;
		var varianceamt = 0;
		var ratio = $('#hdn-' + row + '-cidrpp').attr('value').split('-')[2];
		var cost = $('#hdn-' + row + '-cidrpp').attr('value').split('-')[0];;
		parseFloat(endtotal);
		parseFloat(dirusage);
		parseFloat(varianceqty);
		parseFloat(varianceamt);
		
		endtotal = parseFloat($(sid + 6).val());
		//endtotal = (parseFloat($(sid + 1).val()) * parseFloat(ratio)) + parseFloat($(sid + 0).val());
		//$(sid + 'endtotal').val(endtotal.toFixed(4)); //alert(adj);
		
		dirusage = parseFloat($(sid + 2).val()) + parseFloat($(sid + 3).val()) + parseFloat($(sid + 4).val()) - parseFloat($(sid + 5).val()) - endtotal;
		//alert(dirusage);
		
		//varianceqty = dirusage + adj - parseFloat($(sid + 'processout').val());
		//$(sid + 'varianceqty').attr('value',varianceqty.toFixed(4));
		
		//varianceamt = parseFloat(cost) * varianceqty.toFixed(4);
		//$(sid + 'varianceamt').attr('value',varianceamt.toFixed(4));
		
		//alert(varianceqty + ' ----- ' + cost + ' ----- ' + varianceamt);
		
		// Forecolor 
		if(endtotal < 0){ 
			$('#txt-' + row + '-endtotal').css('color','#F00');
		}else{
			$('#txt-' + row + '-endtotal').css('color','#8C8C8C');
		};
		//alert(dirusage);
		if(dirusage < 0){
			$('#txt-' + row + '-processout').css('color','#F00');
		}else{
			$('#txt-' + row + '-processout').css('color','#8C8C8C');
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
		$(sid + 'processout').val((parseFloat(dirusage)/parseFloat(ratio)).toFixed(4));
	});
	
	$('.inventorylistviewsubitemrawmats').click(function(){
		$(this).select();
	});
	
	
	
	$(window).scroll(function(e) {
        $('#maindiv').remove();
    });
	
	/* ------ REPORTS : INCENTIVES --------- */
	$('.repquota').focusout(function(){
		/*var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
		if (!numberRegex.test($(this).val())){
			$(this).attr('value','0.00');
		}*/
		/*pro = $(this).val();
		alert(pro);*/
		thisval = $(this).val();
		$(this).attr('value',thisval);
		processIncentives();
		
	});
	$('.reposname').focusout(function(){
		processIncentives()
	});
	$('.repomname').focusout(function(){
		processIncentives()
	});
	$('.repssname').focusout(function(){
		processIncentives()
	});
	$('.reposhr').focusout(function(){
		processIncentives()
	});
	$('.repomhr').focusout(function(){
		processIncentives()
	});
	$('.repsshr').focusout(function(){
		processIncentives()
	});
	 /*$('.repquota').keyup(function(){
		 var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
		 if (!numberRegex.test($(this).val())){
			//$(this).attr('value','0.0000');
			$(this).attr('value','0.00');
		}
	 });*/
	$('.repquota').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		parseInt(row);
		if (e.which==13){
			row ++;
			event.preventDefault();
			$('#repquota-' + row).select();
		};
	});
	$('.reposname').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(col);
		if (e.which==13){
			col ++;
			$('#reposname-' + row + '-' + col).select();
		};
	});
	$('.repssname').keypress(function(e){
		row=$(this).attr('id').split('-')[1];
		col=$(this).attr('id').split('-')[2];
		parseInt(col);
		if (e.which==13){
			col ++;
			$('#repssname-' + row + '-' + col).select();
		};
	});
	$('.repomname').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(col);
		if (e.which==13){
			col ++;
			$('#repomname-' + row + '-' + col).select();
		};
	});
	$('.reposhr').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(col);
		if (e.which==13){
			col ++;
			$('#reposhr-' + row + '-' + col).select();
		};
	});
	$('.repsshr').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(col);
		if (e.which==13){
			col ++;
			$('#repsshr-' + row + '-' + col).select();
		};
	});
	$('.repomhr').keypress(function(e){
		var row=$(this).attr('id').split('-')[1];
		var col=$(this).attr('id').split('-')[2];
		parseInt(col);
		if (e.which==13){
			col ++;
			$('#repomhr-' + row + '-' + col).select();
		};
	});
});

function totalCashBreakdown(){
	var tBill=0.00;
	var tCoin=0.00;
	var tBnC=0.00;
	var tDepo=0.00;
	var tChange=0.00;
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
		$('#txt-7-4').val(tBill.toFixed(2));
		$('#txt-7-5').val(tCoin.toFixed(2));
	}
	tBnC = parseFloat(tBill)+parseFloat(tCoin);
	$('#txt-8-5').val(tBnC.toFixed(2));
}

function processIncentives(){
	summarytotal = 0;
	for(i=0;i<200;i++){
			if($("#repquota-" + i).length > 0) {
				if($("#repquota-" + i).val()>0){
				//if($("#repquota-" + i).val()<$("#repnetsales-" + i).attr('pseudo')&& $("#repquota-" + i).val()>0){
					netsales = parseFloat($("#repnetsales-" + i).attr('pseudo'));
					quota = parseFloat($("#repquota-" + i).val());
					points = Math.floor(((netsales-quota)/1000)+1)*100;
					$("#reppoints-" + i).val(points);
					if($("#repss-" + i).length > 0) {
						repssval = $("#repss-" + i).attr('data-id');
						repssinc = (parseFloat(repssval)*.010)*points
						$("#repss-" + i).val(repssinc);
					}
					repcrewval = $("#repcrew-" + i).attr('data-id');
					repcrewinc = (parseFloat(repcrewval)*.010)*points
					$("#repcrew-" + i).val(repcrewinc);
					repomval = $("#repom-" + i).attr('data-id');
					repominc = (parseFloat(repomval)*.010)*points
					$("#repom-" + i).val(repominc);
					reposval = $("#repos-" + i).attr('data-id');
					reposinc = (parseFloat(reposval)*.010)*points
					$("#repos-" + i).val(reposinc);
					totalPercent = 0;
					totalInc = 0;
					for(j=0;j<20;j++){
						if($("#repcrewhr-" + i + "-" + j).length > 0){
							if($("#repcrewhr-" + i + "-" + j).val() > 0){
								perchr = parseFloat(parseFloat($("#repcrewhr-" + i + "-" + j).val())/parseFloat($("#repcrewtotalhr-" + i).val()))*100;
								$("#repcrewperc-" + i + "-" + j).val(perchr.toFixed(2)+'%');
								perinc = parseFloat(repcrewinc)*parseFloat(parseFloat(perchr)/100);
								$("#repcrewinc-" + i + "-" + j).val(perinc.toFixed(2));
								totalInc += parseFloat(perinc);
								totalPercent = parseFloat(totalPercent) + parseFloat(perchr);
							}
						}
					}
					$("#repcrewtotalperc-" + i).val(totalPercent.toFixed(2)+'%');
					$("#repcrewtotalinc-" + i).val(totalInc.toFixed(2));
					reposname = 0;
					repssname = 0;
					repomname = 0;
					totalHROS = 0;
					totalPercOS = 0;
					totalIncOS = 0;
					totalHRSS = 0;
					totalPercSS = 0;
					totalIncSS = 0;
					totalHROM = 0;
					totalPercOM = 0;
					totalIncOM = 0;
					for(j=0;j<5;j++){
						if($("#reposname-" + i + "-" + j).length > 0){
							if($("#reposname-" + i + "-" + j).val()!=''){
								reposname++;
								if($("#reposhr-" + i + "-" + j).val() > 0){
									totalHROS += parseFloat($("#reposhr-" + i + "-" + j).val());
								}
							}
						}
						if($("#repssname-" + i + "-" + j).length > 0){
							if($("#repssname-" + i + "-" + j).val()!=''){
								repssname++;
								if($("#repsshr-" + i + "-" + j).val() > 0){
									totalHRSS += parseFloat($("#repsshr-" + i + "-" + j).val());
								}
							}
						}
						if($("#repomname-" + i + "-" + j).length > 0){
							if($("#repomname-" + i + "-" + j).val()!=''){
								repomname++;
								if($("#repomhr-" + i + "-" + j).val() > 0){
									totalHROM += parseFloat($("#repomhr-" + i + "-" + j).val());
								}
							}
						}
					}
					/*---------HOURLESS-------------for(j=0;j<5;j++){
						if($("#reposname-" + i + "-" + j).length > 0){
							if($("#reposname-" + i + "-" + j).val()!=''){
								speosperc = 100/parseFloat(reposname);
								speosinc = (parseFloat(speosperc)/100)*reposinc;
								$("#reposperc-" + i + "-" + j).val(speosperc.toFixed(2)+'%');
								$("#reposinc-" + i + "-" + j).val(speosinc.toFixed(2));
								totalPercOS += speosperc;
								totalIncOS += speosinc;
							}
						}
					}*/
					for(j=0;j<5;j++){
						if($("#reposname-" + i + "-" + j).length > 0){
							if($("#reposname-" + i + "-" + j).val()!=''){
								if($("#reposhr-" + i + "-" + j).val() > 0){
									speosperc = parseFloat(parseFloat($("#reposhr-" + i + "-" + j).val())/parseFloat(totalHROS))*100;
									speosinc = (parseFloat(speosperc)/100)*reposinc;
									$("#reposperc-" + i + "-" + j).val(speosperc.toFixed(2)+'%');
									$("#reposinc-" + i + "-" + j).val(speosinc.toFixed(2));
									totalPercOS += speosperc;
									totalIncOS += speosinc; 
								}
							}
						}
					}
					for(j=0;j<5;j++){
						if($("#repomname-" + i + "-" + j).length > 0){
							if($("#repomname-" + i + "-" + j).val()!=''){
								if($("#repomhr-" + i + "-" + j).val() > 0){
									speomperc = parseFloat(parseFloat($("#repomhr-" + i + "-" + j).val())/parseFloat(totalHROM))*100;
									speominc = (parseFloat(speomperc)/100)*repominc;
									$("#repomperc-" + i + "-" + j).val(speomperc.toFixed(2)+'%');
									$("#repominc-" + i + "-" + j).val(speominc.toFixed(2));
									totalPercOM += speomperc;
									totalIncOM += speominc;
								}
							}
						}
					}
					for(j=0;j<5;j++){
						if($("#repssname-" + i + "-" + j).length > 0){
							if($("#repssname-" + i + "-" + j).val()!=''){
								if($("#repsshr-" + i + "-" + j).val() > 0){
									spessperc = parseFloat(parseFloat($("#repsshr-" + i + "-" + j).val())/parseFloat(totalHRSS))*100;
									spessinc = (parseFloat(spessperc)/100)*repssinc;
									$("#repssperc-" + i + "-" + j).val(spessperc.toFixed(2)+'%');
									$("#repssinc-" + i + "-" + j).val(spessinc.toFixed(2));
									totalPercSS += spessperc;
									totalIncSS += spessinc;
								}
							}
						}
					}
					$("#repostotalhr-" + i).val(totalHROS.toFixed(2));
					$("#repostotalperc-" + i).val(totalPercOS.toFixed(2)+'%');
					$("#repostotalinc-" + i).val(totalIncOS.toFixed(2));
					$("#repsstotalhr-" + i).val(totalHRSS.toFixed(2));
					$("#repsstotalperc-" + i).val(totalPercSS.toFixed(2)+'%');
					$("#repsstotalinc-" + i).val(totalIncSS.toFixed(2));
					$("#repomtotalhr-" + i).val(totalHROM.toFixed(2));
					$("#repomtotalperc-" + i).val(totalPercOM.toFixed(2)+'%');
					$("#repomtotalinc-" + i).val(totalIncOM.toFixed(2));
				}else{
					$("#repquota-" + i).val('0.00');
					$("#repostotalhr-" + i).val('0.00');
					$("#repostotalperc-" + i).val('0.00');
					$("#repostotalinc-" + i).val('0.00');
					$("#repsstotalhr-" + i).val('0.00');
					$("#repsstotalperc-" + i).val('0.00');
					$("#repsstotalinc-" + i).val('0.00');
					$("#repomtotalhr-" + i).val('0.00');
					$("#repomtotalperc-" + i).val('0.00');
					$("#repomtotalinc-" + i).val('0.00');
					$("#reppoints-" + i).val('0.00');
					if($("#repss-" + i).length > 0) {
						$("#repss-" + i).val('0.00');
					}
					$("#repos-" + i).val('0.00');
					$("#repom-" + i).val('0.00');
					$("#repcrew-" + i).val('0.00');
					for(j=0;j<20;j++){
						$("#repcrewperc-" + i + "-" + j).val('%');
						$("#repcrewinc-" + i + "-" + j).val('0.00');
						$("#reposperc-" + i + "-" + j).val('%');
						$("#reposinc-" + i + "-" + j).val('0.00');
						$("#repssperc-" + i + "-" + j).val('%');
						$("#repssinc-" + i + "-" + j).val('0.00');
						$("#repomperc-" + i + "-" + j).val('%');
						$("#repominc-" + i + "-" + j).val('0.00');
					}
					$("#repcrewtotalperc-" + i).val('0.00');
					$("#repcrewtotalinc-" + i).val('0.00');
				}
			}
		}
		$('.rptsumrow .rptsumamt').each(function(){
			$(this).text('0.00');
		});
		$('.rptsumcrewtr').remove();
		$('.rptsumom').remove();
		$('.rptsumss').remove();
		$('.rptsumos').remove();
		$($('.rptrow .rptccrew').get().reverse()).each(function(){
			repcstr = $(this).text().toUpperCase();
			if(repcstr!=''){
				if ($(".rptsumrow .rptsumcrew")[0]){
					same = 0;
					$('.rptsumrow .rptsumcrew').each(function(){
						repsumname = $(this).text().toUpperCase();
						if(repcstr == repsumname){
							same++;
						}
						/*$('#incentivesrptsumtab tr:nth-child(2)').after('<tr class="rptsumrow rptsumcrewtr"><td class="rptsumcrew" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repcstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');*/
					});
					if(same == 0){
						$('#incentivesrptsumtab tr:nth-child(2)').after('<tr class="rptsumrow rptsumcrewtr rptsumcrewtrsort"><td class="rptsumcrew" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repcstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
					}
				}else{
					$('#incentivesrptsumtab tr:nth-child(2)').after('<tr class="rptsumrow rptsumcrewtr rptsumcrewtrsort"><td class="rptsumcrew" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repcstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
				}
			}
		});
		$($('.rptrow .reposname').get().reverse()).each(function(){
			reposstr = $(this).val().toUpperCase();
			if(reposstr!=''){
				if ($(".rptsumos .rptsumosname")[0]){
					same = 0;
					$('.rptsumos .rptsumosname').each(function(){
						repsumname = $(this).text().toUpperCase();
						if(reposstr == repsumname){
							same++;
						}
					});
					if(same == 0){
						$('#incentivesrptsumtab tr:last').before('<tr class="rptsumrow rptsumos rptsumspec"><td class="rptsumcrew rptsumosname" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+reposstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
					}
				}else{
					$('#incentivesrptsumtab tr:last').before('<tr class="rptsumrow rptsumos rptsumspec"><td class="rptsumcrew rptsumosname" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+reposstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
				}
			}
		});
		$($('.rptrow .repomname').get().reverse()).each(function(){
			repomstr = $(this).val();
			if(repomstr!=''){
				if ($(".rptsumom .rptsumomname")[0]){
					same = 0;
					$('.rptsumom .rptsumomname').each(function(){
						repsumname = $(this).text().toUpperCase();
						if(repomstr == repsumname){
							same++;
						}
					});
					if(same == 0){
						$('#incentivesrptsumtab tr:last').before('<tr class="rptsumrow rptsumom rptsumspec"><td class="rptsumcrew rptsumomname" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repomstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
					}
				}else{
					$('#incentivesrptsumtab tr:last').before('<tr class="rptsumrow rptsumom rptsumspec"><td class="rptsumcrew rptsumomname" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repomstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
				}
			}
		});
		$($('.rptrow .repssname').get().reverse()).each(function(){
			repssstr = $(this).val();
			if(repssstr!=''){
				if ($(".rptsumss .rptsumssname")[0]){
					same = 0;
					$('.rptsumss .rptsumssname').each(function(){
						repsumname = $(this).text().toUpperCase();
						if(repssstr == repsumname){
							same++;
						}
					});
					if(same == 0){
						$('#incentivesrptsumtab tr:last').before('<tr class="rptsumrow rptsumss rptsumspec"><td class="rptsumcrew rptsumssname" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repssstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
					}
				}else{
					$('#incentivesrptsumtab tr:last').before('<tr class="rptsumrow rptsumss rptsumspec"><td class="rptsumcrew rptsumssname" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:left;text-transform: uppercase; padding:2px;">'+repssstr.toUpperCase()+'</td><td class="rptsumamt" style="width:50%; border: 1px solid #000; border-collapse: collapse; text-align:right;padding:2px;">0.00</td></tr>');
				}
			}
		});
		sortTable('rptsumcrewtrsort');
		sortTable('rptsumspec');
		$('.rptrow .rptamt').each(function(){
		  	amt = $(this).val();
			str = $(this).parent().siblings('.rptcrew').text().toUpperCase();
			if(str == null || str == ''){
				str = $(this).parent().siblings('.rptcrew').find('input').val().toUpperCase();
			}
			if(amt == null || amt == ''){
				amt = 0.00;
			}
			//alert(amt);
			$('.rptsumrow .rptsumamt').each(function(){
				sumrowamt = $(this).text();
				//alert(sumrowamt);
				sumstr = $(this).siblings('.rptsumcrew').text().toUpperCase();
				//alert(sumstr);
				if(sumstr==str){
					sumamt = parseFloat(amt) + parseFloat(sumrowamt);
					$(this).text(addCommas(sumamt.toFixed(2)));
					//summarytotal += parseFloat(sumamt);
				}
			});
		});
		$('.rptsumrow .rptsumamt').each(function(){
			sumrowamtfin = $(this).text();
			sumrowamtfin=sumrowamtfin.replace(/\,/g,'');
			summarytotal = parseFloat(summarytotal)+Math.max(0, parseFloat(sumrowamtfin));
			//alert(summarytotal); 
		});
		$("#summarytotal").text(addCommas(Math.round(summarytotal.toFixed(2))));
		$('.rptsumrow').each(function(){
			sumcheckamt = $(this).children('.rptsumamt').text();
			if(sumcheckamt != "0.00"){
				//$(this).remove();
				//alert(sumcheckamt);
			}else{
				$(this).remove();
			}
		});
}

function sortTable(classname){
	var table, rows, switching, i, x, y, shouldSwitch;
		  table = document.getElementById("incentivesrptsumtab");
		  switching = true;
		  /*Make a loop that will continue until
		  no switching has been done:*/
		  while (switching) {
			//start by saying: no switching is done:
			switching = false;
			rows = table.getElementsByClassName(classname);
			/*Loop through all table rows (except the
			first, which contains table headers):*/
			for (i = 1; i < (rows.length - 1); i++) {
			  //start by saying there should be no switching:
			  shouldSwitch = false;
			  /*Get the two elements you want to compare,
			  one from current row and one from the next:*/
			  x = rows[i].getElementsByClassName("rptsumcrew")[0];
			  y = rows[i + 1].getElementsByClassName("rptsumcrew")[0];
			  //check if the two rows should switch place:
			  if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
				//if so, mark as a switch and break the loop:
				shouldSwitch= true;
				break;
			  }
			}
			if (shouldSwitch) {
			  /*If a switch has been marked, make the switch
			  and mark that a switch has been done:*/
			  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
			  switching = true;
			}
		  }
}

function sortFOMTable(classname){
	var table, rows, switching, i, x, y, shouldSwitch;
		  table = document.getElementById("fomincentivesrptsumtab");
		  switching = true;
		  /*Make a loop that will continue until
		  no switching has been done:*/
		  while (switching) {
			//start by saying: no switching is done:
			switching = false;
			rows = table.getElementsByClassName(classname);
			/*Loop through all table rows (except the
			first, which contains table headers):*/
			for (i = 1; i < (rows.length - 1); i++) {
			  //start by saying there should be no switching:
			  shouldSwitch = false;
			  /*Get the two elements you want to compare,
			  one from current row and one from the next:*/
			  x = rows[i].getElementsByClassName("rptsumcrew")[0];
			  y = rows[i + 1].getElementsByClassName("rptsumcrew")[0];
			  //check if the two rows should switch place:
			  if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
				//if so, mark as a switch and break the loop:
				shouldSwitch= true;
				break;
			  }
			}
			if (shouldSwitch) {
			  /*If a switch has been marked, make the switch
			  and mark that a switch has been done:*/
			  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
			  switching = true;
			}
		  }
}
function addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}



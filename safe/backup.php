 <!--================================================INVENTORY JS (piece/pack)==============================================-->
 
 				
				
				/*
                
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
                
                var divpack = document.createElement('div');
				var labelpack = document.createElement('div');
				var txtpack = document.createElement('input');
				
				divpack.className = 'popupitem';
				divpack.style.width = 'inherit';
				
				labelpack.className = 'popupitemlabel';
				labelpack.innerHTML = 'Pack(s)';
				labelpack.style.width = '52px';
				labelpack.style.marginLeft = '5px';
				labelpack.style.color = '#666';
				
				txtpack.type = 'text';
				txtpack.setAttribute('autocomplete','off');
				txtpack.setAttribute('placeholder','0.00');
				txtpack.value = Math.floor(pck);
				txtpack.style.width = '121px';
				txtpack.style.textAlign = 'right';
				txtpack.onkeypress = function(event){
					var key;
					if(window.event){
						key = window.event.keyCode; //IE
					}else{
						key = e.which; //firefox      																							  
					};
					return (key != 13);
				};
				txtpack.onkeydown = function(event){
					if(event.keyCode == 27){
						$('#maindiv').remove();
						$('#txt-'+row+'-'+col).focus();
						$('#txt-'+row+'-'+col).select();
					}
					if(event.keyCode == 13){
						txtpiece.select();
					}
					// Allow: backspace, delete, tab, escape, and enter
					if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
						 // Allow: Ctrl+A
						(event.keyCode == 65 && event.ctrlKey === true) || 
						 // Allow: home, end, left, right
						(event.keyCode >= 35 && event.keyCode <= 39) ||
						(event.keyCode == 190 || event.keyCode == 110)) {
							 // let it happen, don't do anything
							 return;
					}
					else {
						// Ensure that it is a number and stop the keypress
						if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
							event.preventDefault(); 
						}   
					}
				}
				
				divpack.appendChild(labelpack);
				divpack.appendChild(txtpack);
				
				var divpiece = document.createElement('div');
				var labelpiece = document.createElement('div');
				var txtpiece = document.createElement('input');
				
				divpiece.className = 'popupitem';
				divpiece.style.width = 'inherit';
				
				labelpiece.className = 'popupitemlabel';
				labelpiece.innerHTML = 'Piece(s)';
				labelpiece.style.width = '52px';
				labelpiece.style.marginLeft = '5px';
				labelpiece.style.color = '#666';
				
				txtpiece.type = 'text';
				txtpiece.setAttribute('autocomplete','off');
				txtpiece.setAttribute('placeholder','0.00');
				txtpiece.value = Math.floor(pc);
				txtpiece.style.width = '121px';
				txtpiece.style.textAlign = 'right';
				txtpiece.onkeypress = function(event){
					var key;
					if(window.event){
						key = window.event.keyCode; //IE
					}else{
						key = e.which; //firefox      																							  
					};
					return (key != 13);
				};
				txtpiece.onkeydown = function(event){
					if(event.keyCode == 27){
						$('#maindiv').remove();
						$('#txt-'+row+'-'+col).focus();
						$('#txt-'+row+'-'+col).select();
					}
					if(event.keyCode == 13){
						var starting = (parseFloat(txtpiece.value) * $('#hdnpack-'+row).val()) + parseFloat(txtpack.value);
						$('#txt-'+row+'-'+col).val(starting);
						$('#txt-'+row+'-'+col).focus();
						$('#maindiv').remove();
					}
					// Allow: backspace, delete, tab, escape, and enter
					if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
						 // Allow: Ctrl+A
						(event.keyCode == 65 && event.ctrlKey === true) || 
						 // Allow: home, end, left, right
						(event.keyCode >= 35 && event.keyCode <= 39) ||
						(event.keyCode == 190 || event.keyCode == 110)) {
							 // let it happen, don't do anything
							 return;
					}
					else {
						// Ensure that it is a number and stop the keypress
						if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
							event.preventDefault(); 
						}   
					}
				}
				
				divpiece.appendChild(labelpiece);
				divpiece.appendChild(txtpiece);
				
				maindiv.appendChild(divtitle);
				maindiv.appendChild(divpack);
				maindiv.appendChild(divpiece);
				
				document.getElementById('lvrawmats').appendChild(maindiv);
				txtpack.select();*/
                
                /*$.post('ajax/sold.ajax.php',
				{
					qid:'LoadFraction',
					unPI:unPI,
				},
				function(data,status){
					//obj = JSON.stringify(data);
					//alert(obj);
					//$('#soldshiftdata').empty();
					//$('#maindiv').append(data);
					var obj = JSON.parse(data);
					/*WPCUnit=obj.PCUnit;
					alert(WPCUnit);*/
					/*obj = JSON.stringify(data);
					alert(obj);*/
					/*for (var i = 0; i < obj.PCUnit.length; i++) {
    					var PCUnit = obj.PCUnit[i];
						var PCSet = obj.PCSet[i];
						var PCRatio = obj.PCRatio[i];
							if(PCSet=='W'){
							alert(PCUnit+' '+PCRatio);
						};
					};
				});*/

 

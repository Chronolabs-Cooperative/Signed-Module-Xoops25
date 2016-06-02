// JavaScript Document


function refreshform(data){
	$.each(data, function(i, n){
		switch(i){
		case 'innerhtml':
			$.each(n, function(y, k){
			  var tmp = document.getElementById(y);
			  if (tmp)
			  	tmp.innerHTML = k
			  var tmp = false;
			});
			break;
		case 'disable':
			$.each(n, function(y, k){
				switch(k){
				case '':
				case 'false':
				  document.getElementById(y).disabled = false;
				  break;
				default:
				  document.getElementById(y).disabled = true;
				  break;
				}
			});
			break;			
		case 'checked':
			$.each(n, function(y, k){
				switch(k){
				case 'false':
				  document.getElementById(y).checked = false;
				  break;
				default:
				  document.getElementById(y).checked = true;
				  break;
				}
			});
			break;						
		}
	});
}
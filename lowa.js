jQuery(document).ready(function($){
	$('#add-menu-form').submit(function(e){
		e.preventDefault();
		alert("caught the click");
		var menu_id =document.getElementById("menu_id").value;
		var menu_price =document.getElementById("menu_price").value;
		var menu_name =document.getElementById("menu_name").value;
		var menu_stock =document.getElementById("menu_stock").value;
			var post_data = {
			 	action:'add_menu',
			 	menu_id: menu_id,
			 	menu_price: menu_price,
			 	menu_name: menu_name,
			 	menu_stock: menu_stock,
			 	lowa_nonce: lowadata.nonce
			};
			$.post(lowadata.ajaxurl,post_data,function(response){
				if(response == 'finished') {
			 		alert('Successful');
			 	}else if(response == 'not entered'){
			 		alert('Not entered');
			 	}
			});
	});
});
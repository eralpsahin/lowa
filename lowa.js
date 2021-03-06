jQuery(document).ready(function($){
	 $("#qu").hide();
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
					document.getElementById("menu_id").value='';
					document.getElementById("menu_price").value='';
					document.getElementById("menu_name").value='';
			 		alert(menu_id.concat(" (",menu_name ,") added to the Menu Table!"));
			 	}else if(response == 'not entered'){
			 		alert('Not entered');
			 	}else {
					alert('Entered id already exists');
				}
			});
	});
	$('#add-ingredient-form').submit(function(e){
		e.preventDefault();
		var menu = document.getElementById("menulist").value;
		var ingredient = document.getElementById("ingredientlist").value;
		alert(ingredient);
		var post_data = {
			 	action:'add_ingredient',
			 	menu_id: menu,
			 	i_name: ingredient,
			 	lowa_nonce: lowadata.nonce
			};
			$.post(lowadata.ajaxurl,post_data,function(response){
				if(response == 'finished') {
					alert(menu.concat(" containts ",ingredient," now!"));
			 	}else {
					alert('Entered ingredient already exists for the menu!');
				}
			});
	});
	$('#add-group-form').submit(function(e){
		e.preventDefault();
		var num_people =document.getElementById("num_people").value;
		var val = document.getElementById("btn").value;
		if(val=='Find corresponding table'){
		alert(num_people);
		var post_data = {
			 	action:'find_table',
				num_people: num_people,
			 	lowa_nonce: lowadata.nonce
			};
		$.post(lowadata.ajaxurl,post_data,function(response){
			alert("Table with id ".concat(response," is available for the group with ",num_people," people"));
			document.getElementById("btn").value='Seat the group to the table id '.concat(response);
			document.getElementById("t_id").value=response;
			document.getElementById("n_people").value=num_people;
		});
		}else{
			alert("Seating the group");
			var t_id =document.getElementById("t_id").value;
			var num_people=document.getElementById("n_people").value;
			alert(t_id);
			alert(num_people);
			var post_data = {
			 	action:'insert_group',
			 	t_id: t_id,
				num_people: num_people,
			 	lowa_nonce: lowadata.nonce
			};
			$.post(lowadata.ajaxurl,post_data,function(response){
				if(response == 'finished') {
					alert("Group is seated");
					window.location.reload(true);
			 	}else {
					alert('There was an error while seating the group!');
				}
			});
		}
	});
	$('#find-menu-form').submit(function(e){
		e.preventDefault();
		var ingredient = document.getElementById("ingredientlist").value;
		alert(ingredient);
		var post_data = {
			 	action:'find_menu',
				ingredient: ingredient,
			 	lowa_nonce: lowadata.nonce
			};
		$.post(lowadata.ajaxurl,post_data,function(response){
			if(response == '') {
					alert("No menu with this ingredient");
			 	}else {
					alert(response);
				}
		});
	});
	$('#add-order-form').submit(function(e){
		e.preventDefault();
		var quantity = document.getElementById('quantity').value;
		var menu_id = document.querySelector('input[name="menuid"]:checked').value;
		var group_id = document.querySelector('input[name="groupID"]:checked').value;
		var post_data = {
			 	action:'place_order',
				menu_id: menu_id,
				quantity: quantity,
				group_id: group_id,
			 	lowa_nonce: lowadata.nonce
			};
		$.post(lowadata.ajaxurl,post_data,function(response){
			if(response == 'finished') {
					alert("There is no way this alert message would occur");
			 	}else {
					alert(response);
					window.location.reload(true);
				}
		});
	});
	$('.menuID').click(function(){
		//alert('clicked');
		$('#qu').hide();
		var remaining =  $(this).data('val');
		$('#quantity').attr('max',remaining);
		$('#qu').show();
	});
	$('#close-account-form').submit(function(e){
		e.preventDefault();
		var group_id = document.querySelector('input[name="groupID"]:checked').value;
		alert(group_id);
		var post_data = {
			 	action:'finish_account',
				group_id: group_id,
			 	lowa_nonce: lowadata.nonce
			};
		$.post(lowadata.ajaxurl,post_data,function(response){
			if(response == 'finished') {
					alert("");
			 	}else {
			 		alert(response);
					window.location.reload(true);
				}
		});
	});
});
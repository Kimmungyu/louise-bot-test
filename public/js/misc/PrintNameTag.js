function print_name_tag_cafe(){
	document.getElementById('form-type-cafe').style.display ='block';
	document.getElementById('form-type-product').style.display ='none';
	document.getElementById('form-type-general').style.display ='none';
}
function print_name_tag_product(){
	document.getElementById('form-type-cafe').style.display ='none';
	document.getElementById('form-type-product').style.display ='block';
	document.getElementById('form-type-general').style.display ='none';
}
function print_name_tag_general(){
	document.getElementById('form-type-cafe').style.display ='none';
	document.getElementById('form-type-product').style.display ='none';
	document.getElementById('form-type-general').style.display ='block';
}
function showPrint(){ //send data by GET
	var checkNum;
	if(document.getElementById('check1').checked == true){ //cafe
		window.open("/misc/NamePage?" + document.getElementById("bread_name_deutsch").value + "&" +document.getElementById("bread_price").value + "&" + document.getElementById("bread_description").value + "&" + document.getElementById("bread_name_korean").value + "&1");
	}
	else if(document.getElementById('check2').checked == true){ //product
		window.open("/misc/NamePage?" + document.getElementById("product_name").value + "&" +document.getElementById("product_price").value + "&" + document.getElementById("product_description").value+"&2");
	}
	else{ //general
		window.open("/misc/NamePage?" + document.getElementById("general_content").value + "&3");
	}
}

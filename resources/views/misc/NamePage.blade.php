<!DOCTYPE html>
<html>
<head>
	<title>Name Tag</title>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html"; charset="euc-kr">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="{{ asset('js/misc/PrintNameTag.js') }}"></script>
	<style type="text/css" charset="utf-8" pageEncoding="UTF-8">
     @page {
  			display: inline-block;
  			position: relative;
  			width: 74mm;
  			height: 105mm;s
  			margin-bottom: 3mm;
  			border: 0.2mm solid black;
  			page-break-after: always;
  			padding: 5mm;
  			font-family: "Museo Slab", serif;
  			box-sizing: border-box;
  			vertical-align: top;
  	}
    .tag-wrap {
			display: inline-block;
			position: relative;
			width: 74mm;
			height: 105mm;
			margin-bottom: 3mm;
			border: 0.2mm solid black;
			page-break-after: always;
			padding: 5mm;
			font-family: "Museo Slab", serif;
			box-sizing: border-box;
			vertical-align: top;
		}
		.bread-tag-wrap .name {
			font-size: 21pt;
			font-weight: 700;
		}

		.bread-tag-wrap .price {
			font-size: 21pt;
			font-weight: 500;
		}

		.bread-tag-wrap .description {
			position: absolute;
			top: 35mm;
			font-size: 17pt;
			line-height: 150%;
		}

		.bread-tag-wrap .border {
			position: absolute;
			width: 63mm;
			bottom: 15mm;
			min-height: 1px;
			border-bottom: 1px solid black;
		}

		.bread-tag-wrap .name-kr {
			position: absolute;
			bottom: 4mm;
			font-size: 16pt;
			font-weight: 700;
			font-family: "맑은고딕", "Malgungothic", "굴림", sans-serif;
		}
    .product-tag-wrap .name {
			font-size: 17pt;
			font-weight: 700;
		}

		.product-tag-wrap .price {
			font-size: 17pt;
			font-weight: 500;
		}

		.product-tag-wrap .description {
			position: absolute;
			top: 27mm;
			font-size: 17pt;
			line-height: 150%;
		}

		.general-tag-wrap {
			display: table;
			text-align: center;
		}

		.general-tag-wrap .description {
			width: 63mm;
			height: 90mm;
			font-size: 16pt;
			line-height: 150%;
			text-align: center;
			display: flex;
		    justify-content: center; /* align horizontal */
		    align-items: center; /* align vertical */
		    word-break: break-all;
		}
	</style>
</head>
<body>
</body>
<script>
  $(document).ready(function(){
    var str = window.document.URL;
    console.log(str);
    var field1 = "";
    var field2 = "";
    var field3 = "";
		var field4 = "";
    var tagIndex = "";
    var arrHtml = [];

    for(var i=0; i<str.length; i++){
      if(str[i] == "?"){   //field1
        for(var j=i+1; j<str.length; j++){if(str[j] == "&"){i = j-1;break;}else{field1 += str[j];}}}
      if(str[i] == "&"){ //field2
        if(field2.length == 0){
          for(var j=i+1; j<str.length; j++){if(str[j] == "&"){i = j-1;break;}else{field2 += str[j];}}}
        else if(field3.length == 0){ //field3
          for(var j=i+1; j<str.length; j++){if(str[j] == "&"){i = j-1;break;}else{field3 += str[j];}}}
				else{for(var j=i+1; j<str.length; j++){if(str[j] == "&"){i = j;break;}else{field4 += str[j];}}}}
      tagIndex = str[str.length-1];
    }
    var compare = "%20"; // %20 parsing
    while(true){if(field1.indexOf(compare) > 0){field1 = field1.replace(compare, " ");}else{break;}}
		while(true){if(field3.indexOf(compare) > 0){field3 = field3.replace(compare, " ");}else{break;}}
		while(true){if(field4.indexOf(compare) > 0){field4 = field4.replace(compare, " ");}else{break;}}
    tagIndex = parseInt(tagIndex); //string to number convert

		//console.log(field4);
    //console.log(tagIndex);
		field4 = decodeURIComponent(field4);
    if(tagIndex == 1){ // bread / cake / cafe
      arrHtml.push("<div class=\"tag-wrap\">");
      arrHtml.push("<div class=\"bread-tag-wrap\">");
      arrHtml.push("<div class=\"name\">" + field1 + "</div>");
      arrHtml.push("<div class=\"price\">" + field2 + "€ </div>");
      arrHtml.push("<div class=\"description\">" + field3 + "</div>");
      arrHtml.push("<div class=\"border\"></div>");
      arrHtml.push("<div class=\"name-kr\">" + field4 + "</div>");
      arrHtml.push("</div>");
      arrHtml.push("</div>");
      $("body").html(arrHtml.join('')); //add form to NamePage.php page
    }
    else if(tagIndex == 2){ //shop product
      arrHtml.push("<div class=\"tag-wrap\">");
      arrHtml.push("<div class=\"product-tag-wrap\">");
      arrHtml.push("<div class=\"name\">" + field1 + "</div>");
      arrHtml.push("<div class=\"price\">" + field2 + "€ </div>");
      arrHtml.push("<div class=\"description\">" + field3 + "</div>");
      arrHtml.push("</div></div>");
      $("body").html(arrHtml.join('')); //add form to NamePage.php page
    }
    else{ //general
      arrHtml.push("<div class=\"tag-wrap\">");
      arrHtml.push("<div class=\"general-tag-wrap\">");
      arrHtml.push("<div class=\"description\">" + field1 + "</div>");
      arrHtml.push("</div></div>");
      $("body").html(arrHtml.join('')); //add form to NamePage.php page
    }
  });
</script>
</html>

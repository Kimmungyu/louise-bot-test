@extends('layouts.app')
@section('content')
<html id="print-wrap">
	<head>
		<title>Barcode Generator</title>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<script type="text/javascript" src="{{ asset('js/jquery-3.3.1.min.js')}}"></script>
		<script type="text/JavaScript" src="{{ asset('js/misc/js/jsBarcode.all.min.js')}}"></script>
		<style type="text/css">
		@page {
	        size:  auto;
        	margin: 0mm;
    	}
	    </style>
	</head>
	<body>

  </body>
	@section('scripts')
	<script>
		$(document).ready(function(){
      var str = window.document.URL;
			var arrProductName = "";
			var arrBarcode = "";
			var arrBarcodeQunatity = "";
			var arrPrice = 0.0;
			var arrHtml = [];

			for(var i=0; i<str.length; i++){
				if(str[i] == "?"){   //productName
			  	for(var j=i+1; j<str.length; j++){if(str[j] == "#"){i = j;break;}else{arrProductName += str[j];}}
				}
			  if(str[i] == "#"){ //Barcode
			    if(arrBarcode.length == 0){
			      for(var j=i+1; j<str.length; j++){if(str[j] == "#"){i = j-1;break;}else{arrBarcode += str[j];}}
					}
			    else{ //BarcodeQunantity
			    	for(var j=i+1; j<str.length; j++){if(str[j] == "#"){i = j;break;}else{arrBarcodeQunatity += str[j];}}
					}
				}
    		if(str[i] == "#" && arrProductName != "" && arrBarcode != "" && arrBarcodeQunatity != ""){
		       	for(var j=i+1; j<str.length; j++){if(str[j] == "#"){i = j;break;}else{arrPrice += str[j];}}
			 	}
			}

			var compare = "%20";
			while(true){
				if(arrProductName.indexOf(compare) > 0){
					arrProductName = arrProductName.replace(compare, " ");
				}
				else{
					break;
				}
			}
			arrBarcodeQunatity = parseInt(arrBarcodeQunatity); //string to number convert
			arrPrice = parseInt(arrPrice); //string to number convert

			var totalIndex = 0;
			for(var j=0; j<arrBarcodeQunatity; j++){
				totalIndex += 1;
				arrHtml.push("<div style=\"width:170px; height:130px; padding: 10px; page-break-after: always;\">");
				arrHtml.push("<svg class=\"barcode-branch\" data-barcode=\""+arrBarcode+"\" id=\"branch_" + totalIndex + "\"></svg>");
				arrHtml.push("<div style=\"margin: 0 10px 0 10px; font-size:9px; work-break:break-all; \">" + arrProductName + "</div>");
				arrHtml.push("<div style=\"margin: 4px 10px 0 10px; font-size:9px;\">"+ "Price : " + arrPrice + "</div>");
				arrHtml.push("</div>");
			}

			$("body").html(arrHtml.join(''));

			$(".barcode-branch").each(function(){
        console.log($(this).attr("id"))
				var id = "#"+$(this).attr("id");
				var branchNumber = $(this).attr("data-barcode");

				JsBarcode(id, branchNumber, {
					 lineColor: "black",
					 width:1.3,
					 height:60,
					 displayValue: false,
					 fontSize:12,
					 textMargin:1,
					 textPosition:"bottom"
				});
			});
		});
	</script>
	@endsection
</html>

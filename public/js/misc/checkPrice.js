var productObj = {};
var idx = 0;
var nowPage = 1;

function getProductPrice() {
  //findProduct($('#checkprice-search-type').val(), $('#checkprice-input').val());
  findDamiProduct($('#checkprice-search-type').val(), $('#checkprice-input').val());
}

function findProduct(option, keyword) {
  clearTable();
  spinnerPlay();
  $.get('findProduct/?option='+option+'&keyword='+keyword, function(d) {
    spinnerStop();
    $('#checkprice-input').val('');
    $('#checkprice-input').focus();
    if(d.products.length > 0){
      console.log(d.products.length);
      var num = d.products.length;
      addProductObject(d.products, num);
    }
    else {
      alert('No data found.');
    }
  });
}

function findDamiProduct(option, keyword){
  clearTable();
  spinnerPlay();
  $.get('findDamiProduct/?option='+option+'&keyword='+keyword, function(d) {
    spinnerStop();
    $('#checkprice-input').val('');
    $('#checkprice-input').focus();
    if(d.products.length > 0){
      console.log(d.products.length);
      var num = d.products.length;
      addProductObject(d.products, num);
    }
    else {
      alert('No data found.');
    }
  });
}

function addProductObject(product, num) {
  productObj = product;
  setCheckPriceTable(num);
}

function pageMovement(num, isNext){ // if isNext = 0, -> prev / 1, -> next
  if(isNext == 0){ //prev
    nowPage--;
    if(nowPage < 1){
      alert("First page.");
      nowPage = 1;
      return false;
    }
    idx = (nowPage-1)*30;
    setCheckPriceTable(num)
  }
  else{ //next
    if(idx == num) {
      alert("End of page.");
      return false;
    }
    else{
      nowPage++;
      setCheckPriceTable(num);
    }
  }
}

function setCheckPriceTable(num) {
  var totalOfPage = Math.ceil(num / 30);
  var str = "LOUISE"
  $('#checkprice-tbody').html('');
  if(totalOfPage < 2){
    for(var i = 0; i<num; i++){ //console.log(productObj[idx].isLouise);
      $('#checkprice-tbody').append('<tr><td class="dptd" style="text-align:center; ">' + productObj[idx].code + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].name + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].UVP + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].promo_UVP + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].avr_buy_price + '</td><td class="dptd" style="text-align:center; ">' + ((Number(productObj[idx].tax_rate)*100).toFixed(0)) + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].qty + '</td></tr>');
      $('#checkprice-tbody-mobile').append('<tr><td class="row-divider" width="105">Product Code</td><td class="dptd row-divider" style="text-align:center; ">' + productObj[idx].code + '</td></tr><tr><td>Product Name</td><td class="dptd" style="text-align:center; ">' + productObj[idx].name + '</td></tr><tr><td>DAMI UVP</td><td class="dptd" style="text-align:center; ">' + productObj[idx].UVP + '</td></tr><tr><td>Louise26 UVP</td><td class="dptd" style="text-align:center; ">' + productObj[idx].promo_UVP + '</td></tr><tr><td>Tax Rate</td><td class="dptd" style="text-align:center; ">' + ((Number(productObj[idx].tax_rate)*100).toFixed(0)) + '</td></tr><tr><td>Louise26 Stock</td><td class="dptd" style="text-align:center; ">' + productObj[idx].qty + '</td></tr>');
      idx++;
    }
  }
  else{
    var endNum = idx;
    for(var i = endNum; i<endNum+30; i++){
      if(idx == num){
        break;
      }
      $('#checkprice-tbody').append('<tr><td class="dptd" style="text-align:center; ">' + productObj[idx].code + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].name + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].UVP + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].promo_UVP + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].avr_buy_price + '</td><td class="dptd" style="text-align:center; ">' + ((Number(productObj[idx].tax_rate)*100).toFixed(0)) + '</td><td class="dptd" style="text-align:center; ">' + productObj[idx].qty + '</td></tr>');
      $('#checkprice-tbody-mobile').append('<tr><td>Product Name</td><td class="dptd" style="text-align:center; ">' + productObj[idx].code + '</td></tr><tr><td></td><td class="dptd" style="text-align:center; ">' + productObj[idx].name + '</td></tr><tr><td></td><td class="dptd" style="text-align:center; ">' + productObj[idx].UVP + '</td></tr><tr><td></td><td class="dptd" style="text-align:center; ">' + productObj[idx].promo_UVP + '</td></tr><tr><td></td><td class="dptd" style="text-align:center; ">' + productObj[idx].avr_buy_price + '</td></tr><tr><td></td><td class="dptd" style="text-align:center; ">' + ((Number(productObj[idx].tax_rate)*100).toFixed(0)) + '</td></tr><tr><td></td><td class="dptd" style="text-align:center; ">' + productObj[idx].qty + '</td></tr>');
      idx++;
    }
    $('#table-paginator').html('');
    $('#table-paginator').append('<button type="button" class="btn btn-primary ml15" style="width:250px; height:50px; font-size:22px; margin-right:200px;" onClick="pageMovement(' + num + ',0' + ')" >Prev</button>' + '</td>');
    $('#table-paginator').append('<span class="label label-primary" style="width:500px; height:100px; font-size:22px; margin-right:150px;" >' + nowPage + '  /  ' + totalOfPage + '</span>');
    $('#table-paginator').append('<button type="button" class="btn btn-primary ml15" style="width:250px; height:50px; font-size:22px;" onClick="pageMovement(' + num + ',1' + ')" >Next</button>' + '</td>');
  }
}

function clearTable() {
  productObj = {};
  idx = 0;
  nowPage = 1;
  $('#checkprice-tbody').html('');
  $('#checkprice-tbody-mobile').html('');
  $('#table-paginator').html('');
}

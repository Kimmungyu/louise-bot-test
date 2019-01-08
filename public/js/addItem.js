function searchItem() {
  var option = $('#additem-option').val();
  var keyword = $('#additem-name').val();
  if(option == 'none') {
    alert('Please select Search option');
    return;
  }
  if(keyword == '') {
    alert('Please fill the Name field');
    return;
  }
  spinnerPlay();
  findProduct(option, keyword).always(spinnerStop()).done(function(d){
    if(d.products != null){
      addItemTable(d.products);
    }
    else {
      alert('No data found.');
    }
  }).catch(function(err){
    console.log(err);
  });
}

function addItemTable(products) {
  var trStr = [];
  var itemCnt = 0;
  var louiseStock = 0;
  var damiStock = 0;
  $.each(products, function(key, obj) {
    var productCode = obj.code.length > 13 ? insertStr(obj.code, 13, '\n') : obj.code;
    var productPrice = obj.promo_UVP === undefined || Number(obj.promo_UVP) == 0 ? eEuro(obj.UVP) : eEuro(obj.promo_UVP);

    if(!$.isEmptyObject(obj.invens)) {
      louiseStock = obj.invens['BRANCH_INVEN_BAL_QUN'];
    }

    if(!$.isEmptyObject(obj.dami_invens)) {
      damiStock = obj.dami_invens['GOODS_INVEN_BAL_QUN'];
    }

    trStr.push('<tr>');
    trStr.push('<td class="dptd vmiddle" width="128px" style="text-align:center;">' + productCode + '</td>');
    trStr.push('<td class="dptd vmiddle" width="229px">' + obj.name + '</td>');
    trStr.push('<td class="dptd vmiddle" width="129px" style="text-align:center;">' + obj.ean + '</td>');
    trStr.push('<td class="dptd vmiddle" width="78px" style="text-align:center;">' + productPrice + ' €</td>');
    trStr.push('<td class="dptd vmiddle" width="116px" style="text-align:center;">' + louiseStock + ' / ' + damiStock + '</td>');
    trStr.push('<td class="dptd" width="80px" style="text-align:center;"><button class="btn btn-success" onclick="getProductWithProductCode(\'' + obj.code + '\')">Select</button></td>');
    trStr.push('</tr>');
    itemCnt++;
  });

  if(itemCnt < 4) {
    for(var i = 0; i < 4 - itemCnt;i++) {
      trStr.push('<tr><td class="dptd" colspan="7" width="763px"></td></tr>');
    }
  }
  $('#additem-data-area').html("");
  $('#additem-data-area').append(trStr.join(''));
}

function openAddItemPopup() {
  removeFocusFromBarcode();
  initAddItemPopup();
  $('#addItemPopup').show();
  $('#disable-bg').show();
}

function closeAddItemPopup() {
  $('#addItemPopup').hide();
  $('#disable-bg').hide();
  focusOnBarcode();
}

function initAddItemPopup() {
  $('#additem-option').val('none');
  $('#additem-name').val('');
  var trStr = [];
  for(var i=0;i<4;i++) {
    trStr.push('<tr><td class="dptd" colspan="5" width="763px"></td></tr>');
  }
  $('#additem-data-area').html("");
  $('#additem-data-area').append(trStr.join(''));
}

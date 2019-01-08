function openReprintPopup() {
  removeFocusFromBarcode();
  initReprintTable();
  $('#reprint-from-date').val('');
  $('#reprint-to-date').val(moment().format("YYYY-MM-DD"));
  $('#reprintPopup').show();
  $('#disable-bg').show();
}

function closeReprintPopup() {
  $('#reprintPopup').hide();
  $('#disable-bg').hide();
  focusOnBarcode();
  $("#barcode").focus();
}

function setReprintTable() {
  var trStr = [];
  var reprintObjSize = Object.keys(reprintObj).length;
  var rows = Object.keys(reprintObj).reduce(function(val, key) {
    var itemStr = '';

    if(reprintObj[key].order_detail.length > 0) {
      itemStr = reprintObj[key].order_detail.reduce(function(v, k){
        return v + k.product.name.substring(0 , 48) + ' (' + k.qty + ')<br/>';
      }, '');
    }
    else {
      if(reprintObj[key].voucher.length > 0) {
        itemStr = reprintObj[key].voucher.reduce(function(v, k){
          return v + 'Gutschein - ' + k.code + ' (' + eEuro(k.amount) + ')<br/>';
        }, '');
      }
    }

    val.push('<tr>');
    val.push('<td class="dptd" width="97px" style="text-align:center;">' + reprintObj[key].kasse.name + '</td>');
    val.push('<td class="dptd" width="167px" style="text-align:center;'+ (reprintObj[key].is_cancelled == 'Y' ? 'color:red;' : '') + '">' + (reprintObj[key].is_cancelled == 'N' ? reprintObj[key].receipt_num : reprintObj[key].cancelled_receipt_num)+ '</td>');
    val.push('<td class="dptd" width="345px" style="font-size:14px">' + itemStr + '</td>');
    val.push('<td class="dptd" width="59px" style="text-align:center;'+ (reprintObj[key].is_cancelled == 'Y' ? 'color:red;font-weight:bold' : '') + '">' + reprintObj[key].is_cancelled + '</td>');
    val.push('<td class="dptd" width="110px" style="text-align:center;">' + reprintObj[key].sales_price + '</td>');
    val.push('<td class="dptd" width="110px" style="text-align:center;"><button class="btn btn-success" onclick="reprintReceipt(\'' + reprintObj[key].id + '\');">PRINT</button></td>');
    val.push('</tr>');
    return val;
  }, []);

  if(reprintObjSize < 9) {
    for(var i = 0; i < 9 - reprintObjSize;i++) {
      rows.push('<tr><td class="dptd-sm" colspan="6" width="911px"></td></tr>');
    }
  }
  $('#reprint-area').html('');
  $('#reprint-area').append(rows.join(''));
}

function initReprintTable() {
  var trStr = [];
  for(var i = 0; i < 9;i++) {
    trStr.push('<tr><td class="dptd-sm" colspan="5" width="911px"></td></tr>');
  }
  $('#reprint-area').html('');
  $('#reprint-area').append(trStr.join(''));
}

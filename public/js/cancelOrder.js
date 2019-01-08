var isAllCash;
function initRefund() {
  $('#cancelOrder-receiptNum').val('');
  $('#cancelOrder-paymentMethod').text('-');
  $('.btn-group').children('.btn').removeClass('btn-warning');
  $('.card-cancel-yn').hide();
  if($('#cancel-item-chkAll').is(':checked')) {
    $('#cancel-item-chkAll').prop('checked', false);
  }

  refundOrderObj = {};
  cancelledProducts = [];
  initRefundTable();
}

function openCancelOrderPopup() {
  if(listPrice > 0) {
    alert('Unable to open Cancel menu during checkout process');
    return false;
  }
  if(originalReceiptNum != '') {
    alert('Please complete partial cancel process first');
    return false;
  }
  initRefund();
  removeFocusFromBarcode();
  $('#cancelOrderPopup').show();
  $('#disable-bg').show();
  $('#cancelOrder-receiptNum').focus();
}

function closeCancelOrderPopup() {
  enableRefundPartial();
  originalReceiptNum = '';
  $('#cancelOrderPopup').hide();
  $('#disable-bg').hide();
  focusOnBarcode();
  $("#barcode").focus();
}

function openCancelAllPopup() {
  if(!isCardCancellableSelected()) {
    return false;
  }
  var returnAmount = refundOrderObj.sales_price;

  if(cancelledProducts.length > 0) {
    var cancelledAmount = cancelledProducts.reduce(function(total, product){
      return total + ePrice(Number(product.sales_price) * Number(product.qty));
    }, 0);
    returnAmount -= cancelledAmount;
  }

  $('#refund-all-price').text(eEuro(returnAmount) + ' €');

  $('#cancelOrder-all-creditcard-block').hide();
  $('#cancelOrder-all-eccard-block').hide();
  $('#cancelOrder-all-cash-block').hide();
  $('#cancelOrder-all-voucher-block').hide();
  $('#refund-all-creditcard').val(0);
  $('#refund-all-eccard').val(0);
  $('#refund-all-cash').val(0);
  $('#refund-all-voucher').val(0);

  if($('#cancel-item-chkAll').is(':checked')) {
    $('#cancel-item-chkAll').prop('checked', false);
  }

  if(cardCancellable == 'Y') {
    if(Number(refundOrderObj.creditcard_amount) > 0) {
      $('#cancelOrder-all-creditcard-block').show();
      $('#refund-all-creditcard').val(eEuro(refundOrderObj.creditcard_amount));
      $('#cancelOrder-all-cash-block').show();
    }
    if(Number(refundOrderObj.eccard_amount) > 0) {
      $('#cancelOrder-all-eccard-block').show();
      $('#refund-all-eccard').val(eEuro(refundOrderObj.eccard_amount));
      $('#cancelOrder-all-cash-block').show();
    }
    if(Number(refundOrderObj.cash_amount) > 0) {
      $('#cancelOrder-all-cash-block').show();
      $('#refund-all-cash').val(eEuro(refundOrderObj.cash_amount));
    }
    if(Number(refundOrderObj.voucher_amount) > 0) {
      $('#cancelOrder-all-voucher-block').show();
      $('#refund-all-voucher').val(eEuro(refundOrderObj.voucher_amount));
      $('#cancelOrder-all-cash-block').show();
    }
  }
  else if(Number(refundOrderObj.voucher_amount) > 0) {
    $('#cancelOrder-all-voucher-block').show();
    $('#refund-all-voucher').val(eEuro(refundOrderObj.voucher_amount));
    $('#cancelOrder-all-cash-block').show();
    if(Number(refundOrderObj.cash_amount) > 0) {
      returnAmount -= refundOrderObj.voucher_amount;
      $('#refund-all-cash').val(eEuro(returnAmount));
    }
    if(Number(refundOrderObj.creditcard_amount) > 0 || Number(refundOrderObj.eccard_amount) > 0) {
      returnAmount -= refundOrderObj.voucher_amount;
      $('#refund-all-cash').val(eEuro(returnAmount));
    }
  }
  else{
    $('#cancelOrder-all-cash-block').show();
    $('#refund-all-cash').val(eEuro(returnAmount));
  }

  $('#disable-popup').show();
  $('#cancelOrder-all').show();
}

function closeCancelAllPopup() {
  isAllCash = 'N';
  $('#disable-popup').hide();
  $('#cancelOrder-all').hide();
}

function openCancelPartialPopup() {
  if(!isCardCancellableSelected()) {
    return false;
  }

  if(isProductSelected()) {
    $('#refund-partial-select-tab').show();
    $('#refund-partial-refund-tab').hide();
    setRefundPartialPopup();
    $('#disable-popup').show();
    $('#cancelOrder-partial').show();
  }
  else {
    alert('To cancel the order partially, please select at least one item on table');
  }
}

function closeCancelPartialPopup() {
  $('#disable-popup').hide();
  $('#cancelOrder-partial').hide();
  $('.refund-partial-price').css('text-decoration','');
  $('#added-master-coupon').text('');
  $('refund-master-coupon').val('');
  
  $('#refund-master-coupon').val('');
  $('#added-master-coupon').text('');
  
}

function openRefundTab() {
  var isRefundWithCoupon = false;
  console.log(couponObj);
  if(Number(refundAmount) === 0) {
    alert('No item is selected to cancel.');
    return false;
  }

  $.each(partialProducts, function(key, obj) {
    if(obj.product.category == "COUPON")
    {
      isRefundWithCoupon = true;
    }
  });
  console.log(isRefundWithCoupon);

  if(isRefundWithCoupon)
  {
    $('#cancelOrder-partial-usd-block').hide();
    $('#refund-partial-all-euro-btn').hide();
    $('#refund-partial-select-tab').hide();
    $('#refund-partial-refund-tab').show();
    $('.refund-partial-price').text(eEuro(refundAmount) + ' €');
    $('.refund-partial-price').css('text-decoration','line-through');
    // $('#new-coupon-insert').show();
    $('#coupon-caution').show();
    $('.refund-disable-coupon-price').show();
    $('.refund-disable-coupon-price').text(eEuro(refundAmount - couponObj.amount) + " €");
    $('#refund-partial-cash').val(eEuro(refundAmount - couponObj.amount));
  }
  else
  {
    $('#cancelOrder-partial-usd-block').hide();
    $('#refund-partial-all-euro-btn').hide();
    $('#refund-partial-select-tab').hide();
    $('#refund-partial-refund-tab').show();
    $('#refund-partial-cash').val(eEuro(refundAmount));

    $('#new-coupon-insert').hide();
    $('#coupon-caution').hide();
    $('.refund-disable-coupon-price').hide();
  }
}

function backToRefundSelectionTab() {
  $('#refund-partial-refund-tab').hide();
  $('#refund-partial-select-tab').show();
  $('.refund-partial-price').css('text-decoration','');
  if(masterCouponObj.amount != 0)
  {
    refundAmount += masterCouponObj.amount;
  }
  initMasterCouponObj();
  $('#refund-master-coupon').val('');
  $('#added-master-coupon').text('');
}

function setRefundPopup() {
  cardCancellable = '';
  enableRefundPartial();
  $('.btn-group').children('.btn').removeClass('btn-warning');
  $('.card-cancel-yn').hide();
  $('#cardCancellable-yes').prop('disabled', false);

  var rowCnt =0;
  var trStr = [];

  if(Number(refundOrderObj.creditcard_amount) > 0 || Number(refundOrderObj.eccard_amount) > 0) {
    $('.card-cancel-yn').show();
  }

  $.each(refundOrderObj.order_detail, function(key, obj) {
    var isAllCancelled = false;
    obj.cancellableQty = Number(obj.qty);

    if(cancelledProducts.length > 0) {
      var cProduct = cancelledProducts.filter(function(product){return product.item_code === obj.item_code});
      if(cProduct.length > 0) {
        obj.cancellableQty -= cProduct.reduce(function(t, v) {return t + Number(v.qty)}, 0);
        if(obj.cancellableQty === 0) {
          isAllCancelled = true;
        }
      }
    }

    trStr.push('<tr>');
    if(isAllCancelled || obj.product.category == 'COUPON') {
      trStr.push('<td width="50px" class="dptd-sm vmiddle" style="text-align:center"></td>');
    }
    else {
      trStr.push('<td width="50px" class="dptd-sm vmiddle" style="text-align:center"><input class="cancelOrder-item-checkbox" type="checkbox" data-productId="' + obj.item_code + '" data-maxCancelQty="' + obj.cancellableQty + '"/></td>');
    }
    trStr.push('<td width="440px" class="dptd-sm vmiddle">' + obj.product.name + '</td>');
    trStr.push('<td width="49px" class="dptd-sm vmiddle" style="text-align:center">' + obj.cancellableQty + ' / ' + obj.qty + '</td>');
    trStr.push('<td width="112px" class="dptd-sm vmiddle" style="text-align:right">' + eEuro(Number(obj.sales_price)) + ' €</td>');
    trStr.push('<td width="112px" class="dptd-sm vmiddle" style="text-align:right">' + eEuro(Number(obj.sales_price) * Number(obj.cancellableQty))+ ' €</td>');
    trStr.push('</tr>');
    rowCnt++;
  });

  if(cancelledProducts.length > 0) {
    isCardCancellable(document.getElementById("cardCancellable-no"), 'N');
    $('#cardCancellable-yes').prop('disabled', true);
    trStr.push('<tr><td class="dptd-sm" colspan="5" width="763px" style="text-align:center;font-weight:bold;">Cancelled Items...</td></tr>');
    rowCnt++;
    $.each(cancelledProducts, function(key, obj) {
      trStr.push('<tr style="text-decoration:line-through">');
      trStr.push('<td width="50px" class="dptd-sm vmiddle" style="text-align:center"></td>');
      trStr.push('<td width="440px" class="dptd-sm vmiddle">' + obj.product.name + '</td>');
      trStr.push('<td width="49px" class="dptd-sm vmiddle" style="text-align:center">' + obj.qty + '</td>');
      trStr.push('<td width="112px" class="dptd-sm vmiddle" style="text-align:right">' + eEuro(Number(obj.sales_price)) + ' €</td>');
      trStr.push('<td width="112px" class="dptd-sm vmiddle" style="text-align:right">' + eEuro(Number(obj.sales_price) * Number(obj.qty))+ ' €</td>');
      trStr.push('</tr>');
      rowCnt++;
    });
  }

  if(rowCnt < 5) {
    for(var i=0;i < 5 - rowCnt;i++) {
      trStr.push('<tr><td class="dptd-sm" colspan="5" width="763px"></td></tr>');
    }
  }

  $('#refund-main-area').html('');
  $('#cancelOrder-paymentMethod').text('');
  $('#cancelOrder-paymentMethod').text(setRefundPaymentObj().join(', '));
  $('#refund-main-area').append(trStr.join(''));
}

function initRefundTable() {
  var trStr = [];
  for(var i=0;i < 5;i++) {
    trStr.push('<tr><td class="dptd-sm" colspan="5" width="763px"></td></tr>');
  }
  $('.cancelOrder-item-checkbox').prop("checked", false);
  $('#refund-main-area').html('');
  $('#refund-main-area').append(trStr.join(''));
}

function isProductSelected() {
  refundQtyChangeProductId = [];
  $('.cancelOrder-item-checkbox').each(function(idx, el) {
    if($(el).is(':checked')){
      refundQtyChangeProductId.push($(el).attr('data-productId'));
    }
  });
  console.log(refundQtyChangeProductId);
  return refundQtyChangeProductId.length > 0 ? true : false;
}

function setRefundPartialPopup() {
  var productCnt = 0;
  var trStr = [];
  partialProducts = [];

  refundQtyChangeProductId.forEach(function(val) {
    $.each(refundOrderObj.order_detail, function(key, obj) {
      console.log(obj);
      if(val == obj.item_code) {
        trStr.push('<tr class="refund-partial-item-row" data-productId ="' + val + '">');
        trStr.push('<td width="320px" class="dptd-sm" style="font-size:12px">' + obj.product.name + '</td>');
        trStr.push('<td width="89px" class="dptd-sm refund-partial-unitprice" style="text-align:right;font-size:14px">' + eEuro(ePrice(obj.sales_price)) + ' €</td>');
        trStr.push('<td width="130px" class="dptd-sm" style="text-align:center"><input type="text" class="popup-sm-qty-input refund-partial-qty" onclick="openSmCalculator(this)" style="text-align:center" value="0" readonly> / ' + obj.cancellableQty + '</td>');
        trStr.push('<td width="90px" class="dptd-sm" style="text-align:right;font-size:14px"><span class="refund-partial-return-amount"> 0,00 €</span></td>');
        trStr.push('</tr>');
        partialProducts.push($.extend(true, {}, obj));
        productCnt++;
      }
    });
  });

  if(productCnt < 7) {
    for(var i=0;i < 7 - productCnt;i++) {
      trStr.push('<tr><td class="dptd-sm" colspan="5" width="518px"></td></tr>');
    }
  }

  refundAmount = 0;
  initPartialProducts();
  $('.refund-partial-price').text('0,00 €');
  $('#refund-partial-area').html('');
  $('#cancelOrder-paymentMethod').text('');
  $('#cancelOrder-paymentMethod').text(setRefundPaymentObj().join(', '));
  $('#refund-partial-area').append(trStr.join(''));
}

function initPartialProducts() {
  $.each(partialProducts, function(k, o) {
    o.qty = 0;
  });
}

function setRefundPaymentObj () {
  var paymentMethodArr = [];
  refundOrderObj.payment_method.split('').forEach(function(val, idx){
    if(val == 1) {
      if(idx == 0) {
        paymentMethodArr.push('CreditCard');
      } else if (idx == 1) {
        paymentMethodArr.push('EC Card');
      } else if (idx == 2) {
        paymentMethodArr.push('Cash');
      } else if (idx == 3) {
        paymentMethodArr.push('Voucher');
      }
    }
  });

  return paymentMethodArr;
}

function setbasketObj() {
  originalReceiptNum = refundOrderObj.receipt_num;
  $.each(refundOrderObj.order_detail, function(key, obj) {
    obj.product.UVP = ePrice(obj.product.promo_UVP === undefined || Number(obj.product.promo_UVP) === 0 ? obj.product.UVP : obj.product.promo_UVP);
    obj.product.tax_rate = Number(obj.product.tax_rate);
    basketObj[obj.item_code] = {"product":obj.product, "qty":obj.qty};
    if(Number(obj.discount_rate) > 0) {
      basketObj[obj.item_code].product.dc = Number(obj.discount_rate);
      basketObj[obj.item_code].product.discountedNetto = getNetto(obj.sales_price, obj.tax_rate);
      basketObj[obj.item_code].product.discountedUVP = ePrice(obj.sales_price);
    }
  });
  setMainTable();
}

function setCouponBasketObj() {
  originalReceiptNum = refundOrderObj.receipt_num;
  $.each(refundOrderObj.order_detail, function(key, obj) {
    if(obj.product.category != "COUPON")
    {
      obj.product.UVP = ePrice(obj.product.promo_UVP === undefined || Number(obj.product.promo_UVP) === 0 ? obj.product.UVP : obj.product.promo_UVP);
      obj.product.tax_rate = Number(obj.product.tax_rate);
      reBuyBasketObj[obj.item_code] = {"product":obj.product, "qty":obj.qty};
      if(Number(obj.discount_rate) > 0) {
        reBuyBasketObj[obj.item_code].product.dc = Number(obj.discount_rate);
        reBuyBasketObj[obj.item_code].product.discountedNetto = getNetto(obj.sales_price, obj.tax_rate);
        reBuyBasketObj[obj.item_code].product.discountedUVP = ePrice(obj.sales_price);
      }
    }
    else
    {
      addBasketForCoupon(obj.product);
      reBuyBasketObj[obj.item_code] = {"product":obj.product, "qty":obj.qty};
      // if(couponObj.type == 3)
      // {
      //   console.log('check');
      //   checkUseConditionVoucher();
      // }
    }
  });
}

function isCardCancellable(el, type) {
  cardCancellable = type;
  if(!$(el).hasClass('btn-warning')) {
    $(el).addClass('btn-warning');
    if($(el).siblings('.btn').hasClass('btn-warning')) {
      $(el).siblings('.btn').removeClass('btn-warning');
    }
  }
  if(cardCancellable === 'Y') {
    disableRefundPartial();
  }
  else {
    enableRefundPartial();
  }
}

function isCardCancellableSelected() {
  var result = true;
  if($('.card-cancel-yn').is(':visible') && cardCancellable === "") {
    result = false;
    alert('Please select Card Cancellable.');
  }
  return result;
}

function setReturnAmount($row, changedProduct) {
  var returnAmount = Number(changedProduct.sales_price) * changedProduct.qty;
  $row.html("");
  $row.text(eEuro(returnAmount) + ' €');
}

function setRefundAmount() {
  refundAmount = 0;

  checkCancelUseVoucher();
  console.log(couponObj);
  $('.refund-partial-item-row').each(function(idx, row) {
    var unitPrice = eNumber($(row).find('.refund-partial-unitprice').text().split(' ')[0]);
    var qty = Number($(row).find('.refund-partial-qty').val());
    refundAmount += unitPrice * qty;
  });
  


  var isCancelledCoupon = false;
  var isPartialProductsInCoupon = false;
  // var cancelledPrice = 0;
  // var totalRefundPrice = 0;

  $.each(cancelledProducts, function(key, obj) {
    if(obj.is_cancelled == "Y" && obj.product.category == "COUPON")
    {
      isCancelledCoupon = true;
    }
  });

  // console.log(refundOrderObj.sales_price - cancelledPrice - refundAmount);
  // if(eRound(refundOrderObj.sales_price - cancelledPrice - refundAmount,2) < 0)
  // {
  //   $.each(refundOrderObj.order_detail, function(key, obj) {
  //     if(obj.product.category == 'COUPON' && obj.is_cancelled == 'N') {
  //       obj.product.sales_price = couponObj.amount * -1;
  //       partialProducts.push($.extend(true, {}, obj));
  //     }
  //   });
  //   isPartialProductsInCoupon = true;
  // }

  $.each(partialProducts, function(key, obj) {
    if(obj.product.category == "COUPON")
    {
      isPartialProductsInCoupon = true;
    }
  });

  // console.log(isCancelledCoupon);
  // console.log(isPartialProductsInCoupon);
  if(couponObj.isUse == "N" && !isCancelledCoupon && !isPartialProductsInCoupon)
  {
    $.each(refundOrderObj.order_detail, function(key, obj) {
      if(obj.product.category == 'COUPON') {
        obj.product.sales_price = couponObj.amount * -1;
        partialProducts.push($.extend(true, {}, obj));
      }
    });
  }

  var totalCancelledItemCount = 0;
  var totalRefundItemCount = 0;
  var totalOrderedItemCount = 0;

  $.each(refundOrderObj.order_detail, function(key, obj) {
    totalOrderedItemCount += obj.qty;
  });

  $.each(cancelledProducts, function(key, obj) {
    if(obj.product.category != 'MASTERCOUPON')
    {
      totalCancelledItemCount += obj.qty;
    }
  });

  $.each(partialProducts, function(key, obj) {
    totalRefundItemCount += obj.qty;
  });

  $.each(cancelledProducts, function(key, obj) {
    if(obj.is_cancelled == "Y" && obj.product.category == "COUPON")
    {
      isCancelledCoupon = true;
    }
  });

  if( (totalOrderedItemCount-totalCancelledItemCount-totalRefundItemCount) <= 1)
  {
    $.each(refundOrderObj.order_detail, function(key, obj) {
      if(obj.product.category == 'COUPON' && !isCancelledCoupon) {
        // obj.product.sales_price = couponObj.amount * -1;
        partialProducts.push($.extend(true, {}, obj));
      }
    });

    $.each(cancelledProducts.order_detail, function(key, obj) {
      if(obj.product.category == 'MASTERCOUPON' && obj.is_cancelled == 'N') {
        // obj.product.sales_price = couponObj.amount * -1;
        partialProducts.push($.extend(true, {}, obj));
      }
    });
  }

  console.log(totalOrderedItemCount);
  console.log(totalCancelledItemCount);
  console.log(totalRefundItemCount);

  console.log(refundAmount);
  console.log(refundOrderObj);
  console.log(cancelledProducts);
  console.log(partialProducts);
  $('.refund-partial-price').text(eEuro(refundAmount) + ' €');
}

function enableRefundPartial() {
  $('.cancelOrder-item-checkbox').prop('disabled', false);
  $('#refund-item-selection-table').css('opacity', 1);
  $('#refund-partial-btn').prop('disabled', false);
}

function disableRefundPartial() {
  if($('#cancel-item-chkAll').is(':checked')) {
    $('#cancel-item-chkAll').prop('checked', false);
  }
  $('.cancelOrder-item-checkbox').prop("checked", false);
  $('.cancelOrder-item-checkbox').prop('disabled', true);
  $('#refund-item-selection-table').css('opacity', 0.5);
  $('#refund-partial-btn').prop('disabled', true);
}

function addMasterCoupon()
{
  var totalItemNum = 0;
  $.ajax({
    url:"product/getProductWithBarcode/"+$('#refund-master-coupon').val(),
    async:false
  }).done(function(d)
  {
    console.log(d);
    if(d.product != null) 
    {
      if(d.product.category == 'MASTERCOUPON')
      {
        $.ajax({
          url:"product/getCouponInfo/"+d.product.code,
          async:false
        }).done(function(d1)
        {
          console.log(d1);
          if(d1.ok)
          {
            if(d1.couponInfo[0].type == 1)
            {
              masterCouponObj['code'] = d1.couponInfo[0].code;
              masterCouponObj['type'] = d1.couponInfo[0].type;
              masterCouponObj['amount'] = d1.couponInfo[0].amount;
              masterCouponObj['use_condition'] = d1.couponInfo[0].use_condition;
              masterCouponObj['available'] = d1.couponInfo[0].available;
              masterCouponObj['isUse'] = 'N';
              $('#added-master-coupon').text(d.product.name);
              console.log(partialProducts);
              console.log(refundOrderObj);

              $.each(refundOrderObj.order_detail, function(key, obj) {
                ++totalItemNum;
              });

              console.log("itemnum = " + totalItemNum);
              console.log(d1);

              insertMasterCouponInfo = {
                'date' : refundOrderObj.date,
                'cashier_id' : refundOrderObj.cashier_id,
                'cancellableQty' : 1,
                'item_num' : ++totalItemNum,
                // 'is_cancelled' : 'N',
                'item_id' : d.product.id,
                'item_code' : d.product.code,
                'qty' : 1,
                'sales_price' : d1.couponInfo[0].amount,
                'tax_rate' : d.product.tax_rate,
                'discount_rate' : 0.00,
                'is_cancelled' : 'N',
                'kasse_id' : refundOrderObj.kasse_id,
                'netto' : d1.couponInfo[0].amount,
                'vat' : 0.00,
                'product' : d.product
              }
              

              $.each(partialProducts, function(key, obj) {
                if(obj.product.category == 'COUPON') {
                  console.log(obj.sales_price);
                  console.log(d1.couponInfo[0].amount);
                  console.log(obj.sales_price + d1.couponInfo[0].amount);
                  partialProducts.push($.extend(true, {}, insertMasterCouponInfo));
                }
              });
              $('.refund-disable-coupon-price').text(eEuro(refundAmount - couponObj.amount + insertMasterCouponInfo.sales_price) + " €");
              $('#refund-partial-cash').val(eEuro(refundAmount - couponObj.amount + insertMasterCouponInfo.sales_price));
              console.log(partialProducts);
            }
            else
            {
              $('#added-master-coupon').text('');
              alert("Not applicable Coupon.");
            }
          }
          else
          {
            $('#added-master-coupon').text('');
            alert("Please Check Coupon Code.")
          }
        });
      }
      else
      {
        $('#added-master-coupon').text('');
        alert("This is not a coupon.")
      }
    }
    else
    {
      $('#added-master-coupon').text('');
      alert("Please Check Barcode.");
    }
  });

  
  
  //   if(d.ok) 
  //   {
  //     if(d.couponInfo[0].code != masterCouponObj.code)
  //     {
  //       masterCouponObj['code'] = d.couponInfo[0].code;
  //       masterCouponObj['type'] = d.couponInfo[0].type;
  //       masterCouponObj['amount'] = d.couponInfo[0].amount;
  //       masterCouponObj['use_condition'] = d.couponInfo[0].use_condition;
  //       masterCouponObj['available'] = d.couponInfo[0].available;
  //       masterCouponObj['isUse'] = 'N';
  //     }
      
  //     switch(masterCouponObj['type'])
  //     {
  //       case 1:
  //         product.UVP = couponObj['amount'] * -1;
  //         couponObj['isUse'] = 'Y';
  //         break;
  //       case 2:
  //         discountRate = Number(couponObj['amount']/100);
  //         couponObj['isUse'] = 'Y';
  //         break;
  //       case 3:
  //         // product.promo_UVP = d.couponInfo[0].amount * -1;
  //         break;
  //     }
  //   }
  //   else
  //   {
  //     alert("Unavailable Discount Voucher Code.")
  //   }
  // });
}

function getProductWithBarcode(barcode) {
  var parsedBarcode = barcode.replace(/ß|-/g, '');
  if(parsedBarcode != null && parsedBarcode != '') {
    $.get("product/getProductWithBarcode/"+parsedBarcode, function(d) {
      if(d.product != null) {
        if(d.product.category == 'COUPON')
        {
          console.log(d.product);
          console.log(couponObj.code);
          if(!couponObj.length)
          {
            addBasketForCoupon(d.product);
            calcPaymentSumUp();
            addBasket(d.product);
          }
        }
        else
        {
          addBasket(d.product);
          $.each(basketObj, function(key, obj) {
            if(obj.product.category == 'COUPON')
            {
              calcPaymentSumUp();
            }
          });
          
        }
        
      }
      else {
        alert('Barcode( ' + parsedBarcode + ' ) does not exist');
      }
    });
  }
  else {
    alert('Invalid Input: ' + parsedBarcode);
  }
}

function getProductWithProductCode(productCode) {
  $.get("product/getProductWithProductCode/"+productCode, function(d) {
    if(d.product != null) {
      alert(d.product.name + ' is added');
      addBasket(d.product);
      $.each(basketObj, function(key, obj) {
        if(obj.product.category == 'COUPON')
        {
          calcPaymentSumUp();
        }
      });
    }
    else {
      alert('Database Error. Please try again.');
    }
  });

  console.log(basketObj);
  $.each(basketObj, function(key, obj) {
    if(obj.product.category == 'COUPON')
    {
      calcPaymentSumUp();
    }
  });
}

function findProduct(option, keyword) {
  return $.get('product/findProduct/?option='+option+'&keyword='+keyword);
}

function insertOrder() {
  var brutto19Sum = 0;
  var totalPrice = 0;
  var totalSalesPrice = 0;
  spinnerPlay();
  
  if(paymentObj.voucher[1].code !== '')
  {
    paymentObj.voucher_Sum.code = 'multiUse';
  }
  else
  {
    paymentObj.voucher_Sum.code = paymentObj.voucher[0].code;
  }

  $.each(basketObj, function(key, obj) {
    if(obj.product.dc !== undefined && obj.product.dc > 0) {
      totalSalesPrice += obj.product.discountedUVP * obj.qty;
      if(Number(obj.product.tax_rate) === 0.19) {
        brutto19Sum += obj.product.discountedUVP * obj.qty;
      }
    }
    else
    {
      if(obj.product.category != 'COUPON')
      {
        totalPrice += obj.product.UVP * obj.qty;
      }
      totalSalesPrice += obj.product.UVP * obj.qty;
      if(Number(obj.product.tax_rate) === 0.19) {
        brutto19Sum += obj.product.UVP * obj.qty;
      }
    }
  });

  console.log(brutto19Sum);
  console.log(totalPrice);
  console.log(totalSalesPrice);


  // brutto19Sum = eRound(brutto19Sum/totalPrice*totalSalesPrice, 2);
  // console.log(brutto19Sum);

  var data = {
    listPrice: listPrice,
    discountRate: discountRate,
    netPrice: netPrice,
    membership: membership,
    buyGroup: buyGroup,
    basket: basketObj,
    // brutto19Sum : brutto19Sum,
    payment: paymentObj,
    cashReceived: cashReceived,
    originalReceiptNum: originalReceiptNum != '' ? originalReceiptNum : ''
  };

  $.ajax({
    url:'/sales/insertOrder',
    type:'POST',
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: data,
    success: function(d){
      spinnerStop();
      if(d.ok) {
        if(!d.printerOK.ok) {
          alert('Unable to print. Please check the status of Printer.');
          console.log(d.printerOK.msg);
        }
        getCashSumByKasse();
        originalReceiptNum = '';
        closePaymentPopup();
        clearAll();
      }
      else {
        alert('Transaction Error.');
        console.log(d.msg);
      }
    }
  });
}

function searchOrder() {
  var receiptNum = $('#cancelOrder-receiptNum').val();
  reBuyBasketObj = {};
  if(receiptNum != null && receiptNum != '') {
    spinnerPlay();
    $.get("sales/getOrderByReceiptNum?receiptNum=" + receiptNum + "&kasseId=" + $('#cancelOrder-kasse').val(), function(d) {
      spinnerStop();
      if(!d.isValidReceiptNum) {
        alert('Receipt( ' + receiptNum + ' ) does not exist');
        return false;
      }
      console.log(d);
      if(d.isCancellable) {
        refundOrderObj = {};
        cancelledProducts = [];
        refundOrderObj = $.extend(true, {}, d.originalOrder);
        fullOriginalOrderObj = $.extend(true, {}, d.originalOrder);
        if(d.cancelledProducts.length > 0) {
          cancelledProducts = d.cancelledProducts.slice(0);
        }
        setCouponBasketObj(refundOrderObj);
        setRefundPopup();
      }
      else {
        alert('All products have cancelled.');
      }
    });
  }
  else {
    spinnerStop();
    alert('Invalid Input: ' + receiptNum);
  }
}

// 2018-11-08 WonkyoungLee
function searchOrderForVoucherCode(el, idx) {
  if(el.value != null && el.value != '') {
    $.get("sales/getVoucherByCode/" + el.value, function(d) {
      if(d.ok)
      {
        $.get("sales/getOrderById/" + d.voucher[0].order_id, function(d) {
          if(!d.isValidReceiptNum) {
            return false;
          }
          if(d.isCancellable) {
            refundOrderObj = {};
            cancelledProducts = [];
            refundOrderObj = $.extend(true, {}, d.originalOrder);
            if(d.cancelledProducts.length > 0) {
              cancelledProducts = d.cancelledProducts.slice(0);
            }
            refundVoucherOrderObj[idx] = refundOrderObj;
            //console.log(refundVoucherOrderObj);
            $($(el).nextAll()).children('#cancelOrder-paymentMethod').text(setRefundPaymentObj());
          }
        });
      }
    });
  }
}

function refundAllSuccess(type, isAllCash) {
  $('.refund-partial-price').css('text-decoration','');

  if(isAllCash == 'Y'){ //if user want refund to all cash
    isAllCash = 'N';
  }
  else{
  }

  if(!confirm('Do you want to proceed refund?')) {
    return false;
  }
//  spinnerPlay();

  $.each(refundOrderObj.order_detail, function(idx, obj)
  {
    if(obj.product.category == 'COUPON')
    {
      isUseCoupon = 'Y';
    }
  });

  var prefix = '#refund-' + type + '-';
  var isMasterCouponInCancelled = false;
  

  if(type === 'all') { // if user push ALL items Cancel button...
    if(isAllCash == 'Y'){ //if user want refund to all cash
      isAllCash = 'N';
      refundOrderObj.voucher_amount = 0;
      refundOrderObj.creditcard_amount = 0;
      refundOrderObj.eccard_amount = 0;
    }
    else{
      refundOrderObj.creditcard_amount = eNumber($(prefix + 'creditcard').val());
      refundOrderObj.eccard_amount = eNumber($(prefix + 'eccard').val());
      refundOrderObj.voucher_amount = eNumber($(prefix + 'voucher').val());
    }

    if(cancelledProducts.length > 0) {
      $.each(refundOrderObj.order_detail, function(key, obj) {
        var cProduct = cancelledProducts.filter(function(product){return product.item_code === obj.item_code});
        if(cProduct.length > 0) {
          obj.qty = Number(obj.qty) - cProduct.reduce(function(t, v) {return t + Number(v.qty)}, 0);
        }
      });
      refundOrderObj.order_detail = refundOrderObj.order_detail.filter(function(obj){return obj.qty > 0});
    }
  }
  else { //if partial refund...
    refundOrderObj.creditcard_amount = 0;
    refundOrderObj.eccard_amount = 0;
    refundOrderObj.voucher_amount = 0;
    refundOrderObj.order_detail = partialProducts.filter(function(obj){return obj.qty > 0});
  }

  refundOrderObj.cash_amount = eNumber($(prefix + 'cash').val());
  refundOrderObj.sales_price = Number(refundOrderObj.creditcard_amount) + Number(refundOrderObj.eccard_amount)  + Number(refundOrderObj.voucher_amount) + Number(refundOrderObj.cash_amount);
  console.log(refundOrderObj);
  $.each(refundOrderObj.order_detail, function(key, obj) {
    if(obj.product.category=='MASTERCOUPON')
    {
      obj.is_cancelled='N';
    }
    else
    {
      obj.is_cancelled='Y';
    }
  });

  $.each(refundOrderObj.order_detail, function(idx, obj)
  {
    if(obj.product.category == 'MASTERCOUPON')
    {
      $.each(cancelledProducts.order_detail, function(idx, chkobj)
      {
        if(chkobj.product.category == 'MASTERCOUPON' && chkobj.product.is_cancelled == 'N')
        {
          obj.is_cancelled = 'Y'
        }
      });
    }
  });

  var data = {order:refundOrderObj, cancelled:cancelledProducts, original:fullOriginalOrderObj };

  
  console.log(data);

  
  $.ajax({
    url:'/sales/cancelOrder',
    type:'PUT',
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: data,
    success: function(d){
//      spinnerStop();
      console.log(d);
      if(d.ok) {
        if(!d.printerOK.ok) {
          alert('Unable to print. Please check the status of Printer.');
          console.log(d.printerOK.msg);
        }
        getCashSumByKasse();
        if(cardCancellable === 'Y') {
          if(confirm('Do you want to list up the original Order?')){
            setbasketObj();
          }
        }
        if(type === 'all') {
          closeCancelAllPopup();
        }
        else {
          closeCancelPartialPopup();
        }
        closeCancelOrderPopup();
      }
      else {
        alert('Transaction Error.');
        console.log(d.msg);
      }
    }
  });
  originalReceiptNum = '';
  // clearAll();
}

function reprintReceipt(orderId) {
  spinnerPlay();
  $.ajax({
    url:'/sales/reprintReceipt',
    type:'POST',
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: {orderId:orderId},
    success: function(d){
      spinnerStop();
      if(!d.ok) {
        alert('Unable to print. Please check the status of Printer.');
        console.log(d.msg);
      }
    }
  });
}

function openCashier(data) {
  $.ajax({
    url:'/sales/openCashier',
    type:'POST',
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: data,
    success: function(d){
      spinnerStop();
      if(!d.ok) {
        alert('Unable to print. Please check the status of Printer.');
        console.log(d.msg);
      }
      else {
        getCashSumByKasse();
      }
    }
  });
}

function getOrdersWithDate() {
  $.get('sales/getOrdersWithDate/?frDate='+ $('#reprint-from-date').val() + '&toDate=' + $('#reprint-to-date').val() + '&kasseId=' + $('#reprint-kasse').val(), function(d) {
    if(d.orders.length > 0) {
      reprintObj = $.extend(true, {}, d.orders);
      setReprintTable();
    }
    else {
      alert('No Order found.');
      initReprintTable();
    }
  });
}

function addNewProduct(input) {
  $.ajax({
    url:'/product/addNewProduct',
    type:'POST',
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: input,
    success: function(d){
      if(d.ok) {
        alert('Product is added successfully.');
        closeProductPopup();
      }
      else {
        alert(d.msg);
        console.log(d.err);
      }
    }
  });
}

function getSalesSummary() {
  $.get('sales/getSalesSummary/?frDate='+ $('#sales-summary-from-date').val() + '&toDate=' + $('#sales-summary-to-date').val() + '&kasseId=' + $('#sales-summary-kasse').val(), function(d) {
    setSalesSummary(d.data);
  });
}

function productMigration() {
  $.get('product/productMigration', function(d) {
    console.log(d);
  });
}


function getCashSumByKasse() {
  $.get('sales/getCashSumByKasse', function(d) {
    setCashSum(d);
  });
}

function dailyClosing() {
  if(confirm('Do you wish to proceed closing?\nPlease make sure that cash in the box is the same with cash sum.')) {
    $.get('sales/dailyClosing', function(d) {
      if(d.ok) {
        alert('Feuerabend :*)');
      }
      else {
        alert('Please contact to IT team.');
        console.log(d.msg);
      }
    });
  }
}

function syncProduct() {
  if(!confirm('Synchronizing products will take several minutes.\nDo you still wish to run?')) {
    return false;
  }
  spinnerPlay();
  $.get('product/syncProduct', function(d) {
    spinnerStop();
    if(d.ok) {
      alert('Products are updated.');
    }
    else {
      console.log(d.msg);
      alert('Update is failed. Please contact to IT team.');
    }
  });
}


function createVoucher(data) {
  spinnerPlay();
  $.ajax({
    url:'/sales/createVoucher',
    type:'POST',
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: data,
    success: function(d){
      spinnerStop();
      if(d.ok) {
        closeVoucherPopup();
        alert('Voucher is created.');
        if(!d.printerOK.ok) {
          alert('Printer has a problem. Please reprint it. Later');
        }
      }
      else {
        alert('Duplicate Voucher Code.');
        console.log(d.msg);
      }

    }
  });
}

// 2018.10.22 WonkyoungLee
// function checkVoucherByCode(voucherCode) {

//   $.get("sales/checkVoucherByCode/"+voucherCode, function(d) {
//     if(d.ok) {
      
//       paymentObj.voucher.code = d.voucher[0].code;
//       paymentObj.voucher.max = ePrice(d.voucher[0].checkSum);

//       console.log(paymentObj.voucher.code);
//       console.log(paymentObj.voucher.max);

//       $('#payment-selection-voucher-checksum').val(eEuro(paymentObj.voucher.max));
//     }
//     else {
//       paymentObj.voucher.code = '';
//       paymentObj.voucher.max = 0;
//       $('#payment-selection-voucher-checksum').text('Invalid Code.');
//     }
//   });
// }

// 2018.10.22 WonkyoungLee
function checkVoucherByCode(elId) {
  var parentId = $(elId).parent();
  var childrens = $('#voucher-selection-tab').children();
  var paymentObjidx = -1;
  voucherCode = $(elId).val();
  
  $.each(childrens, function(idx, obj)
  {
    if(obj === parentId[0])
    {
      paymentObjidx = idx;
      return false;
    }
  });
  // 2018.10.25 WonkyoungLee if add
  if(paymentObjidx >= 0)
  {
    $.get("sales/checkVoucherByCode/"+voucherCode, function(d) {
      if(d.ok) {
        paymentObj.voucher[paymentObjidx-1].code = d.voucher[0].code;
        paymentObj.voucher[paymentObjidx-1].max = ePrice(d.voucher[0].checkSum);

        $($(elId).next()).val(eEuro(paymentObj.voucher[paymentObjidx-1].max));
        // $(parentId).find('#payment-selection-voucher-checksum').val(eEuro(paymentObj.voucher[paymentObjidx-1].max));
      }
      else {
        paymentObj.voucher[paymentObjidx-1].code = '';
        paymentObj.voucher[paymentObjidx-1].max = 0;
        $($(elId).next()).val('Invalid Code.');
        // alert('Invalid Code.');
      }
    });
  }
}

function addBasketForCoupon(product)
{
  $.ajax({
    url:"product/getCouponInfo/"+product.code,
    async:false
  }).done(function(d)
  {
    if(d.ok) 
    {
      if(d.couponInfo[0].code != couponObj.code)
      {
        delete couponObj;
        couponObj['code'] = d.couponInfo[0].code;
        couponObj['type'] = d.couponInfo[0].type;
        couponObj['amount'] = d.couponInfo[0].amount;
        couponObj['use_condition'] = d.couponInfo[0].use_condition;
        couponObj['available'] = d.couponInfo[0].available;
        couponObj['isUse'] = 'N';
      }
      
      switch(couponObj['type'])
      {
        case 1:
          product.UVP = couponObj['amount'] * -1;
          couponObj['isUse'] = 'Y';
          break;
        case 2:
          discountRate = Number(couponObj['amount']/100);
          couponObj['isUse'] = 'Y';
          break;
        case 3:
          // product.promo_UVP = d.couponInfo[0].amount * -1;
          break;
      }
    }
    else
    {
      alert("Unavailable Discount Voucher Code.")
    }
  });
}
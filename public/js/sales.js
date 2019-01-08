$(function() {
  // Date and clock
  $('#display-date').text(moment().format("DD/MM/YYYY"));

  setInterval(function() {
    $('#display-time').html(moment().format('HH:mm:ss'));
  }, 1000);

  $('#barcode').focus();
  focusOnBarcode();
  $('.datepicker').datepicker();
  getCashSumByKasse();
});

var basketObj = {};
var refundOrderObj = {};
var refundVoucherOrderObj = {};
var fullOriginalOrderObj = {};
var cancelledProducts = [];

var paymentObj = {};
var refundQtyChangeProductId = [];
var searchProducts = {};
var originalReceiptNum = '';
var reprintObj = {};
var membership = '';
var cardCancellable = '';

var listPrice = 0;
var discountRate = 0;
var netPrice = 0;
var tax = {};

var buyGroup = 0;
var isPartialPayment;
var cashReceived = 0;
var cashAmountRequired = ['1', '2', '3'];
var refundAmount = 0;
var partialProducts = [];
var couponObj = {};
var masterCouponObj = {};

var inputEl;

function addCash(amount) {
  $('#payment-transaction-amount').val(function(i, val) {
    return eEuro(Number(eNumber(val)) + parseInt(amount));
  });
}

// Popup and Tab control

function initPaymentPopup() {
//  discountRate = 0;
//  netPrice = 0;
//  buyGroup = '';
  removeFocusFromBarcode();
  cashReceived = 0;
  isPartialPayment = 'N';
  membership = '';
  initPaymentObj();
// 2018.10.23 WonkyoungLee
  initVoucherTab();

  $('#payment-text-netprice').text(eEuro(netPrice) + ' €');
  $('#payment-text-partial').text(eEuro(netPrice) + ' €');
  $('#tpad-text').show();
  $('#partial-price-text').show();

  $('#payment-transaction-amount').val(0);
  $('#payment-transaction-creditcard-type').val(0);
  $('#payment-selection-creditcard').val('');
  $('#payment-selection-eccard').val('');
  $('#payment-selection-cash').val('');
  $('#payment-selection-voucher').val('');

  $('#payment-selection-voucher-use').val('');
  $('#payment-selection-voucher-code').val('');
  $('#payment-selection-voucher-checksum').val('');

  $('#payment-selection-voucher').val('');
  $('#payment-selection-voucher-code').val('');
  $('#payment-selection-voucher-checksum').text('');

  if($('#partial-payment-creditcard').hasClass('btn-default')){
    $('#partial-payment-creditcard').removeClass('btn-default').addClass('btn-success').html('Accept');
  }

  if($('#partial-payment-eccard').hasClass('btn-default')){
    $('#partial-payment-eccard').removeClass('btn-default').addClass('btn-success').html('Accept');
  }

  $('#execute-payment').attr('onclick', '');
  $('#payment-transaction-tab').hide();
  $('#payment-cashsum-tab').hide();
  $('#payment-selection-tab').show();
  $('#voucher-selection-tab').hide();
  
  
}

function openPaymentPopup() {
  if(listPrice == 0) {
    alert('Please add items to proceed payment.');
  }
  else {
    initPaymentPopup();
    initPartialPaymentInput();
    // initPaymentAcceptButtons();
    $('#paymentPopup').show();
    $('#disable-bg').show();
  }
}

function closePaymentPopup() {
  focusOnBarcode();
  $('#paymentPopup').hide();
  $('#disable-bg').hide();
}

function calculatePartialMinusSum() {
  var partialSum = 0;
  $('#payment-selection-tab input').each(function(i, el){
    if(el.value != null && eNumber(el.value) > 0)
      partialSum += eNumber(el.value);
      // partialSum += Number(eNumber(el.value));
  });

  partialSum = partialSum.toFixed(2);
  
  $('#payment-text-partial').text(eEuro(netPrice - partialSum) + ' €');
}

//2018.10.23 WonkyoungLee
function calculatePartialMinusSumforVoucher() {
  var partialSum = 0;
  var objValue = 0;
  var maxAmount = 0;

  //2018.10.26 WonkyoungLee for restAll
  $('#payment-selection-tab input').each(function(i, el){
    if(el.value != null && eNumber(el.value) > 0)
      partialSum += Number(eNumber(el.value));
  });

  if($('.voucher-selection div').length > 1)
  {
    paymentObj.voucher_Sum.amount = 0;
    $('.voucher-selection div').each(function(i, el)
    {
      maxAmount = netPrice - partialSum;
      objValue = $(el).find('#payment-selection-voucher-use').val();
      if(objValue != null && eNumber(objValue) > 0)
      {     
        
        if(paymentObj.voucher[i-1].amount > paymentObj.voucher[i-1].max || Number(eNumber(objValue)) > maxAmount.toFixed(2))
        {
          $(el).find('#payment-selection-voucher-use').val('');
          alert("Please Check Amount.");
          return false;
        }
        else
        {
          partialSum += Number(eNumber(objValue));
          paymentObj.voucher[i-1].amount = Number(eNumber(objValue));
          paymentObj.voucher_Sum.amount += Number(paymentObj.voucher[i-1].amount);
          
        }
      }
    });
    paymentObj.voucher_Sum.amount = paymentObj.voucher_Sum.amount.toFixed(2);
  }
  $('#payment-text-partial').text(eEuro((netPrice - partialSum).toFixed(2)) + ' €');
}

//function openSelectionTab() {
//  initPaymentObj();
//  $('#partial-price-text').show();
//  $('#payment-selection-tab').show();
//}

function backToSelectionTab() {
  // var voucherCode = paymentObj.voucher.code;

  initPaymentObj();
  initPartialPaymentInput();
  initVoucherTab();
  isPartialPayment = 'N';
  $('#payment-transaction-amount').val(0);


  // if(voucherCode !== '') {
  //   checkVoucherByCode(voucherCode);    
  // }

  $('.btn-exec-payment').each(function(idx, obj){
    if($(obj).hasClass('btn-default')) {
      $(obj).removeClass('btn-default').addClass('btn-success').html('Accept');
    }
  });

    calculatePartialMinusSum();
    calculatePartialMinusSumforVoucher();


//  $('.payment-transaction-block').hide();
  $('#partial-payment-creditcard').hide();
  $('#partial-payment-eccard').hide();
  $('#execute-payment').removeAttr('onclick');
  $('#tpad-text').show();
  $('#partial-price-text').show();
  $('#payment-transaction-tab').hide();
  $('#payment-selection-tab').show();
  $('#voucher-selection-tab').hide();
}

function backToTransactionTab() {
  $('#payment-cashsum-cash').text('');
  $('#payment-cashsum-received').text('');
  $('#payment-cashsum-change').text('');
  $('#payment-cashsum-tab').hide();
  $('#payment-transaction-tab').show();
}

function openPaymentPartial() {
  isPartialPayment = 'Y';
  var partialPaymentMethod = [];
  var type,blockId,inputId= '';
  var sum = 0;
  $('#execute-payment').hide();
  $('#payment-selection-tab input').each(function(i, el){
    type,blockId,inputId= '';
    if(el.value != null && eNumber(el.value) > 0) {
      var pAmount = ePrice(eNumber(el.value));
      sum += pAmount;

      if(i == 0) {
        type = 'cash';
        paymentObj.cash.amount = pAmount;
        paymentObj.cash.status = '1';
        $('#execute-payment').text('Check Change');
        $('#execute-payment').attr('onclick', 'payWithCash();');
        $('#execute-payment').show();
      } else if(i == 1) {
        type = 'creditcard';
        paymentObj.creditCard.amount = pAmount;
        paymentObj.creditCard.status = '1';
        $('#partial-payment-creditcard').attr('onclick', 'payByCreditCard();');
        $('#partial-payment-creditcard').show();
      } else if(i == 2) {
        type = 'eccard';
        paymentObj.ecCard.amount = pAmount;
        paymentObj.ecCard.status = '1';
        $('#partial-payment-eccard').attr('onclick', 'payByEcCard();');
        $('#partial-payment-eccard').show();
      } else if(i == 3) {
        type = 'voucher';
        paymentObj.voucher.amount = pAmount;

        paymentObj.voucher_Sum.status = '2';

        $('#partial-payment-voucher').attr('onclick', 'payWithVoucher();');
        $('#partial-payment-voucher').show();
      }
      console.log(paymentObj);
      blockId = type + '-block';
      inputId = 'payment-transaction-' + type;
      $('#'+inputId).val(el.value);
      $('#'+blockId).show();
      console.log("openPaymentPartial()->pAmount : " + pAmount);
    }
  });


  console.log("Partial Price sum : " + sum);
  console.log("Partial Price netPrice : " + netPrice);

  if(sum !== netPrice) {

    alert('Partial Price sum is not the same with payment amount');
  }
  else {
    $('#tpad-text').hide();
    $('#partial-price-text').hide();
    $('#payment-selection-tab').hide();
    $('#payment-transaction-tab').show();
  }
}

function addBasket(product) {
  product.UVP = Number(product.promo_UVP === undefined || Number(product.promo_UVP) === 0 ? product.UVP : product.promo_UVP);
  product.tax_rate = Number(product.tax_rate);
  if(typeof basketObj[product.code] === "undefined"){
    basketObj[product.code] = {product:product, qty:1};
  }
  else {
    basketObj[product.code].qty += 1;
  }
  setMainTable();
}

function addReBuyBasket(product) {
  product.UVP = Number(product.promo_UVP === undefined || Number(product.promo_UVP) === 0 ? product.UVP : product.promo_UVP);
  product.tax_rate = Number(product.tax_rate);
  if(typeof reBuyBasketObj[product.code] === "undefined"){
    reBuyBasketObj[product.code] = {product:product, qty:1};
  }
  else {
    reBuyBasketObj[product.code].qty += 1;
  }
}

function setMainTable() {
  var trStr = [];
  var subTotal = 0;
  var tax = 0;
  var brutto7Sum = 0;
  var brutto19Sum = 0;
  var total = 0;
  var basketSize = 0;
  var couponPrice = 0;
  listPrice = 0;

  console.log(basketObj);

  if(couponObj.type == 3)
  {
    checkUseConditionVoucher();
  }

  $.each(basketObj, function(key, obj) {
    trStr.push('<tr>');
    trStr.push('<td class="dptd" width="465px">' + obj.product.name + '</td>');
    if(obj.product.dc !== undefined && obj.product.dc > 0) {
      trStr.push('<td class="dptd" width="73px" style="text-align:center;"><span style="color:#42f442">' + eRound(obj.product.dc * 100, 0) +' %</span></td>');
      trStr.push('<td class="dptd" width="73px" style="text-align:center;"><input type="text" class="popup-sm-qty-input" onclick="openIndexCalculator(\'qty\', \'' + key + '\')" style="width:50px;text-align:center" value="' + obj.qty + '" readonly></td>');
      trStr.push('<td class="dptd" width="150px" style="text-align:center;"><span style="text-decoration:line-through">' + eEuro(getNetto(obj.product.UVP, obj.product.tax_rate)) + ' €</span><br/>' + eEuro(obj.product.discountedNetto) + '€</td>');
      trStr.push('<td class="dptd" width="75px" style="text-align:center;">' + eRound(obj.product.tax_rate * 100, 0) + ' %</td>');
      trStr.push('<td class="dptd" width="150px" style="text-align:center;"><span style="text-decoration:line-through">' + eEuro(obj.product.UVP) + ' €</span><br/>' + eEuro(obj.product.discountedUVP) + ' €</td>');
      trStr.push('<td class="dptd" width="150px" style="text-align:center;"><span style="text-decoration:line-through">' + eEuro(obj.product.UVP * obj.qty) + ' €</span><br/>' + eEuro(obj.product.discountedUVP * obj.qty) + ' €</td>');
      total += obj.product.discountedUVP * obj.qty;
      if(Number(obj.product.tax_rate) === 0.07) {
        brutto7Sum += obj.product.discountedUVP * obj.qty;
      }
      if(Number(obj.product.tax_rate) === 0.19) {
        brutto19Sum += obj.product.discountedUVP * obj.qty;
      }
    }
    else {
      if(obj.product.category == 'COUPON' && couponObj.isUse == 'Y' && couponObj.type != 2)
      {
        obj.product.UVP = couponObj.amount * -1;
        couponPrice = couponObj.amount;
      }
      else if(obj.product.category == 'COUPON' && couponObj.isUse == 'N')
      {
        obj.product.UVP = 0.00;
      }
      trStr.push('<td class="dptd" width="73px" style="text-align:center;"></td>');
      if(obj.product.code == 'ADD_DLV_CHG') {
        trStr.push('<td class="dptd" width="73px" style="text-align:center;"><input type="text" class="popup-sm-qty-input" style="width:50px;text-align:center" value="-" disabled></td>');
      }
      else {
        trStr.push('<td class="dptd" width="73px" style="text-align:center;"><input type="text" class="popup-sm-qty-input" onclick="openIndexCalculator(\'qty\', \'' + key + '\')" style="width:50px;text-align:center" value="' + obj.qty + '" readonly></td>');
      }
      trStr.push('<td class="dptd" width="150px" style="text-align:center;">' + getNetto(obj.product.UVP, obj.product.tax_rate) + ' €</td>');
      trStr.push('<td class="dptd" width="75px" style="text-align:center;">' + eRound(obj.product.tax_rate * 100, 0) + ' %</td>');
      trStr.push('<td class="dptd" width="150px" style="text-align:center;">' + eEuro(obj.product.UVP) + ' €</td>');
      trStr.push('<td class="dptd" width="150px" style="text-align:center;">' + eEuro(obj.product.UVP * obj.qty) + ' €</td>');
      total += obj.product.UVP * obj.qty;
      if(Number(obj.product.tax_rate) === 0.07) {
        brutto7Sum += obj.product.UVP * obj.qty;
      }
      if(Number(obj.product.tax_rate) === 0.19) {
        brutto19Sum += obj.product.UVP * obj.qty;
      }
    }
    trStr.push('<td class="dptd" width="220px" style="text-align:center;"><button class="btn btn-warning" onclick="openIndexCalculator(\'dc\', \'' + key + '\')">DISCOUNT</button><button class="btn btn-default" style="margin-left:3px" onclick="removeProductOnBasket(\'' + key + '\')">REMOVE</button></td>');
    trStr.push('</tr>');
    listPrice += obj.product.UVP * obj.qty;
    basketSize++;
  });

  if(basketSize < 12) {
    for(var i = 0; i < 12 - basketSize;i++) {
      trStr.push('<tr><td class="dptd" colspan="8" width="1356px"></td></tr>');
    }
  }

  if(brutto7Sum > 0) {
    subTotal += getNetto(brutto7Sum, 0.07);
  }
  if(brutto19Sum > 0) {
    subTotal += getNetto(brutto19Sum, 0.19);
  }

  subTotal = getNetto(eRound(brutto19Sum/(total+couponPrice)*total,2), 0.19);
  subTotal += getNetto(total - eRound(brutto19Sum/(total+couponPrice)*total,2), 0.07);

  tax = total - subTotal;
  setSummary(subTotal, tax, total);
  $('#main-data-area').html("");
  $('#main-data-area').append(trStr.join(''));
}

function setSummary(subTotal, tax, total) {
  netPrice = ePrice(total);
  $('#main-sum-subtotal').text(eEuro(subTotal));
  $('#main-sum-tax').text(eEuro(tax));
  $('#main-sum-total').text(eEuro(total));
}

function removeProductOnBasket(productCode) {
  console.log(basketObj[productCode].product.category);
  console.log(discountRate);
  if(basketObj[productCode].product.category == 'COUPON')
  {
    console.log("discount0");
    discountRate = 0.00;
    calcPaymentSumUp();
  }
  delete basketObj[productCode];
  console.log(discountRate);
  setMainTable();
}

function discountAll(el, dc, discountGroup) {
  buyGroup = 0;
  discountRate = 0;
  if($(el).hasClass('active')) {
    $(el).removeClass('active');
    calcPaymentSumUp();
  }
  else {
    $('.discount-btn-group').removeClass('active');
    $(el).addClass('active');
    buyGroup = discountGroup;
    if(dc == 'custom') {
      openIndexCalculator('dcAll');
    } else {
      discountRate = Number(dc);
      calcPaymentSumUp();
    }
  }
}

function calcPaymentSumUp() {
  var noDiscount = {"low":[], "no":[]};
  netPrice = 0;
  $.each(basketObj, function(key, obj) {
    if(discountRate > 0) {
      if(obj.product.is_discountable == 'Y') {
        if(obj.product.isProductDC != 'Y') {
          obj.product.dc = discountRate;
          obj.product.discountedUVP = getDiscountedSalesPrice(obj.product.UVP, discountRate);
          obj.product.discountedNetto = getNetto(obj.product.discountedUVP, obj.product.tax_rate);

          if(obj.product.discountedUVP < ePrice(Number(obj.product.avr_buy_price) * 1.05)) {
            noDiscount['low'].push(obj.product.name);
          }
        }
      }
      else {
        if(obj.product.category != 'COUPON')
        {
          noDiscount['no'].push(obj.product.name);
        }
      }
    }
    else {
      if(obj.product.isProductDC != 'Y') {
        obj.product.dc = 0;
        obj.product.discountedUVP = 0;
        obj.product.discountedNetto = 0;
      }
    }
  });
  

  var warning = '';
  if(noDiscount['no'].length > 0) {
    warning += 'No Discount: \n' + noDiscount['no'].join('\n') + '\n';
  }
  if(noDiscount['low'].length > 0) {
    warning += 'Less than 5% margin: \n' + noDiscount['low'].join('\n') + '\n';
  }

  if(warning != '') {
    alert(warning);
  }
  setMainTable();
}

function checkUseConditionVoucher()
{
  var calTotal = 0;

  console.log(basketObj);
  $.each(basketObj, function(key, obj) {
    if(obj.product.dc !== undefined && obj.product.dc > 0) {
      calTotal += obj.product.discountedUVP * obj.qty;
    }
    else if(obj.product.category !== 'COUPON') {
      calTotal += obj.product.UVP * obj.qty;
    }
    else if(obj.product.UVP == 'COUPON') {
      obj.product.UVP = couponObj.amount * -1;
    }
  });
  
  if(calTotal >= couponObj.use_condition)
  {
    couponObj.isUse = 'Y';
  }
  else
  {
    couponObj.isUse = 'N';
  }
}

function checkCancelUseVoucher()
{
  var calTotal = refundOrderObj.sales_price;

  $.each(basketObj, function(key, obj) {
    if(obj.product.category == 'COUPON') {
      obj.product.UVP = obj.product.UVP * -1;
    }
  });

  $.each(partialProducts, function(idx, obj){
    calTotal -= obj.qty * obj.sales_price;
  });
  console.log(calTotal);
  console.log(couponObj.use_condition);
  if(calTotal >= couponObj.use_condition)
  {
    couponObj.isUse = 'Y';
  }
  else
  {
    couponObj.isUse = 'N';
  }

  // var calTotal = 0;

  // console.log(basketObj);
  // $.each(basketObj, function(key, obj) {
  //   if(obj.product.dc !== undefined && obj.product.dc > 0) {
  //     calTotal += obj.product.discountedUVP * obj.qty;
  //   }
  //   else if(obj.product.category !== 'COUPON') {
  //     calTotal += obj.product.UVP * obj.qty;
  //   }
  //   else if(obj.product.UVP > 0) {
  //     obj.product.UVP = obj.product.UVP * -1;
  //   }
  // });

  // $.each(partialProducts, function(idx, obj){
  //   calTotal -= obj.qty * obj.sales_price;
  // });
  
  // if(calTotal >= couponObj.use_condition)
  // {
  //   couponObj.isUse = 'Y';
  // }
  // else
  // {
  //   couponObj.isUse = 'N';
  // }
}

function setCashSum(d) {
  $('#nav-cash').text('');
  $('#nav-cash').text(eEuro(d));
}

function initPartialPaymentInput() {
  $('.payment-transaction-block').hide();
  $('#payment-transaction-creditcard').val(0);
  $('#payment-transaction-eccard').val(0);
  $('#payment-transaction-cash').val(0);
  // 2018.10.29 WonkyoungLee
  // $('#payment-transaction-voucher').val(0);
}

// 2018.10.23 WonkyoungLee
function initVoucherTab()
{
  var childrens = $('#voucher-selection-tab').children();
  $.each(childrens, function(idx, obj)
  {
    if(idx >= 1)
    {
      obj.remove();
    }
  })
  addNewVoucherPayment();
}

function initCouponObj() {
  couponObj = {
            "code":'',
            "type":'',
            "amount":'',
            "use_condition":'',
            "available":'',
            "isUse":'N'
  };
}

function initMasterCouponObj() {
  masterCouponObj = {
            "code":'',
            "type":'',
            "amount": 0,
            "use_condition":'',
            "available":'',
            "isUse":'N'
  };
}
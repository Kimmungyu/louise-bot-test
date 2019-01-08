function paymentAll(type) {
  var blockId = type + '-block';
  var inputId = 'payment-transaction-' + type;
  // 2018.10.26 WonkyoungLee
  isPartialPayment = 'N';

  $('#'+inputId).val(eEuro(netPrice));
  $('#'+blockId).show();
  $('#execute-payment').text('SUCCESS');

  if(type == 'creditcard') {
    paymentObj.creditCard.amount = netPrice;
    paymentObj.creditCard.status = '1';
    $('#eccard-block').hide();
    $('#cash-block').hide();
    $('#voucher-block').hide();
    $('#execute-payment').attr('onclick', 'payByCreditCard();');
  } else if(type == 'eccard') {
    paymentObj.ecCard.amount = netPrice;
    paymentObj.ecCard.status = '1';
    $('#creditcard-block').hide();
    $('#cash-block').hide();
    $('#voucher-block').hide();
    $('#execute-payment').attr('onclick', 'payByEcCard();');
  } else if(type == 'cash') {
    paymentObj.cash.amount = netPrice;
    paymentObj.cash.status = '1';
    $('#creditcard-block').hide();
    $('#eccard-block').hide();
    $('#voucher-block').hide();
    $('#execute-payment').text('Check Change');
    $('#execute-payment').attr('onclick', 'payWithCash();');
  } else if(type == 'voucher') {

    //  2018.10.23 WonkyoungLee
    $('#'+inputId).hide();
    $('#'+blockId).hide();
    $('#payment-text-partial').text(eEuro(netPrice) + ' €');
    $('#execute-payment').text('SUCCESS');
    $('#creditcard-block').hide();
    $('#cash-block').hide();
    $('#eccard-block').hide();
    $('#voucher-selection-tab').show();
    $('#execute-payment').attr('onclick', 'payWithVoucher();');
    paymentObj.voucher_Sum.status = '1';

    // $('#payment-text-partial').text(eEuro(netPrice - partialSum) + ' €');

    // 2018.10.23 WonkyoungLee
    // if(ePrice(netPrice) > paymentObj.voucher.max) {
    //   alert('Voucher amount is less than the payment amount');
    //   return false;
    // }
    // paymentObj.voucher.amount = netPrice;
    // paymentObj.voucher.status = '1';
    // $('#creditcard-block').hide();
    // $('#cash-block').hide();
    // $('#eccard-block').hide();
    // $('#execute-payment').attr('onclick', 'payWithVoucher();');

  }

// payment object 추가
  if(type == 'voucher')
  {
    $('#payment-selection-tab').hide();
    $('#execute-payment').show();
    $('#payment-transaction-tab').show();
  }
  else
  {
    $('#tpad-text').hide();
    $('#partial-price-text').hide();
    $('#payment-selection-tab').hide();
    $('#execute-payment').show();
    $('#payment-transaction-tab').show();
  }
}

function payByCreditCard(){
  if(Number(paymentObj.creditCard.type) == 0) {
    alert('Please select Credit Card Type.');
    return false;
  }
  // Status hardcoding due to no API connection
  paymentObj.creditCard.status = '2';
  // when Success
  if(isPartialPayment == 'N') {
    insertOrder();
  }
  else {
    paymentButtonChange('partial-payment-creditcard', true);
  }
}

function payByEcCard() {
  // Status hardcoding due to no API connection
  paymentObj.ecCard.status = '2';
  // when Success
  if(isPartialPayment == 'N') {
    insertOrder();
  }
  else {
    paymentButtonChange('partial-payment-eccard', true);
  }
}

// 2018.10.24 WonkyoungLee modify
function payWithVoucher() {
  var partialSum = 0;
  paymentObj.voucher_Sum.status = '2';

  calculatePartialMinusSumforVoucher();
  if(isPartialPayment == 'N' && netPrice == paymentObj.voucher_Sum.amount)
  {
    $.each(paymentObj.voucher, function(i, obj)
    {
      if(paymentObj.voucher[i].amount > 0 && paymentObj.voucher[i].max >= paymentObj.voucher[i].amount)
      {
        paymentObj.voucher[i].status = 2;
      }
      else
      {
        alert("Check Voucher Balance!");
        return false;
      }
    });
    insertOrder();
  }
  if(isPartialPayment == 'N' && netPrice > paymentObj.voucher_Sum.amount)
  {
    acceptPayVoucher();
  }
  else if(isPartialPayment == 'Y')
  {
    $.each(paymentObj.voucher, function(i, obj)
    {
      if(paymentObj.voucher[i].amount > 0)
      {
        paymentObj.voucher[i].status = 2;
      }
    });
  }
  else {
    alert("Voucher Amount Check!");
    return false;
  }
}

// 2018.10.24 WonkyoungLee modify
function acceptPayVoucher()
{
  console.log($('.voucher-selection div').length);
  if($('.voucher-selection div').length < 2)
  {
    initVoucherTab();
    $('#payment-selection-voucher').val('');
    paymentObj.voucher_Sum.amount = 0;
    for(var i = 0; i < paymentObj.voucher.length; i++)
    {
      paymentObj.voucher[i].status = 0;
      paymentObj.voucher[i].amount = 0;
      paymentObj.voucher[i].code = '';
    }
  }
  else
  {
    $('#payment-selection-voucher').val(eEuro(paymentObj.voucher_Sum.amount));
    calculatePartialMinusSum();
  }

  $('#payment-selection-tab').show();
  $('#voucher-selection-tab').hide();
  $('#payment-transaction-tab').hide();

}

function payWithCash() {
  if(isPartialPayment == 'Y') {
    if(!checkOtherPaymentStatus()){
      alert('Please clear other paymentmethod first');
      return false;
    }
  }
  if(paymentObj.cash.amount > Number(eNumber($('#payment-transaction-amount').val()))) {
    alert('Received amount is smaller than payment');
    return false;
  }
  showCashSum();
}

function checkOtherPaymentStatus() {
  if(paymentObj.creditCard.status == 1 || paymentObj.creditCard.status == -1) {
    return false;
  }
  if(paymentObj.ecCard.status == 1 || paymentObj.ecCard.status == -1) {
    return false;
  }

  if(paymentObj.voucher_Sum.status === 1 || paymentObj.voucher_Sum.status === -1) {

    return false;
  }
  return true;
}

function showCashSum() {
  openCashier({"open_type":"1", "reason_id":"5"});
  cashReceived = eNumber($('#payment-transaction-amount').val());
  $('#payment-cashsum-cash').text(eEuro(paymentObj.cash.amount)  + ' €');
  $('#payment-cashsum-received').text(eEuro(cashReceived) + ' €');
  $('#payment-cashsum-change').text(eEuro(cashReceived - Number(paymentObj.cash.amount)) + ' €');
  $('#payment-transaction-tab').hide();
  $('#payment-cashsum-tab').show();
}

function payWithCashSucess() {
  paymentObj.cash.status = '2';
  insertOrder();
}

function paymentButtonChange(elId, status) {
  if(status) {
    $('#' + elId).removeClass('btn-success').addClass('btn-default').html('<span class="glyphicon glyphicon-ok text-success"></span> Success');
  }
  isPartialPaymentDone();
}

function initPaymentObj() {
  paymentObj = {
    "creditCard":{"amount":0, "status":0, "type":0},
    "ecCard":{"amount":0, "status":0},
    "cash":{"amount":0, "status":0},

    "voucher_Sum":{"amount":0, "status":0, "code":'', "max":0},
    "voucher":[{"amount":0, "status":0, "code":'', "max":0},
              {"amount":0, "status":0, "code":'', "max":0},
              {"amount":0, "status":0, "code":'', "max":0},
              {"amount":0, "status":0, "code":'', "max":0}]

  };
}

function creditCardTypeChanged(el) {
  paymentObj.creditCard.type = $(el).val();
}


function restAll(elId) {
  var rest = Number(eNumber($('#payment-text-partial').text().split(' ')[0]));


  isPartialPayment = 'Y';

  if(elId === 'payment-selection-voucher')
  {
    $('#execute-payment').text('Accept');
    $('#creditcard-block').hide();
    $('#cash-block').hide();
    $('#eccard-block').hide();
    $('#voucher-selection-tab').show();
    $('#execute-payment').attr('onclick', 'acceptPayVoucher();');
    $('#payment-selection-tab').hide();
    $('#execute-payment').show();
    $('#payment-transaction-tab').show();

    $('#payment-selection-voucher').val('');
    calculatePartialMinusSum();
  }
  else
  {
    if(rest < netPrice && rest > 0)
    {
      if(elId === 'payment-selection-voucher' && rest > paymentObj.voucher.max)
      {
        alert('Voucher amount is less than the rest.');
        return false;
      }
      $('#'+elId).val(eEuro(rest));
      calculatePartialMinusSum();
    }
  }


}

// function restAll(elId, idx) {
//   var rest = Number(eNumber($('#payment-text-partial').text().split(' ')[0]));
//   if(rest < netPrice && rest > 0) {
//     if(elId === 'payment-selection-voucher' && rest > paymentObj.voucher.max) {
//       alert('Voucher amount is less than the rest.');
//       return false;
//     }
//     $('#'+elId).val(eEuro(rest));
//     calculatePartialMinusSum();
//   }
// }


function clearValue(elId) {
  $('#'+elId).val('');

  if(elId === 'payment-selection-voucher')
  {
    initVoucherTab();
    initPaymentObj();
  }
  calculatePartialMinusSum();
}

// 2018.10.22 Wonkyoung
function clearVoucherValue(elId) {
  // var parentId = $($(elId).parent()).parent();
  var parentId = $(elId).parent();
  var childrenId = $(parentId).children();
  var childrens = $('#voucher-selection-tab').children();
  var paymentObjidx = 0;

  
  $.each(childrens, function(idx, obj)
  {
    if(obj === parentId[0])
    {
      paymentObjidx = idx;
      return false;
    }
  });

  if(paymentObjidx > 0)
  {
    paymentObj.voucher[paymentObjidx-1].amount = 0;
    paymentObj.voucher[paymentObjidx-1].status = 0;
    paymentObj.voucher[paymentObjidx-1].code = '';
    paymentObj.voucher[paymentObjidx-1].max = 0;

    childrenId[1].value = '';
    childrenId[2].value = '';
    childrenId[3].value = '';
    // $(parentId).find('#payment-selection-voucher-code').val('');
    // $(parentId).find('#payment-selection-voucher-checksum').val('');
    // $(parentId).find('#payment-selection-voucher-use').val('');

    // 2018.10.26 WonkyoungLee
    calculatePartialMinusSumforVoucher();

  }
}

function isPartialPaymentDone() {
  return Object.keys(paymentObj).filter(function(key){return paymentObj[key].status === '1';}).length > 0 ? false : insertOrder();
}


// 2018.10.23 WonkyoungLee
function addNewVoucherPayment()
{
  if($('#voucher-selection-tab').children().length < 5)
  {
    $('#voucher-selection-tab').append('<div class="block"><label for="payment-selection-voucher" class="fs18 popup-label">Voucher</label><input type="text" class=" input-lg form-control popup-label-cell" id="payment-selection-voucher-code" onkeyup="checkVoucherByCode(this);" onclick="clearVoucherValue(this);"><input type="text" class="input-lg form-control popup-label-cell" id="payment-selection-voucher-checksum" readonly>"><input type="text" class="input-lg form-control popup-label-cell" id="payment-selection-voucher-use" onclick="voucherOpenCalculator(this);" readonly><button type="button" class="btn btn-lg btn-danger popup-search-button" id="payment-voucher-delete" style="width:50px" onclick="deleteVoucherPayment(this)">-</button></div>');
  }
  else
  {
    alert("Too many voucher.")
  }

}

// 2018.10.23 WonkyoungLee
function deleteVoucherPayment(elId)
{
  var parentId = $(elId).parent();

  $.each(paymentObj.voucher, function(i, obj)
  {
    paymentObj.voucher[i].amount = 0;
    paymentObj.voucher[i].status = 0;
    paymentObj.voucher[i].code = '';
    paymentObj.voucher[i].max = 0;
  });

  $(parentId).remove();
  // 2018.10.26 WonkyoungLee
  if(isPartialPayment === 'N')
  {
    calculatePartialMinusSumforVoucher();
  }
  else
  {
    calculatePartialMinusSum();
  }

  $('.voucher-selection div').each(function(i, el){
    if(i !== 0)
    {
      paymentObj.voucher[i-1].amount = $(el).find('#payment-selection-voucher-use').val();
      paymentObj.voucher[i-1].code = $(el).find('#payment-selection-voucher-code').val();
      paymentObj.voucher[i-1].max = $(el).find('#payment-selection-voucher-checksum').val();
    }
  });
  if($('#voucher-selection-tab').children().length < 2)
  {
    addNewVoucherPayment();
  }

}

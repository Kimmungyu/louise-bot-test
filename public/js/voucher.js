function openVoucherPopup () {
  removeFocusFromBarcode();
  initVoucherPopup();
  $('#disable-bg').show();
  $('#voucherPopup').show();

  $('#voucher-create-tab').show();
  $('#voucher-refund-tab').hide();

  $('#item-name').focus();
}

function closeVoucherPopup() {
  $('#disable-bg').hide();
  $('#voucherPopup').hide();
  focusOnBarcode();
}

// 2018.10.29 WonkyoungLee
function openRefundVoucherPopup () {
  removeFocusFromBarcode();
  initVoucherPopup();
  $('#disable-bg').show();
  $('#voucherPopup').show();
  $('#voucher-create-tab').hide();
  $('#voucher-refund-tab').show();
  $('.card-cancel-yn').hide();
  $('#voucher-payment').hide();
  $('#item-name').focus();
}

// 2018.10.29 WonkyoungLee
function closeRefundVoucherPopup() {
  $('#disable-bg').hide();
  $('#voucherPopup').hide();
  focusOnBarcode();
}


function initVoucherPopup() {
  $('.voucher-list input').val('');
  $('#voucher-total').val('');
  $('#voucher-cc-amount').val('');
  $('#voucher-cc-type').val('0');
  $('#voucher-ec-amount').val('');
  $('#voucher-cash-amount').val('');

  $('.voucher-refund-list input').val('');

  initPaymentObj();
}

//2018.10.30 WonkyoungLee update
function calculateTotal() {
  var total = 0;
  var amount = 0;
  $('.voucher-list').each(function(idx, obj) {
    if($(obj).find('#voucher-code').val() !== '') {
      if($(obj).find('#voucher-amount').val() !== '' && $(obj).find('#voucher-amount').val() !== null)
      {
        amount = eNumber($(obj).find('#voucher-amount').val());
        total += amount;
      }
    }
  });
  $('#voucher-total').val(eEuro(total));
}

//2018.10.18 WonkyoungLee
function checkVoucherCode(el)
{
  var code = el.value;
  if( code !== '')
  {
    $.get('sales/checkPossibleCode/'+code, function(d)
      {
        if(d.ok === 1 && d.count === 1)
        {
          el.value='';
          el.focus();
          alert(code + " is Duplicate Code");
        }
      });
  }
}

//2018.10.28 WonkyoungLee
function checkRefundVoucher(el)
{
  var code = el.value;
  var refundIdx = 0;

  $('.voucher-refund-list').each(function(idx, obj)
  {
    console.log(idx);
    console.log($(obj).children('#voucher-refund-code').val());
    if($(obj).children('#voucher-refund-code').val() === code)
    {
      refundIdx = idx;
    }
  });

  if( code !== '')
  {
    $.get("sales/checkVoucherByCode/" + code, function(d){
      if(d.ok)
      {
        $($(el).next()).next().val(d.voucher[0].checkSum);
        searchOrderForVoucherCode(el, refundIdx);
        console.log(refundVoucherOrderObj);
      }
      else
      {
        $($(el).next()).next().val('Invalid Code');
      }
    });
  }
  
}

// 2018.11.08 WonkyoungLee
function refundVoucherProcess()
{
  $('.voucher-refund-list').each(function(idx, el){    
    console.log($($(el).children('#voucher-refund-code')).val());
  });

  openCancelAllPopup();
}

function validateVoucher() {
  // input Numeric or empty check
  var vouchers = [];
  var faceAmount = 0;

  var isListErr;
  var totalAmount = eNumber($('#voucher-total').val());

  $('.voucher-list').each(function(idx, obj) {
    var code = $(obj).find('#voucher-code').val();
    var amount = eNumber($(obj).find('#voucher-amount').val());


//    var dc = $(obj).find('#voucher-dc').val() !== '' ? eRound(Number($(obj).find('#voucher-dc').val()) / 100, 2) : 0;
    if(code !== '') {
      if(amount === '' || Number(amount) === 0) {
        isListErr = true;
        alert('Please fill the amount on ' + (idx + 1) + ' row.');
        return false;
      }
    }

    if(Number(amount) > 0) {
      if(code === '') {
        isListErr = true;
        alert('Voucher code is missed on ' + (idx + 1) + ' row.');
        return false;
      }
    }

    vouchers.push({"code":code, "amount":amount, "dc":0});
    faceAmount += ePrice(amount);
  });

  if(isListErr) {
    return false;
  }

  if(!$.isNumeric(totalAmount) || Number(totalAmount) === 0 ){

    alert("Please fill Amount");
    return false;
  }

  // Sum - Payment
  var paymentSum = 0;
  $('.voucher-payment').each(function(idx, obj) {

    paymentSum += $(obj).val() === '' ? 0 : ePrice(eNumber($(obj).val()));
  });

  if(ePrice(totalAmount) !== paymentSum) {

    alert("Payment Amount is not the same with Sales Price.");
    return false;
  }

  // Credit Card Type check
  if($('#voucher-cc-amount').val() !== '' && $('#voucher-cc-type').val() === "0"){
    alert("Please select credit card type.");
    return false;
  }

  // send Ajax call
  if(confirm('Do you wish to proceed the payment?')) {
    vouchers = vouchers.filter(function(v){return v.code !== ''});
    createVoucher({
      "vouchers": vouchers,
      "original_price": faceAmount,
      "sales_price": paymentSum,

      "cc_amount": ePrice(eNumber($('#voucher-cc-amount').val())),
      "cc_type": $('#voucher-cc-type').val(),
      "ec_amount": ePrice(eNumber($('#voucher-ec-amount').val())),
      "cash_amount": ePrice(eNumber($('#voucher-cash-amount').val()))

    });
  }
}



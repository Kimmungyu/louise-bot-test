
// calculator start
function setCalculatorValue(val) {
  var calculatorInput = $('#calculator-value').val();

  if(val == '.') {
    if(calculatorInput.indexOf(".") > -1) {
      alert("Can't use more than one decimal point.");
      return false;
    }
  }

  if(calculatorInput.length == 1 && calculatorInput == 0) {
    if(val != '.') {
      calculatorInput = '';
    }
  }

  if(val == 'del') {
    if(calculatorInput.length < 2) {
      $('#calculator-value').val(0);
    }
    else {
      $('#calculator-value').val(calculatorInput.substring(0, calculatorInput.length-1));
    }
  } else {
    if(calculatorInput.split(".")[1] != null && calculatorInput.split(".")[1].length == 2) {
      alert("Can't add more decimal.");
      return false;
    }
    $('#calculator-value').val(calculatorInput + val);
  }
}

function insertCalculatorValue() {

  if($(inputEl).attr('id') === 'payment-selection-voucher')
  {
    var parentId = $(inputEl).parent();
    var childrens = $('#voucher-selection-tab').children();
    var paymentObjidx = 0;
    $.each(childrens, function(idx, obj)
    {
      if(obj === parentId[0])
      {
        paymentObjidx = idx;
        return false;
      }
      else
      {
        paymentObjidx = -1;
      }
    });

    if(ePrice($('#calculator-value').val()) > paymentObj.voucher.max) {
      alert("Voucher Amount is less than the set value.");
      return false;
    }
  }
  inputEl.value = eEuro($('#calculator-value').val());
  closeCalculator();
}

//2018.10.22 WonkyoungLee
function voucherOpenCalculator(el)
{
  if($('#payment-selection-voucher-code').val() !== '')
  {
    if($('#payment-selection-voucher-checksum').val() !== '')
    {
      openCalculator(el);
    }
    else
    {
      alert("Check Voucher Code.")
    }
  }
  else
  {
    alert("Insert Voucher Code.")
  }
}

function openCalculator(el) {
  $('#disable-popup').show();  
  inputEl = el;
  $('#calculator-value').val(0);
  $('.dapos-popup').css({'margin-left':'-600px'});
  $('#calculatorPopup').show();
}



function closeCalculator() {
  $('#disable-popup').hide();
  if($('#payment-selection-tab').is(':visible')){
    console.log('calculatePartialMinusSum');
    calculatePartialMinusSum();    
  }
  // 2018.10.23 WonkyoungLee
  else if($('#voucher-selection-tab').is(':visible')){
    console.log('calculatePartialMinusSumforVoucher');
    calculatePartialMinusSumforVoucher();
  }

  // 2018.10.16 WonkyoungLee add
  if($('#voucher-create-tab').is(':visible')){
    console.log('calculateTotal');
    calculateTotal();
  }  
  inputEl = {};
  $('#calculatorPopup').hide();
  $('.dapos-popup').css({'margin-left':'-420px'});
}

function openCalculatorWithValue(el) {
  inputEl = el;
  $('#calculator-value').val(eNumber(el.value));
  $('.dapos-popup').css({'margin-left':'-600px'});
  $('#calculatorPopup').show();
}

// calculator end
// calculator sm starts

function setSmCalculatorValue(val) {
  var calculatorInput = $('#calculator-sm-value').val();

  if(val == '.') {
    if(calculatorInput.indexOf(".") > -1) {
      alert("Can't use more than one decimal point.");
      return false;
    }
  }

  if(calculatorInput.length == 1 && calculatorInput == 0) {
    if(val != '.') {
      calculatorInput = '';
    }
  }

  if(val == 'del') {
    if(calculatorInput.length < 2) {
      $('#calculator-sm-value').val(0);
    }
    else {
      $('#calculator-sm-value').val(calculatorInput.substring(0, calculatorInput.length-1));
    }
  } else if (val == 'clear') {
      $('#calculator-sm-value').val(0);
    }
    else {
    if(calculatorInput.split(".")[1] != null && calculatorInput.split(".")[1].length == 2) {
      alert("Can't add more decimal.");
      return false;
    }
    $('#calculator-sm-value').val(calculatorInput + val);
  }
}

function insertSmCalculatorValue() {
  var val = $('#calculator-sm-value').val();
  var $inputEl = $(inputEl);

  if($inputEl.hasClass('refund-partial-qty')) {
    var productId = $inputEl.parents('tr').attr('data-productId');
    var originalProduct = refundOrderObj.order_detail.find(function(product){
      return product.item_code === productId;
    });
    var changedProduct = partialProducts.find(function(product){
      return product.item_code === productId;
    });

    if(val > originalProduct.cancellableQty) {
      alert("You can't cancel over the original quality.");
      return false;
    }
    else {
      var $row = $inputEl.parents('tr').find('.refund-partial-return-amount');
      changedProduct.qty = Number(val);
      $inputEl.val(val);
      setReturnAmount($row, changedProduct);
      setRefundAmount();
    }
  }
  else {
    $inputEl.val(val)
  }

  closeSmCalculator();
}

function openSmCalculator(el) {
  inputEl = el;
  $('#disable-all-popup').show();
  $('#calculator-sm-value').val(0);
  $('#smCalculatorPopup').show();
}

function closeSmCalculator() {
  inputEl = {};
  $('#disable-all-popup').hide();
  $('#smCalculatorPopup').hide();
}

// sm calculator for cancellation

function setSmCalculatorCValue(val) {
  var calculatorInput = $('#calculator-sm-cancel-value').val();

  if(val == '.') {
    if(calculatorInput.indexOf(".") > -1) {
      alert("Can't use more than one decimal point.");
      return false;
    }
  }

  if(calculatorInput.length == 1 && calculatorInput == 0) {
    if(val != '.') {
      calculatorInput = '';
    }
  }

  if(val == 'del') {
    if(calculatorInput.length < 2) {
      $('#calculator-sm-cancel-value').val(0);
    }
    else {
      $('#calculator-sm-cancel-value').val(calculatorInput.substring(0, calculatorInput.length-1));
    }
  } else if (val == 'clear') {
      $('#calculator-sm-cancel-value').val(0);
    }
    else {
    if(calculatorInput.split(".")[1] != null && calculatorInput.split(".")[1].length == 2) {
      alert("Can't add more decimal.");
      return false;
    }
    $('#calculator-sm-cancel-value').val(calculatorInput + val);
  }
}

function insertSmCalculatorCValue() {
  inputEl.value = eEuro($('#calculator-sm-cancel-value').val());
  if($(inputEl).hasClass('refund-all')) {
    // 2018.10.16 Wonkyoun Lee This function is not defined
    // validateRefundAmount('all'); 
  }
  else if($(inputEl).hasClass('refund-partial')) {
    // 2018.10.16 Wonkyoun Lee This function is not defined
    // validateRefundAmount('partial');
  }
  closeSmCalculatorC();
}

function openSmCalculatorC(el) {
  inputEl = el;
  $('#disable-all-popup').show();
  $('#calculator-sm-cancel-value').val(0);
  $('#smCalculatorCancelPopup').show();
}

function closeSmCalculatorC() {
  inputEl = {};
  $('#disable-all-popup').hide();
  $('#smCalculatorCancelPopup').hide();
}

// index calculator

function openIndexCalculator(prop, productCode) {
  $('#calculator-index-value').val(0);
  $("#insert-index-val").off("click");
  $('#insert-index-val').click(function() {
    insertIndexCalculatorValue(prop, productCode);
  });

  $('#disable-bg').show();
  $('#indexCalculatorPopup').show();
}

function closeIndexCalculator() {
  $('#disable-bg').hide();
  $('#indexCalculatorPopup').hide();
}

function setIndexCalculatorValue(val) {
  var calculatorInput = $('#calculator-index-value').val();

  if(val == '.') {
    if(calculatorInput.indexOf(".") > -1) {
      alert("Can't use more than one decimal point.");
      return false;
    }
  }

  if(calculatorInput.length == 1 && calculatorInput == 0) {
    if(val != '.') {
      calculatorInput = '';
    }
  }

  if(val == 'del') {
    if(calculatorInput.length < 2) {
      $('#calculator-index-value').val(0);
    }
    else {
      $('#calculator-index-value').val(calculatorInput.substring(0, calculatorInput.length-1));
    }
  } else if (val == 'clear') {
      $('#calculator-index-value').val(0);
    }
    else {
    if(calculatorInput.split(".")[1] != null && calculatorInput.split(".")[1].length == 2) {
      alert("Can't add more decimal.");
      return false;
    }
    $('#calculator-index-value').val(calculatorInput + val);
  }
}

function insertIndexCalculatorValue(prop, productCode) {
  var calculatorVal = Number($('#calculator-index-value').val());

  if(prop == 'dc') {
    if(basketObj[productCode].product.is_discountable == 'Y') {
      if(calculatorVal === 0) {
        basketObj[productCode].product.isProductDC = 'N';
        basketObj[productCode].product.dc = 0;
        basketObj[productCode].product.discountedUVP = 0;
        basketObj[productCode].product.discountedNetto = 0;
      }
      else {
        basketObj[productCode].product.isProductDC = 'Y';
        basketObj[productCode].product.dc = ePrice(calculatorVal / 100);
        basketObj[productCode].product.discountedUVP = ePrice(basketObj[productCode].product.UVP * (1 - basketObj[productCode].product.dc));
        basketObj[productCode].product.discountedNetto = getNetto(basketObj[productCode].product.discountedUVP,  basketObj[productCode].product.tax_rate);
        if(basketObj[productCode].product.discountedUVP < ePrice(Number(basketObj[productCode].product.avr_buy_price) * 1.05)) {
          alert('Margin is less than 5%');
        }
      }
      setMainTable();
    }
    else {
      alert('This product is unable to discount');
    }
  } else if(prop == 'qty') {
    basketObj[productCode].qty = calculatorVal;
    setMainTable();
  } else if(prop == 'dcAll') {
    discountRate = ePrice(calculatorVal / 100);
    calcPaymentSumUp();
  } else if (prop == 'delivery') {
    addBasket({
      'code' : productCode,
      'name' : 'Delivery Cost',
      'is_discountable' : 'N',
      'tax_rate' : 0,
      'UVP' : calculatorVal
    });
//    basketObj[productCode][product]['품목코드'] = productCode;
//    basketObj[productCode][product]['품목명'] = 'Delivery Cost';
//    basketObj[productCode][product]['할인적용여부'] = 'N';
//    basketObj[productCode][product]['UVP'] = Number($('#calculator-index-value').val());
//    setMainTable();
  }
//  inputEl.value = $('#calculator-sm-value').val();
  closeIndexCalculator();
}

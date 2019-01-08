function openProductPopup() {
  initProductPopup();
  removeFocusFromBarcode();
  $('#disable-bg').show();
  $('#productPopup').show();
}

function closeProductPopup() {
  $('#disable-bg').hide();
  $('#productPopup').hide();
  focusOnBarcode();
}

function validateProduct() {
  if($('#product-barcode').val() == '' || $('#product-barcode').val() == null) {
    alert('Barcode is required');
    return false;
  }
  if($('#product-code').val() == '' || $('#product-code').val() == null) {
    alert('Product Code is required');
    return false;
  }
  if($('#product-uvp').val() == '' || $('#product-uvp').val() == null) {
    alert('UVP is required');
    return false;
  }
  if($('#product-tax').val() == '' || $('#product-tax').val() == null) {
    alert('Tax is required');
    return false;
  }
  addNewProduct({
    barcode: $('#product-barcode').val().replace(/ß|-/g, ''),
    code: $('#product-code').val(),
    name: $('#product-name').val(),
    uvp: $('#product-uvp').val(),
    tax: $('#product-tax').val()
  });
}

function initProductPopup() {
  $('#product-barcode').val('');
  $('#product-code').val('');
  $('#product-name').val('');
  $('#product-uvp').val('');
  $('#product-tax').val(0.19);
}

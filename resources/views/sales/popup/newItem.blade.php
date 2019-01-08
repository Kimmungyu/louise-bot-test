<div class="dapos-calculator-sm" style="left:15%;width:700px;" id="productPopup">
  <div class="popup-head">
    <button type="button" class="close" aria-label="Close" onclick="closeProductPopup()">
      <span aria-hidden="true" style="font">&times;</span>
    </button>
  </div>
  <div class="col-md-12">
    <div class="block">
      <label for="newitem-item-barcode" class="fs18 popup-label" style="width:130px">Barcode </label>
      <input type="text" class="popup-input-text input-lg" id="product-barcode">
    </div>
    <div class="block">
      <label for="newitem-item-code" class="fs18 popup-label" style="width:130px">Item Code </label>
      <input type="text" class="popup-input-text input-lg" id="product-code">
    </div>
    <div class="block">
      <label for="newitem-item-name" class="fs18 popup-label" style="width:130px">Item Name </label>
      <input type="text" class="popup-input-text input-lg" id="product-name">
    </div>
    <div class="block">
      <label for="newitem-item-uvp" class="fs18 popup-label" style="width:130px">UVP(€) </label>
      <input type="text" class="popup-input-text input-lg" id="product-uvp" placeholder="ex) 1234.78">
    </div>
    <div class="block">
      <label for="newitem-item-tax" class="fs18 popup-label" style="width:130px">Tax</label>
      <select class="popup-selectbox input-lg" id="product-tax">
        <option value="0.19" selected>19%</option>
        <option value="0.07">7%</option>
      </select>
    </div>
    <div class="block-bottom">
      <div class="text-center">
        <button class="btn btn-lg btn-danger" style="width:180px" onclick="closeProductPopup()">CANCEL</button>
        <button class="btn btn-lg btn-primary" style="width:180px" onclick="validateProduct()">SUBMIT</button>
      </div>
    </div>
  </div>
</div>

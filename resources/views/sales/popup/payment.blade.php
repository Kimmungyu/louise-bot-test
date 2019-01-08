
<div class="dapos-popup" id="paymentPopup" >
  <div class="popup-title" id="tpad-text">
    <h3 class="text-success">Total Price <span style="float:right;" id="payment-text-netprice"></span></h3>
    <div id="partial-price-text">
      <h3 style="margin: 0; padding: 0 16px;">Partial Price Minus Sum <span style="float:right;" id="payment-text-partial"></span></h3>
    </div>
  </div>
  <div class="mt30" id="payment-selection-tab">
    <div class="block">
      <label for="payment-selection-cash" class="fs18 popup-label wd-200">Cash</label>
      <input type="text" class="popup-input-number input-lg wd-200" id="payment-selection-cash" onclick="openCalculator(this)" readonly>
      <button type="button" class="btn btn-lg btn-success btn-popup-partial" onclick="paymentAll('cash')">All</button>
      <button type="button" class="btn btn-lg btn-warning btn-popup-partial" onclick="restAll('payment-selection-cash')">Rest All</button>
      <!-- 2018.10.20 WonkyoungLee-->
      <button type="button" class="btn btn-lg btn-danger btn-popup-partial" onclick="clearValue('payment-selection-cash')">Clear</button>
    </div>
    <div class="block">
      <label for="payment-selection-creditcard" class="fs18 popup-label wd-200">Credit Card</label>
      <input type="text" class="popup-input-number input-lg partial-amount wd-200" id="payment-selection-creditcard" onclick="openCalculator(this)" readonly>
      <button type="button" class="btn btn-lg btn-success btn-popup-partial" onclick="paymentAll('creditcard')">All</button>
      <button type="button" class="btn btn-lg btn-warning btn-popup-partial" onclick="restAll('payment-selection-creditcard')">Rest All</button>
      <button type="button" class="btn btn-lg btn-danger btn-popup-partial" onclick="clearValue('payment-selection-creditcard')">Clear</button>
    </div>
    <div class="block">
      <label for="payment-selection-eccard" class="fs18 popup-label wd-200">EC Card</label>
      <input type="text" class="popup-input-number input-lg wd-200" id="payment-selection-eccard" onclick="openCalculator(this)" readonly>
      <button type="button" class="btn btn-lg btn-success btn-popup-partial" style="width:100px" onclick="paymentAll('eccard')">All</button>
      <button type="button" class="btn btn-lg btn-warning btn-popup-partial" style="width:100px" onclick="restAll('payment-selection-eccard')">Rest All</button>
      <button type="button" class="btn btn-lg btn-danger btn-popup-partial" style="width:100px" onclick="clearValue('payment-selection-eccard')">Clear</button>
    </div>
    <!-- 2018-12-10 Wonkyoung 숨기기 -->
    <!-- <div class="block">
      <label for="payment-selection-voucher" class="fs18 popup-label wd-200">Voucher</label>
      <input type="text" class="popup-input-number input-lg wd-200" id="payment-selection-voucher" onclick="restAll('payment-selection-voucher')" readonly>
      <button type="button" class="btn btn-lg btn-success btn-popup-partial" style="width:100px" onclick="paymentAll('voucher')">All</button>
      <button type="button" class="btn btn-lg btn-warning btn-popup-partial" style="width:100px" onclick="restAll('payment-selection-voucher')">Rest All</button>
      <button type="button" class="btn btn-lg btn-danger btn-popup-partial" style="width:100px" onclick="clearValue('payment-selection-voucher')">Clear</button>
    </div> -->
    <div class="block-bottom">
      <button class="btn btn-lg btn-danger" onclick="closePaymentPopup()">Cancel</button>
      <button class="btn btn-lg btn-primary" onclick="openPaymentPartial()" style="float:right;">Partial Payment <span class="glyphicon glyphicon-arrow-right"></span></button>
    </div>
  </div>

  <div class="mt30 voucher-selection" id="voucher-selection-tab">
    <div class="block">
      <label for="payment-selection-voucher" class="fs18 popup-label"></label>
      <label for="payment-selection-voucher" class="fs18 popup-label-cell">Voucher Code</label>
      <label for="payment-selection-voucher" class="fs18 popup-label-cell">Balance</label>
      <label for="payment-selection-voucher" class="fs18 popup-label-cell">Amount</label>
      <!-- 2018.10.23 WonkyoungLee-->
      <button type="button" class="btn btn-lg btn-success popup-search-button popup-label-cell" style="width:50px;" onclick="addNewVoucherPayment()">+</button>

    </div>
    <div class="block">
      <label for="payment-selection-voucher" class="fs18 popup-label">Voucher</label>
      <input type="text" class="popup-input-number input-lg" id="payment-selection-voucher-code" onkeyup="checkVoucherByCode(this);" onclick="clearVoucherValue(this);">
      <input type="text" class="popup-input-number input-lg" id="payment-selection-voucher-checksum" readonly>
      <input type="text" class="popup-input-number input-lg" id="payment-selection-voucher-use" onclick="voucherOpenCalculator(this);" readonly>
      <button type="button" class="btn btn-lg btn-danger popup-search-button" id="payment-voucher-delete" style="width:50px" onclick="deleteVoucherPayment(this)">-</button>
      <!-- <div class="popup-label-cell"><button type="button" class="btn btn-lg btn-success popup-search-button" style="width:50px" onclick="addNewVoucherPayment()">+</button> -->
    </div>
  </div>

    <!--
    <div class="block">
      <label for="payment-selection-voucher" class="fs18 popup-label" style="width: 130px;">Voucher 3</label>
      <input type="text" class="popup-input-number input-lg" id="payment-selection-voucher" style="width: 130px;" onclick="if(paymentObj.voucher.max > 0) openCalculator(this); else alert('Invalid Code or No amount.');" readonly>
      <button type="button" class="btn btn-lg btn-success popup-search-button" style="width:100px" onclick="paymentAll('voucher')">All</button>
      <button type="button" class="btn btn-lg btn-warning popup-search-button" style="width:100px" onclick="restAll('payment-selection-voucher')">Rest All</button>
      <button type="button" class="btn btn-lg btn-danger popup-search-button" style="width:100px" onclick="clearVoucherValue()">Clear</button>
    </div>

    <div class="block">
      <label for="payment-selection-voucher-code" class="fs18 popup-label">Voucher Check</label>
      <input type="text" class="popup-input-number input-lg" id="payment-selection-voucher-code" style="width:210px;text-align:left" onkeypress="if(event.keyCode == '13' || event.which == '13') checkVoucherByCode(this.value);">
      <button type="button" class="btn btn-lg btn-warning popup-search-button" style="width:100px" onclick="checkVoucherByCode(document.getElementById('payment-selection-voucher-code').value)">Check</button>
      <span id="payment-selection-voucher-checksum" style="font-size:20px;font-weight:bold;margin-left:5px"></span>
    </div>
    -->

  <!-- </div> -->
  <div class="mt30" id="payment-transaction-tab" style="display:none">
    <div class="block payment-transaction-block" id="creditcard-block" style="display:none">
      <label for="payment-transaction-creditcard" class="fs18 popup-label">CREDIT CARD</label>
      <select class="popup-selectbox input-lg" id="payment-transaction-creditcard-type" onchange="creditCardTypeChanged(this)" style="width:185px;font-size:16px">
        <option value="0">CreditCard Type...</option>
        @foreach($creditCards as $cC)
          <option value="{{ $cC->id }}">{{ $cC->name }}</option>
        @endforeach
      </select>
      <input type="text" class="popup-input-number input-lg" id="payment-transaction-creditcard" style="width: 200px;" disabled> <span style="font-size:24px"> €</span>
      <button class="btn btn-lg btn-success popup-search-button btn-exec-payment" id="partial-payment-creditcard" style="display:none" onclick="event.preventDefault();payByCreditCard(this.value)">Accept</button>
    </div>
    <div class="block payment-transaction-block" id="eccard-block" style="display:none">
      <label for="payment-transaction-eccard" class="fs18 popup-label">EC CARD</label>
      <input type="text" class="popup-input-number input-lg" id="payment-transaction-eccard" style="width: 390px;" disabled> <span style="font-size:24px"> €</span>
      <button class="btn btn-lg btn-success popup-search-button btn-exec-payment" style="display:none" id="partial-payment-eccard" onclick="event.preventDefault();payByEcCard(this.value)">Accept</button>
    </div>
    <div class="block payment-transaction-block" id="voucher-block" style="display:none">
      <label for="payment-transaction-voucher" class="fs18 popup-label">Voucher</label>
      <input type="text" class="popup-input-number input-lg" id="payment-transaction-voucher" style="width: 390px;" disabled> <span style="font-size:24px"> €</span>
      <!-- 2018.10.29 WonkyoungLee delete -->
      <!-- <button class="btn btn-lg btn-success popup-search-button btn-exec-payment" style="display:none" id="partial-payment-voucher" onclick="event.preventDefault();payWithVoucher(this.value)">Accept</button> -->
    </div>
    <div class="block payment-transaction-block" id="cash-block" style="display:none">
      <div class="block-cash">
        <label for="payment-transaction-cash" class="fs18 popup-label">CASH</label>
        <input type="text" class="popup-input-number input-lg" id="payment-transaction-cash" style="width: 390px;" disabled> <span style="font-size:24px"> €</span>
      </div>
      <div class="block-cash">
        <label for="payment-transaction-cash" class="fs18 popup-label">Received Amount</label>
        <button type="button" class="btn btn-lg btn-warning popup-search-button" onclick="document.getElementById('payment-transaction-amount').value=0">Clear</button>
        <input type="text" class="popup-input-number input-lg" id="payment-transaction-amount" style="width: 260px;" value="0" onclick="openCalculatorWithValue(this)" readonly> <span style="font-size:24px"> €</span>
        <button type="button" class="btn btn-lg btn-default popup-search-button" onclick="document.getElementById('payment-transaction-amount').value=document.getElementById('payment-transaction-cash').value">All</button>
      </div>
      <div class="block-cash">
        <label class="fs18 popup-label"></label>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(1)">1 €</button>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(2)">2 €</button>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(5)">5 €</button>
      </div>
      <div class="block-cash">
        <label class="fs18 popup-label"></label>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(10)">10 €</button>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(20)">20 €</button>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(50)">50 €</button>
      </div>
      <div class="block-cash">
        <label class="fs18 popup-label"></label>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(100)">100 €</button>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(200)">200 €</button>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="addCash(500)">500 €</button>
      </div>
    </div>
    <div class="block-bottom">
      <button class="btn btn-lg btn-danger" onclick="closePaymentPopup()">Cancel</button>
      <button class="btn btn-lg btn-warning" onclick="backToSelectionTab()">BACK</button>
      <button class="btn btn-lg btn-success" id="execute-payment" style="float:right;">SUCCESS</button>
    </div>
  </div>
  <div class="mt30" id="payment-cashsum-tab" style="display:none">
    <div class="popup-title">
      <h3>Cash <span style="float:right;" id="payment-cashsum-cash"></span></h3>
    </div>
    <div class="popup-title">
      <h3>Received Amount <span style="float:right;" id="payment-cashsum-received"></span></h3>
    </div>
    <div class="popup-title">
      <h3>Changes <span style="float:right;" id="payment-cashsum-change"></span></h3>
    </div>
    <div class="block-bottom">
      <button class="btn btn-lg btn-danger" onclick="closePaymentPopup()">Cancel</button>
      <button class="btn btn-lg btn-warning" onclick="backToTransactionTab()">BACK</button>
      <button class="btn btn-lg btn-success" style="float:right;" onclick="event.preventDefault();payWithCashSucess()">Accept</button>
    </div>
  </div>
</div>

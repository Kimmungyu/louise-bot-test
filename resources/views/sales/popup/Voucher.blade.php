
<script>
$('#block voucher-list').on("DOMSubtreeModified", function()
{
  calculateTotal();
});
</script>

<div class="dapos-popup" style="height:680px;margin-top:-340px;" id="voucherPopup">
  <div id="voucher-create-tab">
    <div class="popup-title">
      <h3>Create Voucher</h3>
    </div>
    <div class="block voucher-list">
      <div class="form-group">
      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <!-- 2018.10.18 WonkyoungLee -->
      <input type="text" class="popup-input-text input-lg form-control" style="width:200px;text-align:left" id="voucher-code" onkeyup="calculateTotal();" onblur="checkVoucherCode(this)">
      <label for="voucher-amount" class="fs18 popup-label" style="margin-left:10px">Amount (€) </label>
      <!-- 2018.10.16 WonkyoungLee
      change type="number" to type="text", insert onclick="openCalculator(this) readonly"
       -->
      <input type="text" class="popup-input-number input-lg form-control" style="width:200px;text-align:right" id="voucher-amount" onclick="openCalculator(this)" readonly><span class="fs18"> €</span>
      <!--<input type="number" class="popup-input-text input-lg" style="width:200px;text-align:right" id="voucher-amount" onkeyup="calculateTotal();">-->
      </div>
    </div>
    <div class="block voucher-list">
      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-input-text input-lg form-control" style="width:200px;text-align:left" id="voucher-code" onkeyup="calculateTotal();" onblur="checkVoucherCode(this)">
      <label for="voucher-amount" class="fs18 popup-label" style="margin-left:10px">Amount (€) </label>
      <input type="text" class="popup-input-number input-lg form-control" style="width:200px;text-align:right" id="voucher-amount" onclick="openCalculator(this)" readonly><span class="fs18"> €</span>
    </div>
    <div class="block voucher-list">
      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-input-text input-lg form-control" style="width:200px;text-align:left" id="voucher-code" onkeyup="calculateTotal();" onblur="checkVoucherCode(this)">
      <label for="voucher-amount" class="fs18 popup-label" style="margin-left:10px">Amount (€) </label>
      <input type="text" class="popup-input-number input-lg form-control" style="width:200px;text-align:right" id="voucher-amount" onclick="openCalculator(this)" readonly><span class="fs18"> €</span>
    </div>
    <div class="block">
      <label for="voucher-total" class="fs18 popup-label text-success">Total</label>
      <!-- 2018.10.16 WonkyoungLee modify -->
      <input type="text" class="popup-input-text input-lg input-success" style="width:200px;text-align:right" id="voucher-total" readonly> <span class="fs18"> €</span>
      <!--<input type="number" class="popup-input-text input-lg" style="width:200px;text-align:right" id="voucher-total" readonly> <span class="fs18"> €</span>-->
    </div>
    <hr style="border-color: #efefef"/>
    <div class="block">
      <label for="voucher-cc-amount" class="fs18 popup-label">Credit Card</label>
      <!-- 2018.10.16 WonkyoungLee modify -->
      <input type="text" class="popup-input-text input-lg voucher-payment" style="width:200px;text-align:right" id="voucher-cc-amount" onclick="openCalculator(this)" readonly> <span class="fs18"> €</span>
      <!--<input type="number" class="popup-input-text input-lg voucher-payment" style="width:200px;text-align:right" id="voucher-cc-amount"> <span class="fs18"> €</span>-->
      <select class="popup-selectbox input-lg" id="voucher-cc-type" style="width:200px;margin-left:10px">
        <option value="0">Please select...</option>
        @foreach($creditCards as $cc)
          <option value="{{ $cc->id }}">{{$cc->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="block">
      <label for="voucher-ec-amount" class="fs18 popup-label">EC Card: </label>
      <!-- 2018.10.16 WonkyoungLee modify -->
      <input type="text" class="popup-input-text input-lg voucher-payment" style="width:200px;text-align:right" id="voucher-ec-amount" onclick="openCalculator(this)" readonly> <span class="fs18"> €</span>
      <!-- <input type="number" class="popup-input-text input-lg voucher-payment" style="width:200px;text-align:right" id="voucher-ec-amount"> <span class="fs18"> €</span> -->
    </div>
    <div class="block">
      <label for="voucher-cash-amount" class="fs18 popup-label">Cash: </label>
      <!-- 2018.10.16 WonkyoungLee modify -->
      <input type="text" class="popup-input-text input-lg voucher-payment" style="width:200px;text-align:right" id="voucher-cash-amount" onclick="openCalculator(this)" readonly> <span class="fs18"> €</span>
      <!-- <input type="number" class="popup-input-text input-lg voucher-payment" style="width:200px;text-align:right" id="voucher-cash-amount"> <span class="fs18"> €</span> -->
    </div>
    <div class="block-bottom">
      <div class="text-center">
        <button class="btn btn-lg btn-danger" style="width:200px" onclick="closeVoucherPopup()">Cancel</button>
        <button class="btn btn-lg btn-success" style="width:200px;margin-left:10px" onclick="validateVoucher()">Submit</button>
      </div>
    </div>
  </div>
  <!-- 2018.10.29 WonkyoungLee -->
  <div id="voucher-refund-tab">
    <div class="popup-title">
      <h3>Refund Voucher</h3>
    </div>
    <div class="block voucher-refund-list">
      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-label-cell input-lg form-control" style="width: 200px;" id="voucher-refund-code" onkeyup="checkRefundVoucher(this);">
      <label for="voucher-amount" class="fs18 popup-label">Refund Balance</label>
      <input type="text" class="popup-label-cell input-lg form-control" style="width: 200px;" id="voucher-refund-amount" readonly><span class="fs18"> €</span>
      <!-- 2018-11-08 WonkyoungLee -->
      <div class="block voucher-payment-method">
        <label for="voucher-amount" class="fs18 popup-label">Payment Method : </label>
        <span class="fs18" id="cancelOrder-paymentMethod">-</span>
        <span class="card-cancel-yn">
          <label for="payment-option-inout" class="fs18">&emsp;&emsp;* Card Cancellable</label>
          <div class="btn-group ml10">
            <button class="btn btn-lg btn-default" id="cardCancellable-yes" onclick="isCardCancellable(this, 'Y')">Yes</button>
            <button class="btn btn-lg btn-default" id="cardCancellable-no" onclick="isCardCancellable(this, 'N')">No</button>
          </div>
        </span>
      </div>
    </div>
    <div class="block voucher-refund-list">
      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-label-cell input-lg form-control" style="width: 200px;" id="voucher-refund-code" onkeyup="checkRefundVoucher(this);">
      <label for="voucher-amount" class="fs18 popup-label">Refund Balance</label>
      <input type="text" class="popup-label-cell input-lg form-control" style="width: 200px;" id="voucher-refund-amount" readonly><span class="fs18"> €</span>
      <!-- 2018-11-08 WonkyoungLee -->
      <div class="block voucher-payment-method">
        <label for="voucher-amount" class="fs18 popup-label">Payment Method : </label>
        <span class="fs18" id="cancelOrder-paymentMethod">-</span>
        <span class="card-cancel-yn">
          <label for="payment-option-inout" class="fs18">&emsp;&emsp;* Card Cancellable</label>
          <div class="btn-group ml10">
            <button class="btn btn-lg btn-default" id="cardCancellable-yes" onclick="isCardCancellable(this, 'Y')">Yes</button>
            <button class="btn btn-lg btn-default" id="cardCancellable-no" onclick="isCardCancellable(this, 'N')">No</button>
          </div>
        </span>
      </div>
    </div>
    <div class="block voucher-refund-list">
      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-label-cell input-lg form-control" style="width: 200px;" id="voucher-refund-code" onkeyup="checkRefundVoucher(this);">
      <label for="voucher-amount" class="fs18 popup-label">Refund Balance</label>
      <input type="text" class="popup-label-cell input-lg form-control" style="width: 200px;" id="voucher-refund-amount" readonly><span class="fs18"> €</span>
      <!-- 2018-11-08 WonkyoungLee -->
      <div class="block voucher-payment-method">
        <label for="voucher-amount" class="fs18 popup-label">Payment Method : </label>
        <span class="fs18" id="cancelOrder-paymentMethod">-</span>
        <span class="card-cancel-yn">
          <label for="payment-option-inout" class="fs18">&emsp;&emsp;* Card Cancellable</label>
          <div class="btn-group ml10">
            <button class="btn btn-lg btn-default" id="cardCancellable-yes" onclick="isCardCancellable(this, 'Y')">Yes</button>
            <button class="btn btn-lg btn-default" id="cardCancellable-no" onclick="isCardCancellable(this, 'N')">No</button>
          </div>
        </span>
      </div>
    </div>
    <div class="block voucher-refund-list">
      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-label-cell input-lg form-control" style="width: 200px;" id="voucher-refund-code" onkeyup="checkRefundVoucher(this);">
      <label for="voucher-amount" class="fs18 popup-label">Refund Balance</label>
      <input type="text" class="popup-label-cell input-lg form-control" style="width: 200px;" id="voucher-refund-amount" readonly><span class="fs18"> €</span>
      <!-- 2018-11-08 WonkyoungLee -->
      <div class="block voucher-payment-method">
        <label for="voucher-amount" class="fs18 popup-label">Payment Method : </label>
        <span class="fs18" id="cancelOrder-paymentMethod">-</span>
        <span class="card-cancel-yn">
          <label for="payment-option-inout" class="fs18">&emsp;&emsp;* Card Cancellable</label>
          <div class="btn-group ml10">
            <button class="btn btn-lg btn-default" id="cardCancellable-yes" onclick="isCardCancellable(this, 'Y')">Yes</button>
            <button class="btn btn-lg btn-default" id="cardCancellable-no" onclick="isCardCancellable(this, 'N')">No</button>
          </div>
        </span>
      </div>
    </div>
    <div class="block-bottom">
      <div class="text-center">
        <button class="btn btn-lg btn-danger wd-200" onclick="closeVoucherPopup()">Cancel</button>
        <button class="btn btn-lg btn-success wd-200" style="margin-left:10px;" onclick="refundVoucherProcess()">Refund All</button>
      </div>
    </div>
  </div>
</div>


<!--
    <div class="block voucher-list">
      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-input-text input-lg" style="width:120px;text-align:left" id="voucher-code" onkeyup="calculateTotal();">
      <label for="voucher-amount" class="fs18 popup-label" style="margin-left:10px">Amount (€) </label>

      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-amount" onkeyup="calculateTotal();">
      <label for="voucher-dc" class="fs18 popup-label-xs" style="margin-left:10px">DC (%): </label>
      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-dc" onkeyup="calculateTotal();">
    </div>
    <div class="block voucher-list">

      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-input-text input-lg" style="width:120px;text-align:left" id="voucher-code" onkeyup="calculateTotal();">
      <label for="voucher-amount" class="fs18 popup-label" style="margin-left:10px">Amount (€) </label>

      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-amount" onkeyup="calculateTotal();">
      <label for="voucher-dc" class="fs18 popup-label-xs" style="margin-left:10px">DC (%): </label>
      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-dc" onkeyup="calculateTotal();">
    </div>
    <div class="block voucher-list">

      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-input-text input-lg" style="width:120px;text-align:left" id="voucher-code" onkeyup="calculateTotal();">
      <label for="voucher-amount" class="fs18 popup-label" style="margin-left:10px">Amount (€) </label>

      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-amount" onkeyup="calculateTotal();">
      <label for="voucher-dc" class="fs18 popup-label-xs" style="margin-left:10px">DC (%): </label>
      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-dc" onkeyup="calculateTotal();">
    </div>
    <div class="block voucher-list">

      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-input-text input-lg" style="width:120px;text-align:left" id="voucher-code" onkeyup="calculateTotal();">
      <label for="voucher-amount" class="fs18 popup-label" style="margin-left:10px">Amount (€) </label>

      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-amount" onkeyup="calculateTotal();">
      <label for="voucher-dc" class="fs18 popup-label-xs" style="margin-left:10px">DC (%): </label>
      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-dc" onkeyup="calculateTotal();">
    </div>
    <div class="block voucher-list">

      <label for="voucher-amount" class="fs18 popup-label">Code </label>
      <input type="text" class="popup-input-text input-lg" style="width:120px;text-align:left" id="voucher-code" onkeyup="calculateTotal();">
      <label for="voucher-amount" class="fs18 popup-label" style="margin-left:10px">Amount (€) </label>

      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-amount" onkeyup="calculateTotal();">
      <label for="voucher-dc" class="fs18 popup-label-xs" style="margin-left:10px">DC (%): </label>
      <input type="number" class="popup-input-text input-lg" style="width:120px;text-align:right" id="voucher-dc" onkeyup="calculateTotal();">
    </div>
-->


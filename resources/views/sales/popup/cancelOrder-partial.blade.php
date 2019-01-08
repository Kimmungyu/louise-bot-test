<div class="dapos-popup-cancel" id="cancelOrder-partial">
  <div class="popup-head"></div>
  <div class="col-md-12">
    <div class="mt30" id="refund-partial-select-tab">
      <div class="popup-title">
        <h3>Refund Amount 
          
          <span style="float:right;" class="refund-partial-price"></span>
        </h3>
      </div>
      <div class="block">
        <table class="table dptbl-sm mt10">
          <thead>
            <tr>
              <th width="320px">Item Name</th>
              <th width="89px" style="text-align:center">Unit Price</th>
              <th width="130px" style="text-align:center">Qty</th>
              <th width="90px" style="text-align:center">Return Price</th>
            </tr>
          </thead>
          <tbody class="fs18" id="refund-partial-area" style="height:300px">
          </tbody>
        </table>
      </div>
      <div class="block-bottom">
        <button class="btn btn-lg btn-danger" onclick="closeCancelPartialPopup()">Cancel</button>
        <button class="btn btn-lg btn-success" id="cancelOrder-partial-select-checkout" onclick="openRefundTab()" style="float:right;">Check Out</button>
      </div>
    </div>
    <div class="mt30" id="refund-partial-refund-tab" style="display:none">
      <div class="popup-title">
        <h3>Refund Amount 
          <span style="float:right;display:none;color:red;" class="refund-disable-coupon-price"> </span>
          <span style="float:right;" class="refund-partial-price"></span>
        </h3>
      </div>
      <div class="block" id="cancelOrder-partial-eur-block">
        <label for="payment-transaction-cash" class="fs18 popup-label" style="width:435px">CASH (€)</label>
        <!-- 2018.10.16 WonkyoungLee Add onclick Action -->
        <input type="text" class="popup-sm-input input-lg refund-partial" id="refund-partial-cash" style="text-align:right" onclick="openSmCalculatorC(this)" readonly> <span style="font-size:24px"> €</span>
      </div>
      <div class="block" id="cancelOrder-partial-eur-block">
        <label for="payment-transaction-voucher" class="fs18 popup-label" style="width:435px">VOUCHER (€)</label>
        <!-- 2018.10.16 WonkyoungLee Add onclick Action -->
        <input type="text" class="popup-sm-input input-lg refund-partial" id="refund-partial-voucher" style="text-align:right" onclick="openSmCalculatorC(this)" readonly> <span style="font-size:24px"> €</span>
      </div>
      
      <div class="block coupon-caution" id="coupon-caution">
        <span style="font-size:36px;font-weight:bold;color:red">※ Caution ※<br></span>
        <span style="font-size:24px;font-weight:bold;color:red">Coupon will be disabled</span>
        <label for="payment-transaction-voucher" class="fs18 popup-label" id="added-master-coupon" style="float:right"></label>
      </div>
      <div class="block new-coupon-insert" id="new-coupon-insert" >
        <!-- <label for="payment-transaction-voucher" class="fs18 popup-label" >New Coupon</label>
        <button class="btn btn-lg btn-success" id="insert-master-coupon" onclick="addMasterCoupon();" style="float:right">Add</button>
        <input type="text" class="popup-sm-input input-lg master-coupon" id="refund-master-coupon" style="float:right;text-align:right" onkeypress="if(event.keyCode == 13){ addMasterCoupon();}"> -->
        
      </div>
      <div class="block-bottom">
        <button class="btn btn-lg btn-danger" onclick="closeCancelPartialPopup()">Cancel</button>
        <button class="btn btn-lg btn-warning" onclick="backToRefundSelectionTab()">Back</button>
        <button class="btn btn-lg btn-success" id="cancelOrder-partial-checkout" onclick="event.preventDefault();refundAllSuccess('partial')" style="float:right;">Check Out</button>
      </div>
    </div>
  </div>
</div>

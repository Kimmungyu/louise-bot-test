<div class="dapos-calculator-sm modal-open-cash-drawer" style="left:15%;width:700px;height:340px" id="cashBoxPopup">
  <div class="block">
    <label for="cashbox-reason" class="fs18 popup-label" style="width:130px">Reason</label>
    <select class="popup-selectbox input-lg" id="cashbox-reason" onchange="setCashBoxReason();" required>
      <option value="0">Choose the reason...</option>
      @foreach($reasons as $reason)
        <option value="{{ $reason->id }}">{{$reason->name}}</option>
      @endforeach
    </select>
  </div>
  <div class="block">
    <label for="cashbox-amount" class="fs18 popup-label" style="width:130px">Amount</label>
    <input type="number" class="input-lg form-control popup-input" id="cashbox-amount" style="width:520px" placeholder="Amount(ex: 1234.56)" disabled>
  </div>
  <div class="block">
    <label for="cashbox-note" class="fs18 popup-label" style="width:130px;vertical-align:top">Note</label>
    <textarea class="form-control input-lg popup-input" id="cashbox-note" style="width:520px;resize: none" rows="3" placeholder="Leave the note for special cases..."></textarea>
  </div>
  <div class="block-bottom">
    <div class="text-center">
      <button class="btn btn-lg btn-primary" style="width:180px" onclick="openCashBox()">Submit</button>
      <button class="btn btn-lg btn-danger" style="width:180px" onclick="closeCashBoxPopup()">Close</button>
    </div>
  </div>
</div>

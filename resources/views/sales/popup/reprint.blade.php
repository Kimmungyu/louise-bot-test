<div class="dapos-popup" id="reprintPopup">
  <div class="popup-title">
    <h3>Reprint</h3>
  </div>
  <div class="block form-inline">
    <label class="fs18 popup-label wd-200">Date</label>
    <select class="popup-selectbox input-lg" id="reprint-kasse" style="width:150px;">
      <option value="all">Retail All</option>
      @foreach($kasses as $kasse)
        <option value="{{ $kasse->id }}">{{ $kasse->name }}</option>
      @endforeach
    </select>
    <div class="form-group" style="margin-left:10px">
        <input type="text" class="form-control input-lg datepicker" style="width:140px;text-align:center" id="reprint-from-date" data-date-format="yyyy-mm-dd" readonly> ~
        <input type="text" class="form-control input-lg datepicker" style="width:140px;text-align:center" id="reprint-to-date" data-date-format="yyyy-mm-dd" readonly>
        <button type="button" class="btn btn-lg btn-primary popup-search-button" onclick="getOrdersWithDate()">Search</button>
    </div>
  </div>
  <div class="block">
    <table class="table dptbl-sm mt30">
      <thead>
        <tr>
          <th width="97px" style="text-align:center">Kasse</th>
          <th width="167px">Bill Number</th>
          <th width="345px">Item List</th>
          <th width="59px" style="text-align:center">Status</th>
          <th width="110px" style="text-align:center">Amount</th>
          <th width="134px"></th>
        </tr>
      </thead>
      <tbody class="fs18" id="reprint-area" style="height:280px">
        @for($i=0;$i<9;$i++)
          <tr><td class="dptd-sm" colspan="5"></td></tr>
        @endfor
      </tbody>
    </table>
  </div>
  <div class="block-bottom">
    <button class="btn btn-lg btn-danger" onclick="closeReprintPopup()">Close</button>
  </div>
</div>

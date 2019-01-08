<div class="dapos-popup" id="addItemPopup">
  <div class="popup-title">
    <h3>Add Item</h3>
  </div>
  <div class="block">
    <label for="additem-option" class="fs18 popup-label" >Search by...</label>
    <select class="popup-selectbox input-lg" id="additem-option">
      <option value="none">Select...</option>
      <option value="code">Item Code</option>
      <option value="name">Item Name</option>
      <option value="barcode">Barcode</option>
    </select>
  </div>
  <div class="block">
    <label for="additem-name" class="fs18 popup-label" >Name</label>
    <input type="text" class="popup-input input-lg" id="additem-name" onkeypress="if(event.keyCode == '13' || event.which == '13') { searchItem() }" style="width: 320px;">
    <button type="button" class="btn btn-lg btn-default popup-search-button" onclick="searchItem()">Search</button>
  </div>
  <div class="block">
    <table class="table dptbl-sm">
      <thead>
        <tr>
          <th width="128px" style="text-align:center;">Item Code</th>
          <th width="229px">Item Name</th>
          <th width="129px" style="text-align:center;">Barcode No.</th>
          <th width="78px" style="text-align:center;">Price</th>
          <th width="116px" style="text-align:center;">Louise / Dami</th>
          <th width="80px" style="text-align:center;"></th>
        </tr>
      </thead>
      <tbody id="additem-data-area">
        @for($i=0;$i<4;$i++)
          <tr><td class="dptd" colspan="7" width="763px"></td></tr>
        @endfor
      </tbody>
    </table>
  </div>
  <div class="block-bottom">
    <button class="btn btn-lg btn-danger" onclick="closeAddItemPopup()">Close</button>
  </div>
</div>

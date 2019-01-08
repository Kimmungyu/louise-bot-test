@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <h2 class="support-header">Check/Search Price</h2>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-inline">
        <div class="form-group search-box">
          <select class="form-control input-md" id="checkprice-search-type">
            <option value="barcode">Barcode</option>
            <option value="name">Name</option>
          </select>
          <input type="text" class="form-control input-md" id="checkprice-input" onkeypress="if(event.keyCode == '13' || event.which == '13') { getProductPrice() }" autofocus>
          <button type="button" class="btn btn-md btn-primary" onClick="getProductPrice()">Search</button>
          <button type="button" class="btn btn-md btn-default" onClick="clearTable()">Clear Table</button>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <table class="table table-bordered table-condensed table-product-list-result" id="checkprice-table">
        <thead>
          <tr>
            <th width="20%">Product Code</th>
            <th width="35%">Name</th>
            <th width="5%">Dami UVP</th>
            <th width="5%">Louise UVP</th>
            <th width="5%">AVR Buy Price</th>
            <th width="5%">Tax Rate</th>
            <th width="5%" style="text-align:center;vertical-align:middle;">Louise Stock</th>
          </tr>
        </thead>
        <tbody id="checkprice-tbody">
        </tbody>
      </table>
      <table class="table table-bordered table-condensed table-product-list-result" id="checkprice-table-mobile">
        <tbody id="checkprice-tbody-mobile">
        </tbody>
      </table>
      <div class="text-center mobile-except" id="table-paginator"></div>
      <div><br></div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/misc/checkPrice.js') }}"></script>
@endsection

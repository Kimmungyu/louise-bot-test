@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <h2 class="support-header">List Product</h2>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-inline">
        <div class="form-group search-box">
          <select class="form-control input-md " id="checkprice-search-type">
            <option value="barcode">Barcode</option>
            <option value="name">Name</option>
          </select>
          <input type="text" class="form-control input-md " id="checkprice-input" onkeypress="if(event.keyCode == '13' || event.which == '13') { getProductPrice() }" autofocus>
          <button type="button" class="btn btn-md btn-primary" onClick="getProductPrice()">Search</button>
          <button type="button" class="btn btn-md btn-default" onClick="clearTable()">Clear Table</button>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <table id="listProduct-table" class="table table-bordered table-product-list-result" style="table-layout: fixed;"> <!-- table-bordered table-condensed table-hover -->
        <thead>
          <tr>
            <th width="20%" style="text-align:center;vertical-align:middle;">Product Code</th>
            <th width="35%" style="text-align:center;vertical-align:middle;">Name</th>
            <th width="20%" style="text-align:center;vertical-align:middle;">Barcode</th>
            <th width="10%" style="text-align:center;">Louise UVP</th>
            <th width="10%" style="text-align:center;">Print Quantity</th>
            <th width="5%" style="text-align:center;vertical-align:middle;">  </th>
          </tr>
        </thead>
        <tbody id="listProduct-tbody">
          @if(count($products) > 0)
            @foreach($products as $row)
              <tr>
                <td style="text-align:center;">{{ $row->id }}</td>
                <td style="text-align:center;">{{ $row->product_code }}</td>
                <td style="text-align:center;">{{ $row->name }}</td>
                <td style="text-align:center;">{{ $row->description }}</td>
                <td style="text-align:right;">{{ $row->sales_price }}</td>
                <td style="text-align:left;"></td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="7" style="text-align:center">No Data Found</td>
            </tr>
          @endif
        </tbody>
      </table>
      <div class="text-center" id="table-paginator">{{ $products }}</div>
      <div><br></div>
    </div>
  </div>
</div>
@section('scripts')
<script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/misc/printBarcode.js') }}"></script>
@endsection
@endsection

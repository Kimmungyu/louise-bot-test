@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <h2 class="support-header">Cafe Product List</h2>
    </div>
  </div>
  @if(Session::has('message'))
      <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('message') !!}</em></div>
  @endif
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
      <table id="listCafeProduct-table" class="table table-bordered table-product-list-result" style="table-layout: fixed;"> <!-- table-bordered table-condensed table-hover -->
        <thead>
          <tr>
            <th width="40" style="text-align:center;vertical-align:middle;">ID</th>
            <th width="100" style="text-align:center;vertical-align:middle;">Product code</th>
            <th width="" style="text-align:center;vertical-align:middle;">Product name</th>
            <th width="" style="text-align:center;vertical-align:middle;">Description</th>
            <th width="" style="text-align:center;vertical-align:middle;">Ingredients</th>
            <th width="" style="text-align:center;vertical-align:middle;">Filling</th>
            <th width="140" style="text-align:center;">Louise UVP</th>
            <th width="80" style="text-align:center;">UVP</th>
            <th width="80" style="text-align:center;vertical-align:middle;">  </th>
          </tr>
        </thead>
        <tbody id="listCafeProduct-tbody">
          @if(count($products) > 0)
            @foreach($products as $row)
              <tr>
                <td style="text-align:center;">{{ $row->id }}</td>
                <td style="text-align:center;">{{ $row->product_code }}</td>
                <td style="text-align:center;">{{ $row->name }}</td>
                <td style="text-align:center;">{{ $row->description }}</td>
                <td style="text-align:center;">{{ $row->description }}</td>
                <td style="text-align:center;">{{ $row->description }}</td>
                <td style="text-align:center;">{{ $row->description }}</td>
                <td style="text-align:center;">{{ $row->sales_price }}</td>
                <td style="text-align:center;"><a href="editCafeProduct/{{ $row->id }}" class="btn btn-sm btn-default">Edit</a></td>
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

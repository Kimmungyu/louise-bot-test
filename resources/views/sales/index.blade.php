@extends('layouts.app')
@section('content')
<div class="container-fluid" style="z-index:0;">
  <input type="text" id="barcode" style="width:0px;height:0px;border:none;" autofocus>
  <!-- 2018-12-10 Wonkyoung Test Use Barcode -->
  <!-- <input type="text" id="barcode"  autofocus> -->
  <div class="row">
    <div class="col-md-12">
      <div class="form-group" style="margin-bottom: 0;">
        @foreach($discountGroup as $dg)
          <button class="btn btn-lg btn-default discount-btn-group" style="margin:0 15px 10px 0" onclick="discountAll(this, {{ $dg->discount_rate }}, {{ $dg->id }});">{{ $dg->name }}({{ $dg->discount_rate * 100 }}%)</button>
        @endforeach
        <button class="btn btn-lg btn-default discount-btn-group" style="margin:0 15px 10px 0" onclick="discountAll(this, 'custom', 99);">Custom Rate</button>

        <!-- 2018-12-10 Wonkyoung 숨기기 -->
        <!-- <button class="btn btn-lg btn-info" style="margin:0 15px 10px 0" onclick="openVoucherPopup();">Create Voucher</button>
        <button class="btn btn-lg btn-warning" style="margin:0 15px 10px 0" onclick="openRefundVoucherPopup();">Refund Voucher</button> -->

        <!--
        <button class="btn btn-lg btn-info discount-btn-group" style="margin:0 15px 10px 0" onclick="openIndexCalculator('delivery', 'ADD_DLV_CHG')">Add Delivery</button>
        -->
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-9" style="border-right:1px solid #e0e4e7;">
      <h3>Items</h3>
      <table class="table dptbl">
        <thead>
          <tr>
            <th width="465px">Item</th>
            <th width="73px" style="text-align:center;"></th>
            <th width="73px" style="text-align:center;">Qty</th>
            <th width="150px" style="text-align:center;">Netto</th>
            <th width="75px" style="text-align:center;">Tax</th>
            <th width="150px" style="text-align:center;">Brutto</th>
            <th width="150px" style="text-align:center;">Total</th>
            <th width="220px"></th>
          </tr>
        </thead>
        <tbody class="fs16" id="main-data-area">
          @for($i=0;$i<12;$i++)
            <tr><td class="dptd" colspan="8" width="1356px"></td></tr>
          @endfor
        </tbody>
      </table>
      <div class="form-group">
        <button type="button" class="btn btn-lg btn-default" style="float:left" onclick="clearAll()">Clear All</button>
        <button type="button" class="btn btn-lg btn-primary" style="float:right" onclick="openAddItemPopup()">Add/Search Item</button>
        <button type="button" class="btn btn-lg btn-default" style="margin-right:15px;float:right" onclick="openProductPopup()">New Item</button>
      </div>
      <div style="clear:both"></div>
    </div>
    <div class="col-md-3">
      <h3>Summary</h3>
      <table class="table dpsumtbl">
        <tr>
          <td>Subtotal</td>
          <td class="sumtd main-sum"><span id="main-sum-subtotal">0,00</span> €</td>
        </tr>
        <tr>
          <td>Tax</td>
          <td class="sumtd main-sum"><span id="main-sum-tax">0,00</span> €</td>
        </tr>
        <tr class="ftbold">
          <td>Total</td>
          <td class="sumtd main-sum"><span id="main-sum-total">0,00</span> €</td>
        </tr>
      </table>
      <div class="text-center mt25 btn-main-block">
        <button type="button" class="btn btn-success btn-main-big" onclick="openPaymentPopup();">Payment</button>
        <button type="button" class="btn btn-warning btn-main-big" onclick="openCancelOrderPopup();">Cancel<br/>Previous Order</button>
      </div>
      <div class="text-center mt10 btn-main-block">
        <button type="button" class="btn btn-default btn-main-big" onclick="openReprintPopup();">Print Old Receipt</button>
        <button type="button" class="btn btn-default btn-main-big" onclick="openCashBoxPopup();">Open<br />Cash Drawer</button>
      </div>
      <div class="info-block">
        <!--button type="button" class="btn btn-lg btn-warning" style="width:320px;margin-left:60px" onclick="dailyClosing()">Daily Closing</button> <br/-->
        <span class="ftbold">Cashier : </span> {{ Auth::user()->name }} <br/>
        <span class="m-ml25 ftbold">Date : </span> <span id="display-date">  </span> <br/>
        <span class="m-ml25 ftbold">Time : </span> <span id="display-time">  </span> <br/>
      </div>
    </div>
  </div>
</div>
<div class="parentDisable" style="display:none;" id="disable-bg"></div>
<div class="popupDisable" style="display:none;" id="disable-popup"></div>
<div class="popupAllDisable" style="display:none;" id="disable-all-popup"></div>
@include('sweetalert::alert')
@include('sales.popup.addItem')
@include('sales.popup.calculator')
@include('sales.popup.calculator-sm')
@include('sales.popup.calculator-sm-cancel')
@include('sales.popup.calculator-index')
@include('sales.popup.cancelOrder')
@include('sales.popup.cancelOrder-all')
@include('sales.popup.cancelOrder-partial')
@include('sales.popup.cashBox')
@include('sales.popup.newItem')
@include('sales.popup.payment')
@include('sales.popup.reprint')
@include('sales.popup.sales-summary')
@include('sales.popup.Voucher')

@endsection
@section('scripts')
<script type="text/javascript" src="{{ asset('js/ajax.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/addItem.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/calculator.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/cancelOrder.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/cashBox.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/newItem.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/payment.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/reprint.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/sales.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/salesSummary.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/voucher.js') }}"></script>
@endsection

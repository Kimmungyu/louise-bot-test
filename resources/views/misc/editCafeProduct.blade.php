@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <h2 class="support-header">Edit cafe Product</h2>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="edit-block">
        <form method="POST" action="/misc/updateCafeProduct">
          {{ csrf_field() }}
          <input type="hidden" value="{{ $item->id }}" name="id" />
          <div class="list-item">
            <div class="title">
              Product code
            </div>
            <div class="content">
              <input type="text" class="form-control" placeholder="{{ $item->product_code }}" value="{{ $item->product_code }}" name="product_code" readonly />
            </div>
          </div>
          <div class="list-item">
            <div class="title">
              Product name (Roman)
            </div>
            <div class="content">
              <input type="text" class="form-control" value="{{ $item->name }}" name="name" />
            </div>
          </div>
          <div class="list-item">
            <div class="title">
              Product name (Korean)
            </div>
            <div class="content">
              <input type="text" class="form-control" placeholder="" name="name_kr" value="{{ $item->name_kr }}" />
            </div>
          </div>
          <div class="list-item">
            <div class="title">
              Description
            </div>
            <div class="content">
              <textarea class="form-control" rows="4" name="description"/>{{ $item->description }}</textarea>
            </div>
          </div>
          <div class="list-item">
            <div class="title">
              Ingredients
            </div>
            <div class="content">
              <textarea class="form-control" rows="4" name="ingredients">{{ $item->ingredients }}</textarea>
            </div>
          </div>
          <div class="list-item">
            <div class="title">
              Filling
            </div>
            <div class="content">
              <textarea class="form-control" rows="4" name="fillings">{{ $item->fillings }}</textarea>
            </div>
          </div>
          <div class="list-btn">
            <input type="submit" class="btn btn-md btn-success" value="Submit" />
            <input type="button" class="btn btn-md btn-default" value="Cancel" />
          </div>
        </form>
      </div>
      <div><br></div>
    </div>
  </div>
</div>
@section('scripts')
<script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/misc/printBarcode.js') }}"></script>
@endsection
@endsection

@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="support-header">Print Name Tag</h2>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-block">
        <div class="row">
          <div class="col-md-2 form-block-header">
            Tag Type
          </div>
          <div class="col-md-6">
            <div class="radio-block">
              <label class="radio-inline"><input type="radio" id="check1" data-tag-name="cafe" name="optradio" checked>Bread/Cake/Cafe</label>
              <label class="radio-inline"><input type="radio" id="check2" data-tag-name="product" name="optradio">Shop Product</label>
              <label class="radio-inline"><input type="radio" id="check3" data-tag-name="general" name="optradio">General</label>
            </div>
            <div >
              <p class="name-tag-description">인쇄하시려는 네임태그 종류를 선택하세요.</p>
              <ul class="name-tag-description-ul">
                <li>Bread/Cake/Cafe는 카페와 베이커리에서 사용하는 네임태그(빵/케이크용)입니다.</li>
                <li>General은 A7 세로 규격에 맞춰 일정한 텍스트를 인쇄할 수 있습니다.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div id="form-type-cafe">
        <div class="sample-block">
          <div class="row">
            <div class="col-md-2">Sample Image</div>
            <div class="col-md-10"><img src="{{ asset('images/sample-1.png') }}" width="240" /></div>
          </div>
        </div>
        <div class="form-block">
          <div class="row">
            <div class="col-md-2 form-block-header">
              Cafe Product Name (Deutsch)
            </div>
            <div class="col-md-10">
              <input id="bread_name_deutsch" type="text" class="form-control pn-input" placeholder="Name (Deutsch)">
            </div>
          </div>
        </div>
        <div class="form-block">
          <div class="row">
            <div class="col-md-2 form-block-header">
              Cafe Product Price
            </div>
            <div class="col-md-10">
              <input id="bread_price" type="text" class="form-control pn-input" placeholder="Price">
            </div>
          </div>
        </div>
        <div class="form-block">
          <div class="row">
            <div class="col-md-2 form-block-header">
              Cafe Product Description (Deutsch)
            </div>
            <div class="col-md-10">
              <input id="bread_description" type="text" class="form-control pn-input" placeholder="Description (Deutsch)">
            </div>
          </div>
        </div>
        <div class="form-block">
          <div class="row">
            <div class="col-md-2 form-block-header">
              Cafe Product Name (Korean)
            </div>
            <div class="col-md-10">
              <input id="bread_name_korean" type="text" class="form-control" placeholder="Name (Korean)">
            </div>
          </div>
        </div>
      </div>

      <div id="form-type-product">
        <div class="sample-block">
          <div class="row">
            <div class="col-md-2">Sample Image</div>
            <div class="col-md-10"><img src="{{ asset('images/sample-2.png') }}" width="240" /></div>
          </div>
        </div>
        <div class="form-block">
          <div class="row">
            <div class="col-md-2 form-block-header">
              Product Name
            </div>
            <div class="col-md-10">
              <input id="product_name" type="text" class="form-control" placeholder="상품 이름">
            </div>
          </div>
        </div>
          <div class="form-block">
          <div class="row">
            <div class="col-md-2 form-block-header">
              Product Price
            </div>
            <div class="col-md-10">
              <input id="product_price" type="text" class="form-control" placeholder="상품 가격">
            </div>
          </div>
        </div>
        <div class="form-block">
          <div class="row">
            <div class="col-md-2 form-block-header">
              Product Description
            </div>
            <div class="col-md-10">
              <input id="product_description" type="text" class="form-control" placeholder="상품 설명">
            </div>
          </div>
        </div>
      </div>

      <div id="form-type-general">
        <div class="sample-block">
          <div class="row">
            <div class="col-md-2">Sample Image</div>
            <div class="col-md-10"><img src="{{ asset('images/sample-3.png') }}" width="240" /></div>
          </div>
        </div>
        <div class="form-block">
          <div class="row">
            <div class="col-md-2 form-block-header">
              Content
            </div>
            <div class="col-md-10">
              <textarea id="general_content" class="form-control" rows="4"></textarea>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="btn-block">
        <input type="button" class="btn btn-md btn-success" onclick="showPrint()" value="인쇄하기" />
        <input type="button" id="clearBtn" class="btn btn-md btn-default" value="CLEAR" />
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{ asset('js/common.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/misc/PrintNameTag.js') }}"></script>
<script>
  $(document).ready(function(){
    $("input[name='optradio']").click(function(){
      var cafeObj = $("#form-type-cafe");
      var productObj = $("#form-type-product");
      var generalObj = $("#form-type-general");

      switch($(this).attr("data-tag-name")){
        case "cafe":
          cafeObj.show();
          productObj.hide();
          generalObj.hide();
        break;
        case "product":
          cafeObj.hide();
          productObj.show();
          generalObj.hide();
        break;
        case "general":
          cafeObj.hide();
          productObj.hide();
          generalObj.show();
        break;
      }
    });

    $('#clearBtn').click(function(){
  		$('input:text').val('');
  	});
    
  });
</script>
@endsection

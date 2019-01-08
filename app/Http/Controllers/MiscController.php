<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ProductService;

class MiscController extends Controller
{
  private $productService;

  public function __construct(ProductService $productService)
  {
      $this->productService = $productService;
  }

  public function printBarcode(){
    return view('misc.printBarcode');
  }

  public function checkPrice() {
    return view('misc.checkPrice');
  }

  public function printNameTag() {
    return view('misc.printNameTag');
  }

  public function findProduct(Request $request) {
    return response(['products' => $this->productService->findProduct($request->all())]);
  }

  public function findDamiProduct(Request $request) {
    return response(['products' => $this->productService->findDamiProduct($request->all())]);
  }

  public function listProduct() {
    $products = $this->productService->listProduct([]);
    return view('misc.listProduct',['products'=>$products]);
  }
}

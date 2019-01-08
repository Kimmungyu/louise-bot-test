<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ProductService;

class ProductController extends Controller
{
  private $productService;

  public function __construct(ProductService $productService)
  {
      $this->middleware('auth');
      $this->productService = $productService;
  }

  public function getProductWithBarcode($barcode) {
    return response(['product' => $this->productService->getProductWithBarcode($barcode)]);
  }

  public function getProductWithProductCode($productCode) {
    return response(['product' => $this->productService->getProductWithProductCode($productCode)]);
  }

  public function findProduct(Request $request) {
    return response(['products' => $this->productService->findProduct($request->all())]);
  }

  public function findDamiProduct(Request $request) {
    return response(['products' => $this->productService->findDamiProduct($request->all())]);
  }

  public function addNewProduct(Request $request) {
    return response($this->productService->addNewProduct($request->all()));
  }

  public function syncProduct() {
    return response($this->productService->syncProduct());
  }

  // 2018.11.12 WonkyoungLee
  public function getCouponInfo($code) {
    return response($this->productService->getCouponInfo($code));
  }
}

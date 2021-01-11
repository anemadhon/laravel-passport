<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = $this->_userId();
        $products = Product::where('user_id', $userId)->get();

        if (!$products) {
            return response()->json([
                'Products Not Found'
            ]);
        }

        return ProductResource::collection($products);
    }

    public function all()
    {
        $products = Product::all();

        if (!$products) {
            return response()->json([
                'Products Not Found'
            ]);
        }

        return ProductResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
    {
        $product->user_id = $this->_userId();
        $product->name = $request->name;
        $product->price = $request->price;

        if (!$product->save()) {
            return response()->json([
                'New Product failed to Created'
            ]);
        }

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        if (!$product) {
            return response()->json([
                'Product not found'
            ]);
        }

        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $singleProduct = $this->_product($product->id);
        
        if (!$singleProduct) {
            return response()->json([
                'Product failed to Updated'
            ]);
        }

        $singleProduct->name = $request->name;
        $singleProduct->price = $request->price;

        $singleProduct->save();

        return new ProductResource($singleProduct);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $singleProduct = $this->_product($product->id);
        
        if (!$singleProduct) {
            return response()->json([
                'Product failed to Deleted'
            ]);
        }

        $singleProduct->delete();

        return new ProductResource($singleProduct);
    }
    
    protected function _userId()
    {
        return auth('api')->user()->id;
    }

    protected function _product($id)
    {
        $userId = $this->_userId();
        $product = Product::where('user_id', $userId)->where('id', $id)->first();

        return $product;
    }
}

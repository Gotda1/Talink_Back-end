<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\Unit;

class ProductsController extends Controller
{
    public function __construct(){
        $this->middleware("jwt");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $products = Product::with(["category", "type", "unit"])
                        ->get();
                        
            return $this->successResponse([
                "products" => $products
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $units              = Unit::where("status",1)->get();
            $product_categories = ProductCategory::where("status", 1)->get();
            $product_types      = ProductType::where("status", 1)->get();
            
            return $this->successResponse([
                "units"              => $units,
                "product_categories" => $product_categories,
                "product_types"      => $product_types,
            ]);
        } catch (\Throwable $e) {
            # Response
            return $this->failedResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data["unit_id"] = 1;
            $data["product_category_id"] = 1;
            $data["product_type_code"] = "SRV";
            $product = Product::create($data);

            return response()->json([
                "head" => "success",
                "body" => ["product" => $product]
            ], 200);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product = Product::with(["category", "type", "unit"])
                                ->find( $id );

            return $this->successResponse([
                "product" => $product
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);
            $product->fill($request->validated());
            $product->save();
            

            return $this->successResponse([
                "product" => $product
            ]);         
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id)->delete();

            return $this->successResponse([
                "product" => $product
            ]);         
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }
}

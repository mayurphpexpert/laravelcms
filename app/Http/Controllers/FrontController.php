<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('is_featured','Yes')->orderBy('id','DESC')->take(8)->where('status',1)->get();
        $data['featuredProducts'] = $products;
        $latestProducts = Product::orderBy('id','DESC')->where('status',1)->take(8)->get();
        $data['latestProducts'] = $latestProducts;


        return view('front.home',$data);
    }

    public function addToWishlist(Request $request){

        if(Auth::check() == false){

            session(['url.intended' => url()->previous()]);

            return response()->json([
                'status' =>false
            ]);
        }

        $product = Product::where('id',$request->id)->first();

        if($product == null){
            return response()->json([
                'status' => true,
                'message' => '<div class="alert alert-danger">Product not found.</div>'
            ]);
        }

        Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id,
            ]
        );

        // $wishlist = new Wishlist();
        // $wishlist->user_id = Auth::user()->id;
        // $wishlist->product_id = $request->id;
        // $wishlist->save();


        return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-success"><strong>"'.$product->title.'"</strong> added in your wishlist</div>'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

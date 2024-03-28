<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        $categorySelected = '';
        // $subCategorySelected = '';
        $brandsArray = [];

        if(!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
        }
        // $brandsArray = $request->get('brand');


        $categories = Category::orderBy('name','ASC')->whereNull('parent_id')->where('status',1)->get();
        $brands = Brands::orderBy('name','ASC')->where('status',1)->get();
        $products = Product::where('status',1);
        // $products = Product::orderBy('id','DESC')->where('status',1)->get();

        //Apply filter here
        if(!empty($categorySlug)){
            $category = Category::where('slug',$categorySlug)->first();
            // dd($category);
            $products= $products->where('category_id',$category->id);
            $categorySelected = $category->id;
        }
        if(!empty($subCategorySlug)){
            $subCategory = Category::where('slug',$subCategorySlug)->first();
            $products= $products->where('category_id',$subCategory->id);
            $categorySelected = $subCategory->id;
            //  dd($products);
        }

        if(!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
            $products= $products->whereIn('brand_id',$brandsArray);

        }

        if($request->get('price_max') != '' && $request->get('price_min') != ''){
            if($request->get('price_max') == 1000){
                $products = $products->whereBetween('price',[intval($request->get('price_min')),10000000]);
            } else {
                $products = $products->whereBetween('price',[intval($request->get('price_min')),intval($request->get('price_max'))]);
            }
            
        }


        if(!empty($request->get('search'))){
            $products= $products->where('title','like','%'.$request->get('search').'%');
        }


        if($request->get('sort') != ''){
            if($request->get('sort') == 'latest'){
                $products = $products->orderBy('id','DESC');
            }else if($request->get('sort') == 'price_asc'){
                $products = $products->orderBy('price','ASC');
            } else{
                $products = $products->orderBy('price','DESC');

            }
        } else{
            $products = $products->orderBy('id','DESC');
        }
        $products= $products->paginate(6);

        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['products'] = $products;
        $data['categorySelected'] = $categorySelected;
        // $data['subCategorySelected'] = $subCategorySelected;
        $data['brandsArray'] = $brandsArray;
        $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
        $data['priceMin'] = intval($request->get('price_min'));
        $data['sort'] = $request->get('sort');
        

        return view('front.shop', $data);
    }


    public function product($slug){
        $product = Product::where('slug',$slug)->withCount('product_ratings')->withSum('product_ratings','rating')->with(['product_images','product_ratings'])->first();

        if ($product == null) {
            abort(404);
        }
        // dd($product);

        $relatedProducts = [];
        if($product->related_products != ''){
            $productArray = explode(',',$product->related_products);

            $relatedProducts = Product::whereIn('id',$productArray)->where('status',1)->with('product_images')->get();
        }

        $data['product'] = $product;        
        $data['relatedProducts'] = $relatedProducts;  
        
        //rating calculation
        //"product_ratings_count" => 5
        // "product_ratings_sum_rating" => 16.0
        $avgRating = '0.00';
        $avgRatingPer = 0;
        if($product->product_ratings_count > 0){
            $avgRating = number_format(($product->product_ratings_sum_rating/$product->product_ratings_count),2);
            $avgRatingPer = ($avgRating*100)/5;
        }
        $data['avgRating'] = $avgRating;
        $data['avgRatingPer'] = $avgRatingPer;

        return view('front.product',$data);
    }

    public function saveRating(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:5',
            'email' => 'required|email',
            'comment' => 'required',
            'rating' => 'required'

        ]);
        
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


        $count = ProductRating::where('email',$request->email)->count();

        if($count > 0){
            session()->flash('error','You already rated this product.');
            return response()->json([
                'status' => true,
                
            ]);
        }

        $productRating = new ProductRating();
        $productRating->product_id = $id;
        $productRating->username = $request->name;
        $productRating->email = $request->email;
        $productRating->comment = $request->comment;
        $productRating->rating = $request->rating;
        $productRating->status = 0;
        $productRating->save();

        session()->flash('success','Thanks for your rating.');

        return response()->json([
            'status' => true,
            'message' => 'Thanks for your rating.'
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

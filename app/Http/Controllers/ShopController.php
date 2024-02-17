<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

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
        $product = Product::where('slug',$slug)->with('product_images')->first();

        if ($product == null) {
            abort(404);
        }

        $relatedProducts = [];
        if($product->related_products != ''){
            $productArray = explode(',',$product->related_products);

            $relatedProducts = Product::whereIn('id',$productArray)->with('product_images')->get();
        }

        $data['product'] = $product;        
        $data['relatedProducts'] = $relatedProducts;        

        return view('front.product',$data);
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
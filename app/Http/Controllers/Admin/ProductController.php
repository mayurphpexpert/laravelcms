<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductRating;
use App\Models\SubCategories;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $product = Product::latest('id')->with('product_images');
        $product = Product::query()->with('product_images');

        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $product->orderBy('id', $order);
        } else {
            $product->latest()->orderBy('id','DESC'); // Default sorting if not specified
        }
        
        if ($request->get('keyword') != ""){
            $product = $product->where('title','like','%'.$request->keyword.'%');
        }

        // $product = $product->paginate();
        // $perPage = $request->get('pagination_limit', 10); // Default: 25 items per page
        // $product = $product->paginate($perPage);
        $perPage = $request->input('per_page', session('perPage', 10));
        $product = $product->paginate($perPage);
        // Store selected perPage value in session
        session(['perPage' => $perPage]);


        $data['products'] = $product;
        return view('admin.products.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $brands = Brands::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.products.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->image_array);
        // exit();
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',

        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->short_description = $request->short_description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            // $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->meta_title = $request->meta_title;
            $product->meta_canonical_url = $request->meta_canonical_url;
            $product->meta_description = $request->meta_description;
            $product->meta_keyword = $request->meta_keyword;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->save();


            //save gallery pics
            if(!empty($request->image_array)){
                foreach ($request->image_array as $temp_image_id) {

                    $tempImageInfo = TempImage::find($temp_image_id); 
                    $extArray = explode('.',$tempImageInfo->name);
                    $ext = last($extArray); //like jpg,gif etc

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //generate  product thumbnails


                    //large Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/product/large/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null, function($constraint){
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);


                    //small image
                    // $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/product/small/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->fit(300,300);
                    $image->save($destPath);

                }
            }

            $request->session()->flash('success','Product added successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product added successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
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
    public function edit($id, Request $request)
    {
        $product = Product::find($id);

        if (empty($product)) {
            //$request->session()->flash('error','Product not found');
            return redirect()->route('products.index')->with('error','Product not found');
        }
        
        //fatch product images
        $productImages = ProductImage::where('product_id',$product->id)->get();

        $subCategories = SubCategories::where('category_id',$product->category_id)->get();

        //fetch related product
        $relatedProducts = [];
        if($product->related_products != ''){
            $productArray = explode(',',$product->related_products);

            $relatedProducts = Product::whereIn('id',$productArray)->with('product_images')->get();
        }
        
        $data = [];
        
        // $categories = Category::orderBy('name','ASC')->get();
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $brands = Brands::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $data['productImages'] = $productImages;
        $data['relatedProducts'] = $relatedProducts;


        return view('admin.products.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',

        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->short_description = $request->short_description;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            // $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->meta_title = $request->meta_title;
            $product->meta_canonical_url = $request->meta_canonical_url;
            $product->meta_description = $request->meta_description;
            $product->meta_keyword = $request->meta_keyword;
            $product->shipping_returns = $request->shipping_returns;
            $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
            $product->save();


            
            $request->session()->flash('success','Product Updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product Updated successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        $product = Product::find($id);

        if(empty($product)) {
            $request->session()->flash('error','Product not found.');
            return response()->json([
                'status' => false,
                'notFound' => true
            ]);
        }

        $productImages = ProductImage::where('product_id',$id)->get();

        if (!empty($productImages)){
            foreach ($productImages as $productImage){
                //delete images 
                File::delete(public_path('uploads/product/large/'.$productImage->image));
                File::delete(public_path('uploads/product/small/'.$productImage->image));
            }
            ProductImage::where('product_id',$id)->delete();            
        }

        $product->delete();

        $request->session()->flash('success','Product deleted successfully.');

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully.'
        ]);        
    }

    public function getProducts(Request $request){

        $tempProduct = [];
        if($request->term != ""){
            $products = Product::where('title','like','%'.$request->term.'%')->get();

            if ($products != null){
                foreach($products as $product){
                    $tempProduct[] = array('id' => $product->id, 'text' => $product->title);
                }
            }
        }

        return response()->json([
            'tags' => $tempProduct,
            'status' => true
        ]);
    }

    public function productRatings(Request $request){
        $ratings = ProductRating::select('product_ratings.*','products.title as productTitle');
        // ->orderBy('product_ratings.created_at','DESC')
        $ratings = $ratings->leftJoin('products','products.id','product_ratings.product_id');
        // $ratings = $ratings->paginate(10);

        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $ratings->orderBy('id', $order);
        } else {
            $ratings->latest()->orderBy('id','DESC'); // Default sorting if not specified
        }
        
        if ($request->get('keyword') != ""){
            $ratings = $ratings->orWhere('products.title','like','%'.$request->keyword.'%');
            $ratings = $ratings->orWhere('product_ratings.username','like','%'.$request->keyword.'%');
            $ratings = $ratings->orWhere('comment','like','%'.$request->keyword.'%');
        }

        $perPage = $request->input('per_page', session('perPage', 10));
        $ratings = $ratings->paginate($perPage);

        // Store selected perPage value in session
        session(['perPage' => $perPage]);

        return view('admin.products.ratings',[
            'ratings' => $ratings,
        ]);
    }

    public function changeRatingStatus(Request $request){

        $productRating = ProductRating::find($request->id);
        $productRating->status = $request->status;
        $productRating->save();

        session()->flash('success','Status changed successfully.');

        return response()->json([
            'status' => true,
        ]);

    }

    public function bulkRatingPublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected categories to 1 (Publish)
            DB::table('product_ratings')->whereIn('id', $ids)->update(['status' => 1]);

            $request->session()->flash('success', 'Status changed successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Status changed successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Review selected for publishing'
            ]);
        }
    }

    public function bulkRatingUnpublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected categories to 0 (Unpublish)
            DB::table('product_ratings')->whereIn('id', $ids)->update(['status' => 0]);

            $request->session()->flash('success', 'Status changed successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Status changed successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Review selected for change status'
            ]);
        }
    }


    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Perform bulk deletion logic here, similar to your existing delete method
            foreach ($ids as $id) {
                $product = Product::find($id);

                if ($product) {
                    $productImages = ProductImage::where('product_id',$id)->get();

                    if (!empty($productImages)){
                        foreach ($productImages as $productImage){
                            //delete images 
                            File::delete(public_path('uploads/product/large/'.$productImage->image));
                            File::delete(public_path('uploads/product/small/'.$productImage->image));
                        }
                        ProductImage::where('product_id',$id)->delete();            
                    }

                    $product->delete();
                }
            }
            $request->session()->flash('success','Product deleted successfully');

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully' 
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Product selected for deletion' 
            ]);
        }
    }

    public function bulkPublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected categories to 1 (Publish)
            DB::table('products')->whereIn('id', $ids)->update(['status' => 1]);

            $request->session()->flash('success', 'Product published successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Product published successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Product selected for publishing'
            ]);
        }
    }

    public function bulkUnpublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected categories to 0 (Unpublish)
            DB::table('products')->whereIn('id', $ids)->update(['status' => 0]);

            $request->session()->flash('success', 'product unpublished successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Product unpublished successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Product selected for unpublishing'
            ]);
        }
    }

    /**
     * Export products to CSV.
     */
    public function export()
    {
        return Excel::download(new ProductsExport, 'products.csv');
    }
}

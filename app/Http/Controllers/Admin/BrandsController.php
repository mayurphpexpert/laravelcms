<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;


class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $brands = Brands::latest('id');
        $brands = Brands::query();

        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $brands->orderBy('id', $order);
        } else {
            $brands->latest()->orderBy('id','DESC'); // Default sorting if not specified
        }

        if (!empty($request->get('keyword'))){
            $brands = $brands->where('name','like','%'.$request->get('keyword').'%');
        }

        // $brands = $brands->paginate(10);
        // $perPage = $request->get('pagination_limit', 10); // Default: 25 items per page
        // $brands = $brands->paginate($perPage);
        $perPage = $request->input('per_page', session('perPage', 10));
        $brands = $brands->paginate($perPage);
        // Store selected perPage value in session
        session(['perPage' => $perPage]);

        return view('admin.brands.list',compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands'
        ]);

        if ($validator->passes()){
            $brand = new Brands();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->description = $request->description;
            $brand->meta_title = $request->meta_title;
            $brand->meta_canonical_url = $request->meta_canonical_url;
            $brand->meta_description = $request->meta_description;
            $brand->meta_keyword = $request->meta_keyword;
            $brand->status = $request->status;
            $brand->save();

            //Save image here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $brand->id.'.'.$ext;
                $sPath= public_path().'/temp/'.$tempImage->name;
                $dPath= public_path().'/uploads/brand/'.$newImageName;
                File::copy($sPath,$dPath);

                //generate image thumbnail
                $dPath= public_path().'/uploads/brand/thumb/'.$newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function($constraint){
                    $constraint->upsize();
                });
                $img->save($dPath);

                $brand->image = $newImageName;
                $brand->save();

            }

            $request->session()->flash('success','Brand added Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully.'
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
        $brand = Brands::find($id);

        if (empty($brand)) {
            $request->session()->flash('error','Record not found.');
            return redirect()->route('brands.index');

        } 

        $data['brand'] = $brand;
        return view('admin.brands.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $brand = Brands::find($id);

        if (empty($brand)) {
            $request->session()->flash('error','Record not found.');
            return response([
                'status' => false,
                'notFound' => true
            ]);
            // return redirect()->route('brands.index');

        } 

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
        ]);

        if ($validator->passes()){
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->description = $request->description;
            $brand->meta_title = $request->meta_title;
            $brand->meta_canonical_url = $request->meta_canonical_url;
            $brand->meta_description = $request->meta_description;
            $brand->meta_keyword = $request->meta_keyword;
            $brand->save();

            $oldImage = $brand->image;

            //Save image here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $brand->id.'-'.time().'.'.$ext;
                $sPath= public_path().'/temp/'.$tempImage->name;
                $dPath= public_path().'/uploads/brand/'.$newImageName;
                File::copy($sPath,$dPath);

                //generate image thumbnail
                $dPath= public_path().'/uploads/brand/thumb/'.$newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function($constraint){
                    $constraint->upsize();
                });
                $img->save($dPath);

                $brand->image = $newImageName;
                $brand->save();

                //delete old image here
                File::delete(public_path().'/uploads/brand/thumb/'.$oldImage);
                File::delete(public_path().'/uploads/brand/'.$oldImage);

            }

            $request->session()->flash('success','Brand updated Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully.'
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
    public function destroy(Request $request, $id)
    {
        $brand = Brands::find($id);
        if (empty($brand)){
            $request->session()->flash('error','Record not found.');
            return response([
                'status' => false,
                'notFound' => true
            ]);
            // return redirect()->route('sub-categories.index');
        }
        // Delete image and perform deletion
        File::delete(public_path().'/uploads/brand/'.$brand->image);
        File::delete(public_path().'/uploads/brand/thumb/'.$brand->image);

        $brand->delete();

        $request->session()->flash('success','Brand deleted Successfully.');

            return response([
                'status' => true,
                'message' => 'Brand deleted Successfully.' 
            ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Perform bulk deletion logic here, similar to your existing delete method
            foreach ($ids as $id) {
                $brand = Brands::find($id);

                if ($brand) {
                    // Delete image and perform deletion
                    File::delete(public_path().'/uploads/brand/'.$brand->image);
                    File::delete(public_path().'/uploads/brand/thumb/'.$brand->image);

                    $brand->delete();
                }
            }
            $request->session()->flash('success','brand deleted successfully');

            return response()->json([
                'status' => true,
                'message' => 'brand deleted successfully' 
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No brand selected for deletion' 
            ]);
        }
    }

    public function bulkPublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected categories to 1 (Publish)
            DB::table('brands')->whereIn('id', $ids)->update(['status' => 1]);

            $request->session()->flash('success', 'brand published successfully.');

            return response()->json([
                'status' => true,
                'message' => 'brand published successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No brand selected for publishing'
            ]);
        }
    }

    public function bulkUnpublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected categories to 0 (Unpublish)
            DB::table('brands')->whereIn('id', $ids)->update(['status' => 0]);

            $request->session()->flash('success', 'brand unpublished successfully.');

            return response()->json([
                'status' => true,
                'message' => 'brand unpublished successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No brand selected for unpublishing'
            ]);
        }
    }

    
}

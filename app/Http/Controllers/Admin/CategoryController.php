<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// use Image;
use Intervention\Image\ImageManagerStatic as Image;
// use Intervention\Image\Image;
// use Intervention\Image\ImageManager;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $categories = Category::latest()->orderBy('id','DESC');
        $categories = Category::query();


        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $categories->orderBy('id', $order);
        } else {
            $categories->latest()->orderBy('id', 'DESC'); // Default sorting if not specified
        }

        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        // $categories = $categories->paginate(10);
        // $perPage = $request->get('pagination_limit', 10); // Default: 25 items per page
        // $categories = $categories->paginate($perPage);
        $perPage = $request->input('per_page', session('perPage', 10));
        $categories = $categories->paginate($perPage);
        // Store selected perPage value in session
        session(['perPage' => $perPage]);

        return view('admin.category.list', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->passes()) {

            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->parent_id = $request->parent_id;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->description = $request->description;
            $category->meta_title = $request->meta_title;
            $category->meta_canonical_url = $request->meta_canonical_url;
            $category->meta_description = $request->meta_description;
            $category->meta_keyword = $request->meta_keyword;
            $category->save();

            //Save image here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);

                //generate image thumbnail
                $dPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPath);

                $category->image = $newImageName;
                $category->save();
            }


            $request->session()->flash('success', 'Category added Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Category added Successfully.'
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
     * Request $request
     */
    public function edit($categoryId, Request $request)
    {
        $category = Category::find($categoryId);
        $categories = Category::whereNull('parent_id')->get();

        if (empty($category)) {
            return redirect()->route('categories.index');
        }

        return view('admin.category.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     * string $id
     */
    public function update(Request $request, $categoryId)
    {
        $category = Category::find($categoryId);
        $categories = Category::all();
        // $categories = Category::whereNull('parent_id')->get();

        if (empty($category)) {
            $request->session()->flash('error', 'Category not found.');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not found.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id . ',id',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->passes()) {

            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->parent_id = $request->parent_id;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->description = $request->description;
            $category->meta_title = $request->meta_title;
            $category->meta_canonical_url = $request->meta_canonical_url;
            $category->meta_description = $request->meta_description;
            $category->meta_keyword = $request->meta_keyword;
            $category->save();

            $oldImage = $category->image;

            //Save image here
            if (!empty($request->image_id)) {
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id . '-' . time() . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempImage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);

                //generate image thumbnail
                $dPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                $img = Image::make($sPath);
                // $img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPath);

                $category->image = $newImageName;
                $category->save();

                //delete old image here
                File::delete(public_path() . '/uploads/category/thumb/' . $oldImage);
                File::delete(public_path() . '/uploads/category/' . $oldImage);
            }


            $request->session()->flash('success', 'Category updated Successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Category updated Successfully.'
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
     * string $id
     */
    public function destroy(Request $request, $categoryId)
    {
        $category = Category::find($categoryId);
        if (empty($category)) {
            // return redirect()->route('categories.index');
            $request->session()->flash('error', 'Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category not found'
            ]);
        }

        //delete image here
        File::delete(public_path() . '/uploads/category/' . $category->image);
        File::delete(public_path() . '/uploads/category/thumb/' . $category->image);

        $category->delete();

        $request->session()->flash('success', 'Category deleted successfully');

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);
    }


    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Perform bulk deletion logic here, similar to your existing delete method
            foreach ($ids as $id) {
                $category = Category::find($id);

                if ($category) {
                    // Delete image and perform deletion
                    File::delete(public_path() . '/uploads/category/' . $category->image);
                    File::delete(public_path() . '/uploads/category/thumb/' . $category->image);

                    $category->delete();
                }
            }
            $request->session()->flash('success', 'Category deleted successfully');

            return response()->json([
                'status' => true,
                'message' => 'Categories deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No categories selected for deletion'
            ]);
        }
    }



    public function bulkPublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected categories to 1 (Publish)
            DB::table('categories')->whereIn('id', $ids)->update(['status' => 1]);

            $request->session()->flash('success', 'Categories published successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Categories published successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No categories selected for publishing'
            ]);
        }
    }

    public function bulkUnpublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected categories to 0 (Unpublish)
            DB::table('categories')->whereIn('id', $ids)->update(['status' => 0]);

            $request->session()->flash('success', 'Categories unpublished successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Categories unpublished successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No categories selected for unpublishing'
            ]);
        }
    }
}

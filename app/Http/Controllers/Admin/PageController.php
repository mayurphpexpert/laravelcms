<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pages = page::query();

        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $pages->orderBy('id', $order);
        } else {
            $pages->latest()->orderBy('id', 'DESC'); // Default sorting if not specified
        }

        if (!empty($request->get('keyword'))) {
            $pages = $pages->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        // $categories = $categories->paginate(10);
        // $perPage = $request->get('pagination_limit', 10); // Default: 25 items per page
        // $categories = $categories->paginate($perPage);
        $perPage = $request->input('per_page', session('perPage', 10));
        $pages = $pages->paginate($perPage);
        // Store selected perPage value in session
        session(['perPage' => $perPage]);

        return view('admin.pages.list',[
            'pages' => $pages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $page = new page();
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page added successfully';

        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
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
        $page = page::find($id);

        if($page == null){
            session()->flash('error','Page not found');

            return redirect()->route('pages.index');
        }

        return view('admin.pages.edit',[
            'page' => $page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $page = page::find($id);

        if($page == null){
            session()->flash('error','Page not found');

            return response()->json([
                'status' => true,                
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page updated successfully';

        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $page = page::find($id);

        if($page == null){
            session()->flash('error','Page not found');

            return response()->json([
                'status' => true,                
            ]);
        }
        
        $page->delete();

        $message = 'Page deleted successfully';

        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }
}

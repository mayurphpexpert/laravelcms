<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orderStatus = OrderStatus::query();
        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $orderStatus->orderBy('id', $order);
        } else {
            $orderStatus->latest()->orderBy('id', 'DESC'); // Default sorting if not specified
        }

        if (!empty($request->get('keyword'))) {
            $orderStatus = $orderStatus->where('name', 'like', '%' . $request->get('keyword') . '%');
        }

        // $categories = $categories->paginate(10);
        // $perPage = $request->get('pagination_limit', 10); // Default: 25 items per page
        // $categories = $categories->paginate($perPage);
        $perPage = $request->input('per_page', session('perPage', 10));
        $orderStatus = $orderStatus->paginate($perPage);
        // Store selected perPage value in session
        session(['perPage' => $perPage]);

        return view('admin.all_settings.order_status.list',[
            'orderStatus' => $orderStatus,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.all_settings.order_status.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $orderstatus = new OrderStatus();
        $orderstatus->name = $request->name;
        $orderstatus->slug = $request->slug;
        $orderstatus->status = $request->status;
        $orderstatus->save();

        $message = 'Order Status added successfully';

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
        $orderstatus = OrderStatus::find($id);

        if($orderstatus == null){
            session()->flash('error','record not found');

            return redirect()->route('orderStatus.index');
        }

        return view('admin.all_settings.order_status.edit',[
            'orderstatus' => $orderstatus,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $orderstatus = OrderStatus::find($id);

        if($orderstatus == null){
            session()->flash('error','Record not found');

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
        $orderstatus->name = $request->name;
        $orderstatus->slug = $request->slug;
        $orderstatus->status = $request->status;
        $orderstatus->save();

        $message = 'Order Status updated successfully';

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
        $orderstatus = OrderStatus::find($id);

        if($orderstatus == null){
            session()->flash('error','Record not found');

            return response()->json([
                'status' => true,                
            ]);
        }
        
        $orderstatus->delete();

        $message = 'Order Status successfully';

        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }
}

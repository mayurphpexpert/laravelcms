<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // latest('orders.created_at')->
        $orders = Order::select('orders.*','users.name','users.email');
        $orders = $orders->leftJoin('users','users.id','orders.user_id');
        
        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $orders->orderBy('id', $order);
        } else {
            $orders->latest()->orderBy('id', 'DESC'); // Default sorting if not specified
        }
        
        if($request->get('keyword') != ""){
            $orders = $orders->where('users.name','like','%'.$request->keyword.'%');
            $orders = $orders->orWhere('users.email','like','%'.$request->keyword.'%');
            $orders = $orders->orWhere('orders.id','like','%'.$request->keyword.'%');
        }



        $perPage = $request->input('per_page', session('perPage', 10));
        $orders = $orders->paginate($perPage);
        // Store selected perPage value in session
        session(['perPage' => $perPage]);
        // $orders = $orders->paginate(10);

        $data['orders'] = $orders;

        return view('admin.orders.list',$data);
    }


    public function detail($orderId){

        $order = Order::select('orders.*','countries.name as countryName')
                        ->where('orders.id',$orderId)
                        ->leftJoin('countries','countries.id','orders.country_id')
                        ->first();
                    
        $orderItems = OrderItems::where('order_id',$orderId)->get();

        return view('admin.orders.detail',[
            'order' => $order,
            'orderItems' => $orderItems
        ]);

    }

    public function changeOrderStatus(Request $request, $orderId){
        $order = Order::find($orderId);
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();

        $message = 'Order Status changed successfully';

        session()->flash('success','Order status updated successfully');

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function sendInvoiceEmail(Request $request, $orderId){
        orderEmail($orderId, $request->userType);

        $message = 'Order email sent successfully';

        session()->flash('success','Order email sent successfully');

        return response()->json([
            'status' => true,
            'message' => $message
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

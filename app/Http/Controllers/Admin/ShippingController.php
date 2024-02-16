<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::get();        
        $data['countries'] = $countries;

        $shippingCharges = ShippingCharges::select('shipping_charges.*','countries.name')->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['shippingCharges'] = $shippingCharges;


        return view('admin.shipping.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        
        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' =>'required|numeric'
        ]);
        
        if($validator->passes()){
            $count = ShippingCharges::where('country_id',$request->country)->count();
            if($count > 0 ) {
                session()->flash('error', 'shipping already added');
                return response()->json([
                    'status' => true,                    
                ]);
            }

            $shipping = new ShippingCharges();
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success', 'shipping added successfully');

            return response()->json([
                'status' => true,
            ]);

        }else{
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
    public function edit(string $id)
    {
        $shippingCharge = ShippingCharges::find($id);

        $countries = Country::get();        
        $data['countries'] = $countries;
        $data['shippingCharge'] = $shippingCharge;

        return view('admin.shipping.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shipping = ShippingCharges::find($id);


        $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' =>'required|numeric'
        ]);

        if($validator->passes()){

            if($shipping == null){

                session()->flash('error', 'shipping not found');
    
                return response()->json([
                    'status' => true,
                ]);
            }


            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success', 'shipping updated successfully');

            return response()->json([
                'status' => true,
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shippingCharge = ShippingCharges::find($id);

        if($shippingCharge == null){

            session()->flash('error', 'shipping not found');

            return response()->json([
                'status' => true,
            ]);
        }

        $shippingCharge->delete();

        session()->flash('success', 'shipping deleted successfully');

        return response()->json([
            'status' => true,
        ]);
    }
}

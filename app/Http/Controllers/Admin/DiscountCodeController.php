<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $discountCoupons = Category::latest()->orderBy('id','DESC');
        $discountCoupons = DiscountCoupon::query();


        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $discountCoupons->orderBy('id', $order);
        } else {
            $discountCoupons->latest()->orderBy('id', 'DESC'); // Default sorting if not specified
        }

        if (!empty($request->get('keyword'))) {
            $discountCoupons = $discountCoupons->where('name', 'like', '%' . $request->get('keyword') . '%');
            $discountCoupons = $discountCoupons->orWhere('code', 'like', '%' . $request->get('keyword') . '%');
        }

        // $discountCoupons = $discountCoupons->paginate(10);
        // $perPage = $request->get('pagination_limit', 10); // Default: 25 items per page
        // $discountCoupons = $discountCoupons->paginate($perPage);
        $perPage = $request->input('per_page', session('perPage', 10));
        $discountCoupons = $discountCoupons->paginate($perPage);
        // Store selected perPage value in session
        session(['perPage' => $perPage]);


        return view('admin.coupon.list',compact('discountCoupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);



        if($validator->passes()){

            //starting date must be greator than current date
            if(!empty($request->starts_at)){
                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if($startAt->lte($now) == true){

                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date can not be less than current date/time.']
                    ]);
                }
            }

            //expiry date must be greator than start date
            if(!empty($request->starts_at) && !empty($request->expires_at)){
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if($expiresAt->gt($startAt) == false){

                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry must be greator than start date.']
                    ]);
                }
            }

            $discountCode = new DiscountCoupon();
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->save();

            $message = 'Discount coupon added successfully.';
            session()->flash('success',$message);

            return response()->json([
                'status' => true,
                'message' => $message 
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
    public function edit(string $id, Request $request)
    {
        $coupon = DiscountCoupon::find($id);

        if($coupon == null){
            session()->flash('error','Record not found');
            return redirect()->route('coupons.index');
        }

        $data['coupon'] = $coupon;

        return view('admin.coupon.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $discountCode = DiscountCoupon::find($id);

        if($discountCode == null){

            session()->flash('error','Record not found');
            return response()->json([
                'status' => true
            ]);
        }

        $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);



        if($validator->passes()){

            //starting date must be greator than current date
            // if(!empty($request->starts_at)){
            //     $now = Carbon::now();
            //     $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

            //     if($startAt->lte($now) == true){

            //         return response()->json([
            //             'status' => false,
            //             'errors' => ['starts_at' => 'Start date can not be less than current date/time.']
            //         ]);
            //     }
            // }

            //expiry date must be greator than start date
            if(!empty($request->starts_at) && !empty($request->expires_at)){
                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if($expiresAt->gt($startAt) == false){

                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry must be greator than start date.']
                    ]);
                }
            }

            
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expires_at = $request->expires_at;
            $discountCode->save();

            $message = 'Discount coupon updated successfully.';
            session()->flash('success',$message);

            return response()->json([
                'status' => true,
                'message' => $message 
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
    public function destroy(string $id, Request $request)
    {
        $discountCode = DiscountCoupon::find($id);

        if($discountCode == null){

            session()->flash('error','Record not found');
            return response()->json([
                'status' => true
            ]);
        }

        $discountCode->delete();

        session()->flash('success','Discount coupon delete successfully.');
        return response()->json([
            'status' => true
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Perform bulk deletion logic here, similar to your existing delete method
            foreach ($ids as $id) {
                $discountCoupon = DiscountCoupon::find($id);
                    $discountCoupon->delete();
            }
            $request->session()->flash('success', 'Discount coupon deleted successfully');

            return response()->json([
                'status' => true,
                'message' => 'Discount coupon deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Discount coupon selected for deletion'
            ]);
        }
    }



    public function bulkPublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected Discount coupon to 1 (Publish)
            DB::table('discount_coupons')->whereIn('id', $ids)->update(['status' => 1]);

            $request->session()->flash('success', 'Discount coupon published successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Discount coupon published successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Discount coupon selected for publishing'
            ]);
        }
    }

    public function bulkUnpublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected Discount coupon to 0 (Unpublish)
            DB::table('discount_coupons')->whereIn('id', $ids)->update(['status' => 0]);

            $request->session()->flash('success', 'Discount coupon unpublished successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Discount coupon unpublished successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Discount coupon selected for unpublishing'
            ]);
        }
    }
}

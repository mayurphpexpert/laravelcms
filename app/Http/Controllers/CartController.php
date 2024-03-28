<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CustomerAddresses;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Models\ShippingCharges;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $product = Product::with('product_images')->find($request->id);

        if($product == null){
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        if(Cart::count() > 0) {
            // echo "product already in cart";
            
            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if ($item->id == $product->id){
                    $productAlreadyExist = true;
                }
            }

            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status = true;
                $message = $product->title.' added in cart';
                session()->flash('success',$message);
            } else {
                $status = false;
                $message = $product->title.' already added in cart';
            }


        } else {
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title.' added in cart';
            session()->flash('success',$message);

        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function cart(){
        $cartContent =  Cart::content();
        $data['cartContent'] = $cartContent;

        return view('front.cart',$data);  
    }


    public function updateCart(Request $request){
        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);

        if($product->track_qty == 'Yes'){
            if ($qty <= $product->qty){
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
                session()->flash('success', $message);

            } else {
                $message = 'Requested qty('.$qty.') not available in stock.';
                $status = false;
                session()->flash('error', $message);
            }
        }else{
            Cart::update($rowId, $qty);
                $message = 'Cart updated successfully';
                $status = true;
                session()->flash('success', $message);

        }
        // Cart::update($rowId, $qty);
        // $message = 'Cart updated successfully';        
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteItem(Request $request){
        $itemInfo = Cart::get($request->rowId);

        if ($itemInfo == null){
            $errorMessage = 'Item not found in cart';
            session()->flash('error',$errorMessage);
            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ]);
        }

        Cart::remove($request->rowId);

        $message = 'Item removed from cart successfully';
        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }


    public function checkout(){

        $discount = 0;

        //if cart is empty redirect to cart page
        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }

        //if user is not logged in then redirect to login page
        if (Auth::check() == false){
            
            if (!session()->has('url.intended')){
                session(['url.intended' => url()->current()]);
            }

            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddresses::where('user_id',Auth::user()->id)->first();

        session()->forget('url.intended');

        $countries = Country::orderBy('name','ASC')->get();

        $subTotal = Cart::subtotal(2,'.','');
        //apply discount here
        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }else{
                $discount = $code->discount_amount;
            }
        }

        //calculate shipping here
        if($customerAddress != ''){
            $userCountry = $customerAddress->country_id;
            $shippingInfo = ShippingCharges::where('country_id',$userCountry)->first();

            $totalQty = 0;
            $totalShippingCharge = 0;
            $grandTotal = 0;
            foreach(Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            // $totalShippingCharge = $totalQty*$shippingInfo->amount;\
            if ($shippingInfo) {
                $totalShippingCharge = $totalQty * $shippingInfo->amount;
            } else {
                $defaultShippingInfo = ShippingCharges::find(2); // Assuming the "rest_of_world" entry has an ID of 2
                if ($defaultShippingInfo) {
                    $totalShippingCharge = $totalQty * $defaultShippingInfo->amount;
                } else {
                    // Handle the case when the default shipping charge is not found
                }
            }
            // if ($shippingInfo) {
            //     $totalShippingCharge = $totalQty * $shippingInfo->amount;
            // } else {
            
            // }

            $grandTotal = ($subTotal-$discount)+$totalShippingCharge;
            
        }   else {
            $grandTotal =  ($subTotal-$discount);
            $totalShippingCharge = 0;
        }     

        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'discount' => $discount,
            'grandTotal' => $grandTotal
        ]);
    }

    public function processCheckout(Request $request){



        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:3',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'please fix the errors',
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        //save user address
        // $customerAddress = CustomerAddresses::find();

        $user = Auth::user();

        CustomerAddresses::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]
        );


        //store data in orders table

        if ($request->payment_method == 'cod'){

            $discountCodeId = null;
            $promoCode = null;

            $shipping = 0;
            $discount = 0;
            $subTotal = Cart::subtotal(2,'.','');
            // $grandTotal = $subTotal+$shipping;

            //apply discount here
            if(session()->has('code')){
                $code = session()->get('code');
                if($code->type == 'percent'){
                    $discount = ($code->discount_amount/100)*$subTotal;
                }else{
                    $discount = $code->discount_amount;
                }

                $discountCodeId = $code->id;
                $promoCode = $code->code;
                
            }

            //calculate shipping
            $shippingInfo = ShippingCharges::where('country_id',$request->country_id)->first();

            $totalQty = 0;
            foreach(Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo != null) {
                // $shippingCharge = $totalQty*$shippingInfo->amount;
                if ($shippingInfo) {
                    $shipping = $totalQty*$shippingInfo->amount;
                } else {
                    
                }
                $grandTotal = ($subTotal-$discount)+$shipping;

            } else {
                $shippingInfo = ShippingCharges::where('country_id','rest_of_world')->first();
                $shipping = $totalQty*$shippingInfo->amount;                
                $grandTotal = ($subTotal-$discount)+$shipping;
            }

            

            
            $order = new Order();
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->coupon_code_id = $discountCodeId;
            $order->coupon_cod = $promoCode;
            $order->payment_status = 'not paid';
            $order->status = 'pending';            
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->state = $request->state;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->notes = $request->notes;
            $order->country_id = $request->country;
            $order->save();

            $orderID = $order->id;

            //store order items in order_items table
            foreach (Cart::content() as $item) {
                // $orderItem = new OrderItems();
                // $orderItem->product_id = $item->id;
                // $orderItem->order_id = $orderID;
                // $orderItem->name = $item->name;
                // $orderItem->qty = $item->qty;
                // $orderItem->price = $item->price;
                // $orderItem->total = $item->price*$item->qty;
                // $orderItem->save();
                $product = Product::find($item->id);

                if ($product) {
                    $orderItem = new OrderItems();
                    $orderItem->product_id = $product->id;
                    $orderItem->order_id = $orderID; // Use the retrieved order ID
                    $orderItem->name = $item->name;
                    $orderItem->qty = $item->qty;
                    $orderItem->price = $item->price;
                    $orderItem->total = $item->price * $item->qty;
                    $orderItem->save();
                }

                //update product stock
                $productData = Product::find($item->id);
                if($productData->track_qty == 'Yes'){
                    $currentQty = $productData->qty;
                    $updatedQty = $currentQty-$item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();
                }
            }

            //send order email

            orderEmail($order->id,'customer');

            session()->flash('success','you have successfully placed your order.');

            Cart::destroy();

            return response()->json([
                'message' => 'order saved successfully',
                'orderId' => $order->id,
                'status' => true,
            ]);
            
        }else{
            //
        }
    }

    public function thankyou($id){
        return view('front.thank',[
            'id' => $id
        ]);
    }

    public function getOrderSummery(Request $request){

        $subTotal = Cart::subtotal(2,'.','');
        $discount = 0;
        $discountString = '';

        //apply discount here
        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }else{
                $discount = $code->discount_amount;
            }
            $discountString = '<div class=" mt-4" id="discount-response"> 
                <strong>'.session()->get('code')->code.'</strong> 
                <a href="" class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>                    
            </div>';
        }


        if($request->country_id > 0) {
            $shippingInfo = ShippingCharges::where('country_id',$request->country_id)->first();
            $totalQty = 0;
            foreach(Cart::content() as $item) {
                $totalQty += $item->qty;
            }

            if($shippingInfo != null) {
                // $shippingCharge = $totalQty*$shippingInfo->amount;
                if ($shippingInfo) {
                    $shippingCharge = $totalQty*$shippingInfo->amount;
                } else {
                    
                }
                $grandTotal = ($subTotal-$discount)+$shippingCharge;

                return response()->json([
                   'status' => true,
                   'grandTotal' =>  number_format($grandTotal,2),
                   'discount' => $discount,
                   'discountString' => $discountString,
                   'shippingCharge' =>  number_format($shippingCharge,2),
                ]);

            } else {
                $shippingInfo = ShippingCharges::where('country_id','rest_of_world')->first();
                $shippingCharge = $totalQty*$shippingInfo->amount;                
                $grandTotal = ($subTotal-$discount)+$shippingCharge;

                return response()->json([
                   'status' => true,
                   'grandTotal' =>  number_format($grandTotal,2),
                   'discount' => $discount,
                   'discountString' => $discountString,
                   'shippingCharge' =>   number_format($shippingCharge,2),
                ]);
                
            }

        } else {
            return response()->json([
                'status' => true,
                'grandTotal' =>  number_format($subTotal-$discount,2),
                'discount' => $discount,
                'discountString' => $discountString,
                'shippingCharge' =>  0,
             ]);
        }
    }

    public function applyDiscount(Request $request){
        
        $code = DiscountCoupon::where('code',$request->code)->first();

        if($code == null){
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon code.',
             ]);
        }

        //check if coupon start date is valid or not

        $now = Carbon::now();
        
        if($code->start_at != ""){
            $startDate = Carbon::parse($code->start_at);
            // dd($startDate);

            if($now->lt($startDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon code.',
                 ]);
            }
        }

        if($code->expires_at != ""){
            $endDate = Carbon::parse($code->expires_at);

            if($now->gt($endDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon code.',
                 ]);
            }
        }

        //max uses check
        if($code->max_uses > 0){
            $couponUsed = Order::where('coupon_code_id',$code->id)->count();

            if($couponUsed >= $code->max_uses){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon',
                ]);
            }
        }
        //max uses user check
        // $couponUsed = Order::where('coupon_code_id',$code->id)->count();

        // if($couponUsed >= $code->max_uses){
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Invalid discount coupon',
        //     ]);
        // }

        $subTotal = Cart::subtotal(2,'.','');
        if($code->min_amount > 0){
            if($subTotal < $code->min_amount){
                return response()->json([
                    'status' => false,
                    'message' => 'your min amount is must be $'.$code->min_amount.'.',
                ]);
            }
        }
        
        session()->put('code',$code);

        return $this->getOrderSummery($request);
    }

    public function removeCoupon(Request $request){

        session()->forget('code');
        return $this->getOrderSummery($request);
    }
}

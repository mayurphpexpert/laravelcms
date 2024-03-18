<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CustomerAddresses;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(){

        return view('front.account.login');
    }

    public function register(){
        return view('front.account.register');
    }

    public function processRegister(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);

        if ($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success','You have been register successfully.');

            return response()->json([
                'status' => true,
            ]);

        }else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->passes()) {

            if(Auth::attempt(['email' => $request->email, 'password' => $request->password],$request->get('remember'))){

                // Get the intended URL or default to 'account.profile'
            $intendedUrl = session()->has('url.intended') ? session()->get('url.intended') : route('account.profile');

            // Clear the 'url.intended' session
            session()->forget('url.intended');

            // Redirect to the intended URL
            return redirect()->to($intendedUrl);

            }else{
                // session()->flash('error','either email/password is incorrect.');
                return redirect()->route('account.login')->withInput($request->only('email'))->with('error','either email/password is incorrect.');
            }

        } else {
            return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }

    public function profile(){

        $userId = Auth::user()->id;

        $countries =  Country::orderBy('name','ASC')->get();

        $user = User::where('id',Auth::user()->id)->first();

        $address = CustomerAddresses::where('user_id',$userId)->first();

        return view('front.account.profile',[
            'user' => $user,
            'countries' => $countries,
            'address' => $address,
        ]);

    }

    public function updateProfile(Request $request){
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userId.',id',
            'phone' => 'required'
        ]);

        if($validator->passes()){

            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            session()->flash('success','Profile Updated successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Profile Updated successfully.'
            ]);


        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function updateAddress(Request $request){
        $userId = Auth::user()->id;

        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country_id' => 'required',
            'address' => 'required|min:3',
            'apartment' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',
        ]);

        if($validator->passes()){

            // $user = User::find($userId);
            // $user->name = $request->name;
            // $user->email = $request->email;
            // $user->phone = $request->phone;
            // $user->save();

            CustomerAddresses::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country_id,
                    'address' => $request->address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip,
                ]
            );

            session()->flash('success','Address Updated successfully.');

            return response()->json([
                'status' => true,
                'message' => 'Address Updated successfully.'
            ]);


        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login')->with('success','you successfully logged out!');
    }

    public function orders(){

        $user = Auth::user();

        $orders = Order::where('user_id',$user->id)->orderBy('created_at','DESC')->get();
        
        $data['orders'] = $orders;
        
        return view('front.account.order',$data);
    }
    
    public function orderDetail($id){
        
        $data = [];
        $user = Auth::user();
        $order = Order::where('user_id',$user->id)->where('id',$id)->first();
        $data['order'] = $order;

        $orderItems = OrderItems::where('order_id',$id)->get();
        $data['orderItems'] = $orderItems;

        $orderItemsCount = OrderItems::where('order_id',$id)->count();
        $data['orderItemsCount'] = $orderItemsCount;
        
        return view('front.account.order-detail',$data);
        
    }
    
    public function wishlist(){
        
        $wishlists = Wishlist::where('user_id',Auth::user()->id)->with('product')->get();
        
        $data = [];
        $data['wishlists'] = $wishlists;

        return view('front.account.wishlist',$data);
    }

    public function removeProductFromWishList(Request $request){
        $wishlist = Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->first();

        if($wishlist == null){
            session()->flash('error','Product already removed');
            return response()->json([
                'status' => true,
            ]);
        } else {
            Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->delete();
            session()->flash('success','Product removed successfully');
            return response()->json([
                'status' => true,
            ]);
        }
    }
}

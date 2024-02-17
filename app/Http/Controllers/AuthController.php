<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
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
        return view('front.account.profile');

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
        
        return view('front.account.order-detail',$data);

    }
}

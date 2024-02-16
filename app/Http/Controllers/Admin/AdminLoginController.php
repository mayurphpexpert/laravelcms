<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $validateor = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validateor->passes()) {
            
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
                
                $admin = Auth::guard('admin')->user();
                // dd('helo');

                if ($admin->role == 2) {

                    if(isset($request['remember'])&&!empty($request['remember'])){
                        setcookie("email",$request['email'],time()+3600);
                        setcookie("password",$request['password'],time()+3600);
                    }else{
                        setcookie("email","");
                        setcookie("password","");

                    }
                    return redirect()->route('admin.dashboard');
                } else {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'Your are not authorized to access admin penal.');
                }

            } else {
                return redirect()->route('admin.login')->with('error', 'Email/Password is incorrect');
            }

        } else {
            return redirect()->route('admin.login')
                ->withErrors($validateor)
                ->withInput($request->only('email'));
        }
    }

    

   
}

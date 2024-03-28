<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ResetAdminPasswordEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


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

    public function forgotPassword(){
        return view('admin.forgot-password');
    }

    public function processForgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $value)->first();
        
                    if (!$user) {
                        $fail('The selected email is invalid.');
                    } elseif ($user->role !== 2) {
                        $fail('The selected email is invalid.');
                    }
                },
            ],
        ]);

        if($validator->fails()){
            return redirect()->route('admin.forgotPassword')->withInput()->withErrors($validator);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->where('email',$request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        //send email here
        $user = User::where('email',$request->email)->first();

        $formData = [
            'token' => $token,
            'user' => $user,
            'mailSubject' => 'Reset Admin Password.'
        ];

        Mail::to($request->email)->send(new ResetAdminPasswordEmail($formData));

        return redirect()->route('admin.forgotPassword')->with('success','Please check your inbox to reset your password.');

    }

    public function resetPassword($token){

        $tokenExist = DB::table('password_reset_tokens')->where('token',$token)->first();

        if($tokenExist == null){
            return redirect()->route('admin.forgotPassword')->with('error','Invalid request');
        }

        return view('admin.reset-password',[
            'token' => $token,
        ]);
    }

    public function processResetPassword(Request $request){
        $token = $request->token;
        
        $tokenObj = DB::table('password_reset_tokens')->where('token',$token)->first();
        
        if($tokenObj == null){
            return redirect()->route('admin.forgotPassword')->with('error','Invalid request');
        }
        
        $user = User::where('email',$tokenObj->email)->first();
        
        $validator = Validator::make($request->all(),[
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);
        // dd($validator);

        if($validator->fails()){
            return redirect()->route('admin.resetPassword',$token)->withErrors($validator);
        }

        User::where('id',$user->id)->update([
            'password' =>Hash::make($request->new_password)
        ]);

        DB::table('password_reset_tokens')->where('email',$user->email)->delete();

        return redirect()->route('admin.login')->with('success','You have successfully updated your password.');
    }

   
}

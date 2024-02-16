<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function showChangePasswordForm(){
        return view('admin.change_password');
        
    }
    
    public function processchangePassword(Request $request){
        // dd("hello");
        $validator = Validator::make($request->all(),[
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $id = Auth::guard('admin')->user()->id;
        $admin = User::where('id',$id)->first();

        if ($validator->passes()){

            if(!Hash::check($request->old_password, $admin->password)){
                session()->flash('error','Your Old Password is incorrect, please try again');
                return response()->json([
                    'status' =>true
                ]);
            }

            User::where('id',$id)->update([
                'password' => Hash::make($request->new_password)
            ]);

            session()->flash('success','You have successfully changed your password');
                return response()->json([
                    'status' =>true
                ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }


    public function showProfileForm(){
        return view('admin.profile.profile');
        
    }

    public function profileUpdate(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . Auth::guard('admin')->user()->id,
        ]);
    
        if ($validator->passes()) {
            $id = Auth::guard('admin')->user()->id;
            User::where('id', $id)->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
    
            session()->flash('success', 'Profile updated successfully');
            return response()->json([
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}

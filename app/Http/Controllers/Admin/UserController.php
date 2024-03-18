<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::where('role', 1);

        // Sorting by ID
        if ($request->has('sort') && $request->sort == 'id') {
            $order = $request->has('order') && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
            $users->orderBy('id', $order);
        } else {
            $users->latest()->orderBy('id', 'DESC'); // Default sorting if not specified
        }
        
        if (!empty($request->get('keyword'))) {
            $users = $users->where('name', 'like', '%' . $request->get('keyword') . '%');
            $users = $users->orWhere('email', 'like', '%' . $request->get('keyword') . '%');
        }

        $perPage = $request->input('per_page', session('perPage', 10));
        $users = $users->paginate($perPage);
        // Store selected perPage value in session
        session(['perPage' => $perPage]);
        // $orders = $orders->paginate(10);

        return view('admin.users.list',[
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'password' => 'required|min:6',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
        ]);

        if($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->status = $request->status;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success','User added successfully.');

            return response()->json([
                'status' => true,
                'message' => 'User added successfully.',
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
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
        $user = User::find($id);

        if($user == null){
            session()->flash('error','User not Found.');
            return redirect()->route('users.index');
        }

        return view('admin.users.edit',[
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if($user == null){
            session()->flash('error','User not Found.');
            return response()->json([
                'status' => true,
                'message' => 'User not found.',
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id.',id',
            'phone' => 'required',
        ]);

        if($validator->passes()){

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->status = $request->status;

            if($request->password != ''){
                $user->password = Hash::make($request->password);
            }
            $user->save();

            session()->flash('success','User update successfully.');

            return response()->json([
                'status' => true,
                'message' => 'User update successfully.',
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if($user == null){
            session()->flash('error','User not Found.');
            return response()->json([
                'status' => true,
                'message' => 'User not found.',
            ]);
        }

        $user->delete();

        session()->flash('success','User deleted successfully.');

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully.',
            ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Perform bulk deletion logic here, similar to your existing delete method
            foreach ($ids as $id) {
                $users = User::find($id);
                    $users->delete();
            }
            $request->session()->flash('success', 'users deleted successfully');

            return response()->json([
                'status' => true,
                'message' => 'users deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No users selected for deletion'
            ]);
        }
    }



    public function bulkPublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected users to 1 (Publish)
            DB::table('users')->whereIn('id', $ids)->update(['status' => 1]);

            $request->session()->flash('success', 'userspublished successfully.');

            return response()->json([
                'status' => true,
                'message' => 'users published successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No users selected for publishing'
            ]);
        }
    }

    public function bulkUnpublish(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            // Update the status of selected users to 0 (Unpublish)
            DB::table('users')->whereIn('id', $ids)->update(['status' => 0]);

            $request->session()->flash('success', 'users unpublished successfully.');

            return response()->json([
                'status' => true,
                'message' => 'users unpublished successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No users selected for unpublishing'
            ]);
        }
    }
}

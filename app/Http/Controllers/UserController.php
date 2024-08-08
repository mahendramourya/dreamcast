<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class UserController extends Controller
{
    public function index(){
        $roles = Role::all();
        $users = User::with('roles')->get();
        return view('user',['roles'=>$roles,'users'=>$users]);
    }
    public function userList(){
        $users = User::with('roles')->get();
        return response()->json([
            'status'=>true,
            'message' => "User List",
            'data' => $users
        ]);
    }

    public function create(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30',
            'email' => 'required|unique:users',
            'phone' => 'required',
            'description' =>'required',
            'file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            

        ]);
        

        if ($validator->fails())
        {
            return response()->json(['status'=>false,'message'=>$validator->errors()]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'description' => $request->description,
        ]);

        $user->roles()->sync($request->roleId);

        if($request->file('file')){
            $file = $request->file('file');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images', $filename, 'public');
            $user->image = $filename;
            $user->save();
        }

        return response()->json(['status'=>true,'message'=>'User is successfully added']);
    }

    public function test(){
        $users = User::with('roles')->get();
        foreach($users as $user){
            foreach($user->roles as $role){
                dump($role->name);
            }
        }
    }
}

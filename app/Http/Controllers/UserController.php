<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function register(Request $req){
        $validator = Validator::make($req->all(),[
            "name"=>"required",
            "email"=>"required|unique:users,email|email",
            "phone"=>"required|unique:users,email|min:10|max:10",
            "password"=>"required|min:6",
            "confirm_password"=>"required|same:password",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }
        if ($validator->passes()) {
            $user = new User();
            $user->name = $req->name;
            $user->email = $req->email;
            $user->password = Hash::make($req->password);
            $user->phone = $req->phone;

            if($user->save()){
                return response()->json([
                    "msg" => "registered successfully"
                ],200);
            }
            return response()->json([
                "msg" => "server error"
            ],200);
        }
    }

    function login(Request $req){
        $validator = Validator::make($req->all(),[
            "email"=>"required|email",
            "password"=>"required|min:6",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }
        if (!Auth::attempt($req->only('email','password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = User::where('email', $req['email'])->first();
        if($user){
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
    }

    function user_logout(){
        $user = Auth::user();
        $logout = $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        if($logout){
            return response()->json([
                'msg'=>"logged out successfully"
            ],200);
        }
        return response()->json([
            'msg'=>"server error"
        ],500);
    }

    function get_user_details(){
        $user = User::where("id",Auth::id())->select("name","email","phone")->first();
        if($user){
            return response()->json([
                'user_details'=>$user
            ],200);
        }
        return response()->json([
            'msg'=>"server error"
        ],500);
    }


    function update_user_details(Request $req){
        $validator = Validator::make($req->all(),[
            "name"=>"required",
            "email"=>"required|email|unique:users,email,".Auth::id(),
            "phone"=>"required|min:10|max:10|unique:users,phone,".Auth::id(),
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }
        $user = User::where("id",Auth::id())->first();
        if(!$user){
            return response()->json([
                "msg"=>"invalid attempt"
            ],400);
        }
        $user->name = $req->name;
        $user->email = $req->email;
        $user->phone = $req->phone;
        if($user->save()){
            return response()->json([
                "msg"=>"details updated",
                'user_details'=>$user
            ],200);
        }
        return response()->json([
            'msg'=>"server error"
        ],500);
    }


    function delete_account(){
        $user_deleted = User::where("id",Auth::id())->delete();
        if(!$user_deleted){
            return response()->json([
                "msg"=>"invalid attempt"
            ],400);
        }
        if($user_deleted){
            return response()->json([
                'msg'=>"account deleted successfuly"
            ],200);
        }
        return response()->json([
            'msg'=>"server error"
        ],500);
    }


}

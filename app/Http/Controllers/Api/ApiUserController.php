<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Http\Helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class ApiUserController extends Controller
{

    public function user(Request $request)
    {
       return Helper::success('Berhasil mendapatkan data pengguna',auth()->user());
    }

    public function edit_user(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ]);
        
        if ($validator->fails()) {
            return Helper::error($validator->errors()->first());
        }
        
        try {
            DB::beginTransaction();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            DB::commit();
            return Helper::success('Berhasil mendapatkan data pengguna',auth()->user());
        } catch (\Throwable $th) {
            DB::rollBack();
            return Helper::error('Failed to update user');
        }
    }
}

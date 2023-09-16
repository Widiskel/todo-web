<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class ApiAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ],
        );

        if ($validator->fails()) {
            return Helper::error($validator->errors()->first());
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            return Helper::success('Login berhasil', [
                'user' => $user,
                'token' => $user->createToken('ApiToken')->plainTextToken
            ]);
        }

        return Helper::error('Email atau password salah.', [], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ],
        );

        if ($validator->fails()) {
            return Helper::error($validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            
            DB::commit();
            return Helper::success('Registrasi berhasil',$user);
        } catch (\Throwable $th) {
            DB::rollback();
            return Helper::error('Registrasi gagal, terjadi kesalahan.');
        }
    }

    public function logout()
    {
        try {
            Auth::user()
                ->tokens()
                ->delete();
            return Helper::success('Berhasil keluar');
        } catch (\Throwable $th) {
            return Helper::error('Unauthenticated',[],401);
        }
    }

    public function refresh()
    {
        return Helper::success('Refresh Token berhasil', [
            'user' => Auth::user(),
            'token' => Auth::refresh(),
        ]);
    }
}

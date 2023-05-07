<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware("jwt")->except(["login"]);
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     * @return void
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function login(LoginRequest $request)
    {
        try {
            #   Credentials
            $credentials = array_merge($request->validated(), ["status" => 1]);

            #   Verify credentials
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    "head" => "error",
                    "body" => [
                        "message" => "Usuario y/o contraseña inválidos",
                ]], 400);
            }

            #   Find user
            $usuario = User::with("role.privileges:code")->find( Auth::id());

            #   Response
            return response()->json([
                "head" => "success",
                "body" => [
                    'user'         => $usuario,
                    'access_token' => $token,
                    'expires_in'   => auth()->factory()->getTTL()
                ]
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                "head" => "error",
                "body" => [
                    "message" => "Error del servidor",
                ]
            ], 400);
        }

    }
}

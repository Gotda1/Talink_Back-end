<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware("jwt");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        try {
            $users = User::with("role:code,name")
                        ->where("id", "<>", Auth::id())
                        ->get();
            return $this->successResponse([
                "users" => $users
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $roles =  Role::all();

            return $this->successResponse([
                "roles" => Role::all()
            ]);
        } catch (\Throwable $e) {           
            return $this->failedResponse($e);
        } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveUserRequest $request)
    {
        try {
            $user = User::create($request->validated());

            return $this->successResponse([
                "user" => $user
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::with("role")->find($id);
            
            return $this->successResponse([
                "user" => $user
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        } 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveUserRequest $request, $id)
    {
        try {
            if(!$request->password) unset($request->password);            
            $user = User::find($id);
            $user->fill($request->validated())->save();

            return $this->successResponse([
                "user" => $user
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id)->delete();
            
            return $this->successResponse([
                "user" => $user
            ]);
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }
}

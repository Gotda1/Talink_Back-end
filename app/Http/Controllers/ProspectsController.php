<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProspectRequest;
use App\Models\AcquireType;
use App\Models\Client;
use App\Models\Prospect;
use App\Common\Catalogs;
use Illuminate\Support\Facades\Auth;

class ProspectsController extends Controller
{
    public function __construct(){
        $this->middleware("jwt");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $prospects = Prospect::with(["acquirer_type:code,name", "user:id,name"])
                                 ->when(!$this->hasPrivilege("ALLPRSP"), function($query){
                                    $query->whereIn("user_id", [0, Auth::id()]);
                                 })->get();

            return $this->successResponse([
                "prospects" => $prospects
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
            $acquirers_type = AcquireType::select("code", "name")->get();
            $users          = Catalogs::getSellers();

            return $this->successResponse([
                "aquirers_type" => $acquirers_type,
                "users"         => $users
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
    public function store(SaveProspectRequest $request)
    {
        try {
            $prospect = Prospect::create($request->validated());
            
            return $this->successResponse([
                "prospect" => $prospect
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
            $prospect = Prospect::with(["acquirer_type:id,code,name", "user:id,code,name"])
                                ->find($id);
                        
            return $this->successResponse([
                "prospect" => $prospect
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
    public function update(SaveProspectRequest $request, $id)
    {
        try {
            $prospect = Prospect::find($id);
            $prospect->fill($request->validated());          
            $prospect->save();            
            $prospect->quotations()->update(["seller_id" => $prospect->user_id]);
                        
            return $this->successResponse([
                "prospect" => $prospect
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
            $prospect = Prospect::find($id)->delete();
            
            return $this->successResponse([
                "prospect" => $prospect
            ]);          
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }

    /**
     * Convert a prospect to client and update his quotations
     *
     * @return Client
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function convertToClient( $id ){
        try {
            $prospect = Prospect::find($id);
            $client   = Client::create($prospect->toArray());     
            $prospect->quotations()->update([
                "acquirer_id" => $client->id,
                "catalogue"   => Client::class
            ]);
            $prospect->delete();
        
            return $this->successResponse([
                "client" => $client
            ]);          
        } catch (\Throwable $e) {
            return $this->failedResponse($e);
        }
    }
}

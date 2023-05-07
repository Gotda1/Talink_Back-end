<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveClientRequest;
use App\Models\AcquireType;
use App\Models\Client;
use App\Common\Catalogs;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;

class ClientsController extends Controller
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
            $clients = Client::with(["acquirer_type:code,name", "user:id,name"])
                             ->when(!$this->hasPrivilege("ALLCLNT"), function($query){
                                 $query->whereIn("user_id", [0, Auth::id()]);
                             })->get();        

            return $this->successResponse([
                "clients" => $clients
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
            $aquirers_type = AcquireType::select("code", "name")->get();
            $users         = Catalogs::getSellers();      
                 
            return $this->successResponse([
                "aquirers_type" => $aquirers_type,
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
    public function store(SaveClientRequest $request)
    {
        try {
            $client = Client::create($request->validated());            

            return $this->successResponse([
                "client" => $client
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
            $client = Client::with("acquirer_type:id,code,name")
                              ->with("user:id,code,name")
                              ->find($id);
                                
            return $this->successResponse([
                "client" => $client
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
    public function update(SaveClientRequest $request, $id)
    {
        try {
            $client = Client::find($id);
            $client->fill($request->validated());
            $client->save();                        
            $client->quotations()->update(["seller_id" => $client->user_id]);
            $client->orders()->update(["seller_id" => $client->user_id]);

            return $this->successResponse([
                "client" => $client
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
            $client = Client::find($id)->delete();
            
            return $this->successResponse([
                "client" => $client
            ]);         
        } catch (\Throwable $e) {
            report($e);
            return $this->failedResponse($e);
        }
    }

    /**
     * Eports clients report
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function exportClients(){
        try {
            $report = Excel::download(new ClientsExport(), "clients.xlsx", \Maatwebsite\Excel\Excel::XLSX);

            return $report;
            
        } catch (\Throwable $e) {
            report($e);
            return $this->failedResponse($e);
        }
    }
}

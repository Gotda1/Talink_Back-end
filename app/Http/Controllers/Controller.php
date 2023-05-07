<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\HelperTrait;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HelperTrait;
    

    /**
     * Success 200 response
     */
    /**
     * Success 200 response
     *
     * @param array $body
     * @return response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function successResponse($body){
        return response()->json([
            "head" => "success",
            "body" => $body
        ],200);
    }

    /**
     * Failed 400 response
     *
     * @param Throwable $e
     * @return response
     * @author Guadalupe Ulloa <guadalupe.ulloa@outlook.com>
     */
    public function failedResponse($e = null, $msg = null, $statusCode = 500){
        if($e) report($e);
        
        return response()->json([
            "head" => "error",
            "body" => ["message" => $msg ?: "Error del servidor"]
        ], $statusCode);
    }
}

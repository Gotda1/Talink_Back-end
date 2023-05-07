<?php

namespace App\Http\Requests;

use App\Models\RelRolePrivilege;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\HelperTrait;

class ApiRequest extends FormRequest
{
    use HelperTrait;
    
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {   
        $errors = "";
        foreach ($validator->errors()->toArray() as $idx => $v) 
            $errors .= ( $v[0] . " " );

        $response = [
            "head" => "error",
            "body" => [
                "message" => $errors
            ]
        ];

        throw new HttpResponseException(response()->json($response,400));
    }
} 

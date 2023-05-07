<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveQuotationRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->method() == "POST" ? 
               $this->hasPrivilege("INSQUOT") :
               $this->hasPrivilege("UPDQUOT");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "catalogue"       => "required",
            "acquirer_id"     => "required",
            "validity"        => "required|max:100",
            "observations"    => "max:500",
            "taxes"           => "nullable",
            "warranty"        => "required|max:200",
            "advance_payment" => "required",
        ];
    }
}

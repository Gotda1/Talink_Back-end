<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrderPayment extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->hasPrivilege("INSPAYORD");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "reference_id"        => "required",
            "account_id"          => "required",
            "payment_method_code" => "required",
            "amount"              => "required",
            "observations"        => "max:100",
        ];
    }
}

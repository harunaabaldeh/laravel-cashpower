<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateIntraStarPayTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'star_account_number' => ['required','numeric','min:10'],
            'source_amount' => ['required','numeric','min:1'],
            'destination_amount' => ['required','numeric','min:1'],
            'rate_id' => ['required','numeric','min:1'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMailTemplateRequest extends FormRequest
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
        $rules = [
            'name' => 'required',
            'description_and_mail_class' => 'required',
            'subject' => 'required|max:256',
            'body' => 'required|max:8192',
            'sending_frequency' => 'required',
            'sending_date' => 'required_if:sending_frequency,Mensal|Única',
            'sending_hour' => 'required_if:sending_frequency,Mensal|Única',
        ];

        return $rules;
    }
}

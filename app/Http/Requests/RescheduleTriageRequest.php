<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleTriageRequest extends FormRequest
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
            'date' => 'required|date_format:d/m/Y',
            'hour' => 'required',
            'meetingMode' => 'required',
            'link' => 'required_if:mettingMode,Online',
            'local' => 'required_if:mettingMode,Presencial',
        ];

        return $rules;
    }
}

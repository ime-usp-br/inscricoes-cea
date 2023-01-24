<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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
            'serviceType' => 'required',
            'projectResponsible' => 'required',
            'contactPhone' => 'required',
            'CPFCNPJ' => 'required',
            'email' => 'required',
            'institution' => 'required',
            'institutionRelationship' => 'required',
            'projectPurpose' => 'required|array',
            'ppOther' => 'sometimes',
            'fundingAgency' => 'sometimes|array',
            'faOther' => 'sometimes',
            'knowledgeArea' => 'required|array',
            'kaOther' => 'sometimes',
            "paymentVoucher" => "required|max:10240",
            'refundReceipt' => 'required',
            'refundReceiptData' => 'required_if:refundReceipt,Sim',
            'bdName' => 'required',
            'bdCpfCnpj' => 'required',
            'bdBankName' => 'required',
            'bdAgency' => 'required',
            'bdAccount' => 'required',
            'bdType' => 'required',
            'authorization' => 'required',
            'projectTitle' => 'required',
            'generalAspects' => 'required',
            'generalObjectives' => 'required',
            'features' => 'required',
            'otherFeatures' => 'required',
            'limitations' => 'required',
            'storage' => 'required',
            'conclusions' => 'required',
            'expectedHelp' => 'required',
            'anexosNovos' => "sometimes|array",
            "anexosNovos.*.arquivo" => "required",
        ];

        if(isset(request()->projectPurpose))
        {
          if((in_array('Iniciação Científica',request()->projectPurpose)) or (in_array('Mestrado',request()->projectPurpose)) or (in_array('Doutorado',request()->projectPurpose)))
          {
            $additionalRules = [
            'mentor' => 'required',
            'declaration' => 'required',
            ];
            $rules = $rules+$additionalRules;
          }
        }


        return $rules;
    }
}

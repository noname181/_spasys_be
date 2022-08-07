<?php

namespace App\Http\Requests\Qna;

use App\Http\Requests\BaseFormRequest;

class QnaUpdateRequest extends BaseFormRequest
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
            'qna_title' => [
                'required',
                'string',
            ],
            'qna_content' => [
                'required',
                'string',
            ],
            'qna_status' => [
                'required',
                'string',
                'max:255',
            ],
            'qna_no' => [
                'required',
                'integer',
            ],
            'files' => [
                'array',
            ],
            'files.*' => [
                'file',
                'max:5000',
                'mimes:jpg,jpeg,png,pdf',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }
}

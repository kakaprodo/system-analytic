<?php

namespace Kakaprodo\SystemAnalytic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Kakaprodo\SystemAnalytic\Services\Log\Data\CreateLogData;

class LogAnalyticRequest extends FormRequest
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
        return CreateLogData::formValidationRules($this);
    }

    public function messages()
    {
        return [
            'duplicate_after.in' => 'duplicate after should be one of ' . implode(',', CreateLogData::$strictCheckPeriods)
        ];
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Kakaprodo\SystemAnalytic\Utilities\Util;

class SystemAnalyticRequest extends FormRequest
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
        Util::analyticTypeExists($this->analytic_type);

        return array_merge([
            'analytic_type' => ['bail', 'required', 'string'],
            'scope_type' => [
                'bail',
                Rule::requiredIf($this->isScopeTypeRequired($this->analytic_type)),
                'string'
            ],
            'scope_value' => [
                Rule::requiredIf($isFixedScopeType = Util::isFixedScopeType($this->scope_type)),
                Util::detectDateFormatWhen($isFixedScopeType, $this->scope_type)
            ],
            'scope_from_date' => [
                Rule::requiredIf(Util::isRangeScopeType($this->scope_type)),
                Util::detectDateFormatWhen(!$isFixedScopeType, $this->scope_type)
            ],
            'scope_to_date' => [
                Rule::requiredIf(Util::isRangeScopeType($this->scope_type)),
                Util::detectDateFormatWhen(!$isFixedScopeType, $this->scope_type)
            ],
            'search_value' => ['nullable'],
            'boolean_scope_type' => [
                'nullable',
                'string',
                Rule::in(Util::getBooleanScopeTypes($this->analytic_type))
            ],
            'should_export' => ['nullable', 'boolean'],
            'file_type' => [
                Rule::requiredIf((bool) $this->should_export),
                Rule::in(Util::exportSupportedFiles())
            ],
            'selected_option' => ['nullable', 'string'],
            'should_clear_cache' => ['nullable', 'boolean'],
            'refresh_persisted_result' => ['nullable', 'boolean'],
        ], Util::handlerRegisterClass()::formValidationRules($this));
    }

    public function messages()
    {
        return [
            'boolean_scope_type.in'  => 'The boolean scope type is supposed to be one of : ' . implode(
                ',',
                Util::getBooleanScopeTypes($this->analytic_type)
            ),
            'file_type.in' => 'The file type to be export should be one of: ' . implode(
                ',',
                Util::exportSupportedFiles()
            )
        ];
    }

    /**
     * You can customise this method based on your needs
     */
    protected function  isScopeTypeRequired($analyticType)
    {
        return Str::contains($analyticType, 'search') == false
            && Util::shouldRequireScope($analyticType);
    }
}

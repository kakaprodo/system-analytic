<?php

namespace Kakaprodo\SystemAnalytic\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Kakaprodo\SystemAnalytic\AnalyticGate;
use Illuminate\Foundation\Http\FormRequest;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;
use Kakaprodo\SystemAnalytic\Lib\ExportHub\Base\ExportHubBase;

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
        $analyticTypes =  AnalyticGate::allHandlers();

        $this->validateAnalyticType($analyticTypes);

        return [
            'analytic_type' => ['bail', 'required', 'string'],
            'scope_type' => [
                'bail',
                Rule::requiredIf($this->isScopeTypeRequired($this->analytic_type)),
                'string',
                Rule::in(AnalyticGate::detectScopeTypes($this->analytic_type))
            ],
            'scope_value' => [
                Rule::requiredIf(AnalyticGate::isFixedScopeType($this->scope_type)),
                $this->detectDateFormat()
            ],
            'scope_from_date' => [
                Rule::requiredIf(AnalyticGate::isRangeScopeType($this->scope_type)),
                $this->detectDateFormat()
            ],
            'scope_to_date' => [
                Rule::requiredIf(AnalyticGate::isRangeScopeType($this->scope_type)),
                $this->detectDateFormat()
            ],
            'search_value' => ['nullable'],
            'boolean_scope_type' => [
                'nullable',
                'string',
                Rule::in(AnalyticGate::detectBooleanScopeTypes($this->analytic_type))
            ],
            'should_export' => ['nullable', 'boolean'],
            'file_type' => [
                Rule::requiredIf((bool) $this->should_export),
                Rule::in(ExportHubBase::$supporteFiles)
            ],
            'selected_option' => ['nullable', 'string'],
            'should_clear_cache' => ['nullable', 'boolean']
        ];
    }

    private function validateAnalyticType(array $analyticTypes)
    {
        if (!app()->environment('local')) {
            return whenNot(
                in_array($this->analytic_type, $analyticTypes),
                "The analytic type is supposed to be one of: " .  implode(',', $analyticTypes)
            );
        }

        fireErr(
            "The analytic type is supposed to be one of: " .  implode(',', $analyticTypes)
        )->when(in_array($this->analytic_type, $analyticTypes) == false)
            ->withData([
                'analytic_type' => $analyticTypes
            ])->die();
    }

    public function messages()
    {
        return [
            'scope_type.in' => 'The scope type is supposed to be one of : ' . implode(
                ',',
                AnalyticGate::detectScopeTypes($this->analytic_type)
            ),
            'boolean_scope_type.in'  => 'The boolean scope type is supposed to be one of : ' . implode(
                ',',
                AnalyticGate::detectBooleanScopeTypes($this->analytic_type)
            ),
            'file_type.in' => 'The file type to be export should be one of: ' . implode(
                ',',
                ExportHubBase::$supporteFiles
            )
        ];
    }

    protected function detectDateFormat()
    {
        if (!AnalyticGate::isPeriodicTypes($this->scope_type)) return;

        $format =  [
            AnalyticFilterHub::TYPE_FIXED_MONTH => 'Y-m',
            AnalyticFilterHub::TYPE_FIXED_YEAR => 'Y',
            AnalyticFilterHub::TYPE_RANGE_DATE => 'Y-m-d',
            AnalyticFilterHub::TYPE_RANGE_MONTH => 'Y-m',
            AnalyticFilterHub::TYPE_RANGE_YEAR => 'Y',
        ][$this->scope_type] ?? 'Y-m-d';

        return 'date_format:' . $format;
    }

    protected function  isScopeTypeRequired($analyticTypes)
    {
        // when request is not search analytic
        return Str::contains($analyticTypes, 'search') == false;
    }
}

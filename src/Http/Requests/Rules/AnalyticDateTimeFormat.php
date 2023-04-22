<?php

namespace Kakaprodo\SystemAnalytic\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kakaprodo\SystemAnalytic\AnalyticGate;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;

class AnalyticDateTimeFormat implements Rule
{

    protected $scopeType;

    protected $detectedFormat;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($scopeType = null)
    {
        $this->scopeType = $scopeType;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!AnalyticGate::isPeriodicTypes($this->scopeType)) return true;

        $this->detectedFormat =  [
            AnalyticFilterHub::TYPE_FIXED_HOUR => 'Y-m-d H:i:s',
            AnalyticFilterHub::TYPE_FIXED_MONTH => 'Y-m',
            AnalyticFilterHub::TYPE_FIXED_YEAR => 'Y',
            AnalyticFilterHub::TYPE_RANGE_HOUR => 'Y-m-d H:i:s',
            AnalyticFilterHub::TYPE_RANGE_DATE => 'Y-m-d',
            AnalyticFilterHub::TYPE_RANGE_MONTH => 'Y-m',
            AnalyticFilterHub::TYPE_RANGE_YEAR => 'Y',
        ][$this->scopeType] ?? 'Y-m-d';

        $date = date_parse_from_format($this->detectedFormat, $value);
        return $date['error_count'] === 0 && $date['warning_count'] === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute does not match the format:" . $this->detectedFormat;
    }
}

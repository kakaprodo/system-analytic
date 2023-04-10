<?php

namespace Kakaprodo\SystemAnalytic\Data;

use Kakaprodo\SystemAnalytic\Data\DataType;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;

class AnalyticData extends DataType
{

    /**
     * The date column on which we can reference 
     * the scope
     */
    public $scopeColumn = null;

    protected function expectedProperties(): array
    {
        return [
            'analytic_type' => $this->dataType()->string(),
            'scope_type?' => $this->dataType()->string(),
            'scope_value?',
            'scope_from_date?',
            'scope_to_date?',
            'search_value?',
            'boolean_scope_type?', // like with_trashed
            'should_export?',
            'file_type?',
            'selected_option?',
            'should_clear_cache?' => $this->dataType()->bool()
        ];
    }

    protected function ignoreForKeyGenerator(): array
    {
        return ['should_clear_cache', 'file_type', 'should_export'];
    }

    /**
     * set a date column on which the filter scope will be applied to
     */
    public function setScopeColumn($column)
    {
        $this->scopeColumn = $column;

        return $this;
    }

    public function analyticType()
    {
        return $this->analytic_type;
    }

    /**
     * get the scope value for fixed scope type
     */
    public function scopeValue()
    {
        return e($this->scope_value);
    }

    /**
     * Used forr range scoope type
     */
    public function scopeFromDate()
    {
        return e($this->scope_from_date);
    }

    /**
     * Used forr range scoope type
     */
    public function scopeToDate()
    {
        return e($this->scope_to_date);
    }

    /**
     * check if the scope type target per year filtering
     */
    public function scopeIsYear()
    {
        return in_array($this->scope_type, [
            AnalyticFilterHub::TYPE_YEAR_AGO,
            AnalyticFilterHub::TYPE_THIS_YEAR,
            AnalyticFilterHub::TYPE_FIXED_YEAR,
            AnalyticFilterHub::TYPE_LAST_YEAR,
            AnalyticFilterHub::TYPE_RANGE_MONTH,
            AnalyticFilterHub::TYPE_RANGE_YEAR,
        ]);
    }

    /**
     * Format the scope date column for database query
     */
    public function scopeValueFormatForDb()
    {
        return $this->scopeIsYear() ? '%Y-%m' : '%Y-%m-%d';
    }

    /**
     * Define whether the cached analytics need to be
     * cleared
     */
    public function needsToClearCache(): bool
    {
        return (bool) $this->should_clear_cache;
    }
}

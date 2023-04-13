<?php

namespace Kakaprodo\SystemAnalytic\Data\Base;

use Kakaprodo\SystemAnalytic\Data\Base\DataType;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;

abstract class AnalyticDataBase extends DataType
{
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

<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\SystemAnalytic\Lib\Data\Base\DataType;
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
     * Format the scope date column for database query
     */
    public function scopeValueFormatForDb()
    {
        return [
            self::SCOPE_CATEGORY_HOUR => value(fn () => [
                AnalyticFilterHub::TYPE_FIXED_HOUR => '%Y-%m-%d %H:%i',
                AnalyticFilterHub::TYPE_RANGE_HOUR => '%Y-%m-%d %H:00'
            ][$this->scope_type] ?? null),
            self::SCOPE_CATEGORY_MONTH => '%Y-%m-%d',
            self::SCOPE_CATEGORY_YEAR => '%Y-%m'
        ][$this->scopeCategory()];
    }

    /**
     * Format the scope date column for carbon date
     */
    public function scopeValueFormatForCarbon()
    {
        return [
            self::SCOPE_CATEGORY_HOUR =>  value(fn () => [
                AnalyticFilterHub::TYPE_FIXED_HOUR => 'd M h:i A',
                AnalyticFilterHub::TYPE_RANGE_HOUR => 'd M h A'
            ][$this->scope_type] ?? null),
            self::SCOPE_CATEGORY_MONTH => 'd M Y',
            self::SCOPE_CATEGORY_YEAR => 'M Y'
        ][$this->scopeCategory()];
    }

    /**
     * detect the scope category
     */
    public function scopeCategory()
    {
        if ($this->scopeIsHour()) return self::SCOPE_CATEGORY_HOUR;

        return $this->scopeIsYear() ? self::SCOPE_CATEGORY_YEAR : self::SCOPE_CATEGORY_MONTH;
    }

    /**
     * check if the analytic type is of hour
     * category
     */
    public function scopeIsHour()
    {
        return in_array($this->scope_type, [
            AnalyticFilterHub::TYPE_FIXED_HOUR,
            AnalyticFilterHub::TYPE_RANGE_HOUR,
        ]);
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
     * Define whether the cached analytics need to be
     * cleared
     */
    public function needsToClearCache(): bool
    {
        return (bool) $this->should_clear_cache;
    }
}

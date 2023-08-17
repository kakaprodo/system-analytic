<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\Base\DataType;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticScopeValueHub;

abstract class AnalyticDataBase extends DataType
{
    const SCOPE_CATEGORY_HOUR = 'hour';
    const SCOPE_CATEGORY_DAY = 'day';
    const SCOPE_CATEGORY_MONTH = 'month';
    const SCOPE_CATEGORY_YEAR = 'year';

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
            self::SCOPE_CATEGORY_DAY => '%Y-%m-%d',
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
            self::SCOPE_CATEGORY_DAY => 'd M Y',
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

        if ($this->scopeIsDay()) return self::SCOPE_CATEGORY_DAY;

        if ($this->scopeIsMonth()) return self::SCOPE_CATEGORY_MONTH;

        if ($this->scopeIsYear()) return self::SCOPE_CATEGORY_YEAR;

        return self::SCOPE_CATEGORY_DAY;
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
     * check if scope is of category day
     */
    public function scopeIsDay()
    {
        return in_array($this->scope_type, [
            AnalyticFilterHub::TYPE_TODAY,
            AnalyticFilterHub::TYPE_FIXED_DATE,
            AnalyticFilterHub::TYPE_RANGE_DATE,
            AnalyticFilterHub::TYPE_THIS_WEEK,
            AnalyticFilterHub::TYPE_LAST_WEEK,
            AnalyticFilterHub::TYPE_WEEK_AGO
        ]);
    }

    /**
     * check if scope is of category month
     */
    public function scopeIsMonth()
    {
        return in_array($this->scope_type, [
            AnalyticFilterHub::TYPE_THIS_MONTH,
            AnalyticFilterHub::TYPE_MONTH_AGO,
            AnalyticFilterHub::TYPE_LAST_MONTH,
            AnalyticFilterHub::TYPE_FIXED_MONTH,
            AnalyticFilterHub::TYPE_RANGE_MONTH,

            AnalyticFilterHub::FIRST_QUARTER,
            AnalyticFilterHub::SECOND_QUARTER,
            AnalyticFilterHub::THIRD_QUARTER,
            AnalyticFilterHub::FOURTH_QUARTER
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

    /**
     * when the persisted data need to be refresh
     */
    public function needToRefreshPersistedData()
    {
        $shouldRefresh =  (bool) $this->refresh_persisted_result;

        if ($shouldRefresh) $this->should_clear_cache = true;

        return $shouldRefresh;
    }

    /**
     * Get the value of the scope based on the scope_type
     * 
     * this will return the first_date in case the 
     * scope_type will involve a range date filtering
     * 
     * eg: if the scope_type is this_week, then the return value will be the
     * date of monday
     */
    public function getFilterScopeValue()
    {
        if ($this->filterScopeValue) return $this->filterScopeValue;

        $filter = $this->filterValueHub();
        $startDate = $filter->getStartDate();

        $this->filterScopeValue = count(explode('-', $startDate)) == 1
            ?  ($startDate . '-01') // when only a given year is given, then add january to the year
            : $startDate;

        return  Util::formatDate(
            $this->filterScopeValue,
            $this->scopeIsHour() ? 'Y-m-d H:i:s' : 'Y-m-d'
        );
    }

    /**
     * the gate to the class that detect the original values of provided scopes
     */
    public function filterValueHub(): AnalyticScopeValueHub
    {
        if ($this->filterValueHub) return $this->filterValueHub;

        $this->filterValueHub = AnalyticScopeValueHub::apply($this);

        return $this->filterValueHub;
    }
}

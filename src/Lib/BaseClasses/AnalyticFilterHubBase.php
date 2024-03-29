<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;

abstract class AnalyticFilterHubBase
{
    /**
     * @var AnalyticData
     */
    protected $data;

    /**
     * Keeps the original scope column value that
     * comes in 
     */
    protected $initialScopeColumn;

    /**
     * Package Default scopes handlers 
     */
    protected $defaultScopeHandlers = [];

    /**
     * Plugin(or custom) scope handlers
     */
    protected $customScopeHandlers = [];

    const TYPE_YEAR_AGO = 'year_ago';
    const TYPE_TODAY = 'today';
    const TYPE_MONTH_AGO = 'month_ago';
    const TYPE_WEEK_AGO = 'week_ago';

    const TYPE_THIS_WEEK = "this_week";
    const TYPE_THIS_MONTH = "this_month";
    const TYPE_THIS_YEAR = "this_year";

    const TYPE_LAST_WEEK = "last_week";
    const TYPE_LAST_MONTH = "last_month";
    const TYPE_LAST_YEAR = "last_year";

    const TYPE_FIXED_HOUR = "fixed_hour";
    const TYPE_FIXED_DATE = "fixed_date";
    const TYPE_FIXED_MONTH = "fixed_month";
    const TYPE_FIXED_YEAR = "fixed_year";

    const TYPE_RANGE_HOUR = "range_hour";
    const TYPE_RANGE_DATE = "range_date";
    const TYPE_RANGE_MONTH = "range_month";
    const TYPE_RANGE_YEAR = "range_year";

    const FIRST_QUARTER = "first_quarter";
    const  SECOND_QUARTER = "second_quarter";
    const  THIRD_QUARTER = "third_quarter";
    const  FOURTH_QUARTER = "fourth_quarter";

    const TYPE_ALL = "all";

    public function __construct(AnalyticData &$data)
    {
        $this->data = &$data;
    }

    /**
     * call the appropraite callback(or handler) of a given
     * scope
     */
    public function applyFilter($query)
    {
        $filterHandler = $this->getDefaultScopes($query)[$this->data->scope_type] ?? null;

        if (!$filterHandler) {
            $filterHandler = $this->getPluginScopes($query)[$this->data->scope_type] ?? null;
            $this->data->scopeHandlerIsFromPlugin =  $filterHandler != null;
        }

        return Util::callFunction(
            $filterHandler,
            "The scope type is supposed to be one of: " . implode(',', array_keys(
                array_merge($this->defaultScopeHandlers, $this->customScopeHandlers)
            ))
        );
    }

    /**
     * All the default scope handlers
     */
    private function getDefaultScopes($query)
    {
        return $this->defaultScopeHandlers =  [
            self::TYPE_TODAY => fn () => $this->filterByToday($query),
            self::TYPE_WEEK_AGO => fn () => $this->filterByWeekAgo($query),
            self::TYPE_MONTH_AGO => fn () => $this->filterByMonthAgo($query),
            self::TYPE_YEAR_AGO =>  fn () => $this->filterByYearAgo($query),

            self::TYPE_THIS_WEEK => fn () =>  $this->filterByThisWeek($query),
            self::TYPE_THIS_MONTH => fn () => $this->filterByThisMonth($query),
            self::TYPE_THIS_YEAR => fn () => $this->filterByThisYear($query),

            self::TYPE_LAST_WEEK => fn () => $this->filterByLastWeek($query),
            self::TYPE_LAST_MONTH => fn () => $this->filterByLastMonth($query),
            self::TYPE_LAST_YEAR => fn () => $this->filterByLastYear($query),

            self::TYPE_FIXED_HOUR => fn () => $this->filterByFixedHour($query),
            self::TYPE_FIXED_DATE => fn () => $this->filterByFixedDate($query),
            self::TYPE_FIXED_MONTH => fn () => $this->filterByFixedMonth($query),
            self::TYPE_FIXED_YEAR => fn () => $this->filterByFixedYear($query),

            self::TYPE_RANGE_HOUR => fn () => $this->filterByRangeHour($query),
            self::TYPE_RANGE_DATE => fn () => $this->filterByRangeDate($query),
            self::TYPE_RANGE_MONTH => fn () => $this->filterByRangeMonth($query),
            self::TYPE_RANGE_YEAR => fn () => $this->filterByRangeYear($query),

            self::FIRST_QUARTER => fn () => $this->filterByFirstQuarter($query),
            self::SECOND_QUARTER => fn () => $this->filterBySecondQuarter($query),
            self::THIRD_QUARTER => fn () => $this->filterByThirdQuarter($query),
            self::FOURTH_QUARTER => fn () => $this->filterByFourthQuarter($query),

            self::TYPE_ALL => fn () => $query
        ];
    }

    /**
     * Load all custom scopes registered in the plugin
     */
    private function getPluginScopes($query)
    {
        return $this->customScopeHandlers = $this->data->pluginHub->scopes?->load(
            $query
        ) ?? [];
    }

    /**
     * Convert the provided scope column to array,
     * then check whether or not the handler supports
     * multiple scope columns
     * 
     * @return string|array
     */
    public function getScopeColumns()
    {
        $scopeColumns = $this->data->scopeColumn;

        $this->initialScopeColumn = $scopeColumns;

        $scopeColumns = is_array($scopeColumns) ? $scopeColumns : explode('|', $scopeColumns);

        return count($scopeColumns) == 1 ? $this->initialScopeColumn : $scopeColumns;
    }
}

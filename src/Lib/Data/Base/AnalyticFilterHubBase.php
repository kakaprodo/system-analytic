<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;

abstract class AnalyticFilterHubBase
{
    /**
     * @var AnalyticData
     */
    protected $data;

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

    public function __construct(AnalyticData $data)
    {
        $this->data = $data;
    }

    public function applyFilter($query)
    {
        $filterHandlers =  [
            self::TYPE_TODAY => function () use ($query) {
                return $this->filterByToday($query);
            },
            self::TYPE_WEEK_AGO => function () use ($query) {
                return $this->filterByWeekAgo($query);
            },
            self::TYPE_MONTH_AGO => function () use ($query) {
                return $this->filterByMonthAgo($query);
            },
            self::TYPE_YEAR_AGO =>  function () use ($query) {
                return $this->filterByYearAgo($query);
            },

            self::TYPE_THIS_WEEK => function () use ($query) {
                return $this->filterByThisWeek($query);
            },
            self::TYPE_THIS_MONTH => function () use ($query) {
                return $this->filterByThisMonth($query);
            },
            self::TYPE_THIS_YEAR => function () use ($query) {
                return $this->filterByThisYear($query);
            },

            self::TYPE_LAST_WEEK => function () use ($query) {
                return $this->filterByLastWeek($query);
            },
            self::TYPE_LAST_MONTH => function () use ($query) {
                return $this->filterByLastMonth($query);
            },
            self::TYPE_LAST_YEAR => function () use ($query) {
                return $this->filterByLastYear($query);
            },

            self::TYPE_FIXED_HOUR => function () use ($query) {
                return $this->filterByFixedHour($query);
            },
            self::TYPE_FIXED_DATE => function () use ($query) {
                return $this->filterByFixedDate($query);
            },
            self::TYPE_FIXED_MONTH => function () use ($query) {
                return $this->filterByFixedMonth($query);
            },
            self::TYPE_FIXED_YEAR => function () use ($query) {
                return $this->filterByFixedYear($query);
            },

            self::TYPE_RANGE_HOUR => function () use ($query) {
                return $this->filterByRangeHour($query);
            },
            self::TYPE_RANGE_DATE => function () use ($query) {
                return $this->filterByRangeDate($query);
            },
            self::TYPE_RANGE_MONTH => function () use ($query) {
                return $this->filterByRangeMonth($query);
            },
            self::TYPE_RANGE_YEAR => function () use ($query) {
                return $this->filterByRangeYear($query);
            },

            self::FIRST_QUARTER => function () use ($query) {
                return $this->filterByFirstQuarter($query);
            },
            self::SECOND_QUARTER => function () use ($query) {
                return $this->filterBySecondQuarter($query);
            },
            self::THIRD_QUARTER => function () use ($query) {
                return $this->filterByThirdQuarter($query);
            },
            self::FOURTH_QUARTER => function () use ($query) {
                return $this->filterByFourthQuarter($query);
            },

            self::TYPE_ALL => function () use ($query) {
                return $query;
            }
        ][$this->data->scope_type] ?? null;

        return Util::callFunction(
            $filterHandlers,
            'Un-supported filter type: ' . $this->data->scope_type
        );
    }
}

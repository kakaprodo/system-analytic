<?php

namespace Kakaprodo\SystemAnalytic\Lib\FilterHub;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;

class AnalyticFilterHub
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

    const TYPE_ALL = "all";

    /**
     * define wether a request to this class needs to know
     * only the first date of the scope value
     */
    protected $shouldReturnScopeValue = false;

    /**
     * the start date of the scope
     */
    protected $startFromDate = null;

    public function __construct(AnalyticData $data)
    {
        $this->data = $data;
    }

    /**
     * create the instance and filter the analytic query
     * 
     * Note: the query is null only when when need to find
     *  the value of the scope
     */
    public static function apply(
        AnalyticData $data,
        $query = null,
        $onlyScopeValue = false
    ) {
        if (!$data->scope_type) return $query;

        return (new self($data))
            ->getOnlyScopeValue($onlyScopeValue)
            ->applyFilter($query);
    }

    public function getOnlyScopeValue($onlyScopeValue)
    {
        $this->shouldReturnScopeValue = $onlyScopeValue;

        return $this;
    }

    public function applyFilter($query)
    {
        $filterHandlers =  [
            self::TYPE_TODAY => fn () => $this->filterByToday($query),
            self::TYPE_WEEK_AGO => fn () => $this->filterByWeekAgo($query),
            self::TYPE_MONTH_AGO => fn () => $this->filterByMonthAgo($query),
            self::TYPE_YEAR_AGO =>  fn () => $this->filterByYearAgo($query),

            self::TYPE_THIS_WEEK => fn () => $this->filterByThisWeek($query),
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

            self::TYPE_ALL => fn () => $query,
        ][$this->data->scope_type] ?? null;

        return Util::callFunction(
            $filterHandlers,
            'Un-supported filter type: ' . $this->data->scope_type
        );
    }

    protected function filterByToday($query)
    {
        return $query->whereDate($this->data->scopeColumn, today());
    }

    protected function filterByWeekAgo($query)
    {
        return $query->whereDate($this->data->scopeColumn, '>=', today()->subWeek());
    }

    protected function filterByMonthAgo($query)
    {
        return $query->whereDate($this->data->scopeColumn, '>=', today()->subDays(31));
    }

    protected function filterByYearAgo($query)
    {
        return $query->whereDate($this->data->scopeColumn, '>=', today()->subYear());
    }

    protected function filterByThisWeek($query)
    {
        $this->startFromDate =  today()->startOfWeek();

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->whereDate($this->data->scopeColumn, '>=', $this->startFromDate);
    }

    protected function filterByThisMonth($query)
    {
        $this->startFromDate =  today()->startOfMonth();

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->whereDate($this->data->scopeColumn, '>=', $this->startFromDate);
    }

    protected function filterByThisYear($query)
    {
        $this->startFromDate =  today()->startOfYear();

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->whereDate($this->data->scopeColumn, '>=', $this->startFromDate);
    }

    protected function filterByLastWeek($query)
    {
        $previousWeek = today()->subWeek();

        $this->startFromDate = $previousWeek->startOfWeek();
        $this->data->scope_from_date =  Util::formatDate($this->startFromDate, 'Y-m-d');
        $this->data->scope_to_date =  Util::formatDate($previousWeek->endOfWeek(), 'Y-m-d');

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $this->filterByRangeDate($query);
    }

    protected function filterByLastMonth($query)
    {
        $this->startFromDate =  today()->subDays(31)->startOfMonth();
        $this->data->scope_value =   $this->startFromDate;

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $this->filterByFixedMonth($query);
    }

    protected function filterByLastYear($query)
    {
        $this->startFromDate = today()->subYear()->startOfYear();
        $this->data->scope_value = Util::formatDate($this->startFromDate, 'Y');

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $this->filterByFixedYear($query);
    }

    protected function filterByFixedHour($query)
    {
        $this->startFromDate = Util::formatDate($this->data->scopeValue(), 'Y-m-d H:i:s');

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        $this->data->scope_from_date = $this->startFromDate;
        $this->data->scope_to_date =  Util::parseDate($this->data->scope_from_date)->endOfHour();

        return $this->filterByRangeHour($query);
    }

    protected function filterByFixedDate($query)
    {
        $this->startFromDate = $this->data->scopeValue();
        $date = Util::formatDate($this->startFromDate, 'Y-m-d');

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->whereDate($this->data->scopeColumn, $date);
    }

    protected function filterByFixedMonth($query)
    {
        $this->startFromDate = $this->data->scopeValue();

        $monthYear = Util::formatDate($this->startFromDate, 'm-Y');
        [$month, $year] = explode('-', $monthYear);

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->whereMonth($this->data->scopeColumn, $month)
            ->whereYear($this->data->scopeColumn, $year);
    }

    protected function filterByFixedYear($query)
    {
        $this->startFromDate = $this->data->scopeValue();

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->whereYear(
            $this->data->scopeColumn,
            $this->startFromDate
        );
    }

    protected function filterByRangeHour($query)
    {
        $this->startFromDate = Util::formatDate($this->data->scopeFromDate(), 'Y-m-d H:i:s');

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->where(function ($q) {
            $endHour = Util::formatDate($this->data->scopeToDate(), 'Y-m-d H:i:s');

            $q->where($this->data->scopeColumn, '>=', $this->startFromDate)
                ->where($this->data->scopeColumn, '<=', $endHour);
        });
    }

    protected function filterByRangeDate($query)
    {
        $this->startFromDate = $this->data->scopeFromDate();

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->where(function ($q) {
            $q->whereDate($this->data->scopeColumn, '>=', $this->startFromDate)
                ->whereDate($this->data->scopeColumn, '<=', $this->data->scopeToDate());
        });
    }

    protected function filterByRangeMonth($query)
    {
        $this->startFromDate = $this->data->scopeFromDate();

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        $fromMonthYear = Util::parseDate(Util::formatDate($this->startFromDate, 'Y-m-d'));
        $toMonthYear = Util::parseDate(Util::formatDate($this->data->scopeToDate(), 'Y-m-d'));

        $this->data->scope_from_date =  $fromMonthYear->startOfMonth();
        $this->data->scope_to_date =  $toMonthYear->endOfMonth();

        return $this->filterByRangeDate($query);
    }

    protected function filterByRangeYear($query)
    {
        $this->startFromDate = $this->data->scopeFromDate();

        if ($this->shouldReturnScopeValue) return  $this->startFromDate;

        return $query->where(function ($q) {
            $q->whereYear($this->data->scopeColumn, '>=', $this->startFromDate)
                ->whereYear($this->data->scopeColumn, '<=', $this->data->scopeToDate());
        });
    }
}

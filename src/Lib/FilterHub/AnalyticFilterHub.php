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

    const TYPE_FIXED_DATE = "fixed_date";
    const TYPE_FIXED_MONTH = "fixed_month";
    const TYPE_FIXED_YEAR = "fixed_year";

    const TYPE_RANGE_DATE = "range_date";
    const TYPE_RANGE_MONTH = "range_month";
    const TYPE_RANGE_YEAR = "range_year";

    const TYPE_ALL = "all";

    public function __construct(AnalyticData $data)
    {
        $this->data = $data;
    }

    public static function apply(AnalyticData $data, $query)
    {
        if (!$data->scope_type) return $query;

        return (new self($data))->applyFilter($query);
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

            self::TYPE_FIXED_DATE => fn () => $this->filterByFixedDate($query),
            self::TYPE_FIXED_MONTH => fn () => $this->filterByFixedMonth($query),
            self::TYPE_FIXED_YEAR => fn () => $this->filterByFixedYear($query),

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
        return $query->whereDate($this->data->scopeColumn, '>=', today()->startOfWeek());
    }

    protected function filterByThisMonth($query)
    {
        return $query->whereDate($this->data->scopeColumn, '>=', today()->startOfMonth());
    }

    protected function filterByThisYear($query)
    {
        return $query->whereDate($this->data->scopeColumn, '>=', today()->startOfYear());
    }

    protected function filterByLastWeek($query)
    {
        $previousWeek = today()->subWeek();

        $this->data->scope_from_date =  Util::formatDate($previousWeek->startOfWeek(), 'Y-m-d');
        $this->data->scope_to_date =  Util::formatDate($previousWeek->endOfWeek(), 'Y-m-d');

        return $this->filterByRangeDate($query);
    }

    protected function filterByLastMonth($query)
    {
        $this->data->scope_value = today()->subDays(31);

        return $this->filterByFixedMonth($query);
    }

    protected function filterByLastYear($query)
    {
        $this->data->scope_value = Util::formatDate(today()->subYear(), 'Y');

        return $this->filterByFixedYear($query);
    }

    protected function filterByFixedDate($query)
    {
        $date = Util::formatDate($this->data->scopeValue(), 'Y-m-d');

        return $query->whereDate($this->data->scopeColumn, $date);
    }

    protected function filterByFixedMonth($query)
    {
        $monthYear = Util::formatDate($this->data->scopeValue(), 'm-Y');
        [$month, $year] = explode('-', $monthYear);

        return $query->whereMonth($this->data->scopeColumn, $month)
            ->whereYear($this->data->scopeColumn, $year);
    }

    protected function filterByFixedYear($query)
    {
        return $query->whereYear(
            $this->data->scopeColumn,
            $this->data->scopeValue()
        );
    }

    protected function filterByRangeDate($query)
    {
        return $query->where(function ($q) {
            $q->whereDate($this->data->scopeColumn, '>=', $this->data->scopeFromDate())
                ->whereDate($this->data->scopeColumn, '<=', $this->data->scopeToDate());
        });
    }

    protected function filterByRangeMonth($query)
    {
        $fromMonthYear = Util::formatDate($this->data->scopeFromDate(), 'm-Y');
        $toMonthYear = Util::formatDate($this->data->scopeToDate(), 'm-Y');

        return $query->where(function ($q)  use ($fromMonthYear) {

            [$month, $year] = explode('-', $fromMonthYear);

            $q->whereMonth($this->data->scopeColumn, '>=', $month)
                ->whereYear($this->data->scopeColumn, '>=', $year);
        })->where(function ($q) use ($toMonthYear) {

            [$month, $year] = explode('-', $toMonthYear);

            $q->whereMonth($this->data->scopeColumn, '<=', $month)
                ->whereYear($this->data->scopeColumn, '<=', $year);
        });
    }

    protected function filterByRangeYear($query)
    {
        return $query->where(function ($q) {
            $q->whereYear($this->data->scopeColumn, '>=', $this->data->scopeFromDate())
                ->whereYear($this->data->scopeColumn, '<=', $this->data->scopeToDate());
        });
    }
}

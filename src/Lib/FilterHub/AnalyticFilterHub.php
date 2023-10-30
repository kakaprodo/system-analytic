<?php

namespace Kakaprodo\SystemAnalytic\Lib\FilterHub;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\Data\Base\AnalyticFilterHubBase;

/**
 * This class filters the query
 */
class AnalyticFilterHub extends AnalyticFilterHubBase
{

    /**
     * create the instance and filter the analytic query
     * 
     * Note: the query is null only when when need to find
     *  the value of the scope
     */
    public static function apply(
        AnalyticData $data,
        $query = null,
    ) {
        if (!$data->scope_type) return $query;

        return (new self($data))
            ->startFilteringProcess($query);
    }

    /**
     * Apply multiple scope columns filtering if
     * the handler supports that
     */
    public function startFilteringProcess($query)
    {
        $columns = $this->getScopeColumns();

        if (is_string($columns)) return $this->applyFilter($query);

        return $query->where(function ($q) use ($columns) {
            $firstScopeColumn = array_shift($columns);

            $this->data->setScopeColumn($firstScopeColumn);
            $query = $this->applyFilter($q);

            $remainingColumns = $columns;

            foreach ($remainingColumns as $column) {
                $this->data->setScopeColumn($column);
                $query->orWhere(fn ($q) => $this->applyFilter($q));
            }

            $this->data->setScopeColumn($this->initialScopeColumn);
        });
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
        $this->data->scope_value =   today()->subDays(31)->startOfMonth();

        return $this->filterByFixedMonth($query);
    }

    protected function filterByLastYear($query)
    {
        $this->data->scope_value = Util::formatDate(
            today()->subYear()->startOfYear(),
            'Y'
        );

        return $this->filterByFixedYear($query);
    }

    protected function filterByFixedHour($query)
    {
        $this->data->scope_from_date = Util::formatDate($this->data->scopeValue(), 'Y-m-d H:i:s');
        $this->data->scope_to_date =  Util::parseDate($this->data->scope_from_date)->endOfHour();

        return $this->filterByRangeHour($query);
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

    protected function filterByRangeHour($query)
    {
        return $query->where(function ($q) {
            $startHour =  Util::formatDate($this->data->scopeFromDate(), 'Y-m-d H:i:s');
            $endHour = Util::formatDate($this->data->scopeToDate(), 'Y-m-d H:i:s');

            $q->where($this->data->scopeColumn, '>=', $startHour)
                ->where($this->data->scopeColumn, '<=', $endHour);
        });
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
        $fromMonthYear = Util::parseDate(Util::formatDate($this->data->scopeFromDate(), 'Y-m-d'));
        $toMonthYear = Util::parseDate(Util::formatDate($this->data->scopeToDate(), 'Y-m-d'));

        $this->data->scope_from_date =  $fromMonthYear->startOfMonth();
        $this->data->scope_to_date =  $toMonthYear->endOfMonth();

        return $this->filterByRangeDate($query);
    }

    protected function filterByRangeYear($query)
    {
        return $query->where(function ($q) {
            $q->whereYear($this->data->scopeColumn, '>=', $this->data->scopeFromDate())
                ->whereYear($this->data->scopeColumn, '<=', $this->data->scopeToDate());
        });
    }

    protected function filterByFirstQuarter($query)
    {
        $this->data->scope_from_date = now()->month(1)->startOfMonth();
        $this->data->scope_to_date = now()->month(3)->endOfMonth();

        return $this->filterByRangeDate($query);
    }

    protected function filterBySecondQuarter($query)
    {
        $this->data->scope_from_date = now()->month(4)->startOfMonth();
        $this->data->scope_to_date = now()->month(6)->endOfMonth();
        return $this->filterByRangeDate($query);
    }

    protected function filterByThirdQuarter($query)
    {
        $this->data->scope_from_date = now()->month(7)->startOfMonth();
        $this->data->scope_to_date = now()->month(9)->endOfMonth();
        return $this->filterByRangeDate($query);
    }

    protected function filterByFourthQuarter($query)
    {
        $this->data->scope_from_date = now()->month(10)->startOfMonth();
        $this->data->scope_to_date = now()->month(12)->endOfMonth();
        return $this->filterByRangeDate($query);
    }
}

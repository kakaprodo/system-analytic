<?php

namespace Kakaprodo\SystemAnalytic\Lib\FilterHub;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\BaseClasses\AnalyticFilterHubBase;

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
                $query->orWhere(fn($q) => $this->applyFilter($q));
            }

            $this->data->setScopeColumn($this->initialScopeColumn);
        });
    }

    protected function  filterBySinceOneHour($query)
    {
        return $query->where($this->data->scopeColumn, '>=', now()->subHour());
    }

    protected function filterBySinceTwentyFourHours($query)
    {
        return $query->where($this->data->scopeColumn, '>=', now()->subHours(24));
    }

    protected function filterBySinceSevenDays($query)
    {
        return $query->where($this->data->scopeColumn, '>=', now()->subDays(7));
    }

    protected function filterBySinceFourteenDays($query)
    {
        return $query->where($this->data->scopeColumn, '>=', now()->subDays(14));
    }

    protected function filterBySinceThirtyDays($query)
    {
        return $query->where($this->data->scopeColumn, '>=', now()->subDays(30));
    }

    protected function filterByToday($query)
    {
        $operator = $this->data->scope_is_up_to ? "<=" : "=";

        return $query->whereDate($this->data->scopeColumn, $operator, today());
    }

    protected function filterByWeekAgo($query)
    {
        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', today()->subWeek());
        }

        return $query->where(
            fn($q) => $q->whereDate($this->data->scopeColumn, '>=', today()->subWeek())
                ->whereDate($this->data->scopeColumn, '<=', today())
        );
    }

    protected function filterByMonthAgo($query)
    {
        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', today()->subMonth());
        }

        return $query->where(
            fn($q) => $q->whereDate($this->data->scopeColumn, '>=', today()->subMonth())
                ->whereDate($this->data->scopeColumn, '<=', today())
        );
    }

    protected function filterByYearAgo($query)
    {
        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', today()->subYear());
        }

        return $query->where(
            fn($q) => $q->whereDate($this->data->scopeColumn, '>=', today()->subYear())
                ->whereDate($this->data->scopeColumn, '<=', today())
        );
    }

    protected function filterByThisWeek($query)
    {
        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', today()->endOfWeek());
        }

        return $query->where(
            fn($q) => $q->whereDate($this->data->scopeColumn, '>=', today()->startOfWeek())
                ->whereDate($this->data->scopeColumn, '<=', today()->endOfWeek())
        );
    }

    protected function filterByThisMonth($query)
    {
        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', today()->endOfMonth());
        }

        return $query->where(
            fn($q) => $q->whereDate($this->data->scopeColumn, '>=', today()->startOfMonth())
                ->whereDate($this->data->scopeColumn, '<=', today()->endOfMonth())
        );
    }

    protected function filterByThisYear($query)
    {
        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', today()->endOfYear());
        }

        return $query->where(
            fn($q) => $q->whereDate($this->data->scopeColumn, '>=', today()->startOfYear())
                ->whereDate($this->data->scopeColumn, '<=', today()->endOfYear())
        );
    }

    protected function filterByLastWeek($query)
    {
        $previousWeek = today()->subWeek();

        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', $previousWeek->endOfWeek());
        }

        $this->data->scope_from_date =  Util::formatDate($previousWeek->startOfWeek(), 'Y-m-d');
        $this->data->scope_to_date =  Util::formatDate($previousWeek->endOfWeek(), 'Y-m-d');

        return $this->filterByRangeDate($query);
    }

    protected function filterByLastMonth($query)
    {
        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', today()->subMonth()->endOfMonth());
        }

        $this->data->scope_value =  today()->subMonth()->startOfMonth();

        return $this->filterByFixedMonth($query);
    }

    protected function filterByLastYear($query)
    {
        if ($this->data->scope_is_up_to) {
            return  $query->whereDate($this->data->scopeColumn, '<=', today()->subYear()->endOfYear());
        }

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

        if ($this->data->scope_is_up_to) {
            return $query->where($this->data->scopeColumn, '<=', $this->data->scope_to_date);
        }

        return $this->filterByRangeHour($query);
    }

    protected function filterByFixedDate($query)
    {
        $date = Util::formatDate($this->data->scopeValue(), 'Y-m-d');
        $operator = $this->data->scope_is_up_to ? "<=" : "=";

        return $query->whereDate($this->data->scopeColumn,  $operator, $date);
    }

    protected function filterByFixedMonth($query)
    {
        $monthYear = Util::formatDate($this->data->scopeValue(), 'm-Y');
        [$month, $year] = explode('-', $monthYear);
        $operator = $this->data->scope_is_up_to ? "<=" : "=";

        return $query->whereMonth($this->data->scopeColumn, $operator, $month)
            ->whereYear($this->data->scopeColumn, $operator, $year);
    }

    protected function filterByFixedYear($query)
    {
        $operator = $this->data->scope_is_up_to ? "<=" : "=";

        return $query->whereYear(
            $this->data->scopeColumn,
            $operator,
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

        if ($this->data->scope_is_up_to) {
            return $query->whereDate($this->data->scopeColumn, '<=', $this->data->scope_to_date);
        }

        return $this->filterByRangeDate($query);
    }

    protected function filterBySecondQuarter($query)
    {
        $this->data->scope_from_date = now()->month(4)->startOfMonth();
        $this->data->scope_to_date = now()->month(6)->endOfMonth();

        if ($this->data->scope_is_up_to) {
            return $query->whereDate($this->data->scopeColumn, '<=', $this->data->scope_to_date);
        }

        return $this->filterByRangeDate($query);
    }

    protected function filterByThirdQuarter($query)
    {
        $this->data->scope_from_date = now()->month(7)->startOfMonth();
        $this->data->scope_to_date = now()->month(9)->endOfMonth();

        if ($this->data->scope_is_up_to) {
            return $query->whereDate($this->data->scopeColumn, '<=', $this->data->scope_to_date);
        }

        return $this->filterByRangeDate($query);
    }

    protected function filterByFourthQuarter($query)
    {
        $this->data->scope_from_date = now()->month(10)->startOfMonth();
        $this->data->scope_to_date = now()->month(12)->endOfMonth();

        if ($this->data->scope_is_up_to) {
            return $query->whereDate($this->data->scopeColumn, '<=', $this->data->scope_to_date);
        }

        return $this->filterByRangeDate($query);
    }
}

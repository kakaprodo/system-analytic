<?php

namespace Kakaprodo\SystemAnalytic\Lib\FilterHub;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\BaseClasses\AnalyticFilterHubBase;

/**
 * A filter class that detect the value of a named scope type
 * like this_day will be the carbon's today() function
 */
class AnalyticScopeValueHub extends AnalyticFilterHubBase
{
    /**
     * the scope start date
     */
    protected $startDate = null;

    /**
     * the scope end date
     */
    protected $endDate = null;

    public static function apply(
        AnalyticData $data,
    ) {
        if (!$data->scope_type) return null;

        $filter = (new self($data));

        $filter->applyFilter(null);

        return  $filter;
    }

    /**
     * get the start date after applying the filter
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * get the end date after applying the filter
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    protected function filterByToday($query)
    {
        $this->startDate = today()->endOfDay();
    }

    protected function filterByWeekAgo($query)
    {
        $this->startDate = today()->subWeek();
        $this->endDate = today()->endOfDay();
    }

    protected function filterByMonthAgo($query)
    {
        $this->startDate = today()->subDays(31);
        $this->endDate = today()->endOfDay();
    }

    protected function filterByYearAgo($query)
    {
        $this->startDate = today()->subYear();
        $this->endDate = today()->endOfDay();
    }

    protected function filterByThisWeek($query)
    {
        $this->startDate =  today()->startOfWeek();
        $this->endDate = today()->endOfDay();
    }

    protected function filterByThisMonth($query)
    {
        $this->startDate =  today()->startOfMonth();
        $this->endDate = today()->endOfDay();
    }

    protected function filterByThisYear($query)
    {
        $this->startDate = today()->startOfYear();
        $this->endDate = today()->endOfDay();
    }

    protected function filterByLastWeek($query)
    {
        $previousWeek = today()->subWeek();

        $this->startDate =  Util::formatDate($previousWeek->startOfWeek(), 'Y-m-d');
        $this->endDate =  Util::formatDate($previousWeek->endOfWeek(), 'Y-m-d');
    }

    protected function filterByLastMonth($query)
    {
        $this->startDate = today()->subDays(31)->startOfMonth();
    }

    protected function filterByLastYear($query)
    {
        $this->startDate = today()->subYear()->startOfYear();
    }

    protected function filterByFixedHour($query)
    {
        $this->startDate = Util::formatDate($this->data->scopeValue(), 'Y-m-d H:i:s');
        $this->endDate =  Util::parseDate($this->startDate)->endOfHour();
    }

    protected function filterByFixedDate($query)
    {
        $this->startDate = Util::formatDate($this->data->scopeValue(), 'Y-m-d');
    }

    protected function filterByFixedMonth($query)
    {
        $this->startDate = $this->data->scopeValue();
    }

    protected function filterByFixedYear($query)
    {
        $this->startDate = Util::parseDate($this->data->scopeValue() . '-1')->endOfDay();
    }

    protected function filterByRangeHour($query)
    {
        $this->startDate =  Util::formatDate($this->data->scopeFromDate(), 'Y-m-d H:i:s');
        $this->endDate = Util::formatDate($this->data->scopeToDate(), 'Y-m-d H:i:s');
    }

    protected function filterByRangeDate($query)
    {
        $this->startDate =  $this->data->scopeFromDate();
        $this->endDate = $this->data->scopeToDate();
    }

    protected function filterByRangeMonth($query)
    {
        $fromMonthYear = Util::parseDate(Util::formatDate($this->data->scopeFromDate(), 'Y-m-d'));
        $toMonthYear = Util::parseDate(Util::formatDate($this->data->scopeToDate(), 'Y-m-d'));

        $this->startDate =  $fromMonthYear->startOfMonth();
        $this->endDate =  $toMonthYear->endOfMonth();
    }

    protected function filterByRangeYear($query)
    {
        $this->startDate =   Util::parseDate($this->data->scopeFromDate() . '-1')->endOfDay();
        $this->endDate =  Util::parseDate($this->data->scopeToDate() . '-1')->endOfDay();
    }

    protected function filterByFirstQuarter($query)
    {
        $this->startDate  = now()->month(1)->startOfMonth();
        $this->endDate = now()->month(3)->endOfMonth();
    }

    protected function filterBySecondQuarter($query)
    {
        $this->startDate  = now()->month(4)->startOfMonth();
        $this->endDate  = now()->month(6)->endOfMonth();
    }

    protected function filterByThirdQuarter($query)
    {
        $this->startDate = now()->month(7)->startOfMonth();
        $this->endDate  = now()->month(9)->endOfMonth();
    }

    protected function filterByFourthQuarter($query)
    {
        $this->endDate = now()->month(10)->startOfMonth();
        $this->endDate = now()->month(12)->endOfMonth();
    }
}

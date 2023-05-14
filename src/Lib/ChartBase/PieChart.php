<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase;

use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;
use Kakaprodo\SystemAnalytic\Utilities\Util;

abstract class PieChart extends AnalyticHandler
{
    /**
     * The column name or a closure to group the query with
     */
    protected $groupBy = 'name';

    /**
     * the column to use when mapping data,
     * this coulmn will have the value of 
     * the grouped item.
     */
    protected $mappingColumnValue = null;

    /**
     * define the query to use for pie chart info,
     * 
     * @return Query
     */
    abstract protected function query();

    /**
     * where to inject the response of the pieChart result
     */
    abstract protected function result(array $pieChartData): AnalyticResponse;


    public function handle(): AnalyticResponse
    {
        $this->query = $this->query();

        $this->applyFilter();

        return $this->result($this->calculateResult());
    }

    protected function calculateResult()
    {

        $groupedItems = $this->getGroupedItems();

        $pieChartData = collect($groupedItems)
            ->map(fn ($items, $itemName) => $this->map($items, $itemName))
            ->all();

        return $pieChartData;
    }

    protected function getGroupedItems()
    {
        return $this->query->lazy()
            ->groupBy(function ($item) {
                return is_callable($this->groupBy)
                    ? ($this->groupBy)($item)
                    : $item->{$this->groupBy};
            })->all();
    }

    /**
     * THE MAP function to use after grouping the data per 
     * item
     */
    protected function map($items, $itemName)
    {
        return !$this->mappingColumnValue
            ? $items->count()
            : $items->sum($this->mappingColumnValue);
    }
}

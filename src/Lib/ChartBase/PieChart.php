<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase;

use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;

abstract class PieChart extends AnalyticHandler
{
    /**
     * The column name or a closure to group the query with
     */
    protected $groupBy = 'name';

    /**
     * the column on which aggreator will be applied
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

    /**
     * Format list to be grouped by the value of the $groupBy
     */
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

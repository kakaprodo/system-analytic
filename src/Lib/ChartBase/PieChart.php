<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase;

use App\Utilities\Analytics\Lib\AnalyticHandler;
use App\Utilities\Analytics\Lib\AnalyticResponse;

abstract class PieChart extends AnalyticHandler
{

    /**
     * mention whether the pie chart will come in percentage format
     */
    protected $withParcentage = false;

    /**
     * The column name or a closure to group the query with
     */
    protected $groupBy = 'name';

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
        $this->totalItems = $this->query->count();

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
        return !$this->withParcentage
            ? $items->count()
            : ((100 / $this->totalItems) * $items->count());
    }
}

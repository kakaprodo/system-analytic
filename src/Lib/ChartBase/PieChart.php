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
     * the column on which aggreator will be applied,
     * consider this as the agregation key
     */
    protected $mappingColumnValue = null;

    /**
     * Provide the way to calculate the grouped items
     * based on your aggregation type: sum,count,min,max
     */
    protected $aggregator = null;

    /**
     * In case the group column's value is empty, 
     * this value will be used
     */
    protected $defaultGroupingName = 'N/A';

    /**
     * Define how the group by value need to be formatted
     * 
     * eg: "@{name} and {age}" result in "@kakaprodo and 15"
     */
    protected $groupByPlaceholder = null;

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
            ->map(fn ($items) => $this->map($items))
            ->all();

        return $pieChartData;
    }

    /**
     * Format list to be grouped by the value of the $groupBy
     */
    protected function getGroupedItems()
    {
        // dd($this->query->get());
        return $this->query->lazy()
            ->groupBy(function ($item) {

                if ($this->groupByPlaceholder) return $this->replacePlaceholders(
                    $this->groupByPlaceholder,
                    (array) $item,
                    $this->defaultGroupingName
                );

                $value = is_callable($this->groupBy)
                    ? ($this->groupBy)($item)
                    : $item->{$this->groupBy};

                return  $value ?? $this->defaultGroupingName;
            })->all();
    }

    /**
     * THE MAP function to use after grouping the data per 
     * item
     */
    protected function map($items)
    {
        $aggregator = $this->aggregator;

        if ($aggregator) return $items->$aggregator($this->mappingColumnValue);

        return !$this->mappingColumnValue
            ? $items->count()
            : $items->sum($this->mappingColumnValue);
    }
}

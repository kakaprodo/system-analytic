<?php

namespace Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs;

use Illuminate\Support\Facades\DB;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;
use Kakaprodo\SystemAnalytic\Lib\ChartBase\PieChart;
use Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\Traits\HasAnalyticGateHelperTrait;

class AnalyticLogPieChart extends PieChart
{

    use HasAnalyticGateHelperTrait;

    /**
     * The column on which the scope will be applied to
     */
    protected $scopeColumn = "table.created_at";

    /**
     * The column that will be used to group the result
     * of your query
     */
    protected $groupBy = 'created_at';

    /**
     * the column to use when mapping data,
     * this coulmn will have the value of 
     * the grouped item.
     */
    protected $mappingColumnValue = null;

    /**
     * any task you want to be executed before any other
     * task in your handler
     */
    protected function boot() {}

    /**
     * The query that define the data that the package
     * is going to process
     */
    protected function query()
    {
        return DB::table('example');
    }

    /**
     * Result after processing your data
     */
    protected function result(array $pieChartData): AnalyticResponse
    {
        return $this->response($pieChartData);
    }
}

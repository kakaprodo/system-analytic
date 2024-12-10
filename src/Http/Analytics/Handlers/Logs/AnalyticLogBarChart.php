<?php

namespace Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;
use Kakaprodo\SystemAnalytic\Lib\ChartBase\BlockChart;
use Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\Traits\HasAnalyticGateHelperTrait;

class AnalyticLogBarChart extends BlockChart
{
    use HasAnalyticGateHelperTrait;

    /**
     * The column that will be used to group the result
     * of your query
     */
    protected $groupBy = 'formatted_created_at';

    /**
     * any task you want to be executed before any other
     * task in your handler
     */
    protected function boot()
    {
        $this->scopeColumn = $this->logTableName() . '.created_at';
    }

    /**
     * The query that define the data that the package
     * is going to process
     */
    protected function query()
    {
        return DB::table($this->logTableName())->select([
            DB::raw("SUM(value) as total_value"),
            DB::raw("DATE_FORMAT(created_at, '" . $this->data->scopeValueFormatForDb() . "') as {$this->groupBy}")
        ])->groupBy($this->groupBy)
            ->orderBy('created_at', 'asc');
    }

    /**
     * Result after processing your data
     */
    protected function result(LazyCollection $groupedResult): AnalyticResponse
    {
        $result = $groupedResult->map(function (Collection $logs) {
            return  $logs->sum('total_value');
        })->all();

        return $this->response($result);
    }
}

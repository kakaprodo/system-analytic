<?php

namespace Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs;

use Illuminate\Support\Facades\DB;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;
use Kakaprodo\SystemAnalytic\Lib\ChartBase\PieChart;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\GroupSearchInterface;
use Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\Traits\HasAnalyticGateHelperTrait;

class AnalyticLogPieChart extends PieChart implements GroupSearchInterface
{

    use HasAnalyticGateHelperTrait;

    /**
     * The column that will be used to group the result
     * of your querya
     */
    protected $groupBy = 'tag';

    /**
     * the column to use when mapping data,
     * this coulmn will have the value of 
     * the grouped item.
     */
    protected $mappingColumnValue = "total_value";

    public function beforePropertyValidation()
    {
        $this->logHandlerTypes = [
            Util::logModel()::HANDLER_PIECHAT,
            Util::logModel()::HANDLER_ALL,
        ];
    }

    /**
     * any task you want to be executed before any other
     * task in your handler
     */
    protected function boot()
    {
        $this->scopeColumn = $this->logTableName() . '.created_at';
        $this->logHandlerTypes = [
            Util::logModel()::HANDLER_PIECHAT,
            Util::logModel()::HANDLER_ALL,
        ];

        Util::whenNot($this->logTableExists(), 'Fetching logs is not supported.');
    }

    /**
     * The query that define the data that the package
     * is going to process
     */
    protected function query()
    {
        return DB::table($this->logTableName())
            ->select([
                DB::raw("SUM(value) as total_value"),
                "tag",
            ])->tap(fn($q) => $this->applyCommonFilterToQuery($q))
            ->orderBy('created_at', 'asc')
            ->groupBy('tag');
    }

    /**
     * Result after processing your data
     */
    protected function result(array $pieChartData): AnalyticResponse
    {
        return $this->response($pieChartData);
    }
}

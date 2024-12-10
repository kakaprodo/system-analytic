<?php

namespace Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs;

use Illuminate\Support\Facades\DB;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;
use Kakaprodo\SystemAnalytic\Lib\ChartBase\CardCount;
use Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\Traits\HasAnalyticGateHelperTrait;


class AnalyticLogCardCount extends CardCount
{
    use HasAnalyticGateHelperTrait;

    /**
     * the aggregate function to apply to the data
     */
    protected $aggregate = 'sum';

    /**
     * the column to use as argument of the aggregate function
     */
    protected $columnForAggregate = 'value';

    public static $scopeIsRequired = false;

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
        return DB::table($this->logTableName());
    }

    /**
     * Result after processing your data
     */
    protected function result($numberResult): AnalyticResponse
    {
        return $this->response([
            'number' => (float) $numberResult
        ]);
    }
}

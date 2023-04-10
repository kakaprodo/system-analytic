<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase;

use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;

/**
 * This analytic type doesn't need a main query to
 * provide result
 */
abstract class Computed extends AnalyticHandler
{
    /**
     * where to return the result
     */
    abstract protected function result(): AnalyticResponse;

    public function handle(): AnalyticResponse
    {
        return  $this->result();
    }

    /**
     * apply sum aggregator to a given data list
     */
    protected function sum($dataList, $column = "amount", $round = 2)
    {
        return round($dataList->sum($column), $round);
    }
}

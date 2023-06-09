<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;

abstract class CardCount extends AnalyticHandler
{
    /**
     * define how to count the query result
     * this can be: sum, count , avg...
     */
    protected $aggregate = null;

    protected $columnForAggregate = null;

    static $supportedAggregates = [
        'sum', 'count', 'avg', //.... you can add a new here
    ];

    /**
     * define the query to use for info to search,
     * 
     * @return Query
     */
    abstract protected function query();

    /**
     * where to inject the sum number or the count number
     */
    abstract protected function result($number): AnalyticResponse;

    public function handle(): AnalyticResponse
    {
        $this->query = $this->query();

        $this->filter();

        $this->validateAggregate();

        return  $this->result($this->builtResult());
    }

    protected function validateAggregate()
    {
        if (!$this->aggregate) return;

        $isSupportedAggregate = in_array($this->aggregate, static::$supportedAggregates);

        Util::whenNot(
            $isSupportedAggregate,
            'The aggregate function should be one of: ' . implode(',', static::$supportedAggregates)
        );
    }

    protected function builtResult()
    {
        return $this->aggregator($this->query);
    }

    /**
     * Calculate data based on a define aggregator method
     */
    protected function aggregator($query)
    {
        Util::whenNot($this->aggregate, 'Please define an aggregator on your handler');

        $method = $this->aggregate;

        if ($method == 'count') return $query->$method();

        return $query->$method($this->columnForAggregate);
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase;

use Illuminate\Support\LazyCollection;
use App\Utilities\Analytics\Lib\AnalyticHandler;
use App\Utilities\Analytics\Lib\AnalyticResponse;
use App\Utilities\Analytics\Lib\FilterHub\AnalyticFilterHub;

abstract class BlockChart extends AnalyticHandler
{

    /**
     * The property to group with the result
     */
    protected $groupBy = "created_at";

    static $exceptFilterScopeTypes = [
        AnalyticFilterHub::TYPE_TODAY,
        AnalyticFilterHub::TYPE_ALL
    ];

    /**
     * A column to use for applying data unique
     */
    protected $applyUniqueOnColumn = null;

    /**
     * define the query to use for pie chart info,
     * 
     * @return Query
     */
    abstract protected function query();

    /**
     * where to inject the response of the pieChart result
     */
    abstract protected function result(LazyCollection $data): AnalyticResponse;


    /**
     * Apply filter scope and build data for blockChart
     */
    public function handle(): AnalyticResponse
    {
        $this->query = $this->query();

        $this->applyFilter();

        return $this->result($this->groupBlockResult($this->query));
    }

    /**
     * where the query will be injected after get filtered to 
     * Group result foreach block of the bar-chart
     */
    protected function groupBlockResult($query)
    {
        return $query->lazy()->groupBy(function ($item) {
            return !$this->data->scopeIsYear()
                ? parseDate($item->{$this->groupBy})->format('d M Y')
                : parseDate($item->{$this->groupBy})->format('M Y');
        });
    }

    protected function sortResult(LazyCollection $result)
    {
        return $result->sort(function ($currentItem, $nextItem) {
            $currentDate = $currentItem->{$this->groupBy};
            $nextDate = $nextItem->{$this->groupBy};
            return parseDate($currentDate)->gt(parseDate($nextDate));
        });
    }

    /**
     * Apply unique on a liist of data
     * 
     * @param Collection $dataList
     */
    protected function applyUnique($dataList)
    {
        if (!$this->applyUniqueOnColumn) return $dataList;

        return $dataList->unique($this->applyUniqueOnColumn);
    }
}

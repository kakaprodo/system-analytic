<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase;

use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;

abstract class Search extends AnalyticHandler
{

    /**
     * The api resource class to be used when formating the response
     */
    public $resource = null;

    public $supportExport = true;

    /**
     * define the query to use for info to search,
     * 
     * @return Query
     */
    abstract protected function query();

    /**
     * where to inject the query on which filtering 
     * was appliid
     */
    abstract protected function result($query): AnalyticResponse;

    public function handle(): AnalyticResponse
    {
        $this->query = $this->query();

        $this->beforeFilter();

        if (!$this->data->search_value) $this->filter();

        return  $this->shouldExport ? $this->export($this->query) : $this->result($this->query);
    }

    public function beforeFilter()
    {
        if (!$this->data->search_value) {
            $this->data->throwWhenFieldAbsent('scope_type');
        }
    }

    protected function export($query)
    {
        return $this->response($query);
    }
}

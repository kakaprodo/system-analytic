<?php

namespace Kakaprodo\SystemAnalytic\Lib;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Model;
use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;
use Kakaprodo\SystemAnalytic\Lib\ChartBase\Search;
use Kakaprodo\SystemAnalytic\Lib\ExportHub\ExportHub;

class AnalyticResponse
{

    /**
     * @var \Kakaprodo\SystemAnalytic\Lib\AnalyticHandler
     */
    public $handler;
    public $result;

    public function __construct($result, AnalyticHandler $handler)
    {
        $this->result = $result;
        $this->handler = $handler;
    }

    public static function make($result, $handler)
    {
        return (new self(...func_get_args()));
    }

    public function format()
    {

        $isSearch = ($this->handler instanceof Search);

        if (!$isSearch) return [
            'result' => $this->result
        ];

        return $this->formatSearchAnalytic();
    }

    /**
     * format the response of type search
     */
    protected function formatSearchAnalytic()
    {
        if ($this->handler->shouldExport) return $this->export();

        $resource = $this->handler->resource;

        return  $resource ?  $this->formatResourceData($resource) : $this->result;
    }

    /**
     * Return single resource or a collectioon based on the 
     * response result
     */
    private function formatResourceData(string $resource)
    {
        return $this->result instanceof Model ?
            new $resource($this->result)
            : $resource::collection($this->result);
    }

    /**
     * EXPORT to a given file: excel or csv, pdf(future)
     */
    protected function export()
    {
        whenNot(
            $this->handler->supportExport,
            'The analytic type does not support the export feature'
        );

        return ExportHub::make($this);
    }
}

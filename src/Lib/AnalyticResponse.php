<?php

namespace Kakaprodo\SystemAnalytic\Lib;

use Illuminate\Database\Eloquent\Model;
use Kakaprodo\SystemAnalytic\Utilities\Util;
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

        if (!$resource = $this->handler->resource) return $this->result;

        return  $resource ?  $this->formatResourceData($resource) : $this->result;
    }

    /**
     * Return single resource or a collectioon based on the 
     * response result
     */
    private function formatResourceData(string $resource)
    {
        if ($this->result instanceof Model) return new $resource($this->result);

        return $resource::collection($this->result)->additional([
            'additional' => $this->handler->withResponse()
        ]);
    }

    /**
     * EXPORT to a given file: excel or csv, pdf(future)
     */
    protected function export()
    {
        Util::whenNot(
            $this->handler->supportExport,
            'The analytic type does not support the export feature'
        );

        return ExportHub::make($this);
    }
}

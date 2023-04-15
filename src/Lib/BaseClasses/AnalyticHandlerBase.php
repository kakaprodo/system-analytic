<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses;

use ReflectionClass;
use Illuminate\Support\Arr;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticBoolFilterHub;
use Kakaprodo\SystemAnalytic\Lib\Validation\HasAnalyticInterfaceValidationTrait;

abstract class AnalyticHandlerBase
{
    use HasAnalyticInterfaceValidationTrait;

    /**
     * the analytic query
     */
    protected $query = null;

    /**
     * @var \Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData
     */
    protected $data;

    /**
     * the date column on which the scope will be applied to
     */
    protected $scopeColumn = "created_at";

    /**
     * check if a filter was applied to the query
     */
    protected $filterWasApplied = false;

    /**
     * The unsupported filter types
     */
    static $exceptFilterScopeTypes = [];

    /**
     * The unsupported boolean filter types
     */
    static $exceptBoolFilterScopeTypes = [];

    /**
     * mention if data should be exporte
     */
    public $shouldExport = false;

    /**
     * check if a handler support the export feature
     */
    public $supportExport = false;

    /**
     * The export file, can be: xlsx, pdf, csv
     */
    public $exportFile = 'xlsx';

    /**
     * The class that will format the data before the export.
     * For more info consider reviewing the file: 
     * \Lib\ExportHub\Exports\Example
     */
    public $exportClass = null;

    /**
     * the api resource class that format the response
     */
    public $resource = null;

    /**
     * The view name(path) that data export will be from
     * eg: orders.exports.report
     */
    public $exportView = null;

    /**mention if a handler support filtering based on the provided scope */
    protected $filterable = true;

    /**
     * The key to use for caching or retrieving cached data
     */
    public $resultCacheKey = null;

    public function __construct(AnalyticData $data)
    {
        $this->data = $data;
        $this->shouldExport = $data->should_export;
        $this->exportFile = $data->file_type;
        $this->resultCacheKey = $this->data->dataKey();
    }

    /**
     * Actions(Tasks) to process just after rendering the result from 
     * the handler class
     */
    abstract public function afterResult();

    /**
     * get the query to execute
     */
    public function getQuery()
    {
        return $this->query();
    }

    /**
     * The type of the analytic handler
     */
    public static function type()
    {
        return Util::classToKebak(static::class);
    }


    protected function validateProperties()
    {
        $this->validateHandlerInterface($this);

        return $this;
    }


    /**
     * Define a periode in which analytic result will be cached
     */
    protected function shouldCacheFor()
    {
        return null;
    }

    /**
     * apply all the supported filter on the query
     */
    public function filter()
    {
        if (!$this->filterable) return $this;

        if (
            !$this->data->scope_type
            && !$this->data->boolean_scope_type
        ) return $this;

        return $this->applyBoolFilter()->applyFilter();
    }

    /**
     * reapply all the supported filter on a given query
     */
    public function reFilterQuery($query)
    {
        $this->query = $query;

        $this->filter();

        return $this->query;
    }

    /**
     * Apply scope filter to the existing query
     */
    public function applyFilter()
    {
        if (!$this->query && $this->filterWasApplied) return $this;

        $this->data->setScopeColumn($this->scopeColumn);

        $this->query = AnalyticFilterHub::apply($this->data, $this->query);

        return $this;
    }

    /**
     * Apply a boolean filtering
     */
    public function applyBoolFilter()
    {
        if (!$this->query) return $this;

        $this->query = AnalyticBoolFilterHub::apply($this->data, $this->query);

        return $this;
    }

    /**
     * a filter to be applied on a child query(or relation query)
     * 
     * eg: inside the whereHas closure
     */
    public function applyInnerFilter($query)
    {
        $this->filterWasApplied = true;

        return  AnalyticFilterHub::apply($this->data, $query);
    }

    /**
     * The suppoorted filters of the actual analytic chart type
     */
    public static function supportedFilterScopeTypes()
    {
        $filterHubConstants = (new ReflectionClass(AnalyticFilterHub::class))->getConstants();

        $supportedScopes = array_values($filterHubConstants);

        $exceptScopesIndex = array_map(
            function ($scopeType) use ($supportedScopes) {
                return array_search($scopeType, $supportedScopes);
            },
            static::$exceptFilterScopeTypes
        );

        return Arr::except(
            $supportedScopes,
            $exceptScopesIndex
        );
    }

    /**
     * The supported boolean filtering
     */
    public static function supportedBooleanFilterScopeTypes()
    {
        $filterHubConstants = (new ReflectionClass(AnalyticBoolFilterHub::class))->getConstants();

        $supportedScopes = array_values($filterHubConstants);

        $exceptScopesIndex = array_map(
            function ($scopeType) use ($supportedScopes) {
                return array_search($scopeType, $supportedScopes);
            },
            static::$exceptBoolFilterScopeTypes
        );

        return Arr::except(
            $supportedScopes,
            $exceptScopesIndex
        );
    }

    /**
     * Check if the analytic handler supports data caching
     */
    protected function shouldRenderCaches()
    {
        if ($this->data->needsToClearCache()) {

            cache()->forget($this->data->dataKey());

            return false;
        }

        if (!$this->shouldCacheFor()) return false;

        return cache()->has($this->resultCacheKey);
    }
}

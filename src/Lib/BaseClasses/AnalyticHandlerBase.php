<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses;

use ReflectionClass;
use Illuminate\Support\Arr;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticBoolFilterHub;
use Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits\HasMethodCallingTrait;
use Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits\HasRegisteredPluginClass;
use Kakaprodo\SystemAnalytic\Lib\Validation\HasAnalyticInterfaceValidationTrait;
use Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits\HasGeneralHandlerHelperTrait;

abstract class AnalyticHandlerBase
{
    use HasMethodCallingTrait,
        HasGeneralHandlerHelperTrait,
        HasAnalyticInterfaceValidationTrait,
        HasRegisteredPluginClass;

    /**
     * the analytic query
     */
    protected $query = null;

    /**
     * @var \Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData
     */
    public $data;

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
     * The export file, can be: xlsx, csv
     */
    public $exportFile = 'csv';

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
     * when this is called to a handler, it means, the handler
     * does not have to expect a scope type
     */
    public static $scopeIsRequired = true;

    /**
     * is true when the base query is being refiltered
     */
    public $isRefiltering = false;

    /**
     * the default selected value of the analytic handler that support options
     */
    public $defaultSelectedOption = null;

    /**
     * for group search analytics, validate search fields
     * even when the search_tern is not provided
     */
    public $shouldValidWhenSearchTermAbsent = true;

    public function __construct(AnalyticData $data)
    {
        $this->data = $data;
        $this->shouldExport = $data->should_export;
        $this->exportFile = $data->file_type;
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
     * get the analytic data
     */
    public function getData(): AnalyticData
    {
        return $this->data;
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
     * define wheter the handler support data caching
     */
    public function handlerSupportDataCaching()
    {
        return $this->getCachedPeriod() !== null;
    }

    /**
     * get the handler cache period
     */
    public function getCachedPeriod()
    {
        return $this->shouldCacheFor();
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
     * Call again the query from the handler
     * and apply all the filters on it
     * 
     * @return query
     */
    public function reFilterQuery()
    {
        $this->isRefiltering = true;

        $this->query = $this->query();

        $this->applyBoolFilter()->applyFilter();

        $this->isRefiltering = false;

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
     * data to return with the response result
     * if the handler support api resource class
     */
    public function withResponse(): array
    {
        return [];
    }
}

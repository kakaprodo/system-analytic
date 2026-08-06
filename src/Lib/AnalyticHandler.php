<?php

namespace Kakaprodo\SystemAnalytic\Lib;

use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\Cache\SystemAnalyticCache;
use Kakaprodo\SystemAnalytic\Lib\BaseClasses\AnalyticHandlerBase;

abstract class AnalyticHandler extends AnalyticHandlerBase
{

    /**
     * the lifecycle manager of the analytic chart Type
     */
    abstract protected function handle(): AnalyticResponse;


    /**
     * the lifecycle manager of the analytic handler 
     */
    public static function handler(AnalyticData $data)
    {
        $analytic = new static($data);

        $analytic->processHandleAccessibilityScopes();
        $analytic->processPlugins();

        $cachedResult = $analytic->cache()->getCachedResultIfExists();

        // return cached response if it exists
        if ($cachedResult != SystemAnalyticCache::NO_CACHE_RESULT) {
            return $analytic->response($cachedResult);
        }

        $analytic->beforePropertyValidation();
        $analytic->validateProperties();
        $analytic->boot();

        $result = $analytic->handle();

        $analytic->afterResult();

        return $result;
    }

    /**
     * this will come before validateProperties,boot 
     * method are called
     */
    public function beforePropertyValidation() {}

    /**
     * the task to call right after the creation of the instance object
     */
    protected function boot() {}

    public function afterResult() {}
}

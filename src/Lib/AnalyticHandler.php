<?php

namespace Kakaprodo\SystemAnalytic\Lib;

use Illuminate\Support\Facades\Cache;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
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

        // return cached response if it exists
        if ($analytic->shouldRenderCaches()) {
            $data = cache($analytic->resultCacheKey);
            return $analytic->response($data, true);
        }

        $analytic->validateProperties()->boot();

        $result = $analytic->handle();

        $analytic->afterResult();

        return $result;
    }

    /**
     * the task to call right after the creation of the instance object
     */
    protected function boot()
    {
    }

    public function afterResult()
    {
    }


    /**
     * format analytic response after checking the possibility of caching 
     * result
     */
    public function response($result, $forCache = false): AnalyticResponse
    {
        if ($this->supportCaching) {

            if (($cachePeriode = $this->shouldCacheFor()) && !$forCache) {
                Cache::put($this->resultCacheKey, $result, $cachePeriode);
            }
        }

        return AnalyticResponse::make($result, $this);
    }
}

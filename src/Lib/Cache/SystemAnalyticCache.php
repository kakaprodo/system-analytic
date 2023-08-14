<?php

namespace Kakaprodo\SystemAnalytic\Lib\Cache;

use Illuminate\Support\Facades\Cache;
use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;


class SystemAnalyticCache
{
    /**
     * @var AnalyticHandler
     */
    protected $handler;

    /**
     * check if the project support analytic response
     * caching
     */
    public $supportCaching = null;

    /**
     * The key name to use for caching or retrieving cached data
     */
    public $resultCacheKey = null;

    /**
     * response constant lists
     */

    /**
     * When no cache result was found for the handler
     */
    const NO_CACHE_RESULT = 'NO_CACHE_RESULT';

    public function __construct(AnalyticHandler $handler)
    {
        $this->handler = $handler;

        $this->supportCaching = config('system-analytic.should_cache_result');
    }

    /**
     * get the key name to use for caching data
     */
    public function getCacheKey()
    {
        if ($this->resultCacheKey) return $this->resultCacheKey;

        return $this->resultCacheKey  = $this->handler->data->dataKey();
    }

    /**
     * Check if the analytic handler supports data caching
     * 
     * but also clear analytic handler cache based on user 
     * request
     */
    public function shouldRenderCaches()
    {
        if (!$this->supportCaching) return false;

        if ($this->handler->data->needsToClearCache()) {

            Cache::forget($this->getCacheKey());

            return false;
        }

        if (!$this->handler->handlerSupportDataCaching()) return false;

        return Cache::has($this->getCacheKey());
    }

    /**
     * Fetch the cached result of the analytic handler
     */
    public function getCachedResult()
    {
        return cache()->get($this->getCacheKey());
    }

    /**
     * check if the handler 
     */
    public function getCachedResultIfExists()
    {
        if ($this->shouldRenderCaches()) return $this->getCachedResult();

        return self::NO_CACHE_RESULT;
    }

    /**
     * cache the result if the system support data caching and if
     * the result was not fetched from the existing cached data
     */
    public function cacheResultIfDoesnotExists($result, $fromExistingCache = false)
    {
        $cachePeriode = $this->handler->getCachedPeriod();

        if (
            !$this->supportCaching
            || $fromExistingCache
            || !$cachePeriode
        ) return;

        Cache::put($this->getCacheKey(), $result, $cachePeriode);
    }
}

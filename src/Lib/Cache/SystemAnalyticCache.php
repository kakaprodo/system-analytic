<?php

namespace Kakaprodo\SystemAnalytic\Lib\Cache;

use Illuminate\Support\Facades\Cache;
use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;
use Kakaprodo\SystemAnalytic\Lib\Cache\Traits\HasDataPersistenceTrait;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\ShouldPersistResponse;

class SystemAnalyticCache
{
    use HasDataPersistenceTrait;

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

        $this->handler->shouldPersistResult = $this->handler->shouldPersistResult ?? ($handler instanceof ShouldPersistResponse);
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
     * check if the project support data persistence
     */
    public function dataPersistenceIsSupported()
    {
        return config('system-analytic.persist_report.enabled');;
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
        // get data from cache
        if ($this->shouldRenderCaches()) return $this->getCachedResult();

        //check result from persisted data
        if ($result = $this->getPersistedResult()) return $result;

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

        $this->persistResult($result);
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits;

use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;
use Kakaprodo\SystemAnalytic\Lib\Cache\SystemAnalyticCache;


/**
 * A trait where to register plugin like: cache, response ...
 */
trait HasRegisteredPluginClass
{
    protected $cachingPlugin;

    /**
     * the caching and data persitence gate(plugin)
     */
    public function cache(): SystemAnalyticCache
    {
        if ($this->cachingPlugin) return $this->cachingPlugin;

        return $this->cachingPlugin  = new SystemAnalyticCache($this);
    }

    /**
     * format analytic response after checking the possibility 
     * of caching  result
     * 
     * @param bool $forCache : define whether the result is fetched from cache
     */
    public function response($result, $forCache = false): AnalyticResponse
    {
        $this->cache()->cacheResultIfDoesnotExists(
            $result,
            $forCache
        );

        return AnalyticResponse::make($result, $this);
    }
}

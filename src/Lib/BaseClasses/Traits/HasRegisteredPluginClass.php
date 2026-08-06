<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses\Traits;

use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\Plugins\PluginHub;
use Kakaprodo\SystemAnalytic\Lib\Cache\SystemAnalyticCache;
use Kakaprodo\SystemAnalytic\Lib\Shared\HandlerAccessibilityScope;
use Kakaprodo\SystemAnalytic\Lib\Shared\HandlerInteraction\CallAnalyticHandler;

/**
 * A trait where to register plugin like: cache, response ...
 * 
 * @property AnalyticData $data
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
    public function response($result): AnalyticResponse
    {
        $this->cache()->cacheResultIfDoesnotExists($result);

        return AnalyticResponse::make($result, $this);
    }

    /**
     * The plugin gate on which you can collect data handler for 
     * any supported plugin
     */
    public function plugin(): PluginHub
    {
        return  $this->data->pluginHub = new PluginHub($this->data);
    }

    /**
     * The gate to register the accessibility scope of handlers
     * (for example, you can register a handler to be accessible only
     * when the user is authenticated or based on a given condition)
     */
    public function handlerAccessibilityGate(): HandlerAccessibilityScope
    {
        return $this->data->handlerAccessibilityGate ??= new HandlerAccessibilityScope();
    }

    /**
     * A gate to call the result of a handler from another one
     */
    public function callHandler(string $handlerName)
    {
        return new CallAnalyticHandler($this->data, $handlerName);
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Lib\Plugins\Types;

use ReflectionClass;
use ReflectionMethod;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\BaseClasses\AnalyticPluginBase;

class ScopePlugin extends AnalyticPluginBase
{
    const HANDLER_ARRAY = 'array';
    const HANDLER_CLASS = 'class';

    public function load()
    {
        $dbQuery = func_get_args()[0];

        $handlerType = is_array($this->pluginHandler) ? self::HANDLER_ARRAY : self::HANDLER_CLASS;

        $handler = [
            self::HANDLER_ARRAY => fn () => $this->loadFromArray($dbQuery),
            self::HANDLER_CLASS => fn () => $this->loadFromClass($dbQuery),
        ][$handlerType];

        return Util::callFunction($handler);
    }

    /**
     * inject the main analytic handler query and the inputs data
     * to the scope handler
     */
    private function loadFromArray($dbQuery)
    {
        $scopeHandlers = [];

        foreach ($this->pluginHandler as $scopeName => $handler) {
            $scopeHandlers[$scopeName] = fn () => $handler($dbQuery, $this->data);
        }

        return $scopeHandlers;
    }

    /**
     * Convert class methods to modulars then inject db qeuery and the inputed data
     * to each class method
     */
    private function loadFromClass($dbQuery)
    {
        $pluginHandler = $this->pluginHandler;

        Util::whenNot(
            class_exists($pluginHandler),
            sprintf("Plugin %s not found, make sure registered plugin exist", $pluginHandler)
        );


        $reflection = new ReflectionClass($pluginHandler);
        $publicMethods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        $scopeHandlers = [];
        $pluginHandlerClass = new $pluginHandler();

        foreach ($publicMethods as $method) {
            $scopeHandlers[$method] = fn () => $pluginHandlerClass->{$method}($dbQuery, $this->data);
        }

        return $scopeHandlers;
    }
}

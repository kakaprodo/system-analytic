<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\Plugins\PluginHub;
use Kakaprodo\SystemAnalytic\Http\Requests\SystemAnalyticRequest;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\AnalyticHandlerRegisterInterface;

abstract class AnalyticHandlerRegisterBase implements AnalyticHandlerRegisterInterface
{
    /**
     * all the called macros value
     */
    protected $macros = [];

    /**
     * Handle the macro method calling
     */
    public function __call($name, $arguments)
    {
        $appropriateMethod = "macro" . (Util::strTitle($name));

        if ($this->$appropriateMethod) return $this->$appropriateMethod;

        if (!method_exists($this, $appropriateMethod)) {
            throw Util::fireErr("Method {$name} does not exists");
        }

        $value = $this->$appropriateMethod(...$arguments);

        return  $this->$appropriateMethod = $value;
    }

    /**
     * Build the laravel form validation rules
     */
    public static function formValidationRules($request)
    {
        return array_merge(
            AnalyticData::formValidationRules($request),
            static::requestRules($request)
        );
    }

    /**
     * The additional request rules to use 
     * in the integrated RequestForm validation
     */
    public static function requestRules(SystemAnalyticRequest $request): array
    {
        return [];
    }

    /**
     * method in which to load plugins
     */
    public function loadPlugins(PluginHub $pluginHub)
    {
    }

    public function __get($name)
    {
        return $this->macros[$name] ?? null;
    }

    public function __set($name, $value)
    {
        return $this->macros[$name] = $value;
    }
}

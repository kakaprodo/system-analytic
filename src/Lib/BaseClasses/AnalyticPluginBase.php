<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses;


abstract class AnalyticPluginBase
{
    /**
     * can be any a class or an array of key:value
     * @var string|array|null
     */
    public $pluginHandler = [];

    public function __construct($pluginHandler)
    {
        $this->pluginHandler = $pluginHandler;
    }

    /**
     * load the plugin functionality
     */
    abstract public function load();
}

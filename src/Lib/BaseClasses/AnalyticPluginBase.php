<?php

namespace Kakaprodo\SystemAnalytic\Lib\BaseClasses;

use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;


abstract class AnalyticPluginBase
{
    /**
     * can be any a class or an array of key:value
     * @var string|array|null
     */
    protected $pluginHandler = [];

    /**
     * The inputed data
     * 
     * @var AnalyticData
     */
    protected $data;

    public function __construct($pluginHandler, AnalyticData &$data)
    {
        $this->pluginHandler = $pluginHandler;
        $this->data = &$data;
    }

    /**
     * load the plugin functionality
     */
    abstract public function load();

    /**
     * to the registered handler of agiven plugin,
     * add new one
     * 
     * @param string|array|null $newHandlers
     */
    public function addHandler($newHandler)
    {
        $this->pluginHandler = array_merge((array)$this->pluginHandler, (array) $newHandler);

        return $this;
    }
}

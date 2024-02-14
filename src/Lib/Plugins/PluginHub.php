<?php

namespace Kakaprodo\SystemAnalytic\Lib\Plugins;

use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\Plugins\Types\ScopePlugin;

/**
 * This class will collect data that will used to load 
 * specific plugins
 * 
 * @property ScopePlugin $scopes
 */
class PluginHub
{
    public  $registeredPlugins = [];

    /**
     * The inputed data
     * @var AnalyticData
     */
    protected $data;

    public function __construct(AnalyticData &$data)
    {
        $this->data = &$data;
    }

    /**
     * Register functionalities that will extend scope or
     * query filtering feature
     * 
     * @param string|array payload : can be a class or an array
     */
    public function scope($payload)
    {
        $this->registeredPlugins['scopes'] = new ScopePlugin($payload, $this->data);

        return $this;
    }

    public function __get($name)
    {
        return  $this->registeredPlugins[$name] ?? null;
    }
}

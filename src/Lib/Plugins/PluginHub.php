<?php

namespace Kakaprodo\SystemAnalytic\Lib\Plugins;

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
     * Register functionalities that will extend scope or
     * query filtering feature
     * 
     * @param string|array data : can be a class or an array
     */
    public function scope($data)
    {
        $this->registeredPlugins['scopes'] = new ScopePlugin($data);

        return $this;
    }

    public function __get($name)
    {
        return  $this->registeredPlugins[$name] ?? null;
    }
}

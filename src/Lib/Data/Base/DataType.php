<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Exception;
use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\CustomData\Exceptions\MissedRequiredPropertyException;
use Kakaprodo\SystemAnalytic\Lib\Data\Base\AnalyticHandlerRegisterBase;
use Kakaprodo\SystemAnalytic\Lib\Plugins\PluginHub;

abstract class DataType extends CustomData
{
    /**
     * Class where handlers, expected data and other macroMethod 
     * will be registered
     * 
     * @var AnalyticHandlerRegisterBase
     */
    protected $handlerRegisterData;

    /**
     * Keeps the original values of provided scopes
     * eg: if scope=today; // value will be Y-m-d: 2023-10-11
     */
    protected $filterValueHub;

    /**
     * the gate to access to all registered plugins
     * @var PluginHub 
     */
    public $pluginHub;

    protected function expectedProperties(): array
    {
        return [];
    }

    /**
     * The class namespace of handlers register
     */
    public static function handlerRegisterClass()
    {
        return Util::handlerRegisterClass();
    }

    /**
     * the class where handler, expected Data and other macroMethod 
     * will are registered
     */
    public function handlerRegisterData(): AnalyticHandlerRegisterBase
    {
        if ($this->handlerRegisterData) return $this->handlerRegisterData;

        $register = self::handlerRegisterClass();

        return $this->handlerRegisterData = new $register();
    }

    /**
     * Throw exception when a field does not exist on customdata
     */
    public function throwWhenFieldAbsent($fieldName, $msg = null)
    {
        if ($this->$fieldName !== null) return;

        $msg = $msg ?? "The {$fieldName} field is required";

        if (method_exists($this, 'errorHandler')) {
            $this->errorHandler($msg);
        }

        throw new MissedRequiredPropertyException($msg ?? "The {$fieldName} field is required");
    }

    protected function errorHandler($errorMessage)
    {
        Util::fireErr($errorMessage)->die();
    }
}

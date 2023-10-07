<?php

namespace Kakaprodo\SystemAnalytic\Lib\Data\Base;

use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Http\Requests\SystemAnalyticRequest;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\Interfaces\AnalyticHandlerRegisterInterface;

abstract class AnalyticHandlerRegisterBase implements AnalyticHandlerRegisterInterface
{
    /**
     * Handle the macro method calling
     */
    public function __call($name, $arguments)
    {
        $appropriateMethod = "macro" . (Util::strTitle($name));

        if (!method_exists($this, $appropriateMethod)) {
            throw Util::fireErr("Method {$name} does not exists");
        }

        return $this->$appropriateMethod(...$arguments);
    }

    /**
     * Build the laravel form validation rules
     */
    public static function formValidationRules($request)
    {
        return array_merge(
            AnalyticData::formValidationRules(),
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

    public function __get($name)
    {
        return null;
    }
}

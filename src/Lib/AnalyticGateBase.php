<?php

namespace Kakaprodo\SystemAnalytic\Lib;

use Illuminate\Support\Str;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\CustomData\Helpers\CustomActionBuilder;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;

abstract class AnalyticGateBase extends CustomActionBuilder
{
    /**
     * Get All the Registered analytic types wiith their appropriate
     * handler classes
     */
    abstract static protected function registeredHandlers(): array;

    /**
     * detect the appropriate handler based on the provided data
     * in the request
     */
    protected function detectAndCreateHandler(AnalyticData $data): AnalyticResponse
    {
        $handlerClass = self::handlerClass($data->analyticType());

        return  $handlerClass::handler($data);
    }

    /**
     * all the names of analytics handlers
     */
    public static function allHandlers()
    {
        return array_keys(static::registeredHandlers());
    }

    /**
     * get the handler class of a given analytic type
     */
    public static function handlerClass($handlerType, $shouldThrowError = true)
    {
        $handlerClass = static::registeredHandlers()[Util::classToKebak($handlerType)] ?? null;

        if ($shouldThrowError) {
            Util::whenNot(
                $handlerClass,
                'Invalid analytic type, you should register first the handler of type:' . $handlerType
            );
        }

        return  $handlerClass;
    }

    /**
     * all the scope types supported by a given handler
     */
    public static function detectScopeTypes($handlerType)
    {
        if (!$handlerType) return [];

        $handlerClass = self::handlerClass($handlerType);

        return $handlerClass::supportedFilterScopeTypes();
    }

    /**
     * all the boolean scope types supported by a given handler
     */
    public static function detectBooleanScopeTypes($handlerType)
    {
        if (!$handlerType) return [];

        $handlerClass = self::handlerClass($handlerType);

        return $handlerClass::supportedBooleanFilterScopeTypes();
    }



    /**
     * all the scope types for fixed filtering
     */
    public static function supportedFixedScopeTypes()
    {
        return [
            AnalyticFilterHub::TYPE_FIXED_HOUR,
            AnalyticFilterHub::TYPE_FIXED_DATE,
            AnalyticFilterHub::TYPE_FIXED_MONTH,
            AnalyticFilterHub::TYPE_FIXED_YEAR,
        ];
    }

    public static function supportedRangeScopeTypes()
    {
        return [
            AnalyticFilterHub::TYPE_RANGE_HOUR,
            AnalyticFilterHub::TYPE_RANGE_DATE,
            AnalyticFilterHub::TYPE_RANGE_MONTH,
            AnalyticFilterHub::TYPE_RANGE_YEAR,
        ];
    }

    public static function isFixedScopeType($scopeType)
    {
        if (!$scopeType) return false;

        return in_array($scopeType, self::supportedFixedScopeTypes());
    }

    /**
     * Check if the scope type support from_date and to_date scope values
     */
    public static function isRangeScopeType($scopeType)
    {
        if (!$scopeType) return false;

        return in_array($scopeType, self::supportedRangeScopeTypes());
    }

    /**
     * check if a scope type should require a scope value
     * to be provided
     */
    public static function isPeriodicTypes($scopeType)
    {
        return self::isFixedScopeType($scopeType) || self::isRangeScopeType($scopeType);
    }

    /**
     * refresh persisted result of a given analytic handler
     * 
     * @param 
     */
    public static function refreshPersistedResult(
        $analyticType,
        $persistenceGroup = null,
        $onSingleRefreshing = null
    ) {
        $analyticType = is_numeric($analyticType) ? $analyticType : Util::classToKebak($analyticType);
        $columnName = is_numeric($analyticType) ? 'id' : 'analytic_type';

        $persistedResults = Util::persistModel()::where($columnName, $analyticType)
            ->tap(function ($q) use ($persistenceGroup) {
                if (!$persistenceGroup) return $q;

                $q->where('group', $persistenceGroup);
            })->get();

        foreach ($persistedResults as $analyticReport) {
            $record = array_merge($analyticReport->analytic_data, [
                'refresh_persisted_result' => true
            ]);

            self::process($record);

            Util::callFunction($onSingleRefreshing, null, $analyticReport);
        }
    }
}

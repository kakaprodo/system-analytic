<?php

namespace Kakaprodo\SystemAnalytic\Lib;

use Stringable;
use Illuminate\Support\Str;
use Kakaprodo\SystemAnalytic\Data\AnalyticData;
use App\Utilities\Analytics\Lib\AnalyticResponse;
use Kakaprodo\CustomData\Helpers\CustomActionBuilder;
use App\Utilities\Analytics\Handlers\System\SystemFundCard;
use Kakaprodo\SystemAnalytic\Lib\FilterHub\AnalyticFilterHub;
use App\Utilities\Analytics\Handlers\Setting\AnalyticOptionSetting;
use App\Utilities\Analytics\Handlers\User\SearchSpecificUserHandler;
use App\Utilities\Analytics\Handlers\System\SystemFundByWalletTypeCard;
use App\Utilities\Analytics\Handlers\System\SystemTransactionAmountByTypeCard;

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
        $handlerClass = static::registeredHandlers()[$data->analyticType()] ?? null;

        whenNot($handlerClass, "Invalid analytic type: {$data->analyticType()}");

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
     * all the scope types supported by a given handler
     */
    public static function detectScopeTypes($handlerType)
    {
        if (!$handlerType) return [];

        $handlerClass = static::registeredHandlers()[$handlerType] ?? null;

        whenNot($handlerClass, 'Invalid analalytic type:' . $handlerType);

        return $handlerClass::supportedFilterScopeTypes();
    }

    /**
     * all the boolean scope types supported by a given handler
     */
    public static function detectBooleanScopeTypes($handlerType)
    {
        if (!$handlerType) return [];

        $handlerClass = static::registeredHandlers()[$handlerType] ?? null;

        whenNot($handlerClass, 'Invalid analalytic type:' . $handlerType);

        return $handlerClass::supportedBooleanFilterScopeTypes();
    }



    /**
     * all the scope types for fixed filtering
     */
    public static function supportedFixedScopeTypes()
    {
        return [
            AnalyticFilterHub::TYPE_FIXED_DATE,
            AnalyticFilterHub::TYPE_FIXED_MONTH,
            AnalyticFilterHub::TYPE_FIXED_YEAR,
        ];
    }

    public static function supportedRangeScopeTypes()
    {
        return [
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
     * Check if the request should have a scope type
     */
    public static function  isScopeTypeRequired($analyticTypes)
    {
        // when request is not search analytic
        return Str::contains($analyticTypes, 'search') == false
            // list of analytics handler that don't support scope type
            && (in_array($analyticTypes, [
                SystemFundByWalletTypeCard::type(),
                SystemFundCard::type(),
                AnalyticOptionSetting::type()
            ]) == false);
    }
}

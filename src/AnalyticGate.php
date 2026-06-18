<?php

namespace Kakaprodo\SystemAnalytic;

use Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\AnalyticLogBarChart;
use Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\AnalyticLogCardCount;
use Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\AnalyticLogPieChart;
use Kakaprodo\SystemAnalytic\Lib\AnalyticGateBase;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Services\Computation\ComputationService;
use Kakaprodo\SystemAnalytic\Services\Log\LogService;

/**
 * @property LogService log
 */
class AnalyticGate extends AnalyticGateBase
{
    /**
     * supported services
     */
    static $services = [
        'log' => LogService::class,
        'computation' => ComputationService::class
    ];

    protected static function registeredHandlers(): array
    {
        return array_merge([
            AnalyticLogBarChart::type() => AnalyticLogBarChart::class,
            AnalyticLogPieChart::type() => AnalyticLogPieChart::class,
            AnalyticLogCardCount::type() => AnalyticLogCardCount::class,
        ], AnalyticData::handlers());
    }

    public function handle(AnalyticData $data)
    {
        $analyticResponse =  $this->detectAndCreateHandler($data);

        return $analyticResponse->format();
    }

    /**
     * Gate to analytic log services
     */
    public static function log(): LogService
    {
        return (new self())->log;
    }

    /**
     * Gate to analytic computation services
     */
    public static function computation(): ComputationService
    {
        return (new self())->computation;
    }

    /**
     * Access to service gates dynamically
     */
    public function __get($name)
    {
        $service = self::$services[$name] ?? null;

        if (!$service) return null;

        return new $service($this);
    }
}

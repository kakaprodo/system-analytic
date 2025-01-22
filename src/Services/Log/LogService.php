<?php

namespace Kakaprodo\SystemAnalytic\Services\Log;


use Illuminate\Support\Facades\Route;
use Kakaprodo\SystemAnalytic\Services\Base\ServiceBase;
use Kakaprodo\SystemAnalytic\Services\Log\Data\LogData;
use Kakaprodo\SystemAnalytic\Services\Log\Actions\CreateLogAction;
use Kakaprodo\SystemAnalytic\Http\Controllers\AnalyticLogController;
use Kakaprodo\SystemAnalytic\Services\Log\Actions\CreateManyLogAction;


class LogService extends ServiceBase
{
    /**
     * Create a single analytic log
     */
    public function create(array $inputs)
    {
        return CreateLogAction::process($this->inputs($inputs));
    }

    /**
     * Create many analytic logs at the same time
     */
    public function createMany(array $inputs)
    {
        return CreateManyLogAction::process($this->inputs($inputs));
    }

    /**
     * Load analytic logs routes
     */
    public function loadRoutes()
    {
        Route::group([
            'prefix' => 'analytic-logs',
        ], function () {
            Route::post('/add', [AnalyticLogController::class, 'store']);

            Route::post('/add-many', [AnalyticLogController::class, 'storeMany']);

            Route::get('/group-list/{tenant_id}', [
                AnalyticLogController::class,
                'getGroupList'
            ]);

            Route::get('/tag-list/{tenant_id}', [
                AnalyticLogController::class,
                'getTagList'
            ]);

            Route::get('/action-list/{tenant_id}', [
                AnalyticLogController::class,
                'getActionList'
            ]);
        });
    }

    /**
     * Get all log tags of a given tenant
     * with possiblity to filter by handler_type
     */
    public function getTags($tenantId): array
    {
        $inputs = is_array($tenantId) ?
            $tenantId
            : ['tenant_id' => $tenantId];

        return LogData::make($inputs)->getTags();
    }

    /**
     * Get all log groups of a given tenant
     * with possiblity to filter by handler_type
     */
    public function getGroups($tenantId): array
    {
        $inputs = is_array($tenantId) ?
            $tenantId
            : ['tenant_id' => $tenantId];

        return LogData::make($inputs)->getGroups();
    }

    /**
     * Get all log actions of a given tenant
     * with possiblity to filter by handler_type
     */
    public function getActions($tenantId): array
    {
        $inputs = is_array($tenantId) ?
            $tenantId
            : ['tenant_id' => $tenantId];

        return LogData::make($inputs)->getActions();
    }
}

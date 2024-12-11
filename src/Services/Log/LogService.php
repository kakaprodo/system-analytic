<?php

namespace Kakaprodo\SystemAnalytic\Services\Log;


use Illuminate\Support\Facades\Route;
use Kakaprodo\SystemAnalytic\Services\Base\ServiceBase;
use Kakaprodo\SystemAnalytic\Services\Log\Data\LogData;
use Kakaprodo\SystemAnalytic\Services\Log\Actions\CreateLogAction;
use Kakaprodo\SystemAnalytic\Http\Controllers\AnalyticLogController;


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
     * Load analytic logs routes
     */
    public function loadRoutes()
    {
        Route::group([
            'prefix' => 'analytic-logs',
        ], function () {
            Route::post('/add', [AnalyticLogController::class, 'store']);

            Route::get('/group-list/{tenant_id}', [
                AnalyticLogController::class,
                'getGroupList'
            ]);

            Route::get('/tag-list/{tenant_id}', [
                AnalyticLogController::class,
                'getTagList'
            ]);
        });
    }

    /**
     * Get all log tags of a given tenant
     */
    public function getTags($tenantId): array
    {
        return LogData::make(['tenant_id' => $tenantId])->getTags();
    }

    /**
     * Get all log groups of a given tenant
     */
    public function getGroups($tenantId): array
    {
        return LogData::make(['tenant_id' => $tenantId])->getGroups();
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Services\Log;


use Illuminate\Support\Facades\Route;
use Kakaprodo\SystemAnalytic\Services\Base\ServiceBase;
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
        });
    }
}

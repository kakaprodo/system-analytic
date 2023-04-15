<?php

namespace Kakaprodo\SystemAnalytic;

use Exception;
use Illuminate\Support\ServiceProvider;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Console\CreateAnalyticSkeleton;
use Kakaprodo\SystemAnalytic\Console\InstallAnalyticPackage;
use Kakaprodo\SystemAnalytic\Console\MakeExportFileGenerator;
use Kakaprodo\SystemAnalytic\Console\AnalyticHandlerGenerator;

class SystemAnalyticServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCommands();

        $this->stackToPublish();
    }

    /**
     * Register the command if we are using the application via the CLI
     */
    protected function registerCommands()
    {
        if (!$this->app->runningInConsole()) return;

        $this->commands([
            InstallAnalyticPackage::class,
            CreateAnalyticSkeleton::class,
            AnalyticHandlerGenerator::class,
            MakeExportFileGenerator::class
        ]);
    }

    public function stackToPublish()
    {
        $this->publishes([
            __DIR__ . '/config/system-analytic.php' => config_path('system-analytic.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/Skeleton' => Util::hubFolder(),
        ], 'analytic-skeleton');

        $this->publishes([
            __DIR__ . '/Http/Requests' => Util::validationFolder(),
        ], 'analytic-skeleton');
    }
}

<?php

namespace Kakaprodo\SystemAnalytic;

use Exception;
use Illuminate\Support\ServiceProvider;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Console\InstallAnalyticHub;
use Kakaprodo\SystemAnalytic\Console\MakeExportFileGenerator;
use Kakaprodo\SystemAnalytic\Console\AnalyticHandlerGenerator;
use Kakaprodo\SystemAnalytic\Console\InstallAnalyticConfigFile;
use Kakaprodo\SystemAnalytic\Console\RefreshPersistedAnalyticResult;

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
        if (!$this->app->runningInConsole()) return;

        $this->registerCommands();

        $this->stackToPublish();

        $this->stackToLoad();
    }

    /**
     * Register the command if we are using the application via the CLI
     */
    protected function registerCommands()
    {
        $this->commands([
            InstallAnalyticConfigFile::class,
            InstallAnalyticHub::class,
            AnalyticHandlerGenerator::class,
            MakeExportFileGenerator::class,
            RefreshPersistedAnalyticResult::class
        ]);
    }

    public function stackToPublish()
    {
        $this->checkForRequiredPackages();

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

    protected function stackToLoad()
    {
        if (Util::shouldRunPersistenceMigration()) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
    }

    protected function checkForRequiredPackages()
    {
        if (!class_exists('Kakaprodo\CustomData\CustomData')) {
            throw new Exception('The system analytic package requires the latest version of kakaprodo/custom-data');
        }
    }
}

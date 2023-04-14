<?php

namespace Kakaprodo\SystemAnalytic;

use Exception;
use Illuminate\Support\ServiceProvider;
use Kakaprodo\SystemAnalytic\Console\CreateAnalyticSkeleton;
use Kakaprodo\SystemAnalytic\Console\InstallAnalyticPackage;
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

        $this->checkForRequiredPackages();

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
            AnalyticHandlerGenerator::class
        ]);
    }

    protected function checkForRequiredPackages()
    {
        if (!class_exists('Kakaprodo\CustomData\CustomData')) {
            throw new Exception('The system analytic package requires the latest version of kakaprodo/custom-data');
        }
    }

    public function stackToPublish()
    {
        $this->publishes([
            __DIR__ . '/config/system-analytic.php' => config_path('system-analytic.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/Skeleton' => config('system-analytic.analytic_path') . '/' . config('system-analytic.folder_name'),
        ], 'analytic-skeleton');
    }
}

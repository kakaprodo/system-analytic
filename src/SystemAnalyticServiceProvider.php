<?php

namespace Kakaprodo\SystemAnalytic;

use Exception;
use Illuminate\Support\ServiceProvider;

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
        $this->checkForRequiredPackages();

        $this->stackToPublish();
    }

    protected function checkForRequiredPackages()
    {
        if (!class_exists('Kakaprodo\CustomData\CustomData')) {
            throw new Exception('The system analytic package requires the latest version of kakaprodo/custom-data');
        }

        if (!class_exists('Maatwebsite\Excel\Excel')) {
            throw new Exception('The system analytic package requires the latest version of maatwebsite/excel');
        }
    }

    public function stackToPublish()
    {
        $this->publishes([
            __DIR__ . '/config/system-analytic.php' => config_path('system-analytic.php'),
        ]);
    }
}

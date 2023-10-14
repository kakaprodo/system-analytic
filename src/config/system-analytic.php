<?php

use App\Http\SystemAnalytic\AnalyticHandlerRegister;
use Kakaprodo\SystemAnalytic\Models\SystemAnalyticReport;


return [
    /**
     * The class where analytic handlers will be registered,
     * You can specify your own class but it should follow
     * the same standard as the default one. and when you 
     * change the class's location, remember to resolve the 
     * namespacing
     */
    'handler_register' => AnalyticHandlerRegister::class,

    /**
     * Where the analytic folder will be installed.
     * Note that. after the package is installed you
     * can move this folder to your desired location
     * but you should only resolve the namespacing
     */
    'analytic_path' => app_path('Http'),

    /**
     * The folder name of the analytic hub, this folder
     * contain all the analytic handlers and the handler 
     * register. When you change the name, then you should
     * resolve the namespacing
     */
    'folder_name' => 'SystemAnalytic',


    /**
     * The location where the package will publish a form 
     * request class. Just to help you validating your 
     * analytic input in a good way.If you change the
     * default configuration, you should resolve the 
     * namespace
     */
    'form_validation_path' => app_path('Http/Requests'),

    /**
     * Define if the response about the analytic should be cached.
     * Note that, this is not applied on the "List" analytic type.
     */
    'should_cache_result' => false,


    /**
     * By default the package support data export using  csv, xlsx and 
     * view template. But when the value of this key true, that means, 
     * the package will use only csv to export data.
     */
    'export_to_csv_only' => false,

    /**
     * The chunk number to be used when exporting with csv
     */
    'chunk_number_for_export' => 1000,

    /**
     * Persisting report
     */
    "persist_report" => [
        /**
         * When this is true, result of AnalyticHandlers that 
         * support scope will be kept in database forever if 
         * the scope period is a past date.
         * 
         * Note: after enabling this, you should run the 
         * migration
         */
        'enabled' => false,

        /**
         * When it is false, then the package will not run the 
         * migration file that create the data persistence table
         */
        'should_run_migration' => true,

        /**
         * The model that provides crud on the persisted report
         */
        'model' => SystemAnalyticReport::class,

        /**
         * the table in which persisted result will be stored
         */
        'table_name' => 'system_analytic_reports'
    ]
];

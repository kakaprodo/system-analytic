<?php

use App\Http\SystemAnalytic\AnalyticHandlerRegister;
use Kakaprodo\SystemAnalytic\Models\AnalyticLog;
use Kakaprodo\SystemAnalytic\Models\SystemAnalyticReport;

return [
    /*
    |--------------------------------------------------------------------------
    | Analytic Handler Registration
    |--------------------------------------------------------------------------
    |
    | Class for registering analytic handlers. Ensure to update the namespace
    | if you change the class or its location.
    |
    */
    'handler_register' => AnalyticHandlerRegister::class,

    /*
    |--------------------------------------------------------------------------
    | Analytic Path
    |--------------------------------------------------------------------------
    |
    | Path where the analytic folder will be installed. You can move the folder
    | later but make sure to resolve the namespace.
    |
    */
    'analytic_path' => app_path('Http'),

    /*
    |--------------------------------------------------------------------------
    | Analytic Hub Folder Name
    |--------------------------------------------------------------------------
    |
    | Name of the folder containing analytic handlers and the handler register.
    | Update the namespace if the name changes.
    |
    */
    'folder_name' => 'SystemAnalytic',

    /*
    |--------------------------------------------------------------------------
    | Form Request Validation Path
    |--------------------------------------------------------------------------
    |
    | Path for the form request class used to validate analytic input. Update
    | the namespace if this path changes.
    |
    */
    'form_validation_path' => app_path('Http/Requests'),

    /*
    |--------------------------------------------------------------------------
    | Caching Analytic Results
    |--------------------------------------------------------------------------
    |
    | Enable or disable caching of analytic results. This does not apply to 
    | the "List" analytic type.
    |
    */
    'should_cache_result' => false,

    /*
    |--------------------------------------------------------------------------
    | Export Data Format
    |--------------------------------------------------------------------------
    |
    | Set to true to restrict export to CSV format only. By default, CSV, XLSX,
    | and view templates are supported.
    |
    */
    'export_to_csv_only' => false,

    /*
    |--------------------------------------------------------------------------
    | Chunk Size for CSV Export
    |--------------------------------------------------------------------------
    |
    | Specifies the chunk size for exporting data in CSV format.
    |
    */
    'chunk_number_for_export' => 1000,

    /*
    |--------------------------------------------------------------------------
    | Report Persistence
    |--------------------------------------------------------------------------
    |
    | Configuration for persisting analytic reports. If enabled, you must run 
    | the migration to create the persistence table.
    |
    */
    'persist_report' => [
        'enabled' => false,
        'should_run_migration' => true,
        'model' => SystemAnalyticReport::class,
        'table_name' => 'system_analytic_reports',
    ],


    /*
    |--------------------------------------------------------------------------
    | Report Recording
    |--------------------------------------------------------------------------
    |
    | Configuration for recording any report of your system
    |
    */
    'log_report' => [
        'should_run_migration' => false,
        'table_name' => 'sa_logs',
        'model' => AnalyticLog::class,
    ]
];

<?php

use App\Http\SystemAnalytic\AnalyticHandlerRegister;


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
];

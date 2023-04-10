<?php

use Kakaprodo\SystemAnalytic\Data\AnalyticHandlerRegisterData;


return [
    /**
     * The class where analytic handlers will be registered,
     * You can specify your own class but it should follow
     * the same standard as the default one
     */
    'handler_register' => AnalyticHandlerRegisterData::class,
];

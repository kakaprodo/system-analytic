<?php

namespace Kakaprodo\SystemAnalytic\Lib\Interfaces;


interface AnalyticHandlerRegisterInterface
{
    /**
     * Where to register all your hanlders within a key value array
     * eg:
     * [
     *   ActiveUserHandler::type() => ActiveUserHandler::class
     * ]
     */
    public static function handlers(): array;

    /**
     * Where you need to register all expected data to be used
     * in your handler
     * eg. [
     *  'user_id'
     * ]
     * 
     * then in your handler you can access that property by using:
     * $this->data->user_id
     * 
     */
    public function expectedData(): array;

    /**
     * properties to be ingnored among the expectedData  properties 
     * when generating a unique key for the provided data.
     * 
     * This is useful when you want to cash the analytic response
     */
    public function ignoreForKeyGenerator(): array;
}
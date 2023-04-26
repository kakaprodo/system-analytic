<?php

namespace Kakaprodo\SystemAnalytic\Lib\Interfaces;

use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;


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
    public function expectedData(CustomData $data): array;

    /**
     * properties to be ingnored among the expectedData  properties 
     * when generating a unique key for the provided data.
     * 
     * This is useful when you want to cash the analytic response
     */
    public function ignorePropertyForKeyGenerator(): array;


    /**
     * a function that define whether the current authenticated user 
     * is an admin user
     */
    public function scopeUserIsAdmin(AnalyticHandler $handler);
}

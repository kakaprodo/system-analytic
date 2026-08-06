<?php

namespace Kakaprodo\SystemAnalytic\Lib\Shared;

use Closure;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Utilities\Util;

/**
 * A class to manage the accessibility scope of analytic handlers
 */
class HandlerAccessibilityScope
{
    public static array $accessibleForAuthUsers = [];

    public static array $accessibleOnEvents = [];

    public static array $allProtectedHandlers = [];

    /**
     * Register handlers that can be accessible only when user is authenticated.
     * */
    public function onAuth(array $handlerClasses)
    {
        foreach ($handlerClasses as $handlerClass) {
            HandlerAccessibilityScope::$accessibleForAuthUsers = [
                ...(HandlerAccessibilityScope::$accessibleForAuthUsers ?? []),
                $handlerClass => true
            ];

            HandlerAccessibilityScope::$allProtectedHandlers[$handlerClass::type()] = $handlerClass;
        }
    }


    /**
     * Register handlers that can be accessible only based on a given condition
     * */
    public function when(Closure|bool $statement, array $handlerClasses)
    {
        foreach ($handlerClasses as $handlerClass) {
            HandlerAccessibilityScope::$accessibleOnEvents = [
                ...(HandlerAccessibilityScope::$accessibleOnEvents ?? []),
                $handlerClass => $statement
            ];

            HandlerAccessibilityScope::$allProtectedHandlers[$handlerClass::type()] = $handlerClass;
        }
    }

    /**
     * grab all the protected handlers that are registered in the accessibility scope
     * */
    public static function getAllProtectedHandlers()
    {
        return HandlerAccessibilityScope::$allProtectedHandlers;
    }

    /**
     * Check if a handler is accessible
     * @return bool|never
     * */
    public function validate(string $handlerClass, AnalyticData $data)
    {
        $handlerIsInAuthScope = HandlerAccessibilityScope::$accessibleForAuthUsers[$handlerClass] ?? false;
        $eventStatement = HandlerAccessibilityScope::$accessibleOnEvents[$handlerClass] ?? null;
        $handlerIsInEventsScope = $eventStatement instanceof Closure ? $eventStatement($data) : (bool) $eventStatement;

        // when handler is not registered any where, allow access
        if (!$handlerIsInAuthScope && $eventStatement === null) {
            return true;
        }

        // when handler is registered on auth scop, grant access
        if (auth()->check() && $handlerIsInAuthScope) {
            return true;
        }

        // When handler is registered on event scope, grant access
        if ($handlerIsInEventsScope) {
            return true;
        }

        Util::fireErr("Unauthorized access for the analytic: " . class_basename($handlerClass), 403)
            ->withData([
                'code' => 'HANDLER_ACCESSIBILITY_SCOPE_FAILED',
            ])->die();
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Monitoring\Events;

use Illuminate\Routing\Events\ResponsePrepared;
use Kakaprodo\SystemAnalytic\MonitorCore;
use Kakaprodo\SystemAnalytic\Monitoring\DataPayload\RequestPayloadData;
use Throwable;

/**
 * @internal
 */
final class ResponsePreparedListener
{
    public function __construct(
        private MonitorCore $monitor,
    ) {
        //
    }

    public function __invoke(ResponsePrepared $event): void
    {
        try {
            $request = $event->request;
            $response =  $event->response;
            $user = $request->user();

            RequestPayloadData::make([
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ] : null,
                'payload' => $request->except(['password', 'password_confirmation']),
                'headers' => $request->headers->all(),
                'status_code' =>  $response->getStatusCode(),
                'monitor' => $this->monitor
            ]);
        } catch (Throwable $e) {

            // $this->monitor->report($e);
        }
    }
}

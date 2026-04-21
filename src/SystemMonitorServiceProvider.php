<?php

namespace Kakaprodo\SystemAnalytic;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Routing\Events\ResponsePrepared;
use Illuminate\Support\ServiceProvider;
use Kakaprodo\SystemAnalytic\Facades\SystemMonitor;
use Kakaprodo\SystemAnalytic\Monitoring\Events\ExceptionHandlerResolvedHandler;
use Kakaprodo\SystemAnalytic\Monitoring\Events\QueryExecutedListener;
use Kakaprodo\SystemAnalytic\Monitoring\Events\ResponsePreparedListener;
use Ramsey\Uuid\Uuid as BaseUuid;

class SystemMonitorServiceProvider extends ServiceProvider
{

    private bool $isRequest;
    private MonitorCore $monitor;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!config('system-analytic.monitoring.enabled')) return;

        try {
            $this->captureExecutionType();
            $this->registerBindings();
            $this->registerEvents();
        } catch (\Throwable $th) {
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!config('system-analytic.monitoring.enabled')) return;

        if (!$this->app->runningInConsole()) return;

        $this->registerCommands();
    }

    private function captureExecutionType(): void
    {
        $this->isRequest = !$this->app->runningInConsole();
    }

    private function registerBindings()
    {
        // event collection uuid
        $uuid = BaseUuid::uuid4()->toString();

        $this->app->instance(SystemMonitor::class, $this->monitor = new MonitorCore(
            uuid: $uuid
        ));
    }

    /**
     * Register the command if we are using the application via the CLI
     */
    protected function registerCommands()
    {
        $this->commands([]);
    }

    private function registerEvents()
    {
        /** @var Dispatcher */
        $events = $this->app->make(Dispatcher::class);

        $events->listen(QueryExecuted::class, (new QueryExecutedListener($this->monitor))(...));

        $this->callAfterResolving(ExceptionHandler::class, (new ExceptionHandlerResolvedHandler($this->monitor))(...));

        if ($this->isRequest) {
            $this->registerRequestHooks($events, $this->monitor);
        } else {
            $this->registerConsoleHooks($events, $this->monitor);
        }
    }

    private function registerRequestHooks(Dispatcher $events, MonitorCore $monitor): void
    {
        $events->listen(ResponsePrepared::class, (new ResponsePreparedListener($monitor))(...));
    }

    private function registerConsoleHooks()
    {
        // coming
    }
}

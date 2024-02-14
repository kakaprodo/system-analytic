<?php

namespace Kakaprodo\SystemAnalytic\Lib\Cache\Traits;

use Illuminate\Support\Arr;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\AnalyticHandler;

/**
 * @property AnalyticHandler handler
 * 
 */
trait HasDataPersistenceTrait
{
    /**
     * the scope start date
     */
    protected $startDate = null;

    /**
     * the scope end date
     */
    protected $endDate = null;

    /**
     * validate data persistence then store the result
     * 
     * @param mixed $result: the analytic handler result
     * @param bool $$shouldRefresh define whether the result 
     * need to be updated
     */
    protected function persistResult($result, $shouldRefresh = false)
    {
        if (
            !$this->dataPersistenceIsSupported()
            || !$this->handler->shouldPersistResult
        ) return;

        if (!$shouldRefresh) {
            if ($this->persistedKeyExists($this->getCacheKey())) return;
        }

        if ($this->handler->persistResultOfAnyScope) $this->storeResult($result, $shouldRefresh);

        // the @reportScopeIsInPast sets he startDate and the endDate
        $scopeIsPastPeriod = $this->reportScopeIsInPast();

        if (!$scopeIsPastPeriod) return;

        $this->storeResult($result, $shouldRefresh);
    }

    /**
     * save the result in database
     * 
     * @param bool $shouldRefresh : when the persisted result need to 
     * be refreshed
     */
    private function storeResult($result, $shouldRefresh = false)
    {
        $analyticData = Arr::except(
            $this->handler->data->onlyValidated(),
            $this->handler->data->ignoreForKeyGenerator()
        );

        $records = [
            'name' => $this->getCacheKey(),
            'value' => $result,
            'analytic_type' => Util::classToKebak($this->handler->data->analytic_type),
            'analytic_data' =>  $analyticData,
            'scope_start_date' => $this->startDate,
            'scope_end_date' => $this->endDate,
            'group' => $this->handler->persistenceGroup
        ];

        if ($shouldRefresh) {
            Util::persistModel()::updateOrCreate([
                'name' => $this->getCacheKey(),
            ], $records);

            return;
        }

        Util::persistModel()::create($records);
    }

    /**
     * check if the persisted key already exist
     */
    private function persistedKeyExists($name)
    {
        return  Util::persistModel()::where('name', $name)->exists();
    }

    /**
     * check it the provided scope is in the past and 
     * whether it should be persisted based on the 
     * configuration
     */
    public function reportScopeIsInPast()
    {
        $scopeFilter = $this->handler->data->filterValueHub();

        $this->startDate =  $scopeFilter->getStartDate();
        $this->endDate =  $scopeFilter->getEndDate();

        if (!$this->startDate || !$this->startDate) return false;

        if ($this->endDate) {
            $this->endDate = Util::parseDate($this->endDate);
            return $this->endDate->isPast();
        }

        $this->startDate = Util::parseDate($this->startDate);

        return $this->startDate->isPast();
    }

    /**
     * Grab result from the persisted table if the system
     * and the handler support data persistence
     */
    public function getPersistedResult()
    {
        if ($this->handler->data->needToRefreshPersistedData()) return;

        if (!$this->dataPersistenceIsSupported() || !$this->handler->shouldPersistResult) return;

        $result = Util::persistModel()::where('name', $this->getCacheKey())->first();

        return optional($result)->value;
    }
}

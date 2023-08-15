<?php

namespace Kakaprodo\SystemAnalytic\Lib\Cache\Traits;

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
     */
    protected function persistResult($result)
    {
        if (!$this->dataPersistenceIsSupported() || !$this->handler->shouldPersistResult) return;

        if ($this->persistedKeyExists($this->getCacheKey())) return;

        if (!$this->reportScopeIsInPast()) return;

        $this->storeResult($result);
    }

    /**
     * save the result in database
     * 
     * @param bool $shouldRefresh : when the persisted result need to 
     * be refreshed
     */
    private function storeResult($result, $shouldRefresh = false)
    {
        $records = [
            'name' => $this->getCacheKey(),
            'value' => $result,
            'analytic_type' => $this->handler->data->analytic_type,
            'analytic_data' => $this->handler->data->all(),
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

        if ($this->endDate) return Util::parseDate($this->endDate)->isPast();

        return Util::parseDate($this->startDate)->isPast();
    }

    /**
     * Grab result from the persisted table if the system
     * and the handler support data persistence
     */
    public function getPersistedResult()
    {
        if (!$this->dataPersistenceIsSupported() || !$this->handler->shouldPersistResult) return;

        $result = Util::persistModel()::where('name', $this->getCacheKey())->first();

        return optional($result)->value;
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Lib\ChartBase\Traits;

use Kakaprodo\SystemAnalytic\Lib\AnalyticResponse;

trait ShouldIgnoreFilteringOnSearchAnalytic
{
    public function handle(): AnalyticResponse
    {
        $this->query = $this->query();

        $this->beforeFilter();

        // only filter data when request doesn't have a search term
        if (!$this->data->search_value) $this->filter();

        return  $this->shouldExport ? $this->export($this->query) : $this->result($this->query);
    }

    public function beforeFilter()
    {
        // force scope type when search is not being applied
        if (!$this->data->search_value && $this->shouldForceScopeType) {
            $this->data->throwWhenFieldAbsent('scope_type');
        }
    }
}

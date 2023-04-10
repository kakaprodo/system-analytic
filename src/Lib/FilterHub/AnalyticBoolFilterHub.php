<?php

namespace Kakaprodo\SystemAnalytic\Lib\FilterHub;

use Kakaprodo\SystemAnalytic\Data\AnalyticData;

class AnalyticBoolFilterHub
{
    /**
     * @var AnalyticData
     */
    protected $data;

    const WITH_TRASHED = 'with_trashed';
    const ONLY_TRASHED = 'only_trashed';

    public function __construct(AnalyticData $data)
    {
        $this->data = $data;
    }

    public static function apply(AnalyticData $data, $query)
    {
        if (!$data->boolean_scope_type) return $query;

        return (new self($data))->applyFilter($query);
    }

    public function applyFilter($query)
    {
        $filterHandlers =  [
            self::WITH_TRASHED => fn () => $this->filterWithTrashed($query),
            self::ONLY_TRASHED => fn () => $this->filterOnlyTrashed($query),
        ][$this->data->boolean_scope_type] ?? null;

        return callFunction(
            $filterHandlers,
            'Un-supported boolean filter type: ' . $this->data->boolean_scope_type
        );
    }

    protected function filterWithTrashed($query)
    {
        return $query->withTrashed();
    }

    protected function filterOnlyTrashed($query)
    {
        return $query->onlyTrashed();
    }
}

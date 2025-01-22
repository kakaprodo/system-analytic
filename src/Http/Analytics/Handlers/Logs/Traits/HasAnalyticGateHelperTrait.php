<?php

namespace Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\Traits;

use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\CustomData\Lib\TypeHub\DataTypeHub;

trait HasAnalyticGateHelperTrait
{
    /**
     * The default logs handler types supported by
     * the current analytic handler
     */
    public $logHandlerTypes = [];

    /**
     * the table that save the logged repports
     */
    protected function logTableName()
    {
        return (new (Util::logModel()))->getTable();
    }

    /**
     * Apply tenant, tag, group and identifier filter
     * based on their availability
     */
    public function applyCommonFilterToQuery($q)
    {
        $q->where('tenant_id', $this->getSearchValue('tenant_id'))
            ->whereIn('handler_type', $this->getSearchValue('handler_types'))
            ->tap(function ($q) {
                if (!($group = $this->getSearchValue('group'))) return $q;

                $q->where('group', $group);
            })->tap(function ($q) {
                if (!($tag = $this->getSearchValue('tag'))) return $q;

                $q->where('tag', $tag);
            })->tap(function ($q) {
                if (!($identifier = $this->getSearchValue('identifier'))) return $q;

                $q->where('identifier', $identifier);
            })->tap(function ($q) {
                if (!($action = $this->getSearchValue('action'))) return $q;

                $q->where('action', $action);
            });
    }

    public function expectedSearchFieldsWithValidation(CustomData $data): array
    {
        return [
            'tenant_id' => $data->property()->string(),
            'identifier?' => $data->property()->string(),
            'group?' => $data->property()->string(),
            'action?' => $data->property()->string(),
            'tag?' => $data->property()->string(),
            'handler_types' => $data->property()
                ->default($this->logHandlerTypes)
                ->customValidator(function ($handlerTypes, DataTypeHub $validator) {

                    if (!is_array($handlerTypes)) {
                        $validator->message("handler_types should be an array");
                        return false;
                    }

                    $supportedHandlers = Util::logModel()::$supportedHandlerTypes;

                    foreach ($handlerTypes as $handlerType) {
                        if (!in_array($handlerType, $supportedHandlers, true)) {
                            $validator->message("{$handlerType} is not supported as a handler_type. supported ones: " . implode(',', $supportedHandlers));
                            return false;
                        }
                    }

                    return true;
                })
        ];
    }
}

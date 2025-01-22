<?php

namespace Kakaprodo\SystemAnalytic\Services\Log\Data;

use Illuminate\Support\Facades\DB;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\CustomData\Lib\TypeHub\DataTypeHub;
use Kakaprodo\SystemAnalytic\Services\Base\Data\BaseData;

/**
 * @property string $tenant_id
 * @property array|null $handler_types
 */
class LogData extends BaseData
{

    protected function expectedProperties(): array
    {
        return [
            'tenant_id' => $this->property()->string(),
            'handler_types?' => $this->property()
                ->rules(['nullable', 'array'])
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

    private function db()
    {
        return DB::table(Util::logTableName());
    }

    /**
     * Get all log groups of a given tenant
     */
    public function getGroups(): array
    {
        return $this->getList('group');
    }

    /**
     * Get all log tags of a given tenant
     */
    public function getTags(): array
    {
        return $this->getList('tag');
    }

    /**
     * Get all log tags of a given tenant
     */
    public function getActions(): array
    {
        return $this->getList('action');
    }

    private function getList($columnName)
    {
        $list = $this->db()
            ->selectRaw('distinct(`' . Util::logTableName() . '`.`' . $columnName . '`)')
            ->where('tenant_id', $this->tenant_id)
            ->tap(
                fn($q) => is_array($this->handler_types) ?
                    $q->whereIn('handler_type', $this->handler_types)
                    : $q
            )->get()
            ->pluck($columnName)
            ->filter(fn($value) => boolval($value))
            ->all();
        return [...$list]; // to avoid jumped index
    }
}

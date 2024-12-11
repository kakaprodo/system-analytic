<?php

namespace Kakaprodo\SystemAnalytic\Services\Log\Data;

use Illuminate\Support\Facades\DB;
use Kakaprodo\SystemAnalytic\Services\Base\Data\BaseData;
use Kakaprodo\SystemAnalytic\Utilities\Util;

/**
 * @property string $tenant_id
 */
class LogData extends BaseData
{

    protected function expectedProperties(): array
    {
        return [
            'tenant_id' => $this->property()->string()->rules([
                'required',
                'string'
            ]),
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
        return $this->db()
            ->selectRaw('distinct(`' . Util::logTableName() . '`.`group`)')
            ->where('tenant_id', $this->tenant_id)
            ->get()
            ->pluck('group')
            ->all();
    }

    /**
     * Get all log tags of a given tenant
     */
    public function getTags(): array
    {
        return $this->db()
            ->selectRaw('distinct(tag)')
            ->where('tenant_id', $this->tenant_id)
            ->get()
            ->pluck('tag')
            ->all();
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Http\Analytics\Handlers\Logs\Traits;

use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\CustomData\Lib\TypeHub\DataTypeHub;

trait HasAnalyticGateHelperTrait
{
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
            ->tap(function ($q) {
                if (!($group = $this->getSearchValue('group'))) return $q;

                $q->where('group', $group);
            })->tap(function ($q) {
                if (!($tag = $this->getSearchValue('tag'))) return $q;

                $q->where('tag', $tag);
            })->tap(function ($q) {
                if (!($identifier = $this->getSearchValue('identifier'))) return $q;

                $q->where('identifier', $identifier);
            });
    }

    public function expectedSearchFieldsWithValidation(CustomData $data): array
    {
        return [
            'tenant_id' => $data->property()->string(),
            'identifier?' => $data->property()->string(),
            'group?' => $data->dataType()->customValidator(function ($group, DataTypeHub $validator) {
                $tagExists = Util::logModel()::where('group', $group)->exists();

                if ($tagExists !== true) {
                    $validator->message("Group {$group} not found");
                    return false;
                }

                return true;
            }),
            'tag?' => $data->property()->customValidator(function ($tag, DataTypeHub $validator) {
                $tagExists = Util::logModel()::where('tag', $tag)->exists();

                if ($tagExists !== true) {
                    $validator->message("Tag {$tag} not found");
                    return false;
                }

                return true;
            })
        ];
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Services\Log\Data;

use Illuminate\Validation\Rule;
use Kakaprodo\SystemAnalytic\Services\Base\Data\BaseData;
use Kakaprodo\SystemAnalytic\Utilities\Util;

/**
 * @property string $tenant_id
 * @property string $tag
 * @property string $group
 * @property number $value
 * @property array $payload
 * @property string $identifier
 * @property string $duplicate_after
 */
class CreateLogData extends BaseData
{
    static $strictCheckPeriods = [
        'minute',
        'hour',
        'day',
        'week',
        'month',
        'year',
        'never',
        'every'
    ];

    protected function expectedProperties(): array
    {
        return [
            'tenant_id' => $this->property()->string()->wrap('for_db')->rules([
                'required',
                'string'
            ]),
            'tag' => $this->property()->string()->wrap('for_db')->rules([
                'required',
                'string'
            ]),
            'group?' => $this->property()->string()->wrap('for_db')->rules([
                'nullable',
                'string'
            ]),
            'value' => $this->property()->number(1)->wrap('for_db')->rules([
                'nullable',
                'numeric',
                'min:1'
            ]),
            'identifier' => $this->property()->string()->wrap('for_db')->rules([
                'required',
                'string',
            ]),
            'payload?' => $this->property()->array()->wrap('for_db')->rules([
                'nullable',
                'array',
            ]),
            'duplicate_after?' => $this->property()->inArray(self::$strictCheckPeriods)
                ->default('every')
                ->rules([
                    'nullable',
                    Rule::in(self::$strictCheckPeriods)
                ])
        ];
    }

    protected function ignoreForKeyGenerator(): array
    {
        return ['duplicate_after', 'payload', 'value'];
    }

    /**
     * based on the strict check value , decide whether to store or not
     * the incoming info
     */
    public function shouldStoreLog()
    {
        $allowDupsAfter = $this->duplicate_after;

        if ($allowDupsAfter === 'every') return true;

        $logExists = Util::logModel()::where('key', $this->dataKey())
            ->tap(function ($q) use ($allowDupsAfter) {
                if ($allowDupsAfter === 'never') return $q;
                $method = 'sub' . ucfirst($allowDupsAfter);
                $q->where('created_at', '>=', now()->$method());
            })->exists();

        return $logExists == false;
    }
}

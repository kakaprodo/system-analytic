<?php

namespace Kakaprodo\SystemAnalytic\Services\Computation\Data;

use Kakaprodo\SystemAnalytic\Services\Base\Data\BaseData;
use Illuminate\Support\Carbon;

/**
 * Data object for creating an analytic computation record.
 *
 * @property string $category
 * @property string $tenant
 * @property string|null $sub_tenant
 * @property string|null $reference
 * @property Carbon|string $period
 * @property float $computed_value
 */
class CreateAnalyticComputationData extends BaseData
{

    protected function expectedProperties(): array
    {
        return [
            'category' => $this->property()->string()->wrap('for_db'),
            'tenant' => $this->property()->string()->wrap('for_db'),
            'sub_tenant?' => $this->property()->string()->wrap('for_db'),
            'reference?' => $this->property()->string()->wrap('for_db'),
            'period' => $this->property(Carbon::class)
                ->orUseType('string')
                ->castTo(fn($period) => Carbon::parse($period)->format('Y-m-d H:i:s'))
                ->wrap('for_db'),
            'computed_value' => $this->property()->number()->wrap('for_db')
        ];
    }
}

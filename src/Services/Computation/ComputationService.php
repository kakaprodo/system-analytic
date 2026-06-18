<?php

namespace Kakaprodo\SystemAnalytic\Services\Computation;


use Kakaprodo\SystemAnalytic\Services\Base\ServiceBase;
use Kakaprodo\SystemAnalytic\Services\Computation\Actions\CreateAnalyticComputationAction;


class ComputationService extends ServiceBase
{
    /**
     * Create a single analytic pre-computation record
     *
     * @param array{category:string,tenant:string,sub_tenant:string|null,reference:string|null,period:\Carbon\Carbon|string,computed_value:float} $inputs
     */
    public function create(array $inputs)
    {
        return CreateAnalyticComputationAction::process($this->inputs($inputs));
    }
}

<?php

namespace Kakaprodo\SystemAnalytic\Http\Controllers;

use Kakaprodo\SystemAnalytic\AnalyticGate;
use Kakaprodo\SystemAnalytic\Http\Controllers\Controller;
use Kakaprodo\SystemAnalytic\Http\Requests\LogAnalyticRequest;

class AnalyticLogController extends Controller
{
    public function store(LogAnalyticRequest $request)
    {
        $log = AnalyticGate::log()->create($request->validated());

        return response()->json(['added' => !!$log]);
    }
}

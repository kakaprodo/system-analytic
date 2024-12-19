<?php

namespace Kakaprodo\SystemAnalytic\Http\Controllers;

use Kakaprodo\SystemAnalytic\AnalyticGate;
use Kakaprodo\SystemAnalytic\Http\Controllers\Controller;
use Kakaprodo\SystemAnalytic\Http\Requests\LogAnalyticRequest;
use Kakaprodo\SystemAnalytic\Http\Requests\LogManyAnalyticRequest;

class AnalyticLogController extends Controller
{
    public function store(LogAnalyticRequest $request)
    {
        $log = AnalyticGate::log()->create($request->validated());

        return response()->json(['added' => !!$log]);
    }

    public function storeMany(LogManyAnalyticRequest $request)
    {
        AnalyticGate::log()->createMany($request->validated());

        return response()->json(['added' => true]);
    }

    public function getGroupList($tenantId)
    {
        $groups = AnalyticGate::log()->getGroups($tenantId);

        return response()->json($groups);
    }

    public function getTagList($tenantId)
    {
        $tags = AnalyticGate::log()->getTags($tenantId);

        return response()->json($tags);
    }
}

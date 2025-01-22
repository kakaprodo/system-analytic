<?php

namespace Kakaprodo\SystemAnalytic\Http\Controllers;

use Kakaprodo\SystemAnalytic\AnalyticGate;
use Kakaprodo\SystemAnalytic\Http\Controllers\Controller;
use Kakaprodo\SystemAnalytic\Http\Requests\FetchLogDataRequest;
use Kakaprodo\SystemAnalytic\Http\Requests\CreateLogAnalyticRequest;
use Kakaprodo\SystemAnalytic\Http\Requests\CreateManyLogAnalyticRequest;

class AnalyticLogController extends Controller
{
    public function store(CreateLogAnalyticRequest $request)
    {
        $log = AnalyticGate::log()->create($request->validated());

        return response()->json(['added' => !!$log]);
    }

    public function storeMany(CreateManyLogAnalyticRequest $request)
    {
        AnalyticGate::log()->createMany($request->validated());

        return response()->json(['added' => true]);
    }

    public function getGroupList(FetchLogDataRequest $request, $tenantId)
    {
        $groups = AnalyticGate::log()->getGroups(array_merge([
            'tenant_id' => $tenantId
        ], $request->validated()));

        return response()->json($groups);
    }

    public function getTagList(FetchLogDataRequest $request, $tenantId)
    {
        $tags = AnalyticGate::log()->getTags(array_merge([
            'tenant_id' => $tenantId
        ], $request->validated()));

        return response()->json($tags);
    }

    public function getActionList(FetchLogDataRequest $request, $tenantId)
    {
        $tags = AnalyticGate::log()->getActions(array_merge([
            'tenant_id' => $tenantId
        ], $request->validated()));

        return response()->json($tags);
    }
}

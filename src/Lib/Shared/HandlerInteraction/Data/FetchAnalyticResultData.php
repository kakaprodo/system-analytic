<?php

namespace Kakaprodo\SystemAnalytic\Lib\Shared\HandlerInteraction\Data;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Kakaprodo\CustomData\CustomData;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;

/**
 * fetch from external server , the result of a given 
 * analytic handler
 */
class FetchAnalyticResultData extends CustomData
{
    protected function expectedProperties(): array
    {
        return [
            'analytic_data' => $this->property(AnalyticData::class)->castTo(fn($value) => $value->only([
                'analytic_type',
                'scope_type',
                'scope_value',
                'scope_from_date',
                'scope_to_date',
                'search_value',
                'boolean_scope_type',
                'should_export',
                'file_type',
                'selected_option',
                'should_clear_cache',
                'refresh_persisted_result'
            ])),
            'authorization?' => $this->property()->string(),
            'url' => $this->property()->string(),
            'http_method?'  => $this->property()->string('post'),
            'append_to_header?' => $this->property()->array([]),
            'append_to_body?' => $this->property()->array([]),

            'headers?' => $this->property()->castTo(
                fn() => array_merge([
                    'authorization' => $this->authorization,
                    'Accept' => 'application/json'
                ], $this->append_to_header)
            ),
            'body?' => $this->property()->castTo(
                fn() => array_merge($this->analytic_data, $this->append_to_body)
            ),
        ];
    }

    /**
     * send request to external webiste
     */
    public function fetch()
    {
        $method = $this->http_method;

        $response =  Http::withHeaders($this->headers)
            ->$method($this->url, $this->body);

        if ($response->failed()) {
            $errorBody = $response->json();

            $message = $errorBody['message'] ?? $errorBody['error'] ?? 'Unknown error occurred';
            $prefix = Util::extractHost($this->url) . ' handler=' . Str::studly($this->analytic_data['analytic_type']);

            Util::fireErr($prefix . ' :' . $message)->die();
            return;
        }

        return $response->json();
    }
}

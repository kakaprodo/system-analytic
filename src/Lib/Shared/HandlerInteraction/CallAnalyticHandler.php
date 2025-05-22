<?php

namespace Kakaprodo\SystemAnalytic\Lib\Shared\HandlerInteraction;

use Kakaprodo\SystemAnalytic\AnalyticGate;
use Kakaprodo\SystemAnalytic\Utilities\Util;
use Kakaprodo\SystemAnalytic\Lib\Data\AnalyticData;
use Kakaprodo\SystemAnalytic\Lib\Shared\HandlerInteraction\Data\FetchAnalyticResultData;

class CallAnalyticHandler
{
    /** The inputs data  */
    protected AnalyticData $data;

    public function __construct(AnalyticData $data, string $handlerName)
    {
        $this->data = clone $data;
        $this->data->analytic_type = Util::classToKebak($handlerName);
    }

    /**
     * to the existing data, you can add more
     */
    public function addInputData(array $inputs = [])
    {
        foreach ($inputs as $key => $value) {
            $this->data->$key = $value;
        }

        return $this;
    }

    /**
     * Get the result of the handler
     */
    public function get()
    {
        return AnalyticGate::process($this->data);
    }

    /**
     * Get the result from a remote site
     * 
     * @param array $httpInfo {
     *     @type string $authorization The authType+token for the remote request.
     *     @type string $url The URL of the remote endpoint to call.
     *     @type string $http_method Optional.
     *     @type array $append_to_header array to append to headers input.
     *     @type array $append_to_body array to append to analytic-data input.
     * }
     * 
     * @return array
     */
    public function getFromRemote(array $httpInfo)
    {
        return FetchAnalyticResultData::make([
            'analytic_data' => $this->data,
            'authorization' => $httpInfo['authorization'] ?? null,
            'url' => $httpInfo['url'] ?? null,
            'http_method?' => $httpInfo['http_method'] ?? 'post',
            'append_to_header' => $httpInfo['append_to_header'] ?? [],
            'append_to_body' => $httpInfo['append_to_body'] ?? [],
        ])->fetch();
    }
}

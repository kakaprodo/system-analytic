<?php

namespace Kakaprodo\SystemAnalytic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'tag',
        'action',
        'group',
        'value',
        'identifier',
        'key',
        'payload', // json field for additional data
        'handler_type'
    ];

    const HANDLER_PIECHAT = 'pie-chart';
    const HANDLER_BARCHAT = 'bar-chart';
    const HANDLER_CARD = 'card';
    const HANDLER_LIST = 'list';
    const HANDLER_ALL = 'all';

    static $supportedHandlerTypes = [
        self::HANDLER_PIECHAT,
        self::HANDLER_BARCHAT,
        self::HANDLER_CARD,
        self::HANDLER_LIST,
        self::HANDLER_ALL
    ];

    public function getTable()
    {
        return config('system-analytic.log_report.table_name', 'sa_logs');
    }

    public function setPayloadAttribute($value)
    {
        $this->attributes['payload'] = json_encode($value === null ? [] : $value);
    }

    public function getPayloadAttribute($value)
    {
        return $value === null ? [] : (json_decode($value, true));
    }
}

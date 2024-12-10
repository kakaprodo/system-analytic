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
        'group',
        'value',
        'identifier',
        'key',
        'payload', // json field for additional data
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

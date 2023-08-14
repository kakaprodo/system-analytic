<?php

namespace Kakaprodo\SystemAnalytic\Models;

use Illuminate\Database\Eloquent\Model;


class SystemAnalyticReport extends Model
{
    protected $fillable = [
        'name',
        'value',
        'analytic_type',
        'analytic_data',
        'report_scope',
        'group'
    ];

    public function setValueeAttribute($value)
    {
        $this->attributes['value'] = serialize($value);
    }

    public function getValueAttribute($value)
    {
        return unserialize($value);
    }

    public function setAnalyticDataAttribute($value)
    {
        $this->attributes['analytic_data'] = serialize($value);
    }

    public function getAnalyticDataAttribute($value)
    {
        return unserialize($value);
    }
}

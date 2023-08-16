<?php

namespace Kakaprodo\SystemAnalytic\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Kakaprodo\SystemAnalytic\Utilities\Util;

class SystemAnalyticReport extends Model
{
    protected $fillable = [
        'name',
        'value',
        'analytic_type',
        'analytic_data',
        'scope_start_date',
        'scope_end_date',
        'group'
    ];

    public function getTable()
    {
        return Util::persistTable() ?? Str::snake(Str::pluralStudly(class_basename($this)));
    }

    public function setValueAttribute($value)
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

<?php

namespace Kakaprodo\SystemAnalytic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticComputation extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'tenant',
        'sub_tenant',
        'reference',
        'period',
        'computed_value',
        'data_key'
    ];
}

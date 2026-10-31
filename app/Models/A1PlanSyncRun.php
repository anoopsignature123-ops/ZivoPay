<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class A1PlanSyncRun extends Model
{
    protected $fillable = [
        'catalog_type',
        'status',
        'requested_count',
        'success_count',
        'failure_count',
        'summary',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'summary' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}

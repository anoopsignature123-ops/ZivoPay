<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class A1PlanCatalog extends Model
{
    protected $fillable = [
        'catalog_type',
        'category',
        'operator_code',
        'circle_code',
        'name',
        'payload',
        'synced_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'synced_at' => 'datetime',
    ];
}

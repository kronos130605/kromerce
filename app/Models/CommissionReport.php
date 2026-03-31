<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'year',
        'month',
        'total_sales',
        'total_commission',
        'total_fees',
        'net_earnings',
        'total_orders',
        'currency',
        'generated_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'total_sales' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'total_fees' => 'decimal:2',
        'net_earnings' => 'decimal:2',
        'total_orders' => 'integer',
        'generated_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

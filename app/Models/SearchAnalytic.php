<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchAnalytic extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'query',
        'store_id',
        'results_count',
        'has_results',
        'user_id',
        'session_id',
        'clicked_product_id',
        'searched_at',
    ];

    protected $casts = [
        'results_count' => 'integer',
        'has_results' => 'boolean',
        'searched_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clickedProduct()
    {
        return $this->belongsTo(Product::class, 'clicked_product_id');
    }
}

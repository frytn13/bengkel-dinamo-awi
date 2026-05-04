<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'date',
        'customer_name',
        'service_name',
        'service_fee',
        'sale_type_id',
        'payment_method',
        'status',
        'total_amount'
    ];

    public function saleType()
    {
        return $this->belongsTo(SaleType::class, 'sale_type_id');
    }
    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }
}

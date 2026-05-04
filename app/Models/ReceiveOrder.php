<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveOrder extends Model
{
    use HasFactory;

    protected $fillable = ['ro_number', 'purchase_order_id', 'date', 'payment_method', 'status'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function details()
    {
        return $this->hasMany(ReceiveOrderDetail::class, 'receive_order_id');
    }

    public function getTotalAmountAttribute()
    {
        $total = 0;
        foreach ($this->details as $detail) {
            $total += ($detail->qty * ($detail->product->purchase_price ?? 0));
        }
        return $total;
    }
}

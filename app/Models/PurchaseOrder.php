<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = ['po_number', 'date', 'vendor_id', 'status', 'total_amount'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id');
    }

    public function checkAndUpdateStatus()
    {
        if ($this->status === 'Ditutup') {
            return;
        }

        $totalQtyPesan = 0;
        $totalQtyDiterima = 0;

        foreach ($this->details as $detail) {
            $totalQtyPesan += $detail->qty;
            $totalQtyDiterima += $detail->received_qty;
        }

        if ($totalQtyDiterima == 0) {
            $this->status = 'Pending';
        } elseif ($totalQtyDiterima > 0 && $totalQtyDiterima < $totalQtyPesan) {
            $this->status = 'Parsial';
        } elseif ($totalQtyDiterima >= $totalQtyPesan) {
            $this->status = 'Selesai';
        }

        $this->save();
    }
}

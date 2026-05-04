<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receive_orders', function (Blueprint $table) {
            $table->id();
            $table->string('ro_number')->unique();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('restrict');
            $table->date('date');
            $table->enum('payment_method', ['Tunai', 'Kredit']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receive_orders');
    }
};

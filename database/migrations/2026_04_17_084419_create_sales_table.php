<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('date');
            $table->string('customer_name')->nullable();

            $table->string('service_name')->nullable();
            $table->decimal('service_fee', 15, 2)->default(0);

            $table->foreignId('sale_type_id')->constrained('sale_types')->onDelete('restrict');

            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();

            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

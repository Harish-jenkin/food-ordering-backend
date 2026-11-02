<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->uuid('idempotency_key')->nullable()->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->string('customer_name');
            $table->string('customer_mobile', 20);
            $table->string('customer_email')->nullable();
            $table->enum('order_type', ['delivery', 'takeaway']);
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'out_for_delivery',
                'ready',
                'completed',
                'cancelled',
            ])->default('pending')->index();
            $table->enum('payment_method', ['cash', 'upi', 'card']);
            $table->string('delivery_address')->nullable();
            $table->string('landmark')->nullable();
            $table->string('preferred_time')->nullable();
            $table->text('instructions')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->timestamp('whatsapp_opened_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index(['customer_mobile', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

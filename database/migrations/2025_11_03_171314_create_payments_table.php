<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['PayMongo', 'GCash', 'Cash'])->default('Cash');
            $table->enum('payment_status', ['Pending', 'Paid', 'Failed'])->default('Pending');
            $table->string('reference_no')->nullable();
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

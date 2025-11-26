<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // JSON is perfect for storing:
            // - payment_for (reservation/booking)
            // - payment_option (deposit/full)
            // - valid_id_path, etc.
            $table->json('payment_meta')->nullable()->after('booking_status');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('payment_meta');
        });
    }
};

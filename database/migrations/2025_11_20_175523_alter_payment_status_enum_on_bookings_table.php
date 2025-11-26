<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add "Cancelled" to payment_status enum
        DB::statement("
            ALTER TABLE bookings 
            MODIFY payment_status ENUM(
                'Pending',
                'For Verification',
                'Paid',
                'Rejected',
                'Cancelled'
            ) DEFAULT 'Pending'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum (without Cancelled)
        DB::statement("
            ALTER TABLE bookings 
            MODIFY payment_status ENUM(
                'Pending',
                'For Verification',
                'Paid',
                'Rejected'
            ) DEFAULT 'Pending'
        ");
    }
};

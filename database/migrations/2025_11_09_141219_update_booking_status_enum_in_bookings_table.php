<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the enum field to add "Payment Submitted"
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN booking_status 
            ENUM('Pending Approval', 'Payment Submitted', 'Confirmed', 'Cancelled') 
            NOT NULL DEFAULT 'Pending Approval'
        ");
    }

    public function down(): void
    {
        // Revert back to the original enum (without Payment Submitted)
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN booking_status 
            ENUM('Pending Approval', 'Confirmed', 'Cancelled') 
            NOT NULL DEFAULT 'Pending Approval'
        ");
    }
};

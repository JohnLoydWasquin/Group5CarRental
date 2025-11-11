<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN booking_status 
            ENUM(
                'Pending Approval', 
                'Awaiting Payment', 
                'Under Review', 
                'Confirmed', 
                'Rejected', 
                'Ongoing', 
                'Completed', 
                'Cancelled', 
                'Payment Submitted'
            ) 
            NOT NULL DEFAULT 'Pending Approval'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN booking_status 
            ENUM(
                'Pending Approval', 
                'Awaiting Payment', 
                'Under Review', 
                'Confirmed', 
                'Rejected', 
                'Ongoing', 
                'Completed', 
                'Cancelled'
            ) 
            NOT NULL DEFAULT 'Pending Approval'
        ");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {

            if (!Schema::hasColumn('bookings', 'refund_status')) {
                $table->enum('refund_status', ['pending', 'approved', 'rejected'])
                    ->nullable()
                    ->after('payment_status');
            }

            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)
                    ->nullable()
                    ->after('refund_status');
            }

            if (!Schema::hasColumn('bookings', 'refund_requested_at')) {
                $table->timestamp('refund_requested_at')
                    ->nullable()
                    ->after('refund_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'refund_status',
                'refund_amount',
                'refund_requested_at'
            ]);
        });
    }
};

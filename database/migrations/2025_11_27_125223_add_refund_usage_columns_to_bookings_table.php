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

            if (!Schema::hasColumn('bookings', 'refund_minutes_used')) {
                $table->integer('refund_minutes_used')
                    ->nullable()
                    ->after('refund_requested_at');
            }

            if (!Schema::hasColumn('bookings', 'refund_deduction')) {
                $table->decimal('refund_deduction', 10, 2)
                    ->nullable()
                    ->after('refund_minutes_used');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['refund_minutes_used', 'refund_deduction']);
        });
    }
};

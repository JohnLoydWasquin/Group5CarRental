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
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('booking_id');

            // RELATIONSHIPS
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('VehicleID');

            // RENTAL DETAILS
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->dateTime('pickup_datetime');
            $table->dateTime('return_datetime');
            $table->integer('rental_days')->nullable();

            // ADD-ONS
            $table->json('addons')->nullable();

            // COSTING
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(3000);
            $table->decimal('total_amount', 10, 2)->default(0);

            // PAYMENT DETAILS
            $table->enum('payment_method', ['GCash'])->default('GCash');
            $table->enum('payment_status', ['Pending', 'For Verification', 'Paid', 'Rejected'])->default('Pending');

            // CUSTOMER PAYMENT INFO
            $table->string('payer_name')->nullable();
            $table->string('payer_number')->nullable();
            $table->string('receipt_screenshot')->nullable(); // path to uploaded proof image

            // BOOKING STATUS
            $table->enum('booking_status', [
                'Pending Approval',   // after booking form submit
                'Awaiting Payment',   // before user uploads proof
                'Under Review',       // waiting for staff to verify proof
                'Confirmed',          // approved by staff
                'Rejected',           // payment or booking rejected
                'Ongoing',            // car is rented
                'Completed',          // car returned
                'Cancelled'           // cancelled by user/staff
            ])->default('Pending Approval');

            // REMINDERS & NOTIFICATIONS
            $table->boolean('reminder_sent_3hrs')->default(false);
            $table->boolean('reminder_sent_3days')->default(false);
            $table->boolean('reminder_sent_1week')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // RELATIONSHIPS
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('VehicleID')->references('VehicleID')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

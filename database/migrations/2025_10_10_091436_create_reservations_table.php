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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('ReserveID');
            $table->unsignedBigInteger('CustID');
            $table->unsignedBigInteger('VehicleID');
            $table->date('ReservedDate');
            $table->date('PickupDate');
            $table->string('PickupLocation');
            $table->date('ReturnDate');
            $table->integer('NoOfDays')->nullable();
            $table->string('CancelationDetails')->nullable();
            $table->foreign('CustID')->references('CustID')->on('customers')->onDelete('cascade');
            $table->foreign('VehicleID')->references('VehicleID')->on('vehicles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};

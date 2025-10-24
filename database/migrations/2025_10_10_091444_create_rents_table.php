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
        Schema::create('rents', function (Blueprint $table) {
            $table->id('RentID');
            $table->unsignedBigInteger('ReserveID');
            $table->unsignedBigInteger('VehicleID');
            $table->unsignedBigInteger('CustID');
            $table->decimal('DownPay',10,2);
            $table->decimal('TotalPay',10,2);
            $table->string('PayMethod');
            $table->decimal('Refund',10,2)->nullable();
            $table->decimal('DamageCompensation',10,2)->nullable();
            $table->date('PayDate');
            $table->foreign('ReserveID')->references('ReserveID')->on('reservations')->onDelete('cascade');
            $table->foreign('VehicleID')->references('VehicleID')->on('vehicles')->onDelete('cascade');
            $table->foreign('CustID')->references('CustID')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rents');
    }
};

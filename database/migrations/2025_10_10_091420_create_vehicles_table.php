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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('VehicleID');
            $table->string('PlateNo');
            $table->string('Model');
            $table->integer('Mileage');
            $table->decimal('DailyPrice',10,2);
            $table->string('Condition');
            $table->boolean('Availability')->default(true);
            $table->unsignedBigInteger('EmpID'); // registered by employee
            $table->foreign('EmpID')->references('EmpID')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

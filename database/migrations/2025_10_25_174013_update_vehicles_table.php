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
        Schema::table('vehicles', function (Blueprint $table) {
            
            if (Schema::hasColumn('vehicles', 'Mileage')) {
            $table->dropColumn('Mileage');
            }

            $table->unique('PlateNo');

            $table->string('Brand')->after('PlateNo');
            $table->integer('Passengers')->after('Availability');
            $table->string('FuelType')->after('Passengers');
            $table->enum('Transmission', ['Automatic', 'Manual'])->after('FuelType');
            $table->string('Image')->nullable()->after('Transmission');

            $table->string('Condition')->default('Good')->change();

            if (!Schema::hasColumn('vehicles', 'EmpID')) {
                $table->unsignedBigInteger('EmpID')->after('Availability');
                $table->foreign('EmpID')->references('EmpID')->on('employees')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Drop added columns and constraints
            $table->dropUnique(['PlateNo']);
            $table->dropColumn(['Brand', 'Passengers', 'FuelType', 'Transmission', 'Image']);
            $table->dropForeign(['EmpID']);
        });
    }
};

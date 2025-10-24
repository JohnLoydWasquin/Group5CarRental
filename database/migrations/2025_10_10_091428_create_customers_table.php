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
        Schema::create('customers', function($table){
            $table->id('CustID');
            $table->string('FName');
            $table->string('LName');
            $table->string('Name');
            $table->string('Address');
            $table->string('City');
            $table->string('Country');
            $table->string('ContactNo');
            $table->string('DrivingLicence')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

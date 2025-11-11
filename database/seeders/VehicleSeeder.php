<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'PlateNo' => 'ABC123',
                'Brand'=> 'Toyota',
                'Model' => 'WIGO G',
                'DailyPrice' => 1299,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 4,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'ToyotaWigoG.webp',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC124',
                'Brand'=> 'Geely',
                'Model' => 'Emegrand Comfort',
                'DailyPrice' => 1499,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 5,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'GeelyEmgrand.png',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC125',
                'Brand'=> 'Toyota',
                'Model' => 'Raize',
                'DailyPrice' => 1699,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 5,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'ToyotaRaize.webp',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC126',
                'Brand'=> 'Mitsubishi',
                'Model' => 'Xpander GLS',
                'DailyPrice' => 2199,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 7,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'MitsubishiXpander.webp',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC127',
                'Brand'=> 'Toyota',
                'Model' => 'Vios XLE CVT',
                'DailyPrice' => 1399,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 5,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'ToyotaVios.webp',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC128',
                'Brand'=> 'Toyota',
                'Model' => 'Veloz V',
                'DailyPrice' => 2199,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 7,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'ToyotaVeloz.webp',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC129',
                'Brand'=> 'Toyota',
                'Model' => 'Innova E',
                'DailyPrice' => 2499,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 8,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'ToyotaInnova.webp',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC130',
                'Brand'=> 'Changan',
                'Model' => 'Alsvin A/T',
                'DailyPrice' => 1399,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 5,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'ChanganAlsvin.webp',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC131',
                'Brand'=> 'Geely',
                'Model' => 'Coolray SE',
                'DailyPrice' => 1699,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 5,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'GeelyCoolray.png',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC132',
                'Brand'=> 'Toyota',
                'Model' => 'Rush',
                'DailyPrice' => 2199,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 7,
                'FuelType' => 'Unleaded',
                'Transmission' => 'Automatic',
                'Image' => 'ToyotaRush.webp',
                'EmpID' => 1,
            ],
            [
                'PlateNo' => 'ABC133',
                'Brand'=> 'Mitsubishi',
                'Model' => 'Montero',
                'DailyPrice' => 2799,
                'Condition' => 'Excellent',
                'Availability' => true,
                'Passengers' => 7,
                'FuelType' => 'Diesel',
                'Transmission' => 'Automatic',
                'Image' => 'MitsubishiMontero.webp',
                'EmpID' => 1,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            // Add timestamps
            $vehicle['created_at'] = Carbon::now();
            $vehicle['updated_at'] = Carbon::now();

            // Check if the vehicle already exists
            $exists = DB::table('vehicles')->where('PlateNo', $vehicle['PlateNo'])->first();

            if ($exists) {
                // Update the existing record
                DB::table('vehicles')->where('PlateNo', $vehicle['PlateNo'])->update($vehicle);
            } else {
                // Insert new record
                DB::table('vehicles')->insert($vehicle);
            }
        }
    }
}

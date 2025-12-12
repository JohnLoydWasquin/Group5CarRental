<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [

            // NCR - Metro Manila
            ['name' => 'Manila', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Quezon City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Makati City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Pasay City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Taguig City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Pasig City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Mandaluyong City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Las Piñas City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Muntinlupa City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Parañaque City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Valenzuela City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Caloocan City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],
            ['name' => 'Marikina City', 'type' => 'city', 'region' => 'NCR', 'province' => null, 'code' => null],

            // Luzon Provinces
            ['name' => 'Ilocos Norte', 'type' => 'province', 'region' => 'Region I', 'province' => 'Ilocos Norte', 'code' => null],
            ['name' => 'Ilocos Sur', 'type' => 'province', 'region' => 'Region I', 'province' => 'Ilocos Sur', 'code' => null],
            ['name' => 'La Union', 'type' => 'province', 'region' => 'Region I', 'province' => 'La Union', 'code' => null],
            ['name' => 'Pangasinan', 'type' => 'province', 'region' => 'Region I', 'province' => 'Pangasinan', 'code' => null],

            ['name' => 'Cagayan', 'type' => 'province', 'region' => 'Region II', 'province' => 'Cagayan', 'code' => null],
            ['name' => 'Isabela', 'type' => 'province', 'region' => 'Region II', 'province' => 'Isabela', 'code' => null],
            ['name' => 'Nueva Vizcaya', 'type' => 'province', 'region' => 'Region II', 'province' => 'Nueva Vizcaya', 'code' => null],

            ['name' => 'Aurora', 'type' => 'province', 'region' => 'Region III', 'province' => 'Aurora', 'code' => null],
            ['name' => 'Bataan', 'type' => 'province', 'region' => 'Region III', 'province' => 'Bataan', 'code' => null],
            ['name' => 'Bulacan', 'type' => 'province', 'region' => 'Region III', 'province' => 'Bulacan', 'code' => null],
            ['name' => 'Nueva Ecija', 'type' => 'province', 'region' => 'Region III', 'province' => 'Nueva Ecija', 'code' => null],
            ['name' => 'Pampanga', 'type' => 'province', 'region' => 'Region III', 'province' => 'Pampanga', 'code' => null],
            ['name' => 'Tarlac', 'type' => 'province', 'region' => 'Region III', 'province' => 'Tarlac', 'code' => null],
            ['name' => 'Zambales', 'type' => 'province', 'region' => 'Region III', 'province' => 'Zambales', 'code' => null],

            ['name' => 'Batangas', 'type' => 'province', 'region' => 'Region IV-A', 'province' => 'Batangas', 'code' => null],
            ['name' => 'Cavite', 'type' => 'province', 'region' => 'Region IV-A', 'province' => 'Cavite', 'code' => null],
            ['name' => 'Laguna', 'type' => 'province', 'region' => 'Region IV-A', 'province' => 'Laguna', 'code' => null],
            ['name' => 'Quezon', 'type' => 'province', 'region' => 'Region IV-A', 'province' => 'Quezon', 'code' => null],
            ['name' => 'Rizal', 'type' => 'province', 'region' => 'Region IV-A', 'province' => 'Rizal', 'code' => null],

            ['name' => 'Albay', 'type' => 'province', 'region' => 'Region V', 'province' => 'Albay', 'code' => null],
            ['name' => 'Camarines Norte', 'type' => 'province', 'region' => 'Region V', 'province' => 'Camarines Norte', 'code' => null],
            ['name' => 'Camarines Sur', 'type' => 'province', 'region' => 'Region V', 'province' => 'Camarines Sur', 'code' => null],
            ['name' => 'Catanduanes', 'type' => 'province', 'region' => 'Region V', 'province' => 'Catanduanes', 'code' => null],
            ['name' => 'Masbate', 'type' => 'province', 'region' => 'Region V', 'province' => 'Masbate', 'code' => null],
            ['name' => 'Sorsogon', 'type' => 'province', 'region' => 'Region V', 'province' => 'Sorsogon', 'code' => null],

            // Major Luzon Cities / Hubs
            ['name' => 'Baguio City', 'type' => 'city', 'region' => 'CAR', 'province' => 'Benguet', 'code' => null],
            ['name' => 'San Fernando City', 'type' => 'city', 'region' => 'Region III', 'province' => 'Pampanga', 'code' => null],
            ['name' => 'Angeles City', 'type' => 'city', 'region' => 'Region III', 'province' => 'Pampanga', 'code' => null],
            ['name' => 'Olongapo City', 'type' => 'city', 'region' => 'Region III', 'province' => 'Zambales', 'code' => null],
            ['name' => 'Lucena City', 'type' => 'city', 'region' => 'Region IV-A', 'province' => 'Quezon', 'code' => null],
            ['name' => 'Naga City', 'type' => 'city', 'region' => 'Region V', 'province' => 'Camarines Sur', 'code' => null],
            ['name' => 'Legazpi City', 'type' => 'city', 'region' => 'Region V', 'province' => 'Albay', 'code' => null],

            // Airports in Luzon (commercial)
            ['name' => 'Ninoy Aquino International Airport (NAIA)', 'type' => 'airport', 'region' => 'NCR', 'province' => null, 'code' => 'MNL'],
            ['name' => 'Clark International Airport', 'type' => 'airport', 'region' => 'Region III', 'province' => 'Pampanga', 'code' => 'CRK'],
            ['name' => 'Subic Bay International Airport', 'type' => 'airport', 'region' => 'Region III', 'province' => 'Zambales', 'code' => 'SFS'],
            ['name' => 'Laoag International Airport', 'type' => 'airport', 'region' => 'Region I', 'province' => 'Ilocos Norte', 'code' => 'LAO'],
            ['name' => 'Baguio Loakan Airport', 'type' => 'airport', 'region' => 'CAR', 'province' => 'Benguet', 'code' => 'BAG'],
            ['name' => 'Naga Airport', 'type' => 'airport', 'region' => 'Region V', 'province' => 'Camarines Sur', 'code' => 'WNP'],
            ['name' => 'Legazpi Airport / Bicol International Airport', 'type' => 'airport', 'region' => 'Region V', 'province' => 'Albay', 'code' => 'LGP'],
        ];

        DB::table('locations')->insert($locations);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $country = DB::table('countries')->insertGetId([
            'name'       => 'India',
            'code'       => 'IN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $states = [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
            'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
            'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
            'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
            'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
            'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
            'Delhi', 'Jammu & Kashmir', 'Ladakh',
        ];

        $stateIds = [];
        foreach ($states as $state) {
            $stateIds[$state] = DB::table('states')->insertGetId([
                'name'       => $state,
                'country_id' => $country,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $cities = [
            'Maharashtra'  => ['Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad', 'Kolhapur'],
            'Karnataka'    => ['Bengaluru', 'Mysuru', 'Hubli', 'Mangaluru', 'Belagavi'],
            'Tamil Nadu'   => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem'],
            'Gujarat'      => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar'],
            'Uttar Pradesh'=> ['Lucknow', 'Kanpur', 'Agra', 'Varanasi', 'Meerut', 'Allahabad'],
            'West Bengal'  => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri'],
            'Rajasthan'    => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Ajmer'],
            'Madhya Pradesh'=> ['Bhopal', 'Indore', 'Gwalior', 'Jabalpur', 'Ujjain'],
            'Delhi'        => ['New Delhi', 'Dwarka', 'Rohini', 'Janakpuri'],
            'Telangana'    => ['Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar'],
            'Kerala'       => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur'],
            'Punjab'       => ['Chandigarh', 'Ludhiana', 'Amritsar', 'Jalandhar'],
            'Haryana'      => ['Gurugram', 'Faridabad', 'Panipat', 'Ambala'],
            'Bihar'        => ['Patna', 'Gaya', 'Muzaffarpur', 'Bhagalpur'],
            'Odisha'       => ['Bhubaneswar', 'Cuttack', 'Rourkela', 'Berhampur'],
        ];

        foreach ($cities as $stateName => $cityList) {
            if (!isset($stateIds[$stateName])) continue;
            foreach ($cityList as $city) {
                DB::table('cities')->insert([
                    'name'       => $city,
                    'state_id'   => $stateIds[$stateName],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

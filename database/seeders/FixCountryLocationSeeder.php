<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class FixCountryLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = file_get_contents(base_path('/storage/app/public/postcodes.json'));
        $data = json_decode($path, true);

        $formattedLocation = [];

        foreach ($data as $v) {
            $town = isset($v['town']) && $v['town'] !== '' ? ($v['town'] . ', ') : '';
            $formattedLocation[] = [
                'location'       => $town . $v['region'] . ', ' . $v['postcode'],
                'town'           => $v['town'],
                'country_string' => $v['country_string'],
                'eastings'       => $v['eastings'],
                'country'        => $v['country'],
                'region'         => $v['region'],
                'longitude'      => $v['longitude'],
                'uk_region'      => $v['uk_region'],
                'postcode'       => $v['postcode'],
                'latitude'       => $v['latitude'],
                'northings'      => $v['northings'],
            ];
        }


        foreach ($formattedLocation ?: [] as $f) {
            $location = Location::where('location', $f['location'])->first();
            $location->update(['country' => $f['country']]);
        }
    }
}

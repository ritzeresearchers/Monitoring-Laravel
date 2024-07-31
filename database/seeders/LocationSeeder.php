<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
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
        usort($formattedLocation, static function ($a, $b) {
            return strcmp($a['location'], $b['location']);
        });

        foreach ($formattedLocation ?: [] as $f) {
            Location::create([
                'location'       => $f['location'],
                'town'           => $f['town'],
                'country_string' => $f['country_string'],
                'eastings'       => $f['eastings'],
                'country'        => $f['country'],
                'region'         => $f['region'] ?? '',
                'longitude'      => $f['longitude'],
                'uk_region'      => $f['uk_region'] ?? '',
                'postcode'       => $f['postcode'],
                'latitude'       => $f['latitude'],
                'northings'      => $f['northings'],
            ]);
        }
    }
}

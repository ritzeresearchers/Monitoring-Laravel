<?php

namespace Database\Seeders;

use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Reader\Exception\ReaderNotOpenedException;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\WorkCategory;
use App\Models\Service;

class WorkCategorySeeder extends Seeder
{
    /**
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     * @throws IOException
     */
    public function run(): void
    {
        $collection = (new FastExcel())->import(base_path('/storage/app/public/categories.xlsx'));

        foreach ($collection as $row) {
            $category = ucwords(strtolower($row['Category']));
            $service = $row['Services'];


            $thumbnail = 'https://localpro-assets.s3.eu-west-2.amazonaws.com/carpenter.png';
            if ($category === 'Appliance Repair Service') {
                $thumbnail = 'https://localpro-assets.s3.eu-west-2.amazonaws.com/category/kitchen-cabinets.jpeg';
            }
            if ($category === 'Appliance Store') {
                $thumbnail = 'https://localpro-assets.s3.eu-west-2.amazonaws.com/category/household-appliances.jpg';
            }
            if ($category === 'Computer Support And Services') {
                $thumbnail = 'https://localpro-assets.s3.eu-west-2.amazonaws.com/category/computer-support.JPG';
            }
            if ($category === 'Gas Engineer') {
                $thumbnail = 'https://localpro-assets.s3.eu-west-2.amazonaws.com/category/car-repair-and-maintenance.jpg';
            }
            if ($category === 'Air Conditioning Contractor') {
                $thumbnail = 'https://localpro-assets.s3.eu-west-2.amazonaws.com/category/workman-servicing-air-conditioning.jpg';
            }
            if ($category === 'Plumber') {
                $thumbnail = 'https://localpro-assets.s3.eu-west-2.amazonaws.com/category/plumber-repairing.jpg';
            }
            if ($category === 'Car Repair And Maintenance') {
                $thumbnail = 'https://localpro-assets.s3.eu-west-2.amazonaws.com/category/car-repair-and-maintenance.jpg';
            }

            if ($category) {
                $wc = WorkCategory::create([
                    'name'        => $category,
                    'thumbnail'   => $thumbnail,
                    'description' => '',
                ]);
                $wcId = $wc->id;
            }

            Service::create([
                'name'             => $this->clean(ucwords(strtolower($service))),
                'work_category_id' => $wcId,
                'thumbnail'        => 'https://live.staticflickr.com/4043/4438260868_cc79b3369d_z.jpg',
                'description'      => '',
            ]);

        }
    }

    /**
     * @param $string
     * @return array|string|string[]|null
     */
    public function clean($string)
    {
        $string = str_replace(' ', ' ', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^ \w]+/', '', $string); // Removes special chars.
    }
}

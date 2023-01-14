<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bins = Bin::all();

        foreach ($bins as $bin) {
            Image::factory(rand(1, 2))
                ->for($bin, 'bin')
                ->create();
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Bin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = collect([
            "funny",
            "pics",
            "science",
            "blog",
            "gaming",
            "movies",
            "aww",
            "books",
            "television",
            "sports",
            "space",
            "jokes",
            "food",
            "art",
            "history",
            "gadgets",
            "creepy",
        ]);

        $bins = Bin::all();

        foreach ($bins as $bin) {
            $bin->attachTags($tags->random(rand(1, 2)), 'bin');
        }
    }
}

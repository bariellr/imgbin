<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\Comment;
use App\Models\Image;
use App\Models\User;
use Database\Seeders\BinSeeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\TagSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\VoteSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            BinSeeder::class,
            ImageSeeder::class,
            CommentSeeder::class,
            VoteSeeder::class,
            TagSeeder::class,
        ]);
    }
}

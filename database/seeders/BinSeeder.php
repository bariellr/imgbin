<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Storage::disk('local')->deleteDirectory('.cache');
        Storage::deleteDirectory('images');

        $users = User::all();

        foreach ($users as $user) {
            Bin::factory(rand(0, 1))
                ->for($user, 'user')
                ->create();
        }
    }
}

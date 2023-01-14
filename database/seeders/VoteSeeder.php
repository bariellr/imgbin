<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\Comment;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $users = User::all();
        $bins = Bin::all();
        $comments = Comment::all();

        foreach ($bins as $bin) {
            foreach ($users->random(rand(1, 3)) as $user) {
                if ($faker->boolean()) {
                    $user->upvote($bin);
                } else {
                    $user->downvote($bin);
                }
            }
        }

        foreach ($comments as $comment) {
            foreach ($users->random(rand(1, 3)) as $user) {
                if ($faker->boolean()) {
                    $user->upvote($comment);
                } else {
                    $user->downvote($comment);
                }
            }
        }
    }
}

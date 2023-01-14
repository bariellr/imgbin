<?php

namespace Database\Seeders;

use App\Models\Bin;
use App\Models\Comment;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
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

        foreach ($bins as $bin) {
            for ($i = 0; $i < rand(0, 2); $i++) {
                $datetime = $faker->dateTimeBetween($bin->published_at, 'now');
                Comment::factory()
                    ->for($bin, 'commentable')
                    ->for($users->random(), 'user')
                    ->create([
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ]);
            }
        }

        $comments = Comment::all();

        foreach ($comments->random(round($comments->count() * .75)) as $comment) {
            for ($i = 0; $i < rand(0, 1); $i++) {
                $datetime = $faker->dateTimeBetween($comment->created_at, 'now');
                Comment::factory()
                    ->for($comment->commentable, 'commentable')
                    ->for($users->random(), 'user')
                    ->create([
                        'parent_id' => $comment->id,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ]);
            }
        }

        // try to add 3rd level replies
        $comments = Comment::doesntHave('replies')->get();

        foreach ($comments as $comment) {
            for ($i = 0; $i < rand(0, 2); $i++) {
                $datetime = $faker->dateTimeBetween($comment->created_at, 'now');
                Comment::factory()
                    ->for($comment->commentable, 'commentable')
                    ->for($users->random(), 'user')
                    ->create([
                        'parent_id' => $comment->id,
                        'created_at' => $datetime,
                        'updated_at' => $datetime,
                    ]);
            }
        }
    }
}

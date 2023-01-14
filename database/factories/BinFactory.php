<?php

namespace Database\Factories;

use App\Models\Bin;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bin>
 */
class BinFactory extends Factory
{
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Bin $bin) {
            //
        })->afterCreating(function (Bin $bin) {
            // add views
            $limit = rand(0, 50);
            for ($i = 0; $i < $limit; $i++) {
                $datetime = $this->faker->dateTimeBetween('-1 year', 'now');

                View::create([
                    'viewable_id' => $bin->id,
                    'viewable_type' => Bin::class,
                    'visitor' => Str::random(80),
                    'viewed_at' => $datetime,
                ]);
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $datetime = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'title' => $this->faker->sentence(rand(4, 7)),
            'published_at' => $datetime,
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ];
    }
}

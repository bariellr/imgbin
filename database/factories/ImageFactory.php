<?php

namespace Database\Factories;

use App\Models\Image;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as InterventionImage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Image $image) {
            //
        })->afterCreating(function (Image $image) {
            // add views
            for ($i = 0; $i < rand(0, 20); $i++) {
                $datetime = $this->faker->dateTimeBetween('-1 year', 'now');

                View::create([
                    'viewable_id' => $image->getKey(),
                    'viewable_type' => $image->getMorphClass(),
                    'visitor' => Str::random(80),
                    'viewed_at' => $datetime,
                ]);
            }

            // get original seeder file
            $file = Storage::disk('local')->path('seeding/' . $image->original_filename);
            $targetImage = File::get($file);

            // create bin directory
            if (!Storage::exists($image->bin->directory_path)) {
                Storage::makeDirectory($image->bin->directory_path);
            }

            // save seeder file to storage
            Storage::put(
                $image->full_path,
                $targetImage,
            );
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($faker));

        $datetime = $this->faker->dateTimeBetween('-1 year', 'now');

        // get a random seeder file
        $file = Arr::random(Storage::disk('local')->files('seeding'));
        $image = InterventionImage::make(Storage::disk('local')->path($file));

        return [
            'description' => $faker->markdownP(),
            'original_filename' => $image->basename,
            'mime_type' => $image->mime(),
            'size' => $image->filesize(),
            'width' => $image->width(),
            'height' => $image->height(),
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ];
    }
}

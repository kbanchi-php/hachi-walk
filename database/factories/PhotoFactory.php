<?php

namespace Database\Factories;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Photo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        // set image size
        $width = 500;
        $height = random_int(250, 600);
        // save image and get path
        $file = $this->faker->image(null, $width, $height);
        $path = Storage::putFile('walks', $file);
        File::delete($file);

        $walks = [];
        foreach (\App\Models\Walk::all() as $walk) {
            $walks[] = $walk->id;
        }

        return [
            'org_name' => basename($file),
            'name' => basename($path),
            'walk_id' => $walks[array_rand($walks)],
        ];
    }
}

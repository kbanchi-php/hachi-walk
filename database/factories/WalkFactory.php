<?php

namespace Database\Factories;

use App\Models\Walk;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Walk::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = [];
        foreach (\App\Models\User::all() as $user) {
            $users[] = $user->id;
        }

        $categories = [];
        foreach (\App\Models\Category::all() as $category) {
            $categories[] = $category->id;
        }

        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'category_id' => $categories[array_rand($categories)],
            'user_id' => $users[array_rand($users)],
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FolderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(2, true),
            'user_id' => User::factory(),
            'parent_id' => null, // Folder::factory(),
        ];
    }
}

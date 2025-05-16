<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(3, true),
            'user_id' => User::factory(),
            'folder_id' => null, // Folder::factory(),
        ];
    }
}

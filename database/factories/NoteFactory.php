<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->word(2),
            'content' => fake()->paragraph(3, true),
            'user_id' => User::factory(),
            'folder_id' => null, // Folder::factory(),
        ];
    }
}

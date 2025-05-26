<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Note;
use App\Models\User;
use App\Models\Folder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
            $folders = Folder::factory(rand(10, 15))->create([
                'user_id' => $user->id,
            ]);

            foreach ($folders as $folder) {
                Note::factory(rand(5, 10))->create([
                    'user_id' => $user->id,
                    'folder_id' => $folder->id,
                ]);
            }

            Note::factory(2)->create([
                'user_id' => $user->id,
                'folder_id' => null,
            ]);
        });
    }
}

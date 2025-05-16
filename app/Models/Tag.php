<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function notes()
    {
        return $this->belongsToMany(Note::class, 'note_tag', 'tag_id', 'note_id')
            ->withTimestamps();
    }
}

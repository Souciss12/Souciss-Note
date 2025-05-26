<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $fillable = [
        'primary_color',
        'secondary_color',
        'hover_color',
        'background1_color',
        'background2_color',
        'black_text_color',
        'white_text_color',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getDefaultColors(): array
    {
        return [
            'primary_color' => '#8B5CF6',
            'secondary_color' => '#A78BFA',
            'hover_color' => '#DDD6FE',
            'background1_color' => '#F5F3FF',
            'background2_color' => '#FFFFFF',
            'black_text_color' => '#1F2937',
            'white_text_color' => '#F5F3FF'
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ColorController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        Color::updateOrCreate(
            ['user_id' => $user->id],
            [
                'primary_color' => $request->primary_color,
                'secondary_color' => $request->secondary_color,
                'hover_color' => $request->hover_color,
                'background1_color' => $request->background1_color,
                'background2_color' => $request->background2_color,
                'black_text_color' => $request->black_text_color,
                'white_text_color' => $request->white_text_color,
            ]
        );
    }

    public function reset()
    {
        $user = Auth::user();

        Color::updateOrCreate(
            ['user_id' => $user->id],
            Color::getDefaultColors()
        );
    }
}

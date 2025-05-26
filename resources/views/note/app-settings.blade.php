@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <form method="POST" action="{{ route('colors.update') }}" id="colorForm">
                @csrf
                <div>
                    <div>Primary Color</div>
                    <input type="color" name="primary_color" value="{{ $colors['primary_color'] ?? '#8B5CF6' }}">
                    <input type="text" id="primary_color_text" value="{{ $colors['primary_color'] ?? '#8B5CF6' }}">
                </div>

                <div>
                    <div>Secondary Color</div>
                    <input type="color" name="secondary_color" value="{{ $colors['secondary_color'] ?? '#A78BFA' }}">
                    <input type="text" id="secondary_color_text" value="{{ $colors['secondary_color'] ?? '#A78BFA' }}">
                </div>

                <div>
                    <div>hover Color</div>
                    <input type="color" name="hover_color" value="{{ $colors['hover_color'] ?? '#DDD6FE' }}">
                    <input type="text" id="hover_color_text" value="{{ $colors['hover_color'] ?? '#DDD6FE' }}">
                </div>

                <div>
                    <div>Background 1 Color</div>
                    <input type="color" name="background1_color" value="{{ $colors['background1_color'] ?? '#F5F3FF' }}">
                    <input type="text" id="background1_color_text"
                        value="{{ $colors['background1_color'] ?? '#F5F3FF' }}">
                </div>

                <div>
                    <div>Background 2 Color</div>
                    <input type="color" name="background2_color" value="{{ $colors['background2_color'] ?? '#FFFFFF' }}">
                    <input type="text" id="background2_color_text"
                        value="{{ $colors['background2_color'] ?? '#FFFFFF' }}">
                </div>

                <div>
                    <div>Black Text Color</div>
                    <input type="color" name="black_text_color" value="{{ $colors['black_text_color'] ?? '#1F2937' }}">
                    <input type="text" id="black_text_color_text"
                        value="{{ $colors['black_text_color'] ?? '#1F2937' }}">
                </div>

                <div>
                    <div>White Text Color</div>
                    <input type="color" name="white_text_color" value="{{ $colors['white_text_color'] ?? '#F5F3FF' }}">
                    <input type="text" id="white_text_color_text"
                        value="{{ $colors['white_text_color'] ?? '#F5F3FF' }}">
                </div>

                <button type="submit" class="btn-primary">
                    Save
                </button>
            </form>
        </div>


        <div class="settings-card">
            <form method="POST" action="{{ route('colors.reset') }}" style="display: inline;">
                @csrf
                <div class="btn-group">
                    <button type="submit" class="btn-secondary"
                        onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser les couleurs ?')">
                        Reset
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ route('note.index') }}" class="btn">
            Back to Notes
        </a>
    </div>
@endsection

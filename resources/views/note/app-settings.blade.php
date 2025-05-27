@extends('app')

@section('content')
    <div class="container mx-auto my-auto color-container">
        <div class="row">
            <div class="row">
                <div class="app-settings-title">App Settings</div>
            </div>
            <form method="POST" action="{{ route('colors.update') }}" id="colorForm">
                @csrf
                <div class="row my-2">
                    <div class="d-flex">
                        <div class="col-6 color-title">Primary Color</div>
                        <input type="color" class="color-picker" name="primary_color"
                            value="{{ $colors['primary_color'] ?? '#8B5CF6' }}">
                    </div>
                </div>
                <div class="row my-2">
                    <div class="d-flex">
                        <div class="col-6 color-title">Secondary Color</div>
                        <input type="color" class="color-picker" name="secondary_color"
                            value="{{ $colors['secondary_color'] ?? '#A78BFA' }}">
                    </div>
                </div>
                <div class="row my-2">
                    <div class="d-flex">
                        <div class="col-6 color-title">hover Color</div>
                        <input type="color" class="color-picker" name="hover_color"
                            value="{{ $colors['hover_color'] ?? '#DDD6FE' }}">
                    </div>
                </div>
                <div class="row my-2">
                    <div class="d-flex">
                        <div class="col-6 color-title">Background 1 Color</div>
                        <input type="color" class="color-picker" name="background1_color"
                            value="{{ $colors['background1_color'] ?? '#F5F3FF' }}">
                    </div>
                </div>
                <div class="row my-2">
                    <div class="d-flex">
                        <div class="col-6 color-title">Background 2 Color</div>
                        <input type="color" class="color-picker" name="background2_color"
                            value="{{ $colors['background2_color'] ?? '#FFFFFF' }}">
                    </div>
                </div>
                <div class="row my-2">
                    <div class="d-flex">
                        <div class="col-6 color-title">Black Text Color</div>
                        <input type="color" class="color-picker" name="black_text_color"
                            value="{{ $colors['black_text_color'] ?? '#1F2937' }}">
                    </div>
                </div>
                <div class="row my-2">
                    <div class="d-flex">
                        <div class="col-6 color-title">White Text Color</div>
                        <input type="color" class="color-picker" name="white_text_color"
                            value="{{ $colors['white_text_color'] ?? '#F5F3FF' }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-color" class="color-title">
                    Save
                </button>
            </form>

        </div>
        <div class="row">
            <div class="col-6">
                <div class="settings-card">
                    <form method="POST" action="{{ route('colors.reset') }}" style="display: inline;">
                        @csrf
                        <div class="btn-group">
                            <button type="submit" class="btn btn-color"
                                onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser les couleurs ?')">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-6">
                <a href="{{ route('note.index') }}" class="btn btn-color">
                    Back to Notes
                </a>
            </div>
        </div>
    </div>
    <style>
        .app-settings-title {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-color {
            background-color: var(--secondary-color);
            color: var(--white-text-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 6px 0px 6px 0px;
        }

        .color-container {
            max-width: 600px;
            padding: 25px;
            background-color: var(--background1-color);
            border-radius: 6px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-color:hover {
            background-color: var(--primary-color);
        }

        .color-title {
            color: var(--black-text-color);
        }

        .color-picker {
            appearance: none;
            -webkit-appearance: none;
            border: none;
            width: 60px;
            height: 30px;
            border-radius: 5px;
            cursor: pointer;
            padding: 0;
        }

        .color-picker::-webkit-color-swatch-wrapper {
            padding: 0;
            border-radius: 5px;
        }

        .color-picker::-webkit-color-swatch {
            border: none;
            border-radius: 5px;
        }

        .color-picker::-moz-color-swatch {
            border: none;
            border-radius: 5px;
        }
    </style>
@endsection

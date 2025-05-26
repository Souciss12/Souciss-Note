@extends('app')

@section('content')
    @vite(['resources/css/welcome.css'])

    <div class="container mt-5">
        <h2 class="text-center mb-5 souciss-title">Profile Settings</h2>

        <div class="row mb-4">
            <div class="col-md-6 mb-4 mb-md-0">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="col-md-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-6 text-start">
                <a href="{{ route('note.index') }}" class="btn btn-violet px-4">
                    Back to Notes
                </a>
            </div>
            <div class="col-6">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .souciss-title {
            color: #8B5CF6;
        }

        .card {
            border: 1px solid #c4c4c4;
            background-color: #F5F3FF;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            padding: 24px;
        }

        .form-section-title {
            color: #8B5CF6;
            margin-bottom: 1rem;
        }

        .btn-violet {
            background-color: #8B5CF6;
            color: white;
        }

        .btn-violet:hover {
            background-color: #7C3AED;
        }
    </style>
@endpush

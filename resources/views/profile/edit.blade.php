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
        .card {
            border: 1px solid #c4c4c4;
            background-color: var(--background1-color);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            padding: 24px;
        }

        .form-section-title {
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .btn-violet {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-violet:hover {
            background-color: var(--primary-color);
        }
    </style>
@endpush

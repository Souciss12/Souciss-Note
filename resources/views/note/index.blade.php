@extends('app')
@vite(['resources/css/index.css'])
@section('content')
    <div class="container-fluid g-0 flex-grow-container">
        <div class="row g-0 row-options">
            <div class="col-2">
                <div class="options bg-danger">
                    Recherche
                </div>
            </div>
            <div class="col-10">
                <div class="options bg-info">
                    Barre d'outils
                </div>
            </div>
        </div>
        <div class="row g-0 row-arbo">
            <div class="col-2">
                <x-note-arbo :notes="$notes" :folders="$folders" />
            </div>
            <div class="col-10 note-bg">
                <x-note-content />
            </div>
        </div>
    @endsection

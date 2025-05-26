@extends('app')
@vite(['resources/css/index.css'])
@section('content')
    <div class="container-fluid g-0 flex-grow-container">
        <div class="row g-0 row-options">
            <div class="col-xl-2 col-md-3 col-4">
                <div class="search-bg">
                    <x-note-search />
                </div>
            </div>
            <div class="col-xl-10 col-md-9 col-8 toolbar-bg">
                <x-note-toolbar />
            </div>
        </div>
        <div class="row g-0 row-arbo">
            <div class="col-xl-2 col-md-3 col-4 arbo-bg">
                <x-note-arbo :notes="$notes" :folders="$folders" />
            </div>
            <div class="col-xl-10 col-md-9 col-8 gx-4 note-bg">
                <x-note-content />
            </div>
        </div>
    @endsection

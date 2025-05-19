@extends('app')
@vite(['resources/css/index.css'])
@section('content')
    <div class="container-fluid g-0 flex-grow-container">
        <div class="row g-0 row-options">
            <div class="col-2">
                <div class="note-content bg-danger">
                    content
                </div>
            </div>
            <div class="col-10">
                <div class="note-content bg-info">
                    content
                </div>
            </div>
        </div>
        <div class="row g-0 row-arbo">
            <div class="col-2">
                <x-note-arbo :notes="$notes" :folders="$folders" />
            </div>
            <div class="col-10">
                <div class="note-content bg-primary">
                    content
                </div>
            </div>
        </div>
    @endsection

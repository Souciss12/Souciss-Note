@extends('app')
@section('content')
    <div>
        <x-note-arbo :notes="$notes" :folders="$folders" />
    </div>
@endsection

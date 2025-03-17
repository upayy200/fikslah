@extends('layouts.app')

@section('title', "Dashboard - $kebun->NamaKebun")

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Dashboard - {{ $kebun->NamaKebun }}</h2>
        </div>
        <div class="card-body">
            <p>Selamat datang di kebun: <strong>{{ $kebun->NamaKebun }}</strong></p>
            <p>ID Kebun: <strong>{{ $kebun->KodeKebun }}</strong></p>
        </div>
    </div>
@endsection

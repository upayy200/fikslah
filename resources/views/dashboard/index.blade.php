@extends('layouts.app')

@section('title', "Dashboard - $kebun->nama_kebun")

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Dashboard - {{ $kebun->nama_kebun }}</h2>
        </div>
        <div class="card-body">
            <p>Selamat datang di kebun: <strong>{{ $kebun->nama_kebun }}</strong></p>
            <p>ID Kebun: <strong>{{ $kebun->id }}</strong></p>
        </div>
    </div>
@endsection

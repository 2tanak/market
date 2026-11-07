<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Auth Module - {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="author" content="{{ $author ?? '' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Vite CSS --}}
    
	   @vite(['Modules/Auth/resources/assets/css/login.css'])
	  
</head>

<body>
<div class="alert-container">
    {{-- Блок Ошибки --}}
    @if (session('error'))
        <div class="alert-box alert-error" id="alert-error">
            <div class="alert-content">
                <span>❌</span>
                <div>{{ session('error') }}</div>
            </div>
            <button class="alert-close-btn" onclick="document.getElementById('alert-error').remove()">&times;</button>
        </div>
    @endif

    {{-- Блок Успеха --}}
    @if (session('success'))
        <div class="alert-box alert-success" id="alert-success">
            <div class="alert-content">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
            <button class="alert-close-btn" onclick="document.getElementById('alert-success').remove()">&times;</button>
        </div>
    @endif
</div>


    @yield('content')

   
</body>

@extends('admin::layouts.master')
@section('content')

<div id="app">
<krug-char></krug-char>
</div>

@endsection
@section('js_block')
    @parent <!-- Сохраняет базовые скрипты темы (jquery, bootstrap и т.д.) -->
    @vite(['Modules/Grafika/resources/assets/js/app.js'])
@endsection
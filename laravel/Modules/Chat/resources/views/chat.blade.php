@extends('chat::layouts.master')
@section('css_block')
  @parent
    @vite(['Modules/Chat/resources/assets/sass/app.scss'])
  @endsection
 
@section('content')

@endsection

 @section('js_block')
   @parent
   @vite(['Modules/Chat/resources/assets/js/app.js'])
  @endsection
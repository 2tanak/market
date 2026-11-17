@extends('admin::layouts.master')
@vite(['Modules/Chat/resources/assets/css/app.css']) 
 @section('css_block')
   @parent
    @vite(['Modules/Chat/resources/assets/css/app.css']) 
  @show
@section('content')

@endsection

 @section('js_block')
   @parent
   @vite(['Modules/Chat/resources/assets/js/app.js'])
  @show
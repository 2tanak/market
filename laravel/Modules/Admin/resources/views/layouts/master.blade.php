<!DOCTYPE html>

<head>
 <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Admin Module - {{ config('app.name', 'Laravel') }}</title>


    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? '' }}">
    <meta name="author" content="{{ $author ?? '' }}">
	@vite(['Modules/Admin/resources/assets/sass/admin.scss']) 

</head>
@section('css_block')
    @show

<body>
<div class='main'>

  
 @if(session()->has('error'))
  <div class="alert alert-danger container">
     {{ session()->get('error') }}
  </div>
 @endif
 
 @include('admin::__block.main_navbar')
 
 <div class='content container'>
    @include('admin::__block.sidebar')
	
	<div class="right-bar">
	  @include('admin::__block.page_header')
      @yield('content')
    </div>
	
 </div>

 
 @include('admin::__block.footer')

@section('js_block')
@show
@vite(['Modules/Admin/resources/assets/js/admin.js'])        </div>
</body>
</html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">

   <title>Admin Module - {{ config('app.name', 'Laravel') }}</title>


   <meta name="description" content="{{ $description ?? '' }}">
   <meta name="keywords" content="{{ $keywords ?? '' }}">
   <meta name="author" content="{{ $author ?? '' }}">
   @auth
      <script>
         window.apiToken = @json(session('api_token'));
         window.authUser = @json(auth()->user());
      </script>
   @endauth
   @vite(['Modules/Admin/resources/assets/sass/admin.scss'])
   @section('css_block')
   @show
</head>
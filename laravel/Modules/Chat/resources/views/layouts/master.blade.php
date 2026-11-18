<!DOCTYPE html>

@include('admin::__block.head')
@section('css_block')
   @parent
    @vite(['Modules/Chat/resources/assets/css/app.css']) 
  @show

<body>
<div id="app">
   <div class='main'>
      @include('admin::__block.main_navbar')
	  
	     <div class='main-content container'>
         @include('chat::__block.sidebar')
          
			
        <div class="content">
		   @include('admin::__block.page_header')
		   @include('admin::__block.notifications')
           @yield('content')
		</div>

      </div>
	  </div>
	  </div>
	  

@include('admin::__block.footer')
 @section('js_block')
   @parent
   @vite(['Modules/Chat/resources/assets/js/app.js'])
  @show

	
<script>
     window.apiToken = "{{ $token }}";
</script>	
</body>
</html>

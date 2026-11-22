<!DOCTYPE html>

@include('admin::__block.head')


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
	  @include('admin::__block.footer')
	  </div>
	 </div>
	  


 

	
<script>
     window.apiToken = "{{ $token }}";
	 window.authUser = "{{ json_encode(auth()->user()) }}";
	 
</script>	
</body>
</html>

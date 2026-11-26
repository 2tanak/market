<!DOCTYPE html>
  @include('admin::__block.head')



<body>
   <div class='main'>
      @include('admin::__block.main_navbar')

      <div class='main-content container'>
         @include('admin::__block.sidebar')
          
			
        <div class="content">
		   @include('admin::__block.page_header')
		   @include('admin::__block.notifications')
           @yield('content')
		</div>

      </div>


      @include('admin::__block.footer')

      
      
   </div>
</body>

</html>



<!-- Выдвижная боковая панель -->
<div class="left-bar">
 <nav class="menu">
  <!-- Пункт 1 -->
 


 <div class="menu-item">
    @if(Auth::check())
      <chats
        :authUser="{{json_encode(auth()->user())}}"  
		 
     </chats>
@endif

  </div>
 
</nav>
</div>









				

					


				

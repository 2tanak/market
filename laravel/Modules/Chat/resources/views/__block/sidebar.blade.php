<div class="left-bar">
   @if(Auth::check())
     <chats
        :user="{{ json_encode($user) }}"
        :token="{{ json_encode($token) }}"
     ></chats>
   @endif
</div>









				

					


				

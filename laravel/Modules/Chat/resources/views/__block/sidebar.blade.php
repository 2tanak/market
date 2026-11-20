<div class="left-bar">
   @if(Auth::check())
     <chats
        :authUser="{{ json_encode(auth()->user()) }}"
        :user="{{ json_encode($user) }}"
        :token="{{ json_encode($token) }}"
     ></chats>
   @endif
</div>









				

					


				

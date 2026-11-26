<div class="left-bar">
    @if(Auth::check())
        <chats :user="{{ json_encode($user) }}"></chats>
    @endif
</div>

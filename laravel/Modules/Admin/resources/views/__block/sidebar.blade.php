

<!-- Выдвижная боковая панель -->
<div class="left-bar">
 <nav class="menu">
  <!-- Пункт 1 -->
 

  <!-- Пункт 2 -->
 <div class="menu-item">
    <input type="radio" id="blog" name="accordion" class="menu-radio">
    <label for="blog" class="menu-link">Блог</label>
    <div 
	  class='submenu'
	  >
      <a href="{{ route('blog.index') }}" class="{{ ($rout ?? '') === 'designed' ? 'active' : '' }}">
            Список
      </a>
      
    </div>
  </div>
 <div class="menu-item">
    <input type="radio" id="chat" name="accordion" class="menu-radio">
    <label for="chat" class="menu-link">чаты</label>
    <div 
	  class='submenu'
	  >
      <a href="{{ route('chat') }}" class="{{ ($rout ?? '') === 'designed' ? 'active' : '' }}">
            чат
      </a>
      
    </div>
  </div>
 
</nav>
</div>






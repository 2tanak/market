

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
  <!-- Пункт 3 -->
 <div class="menu-item">
    <input type="radio" id="grafika" name="accordion" class="menu-radio">
    <label for="grafika" class="menu-link">графика</label>
    <div 
	  class='submenu'
	  >
      <a href="{{ route('charline') }}" class="{{ ($rout ?? '') === 'charline' ? 'active' : '' }}">
            Линейный график
      </a>
      
    </div>
  </div>
 
</nav>
</div>






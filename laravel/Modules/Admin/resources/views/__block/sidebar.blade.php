

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
@can('view_design')
 <div class="menu-item">
      <input type="radio" id="menu-2" name="accordion" class="menu-radio">
      <label for="menu-2" class="menu-link">Пользователи</label>
      <div class="submenu">
        <a
          href="{{route('user.index')}}"
          class="{{ ($rout ?? '') === 'user' ? 'active' : '' }}">
          Список юзеров</a>

        <a
          href="{{route('role.index')}}"
          class="{{ ($rout ?? '') === 'role' ? 'active' : '' }}">
          Список ролей</a>

        <a
          href="{{route('permission.index')}}"
          class="{{ ($rout ?? '') === 'permission' ? 'active' : '' }}">
          Список прав</a>
      </div>
    </div>
	@endcan
	
	
</nav>
</div>






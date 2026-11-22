	<input type="checkbox" id="burger-toggle" class="burger-checkbox">

<!-- Шапка сайта -->

<header class="main-header">
  <!-- Иконка бургера (связана с чекбоксом через id) -->
  <div class="header container">
  <label for="burger-toggle" class="burger-btn">
    <span></span>
  </label>
 
  
  
  
  
   <div class="profile-menu" id="profileMenu">
    <button class="profile-btn" id="profileBtn">
      <span>Профиль</span>
      <svg width="12" height="12" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
    </button>
    
    <!-- Всплывающее окно -->
    <div class="dropdown-menu" id="dropdownMenu">
      <a href="/profile" class="dropdown-item">Настройки</a>
      <hr class="dropdown-divider">
      <a href="{{ route('admin_logout') }}" class="dropdown-item logout-btn">Выйти</a>
    </div>
  </div>
  
  </div>
  
</header>
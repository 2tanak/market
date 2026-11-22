const profileBtn = document.getElementById('profileBtn');
const dropdownMenu = document.getElementById('dropdownMenu');

// Переключение видимости окна по клику на кнопку
profileBtn.addEventListener('click', (event) => {
  event.stopPropagation(); // Предотвращаем срабатывание клика на документе
  dropdownMenu.classList.toggle('active');
});

// Закрытие окна при клике в любое другое место экрана
document.addEventListener('click', (event) => {
  if (!dropdownMenu.contains(event.target) && event.target !== profileBtn) {
    dropdownMenu.classList.remove('active');
  }
});

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


/*
document.addEventListener('DOMContentLoaded', () => {
    const burger = document.querySelector('.burger-checkbox');
    
    // Если чекбокса нет на этой странице, выходим из скрипта
    if (!burger) return;

    // 1. Проверяем сохраненное состояние при загрузке страницы
    const savedState = localStorage.getItem('burgerChecked');
    
    if (savedState !== null) {
        // Устанавливаем true или false в зависимости от того, что сохранено
        burger.checked = savedState === 'true';
    } else {
        // Если страница открыта ВПЕРВЫЕ (нет записи), делаем его CHECKED по умолчанию
        burger.checked = true; 
    }

    // 2. Слушаем клики и сохраняем новое состояние пользователя
    burger.addEventListener('change', () => {
        localStorage.setItem('burgerChecked', burger.checked);
    });
});
*/
/*--при клике по пункту меню в left баре закрываем соседнюю открытую--*/
document.addEventListener('DOMContentLoaded', () => {
    // Находим все радиокнопки меню
    const radios = document.querySelectorAll('.menu-radio');
    if (radios.length === 0) return;

    // 1. Проверяем, какой пункт был сохранен ранее
    const savedMenuId = localStorage.getItem('activeMenuId');

    if (savedMenuId && document.getElementById(savedMenuId)) {
        // Восстанавливаем сохраненный пункт
        document.getElementById(savedMenuId).checked = true;
    } else {
        // Если зашли ПЕРВЫЙ раз, принудительно открываем ПЕРВЫЙ пункт
        radios[0].checked = true;
    }

    // 2. Следим за переключением меню и сохраняем ID текущего открытого пункта
    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                localStorage.setItem('activeMenuId', radio.id);
            }
        });
    });
});



/*--закрываем плашку удалить/изменит у соседа--*/
document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = document.querySelectorAll('.action-dropdown');

    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('.btn-action-trigger');

        trigger.addEventListener('click', (event) => {
            // Останавливаем всплытие, чтобы клик по бургеру не ловил документ ниже
            event.stopPropagation(); 

            // Находим, активно ли текущее меню до клика
            const isCurrentActive = dropdown.classList.contains('active');

            // Сначала ЗАКРЫВАЕМ ВСЕ открытые меню у соседей
            dropdowns.forEach(d => d.classList.remove('active'));

            // Если текущее меню не было активно — ОТКРЫВАЕМ его
            if (!isCurrentActive) {
                dropdown.classList.add('active');
            }
        });
    });

    // Если кликнуть в любое другое место экрана — закрываем вообще все меню
    document.addEventListener('click', () => {
        dropdowns.forEach(d => d.classList.remove('active'));
    });
});

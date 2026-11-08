/**
 * Модуль параллельной AJAX-загрузки картинок для слайдера (OLX-интерфейс)
 */
document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const slider = document.getElementById('preview-slider');

    // Проверяем наличие элементов на странице (чтобы JS не падал на страницах без слайдера)
    if (!dropZone || !fileInput || !slider) {
        return;
    }

    // Клик по зоне открывает проводник для выбора файлов
    dropZone.addEventListener('click', () => fileInput.click());

    // Визуальные эффекты подсветки рамки при перетаскивании файлов мышкой
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => { 
            e.preventDefault(); 
            dropZone.style.borderColor = '#4f46e5';
            dropZone.style.backgroundColor = '#f8fafc';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => { 
            e.preventDefault(); 
            dropZone.style.borderColor = '#cbd5e1';
            dropZone.style.backgroundColor = '#ffffff';
        }, false);
    });

    // Обработка сброса файлов мышкой (Drag and Drop)
    dropZone.addEventListener('drop', (e) => {
        handleFiles(e.dataTransfer.files);
    });

    // Обработка стандартного выбора файлов через кнопку "Обзор"
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    // Перебираем все выбранные фотографии
    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                // Отправляем каждую картинку отдельно (параллельно)
                uploadSliderFileParallel(file);
            }
        });
    }

    function uploadSliderFileParallel(file) {
        // Генерируем случайный ID для карточки в браузере
        const uniqueId = 'img_' + Math.random().toString(36).substr(2, 9);
        // Создаем временную локальную ссылку, чтобы показать картинку сразу
        const localBlobUrl = URL.createObjectURL(file);

        // ШАБЛОН КАРТОЧКИ (itemHtml)
        const itemHtml = `
            <div id="${uniqueId}" class="olx-card">
                <div class="olx-img-wrapper">
                    <!-- Локальное превью (слегка размыто, пока идет загрузка) -->
                    <img src="${localBlobUrl}" class="loading">
                    
                    <!-- Оверлей со спиннером загрузки -->
                    <div class="olx-loader">
                        <svg class="olx-spinner" xmlns="http://w3.org" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <div class="olx-loader-text">Загрузка</div>
                    </div>

                    <!-- Кнопка удаления (крестик) -->
                    <button type="button" onclick="removeSliderItem('${uniqueId}')" class="olx-delete-btn">
                        <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Скрытый инпут: сюда запишется реальное имя файла с сервера -->
                <input type="hidden" name="slider_images[]" value="" class="js-server-filename">
            </div>
        `;

        // Вставляем сгенерированный HTML-код карточки в нашу сетку слайдера
        slider.insertAdjacentHTML('beforeend', itemHtml);

        // Формируем FormData для AJAX-отправки ОДНОГО файла
        const formData = new FormData();
        formData.append('file', file);

        // Получаем CSRF-токен для защиты Laravel
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

        // AJAX-запрос на Laravel роут
        fetch('/admin/slider/image-upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Ошибка при загрузке');
            return response.json();
        })
        .then(data => {
            const card = document.getElementById(uniqueId);
            if (card) {
                // Картинка успешно долетела на сервер! Убираем лоадер загрузки
                const loader = card.querySelector('.olx-loader');
                if (loader) loader.remove();
                
                // Делаем картинку яркой
                const img = card.querySelector('img');
                if (img) img.classList.remove('loading');
                
                // ВШИВАЕМ ИМЯ ИЗ ВРЕМЕННОЙ ПАПКИ В СКРЫТЫЙ ИНПУТ
                const input = card.querySelector('.js-server-filename');
                if (input) input.value = data.filename;
            }
        })
        .catch(error => {
            // В случае ошибки удаляем карточку, чтобы пользователь видел сбой загрузки
            const card = document.getElementById(uniqueId);
            if (card) card.remove();
        });
    }
});

window.removeSliderItem = function(id, event) {
    if (event) event.stopPropagation(); // Останавливает любое всплытие клика
    const item = document.getElementById(id);
    if (item) item.remove();
};

/**
 * Глобальная функция AJAX-удаления картинки слайдера с сервера и из БД
 * 
 * @param {string} htmlCardId - ID HTML-контейнера карточки (например, 'saved_img_42')
 * @param {number} dbFileId   - Числовой ID записи из таблицы files (например, 42)
 * @param {Event} event       - Объект события клика для остановки всплытия
 */
window.removeSavedSliderItem = function(htmlCardId, dbFileId, event) {
    // 1. Изолируем клик, чтобы не открывался проводник drop-zone
	
    const e = event || window.event;
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }

    // Опционально: можно добавить подтверждение удаления
    if (!confirm('Удалить это изображение безвозвратно?')) {
        return;
    }

    // 2. Вытаскиваем CSRF-токен со страницы для защиты Laravel
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

    // 3. Шлем асинхронный DELETE запрос на наш роут
    // Передаем dbFileId прямо в URL — Laravel сам найдет модель в базе!
    fetch(`/admin/slider/image-delete/${dbFileId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Если статус не 200 (например 404 или 500), кидаем ошибку
        if (!response.ok) {
            throw new Error('Ошибка сервера при удалении файла');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // 4. Если сервер стер файлы и запись — удаляем карточку из интерфейса
            const cardElement = document.getElementById(htmlCardId);
            if (cardElement) {
                cardElement.remove();
            }
        } else {
            alert('Ошибка: ' + (data.error || 'Не удалось удалить'));
        }
    })
    .catch(error => {
        console.error('Ошибка AJAX:', error);
        alert('Не удалось связаться с сервером для удаления картинки.');
    });
};


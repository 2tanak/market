document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById('image-input');
    const cropperModal = document.getElementById('cropper-modal');
    const imageToCrop = document.getElementById('image-to-crop');
    const saveCropBtn = document.getElementById('save-crop');
    const cancelCropBtn = document.getElementById('cancel-crop');
    const croppedPreview = document.getElementById('cropped-result-preview');
    const croppedContainer = document.getElementById('cropped-result-container');
    
    let cropper = null;

    if (!imageInput) return;

    // 1. Слушаем выбор файла
    imageInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            
            reader.onload = function (event) {
                // Уничтожаем старый кроппер, если он остался от предыдущего файла
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                // Подсовываем картинке новые данные
                imageToCrop.src = event.target.result;
                
                // Принудительно показываем модалку, чтобы у элементов появились размеры на экране
                cropperModal.style.display = 'flex'; 

                // Вешаем инициализацию на событие полной прогрузки тега img
                imageToCrop.onload = function() {
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 16 / 9, // Выставлено 16:9 под условия твоего шаблона
                        viewMode: 1,         // Рамка не может вылезать за пределы фотографии
                        autoCropArea: 0.8,   // Рамка займет 80% площади при старте
                        responsive: true,
                        restore: false,
                        checkCrossOrigin: false
                    });
                };
            };
            reader.readAsDataURL(file);
        }
    });

    // 2. Кнопка "Применить кроп" (Программная подмена файла в инпуте)
    saveCropBtn.addEventListener('click', function () {
        if (!cropper) return;

        // Получаем холст с обрезанной картинкой
        const canvas = cropper.getCroppedCanvas({
            width: 1200, // Ограничиваем максимальную ширину для оптимизации
        });

        // Показываем превью обрезанного файла админу в форме
        if (croppedPreview) croppedPreview.src = canvas.toDataURL('image/jpeg');
        if (croppedContainer) croppedContainer.style.display = 'block';

        // Переводим холст в бинарный Blob
        canvas.toBlob(function (blob) {
            if (!blob) return;
			
            // Создаем объект File из Blob
            const croppedFile = new File([blob], 'cropped_image.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });

            // Используем DataTransfer, чтобы насильно засунуть новый файл внутрь инпута
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
			
            // Подменяем файлы в инпуте. Твой AJAX-скрипт заберет этот инпут уже с новым файлом
            imageInput.files = dataTransfer.files;
			
            // Закрываем окно кропа и очищаем память
            cropperModal.style.display = 'none'; 
            cropper.destroy();
            cropper = null;
        }, 'image/jpeg', 0.9); // Формат и качество (90%)
    });

    // 3. Отмена кропа
    cancelCropBtn.addEventListener('click', function () {
        cropperModal.style.display = 'none';
        imageInput.value = ''; // Полностью сбрасываем инпут, чтобы не улетела старая картинка
        if (croppedContainer) croppedContainer.style.display = 'none';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });
});


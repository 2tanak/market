<div class="form-group">
    <label for="image">Image (Main)</label>

    @if ($errors->has('image'))
        <span class="help-block"><strong>{{ $errors->first('image') }}</strong></span>
    @endif

    {{-- Сюда JS выведет превью НАШЕГО КРОПА перед отправкой (для наглядности) --}}
    <div id="cropped-result-container" style="display:none; margin-bottom: 10px;">
        <img id="cropped-result-preview" src="#" style="max-width: 150px; border-radius: 6px; border: 1px solid #cbd5e1;">
        <p style="font-size: 12px; color: #10b981;">Картинка успешно обрезана и готова к отправке</p>
    </div>

    {{-- Обычный инпут выбора файла --}}
    <input type="file" name="image" class="form-control" id="image-input" accept="image/*">
    <small style="color: #64748b; font-size: 13px;">Пропорции рамки: 16:9 (автоматическая обрезка)</small>
</div>
{{-- ВСПЛЫВАЮЩИЙ БЛОК ДЛЯ КРОПА (появляется только после выбора файла) --}}
<div id="cropper-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; padding: 20px; border-radius: 8px; max-width: 600px; width: 100%; display: flex; flex-direction: column; gap: 15px;">
        <h3 style="margin: 0; font-size: 18px; font-weight: 600;">Обрежьте изображение</h3>
        
        <!-- Контейнер, где Cropper.js развернет картинку -->
        <div style="max-height: 400px; overflow: hidden; background: #f1f5f9;">
            <img id="image-to-crop" src="#" style="max-width: 100%;">
        </div>

        <!-- Кнопки управления -->
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" id="cancel-crop" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Отмена</button>
            <button type="button" id="save-crop" style="background: #10b981; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Применить кроп</button>
        </div>
    </div>
</div>

{{-- Достаем из кэша связей только файл с меткой 'main' --}}
@php
    $mainAvatar = $model->files->where('description', 'main')->first();
@endphp

@if($mainAvatar)
    <div class="current-avatar-preview" style="margin-bottom: 15px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Текущая аватарка:</label>
        {{-- Выводим путь к файлу --}}
        <img src="{{ Storage::disk($mainAvatar->disk)->url($mainAvatar->large_path) }}" style="max-width: 150px; border-radius: 6px; border: 1px solid #cbd5e1;">
    </div>
@endif
<br>
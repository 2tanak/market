<div class="olx-upload-container">
    <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">
        Фотографии слайдера 
    </label>

    
    <!-- Интерактивная Дроп-зона -->
    <div id="drop-zone" class="olx-drop-zone">
        <!-- SVG Иконка теперь имеет жесткие стили и не раздуется -->
        <svg class="olx-icon-cloud" xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
        </svg>
        
        <div class="olx-main-text">
            <span class="olx-link-text">Выберите фотографии</span> или перетащите их сюда
        </div>
        <p class="olx-sub-text">Поддерживаются JPEG, PNG, WEBP (Автоматическое центрирование 301x301)</p>
        
        <!-- Системный инпут полностью прячем, клик на зону откроет его через JS -->
        <input type="file" id="file-input" multiple accept="image/*" style="display: none !important;">
    </div>




<div id="preview-slider" class="olx-preview-grid">
    {{-- ИСПРАВЛЕНО: Добавлен ->where('description', '!=', 'main') --}}
    @if(isset($model) && $model->files->isNotEmpty())
        @foreach($model->files->where('description', '=', 'slider') as $file)
            <div id="saved_img_{{ $file->id }}" class="olx-card">
                <div class="olx-img-wrapper">
                    <img src="{{ Storage::disk($file->disk)->url($file->large_path) }}">
                   <button type="button" onclick="removeSavedSliderItem('saved_img_{{ $file->id }}', {{ $file->id }}, event)" class="olx-delete-btn">✕</button>

                </div>
                <input type="hidden" name="existing_slider_images[]" value="{{ $file->id }}">
            </div>
        @endforeach
    @endif
</div>

	
	
	
	
</div>
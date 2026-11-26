  <!-- Поле Description -->
    <div class="form-group">
        <label for="text">{{ $title }}</label>
        @if ($errors->has($title))
            <span class="help-block">
                <strong>{{ $errors->first($title) }}</strong>
            </span>
        @endif
        
        <textarea class="form-control form-textarea" id="text" name="{{ $title }}">{{ $value ? $value : old($title) }}</textarea>
    </div>
	<br>
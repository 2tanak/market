<!-- Поле Title -->
    <div class="form-group">
        <label for="{{ $title }}">
		 {{ $title }}
		</label>
        @if ($errors->has($title))
            <span class="help-block">
                <strong>{{ $errors->first($title) }}</strong>
            </span>
        @endif
        
        <input 
		  type="text" 
		  class="form-control {{ $class ?? '' }}" 
		  id="{{ $title }}"
		  placeholder="{{ $placeholder ?? '' }}" 
		  maxlength="{{$maxlength ?? 20 }}"
		  name="{{$title}}" 
		  value="{{ isset($value) ? $clear ?? $value  : old($title) }}">
    </div>
<br>
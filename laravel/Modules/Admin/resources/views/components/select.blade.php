
@if(isset($value) && isset($option))
    <div class="form-group">
         <label for="publish">{{ \Illuminate\Support\Str::replace('[]', '', $title) }}</label>
        <select 
		  name="{{$title}}" 
		  id="{{$id ?? 'form'}}" 
		  class="{{$class ?? 'form-control'}}"
		  autocomplete ="{{ $autocomplete ?? 'off'}}"
		  placeholder ="{{ $placeholder ?? '' }}"
		  @if(isset($multiple) && $multiple) multiple @endif
		  >
		 @foreach($option as $k => $val)
		  @if(is_array($value))
			<option value="{{ $k }}" {{ in_array($val, $value) ? 'selected' : '' }} >
			{{ $val }}
		   </option>
		  @else
			<option value="{{ $k }}" 
			 {{ $value === $val  ? 'selected' : '' }}>
		      {{ $val }}
			</option>
		  @endif
		@endforeach
           
        </select>
    </div>
<br>
@endif  
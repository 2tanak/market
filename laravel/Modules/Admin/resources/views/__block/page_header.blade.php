<!-- Page header -->
<nav class="breadcrumbs" aria-label="Хлебные крошки">
  <ol class="breadcrumbs__list">
    <li class="breadcrumbs__item">
      <a href="{{ route('admin_index') }}" class="breadcrumbs__link">@lang('title.main')</a>
    </li>
	@if (isset($ar_bread) && is_array($ar_bread) &&    count($ar_bread) > 0)
       @foreach ($ar_bread as $k => $v)
        @if ($loop->last)
		 <li>{{ $v }}</li>
		@else
        <li class="breadcrumbs__item">
         <a href="{{ $k }}" class="breadcrumbs__link">{{ $v }}</a>
        </li>
         @endif           
        @endforeach
    @endif
   
  </ol>
</nav>
<!-- /page header -->
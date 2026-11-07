@extends('admin::layouts.master')
@section('title', $title)
@section('css_block')
@parent
<link rel="stylesheet" href="/lib/tom-select/bootstrap5.min.css">
<link rel="stylesheet" href="/lib/scropper/scropper.css">
@endsection

@section('content')

	@if($model?->id)
<form action="{{ route($route_path . '.update', $model) }}" method="post" enctype="multipart/form-data"
    class="need_validate_form" novalidate>
    @method('PUT')
	@else
	<form action="{{ route($route_path . '.store') }}" method="post" enctype="multipart/form-data"
    class="need_validate_form" novalidate>
@endif
    
	

    @include('auth::user.__form')
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

    <button type="submit" class="btn btn-primary pull-left" style="margin-bottom:20px">
        @isset($model->id)
        @lang('button.update')
        @else
        @lang('button.save')
        @endisset
    </button>

</form>

@endsection

@section('js_block')
@parent



<script src="/lib/tom-select/tom-select.complete.min.js"></script>

<script>
    // Инициализация Tom Select
    new TomSelect("#roles-select", {
        plugins: {
            // Добавляет удобную кнопку-крестик для удаления выбранного тега
            'remove_button': {
                title: 'Удалить роль',
            }
        },
        create: false,          // Запрещает пользователям вводить свои варианты
        maxItems: null,         // Выбирать можно бесконечно много ролей
        hideSelected: true,     // Скрывает уже выбранные роли из выпадающего списка
        placeholder: "Кликните, чтобы выбрать роли...",
    });
</script>
@endsection
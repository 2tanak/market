@extends('admin::layouts.master')
@section('title', $title)
@section('css_block')
@parent

@endsection

@section('content')
<div class="content-update">
<div class="content-center">
	@if($model?->id)
<form action="{{ route($route_path . '.update', $model) }}" method="post" enctype="multipart/form-data"
    class="need_validate_form" novalidate>
    @method('PUT')
	@else
	<form action="{{ route($route_path . '.store') }}" method="post" enctype="multipart/form-data"
    class="need_validate_form" novalidate>
@endif
    
	

    @include('blog::admin.__form')

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
</div>

</div>
@endsection

@section('js_block')
@vite(['Modules/Admin/resources/assets/js/custom_slider.js'])
@parent








@endsection
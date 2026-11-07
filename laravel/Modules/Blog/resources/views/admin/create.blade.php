@extends('admin::layouts.master')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-md-9">
        <div class="panel panel-flat">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
                <form action="{{route($route_path.'.store')}}" method="post" enctype="multipart/form-data" class="need_validate_form " novalidate>
                    @php
                    $disabled = true;
                    @endphp

                    @include('admin::page.'.$rout.'.__form')
                    <br><br>
                    <button type="submit" class="btn btn-primary pull-left">@lang('button.save')</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
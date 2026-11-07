<!-- Page header -->
<div class="page-header page-header-default">
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{ route('admin_index') }}"><i class="icon-home2 position-left"></i> @lang('title.main')</a></li>
            @if (isset($ar_bread) && is_array($ar_bread) && count($ar_bread) > 0)
                @foreach ($ar_bread as $k => $v)
                    <li><a href="{{ $k }}">{{ $v }}</a></li>
                @endforeach
            @endif
			
            <li class="active">@yield('title')</li>
        </ul>
        
        @hasSection('top_right')
            @yield('top_right')
        @else

        @endif
     
    </div>
</div>
<!-- /page header -->
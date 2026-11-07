@extends('admin::layouts.master')

@section('title', $title)

@section('content')

<div class="table-container">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Имя</th>
                <th>slug</th>
                <th>Дата</th>
                <th class="col-actions-header">
                    <a href="{{route($route_path.'.create')}}" class="btn-add">добавить</a>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i)
            <tr>
                <td class="col-title">{{$i->name}}</td>
                <td class="col-description">
                    {{$i->slug}}
                </td>
                <td class="col-date"><span class="date-badge">{{ $i->created_at->format('d.m.Y') }}</span></td>
                <td class="col-actions">
                    <div class="action-dropdown">
                        <!-- Кнопка-триггер (бургер) -->
                        <button type="button" class="btn-action-trigger">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>

                        <!-- Выпадающая плашка -->
                        <div class="action-menu">
                            <a href="{{ route($route_path.'.edit', $i) }}" class="action-link">@lang('Изменить')</a>
                            <form method="POST" action="{{route($route_path.'.destroy', $i) }}" onsubmit="return confirm('Удалить ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-link delete-link" style="background:none;border:none;">@lang('Удалить')</button>

                            </form>
                        </div>
                    </div>
                </td>


            </tr>

            @endforeach



        </tbody>
    </table>
</div>
<div class="pagination-wrapper">
    {{ $items->links('partials.custom-pagination') }}
</div>



@endsection
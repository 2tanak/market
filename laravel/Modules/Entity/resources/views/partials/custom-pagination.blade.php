@if ($paginator->hasPages())
    <div class="custom-pagination">
        {{-- Элементы пагинации (Номера страниц и троеточия) --}}
        @foreach ($elements as $element)
            {{-- Строка-разделитель "Three Dots" (...) --}}
            @if (is_string($element))
                <span class="page-item disabled">{{ $element }}</span>
            @endif

            {{-- Массив ссылок (Страницы) --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-item active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-item">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>
@endif

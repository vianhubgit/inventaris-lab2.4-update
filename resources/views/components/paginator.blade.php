@props(['paginator'])

@if($paginator->total() > 0)
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Halaman <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $paginator->currentPage() }}</span>/{{ $paginator->lastPage() }}
            <span class="mx-1 text-gray-300 dark:text-gray-600">&bull;</span>
            Total {{ number_format($paginator->total(), 0, ',', '.') }} data
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{ $paginator->onEachSide(1)->links() }}

            @if($paginator->lastPage() > 1)
                <form method="GET" class="flex items-center gap-1.5" data-no-loader>
                    @foreach(request()->except('page') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label class="text-sm text-gray-500 dark:text-gray-400">Ke hal.</label>
                    <input type="number" name="page" min="1" max="{{ $paginator->lastPage() }}"
                           value="{{ $paginator->currentPage() }}"
                           class="form-input w-16 py-1 text-center text-sm"
                           aria-label="Nomor halaman">
                    <button class="btn-secondary px-3 py-1.5 text-sm">Pergi</button>
                </form>
            @endif
        </div>
    </div>
@endif

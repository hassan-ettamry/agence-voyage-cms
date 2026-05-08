<div class="flex bg-slate-100 rounded-lg p-0.5">
    @foreach($filters as $key => $label)
        <button
            type="button"
            data-filter="{{ $key }}"
            class="filter-btn px-3 py-1.5 text-xs font-medium rounded-md
            {{ $loop->first ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-800' }}">
            {{ $label }}
        </button>
    @endforeach
</div>
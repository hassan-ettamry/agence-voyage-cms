<div id="uploadMediaModal"
     class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-slate-950/60 p-4">
    <div class="relative w-full max-w-2xl overflow-hidden rounded-[28px] bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.24em] text-indigo-500">New asset</p>
                <h2 class="mt-1 text-2xl font-black text-slate-950">Upload Image</h2>
                <p class="mt-1 text-sm text-slate-500">Add a reusable image with clear metadata for SEO and accessibility.</p>
            </div>
            <button type="button"
                    onclick="closeModal('uploadMediaModal')"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-900"
                    aria-label="Close upload modal">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data" class="space-y-5 px-6 py-6">
            @csrf
            @include('media.partials.form', ['media' => null, 'upload' => true])

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <button type="button"
                        onclick="closeModal('uploadMediaModal')"
                        class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700">
                    Upload image
                </button>
            </div>
        </form>
    </div>
</div>

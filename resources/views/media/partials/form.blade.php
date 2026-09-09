@if($upload ?? false)
<div>
    <label class="mb-2 block text-sm font-black text-slate-900">Image file</label>
    <label class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-indigo-200 bg-indigo-50/60 px-5 py-8 text-center transition hover:border-indigo-400 hover:bg-indigo-50">
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-indigo-600 shadow-sm">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5" />
            </svg>
        </span>
        <span class="mt-3 text-sm font-black text-slate-900">Choose up to 10 images</span>
        <span class="mt-1 text-xs font-medium text-slate-500">JPG, PNG or WEBP, 5 MB per image.</span>
        <input type="file"
               name="files[]"
               accept="image/jpeg,image/png,image/webp"
               multiple
               required
               class="sr-only">
    </label>
</div>
@endif

<div>
    <label class="mb-2 block text-sm font-black text-slate-900">Title</label>
    <input name="title"
           value="{{ old('title', $media->title ?? '') }}"
           placeholder="Example: Bali beach hero"
           class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
</div>

@if($upload ?? false)
    <p class="-mt-3 text-xs font-medium text-slate-500">Title and alt text apply to a single-image upload. Batch uploads use each filename as the initial title.</p>
@endif

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-black text-slate-900">Copyright holder</label>
        <input name="copyright_holder"
               value="{{ old('copyright_holder', $media->copyright_holder ?? '') }}"
               placeholder="Photographer or agency"
               class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
    </div>
    <div>
        <label class="mb-2 block text-sm font-black text-slate-900">License</label>
        <input name="license"
               value="{{ old('license', $media->license ?? '') }}"
               placeholder="Owned, licensed, CC BY..."
               class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
    </div>
</div>

<div>
    <label class="mb-2 block text-sm font-black text-slate-900">Source URL</label>
    <input type="url"
           name="source_url"
           value="{{ old('source_url', $media->source_url ?? '') }}"
           placeholder="https://example.com/original-image"
           class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
</div>

<div>
    <label class="mb-2 block text-sm font-black text-slate-900">Alt text</label>
    <input name="alt_text"
           value="{{ old('alt_text', $media->alt_text ?? '') }}"
           placeholder="Describe the image for visitors and search engines"
           class="h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
</div>

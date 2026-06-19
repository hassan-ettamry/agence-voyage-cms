<x-ui.modal id="createOfferModal" title="New Offer" width="680px">
    <form method="POST" action="{{ route('offers.store') }}" class="space-y-4">
        @csrf
        @include('offers.partials.form', ['offer' => null, 'destinations' => $destinations, 'mediaAssets' => $mediaAssets])
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="closeModal('createOfferModal')" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">Cancel</button>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Create</button>
        </div>
    </form>
</x-ui.modal>

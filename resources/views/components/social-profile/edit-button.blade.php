@props([
	'title' => null,
	'address' => null,
	'itemType' => null,
    'itemId' => null,
])

<x-modals.modal class="modal-lg">
    <x-slot:trigger class="btn btn-neutral btn-xs ms-auto">
        {{ __('Edit') }} <i class="bi bi-pencil-fill ms-2"></i>
    </x-slot:trigger>

    <livewire:update-social-profile :item-type="$itemType" :item-id="$itemId" :address="$address" :title="$title" />
</x-modals.modal>

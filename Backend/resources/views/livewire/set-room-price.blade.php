<div>
    <div>
        <label class="text-sm">{{ __('ui.livewire_start_date') }}</label>
        <input type="text" x-ref="startDate" class="border px-4 py-2 rounded w-full" placeholder="{{ __('ui.livewire_start_date') }}" readonly>
    </div>
    <div>
        <label class="text-sm">{{ __('ui.livewire_end_date') }}</label>
        <input type="text" x-ref="endDate" class="border px-4 py-2 rounded w-full" placeholder="{{ __('ui.livewire_end_date') }}" readonly>
    </div>
    <div>
        <label class="text-sm">{{ __('ui.livewire_price_label') }}</label>
        <input type="number" x-model="setPrice" class="border px-4 py-2 rounded w-full" placeholder="{{ __('ui.livewire_enter_price') }}">
    </div>
    <div class="flex justify-between items-center pt-2">
        <button @click="updatePrice" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Update</button>
        <button @click="$el.remove()" class="text-sm text-gray-500 hover:underline">Close</button>
    </div>
</div>

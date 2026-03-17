<div class="flex flex-col items-start space-y-4">
    <label class="inline-flex items-center">
        <input type="radio" wire:model="roomType" value="daily" class="form-radio accent-gray-400" disabled />
        <span class="ml-2 text-gray-400">{{ __('ui.livewire_daily_type') }}</span>
    </label>
    <label class="inline-flex items-center">
        <input type="radio" wire:model="roomType" value="monthly" class="form-radio accent-gray-400" disabled />
        <span class="ml-2 text-gray-400">{{ __('ui.livewire_monthly_type') }}</span>
    </label>
</div>

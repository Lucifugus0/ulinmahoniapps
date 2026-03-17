<!-- livewire/price-calendar.blade.php -->
<div>
    <x-datepicker wire:model="selectedDate" />

    <div class="mt-4 text-sm">
        {{ __('ui.livewire_price_for_date') }} {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}:
        <strong>{{ $price }}</strong>
    </div>
</div>
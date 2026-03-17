<div>
    <div class="p-4 text-green-700 font-semibold bg-green-100 rounded">
        Livewire Component Loaded
    </div>
    
    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @if(auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isHORole()))
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.livewire_checkin_property_type') }}</label>
                <select wire:model.lazy="propertyType" class="w-full border-gray-200 rounded-lg">
                    <option value="">{{ __('ui.livewire_all_properties') }}</option>
                    <option value="Apartment">Apartment</option>
                    <option value="Hotel">Hotel</option>
                    <option value="House">House</option>
                    <option value="Villa">Villa</option>
                </select>
                <div class="text-xs mt-1 text-gray-500">Selected Property: {{ $propertyType }}</div>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.status') }}</label>
                <select wire:model.lazy="status" class="w-full border-gray-200 rounded-lg">
                    <option value="">{{ __('ui.livewire_all_statuses') }}</option>
                    <option value="waiting">{{ __('ui.livewire_waiting_checkin') }}</option>
                    <option value="checkin">{{ __('ui.livewire_checked_in') }}</option>
                    <option value="checkout">{{ __('ui.livewire_checked_out') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.livewire_checkin_date') }}</label>
                <input type="date" wire:model.lazy="checkInDate" class="w-full border-gray-200 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.livewire_search_label') }}</label>
                <input type="text" wire:model.lazy.debounce.500ms="search" placeholder="{{ __('ui.livewire_search_placeholder') }}" class="w-full border-gray-200 rounded-lg">
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order ID</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Guest</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Property</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Room</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check-in</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check-out</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status</th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-indigo-600">{{ $booking->order_id }}</div>
                            <div class="text-sm text-gray-500">ID: {{ $booking->transaction->user_id }}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $booking->transaction->user_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->transaction->user_email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $booking->transaction->property_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $booking->transaction->room_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($booking->check_in_at)
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->check_in_at)->format('d M Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($booking->check_in_at)->format('H:i') }}
                                </div>
                            @else
                                <div class="text-sm text-gray-500 italic">Not Checked-In Yet</div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($booking->check_out_at)
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->check_out_at)->format('d M Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($booking->check_out_at)->format('H:i') }}
                                </div>
                            @else
                                <div class="text-sm text-gray-500 italic">Not Checked-Out Yet</div>
                            @endif
                        </td>                                    

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->status }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right" x-data="{ open: false }">

                            @if ((is_null($booking->check_in_at) && is_null($booking->check_out_at)) && \Carbon\Carbon::parse($booking->transaction->check_in)->isToday())
                                <!-- Trigger Button -->
                                <button @click="open = true"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none">
                                    <!-- Heroicon: door-open -->
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 21V3a1 1 0 011-1h5.5a1 1 0 011 1v2m0 0v14m0-14l7 2v14l-7-2">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h1">
                                        </path>
                                    </svg>
                                    Check-In
                                </button>

                                {{-- <!-- Old Modal -->
                                <div x-show="open" x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4">
                                    <div
                                        class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mt-20 text-left">
                                        <h2 class="text-lg font-semibold mb-4 text-gray-800">Confirm Check-In
                                        </h2>
                                        <p class="mb-6 text-sm text-gray-600">Are you sure you want to check-in
                                            this guest?</p>
                                        <div class="flex justify-end gap-3">
                                            <button @click="open = false"
                                                class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                                                Cancel
                                            </button>
                                            <form method="POST"
                                                action="{{ route('bookings.checkin', $booking->idrec) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="px-4 py-2 text-sm text-white bg-green-600 rounded hover:bg-green-700">
                                                    Yes, Check-In
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div> --}}

                                <!-- Modal backdrop -->
                                <div class="fixed inset-0 backdrop-blur bg-opacity-30 z-50 transition-opacity"
                                    x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-out duration-100" x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0" aria-hidden="true" x-cloak></div>

                                <!-- Modal dialog -->
                                <div id="feedback-modal1"
                                    class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                                    role="dialog" aria-modal="true" x-show="open"
                                    x-transition:enter="transition ease-in-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-4"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in-out duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-4" x-cloak>
                                    <div class="bg-white rounded shadow-lg overflow-auto w-1/3 max-h-full"
                                        @click.outside="open = false" @keydown.escape.window="open = false">
                                        <!-- Modal header -->
                                        <div class="px-5 py-3 border-b border-slate-200" id="modalAddLpjDetail">
                                            <div class="flex justify-between items-center">
                                                <div class="font-semibold text-slate-800">Tambahkan Detail</div>
                                                <button type="button" class="text-slate-400 hover:text-slate-500"
                                                    @click="open = false">
                                                    <div class="sr-only">Close</div>
                                                    <svg class="w-4 h-4 fill-current">
                                                        <path
                                                            d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Modal content -->
                                        <div class="modal-content text-xs px-5 py-4">
                                            <form id="checkinForm" method="POST" action="{{ route('bookings.checkin', $booking->idrec) }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <!-- Step 1 - Basic Information -->
                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="deposit_amount"
                                                            class="block text-sm text-left font-medium text-gray-700">Deposito</label>
                                                        <input type="text" id="deposit_amount" name="deposit_amount" required
                                                            value="{{ old('deposit_amount', 0) }}"
                                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    </div>
                                                </div>

                                                <!-- Form Actions -->
                                                <div class="mt-6 flex justify-end gap-3">
                                                    <div class="flex space-x-3">
                                                        <button type="button" @click="open = false"
                                                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                            Tutup
                                                        </button>
                                                        <button type="submit"
                                                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                            Check-In
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            No bookings found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="py-4">
        {{ $bookings->links() }}
    </div>

    {{-- <div class="text-sm text-gray-500">
        Debug: {{ $status }}, {{ $propertyType }}, {{ $checkInDate }}
    </div> --}}
</div>

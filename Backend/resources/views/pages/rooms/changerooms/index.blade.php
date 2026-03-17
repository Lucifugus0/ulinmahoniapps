<x-app-layout>
    <div class="flex flex-col h-full">
        <!-- Main Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <div class="w-full">
                    <!-- Tabs Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="flex space-x-8">
                            <button id="initiateTab"
                                class="py-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600">
                                {{ __('ui.change_room') }}
                            </button>
                            <button id="historyTab"
                                class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                {{ __('ui.transfer_history') }}
                            </button>
                        </nav>
                    </div>

                    <!-- Initiate Transfer Tab Content -->
                    <div id="initiateTransferContent">
                        <form id="roomTransferForm" action="{{ route('changeroom.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" id="formOrderId">
                            <input type="hidden" name="current_property_id" id="formCurrentPropertyId">
                            <input type="hidden" name="check_in" id="formCheckIn">
                            <input type="hidden" name="check_out" id="formCheckOut">

                            <!-- Transfer Controls -->
                            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                                        {{ __('ui.change_room') }}
                                    </h1>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Left Column: Booking Selection -->
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <h3 class="font-medium text-gray-700 mb-3">{{ __('ui.select_booking') }}</h3>

                                        <!-- Search Input -->
                                        <div class="mb-4">
                                            <div class="relative">
                                                <input type="text" id="searchInput"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                                    placeholder="{{ __('ui.search_booking_guest_placeholder') }}">
                                                <div class="absolute right-3 top-3 text-gray-400">
                                                    <i class="fas fa-search"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Booking Cards -->
                                        <div class="space-y-3 max-h-96 overflow-y-auto" id="bookingResultsContainer">
                                            @include('pages.rooms.changerooms.partials.changeRoom_table', [
                                                'bookings' => $bookings,
                                                'per_page' => request('per_page', 3),
                                            ])
                                        </div>

                                        <div class="mt-4" id="paginationContainer">
                                            {{ $bookings->withQueryString()->links() }}
                                        </div>
                                    </div>

                                    <!-- Right Column: Transfer Form -->
                                    <div class="space-y-4">
                                        <!-- Current Room Details -->
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <h3 class="font-medium text-gray-700 mb-3">{{ __('ui.current_room') }}</h3>
                                            <div class="bg-white p-4 rounded-lg border-2 border-gray-200 shadow-sm" id="currentRoomDetails">
                                                <div class="text-center py-8 text-gray-500" id="noBookingSelected">
                                                    <i class="fas fa-hand-pointer text-4xl mb-3 text-gray-300"></i>
                                                    <p class="text-sm">{{ __('ui.select_booking_to_view_details') }}</p>
                                                </div>
                                                <div class="hidden" id="bookingDetails">
                                                    <!-- Header: Guest Name & Status -->
                                                    <div class="flex items-start justify-between mb-3">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm" id="guestAvatar">
                                                                ?
                                                            </div>
                                                            <div>
                                                                <div class="font-semibold text-gray-800 text-sm" id="guestName">-</div>
                                                                <div class="text-xs text-gray-500" id="guestEmail">-</div>
                                                                <div class="text-xs text-gray-500" id="guestPhone">-</div>
                                                                <div class="text-xs text-gray-500 font-mono" id="orderId">-</div>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-col items-end space-y-1">
                                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200" id="bookingStatusBadge">
                                                                <i class="fas fa-clock mr-1"></i>-
                                                            </span>
                                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 border border-blue-200 hidden" id="transferBadge">
                                                                <i class="fas fa-exchange-alt mr-1"></i><span id="transferCount">0x</span> {{ __('ui.transferred') }}
                                                            </span>
                                                            <button type="button" id="viewHistoryBtn" class="text-indigo-600 hover:text-indigo-800 text-xs hidden">
                                                                <i class="fas fa-history mr-1"></i>{{ __('ui.view_history') }}
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Details Grid -->
                                                    <div class="space-y-1.5 text-xs mt-3">
                                                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                                                            <span class="text-gray-500 font-medium">
                                                                <i class="fas fa-building w-4 text-gray-400"></i> {{ __('ui.property') }}
                                                            </span>
                                                            <span class="text-gray-800 font-medium text-right" id="propertyName">-</span>
                                                        </div>
                                                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                                                            <span class="text-gray-500 font-medium">
                                                                <i class="fas fa-door-open w-4 text-gray-400"></i> {{ __('ui.room') }}
                                                            </span>
                                                            <span class="text-gray-800 font-medium text-right">
                                                                <span id="roomNumber">-</span>
                                                                <span class="text-gray-500">{{ __('ui.no') }} <span id="roomNo">-</span></span>
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center justify-between py-1 border-b border-gray-100">
                                                            <span class="text-gray-500 font-medium">
                                                                <i class="fas fa-calendar-check w-4 text-green-500"></i> {{ __('ui.check_in') }}
                                                            </span>
                                                            <span class="text-green-600 font-medium text-right" id="checkIn">-</span>
                                                        </div>
                                                        <div class="flex items-center justify-between py-1">
                                                            <span class="text-gray-500 font-medium">
                                                                <i class="fas fa-calendar-times w-4 text-red-500"></i> {{ __('ui.check_out') }}
                                                            </span>
                                                            <span class="text-red-600 font-medium text-right" id="checkOut">-</span>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" id="propertyId">
                                                    <input type="hidden" id="roomId">
                                                    <input type="hidden" id="currentBookingId">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Arrow Indicator -->
                                        <div class="flex justify-center">
                                            <div class="bg-indigo-100 rounded-full p-2">
                                                <i class="fas fa-arrow-down text-indigo-600"></i>
                                            </div>
                                        </div>

                                        <!-- New Room Selection -->
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <h3 class="font-medium text-gray-700 mb-3">{{ __('ui.transfer_to_new_room') }}</h3>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm text-gray-600 mb-1">{{ __('ui.select_room') }} <span class="text-red-500">*</span></label>
                                                    <select name="new_room" id="newRoomSelect"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                                        disabled required>
                                                        <option value="" disabled selected>{{ __('ui.select_new_room_placeholder') }}</option>
                                                    </select>
                                                </div>

                                                <div class="bg-white p-3 rounded border border-gray-200">
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-600 text-sm">{{ __('ui.selected_room') }}:</span>
                                                        <span class="font-medium" id="selectedNewRoom">-</span>
                                                    </div>
                                                    <div class="flex justify-between items-center mt-1">
                                                        <span class="text-gray-600 text-sm">{{ __('ui.status') }}:</span>
                                                        <span class="font-medium text-green-600" id="roomAvailability">-</span>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-sm text-gray-600 mb-1">{{ __('ui.transfer_reason') }} <span class="text-red-500">*</span></label>
                                                    <select name="reason" id="transferReasonSelect"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                                        disabled required>
                                                        <option value="" disabled selected>{{ __('ui.select_reason') }}</option>
                                                        <option value="maintenance">{{ __('ui.maintenance') }}</option>
                                                        <option value="guest_request">{{ __('ui.guest_request') }}</option>
                                                        <option value="other">{{ __('ui.other') }}</option>
                                                    </select>
                                                    <p id="reasonError" class="text-red-500 text-xs mt-1 hidden">{{ __('ui.please_select_reason') }}</p>
                                                </div>

                                                <div>
                                                    <label class="block text-sm text-gray-600 mb-1">{{ __('ui.notes') }}</label>
                                                    <textarea name="notes" id="transferNotes"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                                        disabled rows="2" placeholder="{{ __('ui.additional_notes_placeholder') }}"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex justify-end space-x-3 pt-4">
                                            <button type="button"
                                                onclick="window.location.href='{{ route('changerooom.index') }}'"
                                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                                                {{ __('ui.cancel') }}
                                            </button>
                                            <button type="submit" id="submitButton"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                                                disabled>
                                                <i class="fas fa-exchange-alt mr-2"></i>
                                                {{ __('ui.process_room_transfer') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- History Tab Content -->
                    <div id="historyContent" class="hidden">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-800">{{ __('ui.room_transfer_history') }}</h2>
                                <div class="relative w-64">
                                    <input type="text" id="historySearchInput"
                                        value="{{ $historySearch ?? '' }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                        placeholder="{{ __('ui.search_order_name_placeholder') }}">
                                    <div class="absolute right-3 top-2.5 text-gray-400">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </div>
                            </div>

                            @if (request('history_search'))
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="text-sm text-gray-600">{{ __('ui.active_filter') }}:</span>
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                                        {{ request('history_search') }}
                                        <button onclick="clearHistorySearch()" class="ml-2 hover:text-indigo-900">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>
                                </div>
                            @endif

                            <!-- History Cards with Chain Visualization -->
                            <div class="space-y-4" id="historyContainer">
                                @forelse ($transferHistory as $history)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <!-- Header -->
                                        <div class="bg-gray-50 px-4 py-3 flex justify-between items-center">
                                            <div class="flex items-center space-x-4">
                                                <div>
                                                    <span class="text-sm text-gray-500">{{ __('ui.order') }}:</span>
                                                    <span class="font-semibold text-gray-800">{{ $history['order_id'] }}</span>
                                                </div>
                                                <div class="text-gray-300">|</div>
                                                <div>
                                                    <span class="text-sm text-gray-500">{{ __('ui.guest') }}:</span>
                                                    <span class="font-medium text-gray-800">{{ $history['guest_name'] }}</span>
                                                </div>
                                                <div class="text-gray-300">|</div>
                                                <div>
                                                    <span class="text-sm text-gray-500">{{ __('ui.property') }}:</span>
                                                    <span class="font-medium text-gray-800">{{ $history['property']->name ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                                                    {{ $history['transfer_count'] }}x {{ __('ui.transfers') }}
                                                </span>
                                                @if($history['active_booking'] && $history['active_booking']->previous_booking_id)
                                                    <button type="button"
                                                        onclick="openRollbackModal({{ $history['active_booking']->idrec }}, '{{ $history['order_id'] }}')"
                                                        class="px-3 py-1 bg-orange-100 text-orange-700 rounded text-xs font-medium hover:bg-orange-200 transition">
                                                        <i class="fas fa-undo mr-1"></i> {{ __('ui.rollback') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Chain Visualization -->
                                        <div class="p-4 overflow-x-auto">
                                            <div class="flex items-center space-x-2 min-w-max">
                                                @foreach ($history['chain'] as $index => $booking)
                                                    @php $isOrigin = is_null($booking->previous_booking_id); @endphp
                                                    <!-- Room Node -->
                                                    <div class="flex flex-col items-center">
                                                        <div class="w-24 p-3 rounded-lg border-2 text-center
                                                            {{ $booking->status == 1 ? 'border-green-500 bg-green-50' : ($isOrigin ? 'border-purple-400 bg-purple-50' : 'border-gray-300 bg-gray-50') }}">
                                                            <div class="font-bold text-sm {{ $booking->status == 1 ? 'text-green-700' : ($isOrigin ? 'text-purple-700' : 'text-gray-600') }}">
                                                                {{ $booking->room->name ?? 'N/A' }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">{{ __('ui.no') }} {{ $booking->room->no ?? '' }}</div>
                                                            @if($booking->status == 1)
                                                                <span class="inline-block mt-1 px-2 py-0.5 bg-green-500 text-white text-xs rounded">{{ __('ui.active') }}</span>
                                                            @elseif($isOrigin)
                                                                <span class="inline-block mt-1 px-2 py-0.5 bg-purple-500 text-white text-xs rounded">Awal</span>
                                                            @endif
                                                        </div>
                                                        <!-- Info below node -->
                                                        <div class="mt-2 text-center">
                                                            @if($isOrigin)
                                                                <span class="text-xs font-medium text-purple-600">Kamar Asal</span>
                                                                <div class="text-xs text-gray-400">
                                                                    {{ $booking->created_at->format('d M H:i') }}
                                                                </div>
                                                            @else
                                                                <span class="text-xs font-medium
                                                                    @if($booking->reason === 'rollback') text-orange-600
                                                                    @else text-blue-600
                                                                    @endif">
                                                                    {{ ucfirst($booking->reason ?? 'Transfer') }}
                                                                </span>
                                                                <div class="text-xs text-gray-400">
                                                                    {{ $booking->room_changed_at ? $booking->room_changed_at->format('d M H:i') : $booking->created_at->format('d M H:i') }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Arrow (except after last item) -->
                                                    @if(!$loop->last)
                                                        <div class="flex items-center text-gray-400 pb-8">
                                                            <i class="fas fa-arrow-right text-lg"></i>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <i class="fas fa-exchange-alt text-6xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">{{ __('ui.no_room_transfer_history') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rollback Modal -->
    <div x-data="{ open: false }"
         x-show="open"
         x-on:open-rollback-modal.window="open = true"
         x-on:close-rollback-modal.window="open = false"
         x-on:keydown.escape.window="open = false"
         id="rollbackModal"
         class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
         style="display: none;">
        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transform transition-all"
             x-on:click="open = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- Modal Content -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
             x-on:click.stop>
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-undo text-orange-500 mr-2"></i>
                    {{ __('ui.confirm_rollback') }}
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">{{ __('ui.rollback_confirmation_message') }}</p>

                <div class="flex items-center justify-center space-x-4 mb-4">
                    <div class="text-center p-3 bg-gray-100 rounded-lg">
                        <div class="text-xs text-gray-500">{{ __('ui.now') }}</div>
                        <div class="font-bold text-gray-800" id="rollbackCurrentRoom">-</div>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400"></i>
                    <div class="text-center p-3 bg-green-100 rounded-lg">
                        <div class="text-xs text-gray-500">{{ __('ui.return_to') }}</div>
                        <div class="font-bold text-green-700" id="rollbackPreviousRoom">-</div>
                    </div>
                </div>

                <div id="rollbackAvailabilityStatus" class="mb-4 p-3 rounded-lg">
                    <!-- Will be filled by JS -->
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">{{ __('ui.rollback_notes_optional') }}</label>
                    <textarea id="rollbackNotes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        rows="2" placeholder="{{ __('ui.rollback_reason_placeholder') }}"></textarea>
                </div>

                <form id="rollbackForm" action="{{ route('changeroom.rollback') }}" method="POST">
                    @csrf
                    <input type="hidden" name="booking_id" id="rollbackBookingId">
                    <input type="hidden" name="notes" id="rollbackNotesInput">
                </form>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" x-on:click="open = false"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                    {{ __('ui.cancel') }}
                </button>
                <button type="button" id="confirmRollbackBtn" onclick="submitRollback()"
                    class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-undo mr-1"></i> {{ __('ui.yes_rollback') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Chain Detail Modal -->
    <div x-data="{ open: false }"
         x-show="open"
         x-on:open-chain-modal.window="open = true"
         x-on:close-chain-modal.window="open = false"
         x-on:keydown.escape.window="open = false"
         id="chainDetailModal"
         class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
         style="display: none;">
        <!-- Backdrop -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transform transition-all"
             x-on:click="open = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- Modal Content -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
             x-on:click.stop>
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">{{ __('ui.transfer_history_detail') }}</h3>
                <button x-on:click="open = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto" id="chainDetailContent">
                <!-- Will be filled by JS -->
            </div>
        </div>
    </div>

    <script>
        // Show toast notifications for session messages
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{{ $errors->first() }}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'initiate';

            if (activeTab === 'history') {
                showHistoryTab();
            } else {
                showInitiateTab();
            }

            document.getElementById('initiateTab').addEventListener('click', showInitiateTab);
            document.getElementById('historyTab').addEventListener('click', showHistoryTab);
        });

        function showInitiateTab() {
            document.getElementById('initiateTab').classList.add('border-indigo-500', 'text-indigo-600');
            document.getElementById('initiateTab').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            document.getElementById('historyTab').classList.remove('border-indigo-500', 'text-indigo-600');
            document.getElementById('historyTab').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

            document.getElementById('initiateTransferContent').classList.remove('hidden');
            document.getElementById('historyContent').classList.add('hidden');

            const url = new URL(window.location);
            url.searchParams.delete('tab');
            url.searchParams.delete('history_search');
            window.history.replaceState({}, '', url);
        }

        function showHistoryTab() {
            document.getElementById('historyTab').classList.add('border-indigo-500', 'text-indigo-600');
            document.getElementById('historyTab').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            document.getElementById('initiateTab').classList.remove('border-indigo-500', 'text-indigo-600');
            document.getElementById('initiateTab').classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

            document.getElementById('historyContent').classList.remove('hidden');
            document.getElementById('initiateTransferContent').classList.add('hidden');

            const url = new URL(window.location);
            url.searchParams.set('tab', 'history');
            window.history.pushState({}, '', url);
        }

        // Live search functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const bookingResultsContainer = document.getElementById('bookingResultsContainer');
        const paginationContainer = document.getElementById('paginationContainer');

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchValue = this.value.trim();
            searchTimeout = setTimeout(function() {
                performSearch(searchValue);
            }, 500);
        });

        function performSearch(query, page = 1) {
            const url = new URL('{{ route('changerooom.index') }}');
            if (query) {
                url.searchParams.append('search', query);
            }
            if (page > 1) {
                url.searchParams.append('page', page);
            }
            url.searchParams.append('ajax', '1');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                bookingResultsContainer.innerHTML = data.html;
                paginationContainer.innerHTML = data.pagination;
                attachBookingCardHandlers();
                attachPaginationHandlers();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function attachBookingCardHandlers() {
            document.querySelectorAll('.booking-card').forEach(card => {
                card.addEventListener('click', function() {
                    selectBooking(this);
                });
            });
        }

        function attachPaginationHandlers() {
            paginationContainer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page') || 1;
                    const currentSearch = searchInput.value.trim();
                    performSearch(currentSearch, page);
                });
            });
        }

        // Initial attachment
        attachBookingCardHandlers();
        attachPaginationHandlers();

        // Form validation
        document.getElementById('roomTransferForm').addEventListener('submit', function(e) {
            const reasonSelect = document.getElementById('transferReasonSelect');
            const reasonError = document.getElementById('reasonError');
            const newRoomSelect = document.getElementById('newRoomSelect');

            if (!reasonSelect.value || !newRoomSelect.value) {
                e.preventDefault();

                if (!reasonSelect.value) {
                    reasonSelect.classList.add('border-red-500');
                    reasonError.classList.remove('hidden');
                }

                if (!newRoomSelect.value) {
                    newRoomSelect.classList.add('border-red-500');
                }

                reasonSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        document.getElementById('transferReasonSelect').addEventListener('change', function() {
            if (this.value) {
                this.classList.remove('border-red-500');
                document.getElementById('reasonError').classList.add('hidden');
            }
        });

        function formatDateTime(dateStr) {
            const d = new Date(dateStr);
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            const hours = String(d.getHours()).padStart(2, '0');
            const minutes = String(d.getMinutes()).padStart(2, '0');
            return `${year}/${month}/${day} ${hours}:${minutes}`;
        }

        function selectBooking(element) {
            // Reset form
            const reasonSelect = document.getElementById('transferReasonSelect');
            reasonSelect.value = '';
            reasonSelect.classList.remove('border-red-500');
            document.getElementById('reasonError').classList.add('hidden');
            document.getElementById('selectedNewRoom').textContent = '-';
            document.getElementById('roomAvailability').textContent = '-';
            document.getElementById('transferNotes').value = '';
            document.getElementById('newRoomSelect').value = '';

            // Update card selection
            document.querySelectorAll('.booking-card').forEach(card => {
                card.classList.remove('border-indigo-500', 'bg-indigo-50');
            });
            element.classList.add('border-indigo-500', 'bg-indigo-50');

            // Get booking data
            const bookingData = JSON.parse(element.getAttribute('data-booking'));

            // Fill hidden fields
            document.getElementById('formOrderId').value = bookingData.order_id;
            document.getElementById('formCurrentPropertyId').value = bookingData.property_id;
            document.getElementById('formCheckIn').value = bookingData.check_in;
            document.getElementById('formCheckOut').value = bookingData.check_out;

            // Fill guest details with avatar
            const guestName = bookingData.guest_name || '?';
            const guestInitial = guestName.charAt(0).toUpperCase();
            document.getElementById('guestAvatar').textContent = guestInitial;
            document.getElementById('guestName').textContent = guestName;
            document.getElementById('guestEmail').textContent = bookingData.guest_email || '-';
            document.getElementById('guestPhone').textContent = bookingData.guest_phone || '-';
            document.getElementById('orderId').textContent = bookingData.order_id;

            // Update status badge
            const statusBadge = document.getElementById('bookingStatusBadge');
            if (bookingData.is_checked_in) {
                statusBadge.className = 'px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700 border border-green-200';
                statusBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i>{{ __('ui.checked_in_status') }}';
            } else {
                statusBadge.className = 'px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 border border-yellow-200';
                statusBadge.innerHTML = '<i class="fas fa-clock mr-1"></i>{{ __('ui.pending_status') }}';
            }

            // Update transfer badge
            const transferCount = bookingData.transfer_count || 0;
            const transferBadge = document.getElementById('transferBadge');
            const viewHistoryBtn = document.getElementById('viewHistoryBtn');

            if (transferCount > 0) {
                document.getElementById('transferCount').textContent = transferCount + 'x';
                transferBadge.classList.remove('hidden');
                viewHistoryBtn.classList.remove('hidden');
                viewHistoryBtn.onclick = () => showChainDetail(bookingData.order_id);
            } else {
                transferBadge.classList.add('hidden');
                viewHistoryBtn.classList.add('hidden');
            }

            // Fill property and room details
            document.getElementById('propertyName').textContent = bookingData.propertyName;
            document.getElementById('roomNumber').textContent = bookingData.room_number;
            document.getElementById('roomNo').textContent = bookingData.room_no;
            document.getElementById('checkIn').textContent = formatDateTime(bookingData.check_in);
            document.getElementById('checkOut').textContent = formatDateTime(bookingData.check_out);

            // Store hidden values
            document.getElementById('propertyId').value = bookingData.property_id;
            document.getElementById('roomId').value = bookingData.room_id;
            document.getElementById('currentBookingId').value = bookingData.booking_id;

            // Show booking details
            document.getElementById('noBookingSelected').classList.add('hidden');
            document.getElementById('bookingDetails').classList.remove('hidden');

            // Enable form controls
            document.getElementById('newRoomSelect').disabled = false;
            document.getElementById('transferReasonSelect').disabled = false;
            document.getElementById('transferNotes').disabled = false;
            document.getElementById('submitButton').disabled = false;

            // Fetch available rooms based on current reason selection
            fetchAvailableRooms(
                bookingData.property_id,
                bookingData.room_id,
                bookingData.check_in,
                bookingData.check_out,
                document.getElementById('transferReasonSelect').value
            );
        }

        function fetchAvailableRooms(property_id, room_id, checkInDate, checkOutDate, reason = '') {
            const url = new URL('/rooms/change-room/available-rooms', window.location.origin);
            url.searchParams.append('property_id', property_id);
            url.searchParams.append('room_id', room_id);
            url.searchParams.append('check_in', checkInDate);
            url.searchParams.append('check_out', checkOutDate);
            if (reason) url.searchParams.append('reason', reason);

            fetch(url)
                .then(response => response.json())
                .then(rooms => {
                    const select = document.getElementById('newRoomSelect');
                    select.innerHTML = '<option value="" disabled selected>{{ __('ui.select_new_room_placeholder') }}</option>';

                    if (rooms.length === 0) {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = '{{ __('ui.no_available_rooms') }}';
                        option.disabled = true;
                        select.appendChild(option);
                    } else {
                        rooms.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.idrec;
                            option.textContent = `${room.name} - {{ __('ui.no') }} ${room.no}`;
                            select.appendChild(option);
                        });
                    }

                    select.disabled = false;
                    select.addEventListener('change', function() {
                        updateNewRoomDetails(this);
                    });
                })
                .catch(error => {
                    console.error('Error fetching available rooms:', error);
                    const select = document.getElementById('newRoomSelect');
                    select.innerHTML = '<option value="" disabled selected>{{ __('ui.error_loading_rooms') }}</option>';
                });
        }

        function updateNewRoomDetails(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            document.getElementById('selectedNewRoom').textContent = selectedOption.text;

            const availabilityElement = document.getElementById('roomAvailability');
            availabilityElement.textContent = '{{ __('ui.available') }}';
            availabilityElement.classList.remove('text-red-600');
            availabilityElement.classList.add('text-green-600');

            selectElement.classList.remove('border-red-500');
        }

        // History Search
        let historySearchTimeout;
        const historySearchInput = document.getElementById('historySearchInput');

        if (historySearchInput) {
            historySearchInput.addEventListener('input', function() {
                clearTimeout(historySearchTimeout);
                const searchValue = this.value.trim();
                historySearchTimeout = setTimeout(function() {
                    performHistoryFilter(searchValue);
                }, 500);
            });
        }

        function performHistoryFilter(search) {
            const url = new URL('{{ route('changerooom.index') }}');
            url.searchParams.append('tab', 'history');
            if (search) {
                url.searchParams.append('history_search', search);
            }
            window.location.href = url.toString();
        }

        function clearHistorySearch() {
            if (historySearchInput) {
                historySearchInput.value = '';
            }
            performHistoryFilter('');
        }

        // Rollback Modal Functions
        function openRollbackModal(bookingId, orderId) {
            document.getElementById('rollbackBookingId').value = bookingId;
            window.dispatchEvent(new CustomEvent('open-rollback-modal'));

            // Fetch rollback availability
            fetch(`/rooms/change-room/check-rollback?booking_id=${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('rollbackAvailabilityStatus');
                    const confirmBtn = document.getElementById('confirmRollbackBtn');

                    if (data.room) {
                        document.getElementById('rollbackPreviousRoom').textContent = data.room.name + ' {{ __('ui.no') }} ' + data.room.no;
                    }

                    if (data.available) {
                        statusDiv.innerHTML = '<div class="bg-green-100 text-green-700 p-3 rounded"><i class="fas fa-check-circle mr-2"></i>' + data.message + '</div>';
                        confirmBtn.disabled = false;
                    } else {
                        statusDiv.innerHTML = '<div class="bg-red-100 text-red-700 p-3 rounded"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>';
                        confirmBtn.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });

            // Fetch current room info
            fetch(`/rooms/change-room/chain?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    const activeRoom = data.chain.find(b => b.is_active);
                    if (activeRoom) {
                        document.getElementById('rollbackCurrentRoom').textContent = activeRoom.room_name + ' {{ __('ui.no') }} ' + activeRoom.room_no;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function closeRollbackModal() {
            window.dispatchEvent(new CustomEvent('close-rollback-modal'));
            document.getElementById('rollbackNotes').value = '';
        }

        function submitRollback() {
            document.getElementById('rollbackNotesInput').value = document.getElementById('rollbackNotes').value;
            window.dispatchEvent(new CustomEvent('close-rollback-modal'));
            document.getElementById('rollbackForm').submit();
        }

        // Chain Detail Modal
        function showChainDetail(orderId) {
            fetch(`/rooms/change-room/chain?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    let html = '<div class="space-y-4">';
                    data.chain.forEach((booking, index) => {
                        html += `
                            <div class="flex items-start space-x-3 ${index > 0 ? 'pt-3 border-t' : ''}">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold ${booking.is_active ? 'bg-green-500' : 'bg-gray-400'}">
                                    ${index + 1}
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">${booking.room_name} {{ __('ui.no') }} ${booking.room_no}</div>
                                    <div class="text-sm text-gray-500">
                                        ${index === 0 ? '{{ __('ui.original_room') }}' : booking.reason_label}
                                        ${booking.room_changed_at ? ' - ' + booking.room_changed_at : ''}
                                    </div>
                                    ${booking.description ? '<div class="text-sm text-gray-400 mt-1">' + booking.description + '</div>' : ''}
                                    ${booking.room_changed_by ? '<div class="text-xs text-gray-400">{{ __('ui.by') }}: ' + booking.room_changed_by + '</div>' : ''}
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';

                    document.getElementById('chainDetailContent').innerHTML = html;
                    window.dispatchEvent(new CustomEvent('open-chain-modal'));
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</x-app-layout>

{{-- Ticket list partial — renders ticket rows for the left panel.
     Reloaded via AJAX on filter/search changes. --}}
@forelse($tickets as $ticket)
<div class="ticket-item cursor-pointer border-b border-gray-200 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
     data-ticket-id="{{ $ticket->id }}"
     onclick="if(window._selectTicket) window._selectTicket({{ $ticket->id }})">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            {{-- Ticket number + status badge --}}
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $ticket->ticket_number }}</span>
                @php
                    $statusColors = [
                        'open' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                        'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        'reopened' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                    ];
                @endphp
                <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$ticket->ticket_status] ?? '' }}">
                    {{ ucfirst(str_replace('_', ' ', $ticket->ticket_status)) }}
                </span>
            </div>

            {{-- Subject --}}
            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $ticket->subject }}</p>

            {{-- Category badge --}}
            <div class="flex items-center gap-2 mt-1">
                @php
                    $typeColors = [
                        'booking' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                        'complaint' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                        'suggestion' => 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300',
                    ];
                @endphp
                <span class="text-xs px-1.5 py-0.5 rounded {{ $typeColors[$ticket->category->ticket_type ?? ''] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($ticket->category->ticket_type ?? '') }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $ticket->category->localized_label ?? '' }}
                </span>
            </div>

            {{-- Row 1: Name | Booking ID --}}
            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                <span>{{ trim(($ticket->user->first_name ?? '') . ' ' . ($ticket->user->last_name ?? '')) ?: ($ticket->user->name ?? 'User') }}</span>
                @if($ticket->order_id)
                    <span>&bull;</span>
                    <span class="font-mono">{{ $ticket->order_id }}</span>
                @endif
            </div>
            {{-- Row 2: Property Name | Room Type | Room Number --}}
            @if($ticket->property || $ticket->transaction)
            <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                @if($ticket->property)
                    <span>{{ $ticket->property->name }}</span>
                @endif
                @if($ticket->transaction?->room_name)
                    <span>&bull;</span>
                    <span>{{ $ticket->transaction->room_name }}</span>
                @endif
                @if($ticket->transaction?->room?->no)
                    <span>&bull;</span>
                    <span>No.{{ $ticket->transaction->room->no }}</span>
                @endif
            </div>
            @endif
        </div>

        {{-- Right side: time + unread badge --}}
        <div class="flex flex-col items-end ml-2 shrink-0">
            <span class="text-xs text-gray-400 dark:text-gray-500">
                {{ $ticket->last_message_at ? $ticket->last_message_at->diffForHumans() : $ticket->created_at->diffForHumans() }}
            </span>
            @if(($ticket->unread_count ?? 0) > 0)
                <span class="mt-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                    {{ $ticket->unread_count > 99 ? '99+' : $ticket->unread_count }}
                </span>
            @endif
        </div>
    </div>
</div>
@empty
<div class="p-8 text-center text-gray-500 dark:text-gray-400">
    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    <p class="mt-2 text-sm">{{ __('ui.ticket_no_tickets') ?? 'No tickets found' }}</p>
</div>
@endforelse

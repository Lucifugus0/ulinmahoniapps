<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order ID
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        User
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Property
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kamar
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Check-in / Check-out
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Messages
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Last Message
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($conversations as $conversation)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $conversation->order_id }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $conversation->booking->user_name ?? ($conversation->transaction->user->name ?? 'N/A') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $conversation->booking->user_email ?? ($conversation->transaction->user->email ?? '') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $conversation->property->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                No. {{ $conversation->booking->room->no ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $conversation->booking->room->name ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="relative">
                                <!-- Timeline line -->
                                <div
                                    class="absolute left-3 top-0 bottom-0 w-0.5 bg-gradient-to-b from-green-400 to-red-400">
                                </div>

                                <!-- Check-in -->
                                <div class="flex items-center mb-4 relative z-10">
                                    <div
                                        class="w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 uppercase tracking-wider font-medium">Check-in
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $conversation->booking->transaction->check_in ? \Carbon\Carbon::parse($conversation->booking->transaction->check_in)->format('d M Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Check-out -->
                                <div class="flex items-center relative z-10">
                                    <div
                                        class="w-6 h-6 rounded-full bg-red-500 border-4 border-white shadow-sm flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 uppercase tracking-wider font-medium">
                                            Check-out</div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $conversation->booking->transaction->check_out ? \Carbon\Carbon::parse($conversation->booking->transaction->check_out)->format('d M Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $conversation->messages_count }} messages
                                </span>
                                @if (isset($conversation->unread_count) && $conversation->unread_count > 0)
                                    <span
                                        class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-bold bg-red-500 text-white min-w-[24px]">
                                        {{ $conversation->unread_count }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if ($conversation->last_message_at)
                                {{ $conversation->last_message_at->diffForHumans() }}
                            @else
                                <span class="text-gray-400">{{ __('ui.chat_no_messages') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button onclick="openChat({{ $conversation->id }})" type="button"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Chat
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">{{ __('ui.chat_no_conversations') }}</h3>
                                <p class="text-gray-500">{{ __('ui.chat_no_conversations_desc') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

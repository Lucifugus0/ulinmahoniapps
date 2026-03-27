{{-- Broadcast Management Index — lists all sent broadcasts with option to create new ones. --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-bullhorn mr-2 text-indigo-500"></i>{{ __('ui.broadcasts') ?? 'Broadcasts' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('ui.broadcast_subtitle') ?? 'One-way announcements to users' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tickets.index') }}"
                   class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>{{ __('ui.back_to_tickets') ?? 'Back to Tickets' }}
                </a>
                <a href="{{ route('broadcasts.create') }}"
                   class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-1"></i>{{ __('ui.new_broadcast') ?? 'New Broadcast' }}
                </a>
            </div>
        </div>

        {{-- Broadcast list table --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.broadcast_number') ?? 'Number' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.title') ?? 'Title' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.sender') ?? 'Sender' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.audience') ?? 'Audience' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.recipients') ?? 'Recipients' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.sent_at') ?? 'Sent At' }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('ui.actions') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($broadcasts as $broadcast)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-sm font-mono text-indigo-600 dark:text-indigo-400">{{ $broadcast->broadcast_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($broadcast->title, 40) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $broadcast->sender->first_name ?? $broadcast->sender->name ?? '-' }}
                            <span class="text-xs px-1.5 py-0.5 rounded {{ $broadcast->sender_type === 'hq' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' }}">
                                {{ $broadcast->sender_type === 'hq' ? 'HQ' : ($broadcast->property->name ?? 'FD') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ ucwords(str_replace('_', ' ', $broadcast->audience)) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ number_format($broadcast->recipient_count) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $broadcast->sent_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('broadcasts.show', $broadcast->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                                {{ __('ui.view') ?? 'View' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            {{ __('ui.no_broadcasts') ?? 'No broadcasts sent yet.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $broadcasts->links() }}
        </div>
    </div>
</x-app-layout>

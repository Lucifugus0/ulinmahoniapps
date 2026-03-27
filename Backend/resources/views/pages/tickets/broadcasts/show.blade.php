{{-- Broadcast Detail View — shows the full broadcast content and metadata. --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('broadcasts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-arrow-left mr-1"></i>{{ __('ui.back_to_broadcasts') ?? 'Back to Broadcasts' }}
            </a>
        </div>

        {{-- Broadcast card --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Header banner --}}
            <div class="bg-indigo-600 px-6 py-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-mono text-indigo-200">{{ $broadcast->broadcast_number }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $broadcast->sender_type === 'hq' ? 'bg-purple-200 text-purple-800' : 'bg-blue-200 text-blue-800' }}">
                        {{ $broadcast->sender_type === 'hq' ? 'HQ' : ($broadcast->property->name ?? 'Front Desk') }}
                    </span>
                </div>
                <h2 class="text-xl font-bold text-white">{{ $broadcast->title }}</h2>
            </div>

            {{-- Content --}}
            <div class="p-6">
                <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 whitespace-pre-wrap text-sm">{{ $broadcast->message_text }}</div>
            </div>

            {{-- Metadata --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs block">{{ __('ui.sent_by') ?? 'Sent By' }}</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $broadcast->sender->first_name ?? $broadcast->sender->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs block">{{ __('ui.audience') ?? 'Audience' }}</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ ucwords(str_replace('_', ' ', $broadcast->audience)) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs block">{{ __('ui.recipients') ?? 'Recipients' }}</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ number_format($broadcast->recipient_count) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 text-xs block">{{ __('ui.sent_at') ?? 'Sent At' }}</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $broadcast->sent_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

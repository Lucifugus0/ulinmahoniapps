<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.property') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.deposit_fee_amount') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.added_by') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.change_date') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.status') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($depositFees as $deposit)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $deposit->property->name ?? '-' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $deposit->property->city ?? '' }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                        Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $deposit->createdBy->username ?? 'System' }}
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $deposit->created_at->format('d M Y') }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($deposit->updated_by)
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $deposit->updatedBy->username ?? 'System' }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $deposit->updated_at->format('d M Y') }}
                        </div>
                    @else
                        <div class="text-sm font-medium text-gray-400 italic">
                            {{ __('ui.no_data') }}
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                class="sr-only peer deposit-status-toggle"
                                data-id="{{ $deposit->idrec }}"
                                {{ $deposit->status == 1 ? 'checked' : '' }}
                                onchange="toggleDepositStatus(this)">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                            <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                        </label>
                        <span class="text-sm font-medium status-label {{ $deposit->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $deposit->status == 1 ? __('ui.active') : __('ui.inactive') }}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <button type="button"
                        onclick="openEditDepositModal(@js($deposit))"
                        class="text-yellow-500 hover:text-yellow-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

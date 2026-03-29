<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.voucher_code') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.name') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.discount') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.usage') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.period') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.scope') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.status') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.action') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $voucher->code }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $voucher->name }}
                            </div>
                            @if($voucher->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($voucher->description, 50) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $voucher->discount_percentage }}%
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('ui.max_label') }}: Rp {{ number_format($voucher->max_discount_amount, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $voucher->current_usage_count }} / {{ $voucher->max_total_usage == 0 ? '∞' : $voucher->max_total_usage }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('ui.max_per_user') }}: {{ $voucher->max_usage_per_user }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div>{{ \Carbon\Carbon::parse($voucher->valid_from)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('ui.until_short') }} {{ \Carbon\Carbon::parse($voucher->valid_to)->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $voucher->scope_type === 'global' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                {{ $voucher->scope_type === 'property' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $voucher->scope_type === 'room' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}">
                                {{ ucfirst($voucher->scope_type) }}
                            </span>
                        </td>
                        {{-- Status toggle switch — matches Property master page pattern --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer voucher-status-toggle"
                                        data-id="{{ $voucher->idrec }}" {{ $voucher->status === 'active' ? 'checked' : '' }}
                                        onchange="toggleVoucherStatus(this)">
                                    {{-- Toggle switch — green when active, gray when inactive --}}
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                                </label>
                                <span class="text-sm font-medium {{ $voucher->status === 'active' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $voucher->status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        {{-- Action column: view + edit --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <!-- View button -->
                                <button onclick="openViewVoucherModal({{ $voucher->idrec }})"
                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                    title="{{ __('ui.view') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <!-- Edit button -->
                                <button onclick="openEditModal({{ $voucher->idrec }})"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="{{ __('ui.edit') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                                <p class="text-sm">{{ __('ui.no_voucher_data') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($vouchers instanceof \Illuminate\Pagination\LengthAwarePaginator && $vouchers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $vouchers->links() }}
        </div>
    @endif
</div>

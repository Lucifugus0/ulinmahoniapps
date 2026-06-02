<!-- Banner table — columns: Preview, Title, Description, Promo Code, Status, Actions, Created By -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.promo_banner_preview') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.promo_banner_col_title') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.promo_banner_description_label') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.promo_banner_col_promo_code') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.status') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.actions') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ __('ui.promo_banner_created_by') }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($banners as $banner)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <!-- Preview thumbnail -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($banner->primaryImage && $banner->primaryImage->image_url)
                                <img src="{{ $banner->primaryImage->image_url }}"
                                    alt="{{ $banner->title }}"
                                    class="h-16 w-32 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                                    onclick="openImagePreview('{{ $banner->primaryImage->image_url }}')"
                                    loading="lazy">
                            @else
                                <div class="h-16 w-32 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <!-- Title -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $banner->title }}</div>
                        </td>
                        <!-- Description -->
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $banner->descriptions ?: '-' }}</div>
                        </td>
                        <!-- Promo Code -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($banner->promo_code)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 tracking-wide">{{ $banner->promo_code }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <!-- Status toggle -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer banner-status-toggle" data-id="{{ $banner->idrec }}" {{ $banner->status == 1 ? 'checked' : '' }} onchange="toggleBannerStatus(this)">
                                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                                </label>
                                <span class="text-sm font-medium status-label {{ $banner->status == 1 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $banner->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <!-- Actions: View, Edit -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="openViewModal({{ $banner->idrec }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" title="{{ __('ui.view') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button onclick="openEditModal({{ $banner->idrec }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <!-- Created By + date badge (moved to last column) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $banner->creator->username ?? '-' }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($banner->created_at)->format('d M Y, H:i') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm">{{ __('ui.promo_banner_no_data') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($banners instanceof \Illuminate\Pagination\LengthAwarePaginator && $banners->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $banners->links() }}
        </div>
    @endif
</div>

<!-- View Banner Modal — read-only detail view -->
<div id="viewBannerModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="viewModalTitle">Banner Details</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="px-6 py-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.promo_banner_title_label') }}</label>
                <p class="text-gray-900 dark:text-white font-medium" id="viewBannerTitle">-</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.promo_banner_description_label') }}</label>
                <p class="text-gray-700 dark:text-gray-300 text-sm" id="viewBannerDescription">-</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.promo_banner_promo_code') }}</label>
                <p class="text-gray-900 dark:text-white" id="viewBannerPromoCode">-</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.promo_banner_image_label') }}</label>
                <img id="viewBannerImage" src="" alt="Banner" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 hidden">
                <p id="viewBannerImageEmpty" class="text-gray-400 text-sm">-</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.promo_banner_mobile_image_label') }}</label>
                <img id="viewBannerMobileImage" src="" alt="Mobile Banner" class="w-full max-w-md rounded-lg border border-gray-200 dark:border-gray-600 hidden">
                <p id="viewBannerMobileImageEmpty" class="text-gray-400 text-sm">-</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.promo_banner_how_to_claim') }}</label>
                <ol id="viewBannerHowToClaim" class="list-decimal list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300"></ol>
                <p id="viewBannerHowToClaimEmpty" class="text-gray-400 text-sm">-</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.promo_banner_terms_conditions') }}</label>
                <ul id="viewBannerTerms" class="list-disc list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300"></ul>
                <p id="viewBannerTermsEmpty" class="text-gray-400 text-sm">-</p>
            </div>
        </div>
    </div>
</div>

{{-- Table rows for Access Management page --}}
{{-- Styled to match All Bookings table: consistent text colors, badge styling, dark mode support --}}
@forelse ($adminUsers as $index => $user)
    <tr class="hover:bg-gray-50 transition-colors duration-150">
        {{-- Row number --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $perPage === 'all' ? $index + 1 : ($adminUsers->currentPage() - 1) * $adminUsers->perPage() + $index + 1 }}
        </td>
        {{-- User info: avatar + name + username --}}
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                    <div
                        class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr($user->first_name ?? ($user->name ?? 'U'), 0, 1)) }}
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $user->first_name && $user->last_name ? $user->first_name . ' ' . $user->last_name : $user->name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $user->username ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </td>
        {{-- Email --}}
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-900">{{ $user->email }}</div>
        </td>
        {{-- Current Role badge — uses soft colored pill matching All Bookings status badge style --}}
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($user->role)
                <span
                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    {{ $user->role->name }}
                </span>
            @else
                <span
                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                    No Role
                </span>
            @endif
        </td>
        {{-- Sidebar Access button — solid style for clear visibility --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button
                onclick="manageAccessRights({{ $user->id }}, '{{ addslashes($user->first_name ?? $user->name) }}')"
                class="inline-flex items-center px-3 py-1.5 text-xs leading-5 font-semibold rounded-full bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                <i class="fas fa-key mr-1.5"></i>
                {{ __('ui.settings_sidebar_menu') }}
            </button>
        </td>
        {{-- Dashboard Widgets button — solid style for clear visibility --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            @if ($user->role)
                <button
                    onclick="manageDashboardWidgets({{ $user->role->id }}, '{{ addslashes($user->role->name) }}')"
                    class="inline-flex items-center px-3 py-1.5 text-xs leading-5 font-semibold rounded-full bg-purple-600 text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-150">
                    <i class="fas fa-th-large mr-1.5"></i>
                    {{ __('ui.dashboard_widgets') }}
                </button>
            @else
                <span class="text-xs text-gray-500 italic">{{ __('ui.settings_no_role_assigned') }}</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
            <i class="fas fa-users text-4xl mb-4 block text-gray-300"></i>
            <p class="text-lg font-medium">{{ __('ui.settings_no_admin_users') }}</p>
            <p class="text-sm">{{ __('ui.settings_admin_hint') }}</p>
        </td>
    </tr>
@endforelse

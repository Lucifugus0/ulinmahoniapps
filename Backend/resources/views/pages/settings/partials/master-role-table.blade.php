@forelse ($adminUsers as $index => $user)
    <tr class="hover:bg-gray-50 transition-colors duration-150">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $perPage === 'all' ? $index + 1 : ($adminUsers->currentPage() - 1) * $adminUsers->perPage() + $index + 1 }}
        </td>
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
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-900">{{ $user->email }}</div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            @if ($user->role)
                <span
                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ $user->role->name }}
                </span>
            @else
                <span
                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                    No Role
                </span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button
                onclick="manageAccessRights({{ $user->id }}, '{{ addslashes($user->first_name ?? $user->name) }}')"
                class="btn-access-rights inline-flex items-center px-3 py-2 border border-blue-600 text-sm leading-4 font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                <i class="fas fa-key mr-2"></i>
                Sidebar Menu
            </button>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            @if ($user->role)
                <button
                    onclick="manageDashboardWidgets({{ $user->role->id }}, '{{ addslashes($user->role->name) }}')"
                    class="btn-widgets inline-flex items-center px-3 py-2 border border-purple-600 text-sm leading-4 font-medium rounded-md text-purple-600 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-150">
                    <i class="fas fa-th-large mr-2"></i>
                    Widgets
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

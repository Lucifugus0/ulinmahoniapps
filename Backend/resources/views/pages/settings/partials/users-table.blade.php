@foreach ($users as $user)
    <tr class="hover:bg-gray-50/80 transition-colors duration-150">
        @php
            $colors = [
                'bg-red-500/35',
                'bg-blue-500/35',
                'bg-green-500/35',
                'bg-purple-500/35',
                'bg-pink-500/35',
                'bg-yellow-500/35',
                'bg-indigo-500/35',
                'bg-teal-500/35',
                'bg-orange-500/35',
            ];
            $bgColor = $colors[array_rand($colors)];
        @endphp

        <!-- Name -->
        <td class="px-4 py-4 text-sm text-gray-900">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 flex items-center justify-center rounded-full text-white font-semibold {{ $bgColor }}">
                    {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                </div>
                <div>
                    <div>
                        {{ $user->first_name }} {{ $user->last_name }}
                    </div>
                    <div class="text-gray-500 text-xs">
                        {{ '@' . $user->username }}
                    </div>
                </div>
            </div>
        </td>

        <!-- Email -->
        <td class="px-4 py-4 text-sm text-gray-900">
            {{ $user->email }}
        </td>

        <!-- User Type -->
        <td class="px-4 py-4 text-center">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                {{ $user->user_type == 0 ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                <span class="text-base">{{ $user->user_type == 0 ? '🏢' : '📍' }}</span>
                {{ $user->user_type == 0 ? 'HO' : 'Site' }}
            </span>
        </td>

        @php
            $roleName = optional($user->role)->name ?? 'No Role';
        @endphp
        <!-- Role -->
        <td class="px-4 py-4 text-center">
            <span
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">

                @switch($roleName)
                    @case('Owner')
                        <i class="fas fa-crown"></i>
                    @break

                    @case('Manager')
                        <i class="fas fa-user-tie"></i>
                    @break

                    @case('Front Desk')
                        <i class="fas fa-concierge-bell"></i>
                    @break

                    @case('Finance')
                        <i class="fas fa-wallet"></i>
                    @break

                    @case('CS')
                        <i class="fas fa-headset"></i>
                    @break

                    @case('Sales')
                        <i class="fas fa-chart-line"></i>
                    @break

                    @case('Property Owner')
                        <i class="fas fa-building"></i>
                    @break

                    @case('creative')
                        <i class="fas fa-paint-brush"></i>
                    @break

                    @case('administrator')
                        <i class="fas fa-user-shield"></i>
                    @break

                    @default
                        <i class="fas fa-user"></i>
                @endswitch

                {{ $roleName }}
            </span>
        </td>

        <!-- Property -->
        <td class="px-4 py-4 text-sm text-gray-900">
            @if($user->property)
                <div class="flex items-center gap-2">
                    <i class="fas fa-building text-indigo-600"></i>
                    <span>{{ $user->property->name }}</span>
                </div>
            @else
                <span class="text-gray-400 italic">{{ __('ui.settings_no_property') }}</span>
            @endif
        </td>

        <!-- Status -->
        <td class="px-4 py-4 whitespace-nowrap text-center">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" value="" class="sr-only peer" data-id="{{ $user->id }}"
                    {{ $user->status == 1 ? 'checked' : '' }} onchange="toggleUserStatus(this)">
                <div
                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                </div>
                <span class="ml-3 text-sm font-medium text-gray-900 transition-opacity duration-200">
                    {{ $user->status == 1 ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </label>
        </td>
        <!-- Created At -->
        <td class="px-4 py-4 text-sm text-gray-900">
            <div class="text-sm font-medium text-gray-900">
                {{ \Carbon\Carbon::parse($user->created_at)->format('Y M d') }}</div>
            <div class="text-xs text-gray-400">
                {{ \Carbon\Carbon::parse($user->created_at)->format('H:i') }}</div>
        </td>
        <!-- Actions -->
        <td class="px-4 py-4">
            <div class="flex gap-2">
                <!-- Reset Password Button (Only for Superadmin) -->
                @if(Auth::user()->isSuperAdmin())
                    <button class="p-2 rounded-xl text-amber-600 hover:bg-amber-50 transition"
                        type="button"
                        title="{{ __('ui.settings_reset_password') }}"
                        onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->first_name) }}', '{{ addslashes($user->last_name) }}', '{{ addslashes($user->username) }}')">
                        <i class="fas fa-key"></i>
                    </button>
                @endif

                {{-- Edit button — hidden for customer accounts (role_id NULL = no staff role assigned). The Account Settings list shows admins, site staff, and registered guests in the same table; customers shouldn't be edited via this admin form. --}}
                @if (!is_null($user->role_id))
                    <button class="p-2 rounded-xl text-indigo-600 hover:bg-gray-100 transition"
                        type="button"
                        onclick="openEditModal({{ $user->id }}, {{ json_encode([
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'username' => $user->username,
                            'email' => $user->email,
                            'nik' => $user->nik,
                            'role_id' => $user->role_id,
                            'user_type' => $user->user_type,
                            'property_id' => $user->property_id
                        ]) }})">
                        <i class="fas fa-edit"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@endforeach

@if($users->isEmpty())
    <tr>
        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
            <i class="fas fa-users text-4xl mb-4 block text-gray-300"></i>
            <p class="text-lg font-medium">{{ __('ui.settings_no_users_found') }}</p>
        </td>
    </tr>
@endif
